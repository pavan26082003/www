<?php
/**
 * Standalone IoT dashboard API for www/v8/iot_api_creation.php.
 *
 * Actions:
 *   GET  ?action=get_iot_devices
 *   GET  ?action=getAllNodesStatus
 *   GET  ?action=generate_iot_schema
 *   GET  ?action=get_iot_schema
 *   POST ?action=device_card_click
 *   POST ?action=device_power_toggle
 *
 * Send the product access token as:
 *   X-IOT-Access-Token: your-access-token
 *   or ?access_token=your-access-token
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
date_default_timezone_set('Asia/Kolkata');

function iot_json(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function iot_repair_access_token(string $token): string
{
    $token = trim($token);
    if ($token === '') {
        return '';
    }

    // Query strings decode '+' as space, which corrupts base64 JWT segments in URLs.
    if (strpos($token, ' ') !== false) {
        $token = str_replace(' ', '+', $token);
    }

    return $token;
}

function iot_debug_enabled(): bool
{
    if (isset($_GET['debug']) && (string) $_GET['debug'] === '1') {
        return true;
    }

    $flag = iot_config_value('IOT_DEBUG', null);
    if ($flag === null) {
        $flag = iot_config_value('DISPLAY_DEBUG', null);
    }

    return in_array(strtolower((string) $flag), array('1', 'true', 'yes'), true);
}

function iot_api_error(Throwable $e, int $status = 500): void
{
    error_log('IoT API error: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    $payload = array('success' => false, 'message' => 'IoT API error.');
    if (iot_debug_enabled()) {
        $payload['error'] = $e->getMessage();
        $payload['file'] = $e->getFile();
        $payload['line'] = $e->getLine();
    }

    iot_json($payload, $status);
}

function iot_prepare_heavy_action(): void
{
    @ini_set('memory_limit', '512M');
    @set_time_limit(120);
}

function iot_config_value(string $key, ?string $fallback = null): ?string
{
    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return $value;
    }

    $configPath = __DIR__ . '/config.php';
    if (is_file($configPath)) {
        require_once $configPath;
    }

    $constant = 'IOT_' . $key;
    if (defined($constant)) {
        return (string) constant($constant);
    }

    if (defined($key)) {
        return (string) constant($key);
    }

    return $fallback;
}

function iot_access_token(): string
{
    $headers = function_exists('getallheaders') ? getallheaders() : array();
    $token = '';
    foreach ($headers as $key => $value) {
        if (strtolower((string) $key) === 'x-iot-access-token') {
            $token = trim((string) $value);
            break;
        }
    }

    if ($token === '') {
        foreach ($headers as $key => $value) {
            if (strtolower((string) $key) === 'authorization') {
                $auth = trim((string) $value);
                // Accept "Bearer <token>" or bare token (no prefix).
                if (preg_match('/^Bearer\s+(\S+)/i', $auth, $matches)) {
                    $token = $matches[1];
                } elseif ($auth !== '') {
                    $token = $auth;
                }
                break;
            }
        }
    }

    if ($token === '') {
        $token = iot_repair_access_token((string) ($_GET['access_token'] ?? $_GET['token'] ?? ''));
    }

    $token = iot_repair_access_token($token);

    // Legacy getAllNodesStatus clients send JWT as the first header value.
    if ($token === '') {
        foreach ($headers as $key => $value) {
            $headerName = strtolower((string) $key);
            if (in_array($headerName, array('host', 'accept', 'accept-encoding', 'connection', 'content-type', 'content-length', 'user-agent'), true)) {
                continue;
            }
            $candidate = trim((string) $value);
            if ($candidate !== '' && strncmp($candidate, 'eyJ', 3) === 0) {
                $token = $candidate;
                break;
            }
        }
    }

    if ($token === '') {
        iot_json(array('success' => false, 'message' => 'X-IOT-Access-Token is required.'), 401);
    }

    return $token;
}

function iot_db(): mysqli
{
    $host = iot_config_value('DB_HOST', 'localhost');
    $user = iot_config_value('DB_USER', 'root');
    $pass = iot_config_value('DB_PASS', 'mysql');
    $name = iot_config_value('DB_NAME', 'factor_db2');
    $port = (int) iot_config_value('DB_PORT', '3306');

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = new mysqli((string) $host, (string) $user, (string) $pass, (string) $name, $port);
    $db->set_charset('utf8mb4');
    iot_ensure_schema($db);
    return $db;
}

function iot_ensure_schema(mysqli $db): void
{
    $db->query("
        CREATE TABLE IF NOT EXISTS `iot_dashboard_schemas` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` INT UNSIGNED NOT NULL,
            `access_token` VARCHAR(1024) NOT NULL DEFAULT '',
            `access_token_hash` CHAR(64) NOT NULL DEFAULT '',
            `schema_json` LONGTEXT NOT NULL,
            `schema_version` VARCHAR(32) NOT NULL DEFAULT '2.3.2',
            `created_at` DATETIME NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_user_id` (`user_id`),
            INDEX `idx_token_hash` (`access_token_hash`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $db->query("
        CREATE TABLE IF NOT EXISTS `iot_dashboard_token_schemas` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `access_token_hash` CHAR(64) NOT NULL,
            `schema_json` LONGTEXT NOT NULL,
            `schema_version` VARCHAR(32) NOT NULL DEFAULT '1.0.0',
            `created_at` DATETIME NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `access_token_hash` (`access_token_hash`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    iot_ensure_column($db, 'iot_dashboard_schemas', 'access_token', "ALTER TABLE `iot_dashboard_schemas` ADD COLUMN `access_token` VARCHAR(1024) NOT NULL DEFAULT '' AFTER `user_id`");
    iot_ensure_column($db, 'iot_dashboard_schemas', 'access_token_hash', "ALTER TABLE `iot_dashboard_schemas` ADD COLUMN `access_token_hash` CHAR(64) NOT NULL DEFAULT '' AFTER `access_token`");
    iot_ensure_column($db, 'productcats', 'img_path', "ALTER TABLE `productcats` ADD COLUMN `img_path` VARCHAR(500) NULL AFTER `product_image`");
    iot_ensure_column($db, 'brands', 'img_path', "ALTER TABLE `brands` ADD COLUMN `img_path` VARCHAR(500) NULL AFTER `brandlogo`");
    iot_ensure_column($db, 'efactorproduct', 'vendor_type', "ALTER TABLE `efactorproduct` ADD COLUMN `vendor_type` VARCHAR(255) NULL AFTER `vendor_name`");
    iot_ensure_access_token_column_width($db, 'iot_dashboard_schemas', 'access_token', 1024);
}

function iot_ensure_access_token_column_width(mysqli $db, string $table, string $column, int $minLength): void
{
    try {
        $stmt = $db->prepare("
            SELECT CHARACTER_MAXIMUM_LENGTH AS max_len
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
            LIMIT 1
        ");
        $stmt->bind_param('ss', $table, $column);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $current = (int) ($row['max_len'] ?? 0);
        if ($current > 0 && $current < $minLength) {
            $db->query("ALTER TABLE `{$table}` MODIFY COLUMN `{$column}` VARCHAR({$minLength}) NOT NULL DEFAULT ''");
        }
    } catch (Throwable $e) {
        error_log('IoT access_token column widen skipped: ' . $e->getMessage());
    }
}

function iot_ensure_column(mysqli $db, string $table, string $column, string $alterSql): void
{
    $stmt = $db->prepare("
        SELECT COLUMN_NAME
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
        LIMIT 1
    ");
    $stmt->bind_param('ss', $table, $column);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        try {
            $db->query($alterSql);
        } catch (Throwable $e) {
            error_log('IoT schema migration skipped (' . $table . '.' . $column . '): ' . $e->getMessage());
        }
    }
}

function iot_asset_url(string $path): string
{
    $path = trim($path);
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        if (preg_match('#^http://((?:www\.)?efactor\.tech)(/|$)#i', $path)) {
            return 'https://' . substr($path, 7);
        }
        return $path;
    }

    $host = $_SERVER['HTTP_HOST'] ?? '';
    $hostName = strtolower((string) preg_replace('/:\d+$/', '', $host));
    $forwarded = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
    $scheme = (preg_match('/(^|\.)efactor\.tech$/', $hostName) || $forwarded === 'https' || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'))
        ? 'https'
        : 'http';
    if (strpos($path, '/') !== false) {
        return $host ? $scheme . '://' . $host . '/' . ltrim($path, '/') : '/' . ltrim($path, '/');
    }

    return $host ? $scheme . '://' . $host . '/files/brand_logo/' . rawurlencode($path) : '/files/brand_logo/' . rawurlencode($path);
}

function iot_bool_online($value): bool
{
    return in_array(strtolower(trim((string) $value)), array('1', 'true', 'yes', 'online', 'connected'), true);
}

function iot_bool_power($value): bool
{
    return in_array(strtolower(trim((string) $value)), array('1', '01', 'true', 'yes', 'on'), true);
}

function iot_devices(mysqli $db, string $accessToken): array
{
    $accessToken = iot_normalize_access_token($accessToken);
    $userId = iot_user_id_for_token($db, $accessToken);

    $byUser = array();
    $byToken = array();

    // Token identifies an account — return all products for that user (matches IoT dashboard).
    if ($userId > 0) {
        $byUser = iot_devices_for_user($db, $userId);
    }

    // Also merge token-matched rows so a just-bound device is never missed if
    // user_id resolution and product token briefly disagree.
    $byToken = iot_devices_by_access_token($db, $accessToken);

    if ($byUser === array() && $byToken === array()) {
        return array();
    }
    if ($byUser === array()) {
        return $byToken;
    }
    if ($byToken === array()) {
        return $byUser;
    }

    $merged = array();
    $seen = array();
    foreach (array_merge($byUser, $byToken) as $device) {
        if (!is_array($device)) {
            continue;
        }
        $key = trim((string) ($device['product_id'] ?? $device['id'] ?? $device['node_id'] ?? ''));
        if ($key === '') {
            $merged[] = $device;
            continue;
        }
        if (isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;
        $merged[] = $device;
    }

    return $merged;
}

function iot_devices_with_live_status(mysqli $db, string $accessToken): array
{
    $devices = iot_devices($db, $accessToken);
    if ($devices === array()) {
        return array();
    }

    $userId = iot_user_id_for_token($db, $accessToken);
    $allNodes = iot_fetch_all_nodes($db, $accessToken);
    $devices = (new \App\Libraries\IotNodesStatusBuilder())->enrichDevicesLiveState($db, $devices, $allNodes, $userId);

    $apiBase = iot_public_api_base();
    $normalized = array();
    foreach ($devices as $device) {
        if (!is_array($device)) {
            continue;
        }
        $normalized[] = \App\Libraries\IotDeviceNormalizer::finalize($device, $apiBase);
    }

    return iot_schema_builder()->sortDevicesOnlineFirst($normalized);
}

function iot_normalize_node_id(string $raw): string
{
    $value = trim($raw);
    if ($value === '') {
        return '';
    }

    if ($value[0] === '{') {
        $parsed = json_decode($value, true);
        if (is_array($parsed)) {
            foreach (array('node_id', 'nodeId', 'id') as $key) {
                if (!empty($parsed[$key])) {
                    $value = trim((string) $parsed[$key]);
                    break;
                }
            }
        }
    }

    if (preg_match('/^https?:\/\//i', $value)) {
        $parts = parse_url($value);
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $query);
            if (!empty($query['node_id'])) {
                $value = trim((string) $query['node_id']);
            }
        }
    }

    if (stripos($value, 'socket_') === 0) {
        $value = substr($value, 7);
    }

    return trim($value);
}

function iot_resolve_product_node_id(mysqli $db, string $nodeId, int $userId, string $accessToken): string
{
    $nodeId = iot_normalize_node_id($nodeId);
    if ($nodeId === '') {
        return '';
    }

    $candidates = array($nodeId, 'socket_' . $nodeId);
    foreach ($candidates as $candidate) {
        if ($candidate === '') {
            continue;
        }
        try {
            if ($userId > 0) {
                $stmt = $db->prepare('SELECT node_id FROM efactorproduct WHERE node_id = ? AND user_id = ? LIMIT 1');
                $stmt->bind_param('si', $candidate, $userId);
            } else {
                $stmt = $db->prepare('SELECT node_id FROM efactorproduct WHERE node_id = ? AND access_token = ? LIMIT 1');
                $stmt->bind_param('ss', $candidate, $accessToken);
            }
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            if (is_array($row) && !empty($row['node_id'])) {
                return trim((string) $row['node_id']);
            }
        } catch (Throwable $e) {
            continue;
        }
    }

    return $nodeId;
}

function iot_find_device_power(mysqli $db, string $accessToken, string $nodeId): ?bool
{
    iot_require_nodes_status_builder();

    $nodeId = trim($nodeId);
    if ($nodeId === '') {
        return null;
    }

    $userId = iot_user_id_for_token($db, $accessToken);
    $nodeId = iot_resolve_product_node_id($db, $nodeId, $userId, $accessToken);
    $builder = iot_nodes_status_builder();

    try {
        if ($userId > 0) {
            $stmt = $db->prepare(
                'SELECT id, node_id, name, device_type, model_name, model_number, internet, power, add_gateway, is_added, module_type, access_token
                 FROM efactorproduct WHERE node_id = ? AND user_id = ? ORDER BY id DESC LIMIT 1'
            );
            $stmt->bind_param('si', $nodeId, $userId);
        } else {
            $stmt = $db->prepare(
                'SELECT id, node_id, name, device_type, model_name, model_number, internet, power, add_gateway, is_added, module_type, access_token
                 FROM efactorproduct WHERE node_id = ? AND access_token = ? ORDER BY id DESC LIMIT 1'
            );
            $stmt->bind_param('ss', $nodeId, $accessToken);
        }
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) {
            return null;
        }

        $online = \App\Libraries\IotDeviceNormalizer::isOnline($row['internet'] ?? null);

        return $builder->resolveProductPower($db, $row, $nodeId, $online);
    } catch (Throwable $e) {
        return null;
    }
}

function iot_resolve_power_command(array $body, ?bool $currentPower): array
{
    if (array_key_exists('power', $body)) {
        $raw = $body['power'];
        if (is_bool($raw)) {
            return array(
                'command' => $raw ? 'on' : 'off',
                'power' => $raw,
            );
        }
        $normalized = strtolower(trim((string) $raw));
        if (in_array($normalized, array('1', '01', 'true', 'yes', 'on'), true)) {
            return array('command' => 'on', 'power' => true);
        }
        if (in_array($normalized, array('0', '00', 'false', 'no', 'off'), true)) {
            return array('command' => 'off', 'power' => false);
        }
    }

    $command = strtolower(trim((string) ($body['command'] ?? 'toggle_power')));
    if (in_array($command, array('on', 'power_on', '1'), true)) {
        return array('command' => 'on', 'power' => true);
    }
    if (in_array($command, array('off', 'power_off', '0'), true)) {
        return array('command' => 'off', 'power' => false);
    }

    $next = !($currentPower ?? false);

    return array(
        'command' => $next ? 'on' : 'off',
        'power' => $next,
    );
}

function iot_set_device_power(mysqli $db, string $accessToken, string $nodeId, bool $powerOn): void
{
    iot_require_nodes_status_builder();

    $nodeId = trim($nodeId);
    if ($nodeId === '') {
        return;
    }

    // Power on/off must NEVER flip online/offline (`internet`). Those are separate.
    $powerValue = $powerOn ? 'true' : 'false';
    $userId = iot_user_id_for_token($db, $accessToken);
    $nodeId = iot_resolve_product_node_id($db, $nodeId, $userId, $accessToken);

    $productRow = null;
    try {
        $selectSql = $userId > 0
            ? 'SELECT model_number, module_type, add_gateway, is_added, internet FROM efactorproduct WHERE node_id = ? AND user_id = ? ORDER BY id DESC LIMIT 1'
            : 'SELECT model_number, module_type, add_gateway, is_added, internet FROM efactorproduct WHERE node_id = ? AND access_token = ? ORDER BY id DESC LIMIT 1';
        $stmt = $db->prepare($selectSql);
        if ($userId > 0) {
            $stmt->bind_param('si', $nodeId, $userId);
        } else {
            $stmt->bind_param('ss', $nodeId, $accessToken);
        }
        $stmt->execute();
        $productRow = $stmt->get_result()->fetch_assoc();
    } catch (Throwable $e) {
        $productRow = null;
    }

    try {
        if ($userId > 0) {
            $stmt = $db->prepare('UPDATE efactorproduct SET power = ? WHERE node_id = ? AND user_id = ?');
            $stmt->bind_param('ssi', $powerValue, $nodeId, $userId);
        } else {
            $stmt = $db->prepare('UPDATE efactorproduct SET power = ? WHERE node_id = ? AND access_token = ?');
            $stmt->bind_param('sss', $powerValue, $nodeId, $accessToken);
        }
        $stmt->execute();
    } catch (Throwable $e) {
        error_log('IoT power update failed (efactorproduct): ' . $e->getMessage());
    }

    try {
        $modelNumber = trim((string) ($productRow['model_number'] ?? ''));
        if ($modelNumber === '' || strcasecmp($modelNumber, 'EFDEFLT V1.0') === 0) {
            return;
        }
        if (!preg_match('/^[A-Za-z0-9_\-\. ]+$/', $modelNumber)) {
            return;
        }

        $table = str_replace('`', '``', $modelNumber);
        $updateSql = 'UPDATE `' . $table . '` SET power = ? WHERE node_id = ? LIMIT 1';
        $modelStmt = $db->prepare($updateSql);
        $modelStmt->bind_param('ss', $powerValue, $nodeId);
        $modelStmt->execute();
    } catch (Throwable $e) {
        error_log('IoT power update skipped (model table): ' . $e->getMessage());
    }
}

function iot_normalize_access_token(string $token): string
{
    $token = iot_repair_access_token($token);
    if ($token === '') {
        return '';
    }

    // Repair common copy/paste issue: JWT payload only (no header.signature).
    if (strpos($token, '.') === false && strncmp($token, 'eyJ', 3) === 0) {
        return $token;
    }

    return $token;
}

function iot_user_id_for_token(mysqli $db, string $accessToken): int
{
    $accessToken = iot_normalize_access_token($accessToken);
    if ($accessToken === '') {
        return 0;
    }

    // Exact product token — owner of devices using this token (profile default).
    $userId = iot_query_user_id(
        $db,
        'SELECT user_id FROM efactorproduct WHERE access_token = ? AND user_id IS NOT NULL GROUP BY user_id ORDER BY COUNT(*) DESC LIMIT 1',
        's',
        array($accessToken)
    );
    if ($userId > 0) {
        return $userId;
    }

    $userId = iot_query_user_id(
        $db,
        'SELECT id AS user_id FROM users WHERE access_token = ? LIMIT 1',
        's',
        array($accessToken)
    );
    if ($userId > 0) {
        return $userId;
    }

    $hash = hash('sha256', $accessToken);
    $userId = iot_query_user_id(
        $db,
        'SELECT user_id FROM iot_dashboard_schemas WHERE access_token = ? OR access_token_hash = ? ORDER BY updated_at DESC LIMIT 1',
        'ss',
        array($accessToken, $hash)
    );
    if ($userId > 0) {
        return $userId;
    }

    $payloadPart = iot_jwt_payload_part($accessToken);
    if ($payloadPart !== '') {
        $like = '%' . $payloadPart . '%';
        $userId = iot_query_user_id(
            $db,
            'SELECT user_id FROM efactorproduct WHERE access_token LIKE ? AND user_id IS NOT NULL GROUP BY user_id ORDER BY COUNT(*) DESC LIMIT 1',
            's',
            array($like)
        );
        if ($userId > 0) {
            return $userId;
        }
    }

    return iot_user_id_from_jwt_claims($accessToken);
}

function iot_jwt_payload_part(string $token): string
{
    $token = iot_repair_access_token(trim($token));
    if ($token === '') {
        return '';
    }

    if (strpos($token, '.') !== false) {
        $parts = explode('.', $token);

        return trim((string) ($parts[1] ?? ''));
    }

    if (strncmp($token, 'eyJ', 3) === 0) {
        return $token;
    }

    return '';
}

function iot_user_id_from_jwt_claims(string $token): int
{
    $part = iot_jwt_payload_part($token);
    if ($part === '') {
        return 0;
    }

    $padded = strtr($part, '-_', '+/');
    $mod = strlen($padded) % 4;
    if ($mod > 0) {
        $padded .= str_repeat('=', 4 - $mod);
    }

    $payload = base64_decode($padded, true);
    if ($payload === false) {
        return 0;
    }

    $data = json_decode($payload, true);
    if (!is_array($data)) {
        return 0;
    }

    foreach (array('custom:user_id', 'user_id', 'customUserId') as $key) {
        if (!empty($data[$key])) {
            return (int) $data[$key];
        }
    }

    return 0;
}

function iot_query_user_id(mysqli $db, string $sql, string $types, array $params): int
{
    try {
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            return 0;
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        return (int) ($row['user_id'] ?? 0);
    } catch (Throwable $e) {
        return 0;
    }
}

function iot_devices_by_access_token(mysqli $db, string $accessToken): array
{
    iot_require_device_normalizer();
    $accessToken = iot_normalize_access_token($accessToken);
    if ($accessToken === '') {
        return array();
    }

    $sql = iot_devices_select_sql('p.access_token = ?');
    $devices = iot_fetch_normalized_devices($db, $sql, 's', array($accessToken));
    if ($devices !== array()) {
        return $devices;
    }

    if (strpos($accessToken, '.') === false) {
        $like = '%' . $accessToken . '%';
        return iot_fetch_normalized_devices($db, iot_devices_select_sql('p.access_token LIKE ?'), 's', array($like));
    }

    return array();
}

function iot_devices_select_sql(string $whereClause): string
{
    return "
        SELECT
            p.id, p.user_id, p.node_id, p.device_type, p.vendor_type, p.vendor_name, p.name,
            p.internet, p.power, p.model_name, p.model_number, p.module_type,
            p.add_gateway, p.is_added, p.access_token,
            p.prod_category, p.category_id, p.vendor_id, p.brand_logo AS device_brand_logo,
            (
                SELECT pc.name
                FROM productcats pc
                WHERE pc.user_id = p.user_id
                  AND (CAST(pc.id AS CHAR) = CAST(p.category_id AS CHAR) OR LOWER(TRIM(pc.name)) = LOWER(TRIM(p.name)) OR LOWER(TRIM(pc.name)) = LOWER(TRIM(p.device_type)) OR LOWER(TRIM(pc.name)) = LOWER(TRIM(p.prod_category)))
                ORDER BY CASE WHEN CAST(pc.id AS CHAR) = CAST(p.category_id AS CHAR) THEN 1 WHEN LOWER(TRIM(pc.name)) = LOWER(TRIM(p.name)) THEN 2 WHEN LOWER(TRIM(pc.name)) = LOWER(TRIM(p.device_type)) THEN 3 ELSE 4 END, pc.id DESC
                LIMIT 1
            ) AS product_category_name,
            COALESCE(
                (
                    SELECT CASE
                        WHEN TRIM(vp.model_logo) LIKE 'http%' OR TRIM(vp.model_logo) LIKE '/%' OR TRIM(vp.model_logo) LIKE 'files/%'
                            THEN NULLIF(TRIM(vp.model_logo), '')
                        ELSE CONCAT('files/model_logo/', TRIM(vp.model_logo))
                    END
                    FROM vendorproducts vp
                    WHERE NULLIF(TRIM(vp.model_logo), '') IS NOT NULL
                      AND (
                            LOWER(TRIM(vp.modelname)) = LOWER(TRIM(p.model_name))
                         OR LOWER(TRIM(vp.modelno)) = LOWER(TRIM(p.model_number))
                         OR LOWER(TRIM(vp.modelname)) = LOWER(TRIM(p.model_number))
                         OR LOWER(TRIM(vp.modelno)) = LOWER(TRIM(p.model_name))
                         OR LOWER(TRIM(vp.description)) = LOWER(TRIM(p.name))
                         OR LOWER(TRIM(vp.description)) = LOWER(TRIM(p.device_type))
                         OR LOWER(TRIM(vp.modelname)) = LOWER(TRIM(p.name))
                         OR LOWER(TRIM(vp.modelname)) = LOWER(TRIM(p.device_type))
                      )
                    ORDER BY
                        CASE
                            WHEN LOWER(TRIM(vp.modelname)) = LOWER(TRIM(p.model_name)) THEN 1
                            WHEN LOWER(TRIM(vp.modelno)) = LOWER(TRIM(p.model_number)) THEN 2
                            WHEN LOWER(TRIM(vp.description)) = LOWER(TRIM(p.name)) THEN 3
                            WHEN LOWER(TRIM(vp.description)) = LOWER(TRIM(p.device_type)) THEN 4
                            ELSE 5
                        END,
                        CASE WHEN CAST(vp.user_id AS CHAR) = CAST(p.user_id AS CHAR) THEN 0 ELSE 1 END,
                        vp.id DESC
                    LIMIT 1
                ),
                (
                    SELECT COALESCE(NULLIF(pc.img_path, ''), NULLIF(pc.product_image, ''))
                    FROM productcats pc
                    WHERE pc.user_id = p.user_id
                      AND (CAST(pc.id AS CHAR) = CAST(p.category_id AS CHAR) OR LOWER(TRIM(pc.name)) = LOWER(TRIM(p.name)) OR LOWER(TRIM(pc.name)) = LOWER(TRIM(p.device_type)) OR LOWER(TRIM(pc.name)) = LOWER(TRIM(p.prod_category)))
                    ORDER BY CASE WHEN CAST(pc.id AS CHAR) = CAST(p.category_id AS CHAR) THEN 1 WHEN LOWER(TRIM(pc.name)) = LOWER(TRIM(p.name)) THEN 2 WHEN LOWER(TRIM(pc.name)) = LOWER(TRIM(p.device_type)) THEN 3 ELSE 4 END, pc.id DESC
                    LIMIT 1
                )
            ) AS product_image,
            (
                SELECT b.name
                FROM brands b
                WHERE b.user_id = p.user_id
                  AND (CAST(b.id AS CHAR) = CAST(p.vendor_id AS CHAR) OR CAST(b.id AS CHAR) = CAST(p.vendor_type AS CHAR) OR CAST(b.id AS CHAR) = CAST(p.vendor_name AS CHAR) OR LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_name)) OR LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_type)))
                ORDER BY CASE WHEN CAST(b.id AS CHAR) = CAST(p.vendor_id AS CHAR) THEN 1 WHEN LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_name)) THEN 2 WHEN CAST(b.id AS CHAR) = CAST(p.vendor_type AS CHAR) THEN 3 ELSE 4 END, b.id DESC
                LIMIT 1
            ) AS brand_name,
            COALESCE(NULLIF(p.brand_logo, ''), (
                SELECT COALESCE(NULLIF(b.img_path, ''), NULLIF(b.brandlogo, ''))
                FROM brands b
                WHERE b.user_id = p.user_id
                  AND (CAST(b.id AS CHAR) = CAST(p.vendor_id AS CHAR) OR CAST(b.id AS CHAR) = CAST(p.vendor_type AS CHAR) OR CAST(b.id AS CHAR) = CAST(p.vendor_name AS CHAR) OR LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_name)) OR LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_type)))
                ORDER BY CASE WHEN CAST(b.id AS CHAR) = CAST(p.vendor_id AS CHAR) THEN 1 WHEN LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_name)) THEN 2 WHEN CAST(b.id AS CHAR) = CAST(p.vendor_type AS CHAR) THEN 3 ELSE 4 END, b.id DESC
                LIMIT 1
            ), (
                SELECT COALESCE(NULLIF(b.img_path, ''), NULLIF(b.brandlogo, ''))
                FROM brands b
                WHERE COALESCE(NULLIF(b.img_path, ''), NULLIF(b.brandlogo, '')) IS NOT NULL
                  AND (LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_name)) OR LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_type)) OR CAST(b.id AS CHAR) = CAST(p.vendor_id AS CHAR))
                ORDER BY CASE WHEN LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_name)) THEN 1 WHEN CAST(b.id AS CHAR) = CAST(p.vendor_id AS CHAR) THEN 2 ELSE 3 END, b.id DESC
                LIMIT 1
            )) AS brand_logo
        FROM efactorproduct p
        WHERE {$whereClause}
        ORDER BY p.id DESC
    ";
}

function iot_fetch_normalized_devices(mysqli $db, string $sql, string $types, array $params): array
{
    try {
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            return array();
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $rows = array();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        if ($rows === array()) {
            return array();
        }

        $apiBase = iot_public_api_base();
        $devices = array();
        foreach (\App\Libraries\IotDeviceNormalizer::uniqueRows($rows) as $row) {
            $devices[] = \App\Libraries\IotDeviceNormalizer::fromRow($row, $apiBase);
        }

        return iot_schema_builder()->sortDevicesOnlineFirst($devices);
    } catch (Throwable $e) {
        error_log('IoT devices query failed: ' . $e->getMessage());
        return array();
    }
}

function iot_device_stats(array $devices): array
{
    return iot_schema_builder()->countDeviceStats($devices);
}

function iot_devices_for_user(mysqli $db, int $userId): array
{
    iot_require_device_normalizer();

    return iot_fetch_normalized_devices($db, iot_devices_select_sql('p.user_id = ?'), 'i', array($userId));
}

function iot_require_device_normalizer(): void
{
    if (class_exists(\App\Libraries\IotDeviceNormalizer::class)) {
        return;
    }

    foreach (iot_schema_builder_paths() as $path) {
        $normalizerPath = str_replace('IotSduiSchemaBuilder.php', 'IotDeviceNormalizer.php', $path);
        if (is_file($normalizerPath)) {
            require_once $normalizerPath;
            break;
        }
    }
}

function iot_schema_builder_paths(): array
{
    return array(
        __DIR__ . '/../../Libraries/IotSduiSchemaBuilder.php',
        __DIR__ . '/../app/Libraries/IotSduiSchemaBuilder.php',
        dirname(__DIR__, 2) . '/Libraries/IotSduiSchemaBuilder.php',
        __DIR__ . '/../../app/Libraries/IotSduiSchemaBuilder.php',
    );
}

function iot_schema_builder(): \App\Libraries\IotSduiSchemaBuilder
{
    static $builder = null;
    if ($builder instanceof \App\Libraries\IotSduiSchemaBuilder) {
        return $builder;
    }

    foreach (iot_schema_builder_paths() as $path) {
        if (is_file($path)) {
            require_once $path;
            break;
        }
    }

    if (!class_exists(\App\Libraries\IotSduiSchemaBuilder::class)) {
        throw new RuntimeException('IotSduiSchemaBuilder library not found on server.');
    }

    $builder = new \App\Libraries\IotSduiSchemaBuilder();
    return $builder;
}

function iot_require_nodes_status_builder(): void
{
    // Power/online helpers depend on IotDeviceNormalizer — load it first to avoid fatals in getAllNodesStatus.
    iot_require_device_normalizer();

    if (class_exists(\App\Libraries\IotNodesStatusBuilder::class)) {
        return;
    }

    foreach (iot_schema_builder_paths() as $path) {
        $nodesPath = str_replace('IotSduiSchemaBuilder.php', 'IotNodesStatusBuilder.php', $path);
        if (is_file($nodesPath)) {
            require_once $nodesPath;
            break;
        }
    }
}

function iot_nodes_status_builder(): \App\Libraries\IotNodesStatusBuilder
{
    static $builder = null;
    if ($builder instanceof \App\Libraries\IotNodesStatusBuilder) {
        return $builder;
    }

    iot_require_nodes_status_builder();
    if (!class_exists(\App\Libraries\IotNodesStatusBuilder::class)) {
        throw new RuntimeException('IotNodesStatusBuilder library not found on server.');
    }

    $builder = new \App\Libraries\IotNodesStatusBuilder();
    return $builder;
}

function iot_all_nodes_status(mysqli $db, string $accessToken): array
{
    try {
        $accessToken = iot_normalize_access_token($accessToken);
        $userId = iot_user_id_for_token($db, $accessToken);
        $payload = iot_nodes_status_builder()->build($db, $accessToken, $userId);
        $payload['success'] = true;
        // Help Flutter match scanned QR ids (plain) vs DB socket_ ids.
        $payload['node_ids'] = array();
        foreach ($payload['all_nodes'] ?? array() as $node) {
            if (!is_array($node)) {
                continue;
            }
            $nid = trim((string) ($node['node_id'] ?? ''));
            if ($nid === '') {
                continue;
            }
            $payload['node_ids'][] = $nid;
            $plain = preg_replace('/^socket_/i', '', $nid);
            if ($plain !== '' && strcasecmp($plain, $nid) !== 0) {
                $payload['node_ids'][] = $plain;
            }
        }
        $payload['node_ids'] = array_values(array_unique($payload['node_ids']));

        return $payload;
    } catch (Throwable $e) {
        error_log('IoT getAllNodesStatus failed: ' . $e->getMessage());

        return array(
            'success'   => true,
            'url'       => 'action=getAllNodesStatus',
            'token'     => $accessToken,
            'all_nodes' => array(),
            'node_ids'  => array(),
            'total'     => 0,
            'online'    => 0,
            'offline'   => 0,
            'message'   => 'Nodes status temporarily unavailable.',
            'degraded'  => true,
        );
    }
}

function iot_public_api_base(): string
{
    return iot_schema_builder()->detectPublicApiBaseUrl();
}

function iot_build_schema(mysqli $db, string $accessToken, array $devices): array
{
    $userId = iot_user_id_for_token($db, $accessToken);
    return iot_schema_builder()->build(
        $devices,
        $userId,
        $accessToken,
        iot_public_api_base()
    );
}

function iot_save_schema(mysqli $db, string $accessToken, array $schema): void
{
    try {
        $hash = hash('sha256', $accessToken);
        $json = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new RuntimeException('Schema JSON encode failed.');
        }

        $version = (string) ($schema['schemaVersion'] ?? '2.3.2');
        $now = date('Y-m-d H:i:s');
        $userId = iot_user_id_for_token($db, $accessToken);

        $stmt = $db->prepare("
            INSERT INTO iot_dashboard_token_schemas (access_token_hash, schema_json, schema_version, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE schema_json = VALUES(schema_json), schema_version = VALUES(schema_version), updated_at = VALUES(updated_at)
        ");
        $stmt->bind_param('sssss', $hash, $json, $version, $now, $now);
        $stmt->execute();

        if ($userId > 0) {
            $stmt = $db->prepare("
                INSERT INTO iot_dashboard_schemas
                    (user_id, access_token, access_token_hash, schema_json, schema_version, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    access_token = VALUES(access_token),
                    access_token_hash = VALUES(access_token_hash),
                    schema_json = VALUES(schema_json),
                    schema_version = VALUES(schema_version),
                    updated_at = VALUES(updated_at)
            ");
            $stmt->bind_param('issssss', $userId, $accessToken, $hash, $json, $version, $now, $now);
            $stmt->execute();
        }
    } catch (Throwable $e) {
        error_log('IoT schema save failed: ' . $e->getMessage());
    }
}

function iot_fetch_all_nodes(mysqli $db, string $accessToken): array
{
    try {
        $nodesPayload = iot_all_nodes_status($db, $accessToken);

        return is_array($nodesPayload['all_nodes'] ?? null) ? $nodesPayload['all_nodes'] : array();
    } catch (Throwable $e) {
        error_log('IoT nodes status skipped: ' . $e->getMessage());

        return array();
    }
}

function iot_load_saved_schema(mysqli $db, string $accessToken): ?array
{
    $hash = hash('sha256', $accessToken);
    $stmt = $db->prepare('SELECT schema_json, schema_version, updated_at FROM iot_dashboard_token_schemas WHERE access_token_hash = ? LIMIT 1');
    $stmt->bind_param('s', $hash);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    // Fallback: schema may exist only by user_id (created on web dashboard before token mirror).
    if (!$row) {
        $userId = iot_user_id_for_token($db, $accessToken);
        if ($userId > 0) {
            $stmt = $db->prepare(
                'SELECT schema_json, schema_version, updated_at FROM iot_dashboard_schemas WHERE user_id = ? ORDER BY updated_at DESC LIMIT 1'
            );
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            // Mirror into token table so subsequent get_iot_schema hits are fast and app-token keyed.
            if ($row && !empty($row['schema_json'])) {
                $schemaForMirror = json_decode((string) $row['schema_json'], true);
                if (is_array($schemaForMirror)) {
                    iot_save_schema($db, $accessToken, $schemaForMirror);
                }
            }
        }
    }

    // UI schema is shared. A profile that never opened the web studio still gets the
    // latest complete shell; live devices for this token are applied by the caller.
    if (!$row || empty($row['schema_json'])) {
        $result = $db->query(
            "SELECT schema_json, schema_version, updated_at
             FROM iot_dashboard_schemas
             WHERE schema_json IS NOT NULL AND schema_json != ''
             ORDER BY updated_at DESC
             LIMIT 1"
        );
        $canonical = $result ? $result->fetch_assoc() : null;
        if (is_array($canonical) && !empty($canonical['schema_json'])) {
            $row = $canonical;
        }
    }

    if (!$row) {
        return null;
    }

    $schema = json_decode((string) $row['schema_json'], true);
    if (!is_array($schema)) {
        return null;
    }

    unset($schema['all_nodes']);

    return array(
        'schema' => $schema,
        'schema_version' => (string) ($row['schema_version'] ?: '1.0.0'),
        'updated_at' => (string) ($row['updated_at'] ?? ''),
    );
}

/**
 * Fingerprint of the device roster (+ builder file) that drives IoT SDUI structure.
 */
