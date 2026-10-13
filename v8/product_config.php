<?php

$response = array();
include('config.php');
include('functions.php');
include('phpMQTT.php');
header('Content-Type: application/json');

header('Content-Type: application/x-www-form-urlencoded');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

$req = file_get_contents('php://input');
$json_data = json_decode($req);
header('Content-Type: application/json');
header('Authorization: API Key');

/*-----------------------MQTT--------------------------*/

$server = 'efactor.tech';
$port = 1883;
$username = 'efactorpw';
$password = '123456';
$client_id = 'phpMQTT-publisher';

/*----------------------------mail-------------------------*/
require_once 'vendor/autoload.php';
$mail = new PHPMailer\PHPMailer\PHPMailer();

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'factorenovations23@gmail.com';
$mail->Password = 'cuqvonplawmksoyb';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$req = file_get_contents('php://input');
$json_data = json_decode($req, true);

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');
$efactor = 'Efactor';
$things = 'things';
$command = 'command';

/*******************************/
function stringToAscii($str) {
    $asciiArray = [];
    for ($i = 0; $i < strlen($str); $i++) {
        $asciiArray[] = ord($str[$i]);
    }
    return implode(',', $asciiArray);
}

function asciiToHex($asciiString) {
    $asciiValues = explode(',', $asciiString);
    $hexArray = [];
    foreach ($asciiValues as $ascii) {
        $hexArray[] = dechex($ascii);
    }
    return implode(' ', $hexArray);
}

function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] " . $message;
    echo "<pre>" . htmlspecialchars($logEntry) . "</pre>";
    error_log($logEntry);
    ob_flush();
    flush();
}

function hexToFloat($hex) {
    $hex = str_pad($hex, 8, '0', STR_PAD_LEFT);
    $reversedHex = implode('', array_reverse(str_split($hex, 2)));
    $binary = pack('H*', $reversedHex);
    $float = unpack('f', $binary);
    return $float[1];
}

function float_to_hex_big_endian($float) {
    $binary = pack('G', $float);
    $hex = strtoupper(bin2hex($binary));
    return $hex;
}

function sendMqttMessage($mqtt, $nodeid, $deviceid, $payload) {
    global $efactor, $things, $command;

    if ($address != 'NULL') {
        $topic = "$efactor/$things/$nodeid/$command";
        $mqtt_json = array(
            "command_type" => "GET_DEVICE_STATUS",
            "address" => $nodeid,
            "device_id" => $deviceid,
            "payload" => $payload
        );
        $mqtt_json_1 = json_encode($mqtt_json);
        if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
        }
    } else {
        if ($deviceid != 'NULL') {
            $topic = "$efactor/$things/$deviceid/$command";
            $mqtt_json = array(
                "command_type" => "GET_DEVICE_STATUS",
                "address" => $deviceid,
                "device_id" => $deviceid,
                "payload" => $payload
            );
            $mqtt_json_1 = json_encode($mqtt_json);
            if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
                $mqtt->publish($topic, $mqtt_json_1);
            }
        }
    }
}

/*---------------------------------------------------------*/

// Disable output buffering
while (ob_get_level()) {
    ob_end_flush();
}

header('Content-Type: application/json');
header('Cache-Control: no-cache');
header('Access-Control-Allow-Origin: *');

/*-----------------------------------------------------------------------------------*/

if ($_POST['action'] === 'config_stop_operation') {
    $_SESSION['active_operation'] = false;
    echo "Operation stopped for model: " . $_POST['model_number'];
}

/*-----------------------------------------------------------------------------------*/

