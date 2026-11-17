<?php
require dirname(__DIR__) . '/Libraries/AppHomeBootstrap.php';
App\Libraries\AppHomeBootstrap::bootIfNeeded();
$db = db_connect();
$rows = $db->query('SELECT DISTINCT model_name FROM api_product_configs LIMIT 15')->getResultArray();
echo "v2_models=" . json_encode($rows) . PHP_EOL;
$n = $db->query("SELECT model_number, model_name FROM efactorproduct WHERE node_id='FFFFFFFFFFFFFFFF7EC200000003BC00' LIMIT 1")->getRowArray();
echo "fan_row=" . json_encode($n) . PHP_EOL;
