<?php

declare(strict_types=1);

/**
 * v8/api_auth_schema.php
 *
 * Compatible with:
 * - PHP 7.4.x
 * - CodeIgniter 4.1.1
 * - MySQL 5.x
 *
 * GET:
 *   Return latest stored auth UI schema.
 *
 * POST:
 *   1) Accept an already-generated schema, OR
 *   2) Generate the schema locally from App\Services\AuthUiSchemaBuilder,
 *   3) Store the resulting schema in auth_ui_schemas.
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With');
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

define('AUTH_SCHEMA_KEY', 'efactor-auth');
define('AUTH_SCHEMA_TYPE', 'auth-ui');

function apiRespond(array $payload, int $statusCode = 200): void
{
    if (array_key_exists('status', $payload) && !array_key_exists('success', $payload)) {
        $payload['success'] = (bool) $payload['status'];
    }

    if (array_key_exists('success', $payload) && !array_key_exists('status', $payload)) {
        $payload['status'] = (bool) $payload['success'];
    }

    http_response_code($statusCode);

    $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

    if (!empty($_GET['pretty']) || !empty($_GET['pretty_print'])) {
        $flags |= JSON_PRETTY_PRINT;
    }

    $json = json_encode($payload, $flags);

    if ($json === false) {
        http_response_code(500);
        echo '{"status":false,"success":false,"message":"Unable to encode API response."}';
        exit;
    }

    echo $json;
    exit;
}

function authSchemaConfigPath(): string
{
    $dir = __DIR__;

    for ($i = 0; $i < 6; $i++) {
        $paths = [
            $dir . '/config.php',
            $dir . '/v8/config.php',
        ];

        foreach ($paths as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        $parent = dirname($dir);
        if ($parent === $dir) {
            break;
        }
        $dir = $parent;
    }

    return '';
}

$configPath = authSchemaConfigPath();

if ($configPath === '') {
    apiRespond([
        'status' => false,
        'message' => 'v8/config.php was not found.',
        'checked_path' => __DIR__ . '/config.php',
    ], 500);
}

require_once $configPath;

function dbEscapeValue($value): string
{
    global $db;

    if (!isset($db)) {
        apiRespond([
            'status' => false,
            'message' => 'Database object $db was not created by v8/config.php.',
        ], 500);
    }

    return $db->escape((string) $value);
}

function latestAuthSchemaRow(int $id = 0)
{
    global $db;

    $sql = 'SELECT * FROM auth_ui_schemas';

    if ($id > 0) {
        $sql .= ' WHERE id=' . (int) $id;
    } else {
        $sql .= " WHERE schema_key='" . dbEscapeValue(AUTH_SCHEMA_KEY) . "' AND status=1";
    }

    $sql .= ' ORDER BY id DESC LIMIT 1';

    return $db->get_row($sql, true);
}

function ensureAuthSchemaTable(): void
{
    global $db;

    $sql = "CREATE TABLE IF NOT EXISTS `auth_ui_schemas` ("
        . "`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,"
        . "`schema_key` VARCHAR(100) NOT NULL,"
        . "`schema_type` VARCHAR(50) NOT NULL DEFAULT 'auth-ui',"
        . "`schema_version` INT(11) UNSIGNED NOT NULL DEFAULT 1,"
        . "`schema_hash` VARCHAR(64) NOT NULL,"
        . "`schema_json` LONGTEXT NOT NULL,"
        . "`status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,"
        . "`created_at` DATETIME NULL DEFAULT NULL,"
        . "`updated_at` DATETIME NULL DEFAULT NULL,"
        . "PRIMARY KEY (`id`),"
        . "KEY `auth_ui_schemas_schema_key` (`schema_key`),"
        . "KEY `auth_ui_schemas_schema_key_status` (`schema_key`,`status`)"
        . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";

    if (!$db->query($sql)) {
        apiRespond([
            'status' => false,
            'message' => 'Unable to create or verify auth_ui_schemas table.',
        ], 500);
    }
}
function rowMeta($row, $schema, string $schemaJson): array
{
    return [
        'id' => (int) (isset($row->id) ? $row->id : 0),
        'schema_key' => isset($row->schema_key) ? $row->schema_key : AUTH_SCHEMA_KEY,
        'schema_type' => isset($row->schema_type) ? $row->schema_type : AUTH_SCHEMA_TYPE,
        'schema_version' => (int) (
            isset($row->schema_version)
                ? $row->schema_version
                : (is_array($schema) && isset($schema['schemaVersion']) ? $schema['schemaVersion'] : 1)
        ),
        'schema_hash' => isset($row->schema_hash)
            ? $row->schema_hash
            : ($schemaJson !== '' ? hash('sha256', $schemaJson) : null),
        'status' => (int) (isset($row->status) ? $row->status : 1),
        'created_at' => isset($row->created_at) ? $row->created_at : null,
        'updated_at' => isset($row->updated_at) ? $row->updated_at : null,
    ];
}

function normalizeAuthSchemaResponse($row): array
{
    $schemaJson = isset($row->schema_json) ? (string) $row->schema_json : '';
    $schema = json_decode($schemaJson, true);

    if (!is_array($schema)) {
        return [
            'status' => false,
            'message' => 'Latest auth UI schema row contains invalid JSON.',
            'data' => rowMeta($row, null, $schemaJson),
            'schema' => null,
        ];
    }

    return [
        'status' => true,
        'message' => 'Auth UI schema loaded successfully.',
        'data' => rowMeta($row, $schema, $schemaJson),
        'schema' => $schema,
    ];
}

function requestBody(): array
{
    $contentType = isset($_SERVER['CONTENT_TYPE'])
        ? strtolower((string) $_SERVER['CONTENT_TYPE'])
        : '';

    if (strpos($contentType, 'application/json') !== false) {
        $raw = file_get_contents('php://input');

        if (!is_string($raw) || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    if (!empty($_POST) && is_array($_POST)) {
        return $_POST;
    }

    return [];
}

function currentOrigin(): string
{
    $https = false;

    if (
        isset($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] !== ''
        && strtolower((string) $_SERVER['HTTPS']) !== 'off'
    ) {
        $https = true;
    }

    if (
        isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
        && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https'
    ) {
        $https = true;
    }

    $host = isset($_SERVER['HTTP_HOST'])
        ? trim((string) $_SERVER['HTTP_HOST'])
        : 'localhost';

    return ($https ? 'https' : 'http') . '://' . $host;
}

if (!function_exists('helper')) {
    function helper($name): void
    {
    }
}

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        return rtrim(currentOrigin(), '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('site_url')) {
    function site_url(string $path = ''): string
    {
        return rtrim(currentOrigin(), '/') . '/' . ltrim($path, '/');
    }
}

function authSchemaBuilderPath(): string
{
    $dir = str_replace('\\', '/', __DIR__);
    $candidates = [];

    for ($i = 0; $i < 8; $i++) {
        $candidates[] = $dir . '/app/Services/AuthUiSchemaBuilder.php';
        $candidates[] = $dir . '/Services/AuthUiSchemaBuilder.php';
        $parent = dirname($dir);
        if ($parent === $dir) {
            break;
        }
        $dir = $parent;
    }

    $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
    if ($docRoot !== '') {
        $candidates[] = $docRoot . '/app/Services/AuthUiSchemaBuilder.php';
        $candidates[] = $docRoot . '/../app/Services/AuthUiSchemaBuilder.php';
        $candidates[] = dirname($docRoot) . '/app/Services/AuthUiSchemaBuilder.php';
    }

    foreach ($candidates as $path) {
        $real = realpath($path);
        if ($real && is_file($real)) {
            return $real;
        }
    }

    return '';
}

/**
 * Real sd_auth view directory (welcome/login/signup), not the /v8 API folder.
 */
