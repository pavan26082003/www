<?php

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
@ini_set('memory_limit', '2048M');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, If-None-Match, If-Modified-Since, Cache-Control');
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: private, no-cache, max-age=0, must-revalidate');
header('Vary: Accept-Encoding, If-None-Match');

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/config.php';

if (!isset($db)) {
    echo json_encode([
        'status' => false,
        'message' => 'Database not available',
    ]);
    exit();
}

$generatorCandidates = [
    dirname(__DIR__) . '/app/services/DeviceUiSchemaGenerator.php',
    dirname(__DIR__) . '/app/Services/DeviceUiSchemaGenerator.php',
];
foreach ($generatorCandidates as $generatorFile) {
    if (!is_file($generatorFile)) {
        continue;
    }
    require_once $generatorFile;
    if (class_exists('\\App\\Services\\DeviceUiSchemaGenerator', false)) {
        \App\Services\DeviceUiSchemaGenerator::raiseMemoryLimit();
    }
    break;
}

$slicerCandidates = [
    dirname(__DIR__) . '/app/services/DeviceUiSchemaPageSlicer.php',
    dirname(__DIR__) . '/app/Services/DeviceUiSchemaPageSlicer.php',
];
foreach ($slicerCandidates as $slicerFile) {
    if (!is_file($slicerFile)) {
        continue;
    }
    require_once $slicerFile;
    break;
}

$model_no = isset($_GET['model_no']) ? trim((string) $_GET['model_no']) : '';
$node_id = isset($_GET['node_id']) ? trim((string) $_GET['node_id']) : '';
// Optional SDUI optimization (ignored by older apps):
//   part=meta  -> shell + page list, no control trees
//   page_no=N  -> one page + its controls only
// Default (no part/page_no) remains the complete schema.
$part = isset($_GET['part']) ? trim((string) $_GET['part']) : '';
$page_no = isset($_GET['page_no']) ? trim((string) $_GET['page_no']) : '';
if ($page_no === '' && isset($_GET['pageNo'])) {
    $page_no = trim((string) $_GET['pageNo']);
}
$requestedModelNo = $model_no;
$requestedNodeId = $node_id;
$deliveryMode = class_exists('\\App\\Services\\DeviceUiSchemaPageSlicer', false)
    ? \App\Services\DeviceUiSchemaPageSlicer::resolveMode($part, $page_no)
    : 'full';

if ($model_no === '') {
    echo json_encode([
        'status' => false,
        'message' => 'model_no is required',
    ]);
    exit();
}

// Always create/refresh from current pages+APIs before serving.
// Previously this ran only when no device_ui_schemas row existed, so models with
// an older saved schema kept serving stale UI until Design Studio "Save Changes".
try {
    require_once __DIR__ . '/bootstrap_device_ui_schema.php';
    if (function_exists('efactor_ensure_device_ui_schema')) {
        efactor_ensure_device_ui_schema($model_no, $node_id);
    }
} catch (Throwable $exception) {
    error_log('v8 auto schema sync failed: ' . $exception->getMessage());
}

$model_no_sql = $db->escape($model_no);
$sql = "SELECT *
        FROM device_ui_schemas
        WHERE model_name = '$model_no_sql'";

if ($node_id !== '') {
    $node_id_sql = $db->escape($node_id);
    $sql .= " AND node_id = '$node_id_sql'";
}

$sql .= ' ORDER BY updated_at DESC, id DESC LIMIT 1';
$schema = $db->get_row($sql);

// If the requested node_id is not stored on the row, still serve the latest model schema.
if (!$schema && $requestedNodeId !== '') {
    $sql = "SELECT *
            FROM device_ui_schemas
            WHERE model_name = '$model_no_sql'
            ORDER BY updated_at DESC, id DESC
            LIMIT 1";
    $schema = $db->get_row($sql);
}

