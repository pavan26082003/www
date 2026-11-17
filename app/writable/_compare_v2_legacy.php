<?php
ini_set('memory_limit', '512M');

$v2 = json_decode(file_get_contents(__DIR__ . '/_v2_fan_nodes.json'), true);
$auto = json_decode(file_get_contents(__DIR__ . '/_v2_fan_autoget.json'), true);
$schemaRaw = json_decode(file_get_contents(__DIR__ . '/_v2_fan_schema.json'), true);
$schema = $schemaRaw['data']['schema_json'] ?? null;
if (is_string($schema)) $schema = json_decode($schema, true);

echo "=== V2 test_nodes envelope ===\n";
echo 'status=' . json_encode($v2['status'] ?? null) . "\n";
echo 'source=' . ($v2['source'] ?? '') . "\n";
echo 'message=' . ($v2['message'] ?? '') . "\n";
echo 'connected=' . json_encode($v2['node_details'][0]['status']['connectivity']['connected'] ?? null) . "\n";
echo 'info.name=' . json_encode($v2['node_details'][0]['config']['info']['name'] ?? null) . "\n";
echo 'info.model=' . json_encode($v2['node_details'][0]['config']['info']['model'] ?? null) . "\n";
$pages = $v2['node_details'][0]['config']['devices'][0]['pages'] ?? [];
echo 'page_keys=' . json_encode(array_keys($pages)) . "\n";

$samples = [];
$nullEmpty = 0;
$hasAsperui = 0;
$hasWire = 0;
$hiddenLike = 0;
$totalData = 0;
$looksStaticDefaults = 0;

foreach ($pages as $pn => $frames) {
    if (!is_array($frames)) continue;
    foreach ($frames as $item) {
        $f = $item['frame'] ?? $item;
        if (!is_array($f)) continue;
        $fname = $f['name'] ?? '?';
        foreach (['commands', 'response', 'error'] as $b) {
            foreach ($f[$b] ?? [] as $field) {
                if (!is_array($field)) continue;
                $desc = strtolower(trim((string) ($field['Field_description'] ?? $field['field_description'] ?? $field['default_name'] ?? '')));
                if (!in_array($desc, ['data', 'trigger'], true) && empty($field['ui_type'])) continue;
                $ui = strtolower((string) ($field['ui_type'] ?? 'none'));
                if ($ui === 'none' && !in_array($desc, ['data', 'trigger'], true)) continue;
                $totalData++;
                $v = $field['value'] ?? null;
                $a = $field['asperui_value'] ?? null;
                if ($a !== null && $a !== '') $hasAsperui++;
                if ($v !== null && $v !== '') $hasWire++;
                if (($a === null || $a === '') && ($v === null || $v === '')) $nullEmpty++;
                // "hidden" would mean frame omitted; we still include fields
                if (is_array($a) && $a === []) $hiddenLike++;
                if (is_array($field['asperui'] ?? null) && $field['asperui'] === []) { /* option list empty ok */ }

                if (count($samples) < 12) {
                    $samples[] = [
                        'page' => $pn,
                        'frame' => $fname,
                        'desc' => $desc,
                        'ui' => $field['ui_type'] ?? null,
                        'value' => $v,
                        'asperui_value' => $a,
                        'asperui' => $field['asperui'] ?? null,
                    ];
                }

                // Heuristic: common static seeds
                if ($a === 0 || $a === '0' || $a === 'OFF' || $a === false || $a === 'false' || $v === '00' || $v === '0000') {
                    $looksStaticDefaults++;
                }
            }
        }
    }
}

echo "data_fields=$totalData with_asperui=$hasAsperui with_wire=$hasWire null_both=$nullEmpty empty_asperui_arrays=$hiddenLike staticish=$looksStaticDefaults\n";
echo "samples=" . json_encode($samples, JSON_PRETTY_PRINT) . "\n";

echo "\n=== auto_get for same node ===\n";
echo 'payload=' . (isset($auto['payload']) && $auto['payload'] ? 'YES' : 'NO') . "\n";
echo 'connected=' . json_encode($auto['connected'] ?? null) . "\n";
$pageFound = false;
foreach ($auto ?? [] as $k => $block) {
    if (!is_array($block)) continue;
    foreach ($block as $pn => $vals) {
        if (is_string($pn) && stripos($pn, 'Page') === 0 && is_array($vals)) {
            $pageFound = true;
            echo "deviceKey=$k page=$pn keys=" . json_encode($vals) . "\n";
        }
    }
}
if (!$pageFound) {
    echo "no Page*.Operation block\n";
    echo 'prefix=' . substr(json_encode($auto), 0, 300) . "\n";
}

echo "\n=== api_schema node_details vs controls ===\n";
echo 'build=' . ($schema['meta']['schemaBuildVersion'] ?? '') . "\n";
echo 'v2Handlers=' . json_encode($schema['meta']['v2DisplayHandlers'] ?? $schema['meta']['app']['v2DisplayHandlers'] ?? null) . "\n";
$sp = $schema['node_details'][0]['config']['devices'][0]['pages'] ?? [];
echo 'schema_pages=' . json_encode(array_keys($sp)) . "\n";
echo 'connected=' . json_encode($schema['node_details'][0]['status']['connectivity']['connected'] ?? null) . "\n";

// Compare a few controls
$want = ['power', 'voltage', 'current', 'speed', 'buzzer'];
foreach ($schema['controls'] ?? [] as $c) {
    $name = strtolower((string) ($c['parameterName'] ?? ''));
    $hit = false;
    foreach ($want as $w) if (str_contains($name, $w)) { $hit = true; break; }
    if (!$hit) continue;
    echo 'ctrl ' . ($c['parameterKey'] ?? '') 
        . ' name=' . ($c['parameterName'] ?? '')
        . ' value=' . json_encode($c['value'] ?? null)
        . ' init=' . json_encode($c['initialValue'] ?? null)
        . ' loading=' . json_encode($c['valueLoading'] ?? null)
        . "\n";
}

echo "\n=== SOURCE ANALYSIS ===\n";
echo "V2 builder uses: getDeviceUiInfo (DB mapping api_product_configs/frames/fields + product_api metadata)\n";
echo "  then V2ValueHandlers (typed display from wire/defaults)\n";
echo "  then overlay from auto_get_nodes_params if payload/page present\n";
echo "NOT hardcoded fixed JSON. Defaults come from DB field defaults/wire hex when live overlay missing.\n";
