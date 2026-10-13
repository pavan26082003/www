<?php
/**
 * Shared live-state helpers for getAllNodes / getAllNodesStatus / all_nodes.
 *
 * Adds a flat parameters map from the device model table + product_api names
 * without renaming existing catalogue fields.
 */

if (!function_exists('efactor_node_state_now_ms')) {
    function efactor_node_state_now_ms()
    {
        return (int) round(microtime(true) * 1000);
    }
}

if (!function_exists('efactor_node_state_safe_table')) {
    function efactor_node_state_safe_table($table)
    {
        $table = trim((string) $table);

        return $table !== '' && (bool) preg_match('/^[A-Za-z0-9_\-\. ]+$/', $table);
    }
}

if (!function_exists('efactor_node_state_db_row')) {
    function efactor_node_state_db_row($db, $sql, $types = '', array $params = [])
    {
        if ($db instanceof mysqli) {
            $stmt = $db->prepare($sql);
            if (!$stmt) {
                return null;
            }
            if ($types !== '' && $params !== []) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result ? $result->fetch_assoc() : null;
            $stmt->close();

            return is_array($row) ? $row : null;
        }
        if (is_object($db) && method_exists($db, 'get_row')) {
            $row = $db->get_row($sql);

            return is_array($row) ? $row : (is_object($row) ? (array) $row : null);
        }

        return null;
    }
}

if (!function_exists('efactor_node_state_db_rows')) {
    function efactor_node_state_db_rows($db, $sql, $types = '', array $params = [])
    {
        if ($db instanceof mysqli) {
            $stmt = $db->prepare($sql);
            if (!$stmt) {
                return [];
            }
            if ($types !== '' && $params !== []) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $stmt->close();

            return $rows;
        }
        if (is_object($db) && method_exists($db, 'get_results')) {
            $rows = $db->get_results($sql);
            if (!is_array($rows)) {
                return [];
            }
            $out = [];
            foreach ($rows as $row) {
                $out[] = is_array($row) ? $row : (array) $row;
            }

            return $out;
        }

        return [];
    }
}

if (!function_exists('efactor_node_state_escape')) {
    function efactor_node_state_escape($db, $value)
    {
        $value = (string) $value;
        if ($db instanceof mysqli) {
            return $db->real_escape_string($value);
        }
        if (is_object($db) && method_exists($db, 'escape')) {
            return (string) $db->escape($value);
        }

        return addslashes($value);
    }
}

if (!function_exists('efactor_node_state_nullable_name')) {
    function efactor_node_state_nullable_name($value)
    {
        if ($value === null) {
            return null;
        }
        $text = trim((string) $value);
        if ($text === '' || strcasecmp($text, 'NULL') === 0) {
            return null;
        }

        return $text;
    }
}

if (!function_exists('efactor_node_state_is_connected')) {
    function efactor_node_state_is_connected($value)
    {
        if (is_bool($value)) {
            return $value;
        }
        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['1', 'true', 'yes', 'online', 'connected'], true);
    }
}

if (!function_exists('efactor_node_state_is_boolish_name')) {
    function efactor_node_state_is_boolish_name($name)
    {
        $name = strtolower(trim((string) $name));

        return $name === 'power'
            || $name === 'buzzer'
            || substr($name, -6) === 'buzzer'
            || strpos($name, 'switch') !== false
            || strpos($name, 'toggle') !== false;
    }
}

if (!function_exists('efactor_node_state_coerce')) {
    function efactor_node_state_coerce($name, $value)
    {
        if (is_bool($value) || is_int($value) || is_float($value) || $value === null) {
            return $value;
        }
        if (is_array($value)) {
            return $value;
        }
        $text = trim((string) $value);
        if ($text === '' || strcasecmp($text, 'NULL') === 0) {
            return null;
        }
        $lowerName = strtolower(trim((string) $name));
        if ($lowerName === 'direction' || $lowerName === 'name') {
            return $text;
        }
        $lower = strtolower($text);
        if (in_array($lower, ['true', 'false', 'on', 'off', '01', '00'], true)
            && efactor_node_state_is_boolish_name($name)
        ) {
            return in_array($lower, ['true', 'on', '01'], true);
        }
        if ($lower === 'true' || $lower === 'false') {
            return $lower === 'true';
        }
        if (is_numeric($text) && !preg_match('/^0[0-9A-Fa-f]+$/', $text)) {
            return strpos($text, '.') !== false ? (float) $text : (int) $text;
        }

        return $text;
    }
}

if (!function_exists('efactor_node_state_flatten_payload')) {
    function efactor_node_state_flatten_payload($payload)
    {
        if (!is_array($payload)) {
            return [];
        }
        $out = $payload;
        if (isset($out['']) && is_array($out[''])) {
            $out = array_merge($out[''], $out);
            unset($out['']);
        }
        if (isset($out['parameters']) && is_array($out['parameters'])) {
            $out = array_merge($out, $out['parameters']);
        }

        return $out;
    }
}