function authSchemaViewsDir(): string
{
    $builderPath = authSchemaBuilderPath();
    if ($builderPath !== '') {
        $appRoot = dirname(dirname($builderPath));
        $dir = $appRoot . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'sd_auth';
        if (is_dir($dir)) {
            return $dir;
        }
    }

    if (is_file(__DIR__ . DIRECTORY_SEPARATOR . 'welcome.php')) {
        return __DIR__;
    }

    $dir = str_replace('\\', '/', __DIR__);
    for ($i = 0; $i < 8; $i++) {
        foreach ([
            $dir . '/app/Views/sd_auth',
            $dir . '/Views/sd_auth',
        ] as $candidate) {
            if (is_dir($candidate) && is_file($candidate . '/welcome.php')) {
                return $candidate;
            }
        }
        $parent = dirname($dir);
        if ($parent === $dir) {
            break;
        }
        $dir = $parent;
    }

    $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
    if ($docRoot !== '') {
        foreach ([
            $docRoot . '/app/Views/sd_auth',
            dirname($docRoot) . '/app/Views/sd_auth',
        ] as $candidate) {
            if (is_dir($candidate) && is_file($candidate . '/welcome.php')) {
                return $candidate;
            }
        }
    }

    return '';
}

/* ─────────────────────────────────────────────────────────────────────────
 * Fix #13 + #14: schema_changed WebSocket notification — notify-only.
 * Emits a schema_changed event through the eFactor Cloud WebSocket ONLY
 * after the schema row is fully committed. Authoritative patch/full schema
 * is always fetched via HTTPS — WebSocket is a nudge, not a data channel.
 * ─────────────────────────────────────────────────────────────────────────*/
