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

/*
function floatToHexString($floatVal) {
    $packed = pack('f', $floatVal); // pack the float into binary
    $hexString = '';

    for ($i = 0; $i < strlen($packed); $i++) {
        $hexString .= sprintf("%02X", ord($packed[$i]));
    }

    return $hexString;
}

*/

/*
function floatToHex($float) {
    $packed = pack('f', $float);
    $hex = unpack('H*', $packed)[1];
    return $hex;
}
*/

function floatToHex($float) {
    $binary = pack('f', $float);

    $hexArray = unpack('H*', $binary);

    $hexString = strtoupper($hexArray[1]);

    $hexString = str_pad($hexString, 8, '0', STR_PAD_LEFT);

    $reversedHexString = implode('', array_reverse(str_split($hexString, 2)));

    return $reversedHexString;
}

function intToHex($intValue) {
   
    return dechex(intval($intValue));  
}
function hexToInt($hexString) {
    
    return hexdec($hexString);
}
function padHexValue($hexValue, $numBytes) {
    // Calculate the required length for the hexadecimal string
    $requiredLength = $numBytes * 2;
    
    // Pad the hexadecimal value with leading zeros if necessary
    return str_pad($hexValue, $requiredLength, '0', STR_PAD_LEFT);
}



if (isset($_GET['action']) && $_GET['action']=='test_details')
{

$startid =$_GET['start_id'];
                        
			$header = apache_request_headers();  
			foreach ($header as $key => $val) {
			
			$query_dev="SELECT node_id FROM efactorproduct where access_token='$val'";
			$doctors = $db->get_results($query_dev); 
			$num_dev = $db->num_rows($query_dev);
                        $row_dev = $db->get_row($query_dev);    
		       // $node_id=$row_dev['node_id'];


$node_id = 'FFFFFFFFFFFFFFFF7EC2000000010300';


                        $query_node="SELECT * FROM efactorproduct where node_id='$node_id'";
			$doctors_node = $db->get_results($query_node); 
			$num_node = $db->num_rows($query_node);
                        $row_node = $db->get_row($query_node);    
		        $nodeid=$row_node['node_id'];
                        $user_id=$row_node['user_id'];
                        $model_name=$row_node['model_number'];

                        $moduletype= $row_node['module_type'];
		        $name_dev=$row_node['name'];
                        $mac=$row_node['mac'];
		        $speed=$row_node['speed'];
			$hue=$row_node['Hue'];
		       // $direction=$row_node['direction'];
		       // $power=$row_node['power'];
			//$baudrate=$row_node['baudrate'];
			$led=$row_node['led_indication'];
	                $power=filter_var($row_node['power'], FILTER_VALIDATE_BOOLEAN);
                       // $led = filter_var($row_node['led_indication'], FILTER_VALIDATE_BOOLEAN);
                                           
                        $devicename=$row_node['device_name']; 
                        $curr_devicetype=$row_node['current_devicetype'];
                        $recipe=$row_node['recipe'];
                        $user_priority=$row_node['user_priority'];
                        $cookermode=$row_node['cookermode'];
                        $cooktime=$row_node['cook_time'];
                        $cook_run_temp=$row_node['cook_run_temp'];
                        $current=$row_node['current'];
                        $voltage=$row_node['ac_float'];
                        $bus_voltage=$row_node['bus_voltage'];
                        $rpm=$row_node['rpm'];
                        $timer=$row_node['timer'];
                        $watt=$row_node['power_watt'];
                        $rem_time=$row_node['rem_time'];
                        $hood_autoclean=$row_node['hood_autoclean'];
                        $hood_speed=$row_node['hood_speed'];
                        $hood_lamp=$row_node['hood_lamp'];



//echo $speed;



//$node_id = 'FFFFFFFFFFFFFFFF7EC2000000004200';



$query = "SELECT * FROM product_api WHERE model_name = '$model_name'/* AND user_id = '$user_id'*/";
$result = $db->get_results($query);
$num = count($result); // Count the number of rows in the results
$row = $result; // Get the first row of the results

/*------------------------------------------------------------*/

$frames = array();

        // Iterate through each row in the result set
        foreach ($result as $row) {
            $frames[] = array(
                "name" => $row["cmd_fieldDescription"],
                "type" => ($row["cmd_valueDefinition"] == "fixed value") ? "fixed" : "variable",
                "data_type" => $row["cmd_dataType"],
                "no_of_bytes" => (int)$row["cmd_numberOfBytes"],
                "byte_position" => array(
                    "start" => (int)$row["cmd_startBytePosition"],
                    "end" => (int)$row["cmd_endBytePosition"]
                ),
                "bit_size" => (int)$row["cmd_bitSize"],
                "display_value" => array(
                    "min" => ($row["cmd_displayValueMin"] !== null) ? $row["cmd_displayValueMin"] : "",
                    "max" => ($row["cmd_displayValueMax"] !== null) ? $row["cmd_displayValueMax"] : ""
                ),
                "value_in_hex" => array(
                    "min" => $row["cmd_valueInHexMin"],
                    "max" => $row["cmd_valueInHexMax"]
                )
            );
        }

        



 
$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'/* AND user_id = '$user_id'*/";
        $doctors = $db->get_results($query_1); 
        $num_11 = $db->num_rows($query_1);
        $row = $db->get_row($query_1); 
        $parameter_name = $row['parameter_name'];



//$parameters = ['power', 'speed', 'led', 'direction'];

// Array to store the final result
$result = [];



 
foreach ($doctors as $param) {
   
    $fields_cmd = [];
    $fields_resp = [];
    $fields_error = [];

$param1=$param['parameter_name'];

//echo $param1;

//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFFN02' AND user_id = '177' AND parameter_name='$param1'";
  $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name'/* AND user_id = '$user_id'*/ AND parameter_name='$param1'";
        $params = $db->get_results($query_param); 
        $num_param = $db->num_rows($query_param);
        $row_param = $db->get_row($query_param); 
        $parameter_name = $row_param['parameter_name'];

        $brand_name = $row_param['brand_name'];
        $brand_logo = $row_param['brand_logo'];
        $product_name = $row_param['product_name'];
        $model_name = $row_param['model_name'];
        $product_description = $row_param['product_description'];
        $number_of_pages = $row_param['number_of_pages'];
         $sequence = $row_param['sequence_no'];
        $page_names = $row_param['page_names'];
        $date = $row_param['date'];
        $itemid = $row_param['itemid'];
        $filename = $row_param['filename'];
        $apidate = $row_param['apidate'];
        $parameter_name = $row_param['parameter_name'];
        $cmd_dataflow = $row_param['cmd_dataflow'];
        $cmd_framesize = $row_param['cmd_framesize'];
        $cmd_frametrigger = $row_param['cmd_frametrigger'];
        $resp_dataflow = $row_param['resp_dataflow'];
        $resp_framesize = $row_param['resp_framesize'];
        $resp_frametrigger = $row_param['resp_frametrigger'];
        $error_dataflow = $row_param['error_dataflow'];
        $error_framesize = $row_param['error_framesize'];
        $error_frametrigger = $row_param['error_frametrigger'];

            $cmd_startbytePosition = explode(',', $row_param['cmd_startbytePosition']);
            $cmd_endbytePosition = explode(',', $row_param['cmd_endbytePosition']);
            $cmd_fielddescription = explode(',', $row_param['cmd_fielddescription']);
            $cmd_bitsize = explode(',', $row_param['cmd_bitsize']);
            $cmd_datatype = explode(',', $row_param['cmd_datatype']);
            $cmd_valuedefinition = explode(',', $row_param['cmd_valuedefinition']);
            $cmd_displayvaluemin = explode(',', $row_param['cmd_displayvaluemin']);
            $cmd_displayvaluemax = explode(',', $row_param['cmd_displayvaluemax']);
            $cmd_valueinhexmin = explode(',', $row_param['cmd_valueinhexmin']);
            $cmd_valueinhexmax = explode(',', $row_param['cmd_valueinhexmax']);
            $cmd_uitype = explode(',', $row_param['cmd_uitype']);
            $cmd_uiid = explode(',', $row_param['cmd_uiid']);
            $cmd_uipageno = explode(',', $row_param['cmd_uipageno']);
            $cmd_uidescriptioninout = explode(',', $row_param['cmd_uidescriptioninout']);
            $cmd_sliderstepvalue = explode(',', $row_param['cmd_sliderstepvalue']);
            $cmd_defaultvalue = explode(',', $row_param['cmd_defaultvalue']);
            $cmd_developernotes = explode(',', $row_param['cmd_developernotes']);
            $cmd_noofbytes = explode(',', $row_param['cmd_numberOfbytes']);
            $cmd_asperui = explode(',', $row_param['cmd_asperui']);
            $cmd_asperui_values = explode(',', $row_param['cmd_asperui_values']);

            

             
            $resp_startBytePosition = explode(',', $row_param['resp_startBytePosition']);
            $resp_endBytePosition = explode(',', $row_param['resp_endBytePosition']);
            $resp_fieldDescription = explode(',', $row_param['resp_fieldDescription']);
            $resp_numberOfBytes = explode(',', $row_param['resp_numberOfBytes']);
            $resp_bitSize = explode(',', $row_param['resp_bitSize']);
            $resp_dataType = explode(',', $row_param['resp_dataType']);
            $resp_valueDefinition = explode(',', $row_param['resp_valueDefinition']);
            $resp_displayValueMin = explode(',', $row_param['resp_displayValueMin']);
            $resp_displayValueMax = explode(',', $row_param['resp_displayValueMax']);
            $resp_valueInHexMin = explode(',', $row_param['resp_valueInHexMin']);
            $resp_valueInHexMax = explode(',', $row_param['resp_valueInHexMax']);
            $resp_uiType = explode(',', $row_param['resp_uiType']);
            $resp_uiId = explode(',', $row_param['resp_uiId']);
            $resp_uiPageNo = explode(',', $row_param['resp_uiPageNo']);
            $resp_uiDescriptionInOut = explode(',', $row_param['resp_uiDescriptionInOut']);
            $resp_sliderStepValue = explode(',', $row_param['resp_sliderStepValue']);
            $resp_defaultvalue = explode(',', $row_param['resp_defaultvalue']);
            $resp_developerNotes = explode(',', $row_param['resp_developerNotes']);
            $resp_asperui = explode(',', $row_param['resp_asperui']);
            $resp_asperui_values = explode(',', $row_param['resp_asperui_values']);

            $error_startBytePosition = explode(',', $row_param['error_startBytePosition']);
            $error_endBytePosition = explode(',', $row_param['error_endBytePosition']);
            $error_fieldDescription = explode(',', $row_param['error_fieldDescription']);
            $error_numberOfBytes = explode(',', $row_param['error_numberOfBytes']);
            $error_bitSize = explode(',', $row_param['error_bitSize']);
            $error_dataType = explode(',', $row_param['error_dataType']);
            $error_valueDefinition = explode(',', $row_param['error_valueDefinition']);
            $error_displayValueMin = explode(',', $row_param['error_displayValueMin']);
            $error_displayValueMax = explode(',', $row_param['error_displayValueMax']);
            $error_valueInHexMin = explode(',', $row_param['error_valueInHexMin']);
            $error_valueInHexMax = explode(',', $row_param['error_valueInHexMax']);
            $error_uiType = explode(',', $row_param['error_uiType']);
            $error_uiId = explode(',', $row_param['error_uiId']);
            $error_uiPageNo = explode(',', $row_param['error_uiPageNo']);
            $error_uiDescriptionInOut = explode(',', $row_param['error_uiDescriptionInOut']);
            $error_sliderStepValue = explode(',', $row_param['error_sliderStepValue']);
            $error_defaultvalue = explode(',', $row_param['error_defaultvalue']);
            $error_developerNotes = explode(',', $row_param['error_developerNotes']);
            $error_asperui = explode(',', $row_param['error_asperui']);
            $error_asperui_values = explode(',', $row_param['error_asperui_values']);



                        


        $user_id = $row_param['user_id'];
         $ui_sequence_no = $row_param['ui_sequence_no'];
        $ui_options = $row_param['ui_options'];
        $ui_type = $row_param['ui_type'];
        $ui_name = $row_param['ui_name'];
        $ui_cmd_value = $row_param['ui_cmd_value'];
        $ui_range_min = $row_param['ui_range_min'];
        $ui_data_type = $row_param['ui_data_type'];
        $ui_range_max = $row_param['ui_range_max'];
        $ui_permission = $row_param['ui_permission'];
        $ui_action = $row_param['ui_action'];
        $ui_step = $row_param['ui_step'];
        $page_no = $row_param['page_no'];
        $cmd_noofrows = $row_param['cmd_noofrows'];
        $resp_noofrows = $row_param['resp_noofrows'];
        $error_noofrows = $row_param['error_noofrows'];



    /*    $cmd_asperui= array($row_param['cmd_asperui']);

 
        $resp_asperui = $row_param['resp_asperui'];
        $error_asperui = $row_param['error_asperui'];

        $cmd_asperui_values = $row_param['cmd_asperui_values'];*/


        
 

 
        $cmd_multipleui = $row_param['cmd_multipleui'];
        $resp_multipleui = $row_param['resp_multipleui'];
        $error_multipleui = $row_param['error_multipleui'];


        $cmd_triggerui = $row_param['cmd_triggerui'];
        $resp_triggerui = $row_param['resp_triggerui'];
        $error_triggerui = $row_param['error_triggerui'];

       



        $selected = $row_param['selected'];




 





if ($param1 != 'PUSH') {



/*--------------command------------------------------*/



 if($cmd_framesize < 1)
{

$fields_cmd=array();


}

else{



for ($i = 0; $i < $cmd_framesize; $i++) {

 

if($cmd_defaultvalue[$i] == '')
{


$cmd_defaultvalue[$i]= $cmd_fielddescription[$i];
}
else{
$cmd_defaultvalue[$i]= $cmd_defaultvalue[$i];
}


    if($cmd_fielddescription[$i] == 'Data')
{


       if($cmd_uitype[$i] == 'Text(Defaut)')

         {


              
                $field_cmd = [
            "default_name" =>$cmd_defaultvalue[$i],
            "value" => $cmd_valueinhexmin[$i],
            "Field_description" => $cmd_fielddescription[$i],
            "type" => $cmd_valuedefinition[$i],
            "data_type" => $cmd_datatype[$i],
            "no_of_bytes" => (int)$cmd_noofbytes[$i],
            "byte_start_position" => (int)$cmd_startbytePosition[$i],
            "ui_type"  => $cmd_uitype[$i],
            "ui_ids"   => (int)$cmd_uiid[$i],
            "ui_description" => $cmd_uidescriptioninout[$i]
            ];


//$fields_cmd[] = $field_cmd;



          }


else{

//$cmd_value = floatToHex($cmd_asui_val[$i]);

/*if($cmd_value == '00000000')
{
$cmd_value ='0';
}

*/



        
$as_per_ui = $cmd_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

if (empty($as_per_ui_array_from_formatted) || $as_per_ui_array_from_formatted === ['']) {
    $as_per_ui_array_from_formatted = [];  
}

$asperui_values = $cmd_asperui_values[$i];
$asperui_values_array = explode('][', trim($asperui_values, '[]'));
$formatted_asperui_values = implode(',', $asperui_values_array);
$asperui_values_array_from_formatted = explode(',', $formatted_asperui_values);
 
if (empty($asperui_values_array_from_formatted) || $asperui_values_array_from_formatted === ['']) {
    $asperui_values_array_from_formatted = [];  
}



$baudrate_1 = "30";
$direction = "8";

/*
  
 $speed = "05";
$power = "00";
$baudrate = "30";
$direction = "00";
$name = "fan_42";
$current = "0.72";
$voltage = "23.42";
$rpm = "273";
$Watt = "2";
 
*/

if ($param1 =='Speed') {
    $as_per_ui_value =  $speed;
} elseif ($param1 =='power') {
   if ($cmd_datatype[$i] === 'Float') {
   $as_per_ui_value = (float)$power;
} elseif ($cmd_datatype[$i] === 'UnsignedInt') {
    $as_per_ui_value = (int)$power;
} else {
    $as_per_ui_value = ''; 
}
}  elseif ($param1 =='BAUDRATE') {
    $as_per_ui_value =  $baudrate_1;
}   elseif ($param1 =='Direction') {
  //  $as_per_ui_value =  $direction;
      $as_per_ui_value =  '8';

}   elseif ($param1 =='led') {
    $as_per_ui_value =  $led;
} elseif ($param1 =='name') {
    $as_per_ui_value =  $name_dev;
} elseif ($param1 =='Current') {
    $as_per_ui_value =  $current;
} elseif ($param1 =='Voltage') {
    $as_per_ui_value =  $bus_voltage;
  //  $as_per_ui_value =  $voltage;
} elseif ($param1 =='rpm') {
    $as_per_ui_value =  $rpm;
} elseif ($param1 =='Watt') {
    $as_per_ui_value =  $Watt;
} else {
   $as_per_ui_value =''; 
}

if($cmd_datatype[$i] == 'Float' && $cmd_valuedefinition[$i] == 'Fixed Value'){

  $integer_value = $as_per_ui_value;
}elseif($cmd_datatype[$i] == 'UnsignedInt' && $cmd_valuedefinition[$i] == 'Fixed Value') {
  $integer_value = hexToInt($as_per_ui_value);
}elseif($cmd_datatype[$i] == 'String' && $cmd_valuedefinition[$i] == 'Fixed Value') {
  $integer_value = $as_per_ui_value;
}else{

  $integer_value =  $as_per_ui_value;
}

if($cmd_datatype[$i] == 'Float' && $cmd_valuedefinition[$i] == 'Range Value'){
  $integer_value = $as_per_ui_value;
}elseif($cmd_datatype[$i] == 'UnsignedInt' && $cmd_valuedefinition[$i] == 'Range Value') {
  $integer_value = hexToInt($as_per_ui_value);
}elseif($cmd_datatype[$i] == 'String' && $cmd_valuedefinition[$i] == 'Range Value') {
  $integer_value = $as_per_ui_value;
}else{
  $integervalue =  $as_per_ui_value;
}

 


if ($cmd_datatype[$i] === 'Float') {
   // $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
     $integervalue = (float)$integer_value;
    // $integervalue = empty($integer_value) ? (float)$integer_value :  0.0;
    $cmd_value2 = floatToHex($integervalue);
     $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
    $cmd_sliderstep = (float)$cmd_sliderstepvalue[$i];
   $display_min = (float)$cmd_displayvaluemin[$i];
   $display_max = (float)$cmd_displayvaluemax[$i];
} elseif ($cmd_datatype[$i] === 'UnsignedInt') {
  //  $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
     $integervalue = (int)$integer_value;
     $cmd_value2 = intToHex($integervalue);
      $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
    $cmd_sliderstep = (int)$cmd_sliderstepvalue[$i];
    $display_min = (int)$cmd_displayvaluemin[$i];
    $display_max = (int)$cmd_displayvaluemax[$i];
} elseif ($cmd_datatype[$i] === 'String') {
  $integervalue = (string)$integer_value;
   $cmd_value2 = '';
     $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
} else {
    $asperui_values_array_converted = $asperui_values_array_from_formatted;
    $integervalue = '';
     $cmd_value = '';
    $cmd_sliderstep = '';
    $display_min = '';
    $display_max = '';
}


$field_cmd = [
    "default_name" => $cmd_defaultvalue[$i],
    "asperui_value" => $integervalue,  
    "value" => $cmd_value,
    "Field_description" => $cmd_fielddescription[$i],
    "type" => $cmd_valuedefinition[$i],
    "data_type" => $cmd_datatype[$i],
    "no_of_bytes" => (int)$cmd_noofbytes[$i],
    "byte_start_position" => (int)$cmd_startbytePosition[$i],
    "ui_type"  => $cmd_uitype[$i],
    "ui_ids"   => (int)$cmd_uiid[$i],
    "asperui" => $as_per_ui_array_from_formatted,  
    "asperui_values" => $asperui_values_array_from_formatted,
    "ui_description" => $cmd_uidescriptioninout[$i],
    "slider_step_value" => $cmd_sliderstep
];
          /*   $field_cmd["value_in_hex"] = [
                "min" => $cmd_valueinhexmin[$i],
                "max" => $cmd_valueinhexmax[$i]
            ];
*/
 	
    $field_cmd["display_value"] = [
        "min" => $display_min,
        "max" => $display_max
    ];
 

       // $fields_cmd[] = $field_cmd;
 

}

        $fields_cmd[] = $field_cmd;

}

else
{
if($cmd_uitype[$i] == 'None')
{
           $field_cmd = [
                         
            "default_name" =>$cmd_defaultvalue[$i],
            "value" => $cmd_valueinhexmin[$i],
            "Field_description" => $cmd_fielddescription[$i],
            "no_of_bytes" => (int)$cmd_noofbytes[$i],
            "byte_start_position" => (int)$cmd_startbytePosition[$i],
            "ui_type"  => $cmd_uitype[$i],
          /*  "ui_description" => $cmd_uidescriptioninout[$i],
            "ui_ids"   => (int)$cmd_uiid[$i] */
           
                  ];

$fields_cmd[] = $field_cmd;
}
else{

 $field_cmd = [
                         
            "default_name" =>$cmd_defaultvalue[$i],
            "value" => $cmd_valueinhexmin[$i],
            "Field_description" => $cmd_fielddescription[$i],
            "no_of_bytes" => (int)$cmd_noofbytes[$i],
            "byte_start_position" => (int)$cmd_startbytePosition[$i],
            "ui_type"  => $cmd_uitype[$i],
            "ui_description" => $cmd_uidescriptioninout[$i],
            "ui_ids"   => (int)$cmd_uiid[$i] 
           
                  ];

$fields_cmd[] = $field_cmd;


}

}





    }




}







/***************************************************************/





 
 if ($resp_framesize <= 1) {
    $fields_resp = array();
} else {
    // Loop through $resp_framesize and build $fields_resp
    for ($i = 0; $i < $resp_framesize; $i++) {
       


       





        if ($resp_defaultvalue[$i] == '') {
            $resp_defaultvalue[$i] = $resp_fieldDescription[$i];
        } else {
            $resp_defaultvalue[$i] = $resp_defaultvalue[$i];
        }


  

        if ($resp_fieldDescription[$i] == 'Data') {



            if ($resp_uiType[$i] == 'Text(Default)') {
                $field_resp = [
                    "default_name" => $resp_defaultvalue[$i],
                    "value" => $resp_valueInHexMin[$i],
                    "Field_description" => $resp_fieldDescription[$i],
                    "type" => $resp_valueDefinition[$i],
                    "data_type" => $resp_dataType[$i],
                    "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                    "byte_start_position" => (int)$resp_startBytePosition[$i],
                    "ui_type" => $resp_uiType[$i],
                    "ui_ids" => (int)$resp_uiId[$i],
                    "ui_description" => $resp_uiDescriptionInOut[$i]
                ];
            } else {
                // Construct $field_resp


$resp_value = floatToHex($resp_asui_val[$i]);


/*
if($resp_value == '00000000')
{
$resp_value ='0';
}

  */    

  
$as_per_ui = $resp_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

if (empty($as_per_ui_array_from_formatted) || $as_per_ui_array_from_formatted === ['']) {
    $as_per_ui_array_from_formatted = [];  
}

$asperui_values = $resp_asperui_values[$i];
$asperui_values_array = explode('][', trim($asperui_values, '[]'));
$formatted_asperui_values = implode(',', $asperui_values_array);
$asperui_values_array_from_formatted = explode(',', $formatted_asperui_values);

if (empty($asperui_values_array_from_formatted) || $asperui_values_array_from_formatted === ['']) {
    $asperui_values_array_from_formatted = [];  
}

 $asperui_values_array = explode(',', $formatted_asperui_values);
$first_asperui_value = $asperui_values_array[0]; 
 
 
 if ($resp_defaultvalue[$i] =='Speed') {
    $as_per_ui_value =  $speed;
} elseif ($resp_defaultvalue[$i] =='Power') {
   if ($cmd_datatype[$i] === 'Float') {
   $as_per_ui_value = (float)$power;
} elseif ($cmd_datatype[$i] === 'UnsignedInt') {
    $as_per_ui_value = (int)$power;
} else {
    $as_per_ui_value = ''; 
}
}  elseif ($resp_defaultvalue[$i] =='BAUDRATE') {
    $as_per_ui_value =  $baudrate;
}   elseif ($resp_defaultvalue[$i] =='Direction') {
    $as_per_ui_value =  $direction;
}   elseif ($resp_defaultvalue[$i] =='led') {
    $as_per_ui_value =  $led;
} elseif ($resp_defaultvalue[$i] =='name') {
    $as_per_ui_value =  $name_dev;
} elseif ($resp_defaultvalue[$i] =='current') {
    $as_per_ui_value =  $current;
} elseif ($resp_defaultvalue[$i] =='voltage') {
    $as_per_ui_value =  $bus_voltage;
} elseif ($resp_defaultvalue[$i] =='rpm') {
    $as_per_ui_value =  $rpm;
} else {
   $as_per_ui_value =''; 
}

 /*
 if ($resp_dataType[$i] === 'Float') {
 $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
     $resp_value = floatToHex($as_per_ui_value);
     $asperuivalue = (float)$as_per_ui_value;
    $resp_sliderStep= (float)$resp_sliderStepValue[$i];

} elseif ($resp_dataType[$i] === 'UnsignedInt') {
 $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
    $resp_value = intToHex($as_per_ui_value);
     $asperuivalue = (int)$as_per_ui_value;
    $resp_sliderStep= (int)$resp_sliderStepValue[$i];
} else {
 $asperui_values_array_converted = $asperui_values_array_from_formatted;
    $resp_value = ''; 
    $resp_sliderStep =''; 
}*/
if($resp_dataType[$i] == 'Float' && $resp_valueDefinition[$i] == 'Fixed Value'){
  $integer_value = $as_per_ui_value;
}elseif($resp_dataType[$i] == 'UnsignedInt' && $resp_valueDefinition[$i] == 'Fixed Value') {
  $integer_value = hexToInt($as_per_ui_value);
}elseif($cmd_datatype[$i] == 'String' && $cmd_valuedefinition[$i] == 'Fixed Value') {
  $integer_value = $as_per_ui_value;
}else{
  $integer_value =  $as_per_ui_value;
}

if($resp_dataType[$i] == 'Float' && $resp_valueDefinition[$i] == 'Range Value'){
  $integer_value = $as_per_ui_value;
}elseif($resp_dataType[$i] == 'UnsignedInt' && $resp_valueDefinition[$i] == 'Range Value') {
  $integer_value = hexToInt($as_per_ui_value);
}elseif($cmd_datatype[$i] == 'String' && $cmd_valuedefinition[$i] == 'Range Value') {
  $integer_value = $as_per_ui_value;
}else{
  $integer_value =  $as_per_ui_value;
}

   
if ($resp_dataType[$i] === 'Float') {
    //$asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
    $resp_integervalue = (float)$integer_value;
    $resp_value2 = floatToHex($resp_integervalue);
     $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
    $resp_sliderstep = (float)$resp_sliderstepvalue[$i];
} elseif ($resp_dataType[$i] === 'UnsignedInt') {
   // $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
    $resp_integervalue = (int)$integer_value;
     $resp_value2 = intToHex($resp_integervalue);
      $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
    $resp_sliderStep = (int)$resp_sliderStepValue[$i];
}elseif($resp_dataType[$i] === 'String'){
  $respintegervalue = (string)$integer_value;
$resp_value2 = '';
      $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
} else {
    $asperui_values_array_converted = $asperui_values_array_from_formatted;
    $resp_integervalue = $integer_value;
     $resp_value = '';
    $resp_sliderStep = '';
}
         $field_resp = [
                    "default_name" => $resp_defaultvalue[$i],
                    "asperui_value" => $resp_integervalue,
                    "value" => $resp_value,
                    "Field_description" => $resp_fieldDescription[$i],
                    "type" => $resp_valueDefinition[$i],
                    "data_type" => $resp_dataType[$i],
                    "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                    "byte_start_position" => (int)$resp_startBytePosition[$i],
                    "ui_type" => $resp_uiType[$i],
                    "ui_ids" => (int)$resp_uiId[$i],
                    "asperui" => $as_per_ui_array_from_formatted,
                    "asperui_values" => $asperui_values_array_from_formatted,
                    "ui_description" => $resp_uiDescriptionInOut[$i],
                    "slider_step_value" => $resp_sliderStep
                ];

             /*   $field_resp["value_in_hex"] = [
                    "min" => $resp_valueInHexMin[$i],
                    "max" => $resp_valueInHexMax[$i]
                ];*/

            }
            $fields_resp[] = $field_resp;
        } else {


if($resp_uiType[$i] == 'None')
{
            $field_resp = [
                "default_name" => $resp_defaultvalue[$i],
                "value" => $resp_valueInHexMin[$i],
                "Field_description" => $resp_fieldDescription[$i],
                "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                "byte_start_position" => (int)$resp_startBytePosition[$i],
                "ui_type" => $resp_uiType[$i],
               /* "ui_description" => $resp_uiDescriptionInOut[$i],
               "ui_ids" => (int)$resp_uiId[$i] */
            ];
            $fields_resp[] = $field_resp;

}
else{

  $field_resp = [
                "default_name" => $resp_defaultvalue[$i],
                "value" => $resp_valueInHexMin[$i],
                "Field_description" => $resp_fieldDescription[$i],
                "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                "byte_start_position" => (int)$resp_startBytePosition[$i],
                "ui_type" => $resp_uiType[$i],
                "ui_description" => $resp_uiDescriptionInOut[$i],
               "ui_ids" => (int)$resp_uiId[$i] 
            ];
            $fields_resp[] = $field_resp;




}
        }
    }
}



/**********************************************************************/

 if($error_framesize <= 1)
{

$fields_error=array();


}


else{


    for ($i = 0; $i < $error_framesize; $i++) {


$as_per_ui = $error_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

$asperui_values = $error_asperui_values[$i];
$asperui_values_array = explode('][', trim($asperui_values, '[]'));
$formatted_asperui_values = implode(',', $asperui_values_array);
$asperui_values_array_from_formatted = explode(',', $formatted_asperui_values);

$asperui_values_array = explode(',', $formatted_asperui_values);
$first_asperui_value = $asperui_values_array[0]; 

 if ($param1 =='Speed') {
    $as_per_ui_value =  $speed;
} elseif ($param1 =='power') {
   if ($cmd_datatype[$i] === 'Float') {
   $as_per_ui_value = (float)$power;
} elseif ($cmd_datatype[$i] === 'UnsignedInt') {
    $as_per_ui_value = (int)$power;
} else {
    $as_per_ui_value = ''; 
}
}  elseif ($param1 =='BAUDRATE') {
    $as_per_ui_value =  $baudrate;
}   elseif ($param1 =='Direction') {
    $as_per_ui_value =  $direction;
}   elseif ($param1 =='led') {
    $as_per_ui_value =  $led;
} elseif ($param1 =='name') {
    $as_per_ui_value =  $name;
} elseif ($param1 =='Current') {
    $as_per_ui_value =  $current;
} elseif ($param1 =='Voltage') {
    $as_per_ui_value =  $bus_voltage;
} elseif ($param1 =='rpm') {
    $as_per_ui_value =  $rpm;
} else {
   $as_per_ui_value =''; 
}
/*
if ($error_dataType[$i] === 'Float') {
  $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
    $error_value = floatToHex($as_per_ui_value);
     $asperuivalue = (float)$as_per_ui_value;
    $error_sliderStepValue= (float)$resp_sliderStepValue[$i];

} elseif ($error_dataType[$i] === 'UnsignedInt') {
  $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
    $error_value = intToHex($as_per_ui_value);
     $asperuivalue = (float)$as_per_ui_value;
    $error_sliderStepValue= (int)$resp_sliderStepValue[$i];
} else {
$asperui_values_array_converted = $asperui_values_array_from_formatted;
    $error_value = ''; 
    $error_sliderStepValue =''; 
}
 */
  if($error_datatype[$i] == 'Float' || $error_valuedefinition[$i] == 'Fixed value'){
  $integer_value = hexToInt($as_per_ui_value);
   $integervalue = (float)$integer_value;
}elseif($error_datatype[$i] == 'UnsignedInt' || $error_valuedefinition[$i] == 'Fixed value') {
  $integer_value = $as_per_ui_value;
    $integervalue = (int)$integer_value;
}else{
  $integervalue =  $as_per_ui_value;
}

if($error_datatype[$i] == 'Float' || $error_valuedefinition[$i] == 'Range Value'){
  $integervalue = $as_per_ui_value;
}elseif($error_datatype[$i] == 'UnsignedInt' || $error_valuedefinition[$i] == 'Range Value') {
  $integervalue = hexToInt($as_per_ui_value);
}else{
  $integervalue =  $as_per_ui_value;
}

   
if ($error_datatype[$i] === 'Float') {
    $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
    $error_value2 = floatToHex($integervalue);
     $error_value = padHexValue($error_value2, (int)$error_noofbytes[$i]);
    $error_sliderstep = (float)$error_sliderstepvalue[$i];
} elseif ($error_datatype[$i] === 'UnsignedInt') {
    $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
     $error_value2 = intToHex($integervalue);
      $error_value = padHexValue($error_value2, (int)$error_noofbytes[$i]);
    $error_sliderStep = (int)$error_sliderstepvalue[$i];
} else {
    $asperui_values_array_converted = $asperui_values_array_from_formatted;
     $error_value = '';
    $error_sliderStep = '';
}

        // Construct $field_error
        $field_error = [
              "default_name" =>$error_defaultvalue[$i],
               "asperui_value" => $integervalue,
            "value" => error_value,
            "Field_description" => $error_fieldDescription[$i],
            "type" => $error_valueDefinition[$i],
            "data_type" => $error_dataType[$i],
            "no_of_bytes" => (int) $error_numberOfBytes[$i],
            "byte_start_position" => (int) $error_startBytePosition[$i],
            "ui_type"  => $error_uiType[$i],
            "ui_ids"   => (int) $error_uiId[$i],
             "asperui" => $as_per_ui_array_from_formatted,
            "asperui_values" => $asperui_values_array_converted,
            "ui_description" => $error_uiDescriptionInOut[$i],
            "slider_step_value"=> $error_sliderStepValue[$i]
        ];

        // Add display_value and value_in_hex for Slider type
       /* if ($error_uiType[$i] == 'Slider') {
           /* $field_error["display_value"] = [
                "min" => $error_displayValueMin[$i],
                "max" => $error_displayValueMax[$i]
            ];*/
   /*         $field_error["value_in_hex"] = [
                "min" => $error_valueInHexMin[$i],
                "max" => $error_valueInHexMax[$i]
            ];

*/

  if ($error_dataType[$i] == 'UnsignedInt') {
    $field_error["display_value"] = [
        "min" => (int)$error_displayValueMin[$i],
        "max" => (int)$error_displayValueMax[$i]
    ];
} elseif ($error_dataType[$i] == 'Float') {
    $field_error["display_value"] = [
        "min" => (float)$error_displayValueMin[$i],
        "max" => (float)$error_displayValueMax[$i]
    ];
} else {
    $field_error["display_value"] = [
        "min" => $error_displayValueMin[$i],
        "max" => $error_displayValueMax[$i]
    ];
}











      /*  } else {
            $field_error["value_in_hex"] = [
                "value" => $error_valueInHexMin[$i]
            ];
        }*/

        $fields_error[] = $field_error;
    }


}


 
    if ($number_of_pages == 0){
     // if ($param['parameter_name'] == 0) {
        $result_single0 = [
             "seq" => (int)$sequence,
            "frame" => [
                 "name" => $param['parameter_name'],
            "multiple_ui" => [$cmd_multipleui, $resp_multipleui, $error_multipleui],
            "trigger_ui" => [$cmd_triggerui, $resp_triggerui, $error_triggerui],
                "commands" => $fields_cmd,
                "response" => $fields_resp,
                "error" => $fields_error
            ]
        ];

        $page0[] = $result_single0;
         usort($page0, function($a, $b) {
    return $a['seq'] <=> $b['seq'];
});
    } 
     elseif ($number_of_pages == 2) {
        $result_single2 = [
               "seq" => (int)$sequence,
            "frame" => [
                  "name" => $param['parameter_name'],
            "multiple_ui" => [$cmd_multipleui, $resp_multipleui, $error_multipleui],
            "trigger_ui" => [$cmd_triggerui, $resp_triggerui, $error_triggerui],
                "commands" => $fields_cmd,
                "response" => $fields_resp,
                "error" => $fields_error
            ]
        ];

      $page2[] = $result_single2;
       usort($page2, function($a, $b) {
    return $a['seq'] <=> $b['seq'];
});
    }

   elseif ($number_of_pages == 3) {
        $result_single3 = [
               "seq" => (int)$sequence,
            "frame" => [

                 "name" => $param['parameter_name'],
            "multiple_ui" => [$cmd_multipleui, $resp_multipleui, $error_multipleui],
            "trigger_ui" => [$cmd_triggerui, $resp_triggerui, $error_triggerui],
                "commands" => $fields_cmd,
                "response" => $fields_resp,
                "error" => $fields_error
            ]
        ];

      $page3[] = $result_single3;
       usort($page3, function($a, $b) {
    return $a['seq'] <=> $b['seq'];
});
    }

   elseif ($number_of_pages == 1) {
    $name = $param['parameter_name'];
    
        $para = [
              "seq" => (int)$sequence,
            "frame" => [
                 "name" => $param['parameter_name'],
            "multiple_ui" =>[$cmd_multipleui, $resp_multipleui, $error_multipleui],
            "trigger_ui" => [$cmd_triggerui, $resp_triggerui, $error_triggerui], 
                "commands" => $fields_cmd,
                "response" => $fields_resp,
                "error" => $fields_error
            ]
        ];  
// $result[] = $field_name;
        $result[] = $para;
      usort($result, function($a, $b) {
    return $a['seq'] <=> $b['seq'];
});
}

   
     
} else {
echo "no param";
}




}

 
 











$field_name[] = array(
    "value" => $curr_devicetype,
                "name" => "name",
    "Field_description" => "Data",
    "type" => "Fixed Value",
    "data_type" => "String",
    "no_of_bytes" => 0,
    "byte_position" => array(
        "start" => 1,
        "end" => 2
    ),
    "bit_size" => 0,
    "ui_type" => "Text(Default)",
    "ui_ids" => 1,
    "as per ui" => array(),
    "asperui values" => array(),
    "ui_description" => "Read",
    "value_in_hex" => array(
        "value" => "00"
    )
);




$page_name= array(
"page_no"=> (int)$number_of_pages,
"multiple_ui"=> ["false", "false", "false"],
 "trigger_ui"=>["false","false","false"],
"frame" => array(
"commands"=> $field_name,      
"response"=> array(),
"error"=> array()
)
);



$para_result = [];

$para_result = $result;

/*$para_result = array_merge(array($page_name), $result);

$page0 = array_merge(array($page_name), $page0);

$page2 = array_merge(array($page_name), $page2);

$page3 = array_merge(array($page_name), $page3);*/

$para_default_result = $result_default;




/*-------------------------------------------------------------*/
/*************************************************************/

//$node_id = 'FFFFFFFFFFFFFFFF7EC2000000004200';




/*
$localcontrol =array("POP"=> "a8d8e5ce",
                    "Type"=> 0);

$timezone = array("TZ"=>$time_gatelink,
        "TZ-POSIX"=>"PST8PDT,M3.2.0,M11.1.0");


$Baud_rate = array("BR"=>$baudrate_gatelink,
             "BR-POSIX"=>"0");

$uart_msg_id = array("UART_MSG_ID"=>$uartmsgid_gatelink,
               "UART_MSG_ID POSIX"=>"0");

$error_code = array("ERROR CODE"=>$errorcode_gatelink,
              "ERROR CODE POSIX"=>"0");


$comm_mode = array("Efactor Protocol"=>$comm_mode_gatelink,
             "Efactor Protocol Posix"=>"0");*/


$Baud_rate = 9600;

$uart_msg_id = 'null';

$error_code = 'null';


$comm_mode = 'null';



$query_inter = "SELECT * FROM efactorproduct where node_id='$node_id'";
$doctors_inter = $db->get_results($query_inter); 
$row_inter = $db->get_row($query_inter);
$model_number=$row_inter['model_number'];
$devicename=$row_inter['device_name'];
$vendor_name=$row_inter['vendor_name'];
$moduletype=$row_inter['module_type'];
$net_conn_1=$row_inter['internet'];



$node_details = array(
    "id" => $node_id,
    
    "config" => array(
        "attributes" => array(
            array(
                "name" => "serial_num",
                "value" => "1345"
            ),
            array(
                "name" => "model",
                "value" => "wifi"
            )
        ),
        "config_version" => "2020-03-20",
        "devices" => array(
            array(
                "name" => $curr_devicetype,
                "total_pages" => $number_of_pages,
                "attributes" => array(
                    array(
                        "name" => "Device Id",
                        "value" => $node_id
                    ),
                    array(
                        "name" => "mac",
                        "value" => "CC:50:E3:16:1D:6F"
                    )
                ),
                "pages" => array(
                     "page1" => $para_result,
                                        
                                    ),
                "primary" => "power",
                "type" => "efactor.device.fan",
                "brand_logo" => "https://efactor.tech/files/brand_logo/efactor.jpg"
            )
        ),
        "info" => array(
            "fw_version" => "1.5.1",
            "model" => $model_number,
            "name" => "NULL",
            "type" => "WIFI"
        ),
        "node_id" => $node_id
    ),
    "role" => "primary",
    "status" => array(
        "connectivity" => array(
            "connected" => "true",
            "timestamp" => 1717898131809
        )
    )
);




$access_token='eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJwaG9uZV92ZXJpZmllZCI6dHJ1ZSwiY3VzdG9tOnVzZXJfaWQiOiIxNzciLCJ0b2tlbl91c2UiOiJpZCIsImF1dGhfdGltZSI6MTY2NzAyMjI0MiwiZXhwIjoxNjY3MDI1ODQyLCJpYXQiOjE2NjcwMjIyNDIsInBob25lIjoiOTM5MTA0Mzc4MSJ9.q9CrbVNL-PRfAjnvvLGMmazodCk2Y1Lid9gch5ey0R4';




$nextid='null';


$node = json_encode(array("url"=>"action=test_details",
                      "token"=>$access_token,
                      "next_id"=>$nextid,
                      "total"=>1,
                      "node_details"=>array($node_details),
                       "nodes"=>$node_id
                        ));


//echo json_encode(array("node_details" => array($node_details), "nodes" => $node_id));

echo $node;

exit;



/************************************************************/

    }

}



