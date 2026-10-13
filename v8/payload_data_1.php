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

echo "hi";

{

$nodeid='FFFFFFFFFFFFFFFF7EC200000003A100';
    $payload = 'DEE7000001010200003028400A';
    $payloadSize = strlen($payload) / 2;
$model_name = 'EF-003FMN03';

 $nodeid_notification = 'true';

$model_name_default ='input-output';
   


    if ($model_name_default == 'EFDEFLT V1.0') {
        $db->query("UPDATE efactorproduct SET payload='$payload' WHERE node_id='$nodeid'");
    } else {
        $db->query("UPDATE `$model_name` SET payload='$payload' WHERE node_id='$nodeid'");
    }

    $individualBytes = str_split($payload, 2);
    if ($individualBytes[0] == 'DE' && $individualBytes[1] == 'E7') {





        $payloadSize = strlen($payload) / 2;
        $first_two_bytes_of_payload = substr($payload, 0, 4);    
        $query = "SELECT * FROM product_api WHERE model_name = '$model_name'";

        $result = $db->get_results($query);
        $num = count($result); // Count the number of rows in the results
        $row = $result[0]; // Get the first row of the results

        $frames = array();

        foreach ($result as $row) {
            $frames[] = array(
                "name" => $row["cmd_fieldDescription"],
            );
        }

       $query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name' AND page_names= '$pagename'";
        $query_2 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFDEFLT V1.1' AND page_names= '$pagename'";

        $result_1 = $db->get_results($query_1); 
        $result_2 = $db->get_results($query_2); 

        $result_3 = $db->num_rows($query_1);
        $result_4 = $db->num_rows($query_2);

        $result_5 = $db->get_row($query_1); 
        $result_6 = $db->get_row($query_2); 

        $User_nodes_doc = array_merge($result_1, $result_2);
        $num_11 = $result_3 + $result_4;
        $row = array_merge($result_1, $result_2);
        $parameter_name = isset($row['parameter_name']) ? $row['parameter_name'] : null;


echo $parameter_name;


        foreach ($User_nodes_doc as $param) {
            $param2 = $param['parameter_name'];

            $query_param1 = "SELECT * FROM product_api WHERE model_name = 'EFDEFLT V1.1' AND parameter_name='$param2'";
            $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param2'";

            $result_11 = $db->get_results($query_param1); 
            $result_22 = $db->get_results($query_param); 

            $result_33 = $db->num_rows($query_param1);
            $result_44 = $db->num_rows($query_param);

            $row_param11 = $db->get_row($query_param1); 
            $row_param22 = $db->get_row($query_param); 

            $params = array_merge($result_11, $result_22);
            $num_param = $result_33 + $result_44;
            $row_param = array_merge((array)$row_param11, (array)$row_param22);
            $parameter_name = isset($row_param['parameter_name']) ? $row_param['parameter_name'] : null;

            // Ensure values are set before accessing
            $number_of_pages = isset($row_param['number_of_pages']) ? $row_param['number_of_pages'] : 0;
            $sequence = isset($row_param['sequence_no']) ? $row_param['sequence_no'] : null;
            $page_names = isset($row_param['page_names']) ? $row_param['page_names'] : null;

            $cmd_datatype = explode(',', isset($row_param['cmd_datatype']) ? $row_param['cmd_datatype'] : '');
            $cmd_framesize = isset($row_param['cmd_framesize']) ? $row_param['cmd_framesize'] : null;
            $cmd_fielddescription = explode(',', isset($row_param['cmd_fielddescription']) ? $row_param['cmd_fielddescription'] : '');
            $cmd_uitype = explode(',', isset($row_param['cmd_uitype']) ? $row_param['cmd_uitype'] : '');
            $cmd_defaultvalue = explode(',', isset($row_param['cmd_defaultvalue']) ? $row_param['cmd_defaultvalue'] : '');
            $cmd_asperui = explode(',', isset($row_param['cmd_asperui']) ? $row_param['cmd_asperui'] : '');
            $cmd_asperui_values = explode(',', isset($row_param['cmd_asperui_values']) ? $row_param['cmd_asperui_values'] : '');
            $cmd_valueinhexmin = explode(',', isset($row_param['cmd_valueinhexmin']) ? $row_param['cmd_valueinhexmin'] : '');
            $cmd_numberOfbyte = explode(',', isset($row_param['cmd_numberOfbytes']) ? $row_param['cmd_numberOfbytes'] : '');

            $resp_dataType = explode(',', isset($row_param['resp_dataType']) ? $row_param['resp_dataType'] : '');
            $resp_framesize = isset($row_param['resp_framesize']) ? $row_param['resp_framesize'] : null;
            $resp_fieldDescription = explode(',', isset($row_param['resp_fieldDescription']) ? $row_param['resp_fieldDescription'] : '');
            $resp_uiType = explode(',', isset($row_param['resp_uiType']) ? $row_param['resp_uiType'] : '');
            $resp_defaultvalue = explode(',', isset($row_param['resp_defaultvalue']) ? $row_param['resp_defaultvalue'] : '');
            $resp_asperui = explode(',', isset($row_param['resp_asperui']) ? $row_param['resp_asperui'] : '');
            $resp_asperui_values = explode(',', isset($row_param['resp_asperui_values']) ? $row_param['resp_asperui_values'] : '');
            $resp_numberOfBytes = explode(',', isset($row_param['resp_numberOfBytes']) ? $row_param['resp_numberOfBytes'] : '');

            $error_dataType = explode(',', isset($row_param['error_dataType']) ? $row_param['error_dataType'] : '');
            $error_uiType = explode(',', isset($row_param['error_uiType']) ? $row_param['error_uiType'] : '');
            $error_defaultvalue = explode(',', isset($row_param['error_defaultvalue']) ? $row_param['error_defaultvalue'] : '');
            $error_asperui = explode(',', isset($row_param['error_asperui']) ? $row_param['error_asperui'] : '');
            $error_asperui_values = explode(',', isset($row_param['error_asperui_values']) ? $row_param['error_asperui_values'] : '');

            $selected = isset($row_param['selected']) ? $row_param['selected'] : null;
            $cker = array();

            // Ensure $dbcker is defined
            foreach ($dbcker as $key => $value) {
                $cker[] = array(
                    'name' => $key,
                    'value' => $value
                );
            }

            for ($i = 0; $i < $resp_framesize; $i++) {
                $default_name2 = isset($resp_defaultvalue[$i]) ? $resp_defaultvalue[$i] : '';
                $start = 0;

                foreach ($resp_numberOfBytes as $index => $bytes) {
                    $chunk = substr($payload, $start, $bytes * 2);   
                    $key = isset($resp_defaultvalue[$index]) ? $resp_defaultvalue[$index] : '';
                    $result_payload[$key] = $chunk; 
                    $start += $bytes * 2;
                }

                $result_payload = array_filter($result_payload, function($key) {
                    return !empty($key);  
                }, ARRAY_FILTER_USE_KEY);
            }
        }

        print_r($result_payload); 

        foreach ($result_payload as $key => $value) {
            $key = $db->escape($key);
            $value = $db->escape($value);

            //------------------------------------------------------

            if ($nodeid_notification == 'true') {
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
            }
        }
    }
}
?>
