<?php
$_GET['node_id'] = $argv[1] ?? 'FFFFFFFFFFFFFFFF7EC200000003BC00';
$_GET['model_no'] = $argv[2] ?? 'Ef-fan-hv-d-15-10';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['SCRIPT_NAME'] = '/v8/v2_test_nodes.php';
$_SERVER['REQUEST_URI'] = '/v8/v2_test_nodes.php';

ob_start();
include dirname(__DIR__, 2) . '/v8/v2_test_nodes.php';
$out = ob_get_clean();
echo substr((string) $out, 0, 1500) . "\n";
$j = json_decode($out, true);
echo 'json_ok=' . (is_array($j) ? 'Y' : 'N') . "\n";
if (is_array($j)) {
    echo 'status=' . json_encode($j['status'] ?? null) . "\n";
    echo 'source=' . ($j['source'] ?? '') . "\n";
    echo 'nodes=' . json_encode($j['nodes'] ?? null) . "\n";
    echo 'details=' . (isset($j['node_details']) && is_array($j['node_details']) ? count($j['node_details']) : 0) . "\n";
    $pages = $j['node_details'][0]['config']['devices'][0]['pages'] ?? [];
    echo 'page_keys=' . json_encode(is_array($pages) ? array_keys($pages) : []) . "\n";
    echo 'connected=' . json_encode($j['node_details'][0]['status']['connectivity']['connected'] ?? null) . "\n";
}