/*****************************test_details*****************/
if (isset($_GET['action']) && $_GET['action']=='test_details_11')
{
$node_id = 'FFFFFFFFFFFFFFFF7EC2000000004200';
$parameter_name = 'Speed';
//$query = "SELECT * FROM product_api WHERE node_id = '$node_id' AND parameter_name = '$parameter_name'";

$query = "SELECT * FROM product_api WHERE model_name = 'EFFN02' AND user_id = '177'";


// Execute the query once
$result = $db->get_results($query);
$num = count($result); // Count the number of rows in the results
$row = $result; // Get the first row of the results

/*------------------------------------------------------------*/

$frames = array();

        // Iterate through each row in the result set
        foreach ($result as $row) {
            $frames[] = array(
                "name" => $row["cmd_fieldDescription"],
                "type" => ($row["cmd_valueDefinition"] == "fixed value") ? "fixed" : "variable",
                "data_type" => $row["cmd_dataType"],
                "no_of_bytes" => (int)$row["cmd_numberOfBytes"],
                "byte_position" => array(
                    "start" => (int)$row["cmd_startBytePosition"],
                    "end" => (int)$row["cmd_endBytePosition"]
                ),
                "bit_size" => (int)$row["cmd_bitSize"],
                "display_value" => array(
                    "min" => ($row["cmd_displayValueMin"] !== null) ? $row["cmd_displayValueMin"] : "",
                    "max" => ($row["cmd_displayValueMax"] !== null) ? $row["cmd_displayValueMax"] : ""
                ),
                "value_in_hex" => array(
                    "min" => $row["cmd_valueInHexMin"],
                    "max" => $row["cmd_valueInHexMax"]
                )
            );
        }

        



 
$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFFN02' AND user_id = '177'";
        $doctors = $db->get_results($query_1); 
        $num_11 = $db->num_rows($query_1);
        $row = $db->get_row($query_1); 
        $parameter_name = $row['parameter_name'];



//$parameters = ['power', 'speed', 'led', 'direction'];

// Array to store the final result
$result = [];



 
foreach ($doctors as $param) {
   
    $fields_cmd = [];
    $fields_resp = [];
    $fields_error = [];

$param1=$param['parameter_name'];



$query_param = "SELECT * FROM product_api WHERE model_name = 'EFFN02' AND user_id = '177' AND parameter_name='$param1'";
        $params = $db->get_results($query_param); 
        $num_param = $db->num_rows($query_param);
        $row_param = $db->get_row($query_param); 
        $parameter_name = $row_param['parameter_name'];





         $brand_name = $row_param['brand_name'];
        $brand_logo = $row_param['brand_logo'];
        $product_name = $row_param['product_name'];
        $model_name = $row_param['model_name'];
        $product_description = $row_param['product_description'];
        $number_of_pages = $row_param['number_of_pages'];
        $page_names = $row_param['page_names'];
        $date = $row_param['date'];
        $itemid = $row_param['itemid'];
        $filename = $row_param['filename'];
        $apidate = $row_param['apidate'];
        $parameter_name = $row_param['parameter_name'];
        $cmd_dataflow = $row_param['cmd_dataflow'];
        $cmd_framesize = $row_param['cmd_framesize'];
        $cmd_frametrigger = $row_param['cmd_frametrigger'];
        $resp_dataflow = $row_param['resp_dataflow'];
        $resp_framesize = $row_param['resp_framesize'];
        $resp_frametrigger = $row_param['resp_frametrigger'];
        $error_dataflow = $row_param['error_dataflow'];
        $error_framesize = $row_param['error_framesize'];
        $error_frametrigger = $row_param['error_frametrigger'];



            $cmd_startbytePosition = explode(',', $row_param['cmd_startbytePosition']);
            $cmd_endbytePosition = explode(',', $row_param['cmd_endbytePosition']);
            $cmd_fielddescription = explode(',', $row_param['cmd_fielddescription']);
            $cmd_bitsize = explode(',', $row_param['cmd_bitsize']);
            $cmd_datatype = explode(',', $row_param['cmd_datatype']);
            $cmd_valuedefinition = explode(',', $row_param['cmd_valuedefinition']);
            $cmd_displayvaluemin = explode(',', $row_param['cmd_displayvaluemin']);
            $cmd_displayvaluemax = explode(',', $row_param['cmd_displayvaluemax']);
            $cmd_valueinhexmin = explode(',', $row_param['cmd_valueinhexmin']);
            $cmd_valueinhexmax = explode(',', $row_param['cmd_valueinhexmax']);
            $cmd_uitype = explode(',', $row_param['cmd_uitype']);
            $cmd_uiid = explode(',', $row_param['cmd_uiid']);
            $cmd_uipageno = explode(',', $row_param['cmd_uipageno']);
            $cmd_uidescriptioninout = explode(',', $row_param['cmd_uidescriptioninout']);
            $cmd_sliderstepvalue = explode(',', $row_param['cmd_sliderstepvalue']);
            $cmd_developernotes = explode(',', $row_param['cmd_developernotes']);
            $cmd_numberOfbytes = explode(',', $row_param['cmd_numberOfbytes']);

            
            $resp_startBytePosition = explode(',', $row_param['resp_startBytePosition']);
            $resp_endBytePosition = explode(',', $row_param['resp_endBytePosition']);
            $resp_fieldDescription = explode(',', $row_param['resp_fieldDescription']);
            $resp_numberOfBytes = explode(',', $row_param['resp_numberOfBytes']);
            $resp_bitSize = explode(',', $row_param['resp_bitSize']);
            $resp_dataType = explode(',', $row_param['resp_dataType']);
            $resp_valueDefinition = explode(',', $row_param['resp_valueDefinition']);
            $resp_displayValueMin = explode(',', $row_param['resp_displayValueMin']);
            $resp_displayValueMax = explode(',', $row_param['resp_displayValueMax']);
            $resp_valueInHexMin = explode(',', $row_param['resp_valueInHexMin']);
            $resp_valueInHexMax = explode(',', $row_param['resp_valueInHexMax']);
            $resp_uiType = explode(',', $row_param['resp_uiType']);
            $resp_uiId = explode(',', $row_param['resp_uiId']);
            $resp_uiPageNo = explode(',', $row_param['resp_uiPageNo']);
            $resp_uiDescriptionInOut = explode(',', $row_param['resp_uiDescriptionInOut']);
            $resp_sliderStepValue = explode(',', $row_param['resp_sliderStepValue']);
            $resp_developerNotes = explode(',', $row_param['resp_developerNotes']);

            $error_startBytePosition = explode(',', $row_param['error_startBytePosition']);
            $error_endBytePosition = explode(',', $row_param['error_endBytePosition']);
            $error_fieldDescription = explode(',', $row_param['error_fieldDescription']);
            $error_numberOfBytes = explode(',', $row_param['error_numberOfBytes']);
            $error_bitSize = explode(',', $row_param['error_bitSize']);
            $error_dataType = explode(',', $row_param['error_dataType']);
            $error_valueDefinition = explode(',', $row_param['error_valueDefinition']);
            $error_displayValueMin = explode(',', $row_param['error_displayValueMin']);
            $error_displayValueMax = explode(',', $row_param['error_displayValueMax']);
            $error_valueInHexMin = explode(',', $row_param['error_valueInHexMin']);
            $error_valueInHexMax = explode(',', $row_param['error_valueInHexMax']);
            $error_uiType = explode(',', $row_param['error_uiType']);
            $error_uiId = explode(',', $row_param['error_uiId']);
            $error_uiPageNo = explode(',', $row_param['error_uiPageNo']);
            $error_uiDescriptionInOut = explode(',', $row_param['error_uiDescriptionInOut']);
            $error_sliderStepValue = explode(',', $row_param['error_sliderStepValue']);
            $error_developerNotes = explode(',', $row_param['error_developerNotes']);



               $user_id = $row_param['user_id'];
        $cmd_numberOfbytes = $row_param['cmd_numberOfbytes'];
        $ui_sequence_no = $row_param['ui_sequence_no'];
        $ui_options = $row_param['ui_options'];
        $ui_type = $row_param['ui_type'];
        $ui_name = $row_param['ui_name'];
        $ui_cmd_value = $row_param['ui_cmd_value'];
        $ui_range_min = $row_param['ui_range_min'];
        $ui_data_type = $row_param['ui_data_type'];
        $ui_range_max = $row_param['ui_range_max'];
        $ui_permission = $row_param['ui_permission'];
        $ui_action = $row_param['ui_action'];
        $ui_step = $row_param['ui_step'];
        $page_no = $row_param['page_no'];
        $cmd_noofrows = $row_param['cmd_noofrows'];
        $resp_noofrows = $row_param['resp_noofrows'];
        $error_noofrows = $row_param['error_noofrows'];
        $cmd_asperui = $row_param['cmd_asperui'];
        $resp_asperui = $row_param['resp_asperui'];
        $error_asperui = $row_param['error_asperui'];
        $selected = $row_param['selected'];



if($param1 != 'BAUDRATE')
{

   /*----------------command------------*/
    for ($i = 0; $i < $cmd_framesize; $i++) {
       
        $field_cmd = [
            "name" => $cmd_fielddescription[$i],
            "type" => $cmd_uitype[$i] == 'Slider' ? 'variable' : 'fixed',
            "data_type" => $cmd_datatype[$i],
            "no_of_bytes" => (int) $cmd_numberOfbytes[$i],
            "byte_position" => [
                "start" => (int) $cmd_startbytePosition[$i],
                "end" => (int) $cmd_endbytePosition[$i]
            ],
            "bit_size" => (int) $cmd_bitsize[$i],
            "ui_type"  => $cmd_uitype[$i],
            "ui_ids"   =>(int)$cmd_uiid[$i],
            "ui_description" => $cmd_uidescriptioninout[$i]

            
        ];

       
        if ($cmd_uitype[$i] == 'Slider') {
            $field_cmd["display_value"] = [
                "min" => $cmd_displayvaluemin[$i],
                "max" => $cmd_displayvaluemax[$i]
            ];
            $field_cmd["value_in_hex"] = [
                "min" => $cmd_valueinhexmin[$i],
                "max" => $cmd_valueinhexmax[$i]
            ];
        } else {
            $field_cmd["value_in_hex"] = [
                "value" => $cmd_valueinhexmin[$i]
            ];
        }



     $fields_cmd[] = $field_cmd;


}
      /*-----------------------------------*/





                                  



 
for ($i = 0; $i < $resp_framesize; $i++) {
       
        $field_resp = [
            "name" => $resp_fieldDescription[$i],
            "type" => $resp_uiType[$i] == 'Slider' ? 'variable' : 'fixed',
            "data_type" => $resp_dataType[$i],
            "no_of_bytes" => (int)$resp_numberOfBytes[$i],
            "byte_position" => [
                "start" => (int) $resp_startBytePosition[$i],
                "end" => (int) $resp_endBytePosition[$i]
            ],
            "bit_size" => (int) $resp_bitSize[$i],
             "ui_type"  => $resp_uitype[$i],
             "ui_ids"   =>(int)$resp_uiid[$i],
            "ui_description" => $resp_uidescriptioninout[$i]

        ];

       
        if ($resp_uiType[$i] == 'Slider') {
            $field_resp["display_value"] = [
                "min" => $resp_displayValueMin[$i],
                "max" => $resp_valueInHexMax[$i]
            ];
            $field_resp["value_in_hex"] = [
                "min" => $resp_valueInHexMin[$i],
                "max" => $resp_valueInHexMax[$i]
            ];
        } else {
            $field_resp["value_in_hex"] = [
                "value" => $resp_valueInHexMin[$i]
            ];
        }

    $fields_resp[] = $field_resp;


}

     /*-------------------------------------*/






 

for ($i = 0; $i < $error_framesize; $i++) {
       
        $field_error = [
            "name" => $error_fieldDescription[$i],
            "type" => $error_uiType[$i] == 'Slider' ? 'variable' : 'fixed',
            "data_type" => $error_dataType[$i],
            "no_of_bytes" => (int)$error_numberOfBytes[$i],
            "byte_position" => [
                "start" => (int)$error_startBytePosition[$i],
                "end" => (int)$error_endBytePosition[$i]
            ],
            "bit_size" => (int) $error_bitSize[$i],
             "ui_type"  => $error_uiType[$i],
             "ui_ids"   =>(int)$error_uiId[$i],
            "ui_description" => $error_uiDescriptionInOut[$i]

        ];

       
        if ($error_uiType[$i] == 'Slider') {
            $field_error["display_value"] = [
                "min" => $error_displayValueMin[$i],
                "max" => $error_displayValueMax[$i]
            ];
            $field_error["value_in_hex"] = [
                "min" => $error_valueInHexMin[$i],
                "max" => $error_valueInHexMax[$i]
            ];
        } else {
            $field_error["value_in_hex"] = [
                "value" => $error_valueInHexMin[$i]
            ];
        }

       // $fields[] = $field_name;
        
        
        $fields_error[] = $field_error;

    }

   
/*-----------------------------------*/









    $para = [
        "name" => $param['parameter_name'],
        "page_no" => $number_of_pages,
        "multiple_ui" => "bool", 
        "trigger_ui" => "bool",
   
        "frame" => [
            "commands" => $fields_cmd,
            "response" => $fields_resp,
            "error" => $fields_error
        ]
    ];








   // $result[] = $field_name;
    $result[] = $para;


}else

{



 /*----------------command------------*/
    for ($i = 0; $i < $cmd_framesize; $i++) {
       
        $field_default_cmd = [
            "name" => $cmd_fielddescription[$i],
            "type" => $cmd_uitype[$i] == 'Slider' ? 'variable' : 'fixed',
            "data_type" => $cmd_datatype[$i],
            "no_of_bytes" => (int) $cmd_numberOfbytes[$i],
            "byte_position" => [
                "start" => (int) $cmd_startbytePosition[$i],
                "end" => (int) $cmd_endbytePosition[$i]
            ],
            "bit_size" => (int) $cmd_bitsize[$i],
            "ui_type"  => $cmd_uitype[$i],
            "ui_ids"   =>(int)$cmd_uiid[$i],
            "ui_description" => $cmd_uidescriptioninout[$i]

            
        ];

       
        if ($cmd_uitype[$i] == 'Slider') {
            $field_cmd["display_value"] = [
                "min" => $cmd_displayvaluemin[$i],
                "max" => $cmd_displayvaluemax[$i]
            ];
            $field_cmd["value_in_hex"] = [
                "min" => $cmd_valueinhexmin[$i],
                "max" => $cmd_valueinhexmax[$i]
            ];
        } else {
            $field_cmd["value_in_hex"] = [
                "value" => $cmd_valueinhexmin[$i]
            ];
        }



     $fields_default_cmd[] = $field_default_cmd;


}
      /*-----------------------------------*/





                                  



 
for ($i = 0; $i < $resp_framesize; $i++) {
       
        $field_default_resp = [
            "name" => $resp_fieldDescription[$i],
            "type" => $resp_uiType[$i] == 'Slider' ? 'variable' : 'fixed',
            "data_type" => $resp_dataType[$i],
            "no_of_bytes" => (int)$resp_numberOfBytes[$i],
            "byte_position" => [
                "start" => (int) $resp_startBytePosition[$i],
                "end" => (int) $resp_endBytePosition[$i]
            ],
            "bit_size" => (int) $resp_bitSize[$i],
             "ui_type"  => $resp_uitype[$i],
             "ui_ids"   =>(int)$resp_uiid[$i],
            "ui_description" => $resp_uidescriptioninout[$i]

        ];

       
        if ($resp_uiType[$i] == 'Slider') {
            $field_resp["display_value"] = [
                "min" => $resp_displayValueMin[$i],
                "max" => $resp_valueInHexMax[$i]
            ];
            $field_resp["value_in_hex"] = [
                "min" => $resp_valueInHexMin[$i],
                "max" => $resp_valueInHexMax[$i]
            ];
        } else {
            $field_resp["value_in_hex"] = [
                "value" => $resp_valueInHexMin[$i]
            ];
        }

    $fields_default_resp[] = $field_default_resp;


}

     /*-------------------------------------*/






 

for ($i = 0; $i < $error_framesize; $i++) {
       
        $field_default_error = [
            "name" => $error_fieldDescription[$i],
            "type" => $error_uiType[$i] == 'Slider' ? 'variable' : 'fixed',
            "data_type" => $error_dataType[$i],
            "no_of_bytes" => (int)$error_numberOfBytes[$i],
            "byte_position" => [
                "start" => (int)$error_startBytePosition[$i],
                "end" => (int)$error_endBytePosition[$i]
            ],
            "bit_size" => (int) $error_bitSize[$i],
             "ui_type"  => $error_uiType[$i],
             "ui_ids"   =>(int)$error_uiId[$i],
            "ui_description" => $error_uiDescriptionInOut[$i]

        ];

       
        if ($error_uiType[$i] == 'Slider') {
            $field_error["display_value"] = [
                "min" => $error_displayValueMin[$i],
                "max" => $error_displayValueMax[$i]
            ];
            $field_error["value_in_hex"] = [
                "min" => $error_valueInHexMin[$i],
                "max" => $error_valueInHexMax[$i]
            ];
        } else {
            $field_error["value_in_hex"] = [
                "value" => $error_valueInHexMin[$i]
            ];
        }

       // $fields[] = $field_name;
        
        
        $fields_default_error[] = $field_default_error;

    }

   
/*-----------------------------------*/









    $para_default = [
        "name" => $param['parameter_name'],
        "page_no" => $number_of_pages,
        "multiple_ui" => "bool", 
        "trigger_ui" => "bool",
   
        "frame" => [
            "commands" => $fields_default_cmd,
            "response" => $fields_default_resp,
            "error" => $fields_default_error
        ]
    ];








   // $result[] = $field_name;
    $result_default[] = $para_default;













}



}

 



/*
$field_name = [
            "name" => "name",
            "data_type" => "String",
           "properties"=> ["read","write"],
           "type" => "efactor.param.name",
           "page_no"=> 1
        ];
*/



$field_name =array(["name" => "name",
            "data_type" => "String",
           "properties"=> ["read","write"],
           "type" => "efactor.param.name",
           "page_no"=> 1]);


/*
$fan_p[]= json_decode($fan_P_1, true);
$fan_p[]= json_decode($fan_P_2, true);
*/


$para_result = [];
$para_result = $field_name;
$para_result = array_merge($para_result, $result);   


$para_default_result = $result_default;
/*-------------------------------------------------------------*/
/*************************************************************/

$node_id = 'FFFFFFFFFFFFFFFF7EC2000000004200';




/*
$localcontrol =array("POP"=> "a8d8e5ce",
                    "Type"=> 0);

$timezone = array("TZ"=>$time_gatelink,
        "TZ-POSIX"=>"PST8PDT,M3.2.0,M11.1.0");


$Baud_rate = array("BR"=>$baudrate_gatelink,
             "BR-POSIX"=>"0");

$uart_msg_id = array("UART_MSG_ID"=>$uartmsgid_gatelink,
               "UART_MSG_ID POSIX"=>"0");

$error_code = array("ERROR CODE"=>$errorcode_gatelink,
              "ERROR CODE POSIX"=>"0");


$comm_mode = array("Efactor Protocol"=>$comm_mode_gatelink,
             "Efactor Protocol Posix"=>"0");*/


$Baud_rate = 9600;

$uart_msg_id = 'null';

$error_code = 'null';


$comm_mode = 'null';



$query_inter = "SELECT * FROM efactorproduct where node_id='$node_id'";
 $doctors_inter = $db->get_results($query_inter); 
 $row_inter = $db->get_row($query_inter);
$model_number=$row_inter['model_number'];
$devicename=$row_inter['device_name'];
$vendor_name=$row_inter['vendor_name'];
$moduletype=$row_inter['module_type'];
$net_conn_1=$row_inter['internet'];



$node_details = array(
    "id" => $node_id,
    "pages" => array(
        "fan_7E000042" => array(
            "page0" => array("Local Control"=>$localcontrol,
                   "Time" =>$timezone,
              "Baud_Rate" =>$Baud_rate,  
            "Uart_Msg_Id" =>$uart_msg_id,
             "Error_Code" =>$error_code,
     "Communication_Mode" =>$comm_mode
                     ),
            "page1" => array(
                "Direction" => 0,
                "led colour" => 0,
                "output" => true,
                "power" => true,
                "Speed" => 1,
                "led" => true,
                "name" => "fan",
                "current(A)" => "1.3860000371933",
                "voltage(V)" => "25.375",
                "rpm" => "394",
                "buzzer" => false
            ),
            "page2" => array(
                "resistance" => "4.2",
                "lmin" => "7.3",
                "lmax" => "8",
                "frequency" => "16000",
                "pole pairs" => "8",
                "open loop current" => "1.5",
                "power limit" => "44",
                "torque limit" => "7000",
                "speed1" => "6",
                "speed2" => "10",
                "speed3" => "16",
                "speed4" => "24",
                "speed5" => "32",
                "speed6" => "35",
                "speed7" => "38",
                "button" => null
            )
        )
    ),
    "config" => array(
        "attributes" => array(
            array(
                "name" => "serial_num",
                "value" => "1345"
            ),
            array(
                "name" => "model",
                "value" => "wifi"
            )
        ),
        "config_version" => "2020-03-20",
        "devices" => array(
            array(
                "name" => "fan_7E000042",
                "total_pages" => $number_of_pages,
                "attributes" => array(
                    array(
                        "name" => "Device Id",
                        "value" => $node_id
                    ),
                    array(
                        "name" => "mac",
                        "value" => "CC:50:E3:16:1D:6F"
                    )
                ),
                "pages" => array(
                    "page0" => $para_default_result,
                    "page1" => $para_result
                    
                                    ),
                "primary" => "power",
                "type" => "efactor.device.fan",
                "brand_logo" => "https://efactor.tech/files/brand_logo/efactor.jpg"
            )
        ),
        "info" => array(
            "fw_version" => "1.5.1",
            "model" => $model_number,
            "name" => "NULL",
            "type" => "WIFI"
        ),
        "node_id" => $node_id
    ),
    "role" => "primary",
    "status" => array(
        "connectivity" => array(
            "connected" => "false",
            "timestamp" => 1717898131809
        )
    )
);




$access_token='eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJwaG9uZV92ZXJpZmllZCI6dHJ1ZSwiY3VzdG9tOnVzZXJfaWQiOiIxNzciLCJ0b2tlbl91c2UiOiJpZCIsImF1dGhfdGltZSI6MTY2NzAyMjI0MiwiZXhwIjoxNjY3MDI1ODQyLCJpYXQiOjE2NjcwMjIyNDIsInBob25lIjoiOTM5MTA0Mzc4MSJ9.q9CrbVNL-PRfAjnvvLGMmazodCk2Y1Lid9gch5ey0R4';
$nextid='null';


$node = json_encode(array("url"=>"action=test_details",
                      "token"=>$access_token,
                      "next_id"=>$nextid,
                      "total"=>1,
                      "node_details"=>array($node_details),
                       "nodes"=>$node_id
                        ));


//echo json_encode(array("node_details" => array($node_details), "nodes" => $node_id));

echo $node;



/************************************************************/



}