if (!function_exists('efactor_node_state_reserved_key')) {
    function efactor_node_state_reserved_key($key)
    {
        $key = strtolower(trim((string) $key));
        static $reserved = [
            'id' => true, 'node_id' => true, 'access_token' => true, 'user_id' => true,
            'model_name' => true, 'model_number' => true, 'model_no' => true,
            'created_at' => true, 'updated_at' => true, 'timestamp' => true,
            'internet' => true, 'payload' => true, 'add_gateway' => true, 'is_added' => true,
            'module_type' => true, 'vendor_name' => true, 'vendor_id' => true, 'vendor_type' => true,
            'current_devicetype' => true, 'device_type' => true, 'page_names' => true, 'page_name' => true,
            'brand_logo' => true, 'prod_category' => true, 'category_id' => true, 'selected' => true,
            'address' => true, 'power' => false,
        ];

        return isset($reserved[$key]) && $reserved[$key] === true;
    }
}

if (!function_exists('efactor_node_state_extract')) {
    /**
     * @return array{parameters:array<string,mixed>,parameters_updated_at:int,power:bool,name:?string}
     */
    function efactor_node_state_extract($db, $modelNumber, $nodeId, array $productRow = [])
    {
        $nodeId = trim((string) $nodeId);
        $modelNumber = trim((string) $modelNumber);
        $parameters = [];
        $updatedAt = efactor_node_state_now_ms();
        $name = efactor_node_state_nullable_name($productRow['name'] ?? null);
        $power = false;

        $row = $productRow;
        $table = '';
        if ($modelNumber !== '' && strcasecmp($modelNumber, 'EFDEFLT V1.0') !== 0 && efactor_node_state_safe_table($modelNumber)) {
            $table = $modelNumber;
        }
        if ($table !== '' && $nodeId !== '') {
            $escapedNode = efactor_node_state_escape($db, $nodeId);
            $quotedTable = '`' . str_replace('`', '``', $table) . '`';
            $modelRow = efactor_node_state_db_row(
                $db,
                'SELECT * FROM ' . $quotedTable . " WHERE node_id='" . $escapedNode . "' LIMIT 1"
            );
            if (is_array($modelRow) && $modelRow !== []) {
                $row = array_merge($productRow, $modelRow);
            }
        }

        $payloadRaw = $row['payload'] ?? null;
        if (is_string($payloadRaw) && $payloadRaw !== '') {
            $decoded = json_decode($payloadRaw, true);
            if (is_array($decoded)) {
                foreach (efactor_node_state_flatten_payload($decoded) as $key => $value) {
                    $key = trim((string) $key);
                    if ($key === '' || efactor_node_state_reserved_key($key)) {
                        continue;
                    }
                    $parameters[$key] = efactor_node_state_coerce($key, $value);
                }
            }
        }

        $paramNames = [];
        if ($modelNumber !== '') {
            $escapedModel = efactor_node_state_escape($db, $modelNumber);
            $apiRows = efactor_node_state_db_rows(
                $db,
                "SELECT DISTINCT parameter_name FROM product_api WHERE model_name='" . $escapedModel . "' AND parameter_name IS NOT NULL AND parameter_name != ''"
            );
            foreach ($apiRows as $apiRow) {
                $paramName = trim((string) ($apiRow['parameter_name'] ?? ''));
                if ($paramName !== '') {
                    $paramNames[$paramName] = true;
                }
            }
        }

        $rowLookup = [];
        foreach ($row as $column => $value) {
            $rowLookup[strtolower((string) $column)] = ['name' => (string) $column, 'value' => $value];
        }

        foreach (array_keys($paramNames) as $paramName) {
            $lookup = $rowLookup[strtolower($paramName)] ?? null;
            if ($lookup === null) {
                continue;
            }
            $parameters[$paramName] = efactor_node_state_coerce($paramName, $lookup['value']);
        }

        if (isset($row['power'])) {
            $powerValue = $row['power'];
            $power = $powerValue === true
                || $powerValue === 1
                || $powerValue === '1'
                || $powerValue === '01'
                || strtolower((string) $powerValue) === 'true'
                || strtolower((string) $powerValue) === 'on';
            if (!array_key_exists('power', $parameters)) {
                $parameters['power'] = $power;
            } else {
                $parameters['power'] = efactor_node_state_coerce('power', $parameters['power']);
                if (is_bool($parameters['power'])) {
                    $power = $parameters['power'];
                }
            }
        }

        if (isset($row['name'])) {
            $name = efactor_node_state_nullable_name($row['name']);
        }

        foreach (['updated_at', 'timestamp'] as $stampKey) {
            if (empty($row[$stampKey])) {
                continue;
            }
            $stamp = $row[$stampKey];
            if (is_numeric($stamp)) {
                $asInt = (int) $stamp;
                $updatedAt = $asInt > 20000000000 ? $asInt : ($asInt * 1000);
                break;
            }
            $parsed = strtotime((string) $stamp);
            if ($parsed) {
                $updatedAt = $parsed * 1000;
                break;
            }
        }

        return [
            'parameters' => $parameters,
            'parameters_updated_at' => $updatedAt,
            'power' => $power,
            'name' => $name,
        ];
    }
}

