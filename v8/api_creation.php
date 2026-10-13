<?php
/**
 * Production App Home public schema API.
 * Deploy this file to: /v8/api_creation.php
 *
 * GET ?action=get_schema&user_id=N
 *
 * Response shape (unchanged):
 * { success, user_id, schema_version, updated_at, contentHash, schema }
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: private, no-cache, max-age=0, must-revalidate');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function api_creation_json(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function api_creation_require_bootstrap(): void
{
    $dir = str_replace('\\', '/', __DIR__);
    $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
    $candidates = array(
        $dir . '/../app/Libraries/AppHomeBootstrap.php',
        $dir . '/../../app/Libraries/AppHomeBootstrap.php',
        $dir . '/Libraries/AppHomeBootstrap.php',
    );
    if ($docRoot !== '') {
        $candidates[] = $docRoot . '/app/Libraries/AppHomeBootstrap.php';
    }

    foreach ($candidates as $path) {
        $real = realpath($path);
        if (!$real || !is_file($real)) {
            continue;
        }
        require_once $real;
        if (class_exists(\App\Libraries\AppHomeBootstrap::class, false)) {
            return;
        }
    }

    api_creation_json(array(
        'success' => false,
        'message' => 'App Home bootstrap unavailable.',
    ), 500);
}

$action = strtolower(trim((string) ($_GET['action'] ?? 'get_schema')));
if ($action !== 'get_schema') {
    api_creation_json(array(
        'success' => false,
        'message' => 'Unknown action. Use action=get_schema.',
        'supported' => array('get_schema'),
    ), 400);
}

$userId = (int) ($_GET['user_id'] ?? 0);
if ($userId < 1) {
    api_creation_json(array(
        'success' => false,
        'message' => 'user_id is required and must be a positive integer.',
    ), 400);
}

api_creation_require_bootstrap();

try {
    $model = \App\Libraries\AppHomeBootstrap::appHomePageModel();
    if ($model === null) {
        api_creation_json(array(
            'success' => false,
            'message' => 'Unable to initialize App Home model.',
        ), 500);
    }

    $schema = $model->getSchema($userId);
    if (!is_array($schema) || empty($schema)) {
        api_creation_json(array(
            'success' => false,
            'message' => 'No saved App Home schema was found for this user.',
            'user_id' => $userId,
        ), 404);
    }

    // Serve-time P2 fix: merge 0–599dp into compact without changing other keys.
    if (method_exists($model, 'normalizeSmartphoneBreakpoints')) {
        $schema = $model->normalizeSmartphoneBreakpoints($schema);
    }

    $version = (string) ($schema['schemaVersion'] ?? $schema['schema_version'] ?? '1.0.0');
    $updatedAt = (string) ($schema['generatedAt'] ?? $schema['updated_at'] ?? '');
    $hashSource = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $contentHash = is_string($hashSource) ? hash('sha256', $hashSource) : '';

    api_creation_json(array(
        'success' => true,
        'user_id' => $userId,
        'schema_version' => $version,
        'updated_at' => $updatedAt !== '' ? $updatedAt : null,
        'contentHash' => $contentHash,
        'schema' => $schema,
    ));
} catch (Throwable $e) {
    api_creation_json(array(
        'success' => false,
        'message' => 'Unable to load the app home schema.',
    ), 500);
}
