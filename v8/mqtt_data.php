<?php

$response = array();
include('config.php');
include('functions.php');
include('phpMQTT.php');
header('Content-Type: application/json');

require 'vendor/autoload.php';  // Autoload dependencies
use WebSocket\Client;

set_time_limit(0);
ini_set('memory_limit', '256M');

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

// Database
require_once('class.db.php');
$db = new DB();

// MQTT server credentials
$server = 'efactor.tech';
$port = 1883;
$username = 'efactorpw';
$password = '123456';
$client_id = 'phpMQTT-publisher';
















$rawData = file_get_contents("php://input");

$jsonData = json_decode($rawData, true); 


 

       
//if (json_last_error() === JSON_ERROR_NONE) {
    // Extract the specific fields from the array
    $event_type = isset($jsonData['event_type']) ? $jsonData['event_type'] : null;
    $payload = isset($jsonData['payload']) ? $jsonData['payload'] : null;
    $nodeid = isset($jsonData['device_id']) ? $jsonData['device_id'] : null;
    $address = isset($jsonData['address']) ? $jsonData['address'] : null;
    $command_type= isset($jsonData['command_type']) ? $jsonData['command_type'] : null;  



    // You can now use $event_type, $payload, $nodeid, and $address
 /*   echo "Event Type: $event_type\n";
    echo "Payload: $payload\n";
    echo "Node ID: $nodeid\n";
    echo "Address: $address\n";*/


/*} else {
    echo "Error decoding JSON: " . json_last_error_msg();
}*/

/*
$query_efactor = "SELECT * FROM efactorproduct where node_id='$nodeid'";
$device_info = $db->get_results($query_efactor); 
$row_efactor = $db->get_row($query); 
$device_type=$row_efactor['device_type'];
$model_name=$row_efactor['model_number'];*/






/*********************'DEVICE_DATA_UPDATE'  start*******************/
if($event_type  == 'DEVICE_DATA_UPDATE')
{




//$db->query("update $device_type set payload='$payload' where node_id='$nodeid'");


/*$query_efactor = "SELECT * FROM efactorproduct where node_id='$nodeid'";
$device_info = $db->get_results($query_efactor); 
$row_efactor = $db->get_row($query); 
$device_type=$row_efactor['device_type'];*/


//echo 'DEVICE_DATA_UPDATE';



$query_efactor = "SELECT * FROM efactorproduct where node_id='$nodeid'";
$device_info = $db->get_results($query_efactor); 
$row_efactor = $db->get_row($query_efactor); 
$device_type=$row_efactor['device_type'];
echo $device_type;

/*$query_device_type = "SELECT * FROM $device_type where node_id='$nodeid'";
$device_type_info = $db->get_results($query_device_type); 
$row_device_type = $db->get_row($query_device_type); 
$model_name=$row_device_type['model_number'];
$access_token=$row_device_type['access_token'];
$pagename=$row_device_type['page_name'];
$app_version=$row_device_type['app_version'];
$payload=$row_device_type['payload'];*/


echo $payload;


//$db->query("update $device_type set payload='$payload' where node_id='$nodeid'"); 



     if($num>0)
     {


$payloadSize = strlen($payload) / 2;
$first_two_bytes_of_payload = substr($payload, 0, 4); 

$payload_query = "SELECT * FROM product_api WHERE model_name='$model_name'";
$payload_info = $db->get_results($payload_query);



if($payloadSize<=10)
{

foreach ($payload_info as $row) {


     $parameter_name = $row['parameter_name'];
     $cmd_valueinhexmin = $row['cmd_valueinhexmin'];

     //$product_name = $row['product_name']; 
     // $product_name = 'Fan'; 
     $cmd_value = str_replace(',', '', $cmd_valueinhexmin);
     $first_two_bytes_of_cmd = substr($cmd_value, 0, 4);
     $third_byte_of_cmd = substr($cmd_value, 4, 2);

    if ($first_two_bytes_of_cmd === $first_two_bytes_of_payload) {

     echo $product_name;
     $db->query("update $device_type set $parameter_name='$third_byte_of_cmd' where node_id='$nodeid'");
     echo "Match found for parameter: " . $parameter_name . " - " . $first_two_bytes_of_cmd . "\n";

     $db->query("update $device_type set ping='clear' where node_id='$nodeid'"); 
$db->query("update $device_type set internet='true' where node_id='$nodeid'");
$db->query("update $device_type set device_time='$dt' where node_id='$nodeid'");

    } else {
            }
}

}

else{


foreach ($payload_info as $row) {


     $parameter_name = $row['parameter_name'];
     $resp_valueinhexmin = $row['resp_valueInHexMin'];
     $resp_defaultvalue = $row['resp_defaultvalue'];
     $product_name = $row['product_name']; 
     $resp_value = str_replace(',', '', $resp_valueinhexmin);
     $first_two_bytes_of_resp = substr($resp_value, 0, 4);
     $third_byte_of_resp = substr($resp_value, 4, 2);

//$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFACT V1.1'"; 
 $query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name' AND page_names= '$pagename'";
        $User_nodes_doc = $db->get_results($query_1); 
        $num_11 = $db->num_rows($query_1);
        $row = $db->get_row($query_1); 
        $parameter_name = $row['parameter_name']; 
         
 
$param1=$param['parameter_name'];
$device = [];

foreach ($User_nodes_doc as $param) {
    $param2=$param['parameter_name'];
 
   // $query_param = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.1' AND parameter_name='$param2'";
     $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param2'";

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
    
   }
    
}



$result = [];
 
foreach ($default_name as $stag) {
    $stag_name = $stag['name'];

    foreach ($defaultValues as $entry) {
        if ($stag_name === $entry['name']) {
             $decimalValue = hexdec($entry['value']);
            $result[$stag_name] = $decimalValue;
            break;  
        }
    }
}
 
 $result2 = [];
 
foreach ($default_name as $stag) {
    $stag_name = $stag['name'];

    foreach ($defaultValues2 as $entry) {
        if ($stag_name === $entry['name']) {
        $decimalValue = hexdec($entry['value']);
            $result2[$stag_name] = $decimalValue;
            break;  
        }
    }
}
 
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

        $db->query("update $device_type set $name='$formattedValue' where node_id='$nodeid'");
    }

    

    
}


}

 }

   }
     else
   {
 
 

  }


 

}

/*********************'DEVICE_DATA_UPDATE'  end*******************/

/*********************'DEVICE_DATA_AUTO_UPDATE'  start*******************/



