<?php

$response = array();
include('config.php');
include('functions.php');
include('phpMQTT.php');
header('Content-Type: application/json');

define("SITE_URL", "https://efactor.tech/");
define("BASE_PATH", "/var/www/html");
define("SITE_NAME", "Factor");
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "efactor@24");
define("DB_NAME", "factor_db1");
define("SEND_ERRORS_TO", "factorenovations23@gmail.com");
define("DISPLAY_DEBUG", false);

date_default_timezone_set("Asia/Kolkata");

require_once('class.db.php');
$db = new DB();

// MQTT connection details
$server = 'efactor.tech';
$port = 1883;
$username = 'efactorpw';
$password = '123456';
$client_id = 'phpMQTT-publisher';

// Database connection details
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'efactor@24';
$db_name = 'factor_db1';

$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

// Check if MQTT connection is successful
if (!$mqtt->connect(true, NULL, $username, $password)) {
    exit("Failed to connect to MQTT server");
}


//$payload = 'A1C3000A';
$payload='DEE701010105000004000000983FB500003EA0000041C30000453100000A';
$nodeid='FFFFFFFFFFFFFFFF7EC2000000004200';
$payloadSize = strlen($payload) / 2;
$first_two_bytes_of_payload = substr($payload, 0, 4); // Get the first two bytes (A1C3)

// Query to fetch cmd_valueinhexmin from the database
$payload_query = "SELECT * FROM product_api WHERE model_name='EFACT V1.1'";
$payload_info = $db->get_results($payload_query);



if($payloadSize<=10)
{




foreach ($payload_info as $row) {


     $parameter_name = $row['parameter_name'];
     $cmd_valueinhexmin = $row['cmd_valueinhexmin'];

     //$product_name = $row['product_name']; 
      $product_name = 'Fan'; 
     $cmd_value = str_replace(',', '', $cmd_valueinhexmin);
     $first_two_bytes_of_cmd = substr($cmd_value, 0, 4);
     $third_byte_of_cmd = substr($cmd_value, 4, 2);

    if ($first_two_bytes_of_cmd === $first_two_bytes_of_payload) {

     echo $product_name;
     $db->query("update $product_name set $parameter_name='$third_byte_of_cmd' where node_id='$nodeid'");
     echo "Match found for parameter: " . $parameter_name . " - " . $first_two_bytes_of_cmd . "\n";

    } else {
            }
}

}