function notifySchemaChanged(int $rowId, int $revision, string $hash, string $syncToken): void
{
    // Skip if no WebSocket push infrastructure is configured on this server.
    $pushUrl = defined('IOT_WEBSOCKET_NOTIFY_URL') ? IOT_WEBSOCKET_NOTIFY_URL : '';
    if ($pushUrl === '') {
        // Fallback: log the event so server-side consumers can poll for changes.
        error_log('[auth_schema] schema_changed revision=' . $revision . ' hash=' . substr($hash, 0, 8) . ' sync_token=' . $syncToken);
        return;
    }

    $payload = json_encode([
        'event'        => 'schema_changed',
        'schema_id'    => AUTH_SCHEMA_KEY,
        'revision'     => $revision,
        'content_hash' => $hash,
        'sync_token'   => $syncToken,
        'timestamp'    => date(DATE_ATOM),
    ]);

    // Best-effort fire-and-forget. A failure here must NOT roll back the schema commit.
    try {
        $ctx = stream_context_create(['http' => [
            'method'      => 'POST',
            'header'      => "Content-Type: application/json\r\n",
            'content'     => $payload,
            'timeout'     => 3,
            'ignore_errors' => true,
        ]]);
        @file_get_contents($pushUrl, false, $ctx);
    } catch (Throwable $e) {
        error_log('[auth_schema] notifySchemaChanged failed: ' . $e->getMessage());
    }
}

/* ─────────────────────────────────────────────────────────────────────────
 * Fix #8: metadata endpoint — returns revision + hash without full schema.
 * Client calls this to check staleness before deciding to fetch delta/full.
 * ─────────────────────────────────────────────────────────────────────────*/
function authSchemaMeta(): array
{
    $row = latestAuthSchemaRow();
    if (!$row) {
        return ['status' => false, 'message' => 'No schema found.'];
    }
    $json  = (string) ($row->schema_json ?? '');
    $data  = $json !== '' ? json_decode($json, true) : null;
    return [
        'status'       => true,
        'id'           => (int) $row->id,
        'schema_key'   => (string) ($row->schema_key ?? AUTH_SCHEMA_KEY),
        'revision'     => (int) $row->schema_version,
        'contentHash'  => (string) ($row->schema_hash ?? ''),
        'syncToken'    => is_array($data) ? ($data['syncToken'] ?? null) : null,
        'updated_at'   => (string) ($row->updated_at ?? ''),
    ];
}

/* ─────────────────────────────────────────────────────────────────────────
 * Fix #9 + #11: manifest endpoint — lists all revisions + deleted schemas.
 * Fix #11: includes full_required flag when a delta cannot be computed.
 * ─────────────────────────────────────────────────────────────────────────*/