else if($event_type  == 'DEVICE_DATA_AUTO_UPDATE')
{

//echo 'DEVICE_DATA_AUTO_UPDATE';


$query = "SELECT * FROM efactorproduct where node_id='$nodeid'";
$device_info = $db->get_results($query); 
$row = $db->get_row($query); 
$device_type=$row['device_type'];
$model_number=$row['model_number'];
$access_token=$row['access_token'];

$to=$row['mob_device_token'];
$name=$row['name'];
$current_devicetype=$row['current_devicetype'];
$app_version=$row['app_version'];

$user_id=$row['user_id'];
//date_default_timezone_set('Asia/Kolkata');
//$timestamp = time();
$timestamp=round(microtime(true) * 1000);
//echo $timestamp;
$num = $db->num_rows($query); 

$dt=date("Y-m-d H:i:s");



     if($num>0)
     {
      
 $db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'"); 
  $db->query("update efactorproduct set ping='clear' where node_id='$nodeid'"); 
$db->query("update efactorproduct set internet='true' where node_id='$nodeid'");


/***************************************/
/************device_type fan************/
if($device_type == 'fan')
{

   $db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'"); 
$individualBytes = str_split($payload, 2);

if ($individualBytes[0] == 'DE' && $individualBytes[1] == 'E7') {


    if ($individualBytes[2] == '00') {
        $power = 'false';
    } else if ($individualBytes[2] == '01') {
        $power = 'true';
    }

    switch ($individualBytes[3]) {
        case '01':
            $speed = '1';
            break;
        case '02':
            $speed = '2';
            break;
        case '03':
            $speed = '3';
            break;
        case '04':
            $speed = '4';
            break;
        case '05':
            $speed = '5';
            break;
        default:
            $speed = '';
            break;
    }

    switch ($individualBytes[5]) {
        case '00':
            $hue = '0';
            break;
        case '01':
            $hue = '1';
            break;
        case '02':
            $hue = '2';
            break;
        case '03':
            $hue = '3';
            break;
        case '04':
            $hue = '4';
            break;
        case '05':
            $hue = '5';
            break;
        case '06':
            $hue = '6';
            break;
        default:
            $hue = '';
            break;
    }

    if ($individualBytes[6] == '00') {
        $led_indication = 'true';
    } else if ($individualBytes[6] == '01') {
        $led_indication = 'false';
    }

$direction = $individualBytes[4];

   /* $direction = $individualBytes[4];
    $rpm = $individualBytes[11] . $individualBytes[12];
    $power_wat = $individualBytes[13] . $individualBytes[14] . $individualBytes[15] . $individualBytes[16];
    $power_float = hexToFloat($power_wat);
    $current = $individualBytes[17] . $individualBytes[18] . $individualBytes[19] . $individualBytes[20];
    $current_float = hexToFloat($current);
    $bus_voltage = $individualBytes[21] . $individualBytes[22] . '00' . '00';
    $bus_voltage_float = hexToFloat(41920000);
    $ac = $individualBytes[23] . $individualBytes[24];
    $ac_float = hexToFloat($ac);*/


 if($model_number == 'EFACT V1.0')
{
    $db->query("update efactorproduct set power='$power', speed='$speed', Hue='$hue', direction='$direction', led_indication='$led_indication' where node_id='$nodeid'");
}
else
{

     $direction = $individualBytes[4];


    $rpm_hex = $individualBytes[11] . $individualBytes[12];
    $rpm = hexdec($rpm_hex);

    $power_wat = $individualBytes[13] . $individualBytes[14] . $individualBytes[15] . $individualBytes[16];
    $binarypower_watt = hex2bin($power_wat);
    $binarypower_unpacked = unpack('f*', strrev($binarypower_watt));
    $binarypower_floatValue = $binarypower_unpacked[1];
     
    $power_float = hexToFloat($power_wat);


    $current = $individualBytes[17] . $individualBytes[18] . $individualBytes[19] . $individualBytes[20];
    $current_watt = hex2bin($current);
    $current_unpacked = unpack('f*', strrev($current_watt));
    $current_floatValue = $current_unpacked[1];

    $current_float = hexToFloat($current);


    $bus_voltage = $individualBytes[21] . $individualBytes[22] . '00' . '00';
    $bus_voltage_watt = hex2bin($bus_voltage);
    $bus_voltage_unpacked = unpack('f*', strrev($bus_voltage_watt));
    $bus_voltage_floatValue = $bus_voltage_unpacked[1];

    $bus_voltage_float = hexToFloat(41920000);
    $ac = $individualBytes[23] . $individualBytes[24];
    $ac_float = hexToFloat($ac);

    $db->query("update efactorproduct set power_float='$power_float', power_watt='$binarypower_floatValue', current_float='$current_float', current='$current_floatValue', bus_voltage_float='$bus_voltage_float', bus_voltage='$bus_voltage_floatValue', ac_float='$ac_float', ac_float='$ac_float', power='$power', speed='$speed', Hue='$hue', direction='$direction', rpm='$rpm', led_indication='$led_indication' where node_id='$nodeid'");
}

/*$db->query("update efactorproduct set ping='clear' where node_id='$nodeid'"); 
$db->query("update efactorproduct set internet='true' where node_id='$nodeid'");
$db->query("update efactorproduct set device_time='$dt' where node_id='$nodeid'");*/


//include('https://efactor.tech/app/Models/Items_model.php');

$name=null;
$nodeid=null;
$param=null;
$value=null;
$data=null;
$payload=null;
$to=null;
$url=null;
}
else if($individualBytes[0] == 'A1' && $individualBytes[1] == 'F4'){


//$rpm = $individualBytes[2] . $individualBytes[3];
$rpm = $individualBytes[3] . $individualBytes[2];
$intrpm = hexdec($rpm);




$db->query("update efactorproduct set rpm='$intrpm' where node_id='$nodeid'");





}

else if($individualBytes[0] == 'A1' && $individualBytes[1] == 'F0'){


$power_watt = $individualBytes[3] . $individualBytes[2]. '00' . '00';

/*$binarypower_watt = hex2bin($power_watt);
$floatpower_watt = unpack('f', $binarypower_watt)[1];*/


$binarypower_watt = hex2bin($power_watt);
$binarypower_unpacked = unpack('f*', strrev($binarypower_watt));
$binarypower_floatValue = $binarypower_unpacked[1];


$db->query("update efactorproduct set power_watt='$binarypower_floatValue' where node_id='$nodeid'");



}

else if($individualBytes[0] == 'A1' && $individualBytes[1] == 'F3'){


$current = $individualBytes[3] . $individualBytes[2]. '00' . '00';

/*$binarycurrent = hex2bin($current);
$floatcurrent = unpack('f', $binarycurrent)[1];*/

$current_watt = hex2bin($current);
$current_unpacked = unpack('f*', strrev($current_watt));
$current_floatValue = $current_unpacked[1];


$db->query("update efactorproduct set current='$current_floatValue' where node_id='$nodeid'");



}

else if($individualBytes[0] == 'A1' && $individualBytes[1] == 'F1'){


$bus_voltage = $individualBytes[3] . $individualBytes[2]. '00' . '00';

/*$binarybus_voltage = hex2bin($bus_voltage);
$floatbus_voltage = unpack('f', $binarybus_voltage)[1];*/


$bus_voltage_watt = hex2bin($bus_voltage);
$bus_voltage_unpacked = unpack('f*', strrev($bus_voltage_watt));
$bus_voltage_floatValue = $bus_voltage_unpacked[1];


$db->query("update efactorproduct set bus_voltage ='$bus_voltage_floatValue' where node_id='$nodeid'");



}



else
{


/************************on/off***********************************/

   if($payload == "A1C2010A")
   {
   
       $db->query("update efactorproduct set power='true' where node_id='$nodeid'");  
       $param='power'; 
       $value='true';

   }

   else if($payload == "A1C2000A")
   {
    
     $db->query("update efactorproduct set power='false' where node_id='$nodeid'");
     $param='power';
     $value='false';

   }





   else if($payload == "A1CB000A")
   {
    
     $db->query("update efactorproduct set power='false' where node_id='$nodeid'");
     $param='power';
     $value='false';


     /************/
     $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;
     /***********/





   }
  /***********speed*******************/

  else if($payload == "A1C0010A")
   {
    
     $db->query("update efactorproduct set speed='1' where node_id='$nodeid'");
     $db->query("update efactorproduct set power='true' where node_id='$nodeid'"); 
     $param='Speed';
     $value='1';
      
   }else if($payload == "A1CB010A")
   {
    
     $db->query("update efactorproduct set speed='1' where node_id='$nodeid'");
     $db->query("update efactorproduct set power='true' where node_id='$nodeid'");
     $param='Speed';
     $value='1';
     $power='true';


     /************/
     $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"power\\":' . $power . ',\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     

$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;
     /***********/

      
   }
  
  

  else if($payload == "A1C0020A")
   {
    
     $db->query("update efactorproduct set speed='2' where node_id='$nodeid'");
$db->query("update efactorproduct set power='true' where node_id='$nodeid'"); 

     $param='Speed';
     $value='2';
      
   }

  else if($payload == "A1CB020A")
   {
    
     $db->query("update efactorproduct set speed='2' where node_id='$nodeid'");
     $db->query("update efactorproduct set power='true' where node_id='$nodeid'");
     $param='Speed';
     $value='2';
     $power ='true';


     /************/
     $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;





$param_array = explode(',', $param);

$param_data = array();
foreach ($param_array as $param_item) {
    $param_data[$param_item] = true; // Assuming a boolean value, you can adjust this as needed
}

$payload_data = array(
    $name => $param_data
);







$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"power\\":' . $power . ',\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

     /***********/

      
   }





   else if($payload == "A1C0030A")
   {
    
     $db->query("update efactorproduct set speed='3' where node_id='$nodeid'");
$db->query("update efactorproduct set power='true' where node_id='$nodeid'"); 
     $param='Speed';
     $value='3';
   }

    else if($payload == "A1CB030A")
   {
    
     $db->query("update efactorproduct set speed='3' where node_id='$nodeid'");
     $db->query("update efactorproduct set power='true' where node_id='$nodeid'");
     $param='Speed';
     $power='true';
     $value='3';

     /************/
     $url = 'https://fcm.googleapis.com/fcm/send';
     $update ='updated';
     $space=' ';
    $description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"power\\":' . $power . ',\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     

$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;



     /***********/





   }



   else if($payload == "A1C0040A")
   {
    
     $db->query("update efactorproduct set speed='4' where node_id='$nodeid'");
    $db->query("update efactorproduct set power='true' where node_id='$nodeid'"); 
     $param='Speed';
     $value='4';

    

      
   }
   else if($payload == "A1CB040A")
   {
    
     $db->query("update efactorproduct set speed='4' where node_id='$nodeid'");
     $db->query("update efactorproduct set power='true' where node_id='$nodeid'");
     $param='Speed';
      $power='true';
     $value='4';

     /************/
     $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"power\\":' . $power . ',\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     


$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

     /***********/



      
   }
  
   else if($payload == "A1C0050A")
   {
    
     $db->query("update efactorproduct set speed='5' where node_id='$nodeid'");
     $db->query("update efactorproduct set power='true' where node_id='$nodeid'"); 
     
     $param='Speed';
     $value='5';



      
   }

    else if($payload == "A1CB050A")
   {
    
     $db->query("update efactorproduct set speed='5' where node_id='$nodeid'");
      $db->query("update efactorproduct set power='true' where node_id='$nodeid'");
     $param='Speed';
     $power='true';
     $value='5';


/************/
   $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"power\\":' . $power . ',\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     


$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$to=null;
$payload=null;

     /***********/
      
   }
   /********************************/
  /***********colour****************/
  else if($payload == "A1C3000A")
   {
    
     $db->query("update efactorproduct set Hue='0' where node_id='$nodeid'");
     $db->query("update efactorproduct set led_indication='true' where node_id='$nodeid'");
     $param='led_color';
     $value='0';
      
   }

  else if($payload == "A1C3010A")
   {
    
     $db->query("update efactorproduct set Hue='1' where node_id='$nodeid'");
     $db->query("update efactorproduct set led_indication='true' where node_id='$nodeid'");
     $param='led_color';
     $value='1';
      
   }

  else if($payload == "A1C3020A")
   {
    
     $db->query("update efactorproduct set Hue='2' where node_id='$nodeid'");
     $db->query("update efactorproduct set led_indication='true' where node_id='$nodeid'");
     $param='led_color';
     $value='2';
      
   }
   else if($payload == "A1C3030A")
   {
    
     $db->query("update efactorproduct set Hue='3' where node_id='$nodeid'");
     $db->query("update efactorproduct set led_indication='true' where node_id='$nodeid'");
     $param='led_color';
     $value='3';
      
   }

  else if($payload == "A1C3040A")
   {
    
     $db->query("update efactorproduct set Hue='4' where node_id='$nodeid'");
     $db->query("update efactorproduct set led_indication='true' where node_id='$nodeid'");
     $param='led_color';
     $value='4';
      
   }
   else if($payload == "A1C3050A")
   {
    
    $db->query("update efactorproduct set Hue='5' where node_id='$nodeid'");
    $db->query("update efactorproduct set led_indication='true' where node_id='$nodeid'");
    $param='led_color';
    $value='5';
      
   }

   else if($payload == "A1C3060A")
   {
    
     $db->query("update efactorproduct set Hue='6' where node_id='$nodeid'");
     $db->query("update efactorproduct set led_indication='true' where node_id='$nodeid'");
     $param='led_color';
     $value='6';
      
   }





else if($payload == "A1CC070A")
   {
    
     $db->query("update efactorproduct set led_indication='false' where node_id='$nodeid'");
      $param='led';
     $value='false';

/************/
     $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

     /***********/
   }


/*****************************************************/

else if($payload == "A1CC000A" || $payload == "A1CC010A" || $payload == "A1CC020A" || $payload == "A1CC030A" || $payload == "A1CC040A" || $payload == "A1CC050A" || $payload == "A1CC060A")
   {
    
     $db->query("update efactorproduct set led_indication='false' where node_id='$nodeid'");
      $param='led';
     $value='true';

/************/
     $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

     /***********/
   }

   

/**********************************************************/
    else if($payload == "A1C3070A" )
   {
    
     $db->query("update efactorproduct set led_indication='true' where node_id='$nodeid'");
      $param='led';
     $value='true';


/************/
     $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;
     /***********/
   }


/***********************************************/
/************configuration**********************/

else if($comm_mode == "Destination Enable")
   {
    
/*$payload1 = "CCD40301FFFFFFFFFFFFFFFF7EC2000000000600";
$payload2 = "CCD40301FFFFFFFFFFFFFFFF7EC2000000000600";
$bytes_1_to_7 = substr($payload1, 1, 7);
//$bytes_7_to_8 = substr($payload2, 7, 8);
//echo $bytes_1_to_7; // Output: , worl
//echo $bytes_7_to_8;*/


$bytes_1_to_7 = substr($payload, 1, 7);
$linkdevices=substr($payload, 8);

$db->query("update efactorproduct set link_comm_mode='Destination Enable' where node_id='$nodeid'");
$db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'");
$db->query("update efactorproduct set link_devices='$linkdevices' where node_id='$nodeid'");

//$split= str_split($linkdevices, 32);
//$db->query("update efactorproduct set link_devices='$split' where node_id='$nodeid'");


    
   }

else if($comm_mode == "Source Enable")
   {
    
$bytes_1_to_7 = substr($payload, 1, 7);
$linkdevices=substr($payload, 8);

$db->query("update efactorproduct set link_comm_mode='Source Enable' where node_id='$nodeid'");
$db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'");


$db->query("update efactorproduct set link_devices='$linkdevices' where node_id='$nodeid'");
      
   }


else if($comm_mode == "Source Destination Disable")
   {
   
$bytes_1_to_7 = substr($payload, 1, 7);
$linkdevices=substr($payload, 8);

$db->query("update efactorproduct set link_comm_mode='Source Destination Disable' where node_id='$nodeid'");
$db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'");
$db->query("update efactorproduct set link_devices='$linkdevices' where node_id='$nodeid'");
      
   }

else if($comm_mode == "Source Destination Enable")
   {
 
$bytes_1_to_7 = substr($payload, 1, 7);
$linkdevices=substr($payload, 8);

$db->query("update efactorproduct set link_devices='$linkdevices' where node_id='$nodeid'");
$db->query("update efactorproduct set link_comm_mode='Source Destination Enable' where node_id='$nodeid'");
$db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'");
      
   }


else if($comm_mode == "Link Devices")
   {
 
$bytes_1_to_7 = substr($payload, 1, 7);
$linkdevices=substr($payload, 8);

$db->query("update efactorproduct set link_devices='$linkdevices' where node_id='$nodeid'");
$db->query("update efactorproduct set link_comm_mode='Link Devices' where node_id='$nodeid'");
$db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'");
      
   }

else if($comm_mode == "Unlink Devices")
   {
 
$bytes_1_to_7 = substr($payload, 1, 7);
$unlinkdevices=substr($payload, 8);

$db->query("update efactorproduct set link_devices='$unlinkdevices' where node_id='$nodeid'");
$db->query("update efactorproduct set link_comm_mode='Unlink Devices' where node_id='$nodeid'");
$db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'");
      
   }


else if($comm_mode == "Unlink All")
   {
 
$bytes_1_to_7 = substr($payload, 1, 7);
$unlinkall=substr($payload, 8);

$db->query("update efactorproduct set link_devices='$unlinkall' where node_id='$nodeid'");
$db->query("update efactorproduct set link_comm_mode='Unlink All' where node_id='$nodeid'");
$db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'");
      
   }


else {

//echo 'payload not matched';

$name=null;
$nodeid=null;
$param=null;
$value=null;
$data=null;
$payload=null;
$to=null;
$url=null;

//continue;

}


//print_r($data);

}


/*if($app_version == 'v2')

{

$headers = array(
    'Authorization: key=AAAAtliCgqw:APA91bH5lLMRevBI3pgwbWKgTjXOKiIDaI8BWwta4xMSywZzPPo418reyc2DGboJDklZuyLxfkXS4i-uwAATEKS2jc-BnCEPeTVfKPU3JIdRaavhrqrAfLT0ld-fOdZBmC8GnnU7L3SX',
    'Content-Type: application/json'
);

$curl = curl_init($url);

curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($curl);

if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
   
}


curl_close($curl);
$data=null;
}

*/



}



/*****************************************************************************/
/******************cooker*****************************************************/

else if($device_type == 'cooker')
{

/*-----------------------------------------*/


   $db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'"); 
$individualBytes = str_split($payload, 2);

if ($individualBytes[0] == 'DE' && $individualBytes[1] == 'E7') {
    if ($individualBytes[2] == '00') {
        $power = 'false';
    } else if ($individualBytes[2] == '01') {
        $power = 'true';
    }





    switch ($individualBytes[3]) {

       case '00':
            $cookermode = 'standby';
            $cooker_led = 'Blue';
            break;
        case '01':
            $cookermode = 'pre-timer';
            $cooker_led = 'White';
            break;
        case '02':
            $cookermode = 'wait pre-timer';
            $cooker_led = 'White blinking';
            break;
        case '03':
            $cookermode = 'cooking';
            $cooker_led = 'Red';
            break;
        case '04':
            $cookermode = 'Wait-cooking';
            $cooker_led = 'Red blinking';
            break;
        case '05':
            $cookermode = 'keepwarm';
            $cooker_led = 'Green';
            break;
        case '06':
            $cookermode = 'wait-keepwarm';
            $cooker_led = 'Green blinking';

            break;
        case '07':
            $cookermode = 'soaking';
            $cooker_led = 'White';

            break;


        default:
            $cookermode = '';
            break;
    }

   

    
   //$cook_time = $individualBytes[4];
    $cook_time_1 = $individualBytes[4];
    $cook_time = hexdec($cook_time_1);



      $cookruntemp_1 = $individualBytes[5].$individualBytes[6];
    $cook_run_temp = hexdec($cookruntemp_1);


   $cook_max_temp_1 = $individualBytes[7].$individualBytes[8];
    $cook_max_temp = hexdec($cook_max_temp_1);




switch ($individualBytes[9]) {
        case '00':
            $recipe = 'RICE';
            break;
        case '01':
            $recipe = 'BROWNRICE';
            break;
        case '02':
            $recipe = 'FRY';
            break;
       case '03':
            $recipe = 'MILK';
            break;
        case '04':
            $recipe = 'SOUP';
            break;
        case '05':
            $recipe = 'BIRIYANI';
            break;

       case '06':
            $recipe = 'IDLY';
            break;
        case '07':
            $recipe = 'DHAL';
            break;
        case '08':
            $recipe = 'HARDVEG';
            break;
         case '09':
            $recipe = 'WATER';
            break;
         case '0A':
            $recipe = 'DEEPFRY';
            break;
         case '0B':
            $recipe = 'POPCORN';
            break;
         case '0C':
            $recipe = 'CAKE';
            break;

         case '0D':
            $recipe = 'MAGGI';
            break;
         case '0E':
            $recipe = 'NOODLES';
            break;


         case '0F':
            $recipe = 'DIY';
            break;



        default:
            $recipe = '';
            break;
    }
    
    


 
    $db->query("update efactorproduct set cook_time='$cook_time', cookermode='$cookermode',cooker_led='$cooker_led', cook_run_temp='$cook_run_temp', power='$power', cook_max_temp='$cook_max_temp', recipe='$recipe' where node_id='$nodeid'");


/*$db->query("update efactorproduct set ping='clear' where node_id='$nodeid'"); 
$db->query("update efactorproduct set internet='true' where node_id='$nodeid'");
$db->query("update efactorproduct set device_time='$dt' where node_id='$nodeid'");*/

$name=null;
$nodeid=null;
$param=null;
$value=null;
$data=null;
$payload=null;
$to=null;
$url=null;
}



/*----------------------------------------------*/



          $db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'"); 

         if($payload == "A1C2010A")
           {
   
              $db->query("update efactorproduct set power='true' where node_id='$nodeid'");   
              $param='power';
              $value='true';
           }

         else if($payload == "A1C2000A")
          {
    
             $db->query("update efactorproduct set power='false' where node_id='$nodeid'");
             $param='power';
             $value='false';   
          }


/*$url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);*/
           


}





/*************************cooker-end*************************/


/********************outlet_starts***************************/

/********************outlet_start****************************/
else if($device_type== 'outlet')
{
 $db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'"); 
   $individualBytes = str_split($payload, 2);
   // Print each byte.
   foreach ($individualBytes as $byte) {
    //echo $byte. "\n";
   }


if($individualBytes[0] == 'DE' && $individualBytes[1] == 'E7')
{

  
    if($individualBytes[2] == '00')
    {
       $db->query("update efactorproduct set power='false' where node_id='$nodeid'");
    }

    else if($individualBytes[2] == '01')
    {
       $db->query("update efactorproduct set power='true' where node_id='$nodeid'");  
    }

//device_set_max

    else if($individualBytes[3] == '06')
     {
        $db->query("update efactorproduct set device_set_max='06' where node_id='$nodeid'");
     }
    else if($individualBytes[3] == '10')
     {
       $db->query("update efactorproduct set device_set_max='10' where node_id='$nodeid'");
     }
   else if($individualBytes[3] == '16')
     {
       $db->query("update efactorproduct set device_set_max='16' where node_id='$nodeid'");
     }
   else if($individualBytes[3] == '32')
    {
       $db->query("update efactorproduct set device_set_max='32' where node_id='$nodeid'");
    }
 

//device_lock

else if($individualBytes[6] == '00')
{
   $db->query("update efactorproduct set device_lock='false' where node_id='$nodeid'");
}

else if($individualBytes[6] == '01')
{
   $db->query("update efactorproduct set device_lock='true' where node_id='$nodeid'");
}



$total_watt = $individualBytes[7].$individualBytes[8];
$total_watt_float = hexToFloat($total_watt);
$db->query("update efactorproduct set total_watt='$total_watt_float' where node_id='$nodeid'");


$usersetmax = $individualBytes[5];

$db->query("update efactorproduct set user_set_max='$usersetmax' where node_id='$nodeid'");


$power_fact = $individualBytes[10];
$power_fact_float = hexToFloat($power_fact);
$db->query("update efactorproduct set power_fact='$power_fact_float' where node_id='$nodeid'");


$power = $individualBytes[11].$individualBytes[12].$individualBytes[13].$individualBytes[14];
$power_float = hexToFloat($power);
$db->query("update efactorproduct set power_watt='$power_float' where node_id='$nodeid'");



$current = $individualBytes[16].$individualBytes[17].$individualBytes[18].$individualBytes[19];
$current_float = hexToFloat($current);
$db->query("update efactorproduct set current='$current_float' where node_id='$nodeid'");

$units = $individualBytes[22].$individualBytes[23].'00'.'00';
$units_float = hexToFloat($units);
//echo $units_float; 
$db->query("update efactorproduct set $units='$units_float' where node_id='$nodeid'");


$ac = $individualBytes[20].$individualBytes[21];
$ac_float = hexToFloat($ac);
 


$db->query("update efactorproduct set ac_volt='$ac_float' where node_id='$nodeid'");

/*$db->query("update efactorproduct set ping='clear' where node_id='$nodeid'"); 
$db->query("update efactorproduct set internet='true' where node_id='$nodeid'");
$db->query("update efactorproduct set device_time='$dt' where node_id='$nodeid'");*/

   }


}

/*******************outlet_ends*******************************/

/********************swpcp_start****************************/
else if($device_type== 'swpcp')
{
 $db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'"); 
   $individualBytes = str_split($payload, 2);
   // Print each byte.
   foreach ($individualBytes as $byte) {
    //echo $byte. "\n";
   }

/***********************************************/

if($individualBytes[0] == 'DE' && $individualBytes[1] == 'E7')
{

  
    if($individualBytes[2] == '00')
    {
       $db->query("update efactorproduct set power='false' where node_id='$nodeid'");
    }

    else if($individualBytes[2] == '01')
    {
       $db->query("update efactorproduct set power='true' where node_id='$nodeid'");  
    }

$devicesetmax = $individualBytes[3];
$db->query("update efactorproduct set USER_AND_DEVICE_MAX_CURRENT_CUTOFF_LIMIT='$devicesetmax' where node_id='$nodeid'");


$usersetmax = $individualBytes[4];
$db->query("update efactorproduct set USER_SET_MAX_CURRENT_CUTOFF_LIMIT='$usersetmax' where node_id='$nodeid'");

$alarm = $individualBytes[5];
$db->query("update efactorproduct set swpcp_alarm='$alarm' where node_id='$nodeid'");


//device_lock

if($individualBytes[6] == '00')
{
   $db->query("update efactorproduct set device_lock='false' where node_id='$nodeid'");
}

else if($individualBytes[6] == '01')
{
   $db->query("update efactorproduct set device_lock='true' where node_id='$nodeid'");
}




$total_watt = $individualBytes[7].$individualBytes[8];
$total_watt_float = hexToFloat($total_watt);
$db->query("update efactorproduct set total_watt='$total_watt_float' where node_id='$nodeid'");


$enaordis = $individualBytes[9];
$db->query("update efactorproduct set swpcp_enaordis='$enaordis' where node_id='$nodeid'");

$power_fact = $individualBytes[10];
$power_fact_float = hexToFloat($power_fact);
$db->query("update efactorproduct set power_fact='$power_fact_float' where node_id='$nodeid'");


$power = $individualBytes[11].$individualBytes[12].$individualBytes[13].$individualBytes[14];
$power_float = hexToFloat($power);
$db->query("update efactorproduct set power_watt='$power_float' where node_id='$nodeid'");



$current = $individualBytes[15].$individualBytes[16].$individualBytes[17].$individualBytes[18];
$current_float = hexToFloat($current);
$db->query("update efactorproduct set current='$current_float' where node_id='$nodeid'");


$voltage = $individualBytes[19].$individualBytes[20];
$voltage_float = hexdec($voltage);
//$db->query("update efactorproduct set voltage='$voltage_float' where node_id='$nodeid'");
$db->query("update efactorproduct set ac_float='$voltage_float' where node_id='$nodeid'");


$units = $individualBytes[21].$individualBytes[22].'00'.'00';
$units_float = hexToFloat($units);
//echo $units_float; 
$db->query("update efactorproduct set $units='$units_float' where node_id='$nodeid'");


/*$ac = $individualBytes[19].$individualBytes[20];
$ac_float = hexdec($ac);*/


$starter_relay = $individualBytes[25];
$starter_relay_off_timer = hexdec($starter_relay);
$db->query("update efactorproduct set starter_relay_off_timer='$starter_relay_off_timer' where node_id='$nodeid'");



$timer_1 = $individualBytes[26];
$timer = hexdec($timer_1);
$db->query("update efactorproduct set timer='$timer' where node_id='$nodeid'");


$remtime = $individualBytes[27];
$rem_time = hexdec($remtime);
$db->query("update efactorproduct set rem_time='$rem_time' where node_id='$nodeid'");

$start_high = $individualBytes[28];
$start_high_current_cutoff = hexdec($start_high);
$db->query("update efactorproduct set start_high_current_cutoff='$start_high_current_cutoff' where node_id='$nodeid'");

$start_low = $individualBytes[29];
$start_low_current_cutoff = hexdec($start_low);
$db->query("update efactorproduct set start_high_current_cutoff='$start_high_current_cutoff' where node_id='$nodeid'");


$Run_high = $individualBytes[30];
$Run_high_current_cutoff = hexdec($Run_high);
$db->query("update efactorproduct set Run_high_current_cutoff='$Run_high_current_cutoff' where node_id='$nodeid'");

$Run_low = $individualBytes[31];
$Run_low_current_cutoff = hexdec($Run_low);
$db->query("update efactorproduct set Run_low_current_cutoff='$Run_low_current_cutoff' where node_id='$nodeid'");


$high_voltage = $individualBytes[32].$individualBytes[33];
$high_voltage_cutoff = hexdec($high_voltage);
$db->query("update efactorproduct set high_voltage_cutoff='$high_voltage_cutoff' where node_id='$nodeid'");

$low_voltage = $individualBytes[34].$individualBytes[35];
$low_voltage_cutoff = hexdec($low_voltage);
$db->query("update efactorproduct set low_voltage_cutoff='$low_voltage_cutoff' where node_id='$nodeid'");

$ultra_high_cur = $individualBytes[36];
$ultra_high_current = hexdec($ultra_high_cur );
$db->query("update efactorproduct set ultra_high_current_cutoff='$ultra_high_current' where node_id='$nodeid'");


$ultra_high_volt = $individualBytes[37].$individualBytes[38];
$ultra_high_voltage = hexdec($ultra_high_volt);
$db->query("update efactorproduct set ultra_high_voltage_cutoff='$ultra_high_voltage' where node_id='$nodeid'");

$error= $individualBytes[39];
$error_code = hexdec($error);
$db->query("update efactorproduct set error_code='$error_code' where node_id='$nodeid'");






   }

/************************************************************/

else if($payload == "A1C2010A")
           {
   
              $db->query("update efactorproduct set power='true' where node_id='$nodeid'");   
              $param='power';
              $value='true';




            $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
           }

         else if($payload == "A1C2000A")
          {
    
             $db->query("update efactorproduct set power='false' where node_id='$nodeid'");
             $param='power';
             $value='false';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }


 else if($payload == "A1EE010A")
          {
    
             $db->query("update efactorproduct set errorcode='01' where node_id='$nodeid'");
             $param='ULTRA HIGH CURRENT';
             $value='01';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }


else if($payload == "A1EE020A")
          {
    
             $db->query("update efactorproduct set errorcode='02' where node_id='$nodeid'");
             $param='START HIGH CURRENT';
             $value='02';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }

else if($payload == "A1EE030A")
          {
    
             $db->query("update efactorproduct set errorcode='03' where node_id='$nodeid'");
             $param='low current';
             $value='03';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }


else if($payload == "A1EE040A")
          {
    
             $db->query("update efactorproduct set errorcode='04' where node_id='$nodeid'");
             $param='RUN HIGH CURRENT';
             $value='04';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }



else if($payload == "A1EE050A")
          {
    
             $db->query("update efactorproduct set errorcode='05' where node_id='$nodeid'");
             $param='high current';
             $value='05';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }

else if($payload == "A1EE060A")
          {
    
             $db->query("update efactorproduct set errorcode='06' where node_id='$nodeid'");
             $param='ULTRA HIGH VOLTAGE ';
             $value='06';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }


else if($payload == "A1EE070A")
          {
    
             $db->query("update efactorproduct set errorcode='07' where node_id='$nodeid'");
             $param='HIGH VOLTAGE';
             $value='07';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }


else if($payload == "A1EE080A")
          {
    
             $db->query("update efactorproduct set errorcode='08' where node_id='$nodeid'");
             $param='LOW VOLTAGE';
             $value='08';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }


else if($payload == "A1EE090A")
          {
    
             $db->query("update efactorproduct set errorcode='09' where node_id='$nodeid'");
             $param='DEVICE MAX LIMIT CURRENT';
             $value='09';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }



else if($payload == "A1EE0A0A")
          {
    
             $db->query("update efactorproduct set errorcode='0A' where node_id='$nodeid'");
             $param='USER MAX LIMIT CURRENT';
             $value='0A';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }

else {

//echo 'payload not matched';

$name=null;
$nodeid=null;
$param=null;
$value=null;
$data=null;
$payload=null;
$to=null;
$url=null;

//continue;

}





/**************************************************/

    





/*

if($app_version == 'v2')
{

$headers = array(
    'Authorization: key=AAAAtliCgqw:APA91bH5lLMRevBI3pgwbWKgTjXOKiIDaI8BWwta4xMSywZzPPo418reyc2DGboJDklZuyLxfkXS4i-uwAATEKS2jc-BnCEPeTVfKPU3JIdRaavhrqrAfLT0ld-fOdZBmC8GnnU7L3SX',
    'Content-Type: application/json'
);

$curl = curl_init($url);

curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($curl);

if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
   
}


curl_close($curl);
$data=null;

}*/

/**************************************************************/
}

/*******************swpcp_ends*******************************/


/********************exhaust_hood_start****************************/
else if($device_type== 'exhaust_hood')
{

 $db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'"); 
   /*$db->query("update efactorproduct set ping='clear' where node_id='$nodeid'"); 
$db->query("update efactorproduct set internet='true' where node_id='$nodeid'");*/


   $individualBytes = str_split($payload, 2);
   // Print each byte.
   foreach ($individualBytes as $byte) {
    //echo $byte. "\n";
   }


if($individualBytes[0] == 'DE' && $individualBytes[1] == 'E7')
{

  
    if($individualBytes[2] == '00')
    {
       $db->query("update efactorproduct set power='false' where node_id='$nodeid'");
    }

    else if($individualBytes[2] == '01')
    {
       $db->query("update efactorproduct set power='true' where node_id='$nodeid'");  
    }

//$hood_autoclean = $individualBytes[3];


if($individualBytes[3] == '00')
    {
       $db->query("update efactorproduct set hood_autoclean='false' where node_id='$nodeid'");
    }

    else if($individualBytes[3] == '01')
    {
       $db->query("update efactorproduct set hood_autoclean='true' where node_id='$nodeid'");  
    }

//$db->query("update efactorproduct set hood_autoclean='$hood_autoclean' where node_id='$nodeid'");

$hood_speed = $individualBytes[4];
$db->query("update efactorproduct set hood_speed='$hood_speed' where node_id='$nodeid'");

/*$hood_lamp = $individualBytes[5];
$db->query("update efactorproduct set hood_lamp='$hood_lamp' where node_id='$nodeid'");*/


if($individualBytes[5] == '00')
    {
              $hood_lamp=false;
       $db->query("update efactorproduct set hood_lamp='$hood_lamp' where node_id='$nodeid'");
    }

    else if($individualBytes[5] == '01')
    {
             $hood_lamp=true;
       $db->query("update efactorproduct set hood_lamp='$hood_lamp' where node_id='$nodeid'");  
    }


//device_lock

if($individualBytes[6] == '00')
{
   $db->query("update efactorproduct set device_lock='false' where node_id='$nodeid'");
}

else if($individualBytes[6] == '01')
{
   $db->query("update efactorproduct set device_lock='true' where node_id='$nodeid'");
}



$voltage = $individualBytes[7].$individualBytes[8].$individualBytes[9].$individualBytes[10];
$ac_float = hexToFloat($voltage);
$db->query("update efactorproduct set ac_float='$ac_float' where node_id='$nodeid'");



$current = $individualBytes[11].$individualBytes[12].$individualBytes[13].$individualBytes[14];
$current_float = hexToFloat($current);
$db->query("update efactorproduct set current='$current_float' where node_id='$nodeid'");



$wattage = $individualBytes[15].$individualBytes[16].$individualBytes[17].$individualBytes[18];
$power_watt = hexToFloat($wattage);
$db->query("update efactorproduct set power_watt='$power_watt' where node_id='$nodeid'");


$temp = $individualBytes[19].$individualBytes[20].$individualBytes[21].$individualBytes[22];
$hood_temp = hexToFloat($temp);
$db->query("update efactorproduct set hood_temp='$hood_temp' where node_id='$nodeid'");


/*$db->query("update efactorproduct set ping='clear' where node_id='$nodeid'"); 
$db->query("update efactorproduct set internet='true' where node_id='$nodeid'");
$db->query("update efactorproduct set device_time='$dt' where node_id='$nodeid'");*/

   }


else if($payload == "A130C2010A")
           {
   
              $db->query("update efactorproduct set power='true' where node_id='$nodeid'");   
              $param='power';
              $value='true';




            $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
           }

         else if($payload == "A130C2000A")
          {
    
             $db->query("update efactorproduct set power='false' where node_id='$nodeid'");
             $param='power';
             $value='false';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }


 else if($payload == "A13006000A")
          {
    
             $db->query("update efactorproduct set hood_autoclean=false where node_id='$nodeid'");
             $param='autoclean';
             $value='false';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }


else if($payload == "A13006010A")
          {
    
             $db->query("update efactorproduct set hood_autoclean=true where node_id='$nodeid'");
             $param='autoclean';
             $value='true';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }








 


else if($payload == "A13004000A")
          {
    
             $db->query("update efactorproduct set hood_speed='0' where node_id='$nodeid'");
             $param='airflow';
             $value='0';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }


else if($payload == "A13004010A")
          {
    
             $db->query("update efactorproduct set hood_speed='1' where node_id='$nodeid'");
             $param='airflow';
             $value='1';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }




else if($payload == "A13004020A")
          {
    
             $db->query("update efactorproduct set hood_speed='2' where node_id='$nodeid'");
             $param='airflow';
             $value='2';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }



else if($payload == "A13004030A")
          {
    
             $db->query("update efactorproduct set hood_speed='3' where node_id='$nodeid'");
             $param='airflow';
             $value='3';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }









else if($payload == "A13008000A")
          {
    
             $db->query("update efactorproduct set hood_lamp='false' where node_id='$nodeid'");
             $param='lamp';
             $value='false';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }


else if($payload == "A13008010A")
          {
    
             $db->query("update efactorproduct set hood_lamp='true' where node_id='$nodeid'");
             $param='lamp';
             $value='true';   




                $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

//print_r($data);
          }


/*

if($app_version == 'v2')
{
$headers = array(
    'Authorization: key=AAAAtliCgqw:APA91bH5lLMRevBI3pgwbWKgTjXOKiIDaI8BWwta4xMSywZzPPo418reyc2DGboJDklZuyLxfkXS4i-uwAATEKS2jc-BnCEPeTVfKPU3JIdRaavhrqrAfLT0ld-fOdZBmC8GnnU7L3SX',
    'Content-Type: application/json'
);

$curl = curl_init($url);

curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($curl);

if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
   
}


curl_close($curl);
$data=null;

}


*/




}

/*******************exhaust_hood_ends*******************************/




else if($device_type == 'pcs')
{
 $db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'"); 
$individualBytes = str_split($payload, 2);

if($individualBytes[0] == 'DE' && $individualBytes[1] == 'E7')
{

  
    if($individualBytes[2] == '00')
    {
       $db->query("update efactorproduct set power='false' where node_id='$nodeid'");
    }

    else if($individualBytes[2] == '01')
    {
       $db->query("update efactorproduct set power='true' where node_id='$nodeid'");  
    }


$nurse = $individualBytes[4];
$db->query("update efactorproduct set nurse='$nurse' where node_id='$nodeid'");

$personal_assistant = $individualBytes[6];
$db->query("update efactorproduct set personal_assistant='$personal_assistant' where node_id='$nodeid'");
    
$doctor = $individualBytes[8];
$db->query("update efactorproduct set doctor='$doctor' where node_id='$nodeid'");


/*$db->query("update efactorproduct set ping='clear' where node_id='$nodeid'"); 
$db->query("update efactorproduct set internet='true' where node_id='$nodeid'");
$db->query("update efactorproduct set device_time='$dt' where node_id='$nodeid'");*/
}



}
/************************************************************/

else if($device_type == 'HBMDNC')
{
    $db->query("UPDATE efactorproduct SET payload='$payload' WHERE node_id='$nodeid'");

    $individualBytes = str_split($payload, 2);
    $updates = [];

    if ($individualBytes[0] == 'DE' && $individualBytes[1] == 'E7') {
        $updates['power'] = ($individualBytes[2] == '00') ? 'false' : 'true';
        $updates['nurse'] = ($individualBytes[3] == '00') ? 'false' : 'true';
        $updates['personal_assistant'] = ($individualBytes[4] == '00') ? 'false' : 'true';
        $updates['doctor'] = ($individualBytes[5] == '00') ? 'false' : 'true';

        switch ($individualBytes[6]) {
            case '00':
                $updates['nurse_resp'] = 'off';
                break;
            case '01':
                $updates['nurse_resp'] = 'on';
                break;
            case '02':
                $updates['nurse_resp'] = 'blink';
                break;
            default:
                $updates['nurse_resp'] = '';
                break;
        }

        switch ($individualBytes[7]) {
            case '00':
                $updates['personal_assistant_resp'] = 'off';
                break;
            case '01':
                $updates['personal_assistant_resp'] = 'on';
                break;
            case '02':
                $updates['personal_assistant_resp'] = 'blink';
                break;
            default:
                $updates['personal_assistant_resp'] = '';
                break;
        }

        switch ($individualBytes[8]) {
            case '00':
                $updates['doctor_resp'] = 'off';
                break;
            case '01':
                $updates['doctor_resp'] = 'on';
                break;
            case '02':
                $updates['doctor_resp'] = 'blink';
                break;
            default:
                $updates['doctor_resp'] = '';
                break;
        }

        $updateStrings = [];
        foreach ($updates as $key => $value) {
            $updateStrings[] = "$key = '$value'";
        }
        $updateQuery = "UPDATE efactorproduct SET " . implode(", ", $updateStrings) . " WHERE node_id='$nodeid'";
        $db->query($updateQuery);
    }
}
/************************************************************/




/*****************light_start********************************/
/*else if($device_type == 'light')
{


      $db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'"); 

         if($payload == "A1C2010A")
           {
   
              $db->query("update efactorproduct set power='true' where node_id='$nodeid'");   
              $param='power';
              $value='true';
           }

         else if($payload == "A1C2000A")
          {
    
             $db->query("update efactorproduct set power='false' where node_id='$nodeid'");
             $param='power';
             $value='false';   
          }

       

          else if($payload == "A1C4010A")
           {
   
              $db->query("update efactorproduct set power='true' where node_id='$nodeid'");   
              $param='brightness';
              $value='20';
           }

         else if($payload == "A1C4020A")
          {
    
             $db->query("update efactorproduct set power='false' where node_id='$nodeid'");
             $param='brightness';
             $value='40';   
          }

         else if($payload == "A1C4030A")
           {
   
              $db->query("update efactorproduct set power='true' where node_id='$nodeid'");   
              $param='brightness';
              $value='60';
           }

         else if($payload == "A1C4040A")
          {
    
             $db->query("update efactorproduct set power='false' where node_id='$nodeid'");
             $param='brightness';
             $value='80';   
          }
        else if($payload == "A1C4050A")
           {
   
              $db->query("update efactorproduct set power='true' where node_id='$nodeid'");   
              $param='brightness';
              $value='100';
           }


        

$url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":' . $app_version . ',"id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $current_devicetype . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);








}
*/

/************************light-end*******************************/
   }
     else
   {
 
 //echo 'Device not found';
//exit;

           }


/*-------------------------------------------------------------*/

/*--------------------------------------------------------*/















/*$headers = array(
    'Authorization: key=AAAAtliCgqw:APA91bH5lLMRevBI3pgwbWKgTjXOKiIDaI8BWwta4xMSywZzPPo418reyc2DGboJDklZuyLxfkXS4i-uwAATEKS2jc-BnCEPeTVfKPU3JIdRaavhrqrAfLT0ld-fOdZBmC8GnnU7L3SX',
    'Content-Type: application/json'
);

$curl = curl_init($url);

curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($curl);

if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
   
}


curl_close($curl);
$data=null;*/


/*------------------------------------------------------------------*/
















 

}


