<?php
ini_set('memory_limit', '512M');

$node = 'FFFFFFFFFFFFFFFFZEC200000003A600';
$model = 'EF_smart_plug';

function fetch($url)
{
    $ctx = stream_context_create([
        'http' => ['timeout' => 45, 'ignore_errors' => true, 'header' => "Accept: application/json\r\n"],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    ]);
    $raw = @file_get_contents($url, false, $ctx);
    return is_string($raw) ? $raw : '';
}

$schemaRaw = fetch("https://efactor.tech/v8/api_schema.php?model_no={$model}&node_id={$node}");
$schemaJson = json_decode($schemaRaw, true);
$s = $schemaJson['data']['schema_json'] ?? null;
if (is_string($s)) {
    $s = json_decode($s, true);
}

echo "=== SCHEMA (what app gets on open) ===\n";
echo 'build=' . ($s['meta']['schemaBuildVersion'] ?? '') . "\n";
echo 'state.online=' . json_encode($s['state']['online'] ?? null) . "\n";

$keys = [
    '1_power_command_2_toggle_switch',
    '1_voltage_v_command_0_text',
    '1_current_a_command_0_text',
    '1_total_watt_w_command_0_text',
];
foreach ($keys as $k) {
    foreach ($s['controls'] ?? [] as $c) {
        if (($c['parameterKey'] ?? '') !== $k) {
            continue;
        }
        $state = $s['state']['parameters'][$k] ?? null;
        echo "$k\n";
        echo '  control.value=' . json_encode($c['value'] ?? null)
            . ' loading=' . json_encode($c['valueLoading'] ?? null)
            . ' binding=' . json_encode($c['binding'] ?? null)
            . ' flutterBinding=' . json_encode($c['flutterBinding'] ?? null) . "\n";
        echo '  state.parameters entry=' . json_encode($state) . "\n";
        echo '  state entry type=' . gettype($state) . "\n";
        if (is_array($state)) {
            echo '  has .value key=' . (array_key_exists('value', $state) ? 'Y' : 'N') . "\n";
        }
    }
}

// Poll endpoints the app said it uses
$pollUrls = [
    "https://efactor.tech/v8/api.php?action=auto_get_nodes_params&node_id={$node}&device_id={$node}",
    "https://efactor.tech/v8/newapi.php?action=auto_get_nodes_params&node_id={$node}&device_id={$node}",
    "https://efactor.tech/v8/newapi.php?action=test_nodes&node_id={$node}",
];

foreach ($pollUrls as $url) {
    echo "\n=== POLL $url ===\n";
    $raw = fetch($url);
    echo 'bytes=' . strlen($raw) . "\n";
    echo 'prefix=' . substr(trim($raw), 0, 250) . "\n";
    $j = json_decode($raw, true);
    if (!is_array($j)) {
        // sometimes HTML wrapper
        $start = strpos($raw, '{');
        if ($start !== false) {
            $j = json_decode(substr($raw, $start), true);
        }
    }
    if (!is_array($j)) {
        echo "json=INVALID\n";
        continue;
    }
    echo 'top_keys=' . json_encode(array_keys($j)) . "\n";
    // Look for parameter-like payloads
    $candidates = [];
    $walk = function ($node, $path = '') use (&$walk, &$candidates) {
        if (!is_array($node)) {
            return;
        }
        if (isset($node['1_voltage_v_command_0_text']) || isset($node['voltage']) || isset($node['Voltage'])) {
            $candidates[$path] = $node;
        }
        foreach ($node as $k => $v) {
            if (is_array($v) && count($candidates) < 8) {
                $walk($v, $path . '/' . $k);
            }
        }
    };
    $walk($j);
    if ($candidates) {
        foreach ($candidates as $path => $node) {
            echo "candidate $path => " . substr(json_encode($node), 0, 400) . "\n";
        }
    } else {
        // dump node_details pages values if present
        $td = $j['node_details'][0] ?? ($j['data']['node_details'][0] ?? null);
        if (is_array($td)) {
            echo 'node_details.id=' . json_encode($td['id'] ?? null) . "\n";
            echo 'connected=' . json_encode($td['status']['connectivity']['connected'] ?? null) . "\n";
            $pages = $td['config']['devices'][0]['pages'] ?? [];
            foreach ($pages as $pn => $frames) {
                echo "page=$pn count=" . (is_array($frames) ? count($frames) : 0) . "\n";
                if (!is_array($frames)) {
                    continue;
                }
                foreach ($frames as $item) {
                    $f = $item['frame'] ?? $item;
                    if (!is_array($f)) {
                        continue;
                    }
                    $name = $f['name'] ?? '?';
                    foreach (array_merge($f['commands'] ?? [], $f['response'] ?? []) as $field) {
                        if (!is_array($field)) {
                            continue;
                        }
                        $desc = strtolower(trim((string) ($field['Field_description'] ?? $field['field_description'] ?? '')));
                        if (!in_array($desc, ['data', 'trigger'], true) && empty($field['ui_type'])) {
                            continue;
                        }
                        echo "  $name desc=$desc v=" . json_encode($field['value'] ?? null)
                            . " a=" . json_encode($field['asperui_value'] ?? null) . "\n";
                    }
                }
            }
        } else {
            echo "no node_details; sample=" . substr(json_encode($j), 0, 500) . "\n";
        }
    }
}

echo "\n=== HYPOTHESIS ===\n";
echo "If schema seeds 0 but poll returns empty/missing keys, app live-merge can wipe UI.\n";
echo "If app binds only after live merge and poll has no param keys, UI stays empty.\n";
