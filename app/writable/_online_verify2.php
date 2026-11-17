<?php
ini_set('memory_limit', '512M');

$raw = file_get_contents(__DIR__ . '/_online_test_nodes.json');
$j = json_decode($raw, true);
$td = $j['node_details'][0];
echo json_encode([
  'connected' => $td['status']['connectivity']['connected'] ?? null,
  'info' => $td['config']['info'] ?? null,
  'attrs' => $td['config']['attributes'] ?? null,
  'device_attrs' => $td['config']['devices'][0]['attributes'] ?? null,
  'pages_keys' => array_keys($td['config']['devices'][0]['pages'] ?? []),
], JSON_PRETTY_PRINT);
echo "\n--- pages dump ---\n";
$pages = $td['config']['devices'][0]['pages'] ?? [];
foreach ($pages as $pn => $frames) {
    echo "PAGE $pn count=" . (is_array($frames) ? count($frames) : 0) . "\n";
    if (!is_array($frames)) {
        echo json_encode($frames) . "\n";
        continue;
    }
    foreach ($frames as $i => $item) {
        $f = $item['frame'] ?? $item;
        if (!is_array($f)) {
            echo "  item$i nonarray=" . json_encode($item) . "\n";
            continue;
        }
        echo "  frame=" . ($f['name'] ?? '?') . " cmds=" . count($f['commands'] ?? []) . " resp=" . count($f['response'] ?? []) . "\n";
        foreach (array_merge($f['commands'] ?? [], $f['response'] ?? []) as $field) {
            $desc = $field['Field_description'] ?? $field['field_description'] ?? $field['default_name'] ?? '';
            $v = $field['value'] ?? null;
            $a = $field['asperui_value'] ?? null;
            if ($v !== null || $a !== null || !empty($field['ui_type'])) {
                echo "    desc=$desc ui=" . ($field['ui_type'] ?? '') . " v=" . json_encode($v) . " a=" . json_encode($a) . "\n";
            }
        }
    }
}

echo "\n=== SCHEMA SIDE ===\n";
$data = json_decode(file_get_contents(__DIR__ . '/_online_schema.json'), true);
$s = $data['data']['schema_json'];
if (is_string($s)) $s = json_decode($s, true);
echo 'state.online=' . json_encode($s['state']['online'] ?? null) . PHP_EOL;
echo 'state.updatedAt=' . json_encode($s['state']['updatedAt'] ?? null) . PHP_EOL;
echo 'connectivity=' . json_encode($s['node_details'][0]['status'] ?? null, JSON_PRETTY_PRINT) . PHP_EOL;
echo 'mqtt=' . json_encode($s['mqtt'] ?? null, JSON_PRETTY_PRINT) . PHP_EOL;

echo "\nparams with interesting values:\n";
foreach (($s['state']['parameters'] ?? []) as $k => $p) {
    $v = $p['value'] ?? null;
    if ($v === null || $v === '' || $v === false || $v === 0 || $v === '0' || $v === '0s' || $v === 'OFF' || $v === 'No error') continue;
    echo " $k => " . json_encode($v) . PHP_EOL;
}

echo "\nloading controls:\n";
foreach (($s['controls'] ?? []) as $c) {
    if (!empty($c['valueLoading'])) {
        echo ' ' . $c['parameterKey'] . ' value=' . json_encode($c['value'] ?? null) . ' init=' . json_encode($c['initialValue'] ?? null) . PHP_EOL;
    }
}

echo "\nempty controls:\n";
foreach (($s['controls'] ?? []) as $c) {
    if (!empty($c['valueLoading'])) continue;
    $v = $c['value'] ?? $c['initialValue'] ?? null;
    if ($v === null || $v === '') echo ' ' . $c['parameterKey'] . PHP_EOL;
}

// Check Voltage field datatype / asperui handler path in schema frames
echo "\n=== Voltage field full ===\n";
foreach ($s['deviceConfig']['devices'][0]['pages']['Page1.Operation'] ?? [] as $item) {
    $frame = $item['frame'] ?? $item;
    $name = $frame['name'] ?? '';
    if (stripos($name, 'Voltage') === false && stripos($name, 'Current') === false && stripos($name, 'Watt') === false && strcasecmp($name, 'power') !== 0) continue;
    echo "FRAME $name\n";
    foreach ($frame['commands'] ?? [] as $field) {
        echo "  " . json_encode([
            'desc' => $field['Field_description'] ?? $field['field_description'] ?? null,
            'ui' => $field['ui_type'] ?? null,
            'datatype' => $field['datatype'] ?? $field['data_type'] ?? null,
            'value' => $field['value'] ?? null,
            'asperui_value' => $field['asperui_value'] ?? null,
            'options' => $field['options'] ?? null,
        ], JSON_PRETTY_PRINT) . "\n";
    }
}
