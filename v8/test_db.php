<?php


$startTime = microtime(true);

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

/*-----------------------------------------------------*/
$req = file_get_contents ( 'php://input' );
$rawdata = $HTTP_RAW_POST_DATA;
//$json_data = json_decode($req);

header('Content-Type: application/json');
//header('Authorization: Bearer {token}');
header('Authorization: API Key');
//$not_found = false; // change it to true if you wish device not found response
//$user_check_faild = false; // change to true if you wish user credential error response

/*-----------------------MQTT--------------------------*/

include('jwt_utils.php');
//include('phpMQTT.php');
/*$server = 'efactor.tech';     // change if necessary
$port = 1883;                     // change if necessary
$username = 'mqttusr';                   // set your username
$password = '123456';                   // set your password
$client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()*/
$timestamp=round(microtime(true) * 1000);



/************************************************************/
$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');
$efactor = 'Efactor';
$things = 'things';
$command = 'command';

/************************************************************/


$keys = ['Speed', 'Direction', 'Led', 'RPM', 'Current', 'Voltage', 'Get status', 'Resistance', 'Imin', 'Imax', 'Frequence', 'PolePairs', 'StartupCurrent', 'MaxCurrent', 'MinRpm', 'MaxRpm', 'CurrentBW', 'Bemf_freq', 'Bemf_Volt', 'Speed1', 'Speed2', 'Speed3', 'Speed4', 'OK', 'Led Color', 'name', 'power', 'Display', 'SendData', 'DisplayLog'];
$values = ['1', '2', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];

// Construct the SET clause dynamically
$set_query = [];
foreach ($keys as $index => $key) {
    $value = $values[$index];
    
    // Handle empty values and replace them with NULL
    if ($value === '') {
        $value = 'NULL';
    }
    
    // Add backticks for keys with spaces
    if (strpos($key, ' ') !== false) {
        $set_query[] = "`$key` = $value";
    } else {
        $set_query[] = "$key = $value";
    }
}
$set_clause = implode(", ", $set_query);

// Construct the full SQL query
$query = "
UPDATE `EF-002FMN02`
SET $set_clause
WHERE 
    node_id IN ('FFFFFFFFFFFFFFFF7EC200000001E400') 
    AND user_id = '177';
";

// Output the query
echo $query;
?>