function iot_devices_source_fingerprint(array $devices, string $accessToken = ''): string
{
    unset($accessToken);
    try {
        return iot_schema_builder()->sourceFingerprint($devices);
    } catch (Throwable $e) {
        error_log('IoT source fingerprint failed: ' . $e->getMessage());

        return '';
    }
}

function iot_schema_read_source_fingerprint(array $schema): string
{
    if (!empty($schema['sourceFingerprint']) && is_string($schema['sourceFingerprint'])) {
        return trim($schema['sourceFingerprint']);
    }
    if (!empty($schema['meta']['sourceFingerprint']) && is_string($schema['meta']['sourceFingerprint'])) {
        return trim((string) $schema['meta']['sourceFingerprint']);
    }

    return '';
}

function iot_schema_stamp_source_fingerprint(array $schema, string $fingerprint): array
{
    if ($fingerprint === '') {
        return $schema;
    }
    $schema['sourceFingerprint'] = $fingerprint;
    if (!isset($schema['meta']) || !is_array($schema['meta'])) {
        $schema['meta'] = array();
    }
    $schema['meta']['sourceFingerprint'] = $fingerprint;
    $schema['meta']['mappingSyncedAt'] = date('c');

    return $schema;
}

function iot_resolve_schema(mysqli $db, string $accessToken, bool $forceRegenerate = false, bool $forApiResponse = false): array
{
    $userId = iot_user_id_for_token($db, $accessToken);
    $devices = iot_devices_with_live_status($db, $accessToken);
    $includeLiveNodes = $forApiResponse ? (($_GET['live'] ?? '1') !== '0') : true;
    $allNodes = ($includeLiveNodes && $devices !== array()) ? iot_fetch_all_nodes($db, $accessToken) : array();
    $saved = iot_load_saved_schema($db, $accessToken);
    $schema = is_array($saved) ? ($saved['schema'] ?? null) : null;
    $builder = iot_schema_builder();
    $apiBase = iot_public_api_base();
    $sourceFp = iot_devices_source_fingerprint($devices, $accessToken);
    $storedFp = is_array($schema) ? iot_schema_read_source_fingerprint($schema) : '';
    $fingerprintStale = ($sourceFp !== '' && ($storedFp === '' || $storedFp !== $sourceFp));
    $previousSchema = is_array($schema) ? $schema : null;

    // First app login for a new user: no saved schema yet → always build + persist a default
    // IoT SDUI shell (works with 0 devices). Users must not need to visit the web dashboard.
    if (!$forceRegenerate && !$fingerprintStale && \App\Libraries\IotSduiSchemaBuilder::isSduiCapable($schema)) {
        $needsTemplate = version_compare((string) ($schema['schemaVersion'] ?? '0'), '2.4.3', '<')
            || empty($schema['dataSources']['powerToggle']['nativeApi']['body']['node_id'])
            || in_array((string) ($schema['dataSources']['powerToggle']['enabledWhen'] ?? ''), array('', '{{device.online}}', 'device.online'), true)
            || empty($schema['nodeFields'])
            || empty($schema['dataSources']['getAllNodesStatus'])
            || empty($schema['schemaContract']['interactions']['powerToggle'])
            || empty($schema['ui']['headerActions'])
            || empty($schema['overlays']['qrScannerModal'])
            || (($schema['overlays']['qrScannerModal']['presentation'] ?? '') !== 'appScannerSheet')
            || empty($schema['overlays']['qrScannerModal']['header']['rightAction'])
            || (($schema['overlays']['qrScannerModal']['header']['rightAction']['label'] ?? '') !== "I don't have QR")
            || empty($schema['overlays']['qrScannerModal']['header']['rightAction']['style'])
            || empty($schema['overlays']['qrScannerModal']['manualEntry'])
            || empty($schema['overlays']['qrScannerModal']['manualEntry']['input']['uppercase'])
            || empty($schema['overlays']['qrScannerModal']['manualEntry']['input']['errorMessage'])
            || empty($schema['dataSources']['checkDevice']['query']['action'])
            || (($schema['dataSources']['checkDevice']['query']['action'] ?? '') !== 'check_device')
            || empty($schema['dataSources']['refreshDevices'])
            || empty($schema['dataSources']['powerToggle']['webProxy']);
        if ($needsTemplate) {
            $template = iot_build_schema($db, $accessToken, $devices);
            $schema = $builder->ensureCompleteSchema($schema, $template);
            // Persist upgraded template so next app open does not re-upgrade every time.
            $schema = iot_schema_stamp_source_fingerprint($schema, $sourceFp);
            iot_save_schema($db, $accessToken, iot_finalize_schema_output($builder, $schema, false));
        }
        $schema = $builder->applyLiveMetrics($schema, $devices, $apiBase, $allNodes, false);
        $schema['userId'] = $userId > 0 ? $userId : ($schema['userId'] ?? null);
        $schema = iot_finalize_schema_output($builder, $schema, $forApiResponse);

        return array(
            'schema' => $schema,
            'schema_version' => (string) ($schema['schemaVersion'] ?? '2.3.2'),
            'updated_at' => (string) ($saved['updated_at'] ?? ''),
            'regenerated' => false,
            'complete' => true,
            'user_id' => $userId,
            'device_count' => count($devices),
        );
    }

    try {
        $schema = iot_build_schema($db, $accessToken, $devices);
        $schema = $builder->applyLiveMetrics($schema, $devices, $apiBase, $allNodes, false);
        $schema['savedAt'] = date('c');
        $schema['savedDate'] = date('Y-m-d');
        $schema['savedTime'] = date('H:i:s');
        $schema['savedTimezone'] = date_default_timezone_get();
        $schema['userId'] = $userId > 0 ? $userId : ($schema['userId'] ?? null);
        $schema = iot_schema_stamp_source_fingerprint($schema, $sourceFp);

        $savedSchema = iot_finalize_schema_output($builder, $schema, false);
        iot_save_schema($db, $accessToken, $savedSchema);
        $responseSchema = $forApiResponse ? iot_finalize_schema_output($builder, $schema, true) : $savedSchema;

        return array(
            'schema' => $responseSchema,
            'schema_version' => (string) ($schema['schemaVersion'] ?? '2.3.2'),
            'updated_at' => date('Y-m-d H:i:s'),
            'regenerated' => true,
            'complete' => true,
            'user_id' => $userId,
            'device_count' => count($devices),
        );
    } catch (Throwable $rebuildError) {
        error_log('IoT schema auto-sync rebuild failed: ' . $rebuildError->getMessage());
        if (!is_array($previousSchema) || !\App\Libraries\IotSduiSchemaBuilder::isSduiCapable($previousSchema)) {
            $fallback = iot_load_saved_schema($db, $accessToken);
            if (is_array($fallback) && is_array($fallback['schema'] ?? null)) {
                $previousSchema = $fallback['schema'];
                $saved = is_array($saved) ? $saved : $fallback;
            }
        }
        if (is_array($previousSchema) && \App\Libraries\IotSduiSchemaBuilder::isSduiCapable($previousSchema)) {
            $schema = $builder->applyLiveMetrics($previousSchema, $devices, $apiBase, $allNodes, false);
            $schema['userId'] = $userId > 0 ? $userId : ($schema['userId'] ?? null);
            $schema = iot_finalize_schema_output($builder, $schema, $forApiResponse);

            return array(
                'schema' => $schema,
                'schema_version' => (string) ($schema['schemaVersion'] ?? '2.3.2'),
                'updated_at' => (string) ($saved['updated_at'] ?? ''),
                'regenerated' => false,
                'complete' => true,
                'user_id' => $userId,
                'device_count' => count($devices),
                'sync_error' => $rebuildError->getMessage(),
            );
        }
        throw $rebuildError;
    }
}