/**************************test_nodes**********************************/
if (isset($_GET['action']) && $_GET['action']=='test_nodes')
{

$startid =$_GET['start_id'];
                        
			$header = apache_request_headers();  
			foreach ($header as $key => $val) {
			
			$query_dev="SELECT node_id FROM efactorproduct where access_token='$val'";
			$doctors = $db->get_results($query_dev); 
			$num_dev = $db->num_rows($query_dev);
                        $row_dev = $db->get_row($query_dev);    
		       // $node_id=$row_dev['node_id'];


$node_id = 'FFFFFFFFFFFFFFFF7EC2000000010300';


                        $query_node="SELECT * FROM efactorproduct where node_id='$node_id'";
			$doctors_node = $db->get_results($query_node); 
			$num_node = $db->num_rows($query_node);
                        $row_node = $db->get_row($query_node);    
		        $nodeid=$row_node['node_id'];
                        $user_id=$row_node['user_id'];
                        $model_name=$row_node['model_number'];

                        $moduletype= $row_node['module_type'];
		        $name_dev=$row_node['name'];
                        $mac=$row_node['mac'];
		        $speed=$row_node['speed'];
			$hue=$row_node['Hue'];
		       // $direction=$row_node['direction'];
		       // $power=$row_node['power'];
			//$baudrate=$row_node['baudrate'];
			$led=$row_node['led_indication'];
	                $power=filter_var($row_node['power'], FILTER_VALIDATE_BOOLEAN);
                       // $led = filter_var($row_node['led_indication'], FILTER_VALIDATE_BOOLEAN);
                                           
                        $devicename=$row_node['device_name']; 
                        $curr_devicetype=$row_node['current_devicetype'];
                        $recipe=$row_node['recipe'];
                        $user_priority=$row_node['user_priority'];
                        $cookermode=$row_node['cookermode'];
                        $cooktime=$row_node['cook_time'];
                        $cook_run_temp=$row_node['cook_run_temp'];
                        $current=$row_node['current'];
                        $voltage=$row_node['ac_float'];
                        $bus_voltage=$row_node['bus_voltage'];
                        $rpm=$row_node['rpm'];
                        $timer=$row_node['timer'];
                        $watt=$row_node['power_watt'];
                        $rem_time=$row_node['rem_time'];
                        $hood_autoclean=$row_node['hood_autoclean'];
                        $hood_speed=$row_node['hood_speed'];
                        $hood_lamp=$row_node['hood_lamp'];



//echo $speed;



//$node_id = 'FFFFFFFFFFFFFFFF7EC2000000004200';



$query = "SELECT * FROM product_api WHERE model_name = '$model_name'/* AND user_id = '$user_id'*/";
$result = $db->get_results($query);
$num = count($result); // Count the number of rows in the results
$row = $result; // Get the first row of the results

/*------------------------------------------------------------*/

$frames = array();

        // Iterate through each row in the result set
        foreach ($result as $row) {
            $frames[] = array(
                "name" => $row["cmd_fieldDescription"],
                "type" => ($row["cmd_valueDefinition"] == "fixed value") ? "fixed" : "variable",
                "data_type" => $row["cmd_dataType"],
                "no_of_bytes" => (int)$row["cmd_numberOfBytes"],
                "byte_position" => array(
                    "start" => (int)$row["cmd_startBytePosition"],
                    "end" => (int)$row["cmd_endBytePosition"]
                ),
                "bit_size" => (int)$row["cmd_bitSize"],
                "display_value" => array(
                    "min" => ($row["cmd_displayValueMin"] !== null) ? $row["cmd_displayValueMin"] : "",
                    "max" => ($row["cmd_displayValueMax"] !== null) ? $row["cmd_displayValueMax"] : ""
                ),
                "value_in_hex" => array(
                    "min" => $row["cmd_valueInHexMin"],
                    "max" => $row["cmd_valueInHexMax"]
                )
            );
        }

        



 
$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'/* AND user_id = '$user_id'*/";
        $doctors = $db->get_results($query_1); 
        $num_11 = $db->num_rows($query_1);
        $row = $db->get_row($query_1); 
        $parameter_name = $row['parameter_name'];



//$parameters = ['power', 'speed', 'led', 'direction'];

// Array to store the final result
$result = [];



 
foreach ($doctors as $param) {
   
    $fields_cmd = [];
    $fields_resp = [];
    $fields_error = [];

$param1=$param['parameter_name'];

//echo $param1;

//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFFN02' AND user_id = '177' AND parameter_name='$param1'";
  $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name'/* AND user_id = '$user_id'*/ AND parameter_name='$param1'";
        $params = $db->get_results($query_param); 
        $num_param = $db->num_rows($query_param);
        $row_param = $db->get_row($query_param); 
        $parameter_name = $row_param['parameter_name'];

        $brand_name = $row_param['brand_name'];
        $brand_logo = $row_param['brand_logo'];
        $product_name = $row_param['product_name'];
        $model_name = $row_param['model_name'];
        $product_description = $row_param['product_description'];
        $number_of_pages = $row_param['number_of_pages'];
         $sequence = $row_param['sequence_no'];
        $page_names = $row_param['page_names'];
        $date = $row_param['date'];
        $itemid = $row_param['itemid'];
        $filename = $row_param['filename'];
        $apidate = $row_param['apidate'];
        $parameter_name = $row_param['parameter_name'];
        $cmd_dataflow = $row_param['cmd_dataflow'];
        $cmd_framesize = $row_param['cmd_framesize'];
        $cmd_frametrigger = $row_param['cmd_frametrigger'];
        $resp_dataflow = $row_param['resp_dataflow'];
        $resp_framesize = $row_param['resp_framesize'];
        $resp_frametrigger = $row_param['resp_frametrigger'];
        $error_dataflow = $row_param['error_dataflow'];
        $error_framesize = $row_param['error_framesize'];
        $error_frametrigger = $row_param['error_frametrigger'];

            $cmd_startbytePosition = explode(',', $row_param['cmd_startbytePosition']);
            $cmd_endbytePosition = explode(',', $row_param['cmd_endbytePosition']);
            $cmd_fielddescription = explode(',', $row_param['cmd_fielddescription']);
            $cmd_bitsize = explode(',', $row_param['cmd_bitsize']);
            $cmd_datatype = explode(',', $row_param['cmd_datatype']);
            $cmd_valuedefinition = explode(',', $row_param['cmd_valuedefinition']);
            $cmd_displayvaluemin = explode(',', $row_param['cmd_displayvaluemin']);
            $cmd_displayvaluemax = explode(',', $row_param['cmd_displayvaluemax']);
            $cmd_valueinhexmin = explode(',', $row_param['cmd_valueinhexmin']);
            $cmd_valueinhexmax = explode(',', $row_param['cmd_valueinhexmax']);
            $cmd_uitype = explode(',', $row_param['cmd_uitype']);
            $cmd_uiid = explode(',', $row_param['cmd_uiid']);
            $cmd_uipageno = explode(',', $row_param['cmd_uipageno']);
            $cmd_uidescriptioninout = explode(',', $row_param['cmd_uidescriptioninout']);
            $cmd_sliderstepvalue = explode(',', $row_param['cmd_sliderstepvalue']);
            $cmd_defaultvalue = explode(',', $row_param['cmd_defaultvalue']);
            $cmd_developernotes = explode(',', $row_param['cmd_developernotes']);
            $cmd_noofbytes = explode(',', $row_param['cmd_numberOfbytes']);
            $cmd_asperui = explode(',', $row_param['cmd_asperui']);
            $cmd_asperui_values = explode(',', $row_param['cmd_asperui_values']);

            

             
            $resp_startBytePosition = explode(',', $row_param['resp_startBytePosition']);
            $resp_endBytePosition = explode(',', $row_param['resp_endBytePosition']);
            $resp_fieldDescription = explode(',', $row_param['resp_fieldDescription']);
            $resp_numberOfBytes = explode(',', $row_param['resp_numberOfBytes']);
            $resp_bitSize = explode(',', $row_param['resp_bitSize']);
            $resp_dataType = explode(',', $row_param['resp_dataType']);
            $resp_valueDefinition = explode(',', $row_param['resp_valueDefinition']);
            $resp_displayValueMin = explode(',', $row_param['resp_displayValueMin']);
            $resp_displayValueMax = explode(',', $row_param['resp_displayValueMax']);
            $resp_valueInHexMin = explode(',', $row_param['resp_valueInHexMin']);
            $resp_valueInHexMax = explode(',', $row_param['resp_valueInHexMax']);
            $resp_uiType = explode(',', $row_param['resp_uiType']);
            $resp_uiId = explode(',', $row_param['resp_uiId']);
            $resp_uiPageNo = explode(',', $row_param['resp_uiPageNo']);
            $resp_uiDescriptionInOut = explode(',', $row_param['resp_uiDescriptionInOut']);
            $resp_sliderStepValue = explode(',', $row_param['resp_sliderStepValue']);
            $resp_defaultvalue = explode(',', $row_param['resp_defaultvalue']);
            $resp_developerNotes = explode(',', $row_param['resp_developerNotes']);
            $resp_asperui = explode(',', $row_param['resp_asperui']);
            $resp_asperui_values = explode(',', $row_param['resp_asperui_values']);

            $error_startBytePosition = explode(',', $row_param['error_startBytePosition']);
            $error_endBytePosition = explode(',', $row_param['error_endBytePosition']);
            $error_fieldDescription = explode(',', $row_param['error_fieldDescription']);
            $error_numberOfBytes = explode(',', $row_param['error_numberOfBytes']);
            $error_bitSize = explode(',', $row_param['error_bitSize']);
            $error_dataType = explode(',', $row_param['error_dataType']);
            $error_valueDefinition = explode(',', $row_param['error_valueDefinition']);
            $error_displayValueMin = explode(',', $row_param['error_displayValueMin']);
            $error_displayValueMax = explode(',', $row_param['error_displayValueMax']);
            $error_valueInHexMin = explode(',', $row_param['error_valueInHexMin']);
            $error_valueInHexMax = explode(',', $row_param['error_valueInHexMax']);
            $error_uiType = explode(',', $row_param['error_uiType']);
            $error_uiId = explode(',', $row_param['error_uiId']);
            $error_uiPageNo = explode(',', $row_param['error_uiPageNo']);
            $error_uiDescriptionInOut = explode(',', $row_param['error_uiDescriptionInOut']);
            $error_sliderStepValue = explode(',', $row_param['error_sliderStepValue']);
            $error_defaultvalue = explode(',', $row_param['error_defaultvalue']);
            $error_developerNotes = explode(',', $row_param['error_developerNotes']);
            $error_asperui = explode(',', $row_param['error_asperui']);
            $error_asperui_values = explode(',', $row_param['error_asperui_values']);



                        


        $user_id = $row_param['user_id'];
         $ui_sequence_no = $row_param['ui_sequence_no'];
        $ui_options = $row_param['ui_options'];
        $ui_type = $row_param['ui_type'];
        $ui_name = $row_param['ui_name'];
        $ui_cmd_value = $row_param['ui_cmd_value'];
        $ui_range_min = $row_param['ui_range_min'];
        $ui_data_type = $row_param['ui_data_type'];
        $ui_range_max = $row_param['ui_range_max'];
        $ui_permission = $row_param['ui_permission'];
        $ui_action = $row_param['ui_action'];
        $ui_step = $row_param['ui_step'];
        $page_no = $row_param['page_no'];
        $cmd_noofrows = $row_param['cmd_noofrows'];
        $resp_noofrows = $row_param['resp_noofrows'];
        $error_noofrows = $row_param['error_noofrows'];



    /*    $cmd_asperui= array($row_param['cmd_asperui']);

 
        $resp_asperui = $row_param['resp_asperui'];
        $error_asperui = $row_param['error_asperui'];

        $cmd_asperui_values = $row_param['cmd_asperui_values'];*/


        
 

 
        $cmd_multipleui = $row_param['cmd_multipleui'];
        $resp_multipleui = $row_param['resp_multipleui'];
        $error_multipleui = $row_param['error_multipleui'];


        $cmd_triggerui = $row_param['cmd_triggerui'];
        $resp_triggerui = $row_param['resp_triggerui'];
        $error_triggerui = $row_param['error_triggerui'];

       



        $selected = $row_param['selected'];




 





if ($param1 != 'PUSH') {



/*--------------command------------------------------*/



 if($cmd_framesize < 1)
{

$fields_cmd=array();


}

else{



for ($i = 0; $i < $cmd_framesize; $i++) {

 

if($cmd_defaultvalue[$i] == '')
{


$cmd_defaultvalue[$i]= $cmd_fielddescription[$i];
}
else{
$cmd_defaultvalue[$i]= $cmd_defaultvalue[$i];
}


    if($cmd_fielddescription[$i] == 'Data')
{


       if($cmd_uitype[$i] == 'Text(Defaut)')

         {


              
                $field_cmd = [
            "default_name" =>$cmd_defaultvalue[$i],
            "value" => $cmd_valueinhexmin[$i],
            "Field_description" => $cmd_fielddescription[$i],
            "type" => $cmd_valuedefinition[$i],
            "data_type" => $cmd_datatype[$i],
            "no_of_bytes" => (int)$cmd_noofbytes[$i],
            "byte_start_position" => (int)$cmd_startbytePosition[$i],
            "ui_type"  => $cmd_uitype[$i],
            "ui_ids"   => (int)$cmd_uiid[$i],
            "ui_description" => $cmd_uidescriptioninout[$i]
            ];


//$fields_cmd[] = $field_cmd;



          }


else{

//$cmd_value = floatToHex($cmd_asui_val[$i]);

/*if($cmd_value == '00000000')
{
$cmd_value ='0';
}

*/



        
$as_per_ui = $cmd_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

if (empty($as_per_ui_array_from_formatted) || $as_per_ui_array_from_formatted === ['']) {
    $as_per_ui_array_from_formatted = [];  
}

$asperui_values = $cmd_asperui_values[$i];
$asperui_values_array = explode('][', trim($asperui_values, '[]'));
$formatted_asperui_values = implode(',', $asperui_values_array);
$asperui_values_array_from_formatted = explode(',', $formatted_asperui_values);
 
if (empty($asperui_values_array_from_formatted) || $asperui_values_array_from_formatted === ['']) {
    $asperui_values_array_from_formatted = [];  
}



$baudrate_1 = "30";
$direction = "8";

/*
  
 $speed = "05";
$power = "00";
$baudrate = "30";
$direction = "00";
$name = "fan_42";
$current = "0.72";
$voltage = "23.42";
$rpm = "273";
$Watt = "2";
 
*/

if ($param1 =='Speed') {
    $as_per_ui_value =  $speed;
} elseif ($param1 =='power') {
   if ($cmd_datatype[$i] === 'Float') {
   $as_per_ui_value = (float)$power;
} elseif ($cmd_datatype[$i] === 'UnsignedInt') {
    $as_per_ui_value = (int)$power;
} else {
    $as_per_ui_value = ''; 
}
}  elseif ($param1 =='BAUDRATE') {
    $as_per_ui_value =  $baudrate_1;
}   elseif ($param1 =='Direction') {
  //  $as_per_ui_value =  $direction;
      $as_per_ui_value =  '8';

}   elseif ($param1 =='led') {
    $as_per_ui_value =  $led;
} elseif ($param1 =='name') {
    $as_per_ui_value =  $name_dev;
} elseif ($param1 =='Current') {
    $as_per_ui_value =  $current;
} elseif ($param1 =='Voltage') {
    $as_per_ui_value =  $bus_voltage;
  //  $as_per_ui_value =  $voltage;
} elseif ($param1 =='rpm') {
    $as_per_ui_value =  $rpm;
} elseif ($param1 =='Watt') {
    $as_per_ui_value =  $Watt;
} else {
   $as_per_ui_value =''; 
}

if($cmd_datatype[$i] == 'Float' && $cmd_valuedefinition[$i] == 'Fixed Value'){

  $integer_value = $as_per_ui_value;
}elseif($cmd_datatype[$i] == 'UnsignedInt' && $cmd_valuedefinition[$i] == 'Fixed Value') {
  $integer_value = hexToInt($as_per_ui_value);
}elseif($cmd_datatype[$i] == 'String' && $cmd_valuedefinition[$i] == 'Fixed Value') {
  $integer_value = $as_per_ui_value;
}else{

  $integer_value =  $as_per_ui_value;
}

if($cmd_datatype[$i] == 'Float' && $cmd_valuedefinition[$i] == 'Range Value'){
  $integer_value = $as_per_ui_value;
}elseif($cmd_datatype[$i] == 'UnsignedInt' && $cmd_valuedefinition[$i] == 'Range Value') {
  $integer_value = hexToInt($as_per_ui_value);
}elseif($cmd_datatype[$i] == 'String' && $cmd_valuedefinition[$i] == 'Range Value') {
  $integer_value = $as_per_ui_value;
}else{
  $integervalue =  $as_per_ui_value;
}

 


if ($cmd_datatype[$i] === 'Float') {
   // $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
     $integervalue = (float)$integer_value;
    // $integervalue = empty($integer_value) ? (float)$integer_value :  0.0;
    $cmd_value2 = floatToHex($integervalue);
     $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
    $cmd_sliderstep = (float)$cmd_sliderstepvalue[$i];
   $display_min = (float)$cmd_displayvaluemin[$i];
   $display_max = (float)$cmd_displayvaluemax[$i];
} elseif ($cmd_datatype[$i] === 'UnsignedInt') {
  //  $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
     $integervalue = (int)$integer_value;
     $cmd_value2 = intToHex($integervalue);
      $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
    $cmd_sliderstep = (int)$cmd_sliderstepvalue[$i];
    $display_min = (int)$cmd_displayvaluemin[$i];
    $display_max = (int)$cmd_displayvaluemax[$i];
} elseif ($cmd_datatype[$i] === 'String') {
  $integervalue = (string)$integer_value;
   $cmd_value2 = '';
     $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
} else {
    $asperui_values_array_converted = $asperui_values_array_from_formatted;
    $integervalue = '';
     $cmd_value = '';
    $cmd_sliderstep = '';
    $display_min = '';
    $display_max = '';
}


$field_cmd = [
    "default_name" => $cmd_defaultvalue[$i],
    "asperui_value" => $integervalue,  
    "value" => $cmd_value,
    "Field_description" => $cmd_fielddescription[$i],
    "type" => $cmd_valuedefinition[$i],
    "data_type" => $cmd_datatype[$i],
    "no_of_bytes" => (int)$cmd_noofbytes[$i],
    "byte_start_position" => (int)$cmd_startbytePosition[$i],
    "ui_type"  => $cmd_uitype[$i],
    "ui_ids"   => (int)$cmd_uiid[$i],
    "asperui" => $as_per_ui_array_from_formatted,  
    "asperui_values" => $asperui_values_array_from_formatted,
    "ui_description" => $cmd_uidescriptioninout[$i],
    "slider_step_value" => $cmd_sliderstep
];
          /*   $field_cmd["value_in_hex"] = [
                "min" => $cmd_valueinhexmin[$i],
                "max" => $cmd_valueinhexmax[$i]
            ];
*/
 	
    $field_cmd["display_value"] = [
        "min" => $display_min,
        "max" => $display_max
    ];
 

       // $fields_cmd[] = $field_cmd;
 

}

        $fields_cmd[] = $field_cmd;

}

else
{
if($cmd_uitype[$i] == 'None')
{
           $field_cmd = [
                         
            "default_name" =>$cmd_defaultvalue[$i],
            "value" => $cmd_valueinhexmin[$i],
            "Field_description" => $cmd_fielddescription[$i],
            "no_of_bytes" => (int)$cmd_noofbytes[$i],
            "byte_start_position" => (int)$cmd_startbytePosition[$i],
            "ui_type"  => $cmd_uitype[$i],
          /*  "ui_description" => $cmd_uidescriptioninout[$i],
            "ui_ids"   => (int)$cmd_uiid[$i] */
           
                  ];

$fields_cmd[] = $field_cmd;
}
else{

 $field_cmd = [
                         
            "default_name" =>$cmd_defaultvalue[$i],
            "value" => $cmd_valueinhexmin[$i],
            "Field_description" => $cmd_fielddescription[$i],
            "no_of_bytes" => (int)$cmd_noofbytes[$i],
            "byte_start_position" => (int)$cmd_startbytePosition[$i],
            "ui_type"  => $cmd_uitype[$i],
            "ui_description" => $cmd_uidescriptioninout[$i],
            "ui_ids"   => (int)$cmd_uiid[$i] 
           
                  ];

$fields_cmd[] = $field_cmd;


}

}





    }




}







/***************************************************************/





 
 if ($resp_framesize <= 1) {
    $fields_resp = array();
} else {
    // Loop through $resp_framesize and build $fields_resp
    for ($i = 0; $i < $resp_framesize; $i++) {
       


       





        if ($resp_defaultvalue[$i] == '') {
            $resp_defaultvalue[$i] = $resp_fieldDescription[$i];
        } else {
            $resp_defaultvalue[$i] = $resp_defaultvalue[$i];
        }


  

        if ($resp_fieldDescription[$i] == 'Data') {



            if ($resp_uiType[$i] == 'Text(Default)') {
                $field_resp = [
                    "default_name" => $resp_defaultvalue[$i],
                    "value" => $resp_valueInHexMin[$i],
                    "Field_description" => $resp_fieldDescription[$i],
                    "type" => $resp_valueDefinition[$i],
                    "data_type" => $resp_dataType[$i],
                    "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                    "byte_start_position" => (int)$resp_startBytePosition[$i],
                    "ui_type" => $resp_uiType[$i],
                    "ui_ids" => (int)$resp_uiId[$i],
                    "ui_description" => $resp_uiDescriptionInOut[$i]
                ];
            } else {
                // Construct $field_resp


$resp_value = floatToHex($resp_asui_val[$i]);


/*
if($resp_value == '00000000')
{
$resp_value ='0';
}

  */    

  
$as_per_ui = $resp_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

if (empty($as_per_ui_array_from_formatted) || $as_per_ui_array_from_formatted === ['']) {
    $as_per_ui_array_from_formatted = [];  
}

$asperui_values = $resp_asperui_values[$i];
$asperui_values_array = explode('][', trim($asperui_values, '[]'));
$formatted_asperui_values = implode(',', $asperui_values_array);
$asperui_values_array_from_formatted = explode(',', $formatted_asperui_values);

if (empty($asperui_values_array_from_formatted) || $asperui_values_array_from_formatted === ['']) {
    $asperui_values_array_from_formatted = [];  
}

 $asperui_values_array = explode(',', $formatted_asperui_values);
$first_asperui_value = $asperui_values_array[0]; 
 
 
 if ($resp_defaultvalue[$i] =='Speed') {
    $as_per_ui_value =  $speed;
} elseif ($resp_defaultvalue[$i] =='Power') {
   if ($cmd_datatype[$i] === 'Float') {
   $as_per_ui_value = (float)$power;
} elseif ($cmd_datatype[$i] === 'UnsignedInt') {
    $as_per_ui_value = (int)$power;
} else {
    $as_per_ui_value = ''; 
}
}  elseif ($resp_defaultvalue[$i] =='BAUDRATE') {
    $as_per_ui_value =  $baudrate;
}   elseif ($resp_defaultvalue[$i] =='Direction') {
    $as_per_ui_value =  $direction;
}   elseif ($resp_defaultvalue[$i] =='led') {
    $as_per_ui_value =  $led;
} elseif ($resp_defaultvalue[$i] =='name') {
    $as_per_ui_value =  $name_dev;
} elseif ($resp_defaultvalue[$i] =='current') {
    $as_per_ui_value =  $current;
} elseif ($resp_defaultvalue[$i] =='voltage') {
    $as_per_ui_value =  $bus_voltage;
} elseif ($resp_defaultvalue[$i] =='rpm') {
    $as_per_ui_value =  $rpm;
} else {
   $as_per_ui_value =''; 
}

 /*
 if ($resp_dataType[$i] === 'Float') {
 $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
     $resp_value = floatToHex($as_per_ui_value);
     $asperuivalue = (float)$as_per_ui_value;
    $resp_sliderStep= (float)$resp_sliderStepValue[$i];

} elseif ($resp_dataType[$i] === 'UnsignedInt') {
 $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
    $resp_value = intToHex($as_per_ui_value);
     $asperuivalue = (int)$as_per_ui_value;
    $resp_sliderStep= (int)$resp_sliderStepValue[$i];
} else {
 $asperui_values_array_converted = $asperui_values_array_from_formatted;
    $resp_value = ''; 
    $resp_sliderStep =''; 
}*/
if($resp_dataType[$i] == 'Float' && $resp_valueDefinition[$i] == 'Fixed Value'){
  $integer_value = $as_per_ui_value;
}elseif($resp_dataType[$i] == 'UnsignedInt' && $resp_valueDefinition[$i] == 'Fixed Value') {
  $integer_value = hexToInt($as_per_ui_value);
}elseif($cmd_datatype[$i] == 'String' && $cmd_valuedefinition[$i] == 'Fixed Value') {
  $integer_value = $as_per_ui_value;
}else{
  $integer_value =  $as_per_ui_value;
}

if($resp_dataType[$i] == 'Float' && $resp_valueDefinition[$i] == 'Range Value'){
  $integer_value = $as_per_ui_value;
}elseif($resp_dataType[$i] == 'UnsignedInt' && $resp_valueDefinition[$i] == 'Range Value') {
  $integer_value = hexToInt($as_per_ui_value);
}elseif($cmd_datatype[$i] == 'String' && $cmd_valuedefinition[$i] == 'Range Value') {
  $integer_value = $as_per_ui_value;
}else{
  $integer_value =  $as_per_ui_value;
}

   
if ($resp_dataType[$i] === 'Float') {
    //$asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
    $resp_integervalue = (float)$integer_value;
    $resp_value2 = floatToHex($resp_integervalue);
     $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
    $resp_sliderstep = (float)$resp_sliderstepvalue[$i];
} elseif ($resp_dataType[$i] === 'UnsignedInt') {
   // $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
    $resp_integervalue = (int)$integer_value;
     $resp_value2 = intToHex($resp_integervalue);
      $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
    $resp_sliderStep = (int)$resp_sliderStepValue[$i];
}elseif($resp_dataType[$i] === 'String'){
  $respintegervalue = (string)$integer_value;
$resp_value2 = '';
      $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
} else {
    $asperui_values_array_converted = $asperui_values_array_from_formatted;
    $resp_integervalue = $integer_value;
     $resp_value = '';
    $resp_sliderStep = '';
}
         $field_resp = [
                    "default_name" => $resp_defaultvalue[$i],
                    "asperui_value" => $resp_integervalue,
                    "value" => $resp_value,
                    "Field_description" => $resp_fieldDescription[$i],
                    "type" => $resp_valueDefinition[$i],
                    "data_type" => $resp_dataType[$i],
                    "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                    "byte_start_position" => (int)$resp_startBytePosition[$i],
                    "ui_type" => $resp_uiType[$i],
                    "ui_ids" => (int)$resp_uiId[$i],
                    "asperui" => $as_per_ui_array_from_formatted,
                    "asperui_values" => $asperui_values_array_from_formatted,
                    "ui_description" => $resp_uiDescriptionInOut[$i],
                    "slider_step_value" => $resp_sliderStep
                ];

             /*   $field_resp["value_in_hex"] = [
                    "min" => $resp_valueInHexMin[$i],
                    "max" => $resp_valueInHexMax[$i]
                ];*/

            }
            $fields_resp[] = $field_resp;
        } else {


if($resp_uiType[$i] == 'None')
{
            $field_resp = [
                "default_name" => $resp_defaultvalue[$i],
                "value" => $resp_valueInHexMin[$i],
                "Field_description" => $resp_fieldDescription[$i],
                "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                "byte_start_position" => (int)$resp_startBytePosition[$i],
                "ui_type" => $resp_uiType[$i],
               /* "ui_description" => $resp_uiDescriptionInOut[$i],
               "ui_ids" => (int)$resp_uiId[$i] */
            ];
            $fields_resp[] = $field_resp;

}
else{

  $field_resp = [
                "default_name" => $resp_defaultvalue[$i],
                "value" => $resp_valueInHexMin[$i],
                "Field_description" => $resp_fieldDescription[$i],
                "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                "byte_start_position" => (int)$resp_startBytePosition[$i],
                "ui_type" => $resp_uiType[$i],
                "ui_description" => $resp_uiDescriptionInOut[$i],
               "ui_ids" => (int)$resp_uiId[$i] 
            ];
            $fields_resp[] = $field_resp;




}
        }
    }
}



/**********************************************************************/

 if($error_framesize <= 1)
{

$fields_error=array();


}


else{


    for ($i = 0; $i < $error_framesize; $i++) {


$as_per_ui = $error_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

$asperui_values = $error_asperui_values[$i];
$asperui_values_array = explode('][', trim($asperui_values, '[]'));
$formatted_asperui_values = implode(',', $asperui_values_array);
$asperui_values_array_from_formatted = explode(',', $formatted_asperui_values);

$asperui_values_array = explode(',', $formatted_asperui_values);
$first_asperui_value = $asperui_values_array[0]; 

 if ($param1 =='Speed') {
    $as_per_ui_value =  $speed;
} elseif ($param1 =='power') {
   if ($cmd_datatype[$i] === 'Float') {
   $as_per_ui_value = (float)$power;
} elseif ($cmd_datatype[$i] === 'UnsignedInt') {
    $as_per_ui_value = (int)$power;
} else {
    $as_per_ui_value = ''; 
}
}  elseif ($param1 =='BAUDRATE') {
    $as_per_ui_value =  $baudrate;
}   elseif ($param1 =='Direction') {
    $as_per_ui_value =  $direction;
}   elseif ($param1 =='led') {
    $as_per_ui_value =  $led;
} elseif ($param1 =='name') {
    $as_per_ui_value =  $name;
} elseif ($param1 =='Current') {
    $as_per_ui_value =  $current;
} elseif ($param1 =='Voltage') {
    $as_per_ui_value =  $bus_voltage;
} elseif ($param1 =='rpm') {
    $as_per_ui_value =  $rpm;
} else {
   $as_per_ui_value =''; 
}
/*
if ($error_dataType[$i] === 'Float') {
  $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
    $error_value = floatToHex($as_per_ui_value);
     $asperuivalue = (float)$as_per_ui_value;
    $error_sliderStepValue= (float)$resp_sliderStepValue[$i];

} elseif ($error_dataType[$i] === 'UnsignedInt') {
  $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
    $error_value = intToHex($as_per_ui_value);
     $asperuivalue = (float)$as_per_ui_value;
    $error_sliderStepValue= (int)$resp_sliderStepValue[$i];
} else {
$asperui_values_array_converted = $asperui_values_array_from_formatted;
    $error_value = ''; 
    $error_sliderStepValue =''; 
}
 */
  if($error_datatype[$i] == 'Float' || $error_valuedefinition[$i] == 'Fixed value'){
  $integer_value = hexToInt($as_per_ui_value);
   $integervalue = (float)$integer_value;
}elseif($error_datatype[$i] == 'UnsignedInt' || $error_valuedefinition[$i] == 'Fixed value') {
  $integer_value = $as_per_ui_value;
    $integervalue = (int)$integer_value;
}else{
  $integervalue =  $as_per_ui_value;
}

if($error_datatype[$i] == 'Float' || $error_valuedefinition[$i] == 'Range Value'){
  $integervalue = $as_per_ui_value;
}elseif($error_datatype[$i] == 'UnsignedInt' || $error_valuedefinition[$i] == 'Range Value') {
  $integervalue = hexToInt($as_per_ui_value);
}else{
  $integervalue =  $as_per_ui_value;
}

   
if ($error_datatype[$i] === 'Float') {
    $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
    $error_value2 = floatToHex($integervalue);
     $error_value = padHexValue($error_value2, (int)$error_noofbytes[$i]);
    $error_sliderstep = (float)$error_sliderstepvalue[$i];
} elseif ($error_datatype[$i] === 'UnsignedInt') {
    $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
     $error_value2 = intToHex($integervalue);
      $error_value = padHexValue($error_value2, (int)$error_noofbytes[$i]);
    $error_sliderStep = (int)$error_sliderstepvalue[$i];
} else {
    $asperui_values_array_converted = $asperui_values_array_from_formatted;
     $error_value = '';
    $error_sliderStep = '';
}

        // Construct $field_error
        $field_error = [
              "default_name" =>$error_defaultvalue[$i],
               "asperui_value" => $integervalue,
            "value" => error_value,
            "Field_description" => $error_fieldDescription[$i],
            "type" => $error_valueDefinition[$i],
            "data_type" => $error_dataType[$i],
            "no_of_bytes" => (int) $error_numberOfBytes[$i],
            "byte_start_position" => (int) $error_startBytePosition[$i],
            "ui_type"  => $error_uiType[$i],
            "ui_ids"   => (int) $error_uiId[$i],
             "asperui" => $as_per_ui_array_from_formatted,
            "asperui_values" => $asperui_values_array_converted,
            "ui_description" => $error_uiDescriptionInOut[$i],
            "slider_step_value"=> $error_sliderStepValue[$i]
        ];

        // Add display_value and value_in_hex for Slider type
       /* if ($error_uiType[$i] == 'Slider') {
           /* $field_error["display_value"] = [
                "min" => $error_displayValueMin[$i],
                "max" => $error_displayValueMax[$i]
            ];*/
   /*         $field_error["value_in_hex"] = [
                "min" => $error_valueInHexMin[$i],
                "max" => $error_valueInHexMax[$i]
            ];

*/

  if ($error_dataType[$i] == 'UnsignedInt') {
    $field_error["display_value"] = [
        "min" => (int)$error_displayValueMin[$i],
        "max" => (int)$error_displayValueMax[$i]
    ];
} elseif ($error_dataType[$i] == 'Float') {
    $field_error["display_value"] = [
        "min" => (float)$error_displayValueMin[$i],
        "max" => (float)$error_displayValueMax[$i]
    ];
} else {
    $field_error["display_value"] = [
        "min" => $error_displayValueMin[$i],
        "max" => $error_displayValueMax[$i]
    ];
}











      /*  } else {
            $field_error["value_in_hex"] = [
                "value" => $error_valueInHexMin[$i]
            ];
        }*/

        $fields_error[] = $field_error;
    }


}


 
    if ($number_of_pages == 0){
     // if ($param['parameter_name'] == 0) {
        $result_single0 = [
             "seq" => (int)$sequence,
            "frame" => [
                 "name" => $param['parameter_name'],
            "multiple_ui" => [$cmd_multipleui, $resp_multipleui, $error_multipleui],
            "trigger_ui" => [$cmd_triggerui, $resp_triggerui, $error_triggerui],
                "commands" => $fields_cmd,
                "response" => $fields_resp,
                "error" => $fields_error
            ]
        ];

        $page0[] = $result_single0;
         usort($page0, function($a, $b) {
    return $a['seq'] <=> $b['seq'];
});
    } 
     elseif ($number_of_pages == 2) {
        $result_single2 = [
               "seq" => (int)$sequence,
            "frame" => [
                  "name" => $param['parameter_name'],
            "multiple_ui" => [$cmd_multipleui, $resp_multipleui, $error_multipleui],
            "trigger_ui" => [$cmd_triggerui, $resp_triggerui, $error_triggerui],
                "commands" => $fields_cmd,
                "response" => $fields_resp,
                "error" => $fields_error
            ]
        ];

      $page2[] = $result_single2;
       usort($page2, function($a, $b) {
    return $a['seq'] <=> $b['seq'];
});
    }

   elseif ($number_of_pages == 3) {
        $result_single3 = [
               "seq" => (int)$sequence,
            "frame" => [

                 "name" => $param['parameter_name'],
            "multiple_ui" => [$cmd_multipleui, $resp_multipleui, $error_multipleui],
            "trigger_ui" => [$cmd_triggerui, $resp_triggerui, $error_triggerui],
                "commands" => $fields_cmd,
                "response" => $fields_resp,
                "error" => $fields_error
            ]
        ];

      $page3[] = $result_single3;
       usort($page3, function($a, $b) {
    return $a['seq'] <=> $b['seq'];
});
    }

   elseif ($number_of_pages == 1) {
    $name = $param['parameter_name'];
    
        $para = [
              "seq" => (int)$sequence,
            "frame" => [
                 "name" => $param['parameter_name'],
            "multiple_ui" =>[$cmd_multipleui, $resp_multipleui, $error_multipleui],
            "trigger_ui" => [$cmd_triggerui, $resp_triggerui, $error_triggerui], 
                "commands" => $fields_cmd,
                "response" => $fields_resp,
                "error" => $fields_error
            ]
        ];  
// $result[] = $field_name;
        $result[] = $para;
      usort($result, function($a, $b) {
    return $a['seq'] <=> $b['seq'];
});
}

   
     
} else {
echo "no param";
}




}

 
 











$field_name[] = array(
    "value" => $curr_devicetype,
                "name" => "name",
    "Field_description" => "Data",
    "type" => "Fixed Value",
    "data_type" => "String",
    "no_of_bytes" => 0,
    "byte_position" => array(
        "start" => 1,
        "end" => 2
    ),
    "bit_size" => 0,
    "ui_type" => "Text(Default)",
    "ui_ids" => 1,
    "as per ui" => array(),
    "asperui values" => array(),
    "ui_description" => "Read",
    "value_in_hex" => array(
        "value" => "00"
    )
);




$page_name= array(
"page_no"=> (int)$number_of_pages,
"multiple_ui"=> ["false", "false", "false"],
 "trigger_ui"=>["false","false","false"],
"frame" => array(
"commands"=> $field_name,      
"response"=> array(),
"error"=> array()
)
);



$para_result = [];

$para_result = $result;

/*$para_result = array_merge(array($page_name), $result);

$page0 = array_merge(array($page_name), $page0);

$page2 = array_merge(array($page_name), $page2);

$page3 = array_merge(array($page_name), $page3);*/

$para_default_result = $result_default;




/*-------------------------------------------------------------*/
/*************************************************************/

//$node_id = 'FFFFFFFFFFFFFFFF7EC2000000004200';




/*
$localcontrol =array("POP"=> "a8d8e5ce",
                    "Type"=> 0);

$timezone = array("TZ"=>$time_gatelink,
        "TZ-POSIX"=>"PST8PDT,M3.2.0,M11.1.0");


$Baud_rate = array("BR"=>$baudrate_gatelink,
             "BR-POSIX"=>"0");

$uart_msg_id = array("UART_MSG_ID"=>$uartmsgid_gatelink,
               "UART_MSG_ID POSIX"=>"0");

$error_code = array("ERROR CODE"=>$errorcode_gatelink,
              "ERROR CODE POSIX"=>"0");


$comm_mode = array("Efactor Protocol"=>$comm_mode_gatelink,
             "Efactor Protocol Posix"=>"0");*/


$Baud_rate = 9600;

$uart_msg_id = 'null';

$error_code = 'null';


$comm_mode = 'null';



$query_inter = "SELECT * FROM efactorproduct where node_id='$node_id'";
$doctors_inter = $db->get_results($query_inter); 
$row_inter = $db->get_row($query_inter);
$model_number=$row_inter['model_number'];
$devicename=$row_inter['device_name'];
$vendor_name=$row_inter['vendor_name'];
$moduletype=$row_inter['module_type'];
$net_conn_1=$row_inter['internet'];



$node_details = array(
    "id" => $node_id,
    
    "config" => array(
        "attributes" => array(
            array(
                "name" => "serial_num",
                "value" => "1345"
            ),
            array(
                "name" => "model",
                "value" => "wifi"
            )
        ),
        "config_version" => "2020-03-20",
        "devices" => array(
            array(
                "name" => $curr_devicetype,
                "total_pages" => $number_of_pages,
                "attributes" => array(
                    array(
                        "name" => "Device Id",
                        "value" => $node_id
                    ),
                    array(
                        "name" => "mac",
                        "value" => "CC:50:E3:16:1D:6F"
                    )
                ),
                "pages" => array(
                     "page1" => $para_result,
                                        
                                    ),
                "primary" => "power",
                "type" => "efactor.device.fan",
                "brand_logo" => "https://efactor.tech/files/brand_logo/efactor.jpg"
            )
        ),
        "info" => array(
            "fw_version" => "1.5.1",
            "model" => $model_number,
            "name" => "NULL",
            "type" => "WIFI"
        ),
        "node_id" => $node_id
    ),
    "role" => "primary",
    "status" => array(
        "connectivity" => array(
            "connected" => "true",
            "timestamp" => 1717898131809
        )
    )
);




$access_token='eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJwaG9uZV92ZXJpZmllZCI6dHJ1ZSwiY3VzdG9tOnVzZXJfaWQiOiIxNzciLCJ0b2tlbl91c2UiOiJpZCIsImF1dGhfdGltZSI6MTY2NzAyMjI0MiwiZXhwIjoxNjY3MDI1ODQyLCJpYXQiOjE2NjcwMjIyNDIsInBob25lIjoiOTM5MTA0Mzc4MSJ9.q9CrbVNL-PRfAjnvvLGMmazodCk2Y1Lid9gch5ey0R4';




$nextid='null';


$node = json_encode(array("url"=>"action=test_details",
                      "token"=>$access_token,
                      "next_id"=>$nextid,
                      "total"=>1,
                      "node_details"=>array($node_details),
                       "nodes"=>$node_id
                        ));


echo json_encode(array("node_details" => array($node_details), "nodes" => $node_id));

//echo $node;

exit;



/************************************************************/

    }


}


/************************************************************/

//Output
echo json_encode($response);
//echo json_data;
exit;



?>