if (!function_exists('efactor_enrich_node_state')) {
    function efactor_enrich_node_state($db, array $node, array $productRow = [])
    {
        $nodeId = trim((string) ($node['node_id'] ?? ($node['config']['node_id'] ?? '')));
        if ($nodeId === '' && isset($node['config']['devices'][0]['id'])) {
            $nodeId = trim((string) $node['config']['devices'][0]['id']);
        }
        if ($nodeId === '' && isset($node['devices'][0]['id'])) {
            $nodeId = trim((string) $node['devices'][0]['id']);
        }

        $modelNumber = trim((string) ($productRow['model_number'] ?? ($productRow['model_name'] ?? '')));
        $internet = $productRow['internet'] ?? ($node['status']['connectivity']['connected'] ?? false);

        if ($nodeId !== '' && $productRow === []) {
            $escapedNode = efactor_node_state_escape($db, $nodeId);
            $found = efactor_node_state_db_row(
                $db,
                "SELECT * FROM efactorproduct WHERE node_id='" . $escapedNode . "' ORDER BY id DESC LIMIT 1"
            );
            if (is_array($found)) {
                $productRow = $found;
                if ($modelNumber === '') {
                    $modelNumber = trim((string) ($found['model_number'] ?? $found['model_name'] ?? ''));
                }
                if (!isset($productRow['internet'])) {
                    $internet = $found['internet'] ?? $internet;
                } else {
                    $internet = $productRow['internet'];
                }
            }
        }

        $state = efactor_node_state_extract($db, $modelNumber, $nodeId, $productRow);
        $connected = efactor_node_state_is_connected($internet);
        $timestamp = isset($node['status']['connectivity']['timestamp'])
            ? (int) $node['status']['connectivity']['timestamp']
            : $state['parameters_updated_at'];

        $node['node_id'] = $nodeId;
        if (!isset($node['config']) || !is_array($node['config'])) {
            $node['config'] = [];
        }
        $node['config']['node_id'] = $nodeId;
        $node['parameters'] = $state['parameters'];
        $node['parameters_updated_at'] = $state['parameters_updated_at'];
        if (!isset($node['status']) || !is_array($node['status'])) {
            $node['status'] = [];
        }
        if (!isset($node['status']['connectivity']) || !is_array($node['status']['connectivity'])) {
            $node['status']['connectivity'] = [];
        }
        $node['status']['connectivity']['connected'] = $connected;
        $node['status']['connectivity']['timestamp'] = $timestamp;

        $deviceBags = [];
        if (isset($node['config']['devices']) && is_array($node['config']['devices'])) {
            $deviceBags[] =& $node['config']['devices'];
        }
        if (isset($node['devices']) && is_array($node['devices'])) {
            $deviceBags[] =& $node['devices'];
        }
        foreach ($deviceBags as &$devices) {
            foreach ($devices as &$device) {
                if (!is_array($device)) {
                    continue;
                }
                if (array_key_exists('name', $device)) {
                    $device['name'] = efactor_node_state_nullable_name($device['name']);
                }
                if (array_key_exists('device_name', $device)) {
                    $device['device_name'] = efactor_node_state_nullable_name($device['device_name']);
                }
                if (array_key_exists('power', $device) || isset($state['parameters']['power'])) {
                    $device['power'] = isset($state['parameters']['power'])
                        ? (bool) $state['parameters']['power']
                        : (bool) $state['power'];
                }
            }
            unset($device);
        }
        unset($devices);

        return $node;
    }
}

if (!function_exists('efactor_enrich_all_nodes')) {
    function efactor_enrich_all_nodes($db, array $allNodes)
    {
        foreach ($allNodes as $index => $node) {
            if (!is_array($node)) {
                continue;
            }
            $allNodes[$index] = efactor_enrich_node_state($db, $node);
        }

        return $allNodes;
    }
}

if (!function_exists('efactor_force_https_url')) {
    function efactor_force_https_url($url)
    {
        $url = trim((string) $url);
        if ($url === '' || stripos($url, 'http://') !== 0) {
            return $url;
        }
        $host = parse_url($url, PHP_URL_HOST);
        if (in_array(strtolower((string) $host), ['localhost', '127.0.0.1', '::1'], true)) {
            return $url;
        }

        return 'https://' . substr($url, 7);
    }
}
