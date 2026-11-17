<?php
ini_set('memory_limit', '512M');
$path = __DIR__ . '/_live_check.json';
$raw = file_get_contents($path);
echo 'file_bytes=' . strlen((string) $raw) . "\n";
$d = json_decode($raw, true);
if (!is_array($d)) {
    echo "json=INVALID prefix=" . substr(ltrim((string) $raw), 0, 120) . "\n";
    exit(1);
}
$s = $d['data']['schema_json'] ?? null;
if (is_string($s)) {
    $s = json_decode($s, true);
}
$n = 'FFFFFFFFFFFFFFFFZEC200000003A600';
$bad = 'FFFFFFFFFFFFFFFF8200000000000200';
$mqtt = (string) ($s['mqtt']['topics']['state'] ?? '');
$ws = (string) ($s['websocket']['subscriptions'][0]['topic'] ?? '');
$checks = [
    'status_true' => (($d['status'] ?? null) === true),
    'build_15' => (($s['meta']['schemaBuildVersion'] ?? '') === '2026-11-14.5' || ($s['meta']['normalizerBuild'] ?? '') === '2026-11-14.5'),
    'connected' => !empty($s['node_details'][0]['status']['connectivity']['connected']),
    'state_online_true' => !empty($s['state']['online']),
    'mqtt_topic_ok' => strpos($mqtt, $n) !== false && strpos($mqtt, $bad) === false,
    'ws_topic_ok' => $ws === '' || (strpos($ws, $n) !== false && strpos($ws, $bad) === false),
];
foreach (['1_voltage_v_command_0_text', '1_current_a_command_0_text', '1_total_watt_w_command_0_text'] as $k) {
    $ctrl = null;
    foreach ($s['controls'] ?? [] as $c) {
        if (($c['parameterKey'] ?? '') === $k) {
            $ctrl = $c;
            break;
        }
    }
    $val = $ctrl['value'] ?? null;
    $loading = !empty($ctrl['valueLoading']);
    $checks[$k . '_resolved'] = !$loading && ($val === 0 || $val === 0.0 || $val === '0');
}
echo 'build=' . ($s['meta']['schemaBuildVersion'] ?? '') . "\n";
echo 'normalizer=' . ($s['meta']['normalizerBuild'] ?? '') . "\n";
echo 'connected=' . json_encode($s['node_details'][0]['status']['connectivity']['connected'] ?? null) . "\n";
echo 'state.online=' . json_encode($s['state']['online'] ?? null) . "\n";
echo 'mqtt.state=' . $mqtt . "\n";
echo 'ws.state=' . $ws . "\n";
foreach (['1_power_command_2_toggle_switch', '1_voltage_v_command_0_text', '1_current_a_command_0_text', '1_total_watt_w_command_0_text'] as $k) {
    foreach ($s['controls'] ?? [] as $c) {
        if (($c['parameterKey'] ?? '') !== $k) {
            continue;
        }
        echo $k . ' value=' . json_encode($c['value'] ?? null) . ' loading=' . json_encode($c['valueLoading'] ?? null) . "\n";
    }
}
$fail = [];
foreach ($checks as $name => $ok) {
    echo ($ok ? 'OK ' : 'FAIL ') . $name . "\n";
    if (!$ok) {
        $fail[] = $name;
    }
}
echo $fail === [] ? "VERDICT=IMPLEMENTED_CORRECTLY\n" : ('VERDICT=NOT_FULLY name=' . implode(',', $fail) . "\n");
