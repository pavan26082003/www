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
require_once __DIR__ . '/schema_http_helpers.php';
require_once __DIR__ . '/schema_compact_helpers.php';

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
// Accept the mobile client's alias without changing canonical model_no precedence.
if ($model_no === '' && isset($_GET['model_id']) && is_scalar($_GET['model_id'])) {
    $model_no = trim((string) $_GET['model_id']);
}
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
$wantCompact = function_exists('efactor_schema_wants_compact') && efactor_schema_wants_compact();
$revisionOnly = function_exists('efactor_schema_is_revision_only') && efactor_schema_is_revision_only($part);
$deliveryMode = class_exists('\\App\\Services\\DeviceUiSchemaPageSlicer', false)
    ? \App\Services\DeviceUiSchemaPageSlicer::resolveMode($part, $page_no)
    : 'full';
if ($revisionOnly) {
    // Override slicer "full" when client asks for revision-only probe.
    $deliveryMode = 'revision';
}

if ($model_no === '') {
    echo json_encode([
        'status' => false,
        'message' => 'model_no is required',
    ]);
    exit();
}

$model_no_sql = $db->escape($model_no);

// Cheap metadata read first so a warm cache can skip ensure/normalize (same JSON shape).
$schemaMetaSql = "SELECT id, updated_at, node_id
        FROM device_ui_schemas
        WHERE model_name = '$model_no_sql'";
if ($node_id !== '') {
    $node_id_sql = $db->escape($node_id);
    $schemaMetaSql .= " AND node_id = '$node_id_sql'";
}
$schemaMetaSql .= ' ORDER BY updated_at DESC, id DESC LIMIT 1';
$schemaMeta = $db->get_row($schemaMetaSql);
if (!$schemaMeta && $requestedNodeId !== '') {
    $schemaMetaSql = "SELECT id, updated_at, node_id
            FROM device_ui_schemas
            WHERE model_name = '$model_no_sql'
            ORDER BY updated_at DESC, id DESC
            LIMIT 1";
    $schemaMeta = $db->get_row($schemaMetaSql);
}

$schemaUpdatedAtEarly = is_array($schemaMeta) && isset($schemaMeta['updated_at'])
    ? (string) $schemaMeta['updated_at']
    : '';