/*********************'DEVICE_DATA_AUTO_UPDATE'  end*******************/

/*********************'SET_DEVICE_DETAILS'  start*******************/



else if($event_type  == 'SET_DEVICE_DETAILS')
{

$query = "SELECT * FROM efactorproduct where node_id='$nodeid'";
$device_info = $db->get_results($query); 
$row = $db->get_row($query); 
$device_type=$row['device_type'];
$model_number=$row['model_number'];
$access_token=$row['access_token'];

$to=$row['mob_device_token'];
$name=$row['name'];
$current_devicetype=$row['current_devicetype'];
$app_version=$row['app_version'];

$user_id=$row['user_id'];
$timestamp=round(microtime(true) * 1000);
$num = $db->num_rows($query); 

$dt=date("Y-m-d H:i:s");



$db->query("update efactorproduct set ping='clear' where node_id='$nodeid'"); 
$db->query("update efactorproduct set internet='true' where node_id='$nodeid'");
$db->query("update efactorproduct set device_time='$dt' where node_id='$nodeid'");


     if($num>0)
     {



        $payloadSize = strlen($payload) / 2;
$first_two_bytes_of_payload = substr($payload, 0, 4); 

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
     $resp_defaultvalue = $row['resp_defaultvalue'];
     $product_name = $row['product_name']; 
     $resp_value = str_replace(',', '', $resp_valueinhexmin);
     $first_two_bytes_of_resp = substr($resp_value, 0, 4);
     $third_byte_of_resp = substr($resp_value, 4, 2);

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
    
   }
    
}



