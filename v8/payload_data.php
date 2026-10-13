<?php

$response = array();
include('config.php');
include('functions.php');
include('phpMQTT.php');
header('Content-Type: application/json');
header('Authorization: API Key');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

$req = file_get_contents('php://input');
$json_data = json_decode($req, true);

$server = 'efactor.tech';     // change if necessary
$port = 1883;                     // change if necessary
$username = 'efactorpw';                   // set your username
$password = '123456';                   // set your password
$client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to server - you could use uniqid()

/*---------------------------- mail -------------------------*/
require_once 'vendor/autoload.php';
$mail = new PHPMailer\PHPMailer\PHPMailer();

// Set the SMTP parameters
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'factorenovations23@gmail.com';
$mail->Password = 'cuqvonplawmksoyb';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');
$efactor = 'Efactor';
$things = 'things';
$command = 'command';

    $payload = 'DEE700000101050200B026400A';
    $payloadSize = strlen($payload) / 2;
    $model_name = 'EFDOORSNR';
    $nodeid_notification = 'true';
    $individualBytes = str_split($payload, 2);



    if ($individualBytes[0] == 'DE' && $individualBytes[1] == 'E7') {
        $payload_query = "SELECT * FROM product_api WHERE model_name='$model_name'";
        $payload_info = $db->get_results($payload_query);

        //$defaultValues = [];
        //$defaultValues2 = [];




        foreach ($payload_info as $row) {
            $parameter_name = $row['parameter_name'];
            $resp_valueinhexmin = $row['resp_valueInHexMin'];
            $resp_defaultvalue = $row['resp_defaultvalue'];
            $product_name = $row['product_name'];

            $resp_value = str_replace(',', '', $resp_valueinhexmin);
            $first_two_bytes_of_resp = substr($resp_value, 0, 4);
            $third_byte_of_resp = substr($resp_value, 4, 2);

            $query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name' AND page_names = 'Page1'";
            $User_nodes_doc = $db->get_results($query_1);

            foreach ($User_nodes_doc as $param) {
                $param2 = $param['parameter_name'];    
          echo $param2;     
                $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param2'";
                $row_param = $db->get_row($query_param);

                if ($row_param) {

                    $parameter_name = $row_param['parameter_name'];
                    $cmd_framesize = $row_param['cmd_framesize'];
                    $resp_framesize = $row_param['resp_framesize'];
                    $cmd_fielddescription = explode(',', $row_param['cmd_fielddescription']);
                    $cmd_defaultvalue = explode(',', $row_param['cmd_defaultvalue']);
                    $cmd_numberOfbyte = explode(',', $row_param['cmd_numberOfbytes']);
                    $resp_defaultvalue = explode(',', $row_param['resp_defaultvalue']);
                    $resp_numberOfBytes2 = explode(',', $row_param['resp_numberOfBytes']);




                    if ($cmd_framesize > 10) {
                        $binaryPayload = hex2bin($payload);
                        $currentPos = 0;
                        $defaultValues2 = [];

                        for ($i = 0; $i < $cmd_framesize; $i++) {
                            $numBytes = $cmd_numberOfbyte[$i];
                            $default_name2 = $cmd_defaultvalue[$i];
                            $extracted = substr($binaryPayload, $currentPos, $numBytes);
                            $extractedHex = bin2hex($extracted);
                            $defaultValues2[] = [
                                "name" => $default_name2,
                                "value" => $extractedHex
                            ];
                            $currentPos += $numBytes;
                        }
                    }

                    if ($parameter_name === 'Get status') {
                        $binaryPayload = hex2bin($payload);
                        $currentPos = 0;
                        $defaultValues = [];

                        for ($i = 0; $i < $resp_framesize; $i++) {
                            $numBytes = $resp_numberOfBytes2[$i];
                            $default_name2 = $resp_defaultvalue[$i];
                            $extracted = substr($binaryPayload, $currentPos, $numBytes);
                            $extractedHex = bin2hex($extracted);
                            $defaultValues[] = [
                                "name" => $default_name2,
                                "value" => $extractedHex
                            ];
                            $currentPos += $numBytes;
                        }
                    }

                    $result = [];
                    foreach ($defaultValues as $entry) {
                        $stag_name = $entry['name'];
                        if (!empty($stag_name)) {
                            $decimalValue = hexdec($entry['value']);
                            $result[$stag_name] = $decimalValue;
                        }
                    }

                    $result2 = [];
                    foreach ($defaultValues2 as $entry) {
                        $stag_name = $entry['name'];
                        if (!empty($stag_name)) {
                            $decimalValue = hexdec($entry['value']);
                            $result2[$stag_name] = $decimalValue;
                        }
                    }
                }
            }
        }

        $formattedEntries = [];
        foreach ($defaultValues as $entry) {
            $name = $entry['name'];
            $value = $entry['value'];
     
            echo $name;
            echo $value;

            if ($name == 'Direction') {
                $value = '08';
            }

             if ($nodeid_notification == 'true') {


       echo 'hi_notifica---';

                $devicenotification = "SELECT * FROM notification WHERE modelno='$model_name'";
                $row_noti = $db->get_row($devicenotification);

                if ($row_noti) {
                    $default_values = $row_noti['default_values'];
                    $modelno = $row_noti['modelno'];
                    $default_values_array = explode(',', $default_values);

                    if (in_array($name, $default_values_array)) {
                        echo "'$key' is present in default values.";

                        $filtered_values = array_filter($default_values_array, function($value) {
                            return !empty($value); 
                        });

                        $filtered_values = array_values($filtered_values);

                        echo "<pre>"; 
                        print_r($filtered_values);
                        echo "</pre>";

                        $non_empty_values = [];

                        foreach ($filtered_values as $item) {
                            if (!empty($item)) {
                                $non_empty_values[] = $item;
                            }
                        }

                        $devicequery = "SELECT * FROM `$model_name` WHERE node_id='$nodeid'";
                        $row_data = $db->get_row($devicequery);

                        $current_devicetype = $row_data['current_devicetype'];
                        $devicevalue = $row_data[$name];
                        $devicename = $row_data['name'];

                        $query = "UPDATE `$model_name` SET `$key` = '$value' WHERE node_id = '$nodeid'";
                        $db->query($query);
                        $devicename = 'doorlock';

                        // echo $key;

                        print_r($non_empty_values);

                        foreach ($non_empty_values as $index => $value) {
                            $accessToken = getAccessToken();
                            $title = "Efactor SmartThings";
                            $current_devicetype = 'lock_7E0003A1';

                            $payload_data = [
                                'title' => 'Efactor SmartThings',
                                'body' => 'Door lock open',
                                'event_data_payload' => json_encode([
                                    'event_version' => 'v1',
                                    'event_type' => 'efactor.event.node_params_changed',
                                    'timestamp' => $timestamp,
                                    'description' => $description,
                                    'event_data' => [
                                        'user_id' => (string)$user_id,
                                        'node_id' => $device_id,
                                        'payload' => json_encode([
                                            $current_devicetype => [$pagename => [
                                                $key => $value
                                            ]]
                                        ])
                                    ]
                                ])
                            ];

                            foreach ($payload_data as $key => $value) {
                                $payload_data[$key] = (string)$value;
                            }

                            // $response = sendFCMNotification($accessToken, $mobile_to, $title, $body, $payload_data);
                        }
                    } else {
                        echo "'$name' is not present in default values.";
                        $query = "UPDATE `$model_name` SET `$key` = '$value' WHERE node_id = '$nodeid'";
                        $db->query($query);
                    }
                } else {
                    echo "No data found in the notification table.";
                }
            } else {
                $db->query("UPDATE `$model_name` SET $name='$value' WHERE node_id='$nodeid'");
            }
        }

     /*   $defaultValues = [];
        $defaultValues2 = [];

        $data_auto = [
            "url" => "action=auto_get_nodes_params",
            "token" => $access_token,
            "node_id" => $address,
            "device_id" => $nodeid
        ];


print_r($data_auto);*/

            }


?>
