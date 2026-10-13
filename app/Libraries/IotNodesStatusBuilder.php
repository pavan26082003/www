<?php

namespace App\Libraries;

/**
 * Builds getAllNodesStatus payloads for IoT device control apps.
 * Replaces legacy newapi.php logic with prepared statements and canonical shape.
 */
class IotNodesStatusBuilder
{
    private const DEFAULT_MODEL = 'EFDEFLT V1.0';

    /**
     * @return array{url:string,token:string,all_nodes:array<int,array<string,mixed>>,total:int,online:int,offline:int}
     */
    public function build(\mysqli $db, string $accessToken, int $userId = 0): array
    {
        $rows = $this->fetchProductRows($db, $accessToken, $userId);
        $allNodes = $this->buildAllNodes($db, $rows, $accessToken);

        $online = 0;
        foreach ($allNodes as $node) {
            if ($this->isConnected($node['status']['connectivity']['connected'] ?? 'false')) {
                $online++;
            }
        }
        $total = count($allNodes);

        return array(
            'url'       => 'action=getAllNodesStatus',
            'token'     => $accessToken,
            'all_nodes' => $allNodes,
            'total'     => $total,
            'online'    => $online,
            'offline'   => max(0, $total - $online),
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function buildAllNodes(\mysqli $db, array $rows, string $accessToken): array
    {
        if ($rows === array()) {
            return array();
        }

        $byNodeId = array();
        foreach ($rows as $row) {
            $nodeId = trim((string) ($row['node_id'] ?? ''));
            if ($nodeId === '') {
                continue;
            }
            $byNodeId[$nodeId] = $row;
        }

        $allNodes = array();
        $seenGatewayNodes = array();

        foreach ($byNodeId as $nodeId => $row) {
            $addGateway = $this->dbValue($row['add_gateway'] ?? '');
            $isAdded    = $this->dbValue($row['is_added'] ?? '');

            if ($addGateway !== '' && $isAdded !== '1') {
                continue;
            }

            if ($isAdded === '1') {
                if (isset($seenGatewayNodes[$nodeId])) {
                    continue;
                }
                $seenGatewayNodes[$nodeId] = true;
                $allNodes[] = $this->buildGatewayNode($db, $row, $accessToken);
                continue;
            }

            $allNodes[] = $this->buildStandaloneNode($db, $row);
        }

        return $this->dedupeNodes($allNodes);
    }

    /**
     * Merge live node connectivity/power into SDUI device list.
     *
     * @param array<int, array<string, mixed>> $devices
     * @param array<int, array<string, mixed>> $allNodes
     * @return array<int, array<string, mixed>>
     */
    public function mergeIntoDevices(array $devices, array $allNodes): array
    {
        if ($allNodes === array()) {
            return $devices;
        }

        $deviceState = array();
        $nodeOnline = array();

        foreach ($allNodes as $node) {
            if (!is_array($node)) {
                continue;
            }
            $nodeId = (string) ($node['node_id'] ?? '');
            $connected = $this->isConnected($node['status']['connectivity']['connected'] ?? 'false');
            if ($nodeId !== '') {
                $nodeOnline[$nodeId] = $connected;
            }

            foreach ($node['devices'] ?? array() as $subDevice) {
                if (!is_array($subDevice)) {
                    continue;
                }
                $deviceId = (string) ($subDevice['id'] ?? '');
                if ($deviceId === '') {
                    continue;
                }
                $deviceState[$deviceId] = array(
                    'power'  => IotDeviceNormalizer::isPowerOn($subDevice['power'] ?? null),
                    'name'   => $subDevice['name'] ?? null,
                    'online' => $connected,
                    'readings' => $subDevice,
                );
            }
        }

        foreach ($devices as $index => $device) {
            if (!is_array($device)) {
                continue;
            }
            $nodeId = (string) ($device['node_id'] ?? '');
            $productId = (string) ($device['product_id'] ?? $device['id'] ?? '');

            $state = null;
            if ($nodeId !== '' && isset($deviceState[$nodeId])) {
                $state = $deviceState[$nodeId];
            } elseif ($productId !== '' && isset($deviceState[$productId])) {
                $state = $deviceState[$productId];
            }

            if ($state === null && $nodeId !== '' && isset($nodeOnline[$nodeId])) {
                $devices[$index]['online'] = $nodeOnline[$nodeId];
                $devices[$index]['status'] = $nodeOnline[$nodeId] ? 'Online' : 'Offline';
                continue;
            }

            if ($state === null) {
                continue;
            }

            $devices[$index]['power'] = $state['power'];
            $devices[$index]['powerOn'] = $state['power'];
            $devices[$index]['online'] = $state['online'];
            $devices[$index]['status'] = $state['online'] ? 'Online' : 'Offline';
            if ($state['name'] !== null && $state['name'] !== '') {
                $devices[$index]['name'] = $state['name'];
                if (empty($devices[$index]['product_name'])) {
                    $devices[$index]['product_name'] = $state['name'];
                }
            }
            if (!empty($state['readings']) && is_array($state['readings'])) {
                foreach ($state['readings'] as $key => $value) {
                    if (in_array($key, array('id', 'power', 'name', 'online', 'status', 'powerOn'), true)) {
                        continue;
                    }
                    $devices[$index][$key] = $value;
                }
            }
        }

        return $devices;
    }

    /**
     * Merge node status and always re-resolve power from product + model tables.
     *
     * @param array<int, array<string, mixed>> $devices
     * @param array<int, array<string, mixed>> $allNodes
     * @return array<int, array<string, mixed>>
     */
    public function enrichDevicesLiveState(\mysqli $db, array $devices, array $allNodes = array(), int $userId = 0): array
    {
        if ($allNodes !== array()) {
            $devices = $this->mergeIntoDevices($devices, $allNodes);
        }

        foreach ($devices as $index => $device) {
            if (!is_array($device)) {
                continue;
            }

            $nodeId = trim((string) ($device['node_id'] ?? ''));
            if ($nodeId === '') {
                continue;
            }

            $row = $this->fetchProductRowByNodeId($db, $nodeId, $userId);
            if ($row === null) {
                continue;
            }

            $state = $this->resolveDeviceState($db, $row, $nodeId);
            $connected = $this->isConnected($row['internet'] ?? null);
            $online = array_key_exists('online', $device) ? !empty($device['online']) : $connected;
            if (!array_key_exists('online', $device)) {
                $devices[$index]['online'] = $connected;
            }
            $power = $this->resolveProductPower($db, $row, $nodeId, $online);

            $devices[$index]['power'] = $power;
            $devices[$index]['powerOn'] = $power;
            $devices[$index]['status'] = $online ? 'Online' : 'Offline';

            $resolvedName = $state['name'];
            if ($resolvedName !== null && $resolvedName !== '') {
                $devices[$index]['name'] = $resolvedName;
                if (empty($devices[$index]['product_name']) || strtoupper((string) $devices[$index]['product_name']) === 'NULL') {
                    $devices[$index]['product_name'] = $resolvedName;
                }
            }
            if (!empty($state['readings']) && is_array($state['readings'])) {
                foreach ($state['readings'] as $key => $value) {
                    if (in_array($key, array('id', 'power', 'name', 'online', 'status', 'powerOn'), true)) {
                        continue;
                    }
                    $devices[$index][$key] = $value;
                }
                $devices[$index]['parameters'] = $state['readings'];
            }
        }

        return $devices;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function fetchProductRowByNodeId(\mysqli $db, string $nodeId, int $userId = 0): ?array
    {
        $nodeId = trim($nodeId);
        if ($nodeId === '') {
            return null;
        }

        if ($userId > 0) {
            $rows = $this->queryRows(
                $db,
                'SELECT id, node_id, name, device_type, model_name, model_number, internet, power, add_gateway, is_added, access_token
                 FROM efactorproduct WHERE node_id = ? AND user_id = ? ORDER BY id DESC LIMIT 1',
                'si',
                array($nodeId, $userId)
            );
        } else {
            $rows = $this->queryRows(
                $db,
                'SELECT id, node_id, name, device_type, model_name, model_number, internet, power, add_gateway, is_added, access_token
                 FROM efactorproduct WHERE node_id = ? ORDER BY id DESC LIMIT 1',
                's',
                array($nodeId)
            );
        }

        return $rows[0] ?? null;
    }

    /**
     * Resolve boolean power for API output, with WIFI connectivity fallback.
     *
     * @param array<string, mixed> $row efactorproduct row
     */
    public function resolveProductPower(\mysqli $db, array $row, string $nodeId, bool $online = false): bool
    {
        $state = $this->resolveDeviceState($db, $row, $nodeId);
        if ($state['power']) {
            return true;
        }

        if ($online && self::moduleUsesConnectivityAsPower($row)) {
            return true;
        }

        return false;
    }

    /**
     * Standalone WIFI modules often only update `internet` when powered — no separate relay column.
     *
     * @param array<string, mixed> $row
     */
    public static function moduleUsesConnectivityAsPower(array $row): bool
    {
        $addGateway = trim((string) ($row['add_gateway'] ?? ''));
        $isAdded    = trim((string) ($row['is_added'] ?? ''));
        if ($addGateway !== '' && $isAdded === '1') {
            return false;
        }

        $moduleType = strtoupper(trim((string) ($row['module_type'] ?? '')));

        return $moduleType === '' || in_array($moduleType, array('WIFI', 'EWIFI', 'WIFI_MODULE'), true);
    }

    public static function nodeFieldCatalog(): array
    {
        return array(
            'node' => array(
                'node_id' => 'Hardware node id — primary control key',
                'devices' => 'Child device states attached to this node/gateway',
                'status.connectivity.connected' => 'Online connectivity flag (string true/false legacy compatible)',
                'status.connectivity.timestamp'   => 'Unix epoch milliseconds',
            ),
            'device' => array(
                'devices[].id'    => 'Device/node id used for control commands',
                'devices[].power' => 'Boolean power state',
                'devices[].name'  => 'Runtime device name from model table',
            ),
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchProductRows(\mysqli $db, string $accessToken, int $userId): array
    {
        $select = 'SELECT id, node_id, name, device_type, model_name, model_number, internet, power, add_gateway, is_added, access_token
                   FROM efactorproduct';

        if ($userId > 0) {
            $rows = $this->queryRows(
                $db,
                $select . ' WHERE user_id = ? ORDER BY id DESC',
                'i',
                array($userId)
            );
            if ($rows !== array()) {
                return $rows;
            }
        }

        $rows = $this->queryRows(
            $db,
            $select . ' WHERE access_token = ? ORDER BY id DESC',
            's',
            array($accessToken)
        );
        if ($rows !== array()) {
            return $rows;
        }

        if (strpos($accessToken, '.') === false) {
            $like = '%' . $accessToken . '%';
            $rows = $this->queryRows(
                $db,
                $select . ' WHERE access_token LIKE ? ORDER BY id DESC',
                's',
                array($like)
            );
            if ($rows !== array()) {
                return $rows;
            }
        } else {
            $parts = explode('.', $accessToken);
            if (isset($parts[1]) && $parts[1] !== '') {
                $like = '%' . $parts[1] . '%';
                $rows = $this->queryRows(
                    $db,
                    $select . ' WHERE access_token LIKE ? ORDER BY id DESC',
                    's',
                    array($like)
                );
                if ($rows !== array()) {
                    return $rows;
                }
            }
        }

        return array();
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function buildStandaloneNode(\mysqli $db, array $row): array
    {
        $nodeId = (string) ($row['node_id'] ?? '');
        $state  = $this->resolveDeviceState($db, $row, $nodeId);

        return array(
            'node_id' => $nodeId,
            'devices' => array(
                $this->buildDevicePayload($nodeId, $state),
            ),
            'status' => array(
                'connectivity' => $this->connectivityPayload($row['internet'] ?? null),
            ),
        );
    }

    /**
     * @param array<string, mixed> $gatewayRow
     * @return array<string, mixed>
     */
    private function buildGatewayNode(\mysqli $db, array $gatewayRow, string $accessToken): array
    {
        $gatewayNodeId = (string) ($gatewayRow['node_id'] ?? '');
        $devices = array();

        $gatewayState = $this->resolveDeviceState($db, $gatewayRow, $gatewayNodeId);
        $devices[] = $this->buildDevicePayload($gatewayNodeId, $gatewayState);

        $childRows = $this->fetchGatewayChildren($db, $gatewayNodeId, $accessToken);
        foreach ($childRows as $childRow) {
            $childNodeId = (string) ($childRow['node_id'] ?? '');
            if ($childNodeId === '' || $childNodeId === $gatewayNodeId) {
                continue;
            }
            $childState = $this->resolveDeviceState($db, $childRow, $childNodeId);
            $devices[] = $this->buildDevicePayload($childNodeId, $childState);
        }

        return array(
            'node_id' => $gatewayNodeId,
            'devices' => $devices,
            'status'  => array(
                'connectivity' => $this->connectivityPayload($gatewayRow['internet'] ?? null),
            ),
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchGatewayChildren(\mysqli $db, string $gatewayNodeId, string $accessToken): array
    {
        $rows = $this->queryRows(
            $db,
            'SELECT id, node_id, name, device_type, model_name, model_number, internet, power, add_gateway, is_added
             FROM efactorproduct WHERE add_gateway = ? ORDER BY id DESC',
            's',
            array($gatewayNodeId)
        );
        if ($rows !== array()) {
            return $rows;
        }

        if ($accessToken === '') {
            return array();
        }

        return $this->queryRows(
            $db,
            'SELECT p.id, p.node_id, p.name, p.device_type, p.model_name, p.model_number, p.internet, p.power, p.add_gateway, p.is_added
             FROM efactorproduct p
             INNER JOIN efactorproduct g ON g.node_id = ? AND g.access_token = p.access_token
             WHERE p.add_gateway = ?
             ORDER BY p.id DESC',
            'ss',
            array($gatewayNodeId, $gatewayNodeId)
        );
    }

    /**
     * @param array<string, mixed> $row
     * @return array{power:bool,name:?string,readings:array<string,mixed>}
     */
    private function resolveDeviceState(\mysqli $db, array $row, string $nodeId): array
    {
        $modelNumber = trim((string) ($row['model_number'] ?? ''));
        $fallbackName = $this->nullableName($row['name'] ?? null);
        $fallbackPower = IotDeviceNormalizer::isPowerOn($row['power'] ?? null);
        $fallbackReadings = $this->extractDeviceReadings($row);

        if ($modelNumber === '' || strcasecmp($modelNumber, self::DEFAULT_MODEL) === 0) {
            return array(
                'power' => $fallbackPower,
                'name'  => $fallbackName,
                'readings' => $fallbackReadings,
            );
        }

        if (!$this->isSafeModelTable($modelNumber)) {
            return array(
                'power' => $fallbackPower,
                'name'  => $fallbackName,
                'readings' => $fallbackReadings,
            );
        }

        $modelRow = $this->queryOne(
            $db,
            'SELECT * FROM `' . str_replace('`', '``', $modelNumber) . '` WHERE node_id = ? LIMIT 1',
            's',
            array($nodeId)
        );

        if ($modelRow === null) {
            return array(
                'power' => $fallbackPower,
                'name'  => $fallbackName,
                'readings' => $fallbackReadings,
            );
        }

        $readings = $this->extractDeviceReadings($modelRow);
        if ($readings === array() && $fallbackReadings !== array()) {
            $readings = $fallbackReadings;
        }

        return array(
            'power' => IotDeviceNormalizer::isPowerOn($modelRow['power'] ?? $row['power'] ?? null),
            'name'  => $this->nullableName($modelRow['name'] ?? $fallbackName),
            'readings' => $readings,
        );
    }

    /**
     * Extra live page readings for all_nodes devices[] (Torque, Display Log, etc.).
     * Keeps name/power and adds non-internal model columns without inventing values.
     *
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function extractDeviceReadings(array $row): array
    {
        $skip = array(
            'id' => true,
            'node_id' => true,
            'access_token' => true,
            'password' => true,
            'user_id' => true,
            'add_gateway' => true,
            'is_added' => true,
            'internet' => true,
            'created_at' => true,
            'updated_at' => true,
            'created' => true,
            'updated' => true,
            'model_number' => true,
            'model_name' => true,
            'device_type' => true,
            'current_devicetype' => true,
            'mac' => true,
            'ssid' => true,
            'wifi_password' => true,
        );

        $readings = array();
        foreach ($row as $key => $value) {
            $column = trim((string) $key);
            if ($column === '' || isset($skip[strtolower($column)])) {
                continue;
            }
            if ($value === null) {
                continue;
            }
            if (is_string($value) && (trim($value) === '' || strtoupper(trim($value)) === 'NULL')) {
                continue;
            }
            // Keep original column key and a friendly parameter-style alias.
            $readings[$column] = $this->normalizeReadingValue($value);
            $friendly = $this->friendlyReadingKey($column);
            if ($friendly !== '' && $friendly !== $column && !array_key_exists($friendly, $readings)) {
                $readings[$friendly] = $readings[$column];
            }
        }

        return $readings;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function normalizeReadingValue($value)
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value)) {
            return $value;
        }
        $text = trim((string) $value);
        $lower = strtolower($text);
        if (in_array($lower, array('1', 'true', 'yes', 'on'), true)) {
            return true;
        }
        if (in_array($lower, array('0', 'false', 'no', 'off'), true)) {
            return false;
        }
        if (is_numeric($text)) {
            return strpos($text, '.') !== false ? (float) $text : (int) $text;
        }

        return $text;
    }

    private function friendlyReadingKey(string $column): string
    {
        $map = array(
            'fan_torque_limit' => 'Torque',
            'torque_limit' => 'Torque',
            'torque' => 'Torque',
            'display_log' => 'Display Log',
            'displaylog' => 'Display Log',
            'fan_frequency' => 'frequency',
            'fan_pairs' => 'pole pairs',
            'fan_open_loop_current' => 'Startup Current',
            'fan_power_limit' => 'Power Limit',
            'fan_speed1' => 'Speed L1',
            'fan_speed2' => 'Speed L2',
            'fan_speed3' => 'Speed L3',
            'fan_speed4' => 'Speed L4',
            'power' => 'Power',
            'name' => 'name',
            'direction' => 'Direction',
            'speed' => 'Speed',
        );
        $lower = strtolower($column);
        if (isset($map[$lower])) {
            return $map[$lower];
        }
        // snake_case → Title Case words for parameter name matching.
        if (strpos($column, '_') !== false) {
            $parts = explode('_', strtolower($column));
            $parts = array_map(static function ($part) {
                return $part === '' ? '' : ucfirst($part);
            }, $parts);

            return trim(implode(' ', $parts));
        }

        return $column;
    }

    /**
     * @param array<string, mixed> $state
     * @return array<string, mixed>
     */
    private function buildDevicePayload(string $nodeId, array $state): array
    {
        $device = array(
            'id'    => $nodeId,
            'power' => $state['power'],
            'name'  => $state['name'],
        );
        $readings = isset($state['readings']) && is_array($state['readings']) ? $state['readings'] : array();
        foreach ($readings as $key => $value) {
            if ($key === 'id' || $key === 'power' || $key === 'name') {
                continue;
            }
            $device[$key] = $value;
        }
        if ($readings !== array()) {
            $device['parameters'] = $readings;
        }

        return $device;
    }

    /**
     * @return array{connected:string,timestamp:int}
     */
    private function connectivityPayload($internet): array
    {
        $connected = $this->isConnected($internet);

        return array(
            'connected' => $connected ? 'true' : 'false',
            'timestamp' => (int) round(microtime(true) * 1000),
        );
    }

    private function isConnected($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $normalized = strtolower(trim((string) $value));
        return in_array($normalized, array('1', 'true', 'yes', 'online', 'connected'), true);
    }

    /**
     * @param array<int, array<string, mixed>> $nodes
     * @return array<int, array<string, mixed>>
     */
    private function dedupeNodes(array $nodes): array
    {
        $indexed = array();
        foreach ($nodes as $node) {
            $nodeId = (string) ($node['node_id'] ?? '');
            if ($nodeId === '') {
                continue;
            }
            if (!isset($indexed[$nodeId])) {
                $indexed[$nodeId] = $node;
                continue;
            }

            $existingConnected = $this->isConnected($indexed[$nodeId]['status']['connectivity']['connected'] ?? 'false');
            $incomingConnected = $this->isConnected($node['status']['connectivity']['connected'] ?? 'false');
            if ($incomingConnected && !$existingConnected) {
                $indexed[$nodeId] = $node;
            }
        }

        return array_values($indexed);
    }

    private function dbValue($value): string
    {
        $value = trim((string) $value);
        if ($value === '' || strtoupper($value) === 'NULL') {
            return '';
        }

        return $value;
    }

    private function nullableName($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '' || strtoupper($value) === 'NULL') {
            return null;
        }

        return $value;
    }

    private function isSafeModelTable(string $table): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9_\-\. ]+$/', $table);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function queryRows(\mysqli $db, string $sql, string $types, array $params): array
    {
        try {
            $stmt = $db->prepare($sql);
            if (!$stmt) {
                return array();
            }
            if ($types !== '') {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = array();
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }

            return $rows;
        } catch (\Throwable $e) {
            return array();
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function queryOne(\mysqli $db, string $sql, string $types, array $params): ?array
    {
        $rows = $this->queryRows($db, $sql, $types, $params);

        return $rows[0] ?? null;
    }
}