else{


foreach ($payload_info as $row) {


     $parameter_name = $row['parameter_name'];
     $resp_valueinhexmin = $row['resp_valueInHexMin'];
//echo $resp_valueinhexmin;
     $resp_defaultvalue = $row['resp_defaultvalue'];
     $product_name = $row['product_name']; 
     $resp_value = str_replace(',', '', $resp_valueinhexmin);
     $first_two_bytes_of_resp = substr($resp_value, 0, 4);
     $third_byte_of_resp = substr($resp_value, 4, 2);

/****************************************************************/




$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFACT V1.1'"; 
// $query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name' AND page_names= '$pagename'";
        $User_nodes_doc = $db->get_results($query_1); 
        $num_11 = $db->num_rows($query_1);
        $row = $db->get_row($query_1); 
        $parameter_name = $row['parameter_name']; 
         
 
$param1=$param['parameter_name'];

 

$device = [];


foreach ($User_nodes_doc as $param) {
    $param2=$param['parameter_name'];
 //  echo $param2." ";
// $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param2'";
    $query_param = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.1' AND parameter_name='$param2'";
        $params = $db->get_results($query_param); 
        $num_param = $db->num_rows($query_param);
        $row_param = $db->get_row($query_param); 
        $parameter_name = $row_param['parameter_name'];

        $number_of_pages = $row_param['number_of_pages'];
         $sequence = $row_param['sequence_no'];
        $page_names = $row_param['page_names'];

            $cmd_datatype = explode(',', $row_param['cmd_datatype']);
            $cmd_framesize = $row_param['cmd_framesize'];
            $cmd_fielddescription = explode(',', $row_param['cmd_fielddescription']);
            $cmd_uitype = explode(',', $row_param['cmd_uitype']);
            $cmd_defaultvalue = explode(',', $row_param['cmd_defaultvalue']);
            $cmd_asperui = explode(',', $row_param['cmd_asperui']);
            $cmd_asperui_values = explode(',', $row_param['cmd_asperui_values']);
            $cmd_valueinhexmin = explode(',', $row_param['cmd_valueinhexmin']);
            $cmd_numberOfbyte = explode(',', $row_param['cmd_numberOfbytes']);

            $resp_dataType = explode(',', $row_param['resp_dataType']);
            $resp_framesize = $row_param['resp_framesize'];
            $resp_fieldDescription = explode(',', $row_param['resp_fieldDescription']);
            $resp_uiType = explode(',', $row_param['resp_uiType']);
            $resp_defaultvalue = explode(',', $row_param['resp_defaultvalue']);
            $resp_asperui = explode(',', $row_param['resp_asperui']);
            $resp_asperui_values = explode(',', $row_param['resp_asperui_values']);
            $resp_numberOfBytes2 = explode(',', $row_param['resp_numberOfBytes']);

            $error_dataType = explode(',', $row_param['error_dataType']);
            $error_uiType = explode(',', $row_param['error_uiType']);
            $error_defaultvalue = explode(',', $row_param['error_defaultvalue']);
            $error_asperui = explode(',', $row_param['error_asperui']);
            $error_asperui_values = explode(',', $row_param['error_asperui_values']);
 

        $selected = $row_param['selected'];

      // $default_name = [];
        for ($i = 0; $i < $cmd_framesize; $i++) {
         if($cmd_fielddescription[$i] == 'Data'){
         
          $default_name[] = [
           "name" => $cmd_defaultvalue[$i]
           ];
 
  
}   
  
}  
  




if ($parameter_name === 'Get status') {

$payload2 = $payload;
$binaryPayload = hex2bin($payload2);
$currentPos = 0;

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
elseif ($parameter_name === 'Command Get Status') {



    $payload2 = $payload;
    $binaryPayload = hex2bin($payload2);
    $currentPos = 0;

    for ($i = 0; $i < $resp_framesize; $i++) {
        $numBytes = $resp_numberOfBytes2[$i];
        $default_name2 = $resp_defaultvalue[$i];
        $extracted = substr($binaryPayload, $currentPos, $numBytes);
        $extractedHex = bin2hex($extracted);
       
        $defaultValues3[] = [
            "name" => $default_name2,
            "value" => $extractedHex
        ];
        
        $currentPos += $numBytes;
    }
    
   }
elseif ($cmd_framesize > 10){

$payload2 = $payload;
$binaryPayload = hex2bin($payload2);
$currentPos = 0;

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
     
   }else{
    // echo "else ";
   }
    
}




//print_r($default_name);
//print_r($defaultValues);
//print_r($defaultValues2);
//print_r($defaultValues3);



$result = [];
 
foreach ($default_name as $stag) {
    $stag_name = $stag['name'];

    foreach ($defaultValues as $entry) {
        if ($stag_name === $entry['name']) {
             $decimalValue = hexdec($entry['value']);
            $result[$stag_name] = $decimalValue;
         //   echo "Matched value for $stag_name: " . $entry['value'] . "\n";
            break;  
        }
    }
}
 
//exit(print_r($result));
 
 
 $result2 = [];
 
foreach ($default_name as $stag) {
    $stag_name = $stag['name'];

    foreach ($defaultValues2 as $entry) {
        if ($stag_name === $entry['name']) {
        $decimalValue = hexdec($entry['value']);
            $result2[$stag_name] = $decimalValue;
         //   echo "Matched value for $stag_name: " . $entry['value'] . "\n";
            break;  
        }
    }
}
 
//exit(print_r($result2));
 
 
 
$formattedEntries = [];

foreach ($defaultValues3 as $entry) {
    $name = $entry['name'];
    $value = $entry['value'];
    
    if (!empty($name)) {  
        if (strlen($value) == 1) {
            $formattedValue2 = str_pad($value, 2, '0', STR_PAD_LEFT);  
             $formattedValue3 = hexdec($formattedValue2);
            $formattedValue = $formattedValue3;
        } elseif (strlen($value) == 2) {
            $formattedValue2 = $value;
            $formattedValue3 = hexdec($formattedValue2);
            $formattedValue = $formattedValue3;
        } else {
            $formattedValue2 = substr($value, 0, 2); 
            $formattedValue3 = hexdec($formattedValue2);
            $formattedValue = $formattedValue3; 
        }
        $formattedEntries[] = "$name:$formattedValue";
    }
}
$resultString = implode(', ', $formattedEntries);
 
 //exit(print_r($resultString));



/***************************************************************/

$entries = explode(', ', $resultString); 
$data = [];

/*foreach ($entries as $entry) {
    list($key, $value) = explode(':', $entry); 
    $data[trim($key)] = trim($value); 
}*/


foreach ($entries as $entry) {
    list($key, $value) = explode(':', $entry); // Split each entry by ':'
    $key = trim($key);
    $value = trim($value);
    
    // Clean up the key to match database columns
    $key = str_replace('/', '_', $key);  // Replace `/` with `_`
    $key = str_replace('-', '_', $key);  // Replace `-` with `_`
    
    $data[$key] = $value; // Store key-value pairs in an associative array
}


$updateParts = [];
foreach ($data as $key => $value) {
    $updateParts[] = "$key = '$value'"; 
}

$setClause = implode(', ', $updateParts);

/*$query = "UPDATE $product_name SET $setClause WHERE node_id = '$nodeid'";
$db->query($query);*/

 
/****************************************************************/




if ($first_two_bytes_of_resp === $first_two_bytes_of_payload) {

//echo 'hi3';

     //echo $product_name;
     //$db->query("update $product_name set $resp_defaultvalue='$resp_valueinhexmin' where node_id='$nodeid'");
echo $setClause;

     $query = "UPDATE $product_name SET $setClause WHERE node_id = '$nodeid'";
$db->query($query);
   //  echo "Match found for parameter: " . $resp_defaultvalue . " - " . $resp_valueinhexmin . "\n";
exit;
    } else {
            }
}

}





// Disconnect from MQTT
$mqtt->close();
?>
