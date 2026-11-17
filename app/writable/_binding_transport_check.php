<?php
ini_set('memory_limit', '512M');

$path = __DIR__ . '/_online_schema.json';
if (!is_file($path)) {
    fwrite(STDERR, "missing schema dump\n");
    exit(1);
}

$data = json_decode(file_get_contents($path), true);
$s = $data['data']['schema_json'] ?? null;
if (is_string($s)) {
    $s = json_decode($s, true);
}
if (!is_array($s)) {
    fwrite(STDERR, "bad schema\n");
    exit(1);
}

$withBinding = 0;
$withFlutter = 0;
$both = 0;
$neither = 0;
$mismatch = 0;
$samples = [];

foreach ($s['controls'] ?? [] as $c) {
    if (!is_array($c)) {
        continue;
    }
    $b = $c['binding'] ?? null;
    $f = $c['flutterBinding'] ?? null;
    $hasB = is_string($b) && trim($b) !== '';
    $hasF = is_string($f) && trim($f) !== '';
    if ($hasB) {
        $withBinding++;
    }
    if ($hasF) {
        $withFlutter++;
    }
    if ($hasB && $hasF) {
        $both++;
        if (trim($b) !== trim($f)) {
            $mismatch++;
        }
    }
    if (!$hasB && !$hasF) {
        $neither++;
    }
    if (count($samples) < 8) {
        $key = $c['parameterKey'] ?? ($c['id'] ?? '?');
        $samples[] = [
            'key' => $key,
            'binding' => $b,
            'flutterBinding' => $f,
            'value' => $c['value'] ?? null,
            'valueLoading' => $c['valueLoading'] ?? null,
        ];
    }
}

echo "=== CONTROL BINDINGS ===\n";
echo 'total=' . count($s['controls'] ?? []) . "\n";
echo "with binding=$withBinding\n";
echo "with flutterBinding=$withFlutter\n";
echo "both=$both mismatch=$mismatch neither=$neither\n";
echo "samples=" . json_encode($samples, JSON_PRETTY_PRINT) . "\n";

echo "\n=== MQTT BLOCK ===\n";
echo json_encode($s['mqtt'] ?? null, JSON_PRETTY_PRINT) . "\n";

echo "\n=== WEBSOCKET BLOCK ===\n";
echo json_encode($s['websocket'] ?? null, JSON_PRETTY_PRINT) . "\n";

echo "\n=== DATASOURCES (deviceState / deviceCommands / related) ===\n";
$ds = $s['dataSources'] ?? [];
foreach ($ds as $name => $cfg) {
    $n = strtolower((string) $name);
    if (strpos($n, 'state') === false && strpos($n, 'command') === false && strpos($n, 'mqtt') === false && strpos($n, 'socket') === false && strpos($n, 'poll') === false && strpos($n, 'node') === false) {
        continue;
    }
    echo "-- $name --\n";
    echo json_encode($cfg, JSON_PRETTY_PRINT) . "\n";
}

echo "\n=== LIVE STATE CONTRACT ===\n";
echo 'deviceState=' . json_encode($s['deviceState'] ?? null, JSON_PRETTY_PRINT) . "\n";
echo 'state.online=' . json_encode($s['state']['online'] ?? null) . "\n";
echo 'connected=' . json_encode($s['node_details'][0]['status']['connectivity']['connected'] ?? null) . "\n";
echo 'nodes=' . json_encode($s['nodes'] ?? null) . "\n";
echo 'node_details.id=' . json_encode($s['node_details'][0]['id'] ?? null) . "\n";

// Topic node ids
$mqttState = (string) ($s['mqtt']['topics']['state'] ?? '');
$wsTopics = [];
foreach ($s['websocket']['subscriptions'] ?? [] as $sub) {
    if (is_array($sub) && !empty($sub['topic'])) {
        $wsTopics[] = $sub['topic'];
    }
}
echo "\nmqtt.state topic=$mqttState\n";
echo 'websocket topics=' . json_encode($wsTopics) . "\n";

$requested = 'FFFFFFFFFFFFFFFFZEC200000003A600';
$wrong = 'FFFFFFFFFFFFFFFF8200000000000200';
echo 'mqtt uses requested node=' . (strpos($mqttState, $requested) !== false ? 'YES' : 'NO') . "\n";
echo 'mqtt uses studio/other node=' . (strpos($mqttState, $wrong) !== false ? 'YES' : 'NO') . "\n";
foreach ($wsTopics as $t) {
    echo "ws topic=$t requested=" . (strpos($t, $requested) !== false ? 'YES' : 'NO')
        . ' other=' . (strpos($t, $wrong) !== false ? 'YES' : 'NO') . "\n";
}