$result = [];
 
foreach ($default_name as $stag) {
    $stag_name = $stag['name'];

    foreach ($defaultValues as $entry) {
        if ($stag_name === $entry['name']) {
             $decimalValue = hexdec($entry['value']);
            $result[$stag_name] = $decimalValue;
            break;  
        }
    }
}
 
 $result2 = [];
 
foreach ($default_name as $stag) {
    $stag_name = $stag['name'];

    foreach ($defaultValues2 as $entry) {
        if ($stag_name === $entry['name']) {
        $decimalValue = hexdec($entry['value']);
            $result2[$stag_name] = $decimalValue;
            break;  
        }
    }
}
 
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

        $db->query("update fan set $name='$formattedValue' where node_id='$nodeid'");
    }

    

    
}


}

}



   }
     else
   {
 
 

  }




}

/*********************'SET_DEVICE_DETAILS'  end*******************/
/*********************'SET_DEVICE_DATA'  start*******************/

else if($command_type=='SET_DEVICE_STATUS')
{




$num = $db->num_rows($query); 

     if($num>0)
     {


//$db->query("update $device_type set payload='$payload' where node_id='$nodeid'");

        $payloadSize = strlen($payload) / 2;
        $first_two_bytes_of_payload = substr($payload, 0, 4); 
        
        $payload_query = "SELECT * FROM product_api WHERE model_name='EFACT V1.1'";
        $payload_info = $db->get_results($payload_query);
        
        foreach ($payload_info as $row) {
        
        
             $parameter_name = $row['parameter_name'];
             $cmd_valueinhexmin = $row['cmd_valueinhexmin'];
        
             $product_name = $row['product_name']; 
            //  $product_name = 'Fan'; 
             $cmd_value = str_replace(',', '', $cmd_valueinhexmin);
             $first_two_bytes_of_cmd = substr($cmd_value, 0, 4);
             $third_byte_of_cmd = substr($cmd_value, 4, 2);
        
            if ($first_two_bytes_of_cmd === $first_two_bytes_of_payload) {
        
             echo $product_name;
             $db->query("update $device_type set $parameter_name='$third_byte_of_cmd' where node_id='$nodeid'");
             echo "Match found for parameter: " . $parameter_name . " - " . $first_two_bytes_of_cmd . "\n";
        
            } else {
                    }
        }
        
        
    
   }
     else
   {
 
 //echo 'Device not found';

           }


}
/*********************'SET_DEVICE_DATA'  END*******************/



