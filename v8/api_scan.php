<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once("config.php");

/*
|--------------------------------------------------------------------------
| GET API
|--------------------------------------------------------------------------
| Example:
| GET /api_scan.php?model_no=EFGATEW%20V1.0&node_id=pavan
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $model_no = isset($_GET['model_no']) ? trim($_GET['model_no']) : "";
    $node_id = isset($_GET['node_id']) ? trim($_GET['node_id']) : "";

    if ($model_no == "") {
        echo json_encode([
            "status" => false,
            "message" => "Model Number is required"
        ]);
        exit;
    }

    $sql = "SELECT * FROM device_scan WHERE model_no='".$db->escape($model_no)."'";

    if ($node_id != "") {
        $sql .= " AND node_id='".$db->escape($node_id)."'";
    }

    $sql .= " ORDER BY id DESC LIMIT 1";

    $scan = $db->get_row($sql);

    if ($scan) {

        echo json_encode([
            "status" => true,
            "message" => "Latest scan found",
            "data" => $scan
        ]);

    } else {

        echo json_encode([
            "status" => false,
            "message" => "No scan found"
        ]);

    }

    exit();
}

/*
|--------------------------------------------------------------------------
| POST API
|--------------------------------------------------------------------------
| Example:
| POST /api_scan.php
|--------------------------------------------------------------------------
*/

$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

if (!is_array($data)) {
    $data = [];
}

$model_no = isset($data['model_no']) ? trim($data['model_no']) : "";
$node_id = isset($data['node_id']) ? trim($data['node_id']) : "";
$device_id = isset($data['device_id']) ? trim($data['device_id']) : "";
$qr_value  = isset($data['qr_value']) ? trim($data['qr_value']) : "";

if ($model_no == "" || $node_id == "") {

    echo json_encode([
        "status" => false,
        "message" => "model_no and node_id are required"
    ]);

    exit();
}

$data['node_id'] = $node_id;
$scan_data = json_encode($data);

$result = $db->insert("device_scan", [

    "model_no"  => $model_no,
    "node_id"   => $node_id,
    "device_id" => $device_id,
    "qr_value"  => $qr_value,
    "scan_data" => $scan_data,
    "ip_address"=> $_SERVER['REMOTE_ADDR']

]);

if ($result) {

    echo json_encode([

        "status" => true,
        "message" => "Scan stored successfully",
        "scan_id" => $db->lastid()

    ]);

} else {

    echo json_encode([

        "status" => false,
        "message" => "Database insert failed"

    ]);
}