if (!$schema) {
    // Tell the caller why, so a missing schema can be traced to a model name or a
    // node that was never published instead of looking like a transport failure.
    $knownNodes = [];
    $knownModels = [];
    try {
        $rows = $db->get_results("SELECT DISTINCT node_id
                                  FROM device_ui_schemas
                                  WHERE model_name = '$model_no_sql'
                                  LIMIT 25");
        foreach ((array) $rows as $row) {
            $candidate = trim((string) ($row['node_id'] ?? ''));
            if ($candidate !== '') {
                $knownNodes[] = $candidate;
            }
        }

        $like = $db->escape('%' . $model_no . '%');
        $rows = $db->get_results("SELECT DISTINCT model_name
                                  FROM device_ui_schemas
                                  WHERE model_name LIKE '$like'
                                  LIMIT 25");
        foreach ((array) $rows as $row) {
            $candidate = trim((string) ($row['model_name'] ?? ''));
            if ($candidate !== '') {
                $knownModels[] = $candidate;
            }
        }
    } catch (Throwable $exception) {
        error_log('v8 schema diagnostics failed: ' . $exception->getMessage());
    }

    $reason = $knownModels
        ? 'No published UI schema for this model_no. Publish it from Design Studio, or link its parameters so it can be generated automatically.'
        : 'model_no is not present in device_ui_schemas. Check the model name spelling used by the app.';

    // Status stays 200: the shipped app treats a non-200 as a transport failure.
    echo json_encode([
        'status' => false,
        'message' => 'Schema not found',
        'reason' => $reason,
        'requested' => [
            'model_no' => $requestedModelNo,
            'node_id' => $requestedNodeId,
        ],
        'availableModels' => $knownModels,
        'availableNodes' => $knownNodes,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit();
}

$storedSchemaJson = '';
if (isset($schema['schema_json']) && is_string($schema['schema_json'])) {
    $storedSchemaJson = $schema['schema_json'];
    unset($schema['schema_json']);
}

$isSplitSchema = $storedSchemaJson !== ''
    && strpos($storedSchemaJson, '"schemaStorage"') !== false
    && strpos($storedSchemaJson, 'split-v1') !== false;
$serveStoredJson = $storedSchemaJson !== '' && !$isSplitSchema && $deliveryMode === 'full';

$schemaUpdatedAt = isset($schema['updated_at']) ? (string) $schema['updated_at'] : '';
if ($requestedNodeId !== '') {
    $schema['node_id'] = $requestedNodeId;
}

/**
 * Build absolute api_schema.php URL for paging links.
 */
$efactor_schema_api_base = static function () {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443');
    $scheme = $https ? 'https' : 'http';
    $host = isset($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : 'localhost';
    $script = isset($_SERVER['SCRIPT_NAME']) ? (string) $_SERVER['SCRIPT_NAME'] : '/v8/api_schema.php';
    if ($script === '' || $script[0] !== '/') {
        $script = '/v8/api_schema.php';
    }

    return $scheme . '://' . $host . $script;
};

/**
 * Normalize + optionally slice decoded schema_json, then wrap response body.
 *
 * @param array $row
 * @param array $decoded
 * @return array{body: string|false, ok: bool}
 */
$efactor_finalize_schema_response = static function (array $row, array $decoded) use (
    $requestedModelNo,
    $requestedNodeId,
    $deliveryMode,
    $part,
    $page_no,
    $schemaUpdatedAt,
    $efactor_schema_api_base
) {
    $resolvedNodeId = $requestedNodeId !== ''
        ? $requestedNodeId
        : trim((string) ($row['node_id'] ?? ''));

    try {
        $normalizerFile = dirname(__DIR__) . '/app/services/DeviceUiSchemaNormalizer.php';
        if (!is_file($normalizerFile)) {
            $normalizerFile = dirname(__DIR__) . '/app/Services/DeviceUiSchemaNormalizer.php';
        }
        if (!class_exists('\\App\\Services\\DeviceUiSchemaNormalizer', false) && is_file($normalizerFile)) {
            require_once $normalizerFile;
        }
        if (class_exists('\\App\\Services\\DeviceUiSchemaNormalizer', false)) {
            $decoded = \App\Services\DeviceUiSchemaNormalizer::normalize($decoded, $requestedModelNo, $resolvedNodeId);
        }
    } catch (Throwable $exception) {
        error_log('v8 schema normalize failed: ' . $exception->getMessage());
    }

    if ($resolvedNodeId !== '') {
        $decoded['nodes'] = $resolvedNodeId;
        if (isset($decoded['state']) && is_array($decoded['state'])) {
            $decoded['state']['node_id'] = $resolvedNodeId;
        }
    }

    $paging = null;
    $message = 'Schema found';
    $activeMode = $deliveryMode;

    if ($activeMode !== 'full' && class_exists('\\App\\Services\\DeviceUiSchemaPageSlicer', false)) {
        $sliced = \App\Services\DeviceUiSchemaPageSlicer::apply(
            $decoded,
            $part !== '' ? $part : ($activeMode === 'page' ? 'page' : 'meta'),
            $page_no,
            [
                'model_no' => $requestedModelNo,
                'node_id' => $resolvedNodeId,
                'base_url' => $efactor_schema_api_base(),
                'updated_at' => $schemaUpdatedAt,
            ]
        );

        if (empty($sliced['ok'])) {
            $body = json_encode([
                'status' => false,
                'message' => $sliced['message'] ?? 'Page not found in schema',
                'error' => $sliced['error'] ?? 'PAGE_NOT_FOUND',
                'part' => $sliced['mode'] ?? $activeMode,
                'paging' => $sliced['paging'] ?? null,
                'requested' => [
                    'model_no' => $requestedModelNo,
                    'node_id' => $requestedNodeId,
                    'page_no' => $page_no,
                    'part' => $part,
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            return ['body' => $body, 'ok' => false];
        }

        $decoded = $sliced['schema'];
        $paging = $sliced['paging'];
        $message = $sliced['message'] ?? $message;
        $activeMode = $sliced['mode'] ?? $activeMode;
    }

    $row['schema_json'] = $decoded;
    $payload = [
        'status' => true,
        'message' => $message,
        'data' => $row,
    ];
    // Envelope-only fields for optimized clients. Older apps ignore unknown keys.
    // Default full responses stay {status,message,data} with complete schema_json.
    if ($activeMode !== 'full') {
        $payload['part'] = $activeMode;
        if (is_array($paging)) {
            $payload['paging'] = $paging;
        }
    }

    $body = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    unset($decoded, $row, $paging);

    return ['body' => $body, 'ok' => true];
};

$body = false;

if ($serveStoredJson) {
    // Fast path used to splice raw schema_json. Still normalize so contract
    // fixes (send icon, sendButton, empty props {}, ParameterCard children)
    // reach the app without requiring a republish.
    $decodedFast = null;
    if (class_exists('\\App\\Services\\DeviceUiSchemaGenerator', false)) {
        $decodedFast = \App\Services\DeviceUiSchemaGenerator::decode($storedSchemaJson);
    } else {
        $decodedFast = json_decode($storedSchemaJson, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decodedFast)) {
            $decodedFast = null;
        }
    }

    if (is_array($decodedFast)) {
        $finalized = $efactor_finalize_schema_response($schema, $decodedFast);
        $body = $finalized['body'];
        unset($decodedFast, $storedSchemaJson, $schema);
    } else {
        $rowJson = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($rowJson !== false && substr($rowJson, -1) === '}') {
            $body = '{"status":true,"message":"Schema found","data":'
                . substr($rowJson, 0, -1)
                . ',"schema_json":' . $storedSchemaJson
                . '}}';
            unset($storedSchemaJson, $rowJson, $schema);
        } else {
            unset($rowJson);
            $body = false;
        }
    }
} else {
    $decoded = null;
    if ($storedSchemaJson !== '' && class_exists('\\App\\Services\\DeviceUiSchemaGenerator', false)) {
        $decoded = \App\Services\DeviceUiSchemaGenerator::decode($storedSchemaJson);
    } elseif ($storedSchemaJson !== '') {
        $decoded = json_decode($storedSchemaJson, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            $decoded = null;
        }
    }
    unset($storedSchemaJson);

    if (is_array($decoded)) {
        $finalized = $efactor_finalize_schema_response($schema, $decoded);
        $body = $finalized['body'];
        unset($decoded, $schema);
    } else {
        $body = json_encode([
            'status' => true,
            'message' => 'Schema found',
            'data' => $schema,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        unset($schema);
    }
}

if ($body === false) {
    echo json_encode([
        'status' => false,
        'message' => 'Schema encode failed',
    ]);
    exit();
}

$etag = '"' . substr(hash('sha256', $body), 0, 32) . '"';
header('ETag: ' . $etag);
if ($schemaUpdatedAt !== '') {
    $modified = strtotime($schemaUpdatedAt);
    if ($modified) {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $modified) . ' GMT');
    }
}

$ifNoneMatch = trim((string) ($_SERVER['HTTP_IF_NONE_MATCH'] ?? ''));
if ($ifNoneMatch !== '') {
    foreach (explode(',', $ifNoneMatch) as $candidate) {
        $candidate = trim($candidate);
        if (stripos($candidate, 'W/') === 0) {
            $candidate = trim(substr($candidate, 2));
        }
        if ($candidate === $etag || $candidate === '*') {
            http_response_code(304);
            exit();
        }
    }
}

if (!headers_sent()) {
    header('Content-Length: ' . strlen($body));
}
echo $body;
exit();