function authSchemaManifest(): array
{
    global $db;

    $sql = "SELECT id, schema_key, schema_type, schema_version, schema_hash, status, created_at, updated_at"
         . " FROM auth_ui_schemas WHERE schema_key='" . dbEscapeValue(AUTH_SCHEMA_KEY) . "'"
         . " ORDER BY schema_version ASC";
    $rows = $db->get_results($sql, true) ?? [];

    $schemas = [];
    $deleted = [];
    foreach ((array) $rows as $r) {
        $entry = [
            'id'          => (int) ($r->id ?? 0),
            'revision'    => (int) ($r->schema_version ?? 0),
            'contentHash' => (string) ($r->schema_hash ?? ''),
            'status'      => (int) ($r->status ?? 1),
            'updated_at'  => (string) ($r->updated_at ?? ''),
        ];
        if ((int) ($r->status ?? 1) === 0) {
            $deleted[] = $entry;
        } else {
            $schemas[] = $entry;
        }
    }

    $latest = end($schemas);
    $syncToken = $latest ? ('auth-' . $latest['id'] . '-' . substr($latest['contentHash'], 0, 8)) : null;

    return [
        'status'    => true,
        'schemas'   => $schemas,
        'deleted'   => $deleted,
        'syncToken' => $syncToken,
    ];
}

/* ─────────────────────────────────────────────────────────────────────────
 * Fix #10: RFC-6902 JSON Patch delta endpoint.
 * from_revision, base_hash → patch array (or full_required if gap too large).
 * Fix #11: full_required fallback when delta cannot be computed.
 * Fix #12: sync_token for missed-event recovery.
 * ─────────────────────────────────────────────────────────────────────────*/
function authSchemaDelta(int $fromRevision, string $baseHash): array
{
    global $db;

    $latest = latestAuthSchemaRow();
    if (!$latest) {
        return ['status' => false, 'message' => 'No schema found.', 'full_required' => true];
    }

    $toRevision  = (int) $latest->schema_version;
    $targetHash  = (string) $latest->schema_hash;
    $latestJson  = (string) $latest->schema_json;
    $syncToken   = null;
    if ($latestJson !== '') {
        $d = json_decode($latestJson, true);
        $syncToken = is_array($d) ? ($d['syncToken'] ?? null) : null;
    }

    // Fix #11: if the gap is too large or hash can't be verified, require a full fetch.
    $revisionGap = $toRevision - $fromRevision;
    if ($revisionGap <= 0) {
        return [
            'status'        => true,
            'from_revision' => $fromRevision,
            'to_revision'   => $toRevision,
            'base_hash'     => $baseHash,
            'target_hash'   => $targetHash,
            'patch'         => [],
            'full_required' => false,
            'sync_token'    => $syncToken,
        ];
    }

    if ($revisionGap > 10 || $baseHash === '') {
        return [
            'status'        => true,
            'from_revision' => $fromRevision,
            'to_revision'   => $toRevision,
            'base_hash'     => $baseHash,
            'target_hash'   => $targetHash,
            'patch'         => null,
            'full_required' => true,
            'sync_token'    => $syncToken,
            'message'       => 'Revision gap too large or base_hash missing — fetch full schema.',
        ];
    }

    // Fetch the from-revision row to build an actual diff.
    $fromSql = "SELECT schema_json FROM auth_ui_schemas"
             . " WHERE schema_key='" . dbEscapeValue(AUTH_SCHEMA_KEY) . "'"
             . " AND schema_version=" . $fromRevision
             . " LIMIT 1";
    $fromRow = $db->get_row($fromSql, true);
    if (!$fromRow || empty($fromRow->schema_json)) {
        return [
            'status'        => true,
            'from_revision' => $fromRevision,
            'to_revision'   => $toRevision,
            'patch'         => null,
            'full_required' => true,
            'sync_token'    => $syncToken,
            'message'       => 'From-revision not found in history — fetch full schema.',
        ];
    }

    $fromData   = json_decode((string) $fromRow->schema_json, true);
    $toData     = json_decode($latestJson, true);
    $actualBase = hash('sha256', (string) $fromRow->schema_json);

    // Verify the client's base_hash matches the stored from-revision.
    if ($baseHash !== '' && $baseHash !== $actualBase) {
        return [
            'status'        => true,
            'from_revision' => $fromRevision,
            'to_revision'   => $toRevision,
            'base_hash'     => $baseHash,
            'target_hash'   => $targetHash,
            'patch'         => null,
            'full_required' => true,
            'sync_token'    => $syncToken,
            'message'       => 'base_hash mismatch — fetch full schema.',
        ];
    }

    // Build RFC-6902 patch — simple top-level key diff (sufficient for auth schema evolution).
    $patch = [];
    if (is_array($fromData) && is_array($toData)) {
        foreach ($toData as $key => $value) {
            if (!array_key_exists($key, $fromData)) {
                $patch[] = ['op' => 'add',     'path' => '/' . $key, 'value' => $value];
            } elseif (json_encode($fromData[$key]) !== json_encode($value)) {
                $patch[] = ['op' => 'replace', 'path' => '/' . $key, 'value' => $value];
            }
        }
        foreach ($fromData as $key => $value) {
            if (!array_key_exists($key, $toData)) {
                $patch[] = ['op' => 'remove', 'path' => '/' . $key];
            }
        }
    }

    return [
        'status'        => true,
        'from_revision' => $fromRevision,
        'to_revision'   => $toRevision,
        'base_hash'     => $actualBase,
        'target_hash'   => $targetHash,
        'patch'         => $patch,
        'full_required' => false,
        'sync_token'    => $syncToken,
    ];
}

