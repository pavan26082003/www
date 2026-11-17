<?php
ini_set('memory_limit', '512M');

require_once dirname(__DIR__) . '/services/V2ValueHandlers.php';
require_once dirname(__DIR__) . '/services/DeviceUiDisplaySanitizer.php';
require_once dirname(__DIR__) . '/services/DeviceUiSchemaNormalizer.php';

use App\Services\DeviceUiSchemaNormalizer;

$data = json_decode(file_get_contents(__DIR__ . '/_online_schema.json'), true);
$s = $data['data']['schema_json'];
if (is_string($s)) {
    $s = json_decode($s, true);
}

$nodeId = 'FFFFFFFFFFFFFFFFZEC200000003A600';
$model = 'EF_smart_plug';

// Simulate stale wrong topics + offline flag like production dump.
$s['mqtt']['topics']['state'] = 'device/FFFFFFFFFFFFFFFF8200000000000200/state';
$s['mqtt']['topics']['command'] = 'device/FFFFFFFFFFFFFFFF8200000000000200/command';
$s['mqtt']['clientIdPattern'] = 'efactor_FFFFFFFFFFFFFFFF8200000000000200_{clientId}';
$s['websocket']['subscriptions'][0]['topic'] = 'device/FFFFFFFFFFFFFFFF8200000000000200/state';
$s['state']['online'] = false;
$s['node_details'][0]['status']['connectivity']['connected'] = true;
$s['nodes'] = $nodeId;

$out = DeviceUiSchemaNormalizer::normalize($s, $model, $nodeId, 0);

echo "schemaBuildVersion=" . ($out['meta']['schemaBuildVersion'] ?? 'n/a') . "\n";
echo "state.online=" . json_encode($out['state']['online'] ?? null) . "\n";
echo "connected=" . json_encode($out['node_details'][0]['status']['connectivity']['connected'] ?? null) . "\n";
echo "mqtt.state=" . ($out['mqtt']['topics']['state'] ?? '') . "\n";
echo "mqtt.clientId=" . ($out['mqtt']['clientIdPattern'] ?? '') . "\n";
echo "ws.state=" . ($out['websocket']['subscriptions'][0]['topic'] ?? '') . "\n";

foreach (['Voltage (V)', 'Current (A)', 'Total Watt (W)', 'power'] as $want) {
    foreach ($out['deviceConfig']['devices'][0]['pages']['Page1.Operation'] ?? [] as $item) {
        $f = $item['frame'] ?? $item;
        if (($f['name'] ?? '') !== $want) {
            continue;
        }
        foreach ($f['commands'] ?? [] as $field) {
            $d = strtolower((string) ($field['Field_description'] ?? ''));
            if ($d !== 'data') {
                continue;
            }
            echo "frame $want value=" . json_encode($field['value'] ?? null)
                . " asperui=" . json_encode($field['asperui_value'] ?? null) . "\n";
        }
    }
}

$keys = [
    '1_power_command_2_toggle_switch',
    '1_voltage_v_command_0_text',
    '1_current_a_command_0_text',
    '1_total_watt_w_command_0_text',
    '1_units_kw_command_0_text',
    '1_remaining_time_min_command_0_text',
];
foreach ($out['controls'] as $c) {
    $k = $c['parameterKey'] ?? '';
    if (!in_array($k, $keys, true)) {
        continue;
    }
    echo "ctrl $k value=" . json_encode($c['value'] ?? null)
        . " loading=" . json_encode($c['valueLoading'] ?? null)
        . " state=" . json_encode($out['state']['parameters'][$k]['value'] ?? null)
        . "\n";
}

$okTopics = strpos((string) ($out['mqtt']['topics']['state'] ?? ''), $nodeId) !== false;
$okOnline = !empty($out['state']['online']);
$volt = null;
foreach ($out['controls'] as $c) {
    if (($c['parameterKey'] ?? '') === '1_voltage_v_command_0_text') {
        $volt = $c;
        break;
    }
}
$okVolt = is_array($volt) && empty($volt['valueLoading']) && ($volt['value'] === 0 || $volt['value'] === '0' || $volt['value'] === 0.0);
echo "\nPASS topics=" . ($okTopics ? 'Y' : 'N')
    . " online=" . ($okOnline ? 'Y' : 'N')
    . " voltageResolved=" . ($okVolt ? 'Y' : 'N')
    . "\n";