/*********************AUTO_TEST_START********************************************************/

else if($event_type=="AUTO_TEST")
{

//echo $payload;

$query = "SELECT * FROM efactorproduct where node_id='$address'";
$device_info = $db->get_results($query); 
$row = $db->get_row($query); 
$device_type=$row['device_type'];
$model_number=$row['model_number'];
$access_token=$row['access_token'];

$to=$row['mob_device_token'];
$name=$row['name'];
$current_devicetype=$row['current_devicetype'];
$app_version=$row['app_version'];

$user_id=$row['user_id'];
//date_default_timezone_set('Asia/Kolkata');
//$timestamp = time();
$timestamp=round(microtime(true) * 1000);
//echo $timestamp;
$num = $db->num_rows($query); 

$dt=date("Y-m-d H:i:s");


$db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'");



if($payload == "CD00DA010A")
           {
 

//  SSID PASSWORD CD0345666163746F722C65666163746F726C7464403234

//  SSID CD0145574946495f3745303030303432

  
              $mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
                               "address"=>"FFFFFFFFFFFFFFFF7EC100000000A300",
                           "device_id"=>"FFFFFFFFFFFFFFFF7EC2000000004200",
                              "payload"=>"CD0345666163746F722C65666163746F726C7464403234");
             $mqtt_json_1=json_encode($mqtt_json);
            // echo json_encode($mqtt_json)."\n";
             $efactor='Efactor';
             $things='things';
             $command='command';

              $auto_tester="FFFFFFFFFFFFFFFF7EC100000000A300";

             $topic=$efactor."/".$things."/".$auto_tester."/".$command;
            // echo $topic;
               if ($mqtt->connect(true, NULL, $username, $password)) 
             {
               $mqtt->publish($topic,$mqtt_json_1);
              }
  
             
            
           }


