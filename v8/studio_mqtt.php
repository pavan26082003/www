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

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

$req = file_get_contents( 'php://input' );
$json_data = json_decode($req);
header('Content-Type: application/json');
header('Authorization: API Key');



/*-----------------------MQTT--------------------------*/



$server = 'efactor.tech';     // change if necessary
$port = 1883;                     // change if necessary
$username = 'efactorpw';                   // set your username
$password = '123456';                   // set your password
$client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()


/*----------------------------mail-------------------------*/
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

$req = file_get_contents( 'php://input' ); 
$json_data = json_decode($req, true);
$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');
$efactor = 'Efactor';
$things = 'things';
$command = 'command';


$json_data = file_get_contents('php://input');
if (!empty($json_data)) {
    

$data = json_decode($json_data, true);



$value = isset($data['value']) ? $data['value'] : '';
$parameter_name = isset($data['parameterName']) ? $data['parameterName'] : '';
$userid = isset($data['user_id']) ? $data['user_id'] : '';

$payload= $parameter_name.$userid;

if ($data !== null) {
        



 if ($value ===true) {
    $hexValue = '01';
} elseif ($value ===false) {
    $hexValue = '00';
} else {
    // Check if value is numeric or string and convert to hexadecimal
    /*if (is_numeric($value)) {
        // Numeric value conversion
        $hexValue = dechex($value);
    } elseif (is_string($value)) {
        // String value conversion
        for ($i = 0; $i < strlen($value); $i++) {
            $hexValue .= dechex(ord($value[$i]));
        }
    }*/


     if (is_numeric($value)) {
    // Numeric value conversion
   // $hexValue = dechex($value);
 $hexValue = str_pad(dechex($value), 2, '0', STR_PAD_LEFT);
} elseif (is_string($value)) {
    // String value conversion
    for ($i = 0; $i < strlen($value); $i++) {
        $charHex = dechex(ord($value[$i]));
        // Ensure each hex value is two characters long
        $hexValue .= str_pad($charHex, 2, '0', STR_PAD_LEFT);
    }
}



}






//$user_id='177';
//$model_no='cooker';


$query_productapi = "SELECT * FROM product_api WHERE selected='1' AND parameter_name='$parameter_name' AND user_id='$userid'";
$results_productapi = $db->get_results($query_productapi);
    $row_productapi = $db->get_row($query_productapi);


$nodeID = $row_productapi['node_id'];




$command_type='GET_DEVICE_STATUS';
$address=$nodeID;
$device_id=$nodeID;





$query_mqtt = "SELECT * FROM product_api WHERE node_id='$nodeID' AND parameter_name='$parameter_name'";
$results = $db->get_results($query_mqtt);
    $row = $db->get_row($query_mqtt);

          
        $cmd_value_min = $row['cmd_valueinhexmin'];
        $cmd_value_max = $row['cmd_valueinhexmax'];
        $cmd_value_min = str_replace(' ', '', $cmd_value_min);
        $cmd_value_max = str_replace(' ', '', $cmd_value_max);
        $cmd_value_min_array = explode(',', $cmd_value_min);
        $cmd_value_max_array = explode(',', $cmd_value_max);

      
        //$cmd_value_max_array[2] = '11';

      //  $cmd_value_max = $cmd_value_max_array[0] . $cmd_value_max_array[1] . $cmd_value_max_array[2] . $cmd_value_max_array[3];


        $cmd_value_max = $cmd_value_max_array[0] . $cmd_value_max_array[1] . $hexValue . $cmd_value_max_array[3];   //working

        //$cmd_value_max = 'A1C2030A';

            $mqtt_message = json_encode(array(
            'command_type' => $command_type,
            'address' => $address,
            'device_id' => $device_id,
            'payload' => $cmd_value_max
        ));




$efactor = 'Efactor';
$things = 'things';
$command = 'command';

$topic = "$efactor/$things/$nodeID/$command";


    
        if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {  
            $mqtt->publish($topic, $mqtt_message);
            $mqtt->close();


        }
    } else {
        echo "Invalid JSON data.";
    }
} else {
    echo "No JSON data received.";
}





?>
