<?php
ini_set('memory_limit', '512M');
$data = json_decode(file_get_contents(__DIR__ . '/_online_schema.json'), true);
$s = $data['data']['schema_json'];
if (is_string($s)) $s = json_decode($s, true);

$opened = 'FFFFFFFFFFFFFFFFZEC200000003A600';
echo "opened_node=$opened\n";
echo "nodes=" . json_encode($s['nodes'] ?? null) . "\n";
echo "node_details.id=" . json_encode($s['node_details'][0]['id'] ?? null) . "\n";
echo "connected=" . json_encode($s['node_details'][0]['status']['connectivity']['connected'] ?? null) . "\n";
echo "state.online=" . json_encode($s['state']['online'] ?? null) . "\n";
echo "mqtt.state=" . ($s['mqtt']['topics']['state'] ?? '') . "\n";
echo "ws.state=" . (($s['websocket']['subscriptions'][0]['topic'] ?? '')) . "\n";

$loading = [];
$ready = [];
foreach ($s['controls'] ?? [] as $c) {
    $key = $c['parameterKey'] ?? '';
    $want = ['voltage', 'current', 'watt', 'power', 'units', 'remaining'];
    $hit = false;
    foreach ($want as $w) {
        if (stripos($key, $w) !== false) { $hit = true; break; }
    }
    if (!$hit) continue;
    $row = [
        'key' => $key,
        'value' => $c['value'] ?? null,
        'loading' => $c['valueLoading'] ?? null,
        'binding' => $c['binding'] ?? null,
        'flutterBinding' => $c['flutterBinding'] ?? null,
        'state' => $s['state']['parameters'][$key]['value'] ?? ($s['state']['parameters'][$key] ?? null),
    ];
    if (!empty($c['valueLoading'])) $loading[] = $row; else $ready[] = $row;
}
echo "\nREADY sensors/controls:\n" . json_encode($ready, JSON_PRETTY_PRINT) . "\n";
echo "\nLOADING sensors/controls:\n" . json_encode($loading, JSON_PRETTY_PRINT) . "\n";

$tn = json_decode(file_get_contents(__DIR__ . '/_online_test_nodes.json'), true);
$td = $tn['node_details'][0] ?? [];
echo "\ntest_nodes connected=" . json_encode($td['status']['connectivity']['connected'] ?? null) . "\n";
$pages = $td['config']['devices'][0]['pages'] ?? [];
foreach ($pages as $pn => $frames) {
    echo "test_nodes page=$pn frames=" . (is_array($frames) ? count($frames) : 0) . "\n";
}