// Tiny revision-only probe — no ensure/normalize/full JSON (for future app short-circuit).
if ($revisionOnly) {
    $revision = 0;
    if ($schemaUpdatedAtEarly !== '') {
        $ts = strtotime($schemaUpdatedAtEarly);
        if ($ts) {
            $revision = (int) $ts;
        }
    }
    $revisionBody = json_encode([
        'status' => $revision > 0,
        'message' => $revision > 0 ? 'Schema revision' : 'Schema not found',
        'model_no' => $requestedModelNo,
        'node_id' => $requestedNodeId !== '' ? $requestedNodeId : null,
        'schemaRevision' => $revision,
        'revision' => $revision,
        'updated_at' => $schemaUpdatedAtEarly !== '' ? $schemaUpdatedAtEarly : null,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if (!is_string($revisionBody)) {
        $revisionBody = '{"status":false,"message":"Schema revision encode failed"}';
    }
    if (!headers_sent()) {
        header('X-Schema-Part: revision');
        if ($revision > 0) {
            header('ETag: "rev-' . $revision . '"');
        }
    }
    efactor_schema_emit_json_body($revisionBody);
    exit();
}

$schemaCacheKey = '';
$cachedBody = null;
if ($schemaUpdatedAtEarly !== '') {
    $schemaCacheKey = efactor_schema_cache_key([
        'api_schema',
        $requestedModelNo,
        $requestedNodeId,
        $deliveryMode,
        $part,
        $page_no,
        $wantCompact ? 'compact-v1' : 'full',
        $schemaUpdatedAtEarly,
    ]);
    // 10 minutes: layout rarely changes; Studio save bumps updated_at and misses cache.
    $cachedBody = efactor_schema_cache_get($schemaCacheKey, 600);
}

$body = false;
$schemaUpdatedAt = $schemaUpdatedAtEarly;
$servedFromCache = false;

if (is_string($cachedBody) && $cachedBody !== '') {
    $body = $cachedBody;
    $servedFromCache = true;
} else {
    // Create/refresh from current pages+APIs before serving (cache miss / first publish).
    try {
        require_once __DIR__ . '/bootstrap_device_ui_schema.php';
        if (function_exists('efactor_ensure_device_ui_schema')) {
            efactor_ensure_device_ui_schema($model_no, $node_id);
        }
    } catch (Throwable $exception) {
        error_log('v8 auto schema sync failed: ' . $exception->getMessage());
    }

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
}

if (!$servedFromCache && empty($schema)) {
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
if (!$servedFromCache) {
    if (isset($schema['schema_json']) && is_string($schema['schema_json'])) {
        $storedSchemaJson = $schema['schema_json'];
        unset($schema['schema_json']);
    }

    $schemaUpdatedAt = isset($schema['updated_at']) ? (string) $schema['updated_at'] : $schemaUpdatedAt;
    if ($requestedNodeId !== '') {
        $schema['node_id'] = $requestedNodeId;
    }
}

$isSplitSchema = $storedSchemaJson !== ''
    && strpos($storedSchemaJson, '"schemaStorage"') !== false
    && strpos($storedSchemaJson, 'split-v1') !== false;
$serveStoredJson = !$servedFromCache && $storedSchemaJson !== '' && !$isSplitSchema && $deliveryMode === 'full';

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
    $efactor_schema_api_base,
    $wantCompact
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
            $schemaUserId = \App\Services\DeviceUiSchemaNormalizer::resolveRequestUserId();
            $decoded = \App\Services\DeviceUiSchemaNormalizer::normalize(
                $decoded,
                $requestedModelNo,
                $resolvedNodeId,
                $schemaUserId
            );
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

    // Additive revision for app layout cache (numeric; app skips re-sync when ≤ cached).
    // Does not remove any fields — older clients ignore unknown keys.
    $revision = 0;
    if ($schemaUpdatedAt !== '') {
        $revisionTs = strtotime($schemaUpdatedAt);
        if ($revisionTs) {
            $revision = (int) $revisionTs;
        }
    }
    if ($revision <= 0) {
        $revision = (int) time();
    }
    $decoded['schemaRevision'] = $revision;
    $decoded['revision'] = $revision;
    if (!isset($decoded['meta']) || !is_array($decoded['meta'])) {
        $decoded['meta'] = [];
    }
    $decoded['meta']['schemaRevision'] = $revision;

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

    // Phase B opt-in compact projection (default remains full for older apps).
    if ($wantCompact && function_exists('efactor_schema_apply_compact')) {
        $decoded = efactor_schema_apply_compact($decoded, $requestedModelNo, $resolvedNodeId);
        $message = 'Schema found (compact-v1)';
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
    if ($wantCompact) {
        $payload['projection'] = 'compact-v1';
    }

    $body = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    unset($decoded, $row, $paging);

    return ['body' => $body, 'ok' => true];
};

if (!$servedFromCache) {
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

    // Refresh cache key if ensure/sync bumped updated_at.
    if ($schemaUpdatedAt !== '') {
        $schemaCacheKey = efactor_schema_cache_key([
            'api_schema',
            $requestedModelNo,
            $requestedNodeId,
            $deliveryMode,
            $part,
            $page_no,
            $wantCompact ? 'compact-v1' : 'full',
            $schemaUpdatedAt,
        ]);
        efactor_schema_cache_set($schemaCacheKey, $body);
    }
}

// ETag must be stable for identical schema content. Every request re-stamps
// generation/sync times (normalizedAt, generatedAt, generatedAtIso, updatedAt,
// mappingSyncedAt, row updated_at, connectivity timestamp) even when nothing
// else changed, so hashing the raw body produced a new ETag each call and 304
// never matched. Hash with only those timestamp values blanked; the body sent
// is unchanged, and any real schema change still alters the hash.
$etagSource = preg_replace(
    [
        '/"(normalizedAt|generatedAt|generatedAtIso|updatedAt|updated_at|mappingSyncedAt)":"[^"]*"/',
        '/"timestamp":\d+/',
    ],
    [
        '"$1":""',
        '"timestamp":0',
    ],
    $body
);
if (!is_string($etagSource)) {
    $etagSource = $body;
}
$etag = '"' . substr(hash('sha256', $etagSource), 0, 32) . '"';
unset($etagSource);
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
        // Apache mod_deflate/mod_brotli append -gzip/-br to the ETag it forwards;
        // clients echo that suffixed value back, so accept it as the same tag.
        $candidate = trim((string) $candidate, '"');
        $candidate = preg_replace('/-(gzip|br|deflate)$/', '', $candidate);
        if ($candidate === trim($etag, '"') || $candidate === '*') {
            http_response_code(304);
            exit();
        }
    }
}

if (!headers_sent()) {
    header('X-Schema-Cache: ' . ($servedFromCache ? 'HIT' : 'MISS'));
    header('X-Schema-Projection: ' . ($wantCompact ? 'compact-v1' : 'full'));
}
efactor_schema_emit_json_body($body);
exit();