function iot_finalize_schema_output(\App\Libraries\IotSduiSchemaBuilder $builder, array $schema, bool $forApiResponse): array
{
    if ($forApiResponse) {
        // Keep lightweight device power/online snapshot in API schema for mobile toggles.
        return $builder->stripHeavyPayload($schema, true, false);
    }

    return $builder->stripHeavyPayload($schema, true, true);
}

try {
    if (defined('IOT_API_LIBRARY_ONLY')) {
        return;
    }

    $action = trim((string) ($_GET['action'] ?? 'get_iot_devices'));
    $token = iot_access_token();
    $db = iot_db();

    if ($action === 'getAllNodesStatus') {
        try {
            iot_json(iot_all_nodes_status($db, $token));
        } catch (Throwable $nodesError) {
            iot_api_error($nodesError);
        }
    }

    if ($action === 'get_iot_devices') {
        try {
            $devices = iot_devices_with_live_status($db, $token);
            $stats = iot_device_stats($devices);
            $resolvedUserId = iot_user_id_for_token($db, $token);
            $nodeIds = array();
            foreach ($devices as $device) {
                if (!is_array($device)) {
                    continue;
                }
                $nid = trim((string) ($device['node_id'] ?? ''));
                if ($nid === '') {
                    continue;
                }
                $nodeIds[] = $nid;
                $plain = preg_replace('/^socket_/i', '', $nid);
                if ($plain !== '' && strcasecmp($plain, $nid) !== 0) {
                    $nodeIds[] = $plain;
                }
                if (!empty($device['node_id_aliases']) && is_array($device['node_id_aliases'])) {
                    foreach ($device['node_id_aliases'] as $alias) {
                        $alias = trim((string) $alias);
                        if ($alias !== '') {
                            $nodeIds[] = $alias;
                        }
                    }
                }
            }
            iot_json(array(
                'success' => true,
                'user_id' => $resolvedUserId,
                'total'   => $stats['total'],
                'online'  => $stats['online'],
                'offline' => $stats['offline'],
                'node_ids'=> array_values(array_unique($nodeIds)),
                'devices' => $devices,
            ));
        } catch (Throwable $devicesError) {
            iot_api_error($devicesError);
        }
    }

    if ($action === 'generate_iot_schema') {
        iot_prepare_heavy_action();
        $resolved = iot_resolve_schema($db, $token, true, true);
        $schema = $resolved['schema'];
        iot_json(array(
            'success' => true,
            'message' => 'IoT SDUI schema generated and saved to DB.',
            'savedAt' => $schema['savedAt'] ?? date('c'),
            'savedTime' => $schema['savedTime'] ?? date('H:i:s'),
            'schema_version' => $resolved['schema_version'],
            'user_id' => $resolved['user_id'] ?? 0,
            'device_count' => $resolved['device_count'] ?? 0,
            'schema' => $schema,
        ));
    }
    if ($action === 'get_iot_schema') {
        iot_prepare_heavy_action();
        try {
            iot_schema_builder();
            $resolved = iot_resolve_schema($db, $token, false, true);
            iot_json(array(
                'success' => true,
                'schema_version' => $resolved['schema_version'],
                'updated_at' => $resolved['updated_at'],
                'regenerated' => !empty($resolved['regenerated']),
                'complete' => !empty($resolved['complete']),
                'user_id' => $resolved['user_id'] ?? 0,
                'device_count' => $resolved['device_count'] ?? 0,
                'schema' => $resolved['schema'],
            ));
        } catch (Throwable $schemaError) {
            iot_api_error($schemaError);
        }
    }

    if ($action === 'device_card_click') {
        $body = json_decode((string) file_get_contents('php://input'), true);
        if (!is_array($body)) {
            $body = $_POST;
        }
        iot_json(array(
            'success' => true,
            'message' => 'Device card click accepted. Connect app developer control API here.',
            'node_id' => (string) ($body['node_id'] ?? ''),
        ));
    }

    if ($action === 'device_power_toggle') {
        try {
            $body = json_decode((string) file_get_contents('php://input'), true);
            if (!is_array($body)) {
                $body = $_POST;
            }
            if (!is_array($body)) {
                $body = array();
            }
            $nodeId = trim((string) ($body['node_id'] ?? ''));
            if ($nodeId === '') {
                iot_json(array('success' => false, 'message' => 'node_id is required.'), 400);
            }

            $userId = iot_user_id_for_token($db, $token);
            $resolvedNodeId = iot_resolve_product_node_id($db, $nodeId, $userId, $token);
            $currentPower = iot_find_device_power($db, $token, $resolvedNodeId);
            $resolved = iot_resolve_power_command($body, $currentPower);
            iot_set_device_power($db, $token, $resolvedNodeId, (bool) $resolved['power']);

            iot_json(array(
                'success' => true,
                'message' => 'Power command accepted.',
                'node_id' => $resolvedNodeId,
                'command' => $resolved['command'],
                'power' => $resolved['power'],
            ));
        } catch (Throwable $toggleError) {
            iot_api_error($toggleError);
        }
    }

    /* ─────────────────────────────────────────────────────────────
     * action=check_device
     * POST ?action=check_device
     * Header: Authorization: <access_token>
     * Body (JSON): { "node_id": "...", "latitude": "...", "longitude": "..." }
     *
     * Checks whether the scanned node exists in efactorproduct and is
     * available to be claimed by this user.
     * ─────────────────────────────────────────────────────────────*/
    if ($action === 'check_device') {
        try {
            $body = json_decode((string) file_get_contents('php://input'), true);
            if (!is_array($body)) {
                $body = $_POST;
            }
            if (!is_array($body)) {
                $body = array();
            }

            // node_id: accept from body or query string, then normalise.
            $rawNodeId = trim((string) ($body['node_id'] ?? $_GET['node_id'] ?? ''));
            // Strip the "UID:" / " EF:V1" wrapper that some QR codes emit.
            if (preg_match('/UID\s*:\s*([0-9A-Fa-f]+)/i', $rawNodeId, $m)) {
                $rawNodeId = $m[1];
            }
            $nodeId = iot_normalize_node_id($rawNodeId);

            if ($nodeId === '') {
                iot_json(array('success' => false, 'message' => 'node_id is required.'), 400);
            }

            // Validate that the token belongs to a real user.
            $userId = iot_user_id_for_token($db, $token);
            if ($userId <= 0) {
                iot_json(array('success' => false, 'message' => 'Access token is not valid.'), 401);
            }

            // Look up the device in efactorproduct.
            $stmt = $db->prepare(
                'SELECT node_id, device_type, model_number, access_token, user_id
                   FROM efactorproduct
                  WHERE node_id = ?
                  LIMIT 1'
            );
            $stmt->bind_param('s', $nodeId);
            $stmt->execute();
            $device = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$device) {
                iot_json(array('success' => false, 'message' => 'Device not found. Check the node ID.'), 404);
            }

            // Check whether the device is already claimed by a different user.
            $existingToken = trim((string) ($device['access_token'] ?? ''));
            $existingUserId = (int) ($device['user_id'] ?? 0);

            $isPlaceholder = $existingToken === ''
                || strtolower($existingToken) === 'null'
                || strtolower($existingToken) === 'undefined'
                || $existingToken === '0';

            if (!$isPlaceholder && $existingUserId > 0 && $existingUserId !== $userId) {
                iot_json(array(
                    'success' => false,
                    'message' => 'Device is already registered to another account.',
                    'status'  => 'already_claimed',
                ), 409);
            }

            iot_json(array(
                'success'      => true,
                'status'       => 'success',
                'message'      => 'Device verified. Ready to add.',
                'node_id'      => $nodeId,
                'device_type'  => (string) ($device['device_type'] ?? ''),
                'model_number' => (string) ($device['model_number'] ?? ''),
            ));
        } catch (Throwable $checkError) {
            iot_api_error($checkError);
        }
    }

    /* ─────────────────────────────────────────────────────────────
     * action=add_wifi_node
     * POST ?action=add_wifi_node&node_id=<hex>
     * Header: Authorization: <access_token>
     * Body (JSON): { "node_id": "...", "latitude": "...", "longitude": "..." }
     *
     * Associates the device with the authenticated user's account.
     * Mirrors the logic in the original v8/api.php?action=add_wifi_node.
     * ─────────────────────────────────────────────────────────────*/
    if ($action === 'add_wifi_node') {
        try {
            $body = json_decode((string) file_get_contents('php://input'), true);
            if (!is_array($body)) {
                $body = $_POST;
            }
            if (!is_array($body)) {
                $body = array();
            }

            // node_id: query string takes precedence (matches original API contract),
            // then body, then normalise.
            $rawNodeId = trim((string) ($_GET['node_id'] ?? $body['node_id'] ?? ''));
            if (preg_match('/UID\s*:\s*([0-9A-Fa-f]+)/i', $rawNodeId, $m)) {
                $rawNodeId = $m[1];
            }
            $nodeId = iot_normalize_node_id($rawNodeId);

            if ($nodeId === '') {
                iot_json(array('success' => false, 'message' => 'node_id is required.'), 400);
            }

            // Validate access token → real user row (must exist in users table).
            // Strip "Bearer " prefix if present — users.access_token stores the raw JWT.
            $lookupToken = $token;
            if (preg_match('/^Bearer\s+(\S+)/i', $lookupToken, $bearerMatch)) {
                $lookupToken = $bearerMatch[1];
            }
            $lookupToken = trim($lookupToken);

            $userStmt = $db->prepare(
                'SELECT id, phone, email, mob_device_token, access_token
                   FROM users
                  WHERE access_token = ?
                  LIMIT 1'
            );
            $userStmt->bind_param('s', $lookupToken);
            $userStmt->execute();
            $userRow = $userStmt->get_result()->fetch_assoc();
            $userStmt->close();

            if (!$userRow) {
                iot_json(array('success' => false, 'message' => 'Access token does not match any user account.'), 401);
            }

            // Use the raw token stored in users row for subsequent DB writes (efactorproduct.access_token).
            $writeToken = (string) ($userRow['access_token'] ?? $lookupToken);

            $userId          = (int) $userRow['id'];
            $phone           = (string) ($userRow['phone'] ?? '');
            $email           = (string) ($userRow['email'] ?? '');
            $mobDeviceToken  = (string) ($userRow['mob_device_token'] ?? '');

            // Look up device row.
            $devStmt = $db->prepare(
                'SELECT node_id, device_type, model_number, access_token, user_id
                   FROM efactorproduct
                  WHERE node_id = ?
                  LIMIT 1'
            );
            $devStmt->bind_param('s', $nodeId);
            $devStmt->execute();
            $deviceRow = $devStmt->get_result()->fetch_assoc();
            $devStmt->close();

            if (!$deviceRow) {
                iot_json(array('success' => false, 'message' => 'Device not found in efactorproduct.'), 404);
            }

            $deviceType  = (string) ($deviceRow['device_type'] ?? '');
            $modelNumber = (string) ($deviceRow['model_number'] ?? '');

            // Build device name from node ID segments (matches original logic).
            $len = strlen($nodeId);
            if ($len === 32) {
                $seg1 = substr($nodeId, 16, 2);
                $seg2 = substr($nodeId, 24, 2);
                $seg3 = substr($nodeId, 26, 2);
                $seg4 = substr($nodeId, 28, 2);
            } elseif ($len === 20) {
                $seg1 = substr($nodeId,  8, 2);
                $seg2 = substr($nodeId, 10, 2);
                $seg3 = substr($nodeId, 12, 2);
                $seg4 = substr($nodeId, 14, 2);
            } else {
                iot_json(array(
                    'success'    => false,
                    'status'     => 'failure',
                    'error_code' => '100010',
                    'message'    => 'Invalid node_id length (' . $len . '). Expected 32 or 20 hex characters.',
                ), 400);
            }

            $deviceName = $deviceType . '_' . strtoupper($seg1 . $seg2 . $seg3 . $seg4);
            $now        = date('Y-m-d H:i:s');

            // Validate model_number before using it as a table name (whitelist check).
            $validModel = false;
            if ($modelNumber !== '' && $modelNumber !== 'EFDEFLT V1.0') {
                $tableCheckStmt = $db->prepare(
                    "SELECT TABLE_NAME FROM information_schema.TABLES
                      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
                      LIMIT 1"
                );
                $tableCheckStmt->bind_param('s', $modelNumber);
                $tableCheckStmt->execute();
                $validModel = (bool) $tableCheckStmt->get_result()->fetch_assoc();
                $tableCheckStmt->close();
            }

            // ── P0: Commit the ownership mapping, then verify it is authoritative ──
            // Do NOT return success until the write is confirmed and the node
            // is visible in get_iot_devices for this exact user.
            $upd = $db->prepare(
                'UPDATE efactorproduct
                    SET access_token      = ?,
                        user_id           = ?,
                        mob_device_token  = ?,
                        name              = ?,
                        time              = ?,
                        current_devicetype= ?,
                        phone             = ?,
                        email             = ?
                  WHERE node_id = ?'
            );
            $upd->bind_param(
                'sissssss' . 's',
                $writeToken, $userId, $mobDeviceToken,
                $deviceName, $now, $deviceName,
                $phone, $email,
                $nodeId
            );
            if (!$upd->execute()) {
                error_log('[add_wifi_node] DB update failed for node=' . $nodeId . ' user=' . $userId);
                iot_json(array('success' => false, 'status' => 'failure', 'message' => 'Failed to update device record.'), 500);
            }
            $affectedRows = $upd->affected_rows;
            $upd->close();

            // Diagnostic log — no token values, only IDs and counts.
            error_log('[add_wifi_node] node=' . $nodeId . ' user=' . $userId . ' affected_rows=' . $affectedRows);

            // If the UPDATE matched zero rows the node_id is wrong — reject immediately.
            if ($affectedRows === 0) {
                // Read back to confirm whether the row even exists.
                $chkStmt = $db->prepare('SELECT user_id FROM efactorproduct WHERE node_id = ? LIMIT 1');
                $chkStmt->bind_param('s', $nodeId);
                $chkStmt->execute();
                $chkRow = $chkStmt->get_result()->fetch_assoc();
                $chkStmt->close();
                $chkUserId = (int) ($chkRow['user_id'] ?? 0);
                error_log('[add_wifi_node] node=' . $nodeId . ' row_exists=' . ($chkRow ? 'yes' : 'no') . ' stored_user_id=' . $chkUserId);
                if (!$chkRow) {
                    iot_json(array('success' => false, 'status' => 'failure', 'message' => 'Device not found in efactorproduct.'), 404);
                }
                // Idempotent: device already belongs to this user — return success.
                if ($chkUserId === $userId) {
                    error_log('[add_wifi_node] node=' . $nodeId . ' idempotent — already owned by user=' . $userId);
                    iot_json(array(
                        'success'         => true,
                        'status'          => 'success',
                        'message'         => 'Device is already registered to your account.',
                        'node_id'         => $nodeId,
                        'device_name'     => $deviceName,
                        'visibleAfterAdd' => true,
                        'total'           => 0,
                        'online'          => 0,
                        'offline'         => 0,
                        'devices'         => array(),
                    ));
                }
                // Row exists but owned by someone else — conflict.
                iot_json(array(
                    'success' => false,
                    'status'  => 'failure',
                    'message' => 'Device is already registered to another account.',
                    'node_id' => $nodeId,
                ), 409);
            }

            // Also update the model-specific table if it exists and is not the default.
            if ($validModel) {
                // Backtick-quoted, whitelist-verified table name — safe to use directly.
                $modelUpd = $db->prepare(
                    "UPDATE `{$modelNumber}`
                        SET access_token      = ?,
                            user_id           = ?,
                            mob_device_token  = ?,
                            name              = ?,
                            time              = ?,
                            current_devicetype= ?,
                            phone             = ?,
                            email             = ?
                      WHERE node_id = ?"
                );
                $modelUpd->bind_param(
                    'sissssss' . 's',
                    $writeToken, $userId, $mobDeviceToken,
                    $deviceName, $now, $deviceName,
                    $phone, $email,
                    $nodeId
                );
                $modelUpd->execute();
                $modelUpd->close();
            }

            // ── P0: Confirmation read-back — verify ownership is authoritative ──
            // get_iot_devices uses WHERE user_id = ? — so confirm that column is now set.
            $confirmStmt = $db->prepare(
                'SELECT user_id FROM efactorproduct WHERE node_id = ? LIMIT 1'
            );
            $confirmStmt->bind_param('s', $nodeId);
            $confirmStmt->execute();
            $confirmRow = $confirmStmt->get_result()->fetch_assoc();
            $confirmStmt->close();

            $storedUserId = (int) ($confirmRow['user_id'] ?? 0);
            $ownershipConfirmed = ($storedUserId === $userId);
            error_log('[add_wifi_node] node=' . $nodeId . ' stored_user_id=' . $storedUserId . ' expected_user_id=' . $userId . ' ownership_confirmed=' . ($ownershipConfirmed ? 'yes' : 'no'));

            if (!$ownershipConfirmed) {
                iot_json(array(
                    'success' => false,
                    'status'  => 'failure',
                    'message' => 'Device ownership could not be confirmed after write. Please try again.',
                    'node_id' => $nodeId,
                ), 500);
            }

            // ── P0: Use the same token/userId source as get_iot_devices ──
            // Wrap in try/catch so a list-fetch failure never turns a committed add into HTTP 500.
            $devices = array();
            $stats   = array('total' => 0, 'online' => 0, 'offline' => 0);
            $nodePresent = false;
            try {
                $devices = iot_devices_with_live_status($db, $writeToken);
                $stats   = iot_device_stats($devices);

                // ── P0: Confirm the added node appears in the immediate device list ──
                foreach ($devices as $dev) {
                    if (is_array($dev) && strcasecmp(trim((string) ($dev['node_id'] ?? '')), $nodeId) === 0) {
                        $nodePresent = true;
                        break;
                    }
                }
                error_log('[add_wifi_node] node=' . $nodeId . ' device_count=' . count($devices) . ' node_visible_in_list=' . ($nodePresent ? 'yes' : 'no'));

                // If the node is not in the list yet try the token-only path which always reads fresh rows.
                if (!$nodePresent) {
                    iot_require_device_normalizer();
                    $freshDevices = iot_devices_by_access_token($db, $writeToken);
                    if (!empty($freshDevices)) {
                        $apiBase  = iot_public_api_base();
                        $allNodes = iot_fetch_all_nodes($db, $writeToken);
                        $userId2  = iot_user_id_for_token($db, $writeToken);
                        $freshDevices = (new \App\Libraries\IotNodesStatusBuilder())->enrichDevicesLiveState($db, $freshDevices, $allNodes, $userId2);
                        $normalized = array();
                        foreach ($freshDevices as $dev) {
                            if (is_array($dev)) {
                                $normalized[] = \App\Libraries\IotDeviceNormalizer::finalize($dev, $apiBase);
                            }
                        }
                        $seen = array();
                        foreach ($devices as $dev) {
                            $key = trim((string) ($dev['node_id'] ?? $dev['id'] ?? ''));
                            if ($key !== '') {
                                $seen[$key] = true;
                            }
                        }
                        foreach ($normalized as $dev) {
                            $key = trim((string) ($dev['node_id'] ?? $dev['id'] ?? ''));
                            if ($key !== '' && !isset($seen[$key])) {
                                $devices[] = $dev;
                            }
                        }
                        $devices = iot_schema_builder()->sortDevicesOnlineFirst($devices);
                        $stats   = iot_device_stats($devices);
                        foreach ($devices as $dev) {
                            if (is_array($dev) && strcasecmp(trim((string) ($dev['node_id'] ?? '')), $nodeId) === 0) {
                                $nodePresent = true;
                                break;
                            }
                        }
                        error_log('[add_wifi_node] node=' . $nodeId . ' after_merge device_count=' . count($devices) . ' node_visible=' . ($nodePresent ? 'yes' : 'no'));
                    }
                }
            } catch (Throwable $listError) {
                // A failure fetching the device list must NOT cause the committed add to return HTTP 500.
                // The ownership is already confirmed above; return success with empty devices array.
                error_log('[add_wifi_node] device_list_fetch_failed node=' . $nodeId . ' error=' . $listError->getMessage());
                $nodePresent = true; // ownership was confirmed above; list is just stale
            }

            // Keep IoT SDUI schema aligned with the updated device roster.
            try {
                iot_resolve_schema($db, $writeToken, true, false);
            } catch (Throwable $schemaSyncError) {
                error_log('[add_wifi_node] schema_sync_failed node=' . $nodeId . ' error=' . $schemaSyncError->getMessage());
            }

            iot_json(array(
                'success'              => true,
                'status'               => 'success',
                'message'              => 'Device added successfully.',
                'node_id'              => $nodeId,
                'device_name'          => $deviceName,
                // Diagnostic field: Flutter can log this to confirm the added node is in the list.
                'visibleAfterAdd'      => $nodePresent,
                'total'                => $stats['total'],
                'online'               => $stats['online'],
                'offline'              => $stats['offline'],
                'devices'              => $devices,
            ));
        } catch (Throwable $addError) {
            iot_api_error($addError);
        }
    }

    /* ─────────────────────────────────────────────────────────────
     * action=remove_node
     * POST ?action=remove_node
     * Header: Authorization: <access_token>
     * Body (JSON): { "node_id": "..." }
     *
     * Removes the device association from the authenticated user's account
     * by clearing access_token, user_id, and related columns.
     * ─────────────────────────────────────────────────────────────*/
    if ($action === 'remove_node') {
        try {
            $body = json_decode((string) file_get_contents('php://input'), true);
            if (!is_array($body)) {
                $body = $_POST;
            }
            if (!is_array($body)) {
                $body = array();
            }

            $rawNodeId = trim((string) ($body['node_id'] ?? $_GET['node_id'] ?? ''));
            if (preg_match('/UID\s*:\s*([0-9A-Fa-f]+)/i', $rawNodeId, $m)) {
                $rawNodeId = $m[1];
            }
            $nodeId = iot_normalize_node_id($rawNodeId);

            if ($nodeId === '') {
                iot_json(array('success' => false, 'message' => 'node_id is required.'), 400);
            }

            // Validate token → user.
            $userId = iot_user_id_for_token($db, $token);
            if ($userId <= 0) {
                iot_json(array('success' => false, 'message' => 'Access token is not valid.'), 401);
            }

            // Verify the device exists and belongs to this user.
            $devStmt = $db->prepare(
                'SELECT node_id, model_number, user_id, access_token
                   FROM efactorproduct
                  WHERE node_id = ?
                  LIMIT 1'
            );
            $devStmt->bind_param('s', $nodeId);
            $devStmt->execute();
            $deviceRow = $devStmt->get_result()->fetch_assoc();
            $devStmt->close();

            if (!$deviceRow) {
                iot_json(array('success' => false, 'message' => 'Device not found.'), 404);
            }

            $deviceUserId = (int) ($deviceRow['user_id'] ?? 0);
            if ($deviceUserId > 0 && $deviceUserId !== $userId) {
                iot_json(array('success' => false, 'message' => 'Device does not belong to your account.'), 403);
            }

            $modelNumber = (string) ($deviceRow['model_number'] ?? '');
            $empty       = '';
            $zeroId      = 0;

            // Clear assignment columns on efactorproduct.
            $clr = $db->prepare(
                'UPDATE efactorproduct
                    SET access_token      = ?,
                        user_id           = ?,
                        mob_device_token  = ?,
                        name              = ?,
                        current_devicetype= ?,
                        phone             = ?,
                        email             = ?
                  WHERE node_id = ?'
            );
            $clr->bind_param('sissssss', $empty, $zeroId, $empty, $empty, $empty, $empty, $empty, $nodeId);
            if (!$clr->execute()) {
                iot_json(array('success' => false, 'message' => 'Failed to remove device record.'), 500);
            }
            $clr->close();

            // Also clear the model-specific table if it exists.
            if ($modelNumber !== '' && $modelNumber !== 'EFDEFLT V1.0') {
                $tableCheckStmt = $db->prepare(
                    "SELECT TABLE_NAME FROM information_schema.TABLES
                      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
                      LIMIT 1"
                );
                $tableCheckStmt->bind_param('s', $modelNumber);
                $tableCheckStmt->execute();
                $tableExists = (bool) $tableCheckStmt->get_result()->fetch_assoc();
                $tableCheckStmt->close();

                if ($tableExists) {
                    $mClr = $db->prepare(
                        "UPDATE `{$modelNumber}`
                            SET access_token      = ?,
                                user_id           = ?,
                                mob_device_token  = ?,
                                name              = ?,
                                current_devicetype= ?,
                                phone             = ?,
                                email             = ?
                          WHERE node_id = ?"
                    );
                    $mClr->bind_param('sissssss', $empty, $zeroId, $empty, $empty, $empty, $empty, $empty, $nodeId);
                    $mClr->execute();
                    $mClr->close();
                }
            }

            // Return refreshed device list.
            $devices = iot_devices_with_live_status($db, $token);
            $stats   = iot_device_stats($devices);

            try {
                iot_resolve_schema($db, $token, true, false);
            } catch (Throwable $schemaSyncError) {
                error_log('[remove_node] schema_sync_failed node=' . $nodeId . ' error=' . $schemaSyncError->getMessage());
            }

            iot_json(array(
                'success' => true,
                'status'  => 'success',
                'message' => 'Device removed successfully.',
                'node_id' => $nodeId,
                'total'   => $stats['total'],
                'online'  => $stats['online'],
                'offline' => $stats['offline'],
                'devices' => $devices,
            ));
        } catch (Throwable $removeError) {
            iot_api_error($removeError);
        }
    }

    iot_json(array('success' => false, 'message' => 'Unknown action.'), 404);
} catch (Throwable $e) {
    iot_api_error($e);
}