if (isset($_GET['action']) && $_GET['action'] == 'Product_config') {
    set_time_limit(0);

    $model_name = $_GET['model_number'];
    sleep(6);

    logMessage("========== PRODUCT CONFIG STARTED ==========");
    logMessage("Model: " . $model_name);

    echo "Configuration Started...\n";
    ob_flush();
    flush();

    $multiple_nodes_query = "SELECT * FROM config_table WHERE model_name = '$model_name'";
    $nodes_query = $db->get_results($multiple_nodes_query);

    logMessage("Found " . count($nodes_query) . " configuration(s)");

    foreach ($nodes_query as $node_data) {
        if ($node_data['mode'] == 'Auto test') {
            // AUTO TEST MODE (kept as is)
            $node_ids_string = $node_data['node_id'];
            $node_ids = explode(',', $node_ids_string);
            $user_id = $node_data['user_id'];
            $modelSelect = $node_data['model_name'];
            $router = $node_data['router_id'];

            foreach ($node_ids as $node_id) {
                echo $node_id . " once in a loop ";

                $query_mqtt = "SELECT * FROM autotest WHERE modelno='$model_name'";
                $results = $db->get_row($query_mqtt);

                if (empty($results)) {
                    echo "No results found!";
                    exit;
                }

                $cmd_param_names = $results['cmd_param_names'];
                $cmd_values = $results['cmd_values'];
                $cmd_time = $results['cmd_time'];
                $cmd_startbyte = $results['cmd_startbyte'];

                $cmd_param_names_array = array_map('trim', explode(',', trim($cmd_param_names, '[]')));
                $cmd_time_array = array_map('trim', explode(',', trim($cmd_time, '[]')));
                $cmd_startbyte = trim($cmd_startbyte, '[]');

                $cmd_startbyte_array = explode('],[', $cmd_startbyte);
                foreach ($cmd_startbyte_array as $key => $value) {
                    $cmd_startbyte_array[$key] = $value === '' ? [] : array_map('trim', explode(',', $value));
                }

                $cmd_values = trim($cmd_values, '[]');
                $cmd_values_array = explode('],[', $cmd_values);
                foreach ($cmd_values_array as $key => $value) {
                    $cmd_values_array[$key] = $value === '' ? [] : array_map('trim', explode(',', $value));
                }

                $response_array = [];
                $commands = [];

                foreach ($cmd_param_names_array as $index => $param) {
                    if (empty($param)) continue;

                    $commands[] = [
                        'parameter_name' => $param,
                        'value' => $cmd_values_array[$index] ?? null,
                        'time' => $cmd_time_array[$index] ?? null,
                        'startbyte' => $cmd_startbyte_array[$index] ?? null,
                    ];

                    $param = trim($param, '[]');
                    $cmd_time_array[$index] = trim($cmd_time_array[$index], '[]');

                    if (!empty($param)) {
                        $query_mqtt = "SELECT * FROM product_api WHERE model_name='$model_name' AND parameter_name='$param'";
                        $row_mqtt = $db->get_row($query_mqtt);

                        if (empty($row_mqtt)) continue;

                        $parameter_name = $row_mqtt['parameter_name'];
                        $cmd_framesize = $row_mqtt['cmd_framesize'];
                        $cmd_value_min = str_replace(' ', '', $row_mqtt['cmd_valueinhexmin']);
                        $cmd_value_max = str_replace(' ', '', $row_mqtt['cmd_valueinhexmax']);
                        $cmd_startbytes = $row_mqtt['cmd_startbytePosition'];
                        $cmd_value_min_array = explode(',', $cmd_value_min);
                        $cmd_value_max_array = explode(',', $cmd_value_max);
                        $cmd_db_startbyte = explode(',', $cmd_startbytes);

                        $form_startbyte = $cmd_startbyte_array[$index];
                        $form_values = $cmd_values_array[$index];
                        $form_wait_time = $cmd_time_array[$index];

                        $db_assoc_array = [];
                        foreach ($cmd_db_startbyte as $i => $key) {
                            $db_assoc_array[$key] = $cmd_value_min_array[$i] ?? '';
                        }

                        $form_assoc_array = [];
                        foreach ($form_startbyte as $i => $key) {
                            $form_assoc_array[$key] = $form_values[$i] ?? '';
                        }

                        foreach ($db_assoc_array as $key => $value) {
                            if (isset($form_assoc_array[$key])) {
                                $db_assoc_array[$key] = $form_assoc_array[$key];
                            }
                        }

                        $payload = '';
                        foreach ($db_assoc_array as $value) {
                            $payload .= $value;
                        }

                        if ($node_data['status'] == 'Online') {
                            $mqtt_message = json_encode([
                                'command_type' => 'GET_DEVICE_STATUS',
                                'payload' => $payload,
                            ]);

                            $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

                            if (!$mqtt->connect(true, NULL, $username, $password)) {
                                exit;
                            }

                            $topic = "Efactor/things/{$node_id}/command";
                            $mqtt->publish($topic, $mqtt_message);
                            $mqtt->close();
                        } else {
                            $networkInfos = [];
                            for ($i = 0; $i < 3; $i++) {
                                sleep(3);
                                $query = "SELECT * FROM `$modelSelect` WHERE node_id = '$node_id1'";
                                $row_param = $db->get_row($query);
                                $networkInfos[] = $row_param['network_info'];
                                $network = $row_param['network_info'];
                                if (strpos($network, 'OK') !== false) {
                                    $foundOK = true;
                                    break;
                                }
                            }

                            if ($foundOK) {
                                $mqtt_message = json_encode([
                                    'command_type' => 'GET_DEVICE_STATUS',
                                    'address' => $router,
                                    'device_id' => $node_id1,
                                    'payload' => $payload
                                ]);

                                $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

                                if (!$mqtt->connect(true, NULL, $username, $password)) {
                                    exit("Unable to connect to MQTT broker");
                                }

                                $topic = "Efactor/things/{$node_id1}/command";
                                $mqtt->publish($topic, $mqtt_message_off);
                                sleep(3);
                                $mqtt->publish($topic, $mqtt_message);
                                sleep(3);
                                $mqtt->publish($topic, $mqtt_message_on);
                                $mqtt->close();
                            } else {
                                break;
                            }
                        }

                        if ($form_wait_time > 0) {
                            sleep($form_wait_time);
                        }

                        $query_testing = "SELECT auto_testing FROM `$model_name` WHERE node_id = '$node_id'";
                        $result = $db->get_row($query_testing);

                        if ($result) {
                            $auto_testing_value = $result['auto_testing'];
                            if (strtolower($auto_testing_value) == 'tested') {
                                $response_array[] = "Success " . $param;
                            } else {
                                $response_array[] = "Failed " . $param;
                            }
                        }
                    }
                }

                $response_json = json_encode($response_array);
                echo $response_json;
            }
        } else {
            // ========== CONFIGURATION MODE ==========
            logMessage("========== CONFIGURATION MODE ==========");

            $keys2 = $node_data['config_keys'];
            $values2 = $node_data['configValues'];
            $user_id = $node_data['user_id'];
            $modelSelect = $node_data['model_name'];
            $node_ids2 = $node_data['node_id'];
            $router = $node_data['router_id'];

            logMessage("Router: " . $router . " | Model: " . $modelSelect);

            $keys = explode(',', $keys2);
            $values = explode(',', $values2);
            $node_ids22 = explode(',', $node_ids2);
            $node_ids42 = "'" . implode("','", $node_ids22) . "'";
            $form_entered_values = array_combine($keys, $values);

            $set_query = [];
            foreach ($keys as $index => $key) {
                $value = $values[$index];

                if ($value === '') {
                    $value = 'NULL';
                }

                if (strpos($key, ' ') !== false) {
                    $set_query[] = "`$key` = '$value'";
                } else if (strpos($value, '.') !== false) {
                    $value = float_to_hex_big_endian($value);
                    $set_query[] = "$key = '$value'";
                } else {
                    $value = dechex(intval($value));
                    $set_query[] = "$key = '$value'";
                }
            }

            $set_clause = implode(", ", $set_query);
            $keys_placeholder = "'" . implode("', '", $keys) . "'";
            $param_sql = "SELECT parameter_name, cmd_defaultvalue FROM product_api WHERE model_name = '$modelSelect'";
            $result_221 = $db->get_results($param_sql);

            $filteredResults = [];
            foreach ($result_221 as $row) {
                $cmd_defaultvalue = $row->cmd_defaultvalue;
                foreach ($keys_placeholder as $key) {
                    if (preg_match('/(?:^|,)' . preg_quote($key, '/') . '(?:,|$)/', $cmd_defaultvalue)) {
                        $filteredResults[] = $row;
                        break;
                    }
                }
            }

            foreach ($result_221 as $index => &$item) {
                $cmd_values = explode(',', $item['cmd_defaultvalue']);
                $cmd_values = array_filter($cmd_values, function ($value) {
                    return !empty($value);
                });
                $matches = array_intersect($cmd_values, $keys);
                if (!empty($matches)) {
                    $item['cmd_defaultvalue'] = implode(',', $matches);
                } else {
                    unset($result_221[$index]);
                }
            }
            $result_221 = array_values($result_221);
            foreach ($result_221 as &$item) {
                $cmd_values = explode(',', $item['cmd_defaultvalue']);
                foreach ($cmd_values as &$cmd_value) {
                    if (array_key_exists($cmd_value, $form_entered_values)) {
                        $cmd_value = $form_entered_values[$cmd_value];
                    }
                }
                $item['cmd_defaultvalue'] = implode(',', $cmd_values);
            }

            $startByte_sql = "SELECT cmd_startbytePosition, cmd_endbytePosition, cmd_defaultvalue, cmd_datatype FROM product_api WHERE model_name = '$modelSelect'";
            $result_222 = $db->get_results($startByte_sql);
            $filteredResults = [];

            foreach ($result_222 as &$item) {
                $defaultValues = explode(',', $item['cmd_defaultvalue']);
                $startBytePositions = explode(',', $item['cmd_startbytePosition']);
                $endBytePositions = explode(',', $item['cmd_endbytePosition']);
                $dataTypes = explode(',', $item['cmd_datatype']);

                $filteredDefaultValues = [];
                $filteredStartBytePositions = [];
                $filteredEndBytePositions = [];
                $filteredDataTypes = [];

                foreach ($defaultValues as $index => $value) {
                    if (!empty(trim($value))) {
                        $filteredDefaultValues[] = $value;
                        $filteredStartBytePositions[] = $startBytePositions[$index];
                        $filteredEndBytePositions[] = $endBytePositions[$index];
                        $filteredDataTypes[] = $dataTypes[$index];
                    }
                }

                $item['cmd_defaultvalue'] = implode(',', $filteredDefaultValues);
                $item['cmd_startbytePosition'] = implode(',', $filteredStartBytePositions);
                $item['cmd_endbytePosition'] = implode(',', $filteredEndBytePositions);
                $item['cmd_datatype'] = implode(',', $filteredDataTypes);
            }

            foreach ($result_222 as $index => &$item) {
                $cmd_values = explode(',', $item['cmd_defaultvalue']);
                $cmd_values = array_filter($cmd_values, function ($value) {
                    return !empty($value);
                });
                $matches = array_intersect($cmd_values, $keys);
                if (!empty($matches)) {
                    $item['cmd_defaultvalue'] = implode(',', $matches);
                    $startbytePositions = explode(',', $item['cmd_startbytePosition']);
                    $endbytePositions = explode(',', $item['cmd_endbytePosition']);
                    $dataTypes = explode(',', $item['cmd_datatype']);
                    $filteredStartPositions = [];
                    $filteredEndPositions = [];
                    $filteredTypes = [];

                    foreach ($matches as $match) {
                        $index = array_search($match, $cmd_values);
                        if ($index !== false) {
                            $filteredStartPositions[] = $startbytePositions[$index];
                            $filteredEndPositions[] = $endbytePositions[$index];
                            $filteredTypes[] = $dataTypes[$index];
                        }
                    }

                    $item['cmd_startbytePosition'] = implode(',', $filteredStartPositions);
                    $item['cmd_endbytePosition'] = implode(',', $filteredEndPositions);
                    $item['cmd_datatype'] = implode(',', $filteredTypes);
                } else {
                    unset($result_222[$index]);
                }
            }
            $result_222 = array_values($result_222);

            foreach ($result_221 as $item) {
                $parameters[] = $item['parameter_name'];
                $cmd_defaultvalues[] = $item['cmd_defaultvalue'];
            }

            $cmd_startbyte = [];
            $cmd_endbyte = [];
            $cmd_datatype = [];

            foreach ($result_222 as $item) {
                $cmd_startbyte[] = $item['cmd_startbytePosition'];
                $cmd_endbyte[] = $item['cmd_endbytePosition'];
                $cmd_datatype[] = $item['cmd_datatype'];
            }

            foreach ($cmd_defaultvalues as $index => &$value) {
                if (is_string($value)) {
                    $value = explode(',', $value);
                }
            }

            $cmd_defaultvalues_copy = $cmd_defaultvalues;

            foreach ($cmd_startbyte as $index => &$value) {
                if (is_string($value)) {
                    $value = explode(',', $value);
                }
            }
            foreach ($cmd_defaultvalues as $index => &$value) {
                if (is_string($value)) {
                    $value = explode(',', $value);
                }
            }

            foreach ($cmd_datatype as $index => $data_types) {
                foreach ($data_types as $key => $data_type) {
                    // Processing data types
                }
            }

            $processed_cmd_datatype = [];
            foreach ($cmd_datatype as $index => $data_type) {
                if (strpos($data_type, ',') !== false) {
                    $processed_cmd_datatype[$index] = explode(',', $data_type);
                } else {
                    $processed_cmd_datatype[$index] = [$data_type];
                }
            }

            $converted_values = [];
            foreach ($cmd_defaultvalues_copy as $index => $default_values) {
                $data_types = $processed_cmd_datatype[$index] ?? [];
                $converted_values[$index] = [];

                foreach ($default_values as $key => $value) {
                    $data_type = $data_types[$key] ?? null;

                    if ($data_type === 'UnsignedInt') {
                        if ($value == '16000' || $value == '7000') {
                            $converted_values[$index][$key] = str_pad(strtoupper(dechex($value)), 8, '0', STR_PAD_LEFT);
                        } else {
                            $converted_values[$index][$key] = str_pad(strtoupper(dechex($value)), 2, '0', STR_PAD_LEFT);
                        }
                    } elseif ($data_type === 'Float') {
                        $binary_float = pack('f', $value);
                        $binary_little_endian = strrev($binary_float);
                        $hex_float = bin2hex($binary_little_endian);
                        $converted_values[$index][$key] = strtoupper($hex_float);
                    } else {
                        $converted_values[$index][$key] = $value;
                    }
                }
            }

            /**************************** E_SCAN ******************************/
            logMessage("Starting E_SCAN on router: " . $router);

            // Send E_SCAN command
            $mqtt_message = json_encode([
                'command_type' => 'E_SCAN',
                'address' => $router,
                'device_id' => $router,
                'payload' => "SCAN"
            ]);

            $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

            if (!$mqtt->connect(true, NULL, $username, $password)) {
                logMessage("ERROR: Unable to connect to MQTT broker for E_SCAN");
                exit("Unable to connect to MQTT broker");
            }

            $topic = "Efactor/things/{$router}/command";
            $mqtt->publish($topic, $mqtt_message);
            $mqtt->close();
            logMessage("E_SCAN command sent, waiting for devices...");

            // Reset E_Scan field
            $db->query("update efactorproduct set E_Scan='NULL' where node_id='$router'");
            sleep(10);

            // Retrieve E_Scan results
            $E_Scan = [];
            $router_ids = "SELECT * FROM efactorproduct WHERE node_id = '$router'";
            $router_id = $db->get_row($router_ids);
            $E_Scan[] = $router_id['E_Scan'];

            $match_found = false;
            $scan_values = explode(',', $E_Scan[0]);
            $scan_values = array_filter(array_map('trim', $scan_values));

            logMessage("Found " . count($scan_values) . " device(s) in scan");

            $node_id_matched = [];

            // Match nodes
            foreach ($node_ids22 as $node_id21) {
                $node_last_3 = substr($node_id21, -5, 3);
                $node_last_4 = substr($node_id21, -6, 4);
                $node_last_6 = substr($node_id21, -8, 6);

                foreach ($scan_values as $scan) {
                    $scan_value = trim($scan);
                    if (empty($scan_value)) continue;

                    $scan_last_3 = substr($scan_value, -3);
                    $scan_last_4 = substr($scan_value, -4);
                    $scan_last_6 = substr($scan_value, -6);

                    if ($scan_last_3 === $node_last_3 ||
                        $scan_last_4 === $node_last_4 ||
                        $scan_last_6 === $node_last_6 ||
                        strpos($node_id21, $scan_last_3) !== false ||
                        strpos($node_id21, $scan_last_4) !== false) {

                        $node_id_matched[] = $node_id21;
                        $match_found = true;
                        break;
                    }
                }
            }

            if (!$match_found || empty($node_id_matched)) {
                logMessage("No matches found, using all configured nodes");
                $node_id_matched = $node_ids22;
            }

            logMessage("Matched " . count($node_id_matched) . " node(s) for configuration");

            /****************************************************************/
            // The 8 standard commands
          /* $eight_commands = [
                ['name' => 'Set RYB High Voltage', 'payload' => 'A1F60701C201C201C20A'],
                ['name' => 'Set RYB Low Voltage', 'payload' => 'A1F608017C017C017C0A'],
                ['name' => 'Set RYB High Current', 'payload' => 'A1F6036464640A'],
                ['name' => 'Set RYB Low Current', 'payload' => 'A1F6046464640A'],
                ['name' => 'Phase Seq Enable', 'payload' => 'A1F605010A'],
                ['name' => '1 ph Preventer Enable', 'payload' => 'A1F606010A'],
                ['name' => 'Device Set Max Current', 'payload' => 'A1F2031E0A'],
                ['name' => 'Start Relay Time', 'payload' => 'A1F601050A']
            ];*/
            
            
            
            $eight_commands = [
                ['name' => 'Set RYB High Voltage', 'payload' => '55AA0006000E0502000A01FF0000FFFFFF640A640A'],
                ['name' => 'Set RYB Low Voltage', 'payload' => '55AA0006000E0502000A010000FFFFFFFF640A640A']
             
            ];
            
            
            

            /****************************************************************/
// PROCESS EACH MATCHED NODE
foreach ($node_id_matched as $node_id1) {
    logMessage("----------------------------------------");
    logMessage("Configuring Node: " . $node_id1);
    logMessage("----------------------------------------");

    // Get node type from configuration
   // $nodeType = $node_data['node_type'] ?? 'default';
   // logMessage("Node Type: " . $nodeType);
    
    // ============================================================
    // GENERATE SSID BASED ON NODE TYPE
    // ============================================================
  // 10-byte = 20 characters, 16-byte = 32 characters
$nodeIdLength = strlen($node_id1);
logMessage("Node ID Length: " . $nodeIdLength);

if ($nodeIdLength == 20) {
    // 10-byte nodes: Use bytes 5-8 (positions 8,10,12,14)
    $segment1 = substr($node_id1, 8, 2);   // Byte 5
    $segment2 = substr($node_id1, 10, 2);  // Byte 6
    $segment3 = substr($node_id1, 12, 2);  // Byte 7
    $segment4 = substr($node_id1, 14, 2);  // Byte 8
    
    $wifissid = 'EWIFI_' . $segment1 . $segment2 . $segment3 . $segment4;
    logMessage("SSID (10-byte detected): " . $wifissid);
    logMessage("Segments: " . $segment1 . "-" . $segment2 . "-" . $segment3 . "-" . $segment4);
} else {
    // Other node types: Use bytes at positions 16,24,26,28
    $segment9 = substr($node_id1, 16, 2);
    $segment13 = substr($node_id1, 24, 2);
    $segment14 = substr($node_id1, 26, 2);
    $segment15 = substr($node_id1, 28, 2);
    
    $wifissid = 'EWIFI_' . $segment9 . $segment13 . $segment14 . $segment15;
    logMessage("SSID (default): " . $wifissid);
    logMessage("Segments: " . $segment9 . "-" . $segment13 . "-" . $segment14 . "-" . $segment15);
}

    // Convert SSID to hex for CD01 payload
    $ssidAscii = stringToAscii($wifissid);
    $hexResult = asciiToHex($ssidAscii);
    $hexResultNoSpaces = strtoupper(str_replace(' ', '', $hexResult));
    $payload_cd01 = 'CD01' . $hexResultNoSpaces;
    logMessage("CD01 Payload: " . $payload_cd01);

    // Send CD01
    logMessage("Sending CD01...");
    $mqtt_message = json_encode([
        'command_type' => 'GET_DEVICE_STATUS',
        'address' => $router,
        'device_id' => $node_id1,
        'payload' => $payload_cd01
    ]);

    $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

    if (!$mqtt->connect(true, NULL, $username, $password)) {
        logMessage("ERROR: Failed to connect to MQTT broker for CD01");
        continue;
    }

    $topic = "Efactor/things/{$router}/command";
    $mqtt->publish($topic, $mqtt_message);
    $mqtt->close();
    logMessage("CD01 sent, waiting 30 seconds for network establishment...");
    sleep(10);
                
                
                

                // ============================================================
                // CHECK FOR NETWORK ESTABLISHED (CD00DA030A)
                // ============================================================
                logMessage("Checking network status for node: " . $node_id1);

                // Try multiple times to get network status
                $network_ok = false;
                $network_established = false;

                for ($attempt = 1; $attempt <= 5; $attempt++) {
                    sleep(3);
                    $query_network = "SELECT * FROM `$modelSelect` WHERE node_id = '$node_id1'";
                    $row_param_network = $db->get_row($query_network);
                    $network = $row_param_network['network_info'] ?? '';
                    $auto_testing = $row_param_network['auto_testing'] ?? '';

                    logMessage("Attempt $attempt - Network: '$network' | Auto_testing: '$auto_testing'");

                    // Check for CD00DA030A in auto_testing (network established)
                    if (strpos($auto_testing, 'CD00DA030A') !== false) {
                        $network_established = true;
                        $network_ok = true;
                        logMessage("✅ CD00DA030A received - Network established!");
                        break;
                    }

                    // Check for OK in network_info
                    if (strpos($network, 'OK') !== false || strpos($network, 'ok') !== false) {
                        $network_ok = true;
                        logMessage("✅ Network OK!");
                        break;
                    }

                    if ($network == '1' || $network == 'connected') {
                        $network_ok = true;
                        logMessage("✅ Network connected!");
                        break;
                    }
                }

                // ============================================================
                // SEND CONFIGURATION COMMANDS
                // ============================================================
                if ($network_ok) {
                    logMessage("================================================");
                    logMessage("Network established - Starting configuration");
                    logMessage("================================================");

                    // STEP 1: Send OFF command
                    logMessage("📤 Sending OFF command...");
                   $mqtt_message_off = json_encode([
                        'command_type' => 'GET_DEVICE_STATUS',
                        'address' => $router,
                        'device_id' => $node_id1,
                        'payload' => 'EC0155AA0006000E0502000A0100FF00FFFFFF640A640A'
                    ]);
                    
                  /*  $mqtt_message_off = json_encode([
                        'command_type' => 'GET_DEVICE_STATUS',
                        'address' => $router,
                        'device_id' => $node_id1,
                        'payload' => 'BC32'
                    ]);*/
                    

                    $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);
                    if ($mqtt->connect(true, NULL, $username, $password)) {
                        $topic = "Efactor/things/{$router}/command";
                        $mqtt->publish($topic, $mqtt_message_off);
                        $mqtt->close();
                        logMessage("  ✅ OFF command sent");
                        sleep(3);
                    } else {
                        logMessage("  ❌ Failed to send OFF command");
                        continue;
                    }
                    
                    
                
                    
                    
                    
                    
                    
                    

                    // STEP 2: Send main configuration parameters
                    logMessage("📤 Sending main configuration parameters...");
                    $config_count = 0;
                  /*  foreach ($parameters as $index => $param) {
                        if (empty($param)) continue;

                        if (isset($cmd_defaultvalues_copy[$index])) {
                            $def_value = $converted_values[$index];
                        } else {
                            continue;
                        }

                        if (!empty($param)) {
                            $query_mqtt = "SELECT * FROM product_api WHERE model_name='$modelSelect' AND parameter_name='$param'";
                            $row_mqtt = $db->get_row($query_mqtt);
                            if (empty($row_mqtt)) continue;

                            $cmd_startbytes = $row_mqtt['cmd_startbytePosition'];
                            $cmd_value_min = str_replace(' ', '', $row_mqtt['cmd_valueinhexmin']);
                            $cmd_value_min_array = explode(',', $cmd_value_min);
                            $cmd_db_startbyte = explode(',', $cmd_startbytes);

                            $form_startbyte = $cmd_startbyte[$index] ?? [];
                            $form_values = $def_value;

                            $db_assoc_array = [];
                            foreach ($cmd_db_startbyte as $i => $key) {
                                $db_assoc_array[$key] = $cmd_value_min_array[$i] ?? '';
                            }

                            $form_assoc_array = [];
                            foreach ($form_startbyte as $i => $key) {
                                $form_assoc_array[$key] = $form_values[$i] ?? '';
                            }

                            foreach ($db_assoc_array as $key => $value) {
                                if (isset($form_assoc_array[$key])) {
                                    $db_assoc_array[$key] = $form_assoc_array[$key];
                                }
                            }

                            $payload = '';
                            foreach ($db_assoc_array as $value) {
                                $payload .= $value;
                            }

                            $config_count++;

                            $mqtt_message = json_encode([
                                'command_type' => 'GET_DEVICE_STATUS',
                                'address' => $router,
                                'device_id' => $node_id1,
                                'payload' => 'EC01' . $payload
                            ]);

                            $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

                            if ($mqtt->connect(true, NULL, $username, $password)) {
                                $topic = "Efactor/things/{$router}/command";
                                $mqtt->publish($topic, $mqtt_message);
                                $mqtt->close();
                                logMessage("  Config #{$config_count}: " . $param . " ✅");
                            } else {
                                logMessage("  Config #{$config_count}: " . $param . " ❌");
                            }

                            sleep(1);
                        }
                    }
                    logMessage("✅ Sent {$config_count} configuration parameters");*/

                    // STEP 3: Send the 8 commands one by one
                   logMessage("================================================");
                    logMessage("📤 Sending 8 standard commands...");
                    logMessage("================================================");

                    $total_commands = count($eight_commands);
                    $success_count = 0;
                    $fail_count = 0;

                    foreach ($eight_commands as $index => $cmd) {
                        $payload = $cmd['payload'];
                        $command_number = $index + 1;

                        logMessage("  Command {$command_number}/{$total_commands}: " . $cmd['name']);

                        $mqtt_message = json_encode([
                            'command_type' => 'GET_DEVICE_STATUS',
                            'address' => $router,
                            'device_id' => $node_id1,
                            'payload' => 'EC01' . $payload
                        ]);

                        $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

                        if ($mqtt->connect(true, NULL, $username, $password)) {
                            $topic = "Efactor/things/{$router}/command";
                            $mqtt->publish($topic, $mqtt_message);
                            $mqtt->close();

                            logMessage("    ✅ Sent");
                            $success_count++;

                            // Wait 1 second before next command
                            if ($index < ($total_commands - 1)) {
                                sleep(5);
                            }
                        } else {
                            logMessage("    ❌ Failed");
                            $fail_count++;
                        }
                    }

                    logMessage("================================================");
                    logMessage("📊 8 Commands: {$success_count} successful, {$fail_count} failed");
                    logMessage("================================================");

                    // STEP 4: Wait for ACK and verify
                    logMessage("Waiting for ACK...");
                    sleep(5);

                    // Check final status
                    $query_ack = "SELECT * FROM `$modelSelect` WHERE node_id = '$node_id1'";
                    $row_param_ack = $db->get_row($query_ack);
                    $configure_node = $row_param_ack['configure_node'] ?? '';
                    $auto_testing_final = $row_param_ack['auto_testing'] ?? '';

                    logMessage("Configure_node: '" . $configure_node . "'");
                    logMessage("Auto_testing: '" . $auto_testing_final . "'");

                    if ($configure_node == 'OK') {
                        logMessage("✅ " . $node_id1 . " configured successfully!");
                    } else if (strpos($auto_testing_final, 'tested') !== false) {
                        logMessage("✅ " . $node_id1 . " tested successfully!");
                    } else {
                        logMessage("⚠️ " . $node_id1 . " ACK status: '" . $configure_node . "'");

                        // Try one more time to send commands
                       /* logMessage("Retrying configuration...");
                        foreach ($eight_commands as $index => $cmd) {
                            $payload = $cmd['payload'];
                            $mqtt_message = json_encode([
                                'command_type' => 'GET_DEVICE_STATUS',
                                'address' => $router,
                                'device_id' => $node_id1,
                                'payload' => 'EC01' . $payload
                            ]);

                            $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);
                            if ($mqtt->connect(true, NULL, $username, $password)) {
                                $topic = "Efactor/things/{$router}/command";
                                $mqtt->publish($topic, $mqtt_message);
                                $mqtt->close();
                                sleep(1);
                            }
                        }

                        // Check again
                        sleep(3);
                        $query_ack2 = "SELECT * FROM `$modelSelect` WHERE node_id = '$node_id1'";
                        $row_param_ack2 = $db->get_row($query_ack2);
                        $configure_node2 = $row_param_ack2['configure_node'] ?? '';

                        if ($configure_node2 == 'OK') {
                            logMessage("✅ " . $node_id1 . " configured successfully on retry!");
                        } else {
                            logMessage("⚠️ " . $node_id1 . " still not configured properly");
                        }*/
                    }

                } else {
                    logMessage("================================================");
                    logMessage("❌ Network not established for node: " . $node_id1);
                    logMessage("Network status: '" . $network . "'");
                    logMessage("Auto_testing: '" . $auto_testing . "'");
                    logMessage("================================================");

                    // Check if CD00DA030A was received but not detected
                    if (strpos($auto_testing, 'CD00DA030A') !== false) {
                        logMessage("⚠️ CD00DA030A detected - Attempting configuration anyway...");

                        // Try to send commands anyway
                        foreach ($eight_commands as $index => $cmd) {
                            $payload = $cmd['payload'];
                            $mqtt_message = json_encode([
                                'command_type' => 'GET_DEVICE_STATUS',
                                'address' => $router,
                                'device_id' => $node_id1,
                                'payload' => 'EC01' . $payload
                            ]);
                            
                          /*  $mqtt_message = json_encode([
                                'command_type' => 'GET_DEVICE_STATUS',
                                'address' => $router,
                                'device_id' => $node_id1,
                                'payload' => 'BC32'
                            ]);*/
                            
                            

                            $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);
                            if ($mqtt->connect(true, NULL, $username, $password)) {
                                $topic = "Efactor/things/{$router}/command";
                                $mqtt->publish($topic, $mqtt_message);
                                $mqtt->close();
                                logMessage("  Command " . ($index + 1) . " sent (bypass)");
                                sleep(1);
                            }
                        }
                    }
                }
            }

            // Update database
            logMessage("Updating database...");
            $db->query("UPDATE `$modelSelect` SET $set_clause WHERE node_id IN ($node_ids42) AND user_id = '$user_id'");
            logMessage("Database updated");
        }

        // Send STOP command
        logMessage("Sending STOP command to router: " . $router);
        $mqtt_message_stop = json_encode([
            'command_type' => 'E_SCAN',
            'address' => $router,
            'device_id' => $router,
            'payload' => "STOP"
        ]);

        $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

        if (!$mqtt->connect(true, NULL, $username, $password)) {
            logMessage("ERROR: Failed to connect for STOP command");
        } else {
            $topic = "Efactor/things/{$router}/command";
            $mqtt->publish($topic, $mqtt_message_stop);
            $mqtt->close();
            logMessage("STOP command sent");
        }

        sleep(2);
    }

    logMessage("========== PRODUCT CONFIG COMPLETED ==========");
}
/*---------------------------------------------------------------------------------------*/
?>
