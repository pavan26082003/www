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

$req = file_get_contents( 'php://input' ); 
$json_data = json_decode($req);
header('Content-Type: application/json');
header('Authorization: API Key');

/*-----------------------MQTT--------------------------*/

//include('jwt_utils.php');

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

$command_type = 'GET_DEVICE_STATUS';
$address = 'FFFFFFFFFFFFFFFF7EC200000001E400';
$device_id = 'FFFFFFFFFFFFFFFF7EC200000001E400';


/*
// Extract values from input or use defaults
$node_id = $data['node_id'] ?? 'FFFFFFFFFFFFFFFF7EC200000001E400';
$payload = $data['payload'] ?? 'A1C2010A';
$modelname = $data['modelName'] ?? 'EF-002FMN02';
*/


$payload = 'A1C2010A';
$modelname = 'EF-002FMN02';


// Construct the MQTT message
$mqtt_message = json_encode([
    'command_type' => 'GET_DEVICE_STATUS',
    'address' => $address,
    'device_id' => $device_id,
    'payload' => $payload,
]);



// Create an MQTT connection
$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

if (!$mqtt->connect(true, NULL, $username, $password)) {
    echo json_encode(["status" => "error", "message" => "Failed to connect to MQTT broker"]);
    exit;
}

// Define the MQTT topic and publish the message
$topic = "Efactor/things/$addressd/command";

if ($mqtt->publish($topic, $mqtt_message)) {
    echo json_encode([
        "status" => "success",
        "message" => "MQTT message published",
        "topic" => $topic,
        "mqtt_message" => $mqtt_message,
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to publish MQTT message"]);
}

// Close the MQTT connection
$mqtt->close();

?>





