function generateSchemaLocally(): array
{
    $builderPath = authSchemaBuilderPath();

    if ($builderPath === '') {
        return [
            'ok' => false,
            'schema' => [],
            'error' => 'App\\Services\\AuthUiSchemaBuilder.php was not found.',
        ];
    }

    require_once $builderPath;

    if (!class_exists('\\App\\Services\\AuthUiSchemaBuilder')) {
        return [
            'ok' => false,
            'schema' => [],
            'error' => 'AuthUiSchemaBuilder class could not be loaded.',
        ];
    }

    try {
        $builder = new \App\Services\AuthUiSchemaBuilder();
        $schema = $builder->build();

        return [
            'ok' => is_array($schema) && !empty($schema),
            'schema' => is_array($schema) ? $schema : [],
        ];
    } catch (Throwable $exception) {
        return [
            'ok' => false,
            'schema' => [],
            'error' => $exception->getMessage(),
        ];
    }
}
function storeAuthSchema(array $schema): int
{
    global $db;

    if (empty($schema['schemaId'])) {
        $schema['schemaId'] = AUTH_SCHEMA_KEY;
    }

    if (empty($schema['schemaType'])) {
        $schema['schemaType'] = AUTH_SCHEMA_TYPE;
    }

    if (empty($schema['schemaVersion'])) {
        $schema['schemaVersion'] = 1;
    }

    $schema['generatedAt'] = date(DATE_ATOM);

    // Fix #6: compute next monotonic revision.
    $latestRow = latestAuthSchemaRow();
    $nextRevision = isset($latestRow->schema_version) ? ((int) $latestRow->schema_version + 1) : 1;
    $schema['revision'] = $nextRevision;
    $schema['schemaVersion'] = $nextRevision;

    // Fix #12: stamp syncToken (will be updated with the row id after insert).
    $schema['syncToken'] = null;

    $json = json_encode(
        $schema,
        JSON_UNESCAPED_SLASHES
        | JSON_UNESCAPED_UNICODE
        | JSON_PRETTY_PRINT
    );

    if (!is_string($json)) {
        apiRespond([
            'status' => false,
            'message' => 'Unable to encode auth UI schema.',
            'json_error' => function_exists('json_last_error_msg')
                ? json_last_error_msg()
                : (string) json_last_error(),
        ], 500);
    }

    // Fix #7: canonical SHA-256 hash of the final JSON.
    $hash = hash('sha256', $json);
    $schema['contentHash'] = $hash;

    // Re-encode with the hash stamped in.
    $json = json_encode(
        $schema,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    $now = date('Y-m-d H:i:s');

    $sql = "INSERT INTO auth_ui_schemas "
        . "(schema_key, schema_type, schema_version, schema_hash, schema_json, status, created_at, updated_at) VALUES ("
        . "'" . dbEscapeValue((string) $schema['schemaId']) . "',"
        . "'" . dbEscapeValue((string) $schema['schemaType']) . "',"
        . (int) $nextRevision . ","
        . "'" . dbEscapeValue($hash) . "',"
        . "'" . dbEscapeValue($json) . "',"
        . "1,"
        . "'" . dbEscapeValue($now) . "',"
        . "'" . dbEscapeValue($now) . "'"
        . ")";

    if (!$db->query($sql)) {
        apiRespond([
            'status' => false,
            'message' => 'Unable to store auth UI schema in database.',
        ], 500);
    }

    if (method_exists($db, 'lastid')) {
        $newId = (int) $db->lastid();
    } else {
        $row = latestAuthSchemaRow();
        $newId = isset($row->id) ? (int) $row->id : 0;
    }

    // Fix #12: update syncToken to the row id now that we have it.
    if ($newId > 0) {
        $syncToken = 'auth-' . $newId . '-' . substr($hash, 0, 8);
        $db->query(
            "UPDATE auth_ui_schemas SET schema_json = REPLACE(schema_json, '\"syncToken\":null', '\"syncToken\":\"" . dbEscapeValue($syncToken) . "\"') WHERE id=" . $newId
        );
    }

    // Fix #13: emit schema_changed WebSocket notification AFTER the schema is fully persisted.
    // Notify-only — authoritative schema stays on HTTPS.
    notifySchemaChanged($newId, $nextRevision, $hash, isset($syncToken) ? $syncToken : '');

    return $newId;
}

/**
 * Fingerprint of AuthUiSchemaBuilder + sd_auth view sources that drive the schema.
 */
function authSchemaSourceFingerprint(): string
{
    $parts = [];
    $builderPath = authSchemaBuilderPath();
    if ($builderPath !== '' && is_file($builderPath)) {
        $parts[] = 'builder:' . hash_file('sha256', $builderPath);
    }

    $viewDir = authSchemaViewsDir();
    if ($viewDir !== '' && is_dir($viewDir)) {
        foreach (glob($viewDir . DIRECTORY_SEPARATOR . '*.php') ?: [] as $file) {
            $base = basename((string) $file);
            if ($base === 'api_auth_schema.php' || strpos($base, 'Backup') === 0) {
                continue;
            }
            $parts[] = $base . ':' . hash_file('sha256', (string) $file);
        }
    }

    sort($parts);

    return $parts === [] ? '' : hash('sha256', implode('|', $parts));
}

/**
 * Stable hash of schema content excluding volatile runtime fields.
 */
function authSchemaStructuralHash(array $schema): string
{
    $copy = $schema;
    unset(
        $copy['generatedAt'],
        $copy['revision'],
        $copy['syncToken'],
        $copy['contentHash'],
        $copy['sourceFingerprint'],
        $copy['mappingSyncedAt']
    );
    if (isset($copy['meta']) && is_array($copy['meta'])) {
        unset(
            $copy['meta']['generatedAt'],
            $copy['meta']['mappingSyncedAt'],
            $copy['meta']['sourceFingerprint']
        );
    }

    $json = json_encode($copy, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    return $json === false ? '' : hash('sha256', $json);
}

/**
 * Ensure the stored auth schema matches current builder/view sources.
 * Creates or inserts a new revision only when structural content changed.
 * Failures leave the existing row available.
 *
 * @return array{ok:bool,changed:bool,row?:object|null,schema?:array|null,error?:string,kept_existing?:bool}
 */
function syncAuthSchemaFromSources(bool $force = false): array
{
    static $syncing = false;
    if ($syncing) {
        return ['ok' => false, 'changed' => false, 'error' => 'reentrant'];
    }

    $syncing = true;
    try {
        ensureAuthSchemaTable();
        $row = latestAuthSchemaRow();
        $sourceFp = authSchemaSourceFingerprint();

        if ($row && !$force && $sourceFp !== '') {
            $stored = json_decode((string) ($row->schema_json ?? ''), true);
            $storedFp = '';
            if (is_array($stored)) {
                $storedFp = (string) ($stored['sourceFingerprint'] ?? ($stored['meta']['sourceFingerprint'] ?? ''));
            }
            if ($storedFp !== '' && $storedFp === $sourceFp) {
                return [
                    'ok' => true,
                    'changed' => false,
                    'row' => $row,
                    'schema' => is_array($stored) ? $stored : null,
                ];
            }
        }

        $generated = generateSchemaLocally();
        if (empty($generated['ok']) || empty($generated['schema']) || !is_array($generated['schema'])) {
            if ($row) {
                $stored = json_decode((string) ($row->schema_json ?? ''), true);

                return [
                    'ok' => true,
                    'changed' => false,
                    'kept_existing' => true,
                    'row' => $row,
                    'schema' => is_array($stored) ? $stored : null,
                    'error' => (string) ($generated['error'] ?? 'generate_failed'),
                ];
            }

            return [
                'ok' => false,
                'changed' => false,
                'error' => (string) ($generated['error'] ?? 'generate_failed'),
            ];
        }

        $fresh = $generated['schema'];
        if ($sourceFp !== '') {
            $fresh['sourceFingerprint'] = $sourceFp;
            if (!isset($fresh['meta']) || !is_array($fresh['meta'])) {
                $fresh['meta'] = [];
            }
            $fresh['meta']['sourceFingerprint'] = $sourceFp;
            $fresh['meta']['mappingSyncedAt'] = date('c');
        }

        $freshHash = authSchemaStructuralHash($fresh);
        if ($row && !$force) {
            $stored = json_decode((string) ($row->schema_json ?? ''), true);
            if (is_array($stored) && authSchemaStructuralHash($stored) === $freshHash) {
                return [
                    'ok' => true,
                    'changed' => false,
                    'row' => $row,
                    'schema' => $stored,
                ];
            }
        }

        $newId = storeAuthSchema($fresh);
        $newRow = latestAuthSchemaRow($newId > 0 ? $newId : 0);
        $schema = null;
        if ($newRow) {
            $decoded = json_decode((string) ($newRow->schema_json ?? ''), true);
            $schema = is_array($decoded) ? $decoded : $fresh;
        }

        return [
            'ok' => $newId > 0,
            'changed' => $newId > 0,
            'row' => $newRow,
            'schema' => $schema,
        ];
    } catch (Throwable $exception) {
        error_log('Auth schema auto-sync failed: ' . $exception->getMessage());
        $row = latestAuthSchemaRow();
        $stored = $row ? json_decode((string) ($row->schema_json ?? ''), true) : null;

        return [
            'ok' => (bool) $row,
            'changed' => false,
            'kept_existing' => (bool) $row,
            'row' => $row,
            'schema' => is_array($stored) ? $stored : null,
            'error' => $exception->getMessage(),
        ];
    } finally {
        $syncing = false;
    }
}

$method = isset($_SERVER['REQUEST_METHOD'])
    ? strtoupper((string) $_SERVER['REQUEST_METHOD'])
    : 'GET';

ensureAuthSchemaTable();

if ($method === 'GET') {
    // Fix #8 / #9 / #10: route action-specific sub-endpoints.
    $action = isset($_GET['action']) ? trim((string) $_GET['action']) : '';

    // Fix #8: ?action=meta — lightweight metadata, no full schema.
    if ($action === 'meta') {
        // Keep meta current when sources drift.
        syncAuthSchemaFromSources(false);
        apiRespond(authSchemaMeta(), 200);
    }

    // Fix #9: ?action=manifest — full revision list + deleted entries.
    if ($action === 'manifest') {
        apiRespond(authSchemaManifest(), 200);
    }

    // Fix #10: ?action=delta — RFC-6902 patch between from_revision and latest.
    if ($action === 'delta') {
        syncAuthSchemaFromSources(false);
        $fromRevision = isset($_GET['from_revision']) ? (int) $_GET['from_revision'] : 0;
        $baseHash     = isset($_GET['base_hash'])     ? trim((string) $_GET['base_hash']) : '';
        apiRespond(authSchemaDelta($fromRevision, $baseHash), 200);
    }

    // Historical row by id — do not auto-overwrite.
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id > 0) {
        $row = latestAuthSchemaRow($id);
        if (!$row) {
            apiRespond([
                'status' => false,
                'message' => 'No auth UI schema found.',
            ], 404);
        }
        $payload = normalizeAuthSchemaResponse($row);
        apiRespond($payload, !empty($payload['status']) ? 200 : 500);
    }

    // Default: sync from builder/views when needed, then return latest.
    $sync = syncAuthSchemaFromSources(false);
    $row = $sync['row'] ?? latestAuthSchemaRow();

    if (!$row) {
        apiRespond([
            'status' => false,
            'message' => 'No auth UI schema found.',
            'sync_error' => $sync['error'] ?? null,
        ], 404);
    }

    $payload = normalizeAuthSchemaResponse($row);
    if (!empty($sync['changed'])) {
        $payload['message'] = 'Auth UI schema auto-synced from current sources.';
        $payload['auto_synced'] = true;
    }
    apiRespond($payload, !empty($payload['status']) ? 200 : 500);
}

if ($method === 'POST') {
    $body = requestBody();
    $schema = [];
    $generatorDebug = null;
    $forceRaw = $body['force'] ?? $body['force_update'] ?? ($_GET['force'] ?? false);
    $force = !($forceRaw === false || $forceRaw === 0 || $forceRaw === '0' || $forceRaw === 'false' || $forceRaw === null || $forceRaw === '');

    /*
     * Case 1: caller posted {"schema": {...}}
     */
    if (
        isset($body['schema'])
        && is_array($body['schema'])
        && !empty($body['schema'])
    ) {
        $schema = $body['schema'];
    }

    /*
     * Case 2: caller posted the schema root itself.
     */
    elseif (
        !empty($body)
        && isset($body['schemaType'])
    ) {
        $schema = $body;
    }

    /*
     * Case 3: welcome.php requests generation.
     * force=true (Generate button) always writes a new revision.
     */
    else {
        $sync = syncAuthSchemaFromSources($force);
        $generatorDebug = $sync;
        if (!empty($sync['ok']) && !empty($sync['row'])) {
            $payload = normalizeAuthSchemaResponse($sync['row']);
            $payload['message'] = !empty($sync['changed'])
                ? 'Auth UI schema generated and stored successfully.'
                : 'Auth UI schema already up to date; no new revision created.';
            $payload['auto_synced'] = true;
            $payload['changed'] = !empty($sync['changed']);
            $payload['forced'] = $force;
            apiRespond($payload, !empty($sync['changed']) ? 201 : 200);
        }

        $generated = generateSchemaLocally();
        $generatorDebug = $generated;
        if (isset($generated['schema']) && is_array($generated['schema'])) {
            $schema = $generated['schema'];
        }
    }

    if (empty($schema)) {
        apiRespond([
            'status' => false,
            'message' => 'No schema payload was posted and the local auth schema generator did not return a schema.',
            'generator_debug' => $generatorDebug,
        ], 500);
    }

    $sourceFp = authSchemaSourceFingerprint();
    if ($sourceFp !== '') {
        $schema['sourceFingerprint'] = $sourceFp;
        if (!isset($schema['meta']) || !is_array($schema['meta'])) {
            $schema['meta'] = [];
        }
        $schema['meta']['sourceFingerprint'] = $sourceFp;
    }

    // Skip duplicate revision when posted/generated content matches latest.
    $latest = latestAuthSchemaRow();
    if ($latest && !$force) {
        $stored = json_decode((string) ($latest->schema_json ?? ''), true);
        if (is_array($stored) && authSchemaStructuralHash($stored) === authSchemaStructuralHash($schema)) {
            $payload = normalizeAuthSchemaResponse($latest);
            $payload['message'] = 'Auth UI schema already up to date; no new revision created.';
            $payload['changed'] = false;
            apiRespond($payload, 200);
        }
    }

    $id = storeAuthSchema($schema);

    if ($id <= 0) {
        apiRespond([
            'status' => false,
            'message' => 'Schema insert completed but the new row ID could not be resolved.',
        ], 500);
    }

    $row = latestAuthSchemaRow($id);

    if (!$row) {
        apiRespond([
            'status' => false,
            'message' => 'Schema was stored, but the stored row could not be reloaded.',
        ], 500);
    }

    $payload = normalizeAuthSchemaResponse($row);
    $payload['message'] = 'Auth UI schema generated and stored successfully.';
    $payload['changed'] = true;
    apiRespond($payload, 201);
}

apiRespond([
    'status' => false,
    'message' => 'Only GET and POST methods are allowed.',
], 405);

