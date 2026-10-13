<?php

namespace App\Models;

class IotDashboardModel
{
    private $db;

    public function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
        $this->db = db_connect('default');
        $this->ensureSchema();
    }

    // -------------------------------------------------------------------------
    // Device fetchers
    // -------------------------------------------------------------------------

    public function getDevicesForUser(int $userId, bool $isAdmin = false): array
    {
        $where  = '';
        $params = array();

        if (!$isAdmin) {
            $where    = 'WHERE p.user_id = ?';
            $params[] = $userId;
        }

        return $this->getDevicesByWhere($where, $params);
    }

    /**
     * Device list with live node power/online merged from getAllNodesStatus.
     */
    public function getDevicesWithLiveStatus(int $userId, bool $isAdmin = false, string $accessToken = ''): array
    {
        $devices = $this->getDevicesForUser($userId, $isAdmin);
        if ($devices === array()) {
            return array();
        }

        if ($accessToken === '') {
            $accessToken = $this->getSavedAccessToken($userId, $isAdmin, $devices);
        }

        $mysqli = $this->getMysqliConnection();
        if ($mysqli instanceof \mysqli && $accessToken !== '') {
            try {
                $payload = (new \App\Libraries\IotNodesStatusBuilder())->build($mysqli, $accessToken, $userId);
                $allNodes = is_array($payload['all_nodes'] ?? null) ? $payload['all_nodes'] : array();
                $devices = (new \App\Libraries\IotNodesStatusBuilder())->enrichDevicesLiveState($mysqli, $devices, $allNodes, $userId);
            } catch (\Throwable $e) {
                log_message('error', 'IoT live device status merge failed: ' . $e->getMessage());
            }
        }

        return (new \App\Libraries\IotSduiSchemaBuilder())->hydrateDevices($devices, $this->getPublicApiBaseUrl());
    }

    public function getNodeIdsForUser(int $userId, bool $isAdmin = false): array
    {
        $builder = $this->db->table('efactorproduct')
            ->select('node_id')
            ->where('node_id IS NOT NULL', null, false)
            ->where('node_id !=', '');

        if (!$isAdmin) {
            $builder->where('user_id', $userId);
        }

        $rows    = $builder->orderBy('id', 'DESC')->get()->getResultArray();
        $nodeIds = array();
        foreach ($rows as $row) {
            $nodeId = trim((string) ($row['node_id'] ?? ''));
            if ($nodeId !== '' && !in_array($nodeId, $nodeIds, true)) {
                $nodeIds[] = $nodeId;
            }
        }

        return $nodeIds;
    }

    public function getProductsForUserName(int $userId, string $name, bool $isAdmin = false): array
    {
        $name = trim($name);
        if ($name === '') {
            return array();
        }

        $whereParts = array('(p.vendor_name = ? OR p.vendor_type = ? OR p.device_type = ? OR p.name = ?)');
        $params     = array($name, $name, $name, $name);
        if (!$isAdmin) {
            array_unshift($whereParts, 'p.user_id = ?');
            array_unshift($params, $userId);
        }

        return $this->getDevicesByWhere('WHERE ' . implode(' AND ', $whereParts), $params);
    }

    public function getDevicesByAccessToken(string $accessToken): array
    {
        return $this->getDevicesByWhere('WHERE p.access_token = ?', array($accessToken));
    }

    // -------------------------------------------------------------------------
    // Schema — build (production-grade)
    // -------------------------------------------------------------------------

    /**
     * Build a production-level IoT SDUI dashboard schema (v2.2.1).
     */
    public function buildSchema(array $devices, int $userId = 0, string $accessToken = ''): array
    {
        $builder = new \App\Libraries\IotSduiSchemaBuilder();

        return $builder->build(
            $devices,
            $userId,
            $accessToken,
            $this->getPublicApiBaseUrl(),
            site_url('iot-dashboard/api')
        );
    }

    /**
     * Build schema with live device/node metrics and persist to both schema tables.
     *
     * @return array{schema: array<string, mixed>, save: array<string, mixed>, access_token: string}
     */
    public function generateAndPersistSchema(int $userId, bool $isAdmin = false, string $accessToken = ''): array
    {
        $devices = $this->getDevicesForUser($userId, $isAdmin);
        if ($accessToken === '') {
            // Prefer users.access_token so brand-new accounts (0 devices) still get a schema.
            $accessToken = $this->resolveOnboardingAccessToken($userId, $isAdmin, $devices);
        }
        if ($accessToken === '') {
            $accessToken = $this->getSavedAccessToken($userId, $isAdmin, $devices);
        }

        $devices = $this->getDevicesWithLiveStatus($userId, $isAdmin, $accessToken);
        $builder = new \App\Libraries\IotSduiSchemaBuilder();
        $apiBase = $this->getPublicApiBaseUrl();
        $schema = $builder->build(
            $devices,
            $userId,
            $accessToken,
            $apiBase,
            site_url('iot-dashboard/api')
        );

        $allNodes = array();
        $mysqli = $this->getMysqliConnection();
        if ($mysqli instanceof \mysqli && $accessToken !== '') {
            try {
                $nodesPayload = (new \App\Libraries\IotNodesStatusBuilder())->build($mysqli, $accessToken, $userId);
                $allNodes = is_array($nodesPayload['all_nodes'] ?? null) ? $nodesPayload['all_nodes'] : array();
            } catch (\Throwable $e) {
                log_message('error', 'IoT generateAndPersistSchema nodes status: ' . $e->getMessage());
            }
        }

        $schema = $builder->applyLiveMetrics($schema, $devices, $apiBase, $allNodes, false);
        $schema['savedAt'] = date('c');
        $schema['savedDate'] = date('Y-m-d');
        $schema['savedTime'] = date('H:i:s');
        $schema['savedTimezone'] = date_default_timezone_get();
        $schema['userId'] = $userId > 0 ? $userId : ($schema['userId'] ?? null);
        $schema = $this->stampSchemaSourceFingerprint($schema, $devices, $accessToken);

        $saveResult = $this->saveGeneratedSchema($userId, $schema, $accessToken);

        return array(
            'schema' => $schema,
            'save' => $saveResult,
            'access_token' => $accessToken,
        );
    }

    /**
     * Ensure an IoT SDUI schema exists for this user (empty-device shell is fine).
     * Used on first app/web access so users never need to click "Skema Generate".
     *
     * @return array{success: bool, schema: array<string, mixed>, access_token: string, created: bool, message?: string}
     */
    public function ensureSchemaForUser(int $userId, bool $isAdmin = false): array
    {
        if ($userId <= 0) {
            return array(
                'success' => false,
                'schema' => array(),
                'access_token' => '',
                'created' => false,
                'message' => 'Not signed in.',
            );
        }

        $existing = $this->getGeneratedSchema($userId);
        $devices = $this->getDevicesForUser($userId, $isAdmin);
        $accessToken = $this->resolveOnboardingAccessToken($userId, $isAdmin, $devices);
        if ($accessToken === '') {
            $accessToken = $this->getSavedAccessToken($userId, $isAdmin, $devices);
        }

        $sourceFp = $this->devicesSourceFingerprint($devices, $accessToken);
        $storedFp = $existing === array() ? '' : $this->readSchemaSourceFingerprint($existing);
        $fingerprintStale = ($sourceFp !== '' && $storedFp !== $sourceFp);

        $needsCreate = ($existing === array()
            || empty($existing['dataSources'])
            || empty($existing['schemaVersion'])
            || !\App\Libraries\IotSduiSchemaBuilder::isSduiCapable($existing)
            || $fingerprintStale);

        if (!$needsCreate) {
            return array(
                'success' => true,
                'schema' => $existing,
                'access_token' => $accessToken,
                'created' => false,
            );
        }

        try {
            $generated = $this->generateAndPersistSchema($userId, $isAdmin, $accessToken);
            $schema = is_array($generated['schema'] ?? null) ? $generated['schema'] : array();
            if ($schema === array()) {
                $schema = $this->getSharedIotSchemaTemplate();
            }
            return array(
                'success' => $schema !== array(),
                'schema' => $schema,
                'access_token' => (string) ($generated['access_token'] ?? $accessToken),
                'created' => true,
                'message' => $schema !== array() ? 'IoT schema auto-generated.' : 'Schema generation returned empty.',
            );
        } catch (\Throwable $e) {
            log_message('error', 'IoT ensureSchemaForUser failed: ' . $e->getMessage());
            if ($existing === array()) {
                $existing = $this->getSharedIotSchemaTemplate();
            }
            return array(
                'success' => $existing !== array(),
                'schema' => $existing,
                'access_token' => $accessToken,
                'created' => false,
                'message' => $e->getMessage(),
            );
        }
    }
    // -------------------------------------------------------------------------
    // Schema — save / retrieve (CI session path, keyed by user_id)
    // -------------------------------------------------------------------------

    public function saveGeneratedSchema(int $userId, array $schema, string $accessToken = ''): array
    {
        $this->ensureSchema();

        $now         = date('Y-m-d H:i:s');
        $schemaJson  = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $version     = isset($schema['schemaVersion']) ? (string) $schema['schemaVersion'] : '2.3.2';
        $tokenHash   = $accessToken !== '' ? hash('sha256', $accessToken) : '';

        $sql = "
            INSERT INTO iot_dashboard_schemas
                (user_id, access_token, access_token_hash, schema_json, schema_version, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                access_token      = VALUES(access_token),
                access_token_hash = VALUES(access_token_hash),
                schema_json       = VALUES(schema_json),
                schema_version    = VALUES(schema_version),
                updated_at        = VALUES(updated_at)
        ";

        $params = array($userId, $accessToken, $tokenHash, $schemaJson, $version, $now, $now);

        try {
            $saved = (bool) $this->db->query($sql, $params);
        } catch (\Throwable $e) {
            log_message('error', 'IoT saveGeneratedSchema first attempt: ' . $e->getMessage());
            $this->ensureColumn(
                'iot_dashboard_schemas',
                'access_token',
                "ALTER TABLE `iot_dashboard_schemas`
                 ADD COLUMN `access_token` VARCHAR(1024) NOT NULL DEFAULT '' AFTER `user_id`"
            );
            try {
                $saved = (bool) $this->db->query($sql, $params);
            } catch (\Throwable $e2) {
                log_message('error', 'IoT saveGeneratedSchema retry failed: ' . $e2->getMessage());
                return array('success' => false, 'error' => $e2->getMessage());
            }
        }

        $this->ensureAccessTokenColumnWidth('iot_dashboard_schemas', 'access_token', 1024);

        if ($saved && $accessToken !== '') {
            try {
                $this->saveTokenSchema($accessToken, $schema, $version, $now);
            } catch (\Throwable $e) {
                log_message('error', 'IoT saveTokenSchema failed: ' . $e->getMessage());
            }
        }

        return array('success' => $saved);
    }

    /**
     * Mirror schema into the token-keyed table used by v8/iot_api_creation.php.
     */
    private function saveTokenSchema(string $accessToken, array $schema, string $version, string $now): void
    {
        $hash       = hash('sha256', $accessToken);
        $schemaJson = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $sql = "
            INSERT INTO iot_dashboard_token_schemas
                (access_token_hash, schema_json, schema_version, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                schema_json    = VALUES(schema_json),
                schema_version = VALUES(schema_version),
                updated_at     = VALUES(updated_at)
        ";

        $this->db->query($sql, array($hash, $schemaJson, $version, $now, $now));
    }

    /**
     * Profile access token — always from efactorproduct (devices on screen first).
     */
    public function getSavedAccessToken(int $userId, bool $isAdmin = false, array $devices = array()): string
    {
        return $this->resolveProfileAccessToken($userId, $isAdmin, $devices);
    }

    /**
     * Resolve token from displayed devices, then efactorproduct for this profile.
     */
    public function resolveProfileAccessToken(int $userId, bool $isAdmin = false, array $devices = array()): string
    {
        $token = $this->pickAccessTokenFromDevices($devices);
        if ($token !== '') {
            return $token;
        }

        $token = $this->fetchAccessTokenFromEfactorproduct($userId, $isAdmin);
        if ($token !== '') {
            return $token;
        }

        $token = $this->fetchAccessTokenViaUserPhone($userId);
        if ($token !== '') {
            return $token;
        }

        $token = $this->fetchAccessTokenFromUsers($userId);
        if ($token !== '') {
            return $token;
        }

        return $this->fetchAccessTokenFromSchemaTable($userId);
    }

    /**
     * Session JWT from users table — canonical token for onboarding APIs.
     */
    private function fetchAccessTokenFromUsers(int $userId): string
    {
        return $this->fetchAccessTokenFromUsersRaw($userId);
    }

    /**
     * Read users.access_token without JWT expiry filtering (legacy mobile tokens).
     */
    private function fetchAccessTokenFromUsersRaw(int $userId): string
    {
        if ($userId <= 0) {
            return '';
        }

        try {
            $row = $this->db->table('users')
                ->select('access_token')
                ->where('id', $userId)
                ->get()
                ->getRowArray();

            $token = trim((string) ($row['access_token'] ?? ''));
            if ($token !== '' && !$this->isPlaceholderToken($token)) {
                return $token;
            }
        } catch (\Throwable $e) {
            log_message('error', 'IoT fetchAccessTokenFromUsersRaw: ' . $e->getMessage());
        }

        return '';
    }

    /**
     * Token to bind new devices — prefer tokens already on this user's products.
     */
    public function resolveDeviceBindingToken(int $userId, bool $isAdmin = false, array $devices = array()): string
    {
        if ($devices === array() && $userId > 0) {
            $devices = $this->getDevicesForUser($userId, $isAdmin);
        }

        // Legacy v8 check_device / add_wifi_node authenticate against users.access_token.
        $fromUsers = $this->fetchAccessTokenFromUsersRaw($userId);
        if ($fromUsers !== '') {
            return $fromUsers;
        }

        $fromDevices = $this->pickAccessTokenFromDevices($devices, false);
        if ($fromDevices !== '') {
            return $fromDevices;
        }

        return $this->resolveProfileAccessToken($userId, $isAdmin, $devices);
    }

    /**
     * Prefer live users.access_token for QR onboarding; fall back to profile token chain.
     */
    public function resolveOnboardingAccessToken(int $userId, bool $isAdmin = false, array $devices = array()): string
    {
        return $this->resolveDeviceBindingToken($userId, $isAdmin, $devices);
    }

    /**
     * Normalize QR payloads (JSON, URLs, socket_ prefix) to a hardware node id.
     */
    public function normalizeScannedNodeId(string $raw): string
    {
        $value = trim($raw);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if ($value === '') {
            return '';
        }

        // Some scanners wrap the payload in quotes or encode spaces.
        if (strpos($value, '%') !== false) {
            $decoded = rawurldecode($value);
            if (is_string($decoded) && $decoded !== '') {
                $value = trim($decoded);
            }
        }

        if (isset($value[0]) && $value[0] === '{') {
            $parsed = json_decode($value, true);
            if (is_array($parsed)) {
                foreach (array('node_id', 'nodeId', 'NodeID', 'NODE_ID', 'id', 'device_id', 'deviceId') as $key) {
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
                foreach (array('node_id', 'nodeId', 'id') as $key) {
                    if (!empty($query[$key])) {
                        $value = trim((string) $query[$key]);
                        break;
                    }
                }
            }
            if ($value !== '' && preg_match('/^https?:\/\//i', $value) && !empty($parts['path'])) {
                $pathParts = array_values(array_filter(explode('/', (string) $parts['path'])));
                if ($pathParts !== array()) {
                    $value = trim((string) end($pathParts));
                }
            }
        }

        if (preg_match('/(?:^|\b)UID\s*:\s*([A-Za-z0-9_-]{16,64})/i', $value, $matches)) {
            return trim((string) $matches[1]);
        }

        if (preg_match('/(?:node_id|nodeId|node)\s*[:=]\s*([A-Za-z0-9_-]{16,64})/i', $value, $matches)) {
            return trim((string) $matches[1]);
        }

        if (preg_match('/\b([A-Fa-f0-9]{20,64})\b/', $value, $matches)) {
            return trim((string) $matches[1]);
        }

        if (stripos($value, 'socket_') === 0) {
            $value = substr($value, 7);
        }

        return trim(preg_replace('/[^A-Za-z0-9_-]/', '', $value));
    }

    /**
     * Pull node_id from JSON/post payloads used by web + Flutter clients.
     *
     * @param array<string, mixed> $payload
     */
    public function extractNodeIdFromPayload(array $payload): string
    {
        foreach (array('node_id', 'nodeId', 'NodeID', 'NODE_ID', 'id', 'device_id', 'deviceId') as $key) {
            if (!isset($payload[$key])) {
                continue;
            }
            $value = trim((string) $payload[$key]);
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    /**
     * Verify an unclaimed hardware node before binding (replaces broken v8 check_device proxy).
     *
     * @return array<string, mixed>
     */
    public function checkDeviceNode(int $userId, string $nodeId, string $latitude = '', string $longitude = '', bool $isAdmin = false): array
    {
        if ($userId <= 0) {
            return array('success' => false, 'message' => 'Not signed in.');
        }

        // Keep original QR value too — old app matches node_id exactly (incl. socket_).
        $rawNodeId = trim($nodeId);
        $nodeId = $this->normalizeScannedNodeId($nodeId);
        if ($nodeId === '' && $rawNodeId === '') {
            return array('success' => false, 'message' => 'node_id is required.');
        }
        if ($nodeId === '') {
            $nodeId = $rawNodeId;
        }

        $devices = $this->getDevicesForUser($userId, $isAdmin);
        $accessToken = $this->resolveDeviceBindingToken($userId, $isAdmin, $devices);

        try {
            $row = $this->findProductRowByNodeId($rawNodeId !== '' ? $rawNodeId : $nodeId);
            if ((!is_array($row) || $row === array()) && $rawNodeId !== '' && $rawNodeId !== $nodeId) {
                $row = $this->findProductRowByNodeId($nodeId);
            }
        } catch (\Throwable $e) {
            log_message('error', 'IoT checkDeviceNode lookup failed: ' . $e->getMessage());
            return array('success' => false, 'message' => 'Could not verify device.');
        }

        if (!is_array($row) || $row === array()) {
            // Match v8/api.php check_device wording so app clients stay consistent.
            return array(
                'success'       => false,
                'status'        => 'failure',
                'message'       => 'Device Not Found',
                'error_code'    => '101100',
                'description'   => 'Device Not Found',
            );
        }

        $deviceToken = trim((string) ($row['access_token'] ?? ''));
        $ownerId = (int) ($row['user_id'] ?? 0);
        $isUnclaimed = ($deviceToken === ''
            || strtoupper($deviceToken) === 'NULL'
            || $this->isPlaceholderToken($deviceToken)
            || $ownerId <= 0);

        $resolvedNodeId = trim((string) ($row['node_id'] ?? $nodeId));
        $prodCategory = (string) ($row['prod_category'] ?? '');

        // Already owned by this logged-in user — block re-add (matches legacy v8 Already Added).
        if (!$isUnclaimed && $ownerId === $userId) {
            return array(
                'success'     => false,
                'status'      => 'failure',
                'message'     => 'Already Added',
                'error_code'  => '101005',
                'description' => 'Already Added',
                'prod_category' => $prodCategory,
                'node_id'     => $resolvedNodeId,
            );
        }

        if (!$isUnclaimed) {
            if ($accessToken !== '' && $deviceToken === $accessToken) {
                return array(
                    'success'     => false,
                    'status'      => 'failure',
                    'message'     => 'Already Added',
                    'error_code'  => '101005',
                    'description' => 'Already Added',
                );
            }

            // Same wording as legacy v8 check_device when device token is already bound.
            return array(
                'success'     => false,
                'status'      => 'failure',
                'message'     => 'Do not have permission to do that',
                'error_code'  => '101005',
                'description' => 'Do not have permission to do that',
            );
        }

        $update = array();
        if ($latitude !== '') {
            $update['check_latitude'] = $latitude;
        }
        if ($longitude !== '') {
            $update['check_longitude'] = $longitude;
        }
        if ($update !== array()) {
            $this->db->table('efactorproduct')->where('node_id', $resolvedNodeId)->update($update);
        }

        // Legacy clients treat presence of prod_category as success.
        return array(
            'success'       => true,
            'status'        => 'success',
            'message'       => 'Device verified.',
            'prod_category' => $prodCategory,
            'node_id'       => $resolvedNodeId,
        );
    }

    /**
     * Bind a verified WiFi node to the logged-in user (replaces broken v8 add_wifi_node proxy).
     *
     * @return array<string, mixed>
     */
    public function addWifiNodeToAccount(int $userId, string $nodeId, string $latitude = '', string $longitude = '', bool $isAdmin = false): array
    {
        if ($userId <= 0) {
            return array('success' => false, 'message' => 'Not signed in.');
        }

        $rawNodeId = trim($nodeId);
        $nodeId = $this->normalizeScannedNodeId($nodeId);
        if ($nodeId === '' && $rawNodeId === '') {
            return array('success' => false, 'message' => 'node_id is required.');
        }
        if ($nodeId === '') {
            $nodeId = $rawNodeId;
        }

        try {
            $userRow = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
            if (!is_array($userRow) || $userRow === array()) {
                return array('success' => false, 'message' => 'User profile not found.');
            }

            $devices = $this->getDevicesForUser($userId, $isAdmin);
            // Prefer users.access_token like legacy v8 add_wifi_node (header token = users row).
            $accessToken = trim((string) ($userRow['access_token'] ?? ''));
            if ($accessToken === '' || $this->isPlaceholderToken($accessToken)) {
                $accessToken = $this->resolveDeviceBindingToken($userId, $isAdmin, $devices);
            }
            if ($accessToken === '' || $this->isPlaceholderToken($accessToken)) {
                return array(
                    'success' => false,
                    'message' => 'No access token on your profile. Open IoT Dashboard once or contact support.',
                );
            }

            $deviceRow = $this->findProductRowByNodeId($rawNodeId !== '' ? $rawNodeId : $nodeId);
            if ((!is_array($deviceRow) || $deviceRow === array()) && $rawNodeId !== '' && $rawNodeId !== $nodeId) {
                $deviceRow = $this->findProductRowByNodeId($nodeId);
            }
            if (!is_array($deviceRow) || $deviceRow === array()) {
                return array(
                    'success' => false,
                    'status' => 'failure',
                    'message' => 'Device Not Found',
                    'error_code' => '101100',
                    'description' => 'Device Not Found',
                );
            }

            $resolvedNodeId = trim((string) ($deviceRow['node_id'] ?? $nodeId));
            $deviceToken = trim((string) ($deviceRow['access_token'] ?? ''));
            $ownerId = (int) ($deviceRow['user_id'] ?? 0);
            $isUnclaimed = ($deviceToken === ''
                || strtoupper($deviceToken) === 'NULL'
                || $this->isPlaceholderToken($deviceToken)
                || $ownerId <= 0);

            if (!$isUnclaimed && $ownerId === $userId) {
                return array(
                    'success' => false,
                    'status'  => 'failure',
                    'message' => 'Already Added',
                    'error_code' => '101005',
                    'description' => 'Already Added',
                    'node_id' => $resolvedNodeId,
                );
            }

            if (!$isUnclaimed && $deviceToken === $accessToken) {
                return array(
                    'success' => false,
                    'status'  => 'failure',
                    'message' => 'Already Added',
                    'error_code' => '101005',
                    'description' => 'Already Added',
                    'node_id' => $resolvedNodeId,
                );
            }

            if (!$isUnclaimed) {
                return array(
                    'success' => false,
                    'status' => 'failure',
                    'message' => 'Do not have permission to do that',
                    'error_code' => '101005',
                    'description' => 'Do not have permission to do that',
                );
            }

            $deviceType = (string) ($deviceRow['device_type'] ?? 'device');
            $modelNumber = (string) ($deviceRow['model_number'] ?? '');
            $requestId = (string) random_int(100000, 999999);
            $dt = date('Y-m-d H:i:s');
            // Match legacy v8 naming: deviceType_segments for long node ids.
            $deviceName = $this->buildOnboardingDeviceName($resolvedNodeId, $deviceType);

            $update = array(
                'access_token'       => $accessToken,
                'user_id'            => $userId,
                'mob_device_token'   => (string) ($userRow['mob_device_token'] ?? ''),
                'name'               => $deviceName,
                'time'               => $dt,
                'current_devicetype' => $deviceName,
                'phone'              => (string) ($userRow['phone'] ?? ''),
                'email'              => (string) ($userRow['email'] ?? ''),
                'request_id'         => $requestId,
            );
            if ($latitude !== '') {
                $update['check_latitude'] = $latitude;
            }
            if ($longitude !== '') {
                $update['check_longitude'] = $longitude;
            }

            $this->db->table('efactorproduct')->where('node_id', $resolvedNodeId)->update($update);

            if ($modelNumber !== '' && $modelNumber !== 'EFDEFLT V1.0') {
                $this->updateModelTableRow($modelNumber, $resolvedNodeId, $update);
            }

            // Re-read immediately so refreshAfter/get_iot_devices clients can verify the bind.
            $freshDevices = $this->getDevicesWithLiveStatus($userId, $isAdmin, $accessToken);
            $plainNodeId = preg_replace('/^socket_/i', '', $resolvedNodeId);
            $found = false;
            foreach ($freshDevices as $device) {
                if (!is_array($device)) {
                    continue;
                }
                $candidate = trim((string) ($device['node_id'] ?? ''));
                $candidatePlain = preg_replace('/^socket_/i', '', $candidate);
                if ($candidate === $resolvedNodeId
                    || $candidate === $nodeId
                    || $candidate === $rawNodeId
                    || ($plainNodeId !== '' && strcasecmp($candidatePlain, $plainNodeId) === 0)
                ) {
                    $found = true;
                    break;
                }
            }

            return array(
                'success' => true,
                'message' => 'Device added successfully.',
                'node_id' => $resolvedNodeId,
                'node_id_plain' => $plainNodeId,
                'node_id_aliases' => array_values(array_unique(array_filter(array(
                    $resolvedNodeId,
                    $plainNodeId,
                    $plainNodeId !== '' ? ('socket_' . $plainNodeId) : '',
                    $rawNodeId,
                    $nodeId,
                )))),
                'visible' => $found,
                'devices' => $freshDevices,
                'total' => count($freshDevices),
                'status'  => 'success',
            );
        } catch (\Throwable $e) {
            log_message('error', 'IoT addWifiNodeToAccount failed: ' . $e->getMessage());
            return array('success' => false, 'message' => 'Could not add device: ' . $e->getMessage());
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findProductRowByNodeId(string $nodeId): ?array
    {
        $raw = trim($nodeId);
        // Strip accidental quotes/whitespace/newlines from QR payloads.
        $raw = trim($raw, " \t\n\r\0\x0B\"'");
        $normalized = $this->normalizeScannedNodeId($raw);
        if ($normalized === '' && $raw === '') {
            return null;
        }

        $plain = $normalized !== '' ? $normalized : preg_replace('/^socket_/i', '', $raw);
        $plain = trim((string) $plain);

        $candidates = array();
        foreach (array($raw, $normalized, $plain, 'socket_' . $plain, 'SOCKET_' . $plain, 'Socket_' . $plain) as $candidate) {
            $candidate = trim((string) $candidate);
            if ($candidate !== '') {
                $candidates[] = $candidate;
            }
        }
        $candidates = array_values(array_unique($candidates));

        // 1) Exact match (legacy v8 behavior).
        foreach ($candidates as $candidate) {
            $row = $this->db->table('efactorproduct')
                ->where('node_id', $candidate)
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();
            if (is_array($row) && $row !== array()) {
                return $row;
            }
        }

        // 2) Case-insensitive exact match.
        foreach ($candidates as $candidate) {
            try {
                $row = $this->db->query(
                    'SELECT * FROM efactorproduct WHERE LOWER(TRIM(node_id)) = LOWER(?) ORDER BY id DESC LIMIT 1',
                    array($candidate)
                )->getRowArray();
                if (is_array($row) && $row !== array()) {
                    return $row;
                }
            } catch (\Throwable $e) {
                // continue
            }
        }

        // 3) Match ignoring socket_ prefix differences.
        if ($plain !== '' && strlen($plain) >= 4) {
            try {
                $row = $this->db->query(
                    "SELECT * FROM efactorproduct
                     WHERE LOWER(TRIM(REPLACE(REPLACE(node_id, 'socket_', ''), 'SOCKET_', ''))) = LOWER(?)
                     ORDER BY id DESC LIMIT 1",
                    array($plain)
                )->getRowArray();
                if (is_array($row) && $row !== array()) {
                    return $row;
                }
            } catch (\Throwable $e) {
                // continue
            }

            // 4) Suffix match for long hardware ids vs short QR fragments.
            try {
                $row = $this->db->query(
                    'SELECT * FROM efactorproduct
                     WHERE node_id LIKE ?
                     ORDER BY id DESC LIMIT 1',
                    array('%' . $plain)
                )->getRowArray();
                if (is_array($row) && $row !== array()) {
                    return $row;
                }
            } catch (\Throwable $e) {
                // continue
            }
        }

        return null;
    }

    private function buildOnboardingDeviceName(string $nodeId, string $deviceType): string
    {
        if (strlen($nodeId) >= 30) {
            $segment9 = substr($nodeId, 16, 2);
            $segment13 = substr($nodeId, 24, 2);
            $segment14 = substr($nodeId, 26, 2);
            $segment15 = substr($nodeId, 28, 2);

            return $deviceType . '_' . $segment9 . $segment13 . $segment14 . $segment15;
        }

        return $deviceType;
    }

    /**
     * @param array<string, mixed> $fields
     */
    private function updateModelTableRow(string $modelNumber, string $nodeId, array $fields): void
    {
        if (!preg_match('/^[A-Za-z0-9 _.-]+$/', $modelNumber)) {
            return;
        }

        try {
            if (!$this->db->tableExists($modelNumber)) {
                return;
            }
            $this->db->table($modelNumber)->where('node_id', $nodeId)->update($fields);
        } catch (\Throwable $e) {
            log_message('error', 'IoT updateModelTableRow failed: ' . $e->getMessage());
        }
    }

    private function isJwtExpired(string $token): bool
    {
        $parts = explode('.', $token);
        if (count($parts) < 2) {
            return false;
        }

        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        if (!is_array($payload) || empty($payload['exp'])) {
            return false;
        }

        return (int) $payload['exp'] < time();
    }

    /**
     * Toggle device power using the IoT API with a server-resolved session token.
     *
     * @param array<string, mixed> $body
     * @return array<string, mixed>
     */
    public function toggleDevicePower(int $userId, array $body, bool $isAdmin = false): array
    {
        $rawNodeId = trim((string) ($body['node_id'] ?? ''));
        $nodeId = $this->normalizeScannedNodeId($rawNodeId);
        if ($nodeId === '' && $rawNodeId === '') {
            return array('success' => false, 'message' => 'node_id is required.');
        }
        if ($nodeId === '') {
            $nodeId = $rawNodeId;
        }

        $devices = $this->getDevicesForUser($userId, $isAdmin);
        $token = $this->resolveDeviceBindingToken($userId, $isAdmin, $devices);
        if ($token === '') {
            $token = $this->fetchAccessTokenFromUsersRaw($userId);
        }
        if ($token === '') {
            return array('success' => false, 'message' => 'No access token on profile. Sign in again.');
        }

        $row = $this->findProductRowByNodeId($rawNodeId !== '' ? $rawNodeId : $nodeId);
        $resolvedNodeId = is_array($row) ? trim((string) ($row['node_id'] ?? $nodeId)) : $nodeId;

        $payload = array('node_id' => $resolvedNodeId !== '' ? $resolvedNodeId : $nodeId);
        if (array_key_exists('power', $body)) {
            $payload['power'] = $body['power'];
        }
        if (isset($body['command']) && trim((string) $body['command']) !== '') {
            $payload['command'] = (string) $body['command'];
        } elseif (!array_key_exists('power', $body)) {
            $payload['command'] = 'toggle_power';
        }

        $url = rtrim($this->getPublicApiBaseUrl(), '/')
            . '?action=device_power_toggle';
        // Token goes in Authorization header only — never in the URL.
        $result = $this->httpPostJson($url, $payload, $token);

        if (!isset($result['success'])) {
            if (isset($result['power']) || (($result['status'] ?? '') === 'success')) {
                $result['success'] = true;
            }
        }

        if (empty($result['message']) && !empty($result['description'])) {
            $result['message'] = (string) $result['description'];
        }

        // Ensure response always reflects power (not connectivity).
        if (!empty($result['success']) && !array_key_exists('power', $result) && array_key_exists('power', $payload)) {
            $result['power'] = $payload['power'];
        }
        if (!isset($result['node_id']) || trim((string) $result['node_id']) === '') {
            $result['node_id'] = $resolvedNodeId;
        }

        return $result;
    }

    /**
     * @param array<int, array<string, mixed>> $devices
     */
    private function pickAccessTokenFromDevices(array $devices, bool $skipExpired = true): string
    {
        $counts = array();
        foreach ($devices as $device) {
            if (!is_array($device)) {
                continue;
            }
            $token = trim((string) ($device['access_token'] ?? ''));
            if ($token === '' || strtoupper($token) === 'NULL' || $this->isPlaceholderToken($token)) {
                continue;
            }
            if ($skipExpired && $this->isJwtExpired($token)) {
                continue;
            }
            $counts[$token] = ($counts[$token] ?? 0) + 1;
        }

        if ($counts === array()) {
            return '';
        }

        arsort($counts);

        return (string) array_key_first($counts);
    }

    /**
     * Read the newest non-empty access_token row from efactorproduct.
     */
    private function fetchAccessTokenFromEfactorproduct(int $userId, bool $isAdmin = false): string
    {
        try {
            $sql = "
                SELECT access_token
                FROM efactorproduct
                WHERE access_token IS NOT NULL
                  AND TRIM(access_token) != ''
                  AND UPPER(TRIM(access_token)) NOT IN ('NULL', 'UNDEFINED')
            ";
            $params = array();
            if (!$isAdmin) {
                $sql .= ' AND user_id = ?';
                $params[] = $userId;
            }
            $sql .= ' ORDER BY id DESC';

            $rows = $params === array()
                ? $this->db->query($sql)->getResultArray()
                : $this->db->query($sql, $params)->getResultArray();

            foreach ($rows as $row) {
                $token = trim((string) ($row['access_token'] ?? ''));
                if ($token !== '' && !$this->isPlaceholderToken($token) && !$this->isJwtExpired($token)) {
                    return $token;
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'IoT fetchAccessTokenFromEfactorproduct: ' . $e->getMessage());
        }

        return '';
    }

    /**
     * Match efactorproduct rows by profile phone when user_id rows have no token.
     */
    private function fetchAccessTokenViaUserPhone(int $userId): string
    {
        if ($userId <= 0) {
            return '';
        }

        try {
            $phoneMatch = '';
            if ($this->db->fieldExists('phone', 'users') && $this->db->fieldExists('phone', 'efactorproduct')) {
                $phoneMatch = " OR (u.phone IS NOT NULL AND u.phone != '' AND p.phone = u.phone)";
            }

            $rows = $this->db->query("
                SELECT p.access_token
                FROM efactorproduct p
                INNER JOIN users u ON u.id = ?
                WHERE p.access_token IS NOT NULL
                  AND TRIM(p.access_token) != ''
                  AND UPPER(TRIM(p.access_token)) NOT IN ('NULL', 'UNDEFINED')
                  AND (
                        p.user_id = u.id
                        $phoneMatch
                  )
                ORDER BY p.id DESC
            ", array($userId))->getResultArray();

            foreach ($rows as $row) {
                $token = trim((string) ($row['access_token'] ?? ''));
                if ($token !== '' && !$this->isPlaceholderToken($token) && !$this->isJwtExpired($token)) {
                    return $token;
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'IoT fetchAccessTokenViaUserPhone: ' . $e->getMessage());
        }

        return '';
    }

    /**
     * Last saved dashboard schema token for this profile.
     */
    private function fetchAccessTokenFromSchemaTable(int $userId): string
    {
        if ($userId <= 0) {
            return '';
        }

        try {
            if (!$this->dbColumnExists('iot_dashboard_schemas', 'access_token')) {
                return '';
            }

            $row = $this->db->table('iot_dashboard_schemas')
                ->select('access_token')
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();

            $token = trim((string) ($row['access_token'] ?? ''));
            if ($token !== '' && !$this->isPlaceholderToken($token)) {
                return $token;
            }
        } catch (\Throwable $e) {
            log_message('error', 'IoT fetchAccessTokenFromSchemaTable: ' . $e->getMessage());
        }

        return '';
    }

    /**
     * Public v8 device API (check_device, add_wifi_node).
     */
    public function getV8DeviceApiBaseUrl(): string
    {
        $envUrl = trim((string) (getenv('V8_DEVICE_API_URL') ?: ''));
        if ($envUrl !== '') {
            return rtrim($envUrl, '/');
        }

        $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
        // check_device, add_wifi_node, remove_node are handled by iot_api_creation.php.
        if ($docRoot !== '' && is_file($docRoot . '/v8/iot_api_creation.php')) {
            return rtrim(base_url('v8/iot_api_creation.php'), '/');
        }
        if ($docRoot !== '' && is_file($docRoot . '/v8/api.php')) {
            return rtrim(base_url('v8/api.php'), '/');
        }
        if ($docRoot !== '' && is_file($docRoot . '/api.php')) {
            return rtrim(base_url('api.php'), '/');
        }

        return 'https://efactor.tech/v8/iot_api_creation.php';
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function proxyV8DeviceApi(string $action, array $payload = array(), string $accessToken = '', string $nodeId = ''): array
    {
        $baseUrl = rtrim($this->getV8DeviceApiBaseUrl(), '/');
        $accessToken = trim($accessToken);

        if ($action === 'check_device') {
            // Token goes in Authorization header only — never in the URL.
            $url = $baseUrl . '?action=check_device';
            if ($nodeId !== '' && empty($payload['node_id'])) {
                $payload['node_id'] = $nodeId;
            }
            // Keep node_id, latitude, longitude in the body; strip any stale access_token from body.
            unset($payload['access_token']);

            return $this->httpPostJson($url, $payload, $accessToken);
        }

        if ($action === 'add_wifi_node') {
            $nodeId = trim($nodeId !== '' ? $nodeId : (string) ($payload['node_id'] ?? ''));
            if ($nodeId === '') {
                return array('success' => false, 'message' => 'node_id is required.');
            }

            // node_id stays as a query param per API contract; token goes in Authorization header only.
            $url = $baseUrl . '?action=add_wifi_node&node_id=' . rawurlencode($nodeId);
            $payload['node_id'] = $nodeId;
            // Strip any stale access_token, latitude, longitude from URL — they stay in the body.
            unset($payload['access_token']);

            return $this->httpPostJson($url, $payload, $accessToken);
        }

        if ($action === 'remove_node') {
            $nodeId = trim($nodeId !== '' ? $nodeId : (string) ($payload['node_id'] ?? ''));
            if ($nodeId === '') {
                return array('success' => false, 'message' => 'node_id is required.');
            }

            return $this->removeDeviceNode($nodeId, $accessToken);
        }

        return array('success' => false, 'message' => 'Unknown v8 action.');
    }

    /**
     * Remove a device via v8/iot_api_creation.php?action=remove_node.
     *
     * @return array<string, mixed>
     */
    public function removeDeviceNode(string $nodeId, string $accessToken): array
    {
        $nodeId = trim($nodeId);
        $accessToken = trim($accessToken);
        if ($nodeId === '') {
            return array('success' => false, 'message' => 'node_id is required.');
        }
        if ($accessToken === '') {
            return array('success' => false, 'message' => 'access_token is required.');
        }

        $baseUrl = rtrim($this->getV8DeviceApiBaseUrl(), '/');
        // Token goes in Authorization header only — node_id, operation go in the JSON body.
        // access_token, node_id, operation must NOT appear in the URL.
        $url = $baseUrl . '?action=remove_node';

        $payload = array(
            'node_id' => $nodeId,
        );

        try {
            if (class_exists('\Config\Services')) {
                $client = \Config\Services::curlrequest();
                $response = $client->post($url, array(
                    'headers' => array(
                        'Accept'         => 'application/json',
                        'Content-Type'   => 'application/json',
                        'Authorization'  => 'Bearer ' . $accessToken,
                    ),
                    'body' => json_encode($payload),
                    'timeout' => 45,
                    'http_errors' => false,
                ));
                $status = (int) $response->getStatusCode();
                $raw = (string) $response->getBody();
                $body = json_decode($raw, true);
                if (is_array($body)) {
                    if (!isset($body['success'])) {
                        $body['success'] = ($status >= 200 && $status < 300)
                            && (!isset($body['status']) || strtolower((string) $body['status']) !== 'failure');
                    }
                    $body['node_id'] = $nodeId;
                    return $body;
                }
                if ($status >= 200 && $status < 300) {
                    return array(
                        'success' => true,
                        'message' => 'Device removed successfully.',
                        'node_id' => $nodeId,
                        'raw' => $raw,
                    );
                }

                return array(
                    'success' => false,
                    'message' => 'Remove device request failed (' . $status . ').',
                    'node_id' => $nodeId,
                    'raw' => $raw,
                );
            }
        } catch (\Throwable $e) {
            log_message('error', 'removeDeviceNode curl failed: ' . $e->getMessage());
        }

        $fallback = $this->httpPostJson($url, $payload, $accessToken);
        if (!isset($fallback['success'])) {
            $fallback['success'] = !isset($fallback['status']) || strtolower((string) $fallback['status']) !== 'failure';
        }
        $fallback['node_id'] = $nodeId;
        return $fallback;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function httpPostJson(string $url, array $payload, string $accessToken = ''): array
    {
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
        );
        if ($accessToken !== '') {
            // Token goes in Authorization header only — never in X-IOT-Access-Token or access_token headers.
            $headers[] = 'Authorization: Bearer ' . $accessToken;
        }

        $context = stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'header'  => implode("\r\n", $headers) . "\r\n",
                'content' => $payload !== array() ? json_encode($payload) : '{}',
                'timeout' => 30,
                'ignore_errors' => true,
            ),
            'ssl' => array(
                'verify_peer'      => true,
                'verify_peer_name' => true,
            ),
        ));

        $raw = @file_get_contents($url, false, $context);
        if ($raw === false || $raw === '') {
            return array('success' => false, 'message' => 'Could not reach device API.', 'url' => $url);
        }

        $body = json_decode($raw, true);
        if (!is_array($body)) {
            return array('success' => false, 'message' => 'Invalid device API response.', 'raw' => $raw, 'url' => $url);
        }

        return $body;
    }

    /**
     * Resolve public IoT API script URL (root or /v8/ on different servers).
     */
    public function getPublicApiBaseUrl(): string
    {
        $envUrl = trim((string) (getenv('IOT_API_URL') ?: ''));
        if ($envUrl !== '') {
            return $this->normalizeIotApiEndpoint($envUrl);
        }

        $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
        if ($docRoot !== '' && is_file($docRoot . '/iot_api_creation.php')) {
            return $this->normalizeIotApiEndpoint(base_url('iot_api_creation.php'));
        }
        if ($docRoot !== '' && is_file($docRoot . '/v8/iot_api_creation.php')) {
            return $this->normalizeIotApiEndpoint(base_url('v8/iot_api_creation.php'));
        }

        return 'https://efactor.tech/v8/iot_api_creation.php';
    }

    /**
     * Prevent .../api_creation.php/v8/iot_api_creation.php when App.baseURL is mis-set.
     */
    private function normalizeIotApiEndpoint(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return 'https://efactor.tech/v8/iot_api_creation.php';
        }

        $url = preg_replace(
            '#/(?:v8/)?api_creation\.php/+(?:v8/)?iot_api_creation\.php#i',
            '/v8/iot_api_creation.php',
            $url
        ) ?? $url;
        $url = preg_replace(
            '#/(?:v8/)?iot_api_creation\.php/+(?:v8/)?iot_api_creation\.php#i',
            '/v8/iot_api_creation.php',
            $url
        ) ?? $url;

        // If env pointed at schema API by mistake, rewrite to IoT script.
        if (preg_match('#api_creation\.php#i', $url) && !preg_match('#iot_api_creation\.php#i', $url)) {
            $url = preg_replace('#api_creation\.php#i', 'iot_api_creation.php', $url) ?? $url;
        }

        $parts = parse_url($url);
        if (!empty($parts['scheme']) && !empty($parts['host'])) {
            $path = (string) ($parts['path'] ?? '');
            if ($path === '' || stripos($path, 'iot_api_creation.php') === false) {
                $path = '/v8/iot_api_creation.php';
            }
            $port = isset($parts['port']) ? (':' . (int) $parts['port']) : '';

            return rtrim($parts['scheme'] . '://' . $parts['host'] . $port . $path, '/');
        }

        return rtrim($url, '/');
    }

    /**
     * Human-readable token source for dashboard UI.
     */
    public function describeAccessTokenSource(int $userId, bool $isAdmin = false, array $devices = array(), string $token = ''): string
    {
        if ($token === '') {
            return '';
        }
        if ($this->pickAccessTokenFromDevices($devices) === $token) {
            return 'efactorproduct';
        }
        if ($this->fetchAccessTokenFromEfactorproduct($userId, $isAdmin) === $token) {
            return 'efactorproduct';
        }
        if ($this->fetchAccessTokenViaUserPhone($userId) === $token) {
            return 'efactorproduct.phone';
        }
        if ($this->fetchAccessTokenFromUsers($userId) === $token) {
            return 'users.access_token';
        }
        if ($this->fetchAccessTokenFromSchemaTable($userId) === $token) {
            return 'iot_dashboard_schemas';
        }

        return 'unknown';
    }

    /**
     * Fetch saved IoT SDUI schema from the public IoT API for the logged-in user.
     */
    public function fetchRemoteIotSchema(int $userId): array
    {
        // Always ensure a local default schema exists for new users (0 devices OK).
        // This removes the need to visit IoT Dashboard and click "Skema Generate".
        $ensured = $this->ensureSchemaForUser($userId, false);
        $token = (string) ($ensured['access_token'] ?? '');
        $localSchema = is_array($ensured['schema'] ?? null) ? $ensured['schema'] : array();
        $baseUrl = rtrim($this->getPublicApiBaseUrl(), '/');

        if ($token === '') {
            if ($localSchema !== array()) {
                return array(
                    'success' => true,
                    'schema' => $localSchema,
                    'schema_version' => (string) ($localSchema['schemaVersion'] ?? '2.3.2'),
                    'source' => 'local',
                    'created' => !empty($ensured['created']),
                    'message' => 'Serving local IoT schema (no access token yet).',
                    'iotApiUrl' => $baseUrl,
                );
            }

            return array(
                'success' => false,
                'message' => 'No access token on this profile and no IoT schema could be created.',
                'iotApiUrl' => $baseUrl,
            );
        }

        // Prefer remote get_iot_schema (also auto-creates/saves by token) so Flutter token lookup matches.
        $url = $baseUrl . '?action=get_iot_schema';
        $body = $this->httpGetJson($url, $token);
        if (is_array($body) && !empty($body['success']) && !empty($body['schema']) && is_array($body['schema'])) {
            $body['iotApiUrl'] = $baseUrl;
            $body['source'] = 'remote';
            $body['created'] = !empty($ensured['created']) || !empty($body['regenerated']);
            return $body;
        }

        if ($localSchema !== array()) {
            return array(
                'success' => true,
                'schema' => $localSchema,
                'schema_version' => (string) ($localSchema['schemaVersion'] ?? '2.3.2'),
                'source' => 'local',
                'created' => !empty($ensured['created']),
                'iotApiUrl' => $baseUrl,
                'message' => is_array($body) ? (string) ($body['message'] ?? 'Remote schema unavailable; serving local.') : 'Remote schema unavailable; serving local.',
            );
        }

        if ($body === null) {
            return array(
                'success' => false,
                'message' => 'Could not reach IoT API at ' . $baseUrl,
                'iotApiUrl' => $baseUrl,
            );
        }

        $body['iotApiUrl'] = $baseUrl;
        return $body;
    }

    /**
     * Fetch IoT SDUI schema for App Home Page studio — always includes hydrated devices.
     */
    public function fetchRemoteIotSchemaForStudio(int $userId): array
    {
        $payload = $this->fetchRemoteIotSchema($userId);
        if (empty($payload['success']) || empty($payload['schema']) || !is_array($payload['schema'])) {
            return $payload;
        }

        $schema = $payload['schema'];
        $devices = (isset($schema['devices']) && is_array($schema['devices'])) ? $schema['devices'] : array();
        $token = $this->resolveDeviceBindingToken($userId, false, $devices);
        $localDevices = $this->getDevicesWithLiveStatus($userId, false, $token);
        if ($localDevices !== array()) {
            $devices = $localDevices;
            $schema['devices'] = $localDevices;
        } elseif ($devices === array()) {
            $devicesPayload = $this->fetchRemoteIotDevices($userId);
            if (!empty($devicesPayload['devices']) && is_array($devicesPayload['devices'])) {
                $devices = $devicesPayload['devices'];
                $schema['devices'] = $devices;
            }
        }

        $total = count($devices);
        $online = 0;
        foreach ($devices as $device) {
            if (!empty($device['online'])) {
                $online++;
            }
        }
        $offline = max(0, $total - $online);

        if (!isset($schema['page']) || !is_array($schema['page'])) {
            $schema['page'] = array();
        }
        if (!isset($schema['page']['summary']) || !is_array($schema['page']['summary'])) {
            $schema['page']['summary'] = array();
        }
        $schema['page']['summary']['total'] = $total;
        $schema['page']['summary']['online'] = $online;
        $schema['page']['summary']['offline'] = $offline;
        if (!isset($schema['page']['header']) || !is_array($schema['page']['header'])) {
            $schema['page']['header'] = array(
                'eyebrow' => 'Smart Home',
                'title' => 'My Devices',
            );
        }
        $schema['page']['header']['subtitle'] = $total . ' products · ' . $online . ' online';
        $schema['deviceCount'] = $total;
        $schema['metrics'] = array(
            'total' => $total,
            'online' => $online,
            'offline' => $offline,
        );

        $payload['schema'] = $schema;
        $payload['deviceCount'] = $total;
        $payload['access_token_hint'] = 'dynamic';

        return $payload;
    }

    /**
     * Fetch live IoT devices from the public IoT API for the logged-in user.
     */
    public function fetchRemoteIotDevices(int $userId): array
    {
        $devices = $this->getDevicesForUser($userId, false);
        $token = $this->resolveDeviceBindingToken($userId, false, $devices);
        $local = $this->getDevicesWithLiveStatus($userId, false, $token);
        $baseUrl = rtrim($this->getPublicApiBaseUrl(), '/');
        $total = count($local);
        $online = 0;
        $nodeIds = array();
        foreach ($local as $device) {
            if (!empty($device['online'])) {
                $online++;
            }
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
        }

        return array(
            'success' => true,
            'user_id' => $userId,
            'total'   => $total,
            'online'  => $online,
            'offline' => max(0, $total - $online),
            'node_ids'=> array_values(array_unique($nodeIds)),
            'devices' => $local,
            'source'  => 'local',
            'iotApiUrl' => $baseUrl,
        );
    }

    /**
     * GET JSON from remote IoT API (CURLRequest with stream fallback).
     */
    private function httpGetJson(string $url, string $token): ?array
    {
        try {
            if (class_exists('\Config\Services')) {
                $client = \Config\Services::curlrequest();
                $response = $client->get($url, array(
                    'headers' => array(
                        'Accept'        => 'application/json',
                        // Token goes in Authorization header only — consistent with httpPostJson.
                        'Authorization' => 'Bearer ' . $token,
                    ),
                    'timeout' => 45,
                    'http_errors' => false,
                ));
                $body = json_decode((string) $response->getBody(), true);
                if (is_array($body)) {
                    return $body;
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'IoT curl fetch failed: ' . $e->getMessage());
        }

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'timeout' => 45,
                'header' => "Accept: application/json\r\nAuthorization: Bearer " . $token . "\r\n",
            ),
            'ssl' => array(
                'verify_peer' => true,
                'verify_peer_name' => true,
            ),
        ));

        $raw = @file_get_contents($url, false, $context);
        if ($raw === false || $raw === '') {
            return null;
        }

        $body = json_decode($raw, true);
        return is_array($body) ? $body : null;
    }

    private function isPlaceholderToken(string $token): bool
    {
        $value = strtolower(trim($token));
        $blocked = array(
            'dummy-token', 'dummy', 'test', 'default', 'token', 'sample',
            'your-access-token', 'access_token', 'null', 'undefined',
        );

        return $value === '' || in_array($value, $blocked, true);
    }

    private function dbColumnExists(string $table, string $column): bool
    {
        try {
            return in_array($column, $this->db->getFieldNames($table), true);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getGeneratedSchema(int $userId): array
    {
        $row = $this->db->table('iot_dashboard_schemas')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$row || empty($row['schema_json'])) {
            return array();
        }

        $schema = json_decode((string) $row['schema_json'], true);
        if (!is_array($schema)) {
            return array();
        }

        unset($schema['all_nodes']);

        return $schema;
    }

    /**
     * Latest IoT UI shell from any account. Live devices are applied by the caller.
     */
    private function getSharedIotSchemaTemplate(): array
    {
        try {
            $row = $this->db->table('iot_dashboard_schemas')
                ->select('schema_json')
                ->where('schema_json !=', '')
                ->orderBy('updated_at', 'DESC')
                ->get()
                ->getRowArray();
        } catch (\Throwable $e) {
            return array();
        }

        if (!$row || empty($row['schema_json'])) {
            return array();
        }

        $schema = json_decode((string) $row['schema_json'], true);
        if (!is_array($schema)) {
            return array();
        }

        unset($schema['all_nodes']);

        return $schema;
    }

    public function getSchemaWithLiveMetrics(int $userId, bool $isAdmin = false): array
    {
        $schema = $this->getGeneratedSchema($userId);
        $devices = $this->getDevicesForUser($userId, $isAdmin);
        $accessToken = $this->getSavedAccessToken($userId, $isAdmin, $devices);
        $devices = $this->getDevicesWithLiveStatus($userId, $isAdmin, $accessToken);
        $sourceFp = $this->devicesSourceFingerprint($devices, $accessToken);
        $storedFp = $this->readSchemaSourceFingerprint($schema);
        $fingerprintStale = ($sourceFp !== '' && ($storedFp === '' || $storedFp !== $sourceFp));

        if ($schema === array() || $fingerprintStale || empty($schema['dataSources'])) {
            try {
                $generated = $this->generateAndPersistSchema($userId, $isAdmin, $accessToken);
                $schema = is_array($generated['schema'] ?? null) ? $generated['schema'] : $schema;
            } catch (\Throwable $e) {
                log_message('error', 'IoT auto schema sync failed: ' . $e->getMessage());
                if ($schema === array()) {
                    return array();
                }
            }
        }

        if ($schema === array()) {
            return array();
        }

        $allNodes = array();
        $mysqli = $this->getMysqliConnection();
        if ($mysqli instanceof \mysqli) {
            try {
                $nodesPayload = (new \App\Libraries\IotNodesStatusBuilder())->build($mysqli, $accessToken, $userId);
                $allNodes = is_array($nodesPayload['all_nodes'] ?? null) ? $nodesPayload['all_nodes'] : array();
            } catch (\Throwable $e) {
                log_message('error', 'IoT getAllNodesStatus failed: ' . $e->getMessage());
            }
        }

        $builder = new \App\Libraries\IotSduiSchemaBuilder();
        $template = $builder->build(
            $devices,
            $userId,
            $accessToken,
            $this->getPublicApiBaseUrl(),
            site_url('iot-dashboard/api')
        );
        $schema = $builder->ensureCompleteSchema($schema, $template);
        $schema = $builder->applyLiveMetrics($schema, $devices, $this->getPublicApiBaseUrl(), $allNodes, false);

        return $builder->stripHeavyPayload($schema, true, false);
    }

    /**
     * @param array<int, array<string, mixed>> $devices
     */
    private function devicesSourceFingerprint(array $devices, string $accessToken = ''): string
    {
        unset($accessToken);
        try {
            return (new \App\Libraries\IotSduiSchemaBuilder())->sourceFingerprint($devices);
        } catch (\Throwable $e) {
            log_message('error', 'IoT source fingerprint failed: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * @param array<string, mixed> $schema
     */
    private function readSchemaSourceFingerprint(array $schema): string
    {
        if (!empty($schema['sourceFingerprint']) && is_string($schema['sourceFingerprint'])) {
            return trim($schema['sourceFingerprint']);
        }
        if (!empty($schema['meta']['sourceFingerprint']) && is_string($schema['meta']['sourceFingerprint'])) {
            return trim((string) $schema['meta']['sourceFingerprint']);
        }

        return '';
    }

    /**
     * @param array<string, mixed> $schema
     * @param array<int, array<string, mixed>> $devices
     * @return array<string, mixed>
     */
    private function stampSchemaSourceFingerprint(array $schema, array $devices, string $accessToken = ''): array
    {
        $fingerprint = $this->devicesSourceFingerprint($devices, $accessToken);
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

    /**
     * @return array<string, mixed>
     */
    public function getAllNodesStatus(int $userId, string $accessToken = '', bool $isAdmin = false): array
    {
        $mysqli = $this->getMysqliConnection();
        if (!$mysqli instanceof \mysqli) {
            return array(
                'success' => false,
                'message' => 'Database connection unavailable.',
                'url'     => 'action=getAllNodesStatus',
                'token'   => $accessToken,
                'all_nodes' => array(),
            );
        }

        if ($accessToken === '') {
            $devices = $this->getDevicesForUser($userId, $isAdmin);
            $accessToken = $this->getSavedAccessToken($userId, $isAdmin, $devices);
        }

        $payload = (new \App\Libraries\IotNodesStatusBuilder())->build($mysqli, $accessToken, $userId);
        $payload['success'] = true;

        return $payload;
    }

    private function getMysqliConnection(): ?\mysqli
    {
        if (isset($this->db->connID) && $this->db->connID instanceof \mysqli) {
            return $this->db->connID;
        }

        return null;
    }

    public function countDeviceStats(array $devices): array
    {
        return (new \App\Libraries\IotSduiSchemaBuilder())->countDeviceStats($devices);
    }

    /**
     * Retrieve a schema by the raw access token (SHA-256 matched).
     * Used by the public token-based endpoint.
     */
    public function getGeneratedSchemaByToken(string $accessToken): array
    {
        $hash = hash('sha256', $accessToken);

        $row = $this->db->table('iot_dashboard_schemas')
            ->where('access_token_hash', $hash)
            ->get()
            ->getRowArray();

        if (!$row || empty($row['schema_json'])) {
            return array();
        }

        $schema = json_decode((string) $row['schema_json'], true);
        return is_array($schema) ? $schema : array();
    }

    public function getSchemaMetaByUserId(int $userId): array
    {
        $row = $this->db->table('iot_dashboard_schemas')
            ->select('schema_version, created_at, updated_at')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        return $row ?: array();
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    private function getDevicesByWhere(string $where, array $params): array
    {
        $sql = "
            SELECT
                p.id,
                p.user_id,
                p.node_id,
                p.device_type,
                p.vendor_type,
                p.vendor_name,
                p.name,
                p.internet,
                p.power,
                p.model_name,
                p.model_number,
                p.module_type,
                p.add_gateway,
                p.is_added,
                p.prod_category,
                p.category_id,
                p.vendor_id,
                p.access_token,
                p.brand_logo AS device_brand_logo,
                (
                    SELECT pc.name
                    FROM productcats pc
                    WHERE pc.user_id = p.user_id
                      AND (CAST(pc.id AS CHAR) = CAST(p.category_id AS CHAR)
                           OR LOWER(TRIM(pc.name)) = LOWER(TRIM(p.name))
                           OR LOWER(TRIM(pc.name)) = LOWER(TRIM(p.device_type))
                           OR LOWER(TRIM(pc.name)) = LOWER(TRIM(p.prod_category)))
                    ORDER BY
                        CASE WHEN CAST(pc.id AS CHAR) = CAST(p.category_id AS CHAR) THEN 1
                             WHEN LOWER(TRIM(pc.name)) = LOWER(TRIM(p.name))        THEN 2
                             WHEN LOWER(TRIM(pc.name)) = LOWER(TRIM(p.device_type)) THEN 3
                             ELSE 4 END,
                        pc.id DESC
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
                        SELECT COALESCE(NULLIF(pc.img_path,''), NULLIF(pc.product_image,''))
                        FROM productcats pc
                        WHERE pc.user_id = p.user_id
                          AND (CAST(pc.id AS CHAR) = CAST(p.category_id AS CHAR)
                               OR LOWER(TRIM(pc.name)) = LOWER(TRIM(p.name))
                               OR LOWER(TRIM(pc.name)) = LOWER(TRIM(p.device_type))
                               OR LOWER(TRIM(pc.name)) = LOWER(TRIM(p.prod_category)))
                        ORDER BY
                            CASE WHEN CAST(pc.id AS CHAR) = CAST(p.category_id AS CHAR) THEN 1
                                 WHEN LOWER(TRIM(pc.name)) = LOWER(TRIM(p.name))        THEN 2
                                 WHEN LOWER(TRIM(pc.name)) = LOWER(TRIM(p.device_type)) THEN 3
                                 ELSE 4 END,
                            pc.id DESC
                        LIMIT 1
                    )
                ) AS product_image,
                (
                    SELECT b.name
                    FROM brands b
                    WHERE b.user_id = p.user_id
                      AND (CAST(b.id AS CHAR) = CAST(p.vendor_id AS CHAR)
                           OR CAST(b.id AS CHAR) = CAST(p.vendor_type AS CHAR)
                           OR CAST(b.id AS CHAR) = CAST(p.vendor_name AS CHAR)
                           OR LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_name))
                           OR LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_type)))
                    ORDER BY
                        CASE WHEN CAST(b.id AS CHAR) = CAST(p.vendor_id AS CHAR)   THEN 1
                             WHEN LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_name)) THEN 2
                             WHEN CAST(b.id AS CHAR) = CAST(p.vendor_type AS CHAR) THEN 3
                             ELSE 4 END,
                        b.id DESC
                    LIMIT 1
                ) AS brand_name,
                COALESCE(NULLIF(p.brand_logo,''), (
                    SELECT COALESCE(NULLIF(b.img_path,''), NULLIF(b.brandlogo,''))
                    FROM brands b
                    WHERE b.user_id = p.user_id
                      AND (CAST(b.id AS CHAR) = CAST(p.vendor_id AS CHAR)
                           OR CAST(b.id AS CHAR) = CAST(p.vendor_type AS CHAR)
                           OR CAST(b.id AS CHAR) = CAST(p.vendor_name AS CHAR)
                           OR LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_name))
                           OR LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_type)))
                    ORDER BY
                        CASE WHEN CAST(b.id AS CHAR) = CAST(p.vendor_id AS CHAR)   THEN 1
                             WHEN LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_name)) THEN 2
                             WHEN CAST(b.id AS CHAR) = CAST(p.vendor_type AS CHAR) THEN 3
                             ELSE 4 END,
                        b.id DESC
                    LIMIT 1
                ), (
                    SELECT COALESCE(NULLIF(b.img_path,''), NULLIF(b.brandlogo,''))
                    FROM brands b
                    WHERE COALESCE(NULLIF(b.img_path,''), NULLIF(b.brandlogo,'')) IS NOT NULL
                      AND (LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_name))
                           OR LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_type))
                           OR CAST(b.id AS CHAR) = CAST(p.vendor_id AS CHAR))
                    ORDER BY
                        CASE WHEN LOWER(TRIM(b.name)) = LOWER(TRIM(p.vendor_name)) THEN 1
                             WHEN CAST(b.id AS CHAR) = CAST(p.vendor_id AS CHAR) THEN 2
                             ELSE 3 END,
                        b.id DESC
                    LIMIT 1
                )) AS brand_logo
            FROM efactorproduct p
            $where
            ORDER BY p.id DESC
        ";

        try {
            $rows = $this->uniqueRows($this->db->query($sql, $params)->getResultArray());
        } catch (\Throwable $e) {
            log_message('error', 'IotDashboardModel getDevicesByWhere failed: ' . $e->getMessage());
            return array();
        }
        $devices = array_map(function (array $row): array {
            return $this->normalizeDevice($row);
        }, $rows);

        return $this->sortDevicesOnlineFirst($devices);
    }

    /**
     * Online devices first, then offline — stable within each group.
     */
    public function sortDevicesOnlineFirst(array $devices): array
    {
        usort($devices, function (array $a, array $b): int {
            $aOnline = !empty($a['online']) ? 1 : 0;
            $bOnline = !empty($b['online']) ? 1 : 0;
            if ($aOnline !== $bOnline) {
                return $bOnline <=> $aOnline;
            }

            $aName = strtolower((string) ($a['product_name'] ?? $a['productName'] ?? $a['name'] ?? ''));
            $bName = strtolower((string) ($b['product_name'] ?? $b['productName'] ?? $b['name'] ?? ''));
            return $aName <=> $bName;
        });

        return $devices;
    }

    private function normalizeDevice(array $row): array
    {
        return \App\Libraries\IotDeviceNormalizer::fromRow($row, $this->getPublicApiBaseUrl());
    }

    private function uniqueRows(array $rows): array
    {
        $seen   = array();
        $unique = array();
        foreach ($rows as $row) {
            // Always key by product row id so every efactorproduct record appears in preview.
            $key = (string) ($row['id'] ?? ('row-' . count($unique)));
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[]   = $row;
        }
        return $unique;
    }

    private function cleanText($value): string
    {
        $value = html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8');
        $value = strip_tags($value);
        $value = str_replace(array(chr(34) . '>', chr(39) . '>', '>'), ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);
        return trim((string) $value);
    }

    private function assetUrl(?string $path): string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        if (strpos($path, '/') !== false) {
            return base_url(ltrim($path, '/'));
        }
        return base_url('files/brand_logo/' . rawurlencode($path));
    }

    private function isOnline($value): bool
    {
        $value = strtolower(trim((string) $value));
        return in_array($value, array('1', 'true', 'yes', 'online', 'connected'), true);
    }

    private function isPowerOn($value): bool
    {
        $value = strtolower(trim((string) $value));
        return in_array($value, array('1', '01', 'true', 'yes', 'on'), true);
    }

    // -------------------------------------------------------------------------
    // DB schema bootstrap — runs on every instantiation (cheap IF NOT EXISTS)
    // -------------------------------------------------------------------------

    private function ensureSchema(): void
    {
        // Main schema table — keyed by user_id, also indexed by token hash
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `iot_dashboard_schemas` (
                `id`                BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
                `user_id`           INT UNSIGNED     NOT NULL,
                `access_token`      VARCHAR(1024)    NOT NULL DEFAULT '',
                `access_token_hash` CHAR(64)         NOT NULL DEFAULT '',
                `schema_json`       LONGTEXT         NOT NULL,
                `schema_version`    VARCHAR(32)      NOT NULL DEFAULT '2.2.0',
                `created_at`        DATETIME         NULL,
                `updated_at`        DATETIME         NULL,
                PRIMARY KEY (`id`),
                UNIQUE  KEY `uq_user_id`    (`user_id`),
                INDEX   `idx_token_hash`    (`access_token_hash`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // Add access_token_hash column to existing installations that don't have it yet
        $this->ensureColumn(
            'iot_dashboard_schemas',
            'access_token_hash',
            "ALTER TABLE `iot_dashboard_schemas`
             ADD COLUMN `access_token_hash` CHAR(64) NOT NULL DEFAULT '' AFTER `user_id`,
             ADD INDEX  `idx_token_hash` (`access_token_hash`)"
        );

        $this->ensureColumn(
            'iot_dashboard_schemas',
            'access_token',
            "ALTER TABLE `iot_dashboard_schemas`
             ADD COLUMN `access_token` VARCHAR(1024) NOT NULL DEFAULT '' AFTER `user_id`"
        );

        $this->ensureAccessTokenColumnWidth('iot_dashboard_schemas', 'access_token', 1024);

        // Standalone v8 table (kept for backward compat with iot_api_creation.php)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `iot_dashboard_token_schemas` (
                `id`                BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
                `access_token_hash` CHAR(64)         NOT NULL,
                `schema_json`       LONGTEXT         NOT NULL,
                `schema_version`    VARCHAR(32)      NOT NULL DEFAULT '2.0.0',
                `created_at`        DATETIME         NULL,
                `updated_at`        DATETIME         NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `access_token_hash` (`access_token_hash`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `productcats` (
                `id` int NOT NULL AUTO_INCREMENT,
                `user_id` int NOT NULL DEFAULT 0,
                `name` varchar(255) DEFAULT NULL,
                `product_image` varchar(500) DEFAULT NULL,
                `img_path` varchar(500) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `brands` (
                `id` int NOT NULL AUTO_INCREMENT,
                `user_id` int NOT NULL DEFAULT 0,
                `name` varchar(255) DEFAULT NULL,
                `brandlogo` varchar(500) DEFAULT NULL,
                `img_path` varchar(500) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");

        // Extra columns on related tables
        $this->ensureColumn('productcats',    'img_path',    "ALTER TABLE `productcats`    ADD COLUMN `img_path`    VARCHAR(500) NULL AFTER `product_image`");
        $this->ensureColumn('brands',         'img_path',    "ALTER TABLE `brands`         ADD COLUMN `img_path`    VARCHAR(500) NULL AFTER `brandlogo`");
        $this->ensureColumn('efactorproduct', 'vendor_name', "ALTER TABLE `efactorproduct` ADD COLUMN `vendor_name` VARCHAR(255) NULL");
        $this->ensureColumn('efactorproduct', 'vendor_type', "ALTER TABLE `efactorproduct` ADD COLUMN `vendor_type` VARCHAR(255) NULL AFTER `vendor_name`");
        $this->ensureColumn('efactorproduct', 'access_token',"ALTER TABLE `efactorproduct` ADD COLUMN `access_token` VARCHAR(255) NULL AFTER `vendor_type`");
        $this->ensureColumn('efactorproduct', 'node_id', "ALTER TABLE `efactorproduct` ADD COLUMN `node_id` VARCHAR(255) NULL");
        $this->ensureColumn('efactorproduct', 'device_type', "ALTER TABLE `efactorproduct` ADD COLUMN `device_type` VARCHAR(255) NULL");
        $this->ensureColumn('efactorproduct', 'name', "ALTER TABLE `efactorproduct` ADD COLUMN `name` VARCHAR(255) NULL");
        $this->ensureColumn('efactorproduct', 'power', "ALTER TABLE `efactorproduct` ADD COLUMN `power` VARCHAR(50) NULL");
        $this->ensureColumn('efactorproduct', 'model_name', "ALTER TABLE `efactorproduct` ADD COLUMN `model_name` VARCHAR(255) NULL");
        $this->ensureColumn('efactorproduct', 'model_number', "ALTER TABLE `efactorproduct` ADD COLUMN `model_number` VARCHAR(255) NULL");
        $this->ensureColumn('efactorproduct', 'add_gateway', "ALTER TABLE `efactorproduct` ADD COLUMN `add_gateway` VARCHAR(50) NULL");
        $this->ensureColumn('efactorproduct', 'is_added', "ALTER TABLE `efactorproduct` ADD COLUMN `is_added` VARCHAR(50) NULL");
        $this->ensureColumn('efactorproduct', 'prod_category', "ALTER TABLE `efactorproduct` ADD COLUMN `prod_category` VARCHAR(255) NULL");
        $this->ensureColumn('efactorproduct', 'vendor_id', "ALTER TABLE `efactorproduct` ADD COLUMN `vendor_id` VARCHAR(255) NULL");
        $this->ensureColumn('efactorproduct', 'brand_logo', "ALTER TABLE `efactorproduct` ADD COLUMN `brand_logo` VARCHAR(500) NULL");
        $this->ensureColumn('efactorproduct', 'phone', "ALTER TABLE `efactorproduct` ADD COLUMN `phone` VARCHAR(50) NULL");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `vendorproducts` (
                `id` int NOT NULL AUTO_INCREMENT,
                `user_id` int NOT NULL DEFAULT 0,
                `brand` varchar(255) DEFAULT NULL,
                `category` varchar(255) DEFAULT NULL,
                `product_type` varchar(255) DEFAULT NULL,
                `modelname` varchar(255) DEFAULT NULL,
                `modelno` varchar(255) DEFAULT NULL,
                `description` text,
                `model_version` varchar(100) DEFAULT NULL,
                `Batch` varchar(100) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");
    }

    private function ensureColumn(string $table, string $column, string $alterSql): void
    {
        try {
            $fields = $this->db->getFieldNames($table);
            if (!in_array($column, $fields, true)) {
                $this->db->query($alterSql);
            }
        } catch (\Throwable $e) {
            log_message('error', 'IotDashboardModel ensureColumn failed: ' . $e->getMessage());
        }
    }

    private function ensureAccessTokenColumnWidth(string $table, string $column, int $minLength): void
    {
        try {
            $row = $this->db->query("
                SELECT CHARACTER_MAXIMUM_LENGTH AS max_len
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?
                  AND COLUMN_NAME = ?
                LIMIT 1
            ", array($table, $column))->getRowArray();

            $current = (int) ($row['max_len'] ?? 0);
            if ($current > 0 && $current < $minLength) {
                $this->db->query("ALTER TABLE `{$table}` MODIFY COLUMN `{$column}` VARCHAR({$minLength}) NOT NULL DEFAULT ''");
            }
        } catch (\Throwable $e) {
            log_message('error', 'IotDashboardModel ensureAccessTokenColumnWidth failed: ' . $e->getMessage());
        }
    }
}