else if($payload == "CD00DA030A")

{

$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
                               "address"=>"FFFFFFFFFFFFFFFF7EC100000000A300",
                           "device_id"=>"FFFFFFFFFFFFFFFF7EC2000000004200",
                              "payload"=>"EC01A1E70A");
             $mqtt_json_1=json_encode($mqtt_json);
            // echo json_encode($mqtt_json)."\n";
             $efactor='Efactor';
             $things='things';
             $command='command';

              $auto_tester="FFFFFFFFFFFFFFFF7EC100000000A300";

             $topic=$efactor."/".$things."/".$auto_tester."/".$command;
            // echo $topic;
               if ($mqtt->connect(true, NULL, $username, $password)) 
             {
               $mqtt->publish($topic,$mqtt_json_1);
              }


}


else 
{
/*$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
                               "address"=>"FFFFFFFFFFFFFFFF7EC100000000A300",
                           "device_id"=>"FFFFFFFFFFFFFFFF7EC2000000004200",
                              "payload"=>"EC00AFDA010A");
             $mqtt_json_1=json_encode($mqtt_json);
            // echo json_encode($mqtt_json)."\n";
             $efactor='Efactor';
             $things='things';
             $command='command';

              $auto_tester="FFFFFFFFFFFFFFFF7EC100000000A300";

             $topic=$efactor."/".$things."/".$auto_tester."/".$command;
            // echo $topic;
               if ($mqtt->connect(true, NULL, $username, $password)) 
             {
               $mqtt->publish($topic,$mqtt_json_1);
              }




*/


}


}
/*******************************AUTO_TEST_END*****************************************/


else{
echo 'else';

}

?>