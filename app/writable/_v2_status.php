<?php
ini_set('memory_limit', '512M');

function fetch($url) {
    $ctx = stream_context_create([
        'http' => ['timeout' => 60, 'ignore_errors' => true],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    ]);
    $raw = @file_get_contents($url, false, $ctx);
    return is_string($raw) ? $raw : '';
}

$cases = [
    ['label' => 'EF_smart_plug', 'model' => 'EF_smart_plug', 'node' => 'FFFFFFFFFFFFFFFFZEC200000003A600'],
    ['label' => 'fan_7EC2', 'model' => '', 'node' => 'FFFFFFFFFFFFFFFF7EC200000003BC00'],
];

// Try discover fan model from auto_get
$autoFan = fetch('https://efactor.tech/v8/api.php?action=auto_get_nodes_params&node_id=FFFFFFFFFFFFFFFF7EC200000003BC00&device_id=FFFFFFFFFFFFFFFF7EC200000003BC00');
echo "=== auto_get fan sample ===\n";
$j = json_decode($autoFan, true);
echo 'bytes=' . strlen($autoFan) . " payload=" . (isset($j['payload']) && $j['payload'] ? 'YES' : 'NO') . "\n";
if (is_array($j)) {
    foreach ($j as $k => $v) {
        if (is_array($v) && isset($v['Page1.Operation'])) {
            echo "deviceKey=$k page=" . json_encode($v['Page1.Operation']) . "\n";
        }
    }
}

foreach ([
    ['EF_smart_plug', 'FFFFFFFFFFFFFFFFZEC200000003A600'],
    ['Ef-fan-hv-d-15-10', 'FFFFFFFFFFFFFFFF7EC200000003BC00'],
] as [$model, $node]) {
    echo "\n=== SCHEMA $model / $node ===\n";
    $raw = fetch("https://efactor.tech/v8/api_schema.php?model_no=" . rawurlencode($model) . "&node_id=" . rawurlencode($node));
    $d = json_decode($raw, true);
    if (!is_array($d) || empty($d['status'])) {
        echo 'status=' . json_encode($d['status'] ?? null) . ' msg=' . ($d['message'] ?? substr($raw, 0, 120)) . "\n";
        continue;
    }
    $s = $d['data']['schema_json'] ?? null;
    if (is_string($s)) $s = json_decode($s, true);
    echo 'build=' . ($s['meta']['schemaBuildVersion'] ?? '') . "\n";
    echo 'normalizer=' . ($s['meta']['normalizerBuild'] ?? '') . "\n";
    echo 'v2DisplayHandlers=' . json_encode($s['meta']['v2DisplayHandlers'] ?? $s['meta']['app']['v2DisplayHandlers'] ?? null) . "\n";
    echo 'has_v2_node_details=' . (isset($s['v2_node_details']) ? 'Y' : 'N') . "\n";
    echo 'connected=' . json_encode($s['node_details'][0]['status']['connectivity']['connected'] ?? null) . "\n";
    echo 'state.online=' . json_encode($s['state']['online'] ?? null) . "\n";

    $sensorKeys = 0; $resolved = 0; $loading = 0;
    foreach ($s['controls'] ?? [] as $c) {
        $k = strtolower((string) ($c['parameterKey'] ?? ''));
        if (strpos($k, 'voltage') === false && strpos($k, 'current') === false && strpos($k, 'power') === false && strpos($k, 'watt') === false) {
            continue;
        }
        $sensorKeys++;
        if (!empty($c['valueLoading'])) $loading++;
        $v = $c['value'] ?? null;
        if ($v !== null && $v !== '') $resolved++;
        if ($sensorKeys <= 4) {
            echo ' ctrl ' . ($c['parameterKey'] ?? '') . ' value=' . json_encode($v) . ' loading=' . json_encode($c['valueLoading'] ?? null) . "\n";
        }
    }
    echo "sensorish controls=$sensorKeys resolved=$resolved loading=$loading\n";

    $auto = fetch('https://efactor.tech/v8/api.php?action=auto_get_nodes_params&node_id=' . rawurlencode($node) . '&device_id=' . rawurlencode($node));
    $aj = json_decode($auto, true);
    $hasPage = false;
    if (is_array($aj)) {
        foreach ($aj as $k => $v) {
            if (is_array($v) && isset($v['Page1.Operation'])) {
                $hasPage = true;
                echo "auto_get live Page1 keys=" . json_encode(array_keys($v['Page1.Operation'])) . "\n";
                break;
            }
        }
    }
    echo 'auto_get payload=' . (isset($aj['payload']) && $aj['payload'] ? 'YES' : 'NO') . " page=" . ($hasPage ? 'YES' : 'NO') . "\n";
}

echo "\n=== V2 FLOW MEANING ===\n";
echo "Schema path: V2 handlers convert wire hex -> asperui/typed seed into state.parameters on api_schema serve.\n";
echo "Live path (PM): app polls auto_get_nodes_params every 15s and maps human names -> parameterKeys.\n";
echo "New V2 APIs in Studio get schema controls/bindings automatically if model has api_product_configs; live values still need auto_get payload or WS/LAN.\n";
