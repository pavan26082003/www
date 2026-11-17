<?php
ini_set('memory_limit', '512M');

$nodeId = 'FFFFFFFFFFFFFFFFZEC200000003A600';
$data = json_decode(file_get_contents(__DIR__ . '/_online_schema.json'), true);
$schema = $data['data']['schema_json'] ?? null;
if (is_string($schema)) $schema = json_decode($schema, true);

echo "=== SCHEMA ===\n";
echo "status=" . json_encode($data['status'] ?? null) . "\n";
echo "updated_at=" . ($data['data']['updated_at'] ?? '') . "\n";
echo "schemaBuildVersion=" . ($schema['meta']['schemaBuildVersion'] ?? '') . "\n";
echo "nodes=" . json_encode($schema['nodes'] ?? null) . "\n";
echo "node_details.id=" . json_encode($schema['node_details'][0]['id'] ?? null) . "\n";
echo "connected=" . json_encode($schema['node_details'][0]['status']['connectivity']['connected'] ?? null) . "\n";
echo "state.online=" . json_encode($schema['state']['online'] ?? null) . "\n";
echo "liveStatePath=" . json_encode($schema['deviceState']['liveStatePath'] ?? null) . "\n";
echo "brand=" . json_encode($schema['deviceConfig']['info']['brand'] ?? null) . "\n";
echo "mqtt.state=" . json_encode($schema['mqtt']['topics']['state'] ?? null) . "\n";

function isBracket($v) {
    $t = trim((string)$v);
    if ($t === '' || ($t[0] ?? '') !== '[') return false;
    if ($t === '[]') return true;
    return (bool)preg_match('/^(\[[^\]]*\]){1,}$/', $t);
}

$junk=0;
foreach ($schema['deviceConfig']['devices'][0]['pages'] ?? [] as $frames) {
    foreach ($frames as $item) {
        $frame = $item['frame'] ?? $item;
        foreach (['commands','response','error'] as $b) {
            foreach ($frame[$b] ?? [] as $field) {
                if (isBracket($field['asperui_value'] ?? null)) $junk++;
            }
        }
    }
}
echo "bracket_junk=$junk\n";

echo "\n=== PAGE1 DATA FIELDS ===\n";
foreach ($schema['deviceConfig']['devices'][0]['pages']['Page1.Operation'] ?? [] as $item) {
    $frame = $item['frame'] ?? $item;
    $name = $frame['name'] ?? '';
    foreach ($frame['commands'] ?? [] as $field) {
        if (strtolower(trim((string)($field['Field_description'] ?? $field['field_description'] ?? ''))) !== 'data') continue;
        echo "- $name value=" . json_encode($field['value'] ?? null)
            . " asperui=" . json_encode($field['asperui_value'] ?? null) . "\n";
    }
}

echo "\n=== KEY CONTROLS ===\n";
$want = ['power','voltage','current','watt','error','timer','child'];
foreach ($schema['controls'] ?? [] as $c) {
    $key = strtolower((string)($c['parameterKey'] ?? ''));
    $name = strtolower((string)($c['parameterName'] ?? ''));
    $hit = false;
    foreach ($want as $w) if (str_contains($key, $w) || str_contains($name, $w)) { $hit = true; break; }
    if (!$hit) continue;
    $state = $schema['state']['parameters'][$c['parameterKey']]['value'] ?? null;
    echo "- ".$c['parameterKey']
        . " init=" . json_encode($c['initialValue'] ?? null)
        . " value=" . json_encode($c['value'] ?? null)
        . " loading=" . json_encode($c['valueLoading'] ?? null)
        . " state=" . json_encode($state)
        . " binding=" . json_encode($c['flutterBinding'] ?? null)
        . "\n";
}

echo "\n=== TEST_NODES RAW ===\n";
$raw = file_get_contents(__DIR__ . '/_online_test_nodes.json');
echo "bytes=" . strlen((string)$raw) . "\n";
echo "prefix=" . substr(trim((string)$raw), 0, 300) . "\n";
$test = json_decode($raw, true);
if (!is_array($test)) {
    $start = strpos((string)$raw, '{"node_details"');
    if ($start !== false) $test = json_decode(substr($raw, $start), true);
}
if (!is_array($test)) {
    echo "test_nodes_json=INVALID\n";
    exit(0);
}
$td = $test['node_details'][0] ?? [];
echo "test_nodes connected=" . json_encode($td['status']['connectivity']['connected'] ?? null) . "\n";
echo "test_nodes id=" . json_encode($td['id'] ?? null) . "\n";
$tInfo = $td['config']['info'] ?? [];
echo "test_nodes info name=" . json_encode($tInfo['name'] ?? null) . " fw=" . json_encode($tInfo['fw_version'] ?? null) . "\n";

echo "\n=== TEST_NODES PAGE1 VALUES ===\n";
$pages = $td['config']['devices'][0]['pages'] ?? [];
foreach ($pages as $pageName => $frames) {
    if (stripos($pageName, 'Page1') === false && stripos($pageName, 'Operation') === false) continue;
    foreach ($frames as $item) {
        $frame = $item['frame'] ?? $item;
        if (!is_array($frame)) continue;
        $fname = $frame['name'] ?? '';
        foreach ($frame['commands'] ?? [] as $field) {
            $desc = strtolower(trim((string)($field['Field_description'] ?? $field['field_description'] ?? $field['default_name'] ?? '')));
            if (!in_array($desc, ['data','trigger'], true) && empty($field['ui_type'])) continue;
            $ui = strtolower((string)($field['ui_type'] ?? 'none'));
            if ($ui === 'none' && !in_array($desc, ['data','trigger'], true)) continue;
            echo "- $pageName::$fname value=" . json_encode($field['value'] ?? null)
                . " asperui=" . json_encode($field['asperui_value'] ?? null) . "\n";
        }
    }
}

// Summary for Flutter
$ready=0; $loading=0; $empty=0; $ct=0;
foreach ($schema['controls'] ?? [] as $c) {
    $ct++;
    if (!empty($c['valueLoading'])) { $loading++; continue; }
    $v = $c['value'] ?? $c['initialValue'] ?? null;
    if ($v === null || $v === '') $empty++; else $ready++;
}
echo "\n=== SUMMARY ===\n";
echo "controls ready=$ready loading=$loading empty=$empty total=$ct\n";
echo "schema_connected=" . json_encode($schema['node_details'][0]['status']['connectivity']['connected'] ?? null) . "\n";
echo "test_nodes_connected=" . json_encode($td['status']['connectivity']['connected'] ?? null) . "\n";
