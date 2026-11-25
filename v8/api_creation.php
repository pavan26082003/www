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
header('Vary: Accept-Encoding');

@ini_set('display_errors', '0');
@ini_set('memory_limit', '512M');
@ini_set('max_execution_time', '120');
@set_time_limit(120);

require_once __DIR__ . '/schema_http_helpers.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function api_creation_json(array $payload, int $status = 200): void
{
    http_response_code($status);
    $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($body)) {
        $body = '{"success":false,"message":"Schema encode failed"}';
        http_response_code(500);
    }
    efactor_schema_emit_json_body($body);
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

// Warm cache: same JSON shape; keyed by user + schema contentHash/updated stamp when known.
// photo_webview_v2: bust stale cache that used navigate for profile-photo-options.
$homeCacheProbeKey = efactor_schema_cache_key(array('api_creation', 'get_schema', 'photo_webview_v2', (string) $userId));
// Probe uses a short-lived sticky key; real key includes contentHash after build.
$cachedHome = efactor_schema_cache_get($homeCacheProbeKey, 60);
// Stale HIT without open-url photo actions must rebuild so Account avatar tap works.
$cachedHasPhotoActions = is_string($cachedHome)
    && strpos($cachedHome, 'profile-photo-options') !== false
    && strpos($cachedHome, 'app-home-page/profile-photo') !== false
    && preg_match('/"profile-photo-options"\s*:\s*\{.{0,500}?"type"\s*:\s*"open-url"/s', $cachedHome) === 1;
if (is_string($cachedHome) && $cachedHome !== '' && strpos($cachedHome, '"success":true') !== false && $cachedHasPhotoActions) {
    $cachedHash = '';
    if (preg_match('/"contentHash":"([a-f0-9]+)"/', $cachedHome, $hashMatch)) {
        $cachedHash = substr($hashMatch[1], 0, 32);
    }
    if (!headers_sent()) {
        header('X-Schema-Cache: HIT');
        if ($cachedHash !== '') {
            header('ETag: "' . $cachedHash . '"');
        }
    }
    $ifNoneMatch = trim((string) ($_SERVER['HTTP_IF_NONE_MATCH'] ?? ''));
    if ($ifNoneMatch !== '' && $cachedHash !== '' && trim($ifNoneMatch, '"') === $cachedHash) {
        http_response_code(304);
        exit;
    }
    efactor_schema_emit_json_body($cachedHome);
    exit;
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

    $schema = array();
    $loadError = '';
    try {
        $schema = $model->getSchema($userId);
    } catch (Throwable $e) {
        $loadError = $e->getMessage();
        error_log('api_creation getSchema failed user_id=' . $userId . ': ' . $loadError);
        if (method_exists($model, 'getStoredSchemaOnly')) {
            $schema = $model->getStoredSchemaOnly($userId);
        }
    }
    if ((!is_array($schema) || empty($schema)) && method_exists($model, 'getStoredSchemaOnly')) {
        $schema = $model->getStoredSchemaOnly($userId);
    }
    if (!is_array($schema) || empty($schema)) {
        $meta = method_exists($model, 'getSettingsMeta') ? $model->getSettingsMeta($userId) : array();
        api_creation_json(array(
            'success' => false,
            'message' => 'No saved App Home schema was found for this user.',
            'user_id' => $userId,
            'has_layout' => !empty($meta['has_layout']),
            'has_schema' => !empty($meta['has_schema']),
            'hint' => !empty($meta['has_layout']) && empty($meta['has_schema'])
                ? 'Layout exists but schema_json is empty. Click Save again in App Home Designer after Schema is generated.'
                : 'No app_home_page_settings row / schema for this user_id.',
            'error' => $loadError !== '' ? $loadError : null,
        ), 404);
    }

    // Serve-time P2 fix: merge 0–599dp into compact without changing other keys.
    if (method_exists($model, 'normalizeSmartphoneBreakpoints')) {
        $schema = $model->normalizeSmartphoneBreakpoints($schema);
    }
    // Grid featureCard rows are not Home components. Drop them if a saved schema still has them.
    if (class_exists(\App\Libraries\GridSduiSchemaBuilder::class)) {
        $schema = \App\Libraries\GridSduiSchemaBuilder::omitDisallowedGridExploreComponents($schema);
    }
    // Always enrich photo + rating open-url here so fallback/cached-miss paths still
    // expose Account avatar tap → in-app WebView (same contract as Rating).
    if (method_exists($model, 'prepareRatingOpenUrlForClient')) {
        $schema = $model->prepareRatingOpenUrlForClient($schema, $userId);
    }
    if (method_exists($model, 'attachProfilePhotoOptions')) {
        $schema = $model->attachProfilePhotoOptions($schema, $userId);
    }

    $version = (string) ($schema['schemaVersion'] ?? $schema['schema_version'] ?? '1.0.0');
    $updatedAt = (string) ($schema['generatedAt'] ?? $schema['updated_at'] ?? '');

    // Additive revision for layout freshness (older clients ignore unknown keys).
    $revision = 0;
    if ($updatedAt !== '') {
        $ts = strtotime(preg_replace('/\s+IST$/', '', $updatedAt));
        if ($ts) {
            $revision = (int) $ts;
        }
    }
    if (!isset($schema['schemaRevision'])) {
        $schema['schemaRevision'] = $revision > 0 ? $revision : 1;
    }
    if (!isset($schema['revision'])) {
        $schema['revision'] = $schema['schemaRevision'];
    }
    $revision = (int) $schema['schemaRevision'];

    $hashSource = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $contentHash = is_string($hashSource) ? hash('sha256', $hashSource) : '';

    $payload = array(
        'success' => true,
        'user_id' => $userId,
        'schema_version' => $version,
        'updated_at' => $updatedAt !== '' ? $updatedAt : null,
        'contentHash' => $contentHash,
        'schemaRevision' => $revision,
        'revision' => $revision,
        'schema' => $schema,
    );
    $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($body)) {
        api_creation_json(array(
            'success' => false,
            'message' => 'Unable to encode the app home schema.',
        ), 500);
    }

    efactor_schema_cache_set($homeCacheProbeKey, $body);
    if (!headers_sent()) {
        header('X-Schema-Cache: MISS');
        if ($contentHash !== '') {
            header('ETag: "' . substr($contentHash, 0, 32) . '"');
        }
    }
    http_response_code(200);
    efactor_schema_emit_json_body($body);
    exit;
} catch (Throwable $e) {
    error_log('api_creation get_schema failed: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    api_creation_json(array(
        'success' => false,
        'message' => 'Unable to load the app home schema.',
        'error' => $e->getMessage() . ' @ ' . basename($e->getFile()) . ':' . $e->getLine(),
        'user_id' => $userId,
    ), 500);
}
