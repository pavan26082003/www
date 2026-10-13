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


$db->query("update efactorproduct set app_version='v8' where access_token='$val'");


if(!empty($_GET["start_id"]))
 {



$node_id=$startid;




$query_next_id = "
    SELECT node_id 
    FROM efactorproduct 
    WHERE access_token = '$val' 
      AND is_added = '1' 
      AND node_id > '$node_id' 
    ORDER BY node_id ASC 
    LIMIT 1
";

$row = $db->get_row($query_next_id);

if ($row) {


    $device_id_next = $row['node_id'];
} else {


        $query_next_id = "
        SELECT node_id 
        FROM efactorproduct 
        WHERE access_token = '$val' 
          AND id > (
            SELECT id 
            FROM efactorproduct 
            WHERE access_token = '$val' 
              AND node_id = '$node_id'
            LIMIT 1
          )
        ORDER BY id ASC 
        LIMIT 1";

    $row = $db->get_row($query_next_id);

    if ($row) {
        $device_id_next = $row['node_id'];
    } else {
        $device_id_next = null; // Handle no result found
    }
}

$nextid = $device_id_next;







}






else
{


    $query_3 = "SELECT * FROM efactorproduct where access_token='$val'";
							   $doctors = $db->get_results($query_3); 
							   $row = $db->get_row($query_3);
						           $node_id=$row['node_id'];





$arr=array();
								foreach ($doctors as $key => $doctor) 
								 {
								  array_push($arr,array("id"=>$doctor['id'],"node_id"=>$doctor['node_id'],"device_type"=>$doctor['device_type']));
								 }
  

								  //echo $arr[0]['node_id'];
							       //echo devicetype_1;
								  $device_id=$arr[0]['node_id'];
								  
								  $next_id_2=$arr[1]['node_id'];
								  //echo $next_id_2;
								  $nextid = $next_id_2;








}






//$node_id = 'FFFFFFFFFFFFFFFF7EC2000000004200';


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

                        $get_status_button=$row_node['get_status_button']; 

                 
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




/*************************************************************************************/





                      if($device_type == 'exhaust_hood')
                                                              {
                                                               $url="https://efactor.tech/files/brand_logo/dimplelogom.png";

                                                              }

                                                             else if($vendor_name == 'HIWIN')
                                                             {
                                                               $url="https://efactor.tech/files/brand_logo/hiwin.jpg";

                                                             }

                                                             else
                                                             {
                                                             $url="https://efactor.tech/files/brand_logo/efactor.jpg";

                                                               }



                                           
                                           
                                           /*    $devicename=$row_node['device_name']; 
                                               $curr_devicetype=$row_node['current_devicetype'];
                                             $recipe=$row_node['recipe'];
                                              $user_priority=$row_node['user_priority'];
                                             $cookermode=$row_node['cookermode'];
                                             $cooker_led=$row_node['cooker_led'];
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
                                              $hood_lamp=$row_node['hood_lamp'];*/

                                           
                                           $nurse=filter_var($row_node['nurse'], FILTER_VALIDATE_BOOLEAN);
                                           $personal_assistant=filter_var($row_node['personal_assistant'], FILTER_VALIDATE_BOOLEAN);
                                           $doctormanage=filter_var($row_node['doctor'], FILTER_VALIDATE_BOOLEAN);
                                           $nurse_resp=$row_node['nurse_resp'];      
                                           $personal_assistant_resp=$row_node['personal_assistant_resp'];                    
                                           $doctormanage_resp=$row_node['doctor_resp'];                      




                                      if($device_type == 'swpcp')
                                              {
                                           //swpcp
                                            $starter_relay_off_timer = $row_node['starter_relay_off_timer'];  
                                            $start_high_current_cutoff = $row_node['start_high_current_cutoff'];  
                                            $start_low_current_cutoff= $row_node['start_low_current_cutoff'];  
                                            $Run_high_current_cutoff = $row_node['Run_high_current_cutoff'];  
                                            $Run_low_current_cutoff = $row_node['Run_low_current_cutoff'];  
                                            $high_voltage_cutoff = $row_node['high_voltage_cutoff'];  
                                            $low_voltage_cutoff = $row_node['low_voltage_cutoff'];  
                                            $ultra_high_voltage_cutoff = $row_node['ultra_high_voltage_cutoff'];  
                                            $ultra_high_current_cutoff = $row_node['ultra_high_current_cutoff'];  
                                            $USER_SET_MAX_CURRENT_CUTOFF_LIMIT = $row_node['USER_SET_MAX_CURRENT_CUTOFF_LIMIT'];  
                                            $USER_AND_DEVICE_MAX_CURRENT_LIMIT = $row_node['USER_AND_DEVICE_MAX_CURRENT_LIMIT'];  
                                            $LOCK_ENABLE_DISABLE = (bool)$row_node['LOCK_ENABLE_DISABLE'];  
                                         }



                                         if($device_type == 'cooker')
                                              {
                                           //cooker

                                            $cooker_timer = $row_node['cooker_timer_1'];  
                                            $cooking_temperature = $row_node['cooker_max_temperature_1'];  
                                            $Keep_warm= $row_node['Keep_warm_1'];  
                                            $Pre_timer_constant_mode = $row_node['Pre_timer_constant_mode'];  
                                            $Pre_timer = $row_node['Pre_timer'];  
                                            $Pre_temperature = $row_node['Pre_temperature'];  
                                            $Coooker_max_temperature = $row_node['Coooker_max_temperature'];  
                                            $Cooker_constant_mode = $row_node['Cooker_constant_mode'];  
                                            $Keep_Warm_timer = $row_node['Keep_Warm_timer_1'];  
                                            $Dishes = $row_node['dish'];
                                            $pre_timer_wait_temperature_constant_mode = $row_node['pre_timer_wait_temperature_constant_mode'];  
                                            $pre_timer_waiting_time = $row_node['pre_timer_waiting_time'];
                                            $pre_timer_Waiting_temperature = $row_node['pre_timer_Waiting_temperature'];  
                                            $warm_constant_cut_off = $row_node['warm_constant_cut_off'];  
                                            $cook_wait_temp_constant_mode = $row_node['cook_wait_temp_constant_mode'];  
                                            $cook_waiting_time = $row_node['cook_waiting_time'];  
                                            $cook_Waiting_temperature = $row_node['cook_Waiting_temperature'];  
                                            $warm_wait_temp_constant_mode = $row_node['warm_wait_temp_constant_mode'];  
                                            $warm_waiting_time = $row_node['warm_waiting_time'];  
                                            $warm_Waiting_temperature = $row_node['warm_Waiting_temperature']; 
                                            $cooker_ok_button = $row_node['cooker_ok_button']; 
                                            $reload_default = $row_node['reload_default']; 

	

                                             


                                         }


/*****************************************************/

if ($recipe == "RICE") {
    $recipe = 1;
    $asper_value_hex = '000000003C6E00007D013C2800B400000001036E000000';

} elseif ($recipe == "BROWNRICE") {
    $recipe = 2;
    $asper_value_hex = '000001003C6E010F00013C2800B400000001036E000000';
} elseif ($recipe == "FRY") {
    $recipe = 3;
    $asper_value_hex = '0000020150B4000FB4013C280118000000000000000000';
} elseif ($recipe == "MILK") {
    $recipe = 4;
    $asper_value_hex = '000003011964000F6E013C280096000000000000000000';
} elseif ($recipe == "SOUP") {
    $recipe = 5;
    $asper_value_hex = '000004001E960000B4013C2800B4000000000000000000';
} elseif ($recipe == "BIRIYANI") {
    $recipe = 6;
    $asper_value_hex = '000005002DF00100B4013C2800B4000000000000000000';
} elseif ($recipe == "IDLY") {
    $recipe = 7;
    $asper_value_hex = '000006000F7D00055A013C2800A0000000000000000000';
} elseif ($recipe == "DHAL") {
    $recipe = 8;
   $asper_value_hex = '000007002D960000A0013C280096000000000000000000';
} elseif ($recipe == "HARDVEG") {
    $recipe = 9;

    $asper_value_hex = '0000080128C80000F0013C2800F0000000000000000000';
} elseif ($recipe == "WATER") {
    $recipe = 10;
    $asper_value_hex = '000009001E960000A0013C2800A0000000000000000000';
} elseif ($recipe == "DEEPFRY") {
    $recipe = 11;
    $asper_value_hex = '00000A011EBE000CE6013C2801180104BE000000000000';
} elseif ($recipe == "POPCORN") {
    $recipe = 12;
   $asper_value_hex = '00000B0003DC0006A5013C2801220000000101DC000000';
} elseif ($recipe == "CAKE") {
    $recipe = 13;
    $asper_value_hex = '00000C012DDC0005B4013C280122000000000000000000';
} elseif ($recipe == "MAGGI") {
    $recipe = 14;

    $asper_value_hex = '00000D00056A000A5A013C2800B4000560000000000000';
} elseif ($recipe == "NOODLES") {
    $recipe = 15;
    $asper_value_hex = '00000E000ADC0008B4013C2800B4000000000000000000';
} elseif ($recipe == "DIY") {
    $recipe = 16;
    $asper_value_hex = '00000F002DDC0005B4013C2800B4000000000000000000';
} 
else  {

}









                                 
                                         
  /***************************************************/                                        

                                             if($device_type == 'fan')
                                              {
                                             //fan
                                            $fan_resistance = $row_node['fan_resistance'];  
                                            $fan_lmin = $row_node['fan_lmin'];  
                                            $fan_lmax= $row_node['fan_lmax'];  
                                            $fan_frequency = $row_node['fan_frequency'];  
                                            $fan_pairs = $row_node['fan_pairs'];  
                                            $fan_open_loop_current = $row_node['fan_open_loop_current'];  
                                            $fan_power_limit = $row_node['fan_power_limit'];  
                                            $fan_torque_limit = $row_node['fan_torque_limit'];  
                                            $fan_speed1 = $row_node['fan_speed1'];  
                                            $fan_speed2= $row_node['fan_speed2'];
                                            $fan_speed3 = $row_node['fan_speed3'];  
                                            $fan_speed4= $row_node['fan_speed4'];
                                            $fan_speed5 = $row_node['fan_speed5'];  
                                            $fan_speed6= $row_node['fan_speed6'];
                                            $fan_speed7 = $row_node['fan_speed7'];  
                                            $fan_button= $row_node['fan_button'];
                                             } 
                                       
                                             
                                            
                                            
                                            
                                      
                                       $tz_properties=$row_node['TZ_properties'];
                                       $br_properties=$row_node['BR_properties'];
                                       $umi_properties=$row_node['UMI_properties'];
                                       $ec_properties=$row_node['EC_properties'];
                                       $cm_properties=$row_node['CM_properties'];
                                       $tu_properties=$row_node['TU_properties'];
                                       $remote_pair_properties=$row_node['remote_pair_properties'];
                                       $display_properties=$row_node['display_properties'];
                                       $swpcp_1ph_properties=$row_node['swpcp_1ph_properties'];
                                       $fan_properties=$row_node['fan_properties'];
                                       $lc_properties=$row_node['LC_properties'];
                                       $cooker_properties=$row_node['cooker_properties'];
                                       



                                     $baudrate=$row_node['baudrate'];
                                     $uartmsgid=$row_node['uartmsgid'];
                                     $time=$row_node['time'];
                                     $errorcode=$row_node['errorcode'];
                                     $comm_mode=$row_node['comm_mode'];













/**********************************************************************************************/



//$query = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.1' AND user_id = '177'";
$query = "SELECT * FROM product_api WHERE model_name = '$model_name'";

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

        



 
//$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFACT V1.1' AND user_id = '177'";
$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";

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
  $query_param = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.1' AND user_id = '177' AND parameter_name='$param1'";

 $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND parameter_name='$param1'";



        $params = $db->get_results($query_param); 
        $num_param = $db->num_rows($query_param);
        $row_param = $db->get_row($query_param); 
        $parameter_name = $row_param['parameter_name'];

        $brand_name = $row_param['brand_name'];
        $brand_logo = $row_param['brand_logo'];
        $product_name = $row_param['product_name'];
       // $model_name = $row_param['model_name'];
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



$baudrate_1 = "34";
$direction = "8";


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
}


elseif ($param1 =='Led Color') {
    $as_per_ui_value =  $hue;
}




 elseif ($param1 =='name') {

   if($name_dev == 'NULL')
{

$curr_name=$curr_devicetype;

}

else{
$curr_name=$name_dev;
}







    $as_per_ui_value =  $curr_name;
} elseif ($param1 =='Current') {
    $as_per_ui_value =  $current;
} elseif ($param1 =='Voltage') {
    $as_per_ui_value =  $bus_voltage;
  //  $as_per_ui_value =  $voltage;
} elseif ($param1 =='rpm') {
    $as_per_ui_value =  $rpm;
} elseif ($param1 =='Watt') {
    $as_per_ui_value =  $Watt;
} 

elseif ($param1 =='Skipmode') {
    $as_per_ui_value =  true;
}

elseif ($param1 =='Cooker mode') {
    $as_per_ui_value =  $cookermode;
}


elseif ($param1 =='Recipee' || $cmd_defaultvalue[$i] =='Recipee') {
    $as_per_ui_value =  $recipe;
}






elseif ($param1 =='remaining time') {
    $as_per_ui_value =  $cooktime;
}


elseif ($param1 =='running temperature') {
    $as_per_ui_value =  $cook_run_temp;
}



elseif ($param1 =='Nurse') {
    $as_per_ui_value = $nurse;
}


elseif ($param1 =='Nurse_2') {
    $as_per_ui_value = $nurse_resp;
}


elseif ($param1 =='Doctor') {
    $as_per_ui_value = $doctormanage;
}


elseif ($param1 =='Doctor_2') {
    $as_per_ui_value = $doctormanage_resp;
}


elseif ($param1 =='PA_2') {
    $as_per_ui_value = $personal_assistant_resp;
}

elseif ($param1 =='PA') {
    $as_per_ui_value = $personal_assistant;
}



elseif ($param1 =='Auto Clean') {
    $as_per_ui_value = $hood_autoclean;
}


elseif ($param1 =='Air Flow') {
    $as_per_ui_value = $hood_speed;
}

elseif ($param1 =='Lamp') {
    $as_per_ui_value = $hood_lamp;
}




else {
   $as_per_ui_value =''; 
}













/*


if($model_name = 'EFACT V1.1')
{


if ($cmd_defaultvalue[$i] =='resistance') {
    $as_per_ui_value =  $fan_resistance;
} elseif ($cmd_defaultvalue[$i] =='lmin') {
      $as_per_ui_value = $fan_lmin;
} elseif ($cmd_defaultvalue[$i] =='lmax') {
      $as_per_ui_value = $fan_lmax;
} elseif ($cmd_defaultvalue[$i] =='frequency') {
      $as_per_ui_value = $fan_frequency;
} elseif ($cmd_defaultvalue[$i] =='pole pairs') {
      $as_per_ui_value = $fan_pairs;
} elseif ($cmd_defaultvalue[$i] =='Startup Current') {
      $as_per_ui_value = $fan_startup_current;
} elseif ($cmd_defaultvalue[$i] =='max current') {
      $as_per_ui_value = $fan_max_current;
} elseif ($cmd_defaultvalue[$i] =='min rpm') {
      $as_per_ui_value = $fan_min_rpm;
} elseif ($cmd_defaultvalue[$i] =='max rpm') {
      $as_per_ui_value = $fan_max_rpm;
} elseif ($cmd_defaultvalue[$i] =='current bw') {
      $as_per_ui_value = $fan_current_bw;
} elseif ($cmd_defaultvalue[$i] =='Bemf freq') {
      $as_per_ui_value = $fan_bemf_freq;
} elseif ($cmd_defaultvalue[$i] =='Bemf Volt') {
      $as_per_ui_value = $fan_bemf_volt;
} elseif ($cmd_defaultvalue[$i] =='Speed L1') {
      $as_per_ui_value = $fan_spped_l1;
} elseif ($cmd_defaultvalue[$i] =='Speed L2') {
      $as_per_ui_value = $fan_spped_l2;
} elseif ($cmd_defaultvalue[$i] =='Speed L3') {
      $as_per_ui_value = $fan_spped_l3;
} elseif ($cmd_defaultvalue[$i] =='Speed L4') {
      $as_per_ui_value = $fan_spped_l4;
}




}

*/


if ($cmd_defaultvalue[$i] =='resistance') {
    $as_per_ui_value =  (string)$fan_resistance;
} elseif ($cmd_defaultvalue[$i] =='lmin') {
      $as_per_ui_value = (string)$fan_lmin;
} elseif ($cmd_defaultvalue[$i] =='lmax') {
      $as_per_ui_value = (string)$fan_lmax;
} elseif ($cmd_defaultvalue[$i] =='frequency') {
      $as_per_ui_value = (string)$fan_frequency;
} elseif ($cmd_defaultvalue[$i] =='pole pairs') {
      $as_per_ui_value = (string)$fan_pairs;
} elseif ($cmd_defaultvalue[$i] =='Startup Current') {
      $as_per_ui_value = (string)$fan_startup_current;
} elseif ($cmd_defaultvalue[$i] =='max current') {
      $as_per_ui_value = (string)$fan_max_current;
} elseif ($cmd_defaultvalue[$i] =='min rpm') {
      $as_per_ui_value = (string)$fan_min_rpm;
} elseif ($cmd_defaultvalue[$i] =='max rpm') {
      $as_per_ui_value = (string)$fan_max_rpm;
} elseif ($cmd_defaultvalue[$i] =='current bw') {
      $as_per_ui_value = (string)$fan_current_bw;
} elseif ($cmd_defaultvalue[$i] =='Bemf freq') {
      $as_per_ui_value = (string)$fan_bemf_freq;
} elseif ($cmd_defaultvalue[$i] =='Bemf Volt') {
      $as_per_ui_value = (string)$fan_bemf_volt;
} elseif ($cmd_defaultvalue[$i] =='Speed L1') {
      $as_per_ui_value = (string)$fan_spped_l1;
} elseif ($cmd_defaultvalue[$i] =='Speed L2') {
      $as_per_ui_value = (string)$fan_spped_l2;
} elseif ($cmd_defaultvalue[$i] =='Speed L3') {
      $as_per_ui_value = (string)$fan_spped_l3;
} elseif ($cmd_defaultvalue[$i] =='Speed L4') {
      $as_per_ui_value = (string)$fan_spped_l4;
}

else if ($cmd_defaultvalue[$i] =='Recipee') {
    $as_per_ui_value =  (string)$recipe;
}elseif ($cmd_defaultvalue[$i] =='Cooker Timer') {
      $as_per_ui_value = (int)$cooker_timer;
}elseif ($cmd_defaultvalue[$i] =='Cooking temp constant mode') {
      $as_per_ui_value = (bool)$Cooker_constant_mode;
}elseif ($cmd_defaultvalue[$i] =='Cooking Temperature') {
      $as_per_ui_value = (int)$cooking_temperature;
}elseif ($cmd_defaultvalue[$i] =='Pre-timer temp constant mode') {
      $as_per_ui_value = (bool)$pre_timer_wait_temperature_constant_mode;
}elseif ($cmd_defaultvalue[$i] =='Pre-timer') {
      $as_per_ui_value = (int)$Pre_timer;
}elseif ($cmd_defaultvalue[$i] =='Pre – temperature') {
      $as_per_ui_value = (int)$Pre_temperature;
}elseif ($cmd_defaultvalue[$i] =='keep warm temp constant mode') {
      $as_per_ui_value = (bool)$warm_constant_cut_off;
}elseif ($cmd_defaultvalue[$i] =='Keep Warm timer') {
      $as_per_ui_value = (int)$Keep_Warm_timer;
}elseif ($cmd_defaultvalue[$i] =='Keep Warm temperature') {
      $as_per_ui_value = (int)$Keep_warm;
}elseif ($cmd_defaultvalue[$i] =='Coooker max-temperature') {
      $as_per_ui_value = (int)$Coooker_max_temperature;
}elseif ($cmd_defaultvalue[$i] =='pre-timer wait temperature constant mode') {
      $as_per_ui_value = (bool)$pre_timer_wait_temperature_constant_mode;
}elseif ($cmd_defaultvalue[$i] =='pre-timer waiting time') {
      $as_per_ui_value = (int)$pre_timer_waiting_time;
}elseif ($cmd_defaultvalue[$i] =='pre-timer Waiting temperature') {
      $as_per_ui_value = (int)$pre_timer_Waiting_temperature;
}elseif ($cmd_defaultvalue[$i] =='cook wait temp constant mode') {
      $as_per_ui_value = (int)$cook_wait_temp_constant_mode;
}elseif ($cmd_defaultvalue[$i] =='cook waiting time') {
      $as_per_ui_value = (int)$cook_waiting_time;
}elseif ($cmd_defaultvalue[$i] =='cook Waiting- temperature') {
      $as_per_ui_value = (int)$cook_Waiting_temperature;
}else if ($cmd_defaultvalue[$i] =='warm-wait temp constant mode') {
    $as_per_ui_value =  (bool)$warm_wait_temp_constant_mode;
}else if ($cmd_defaultvalue[$i] =='warm waiting time') {
    $as_per_ui_value =  (int)$warm_waiting_time;
}else if ($cmd_defaultvalue[$i] =='warm Waiting temperature') {
    $as_per_ui_value =  (int)$warm_Waiting_temperature;
}


else if ($cmd_defaultvalue[$i] =='starter relay off timer') {
    $as_per_ui_value =  (int)$starter_relay_off_timer;
}else if ($cmd_defaultvalue[$i] =='start high current cutoff') {
    $as_per_ui_value =  (int)$start_high_current_cutoff;
}else if ($cmd_defaultvalue[$i] =='start low current cutoff') {
    $as_per_ui_value =  (int)$start_low_current_cutoff;
}else if ($cmd_defaultvalue[$i] =='LOCK ENABLE/DISABLE') {
    $as_per_ui_value =  (bool)$LOCK_ENABLE_DISABLE;
}else if ($cmd_defaultvalue[$i] =='Run high current cutoff') {
    $as_per_ui_value =  (int)$Run_high_current_cutoff;
}else if ($cmd_defaultvalue[$i] =='high voltage cutoff') {
    $as_per_ui_value =  (int)$high_voltage_cutoff;
}else if ($cmd_defaultvalue[$i] =='USER SET MAX CURRENT CUTOFF LIMIT') {
    $as_per_ui_value =  (int)$USER_SET_MAX_CURRENT_CUTOFF_LIMIT;
}else if ($cmd_defaultvalue[$i] =='low voltage cutoff') {
    $as_per_ui_value =  (int)$low_voltage_cutoff;
}else if ($cmd_defaultvalue[$i] =='ultra high voltage cutoff') {
    $as_per_ui_value =  (int)$ultra_high_voltage_cutoff;
}else if ($cmd_defaultvalue[$i] =='ultra high current cutoff') {
    $as_per_ui_value =  (int)$ultra_high_current_cutoff;
}else if ($cmd_defaultvalue[$i] =='USER & DEVICE MAX CURRENT LIMIT') {
    $as_per_ui_value =  (int)$USER_AND_DEVICE_MAX_CURRENT_LIMIT;
}else if ($cmd_defaultvalue[$i] =='Run low current cutoff') {
    $as_per_ui_value =  (int)$Run_low_current_cutoff;
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
  $integer_value = (int)$as_per_ui_value;
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

//$devv_type = 'Cooker';
//$hex_ckvalue = 'A301';
if($param1 == 'Recipee' || $cmd_defaultvalue[$i] == 'Recipee'){


$field_cmd = [
    "default_name" => $cmd_defaultvalue[$i],
    "asperui_value" => $integervalue,  
    "value" => $asper_value_hex,  
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
 }else{



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


if($cmd_uitype[$i] == 'Auto Trigger with Timer')
{
$cmd_valueinhexmin[$i]= (int)$get_status_button;

}




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

if($name_dev == 'NULL')
{

$curr_name=$curr_devicetype;

}

else{
$curr_name=$name_dev;
}





    $as_per_ui_value =  $curr_name;
} elseif ($resp_defaultvalue[$i] =='current') {
    $as_per_ui_value =  $current;
} elseif ($resp_defaultvalue[$i] =='voltage') {
    $as_per_ui_value =  $bus_voltage;
} elseif ($resp_defaultvalue[$i] =='rpm') {
    $as_per_ui_value =  $rpm;
} else {
   $as_per_ui_value =''; 
}



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


 /*
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
*/

$unique_params = [];

for ($pages = 0; $pages <= $number_of_pages; $pages++) {
  
    $parameter_name = $param['parameter_name'];

    if (!in_array($parameter_name, $unique_params)) {
        $unique_params[] = $parameter_name;

        $para = [
             
            "frame" => [
            "page_name" => $page_names,
                "page" => (int)$number_of_pages,
                "seq" => (int)$sequence,
                "name" => $parameter_name,
                "multiple_ui" => [$cmd_multipleui, $resp_multipleui, $error_multipleui],
                "trigger_ui" => [$cmd_triggerui, $resp_triggerui, $error_triggerui],
                "commands" => $fields_cmd,
                "response" => $fields_resp,
                "error" => $fields_error
            ]
        ];

        $result[] = $para;
        usort($result, function($a, $b) {
    return $a['frame']['seq'] <=> $b['frame']['seq'];
});
    }
}



usort($result, function($a, $b) {
    return $a['frame']['page'] <=> $b['frame']['page'];
});
 
 $final_result = array_map(function($item) {
    $frame = $item['frame'];
    $item['frame'] = $frame;
    return $item;
}, $result);  
  
     
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



//$para_result = [];

//$para_result = $result;


$para_result = [];

foreach ($final_result as $item) {
    $page = $item['frame']['page_name'];

    if (!isset($para_result[$page])) {
        $para_result[$page] = [];
    }

    $para_result[$page][] = $item;
}

foreach ($para_result as $page => &$items) {
    usort($items, function($a, $b) {
        return $a['frame']['seq'] <=> $b['frame']['seq'];
    });
}

foreach ($para_result as $page => &$items) {
    foreach ($items as &$item) {
        $frame = $item['frame'];
        unset($frame['page']);
         unset($frame['page_name']);
        unset($frame['seq']);
        $item['frame'] = $frame;
    }
}

//unset($item);
//unset($items);


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
$devicetype=$row_inter['device_type'];





$type="efactor.device.$devicetype";



if($name_dev == 'NULL')
{

$curr_name=$curr_devicetype;

}

else{


$curr_name=$name_dev;
}




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
                "pages" =>  $para_result,
                   
               
                "primary" => "power",
                "type" => $type,
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
            "connected" => $net_conn_1,
            "timestamp" => 1717898131809
        )
    )
);


/*

$access_token='eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJwaG9uZV92ZXJpZmllZCI6dHJ1ZSwiY3VzdG9tOnVzZXJfaWQiOiIxNzciLCJ0b2tlbl91c2UiOiJpZCIsImF1dGhfdGltZSI6MTY2NzAyMjI0MiwiZXhwIjoxNjY3MDI1ODQyLCJpYXQiOjE2NjcwMjIyNDIsInBob25lIjoiOTM5MTA0Mzc4MSJ9.q9CrbVNL-PRfAjnvvLGMmazodCk2Y1Lid9gch5ey0R4';






$node = json_encode(array("url"=>"action=test_details",
                      "token"=>$access_token,
                      "next_id"=>$nextid,
                      "total"=>$num_dev,
                      "node_details"=>array($node_details),
                       "nodes"=>$node_id
                        ));

*/




if($node_id)
{


$node = json_encode(array("url"=>"action=test_details",
                      "token"=>$val,
                      "next_id"=>$nextid,
                      "total"=>$num_dev,
                      "node_details"=>array($node_details),
                       "nodes"=>$node_id
                        ));

}

else{


$node = json_encode(array("url"=>"action=test_details",
                      "token"=>$val,
                      "next_id"=>$nextid,
                      "total"=>$num_dev,
                      "node_details"=>"",
                       "nodes"=>$node_id
                        ));



}








//echo json_encode(array("node_details" => array($node_details), "nodes" => $node_id));

echo $node;

exit;



/************************************************************/

    }

}








/**************************test_nodes**********************************/
if (isset($_GET['action']) && $_GET['action']=='test_nodes')
{

$node_id =$_GET['node_id'];
                        
			$header = apache_request_headers();  
			foreach ($header as $key => $val) {
			
			$query_dev="SELECT node_id FROM efactorproduct where access_token='$val'";
			$doctors = $db->get_results($query_dev); 
			$num_dev = $db->num_rows($query_dev);
                        $row_dev = $db->get_row($query_dev);    
		       // $node_id=$row_dev['node_id'];


//$node_id=$startid;


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

                        $get_status_button=$row_node['get_status_button']; 

                 
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




/*************************************************************************************/





                      if($device_type == 'exhaust_hood')
                                                              {
                                                               $url="https://efactor.tech/files/brand_logo/dimplelogom.png";

                                                              }

                                                             else if($vendor_name == 'HIWIN')
                                                             {
                                                               $url="https://efactor.tech/files/brand_logo/hiwin.jpg";

                                                             }

                                                             else
                                                             {
                                                             $url="https://efactor.tech/files/brand_logo/efactor.jpg";

                                                               }



                                           
                                           
                                           /*    $devicename=$row_node['device_name']; 
                                               $curr_devicetype=$row_node['current_devicetype'];
                                             $recipe=$row_node['recipe'];
                                              $user_priority=$row_node['user_priority'];
                                             $cookermode=$row_node['cookermode'];
                                             $cooker_led=$row_node['cooker_led'];
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
                                              $hood_lamp=$row_node['hood_lamp'];*/

                                           
                                           $nurse=filter_var($row_node['nurse'], FILTER_VALIDATE_BOOLEAN);
                                           $personal_assistant=filter_var($row_node['personal_assistant'], FILTER_VALIDATE_BOOLEAN);
                                           $doctormanage=filter_var($row_node['doctor'], FILTER_VALIDATE_BOOLEAN);
                                           $nurse_resp=$row_node['nurse_resp'];      
                                           $personal_assistant_resp=$row_node['personal_assistant_resp'];                    
                                           $doctormanage_resp=$row_node['doctor_resp'];                      




                                      if($device_type == 'swpcp')
                                              {
                                           //swpcp
                                            $starter_relay_off_timer = $row_node['starter_relay_off_timer'];  
                                            $start_high_current_cutoff = $row_node['start_high_current_cutoff'];  
                                            $start_low_current_cutoff= $row_node['start_low_current_cutoff'];  
                                            $Run_high_current_cutoff = $row_node['Run_high_current_cutoff'];  
                                            $Run_low_current_cutoff = $row_node['Run_low_current_cutoff'];  
                                            $high_voltage_cutoff = $row_node['high_voltage_cutoff'];  
                                            $low_voltage_cutoff = $row_node['low_voltage_cutoff'];  
                                            $ultra_high_voltage_cutoff = $row_node['ultra_high_voltage_cutoff'];  
                                            $ultra_high_current_cutoff = $row_node['ultra_high_current_cutoff'];  
                                            $USER_SET_MAX_CURRENT_CUTOFF_LIMIT = $row_node['USER_SET_MAX_CURRENT_CUTOFF_LIMIT'];  
                                            $USER_AND_DEVICE_MAX_CURRENT_LIMIT = $row_node['USER_AND_DEVICE_MAX_CURRENT_LIMIT'];  
                                            $LOCK_ENABLE_DISABLE = (bool)$row_node['LOCK_ENABLE_DISABLE'];  
                                         }



                                         if($device_type == 'cooker')
                                              {
                                           //cooker

                                            $cooker_timer = $row_node['cooker_timer_1'];  
                                            $cooking_temperature = $row_node['cooker_max_temperature_1'];  
                                            $Keep_warm= $row_node['Keep_warm_1'];  
                                            $Pre_timer_constant_mode = $row_node['Pre_timer_constant_mode'];  
                                            $Pre_timer = $row_node['Pre_timer'];  
                                            $Pre_temperature = $row_node['Pre_temperature'];  
                                            $Coooker_max_temperature = $row_node['Coooker_max_temperature'];  
                                            $Cooker_constant_mode = $row_node['Cooker_constant_mode'];  
                                            $Keep_Warm_timer = $row_node['Keep_Warm_timer_1'];  
                                            $Dishes = $row_node['dish'];
                                            $pre_timer_wait_temperature_constant_mode = $row_node['pre_timer_wait_temperature_constant_mode'];  
                                            $pre_timer_waiting_time = $row_node['pre_timer_waiting_time'];
                                            $pre_timer_Waiting_temperature = $row_node['pre_timer_Waiting_temperature'];  
                                            $warm_constant_cut_off = $row_node['warm_constant_cut_off'];  
                                            $cook_wait_temp_constant_mode = $row_node['cook_wait_temp_constant_mode'];  
                                            $cook_waiting_time = $row_node['cook_waiting_time'];  
                                            $cook_Waiting_temperature = $row_node['cook_Waiting_temperature'];  
                                            $warm_wait_temp_constant_mode = $row_node['warm_wait_temp_constant_mode'];  
                                            $warm_waiting_time = $row_node['warm_waiting_time'];  
                                            $warm_Waiting_temperature = $row_node['warm_Waiting_temperature']; 
                                            $cooker_ok_button = $row_node['cooker_ok_button']; 
                                            $reload_default = $row_node['reload_default']; 

	

                                             


                                         }


/*****************************************************/

if ($recipe == "RICE") {
    $recipe = 1;
    $asper_value_hex = '000000003C6E00007D013C2800B400000001036E000000';

} elseif ($recipe == "BROWNRICE") {
    $recipe = 2;
    $asper_value_hex = '000001003C6E010F00013C2800B400000001036E000000';
} elseif ($recipe == "FRY") {
    $recipe = 3;
    $asper_value_hex = '0000020150B4000FB4013C280118000000000000000000';
} elseif ($recipe == "MILK") {
    $recipe = 4;
    $asper_value_hex = '000003011964000F6E013C280096000000000000000000';
} elseif ($recipe == "SOUP") {
    $recipe = 5;
    $asper_value_hex = '000004001E960000B4013C2800B4000000000000000000';
} elseif ($recipe == "BIRIYANI") {
    $recipe = 6;
    $asper_value_hex = '000005002DF00100B4013C2800B4000000000000000000';
} elseif ($recipe == "IDLY") {
    $recipe = 7;
    $asper_value_hex = '000006000F7D00055A013C2800A0000000000000000000';
} elseif ($recipe == "DHAL") {
    $recipe = 8;
   $asper_value_hex = '000007002D960000A0013C280096000000000000000000';
} elseif ($recipe == "HARDVEG") {
    $recipe = 9;

    $asper_value_hex = '0000080128C80000F0013C2800F0000000000000000000';
} elseif ($recipe == "WATER") {
    $recipe = 10;
    $asper_value_hex = '000009001E960000A0013C2800A0000000000000000000';
} elseif ($recipe == "DEEPFRY") {
    $recipe = 11;
    $asper_value_hex = '00000A011EBE000CE6013C2801180104BE000000000000';
} elseif ($recipe == "POPCORN") {
    $recipe = 12;
   $asper_value_hex = '00000B0003DC0006A5013C2801220000000101DC000000';
} elseif ($recipe == "CAKE") {
    $recipe = 13;
    $asper_value_hex = '00000C012DDC0005B4013C280122000000000000000000';
} elseif ($recipe == "MAGGI") {
    $recipe = 14;

    $asper_value_hex = '00000D00056A000A5A013C2800B4000560000000000000';
} elseif ($recipe == "NOODLES") {
    $recipe = 15;
    $asper_value_hex = '00000E000ADC0008B4013C2800B4000000000000000000';
} elseif ($recipe == "DIY") {
    $recipe = 16;
    $asper_value_hex = '00000F002DDC0005B4013C2800B4000000000000000000';
} 
else  {

}









                                 
                                         
  /***************************************************/                                        

                                             if($device_type == 'fan')
                                              {
                                             //fan
                                            $fan_resistance = $row_node['fan_resistance'];  
                                            $fan_lmin = $row_node['fan_lmin'];  
                                            $fan_lmax= $row_node['fan_lmax'];  
                                            $fan_frequency = $row_node['fan_frequency'];  
                                            $fan_pairs = $row_node['fan_pairs'];  
                                            $fan_open_loop_current = $row_node['fan_open_loop_current'];  
                                            $fan_power_limit = $row_node['fan_power_limit'];  
                                            $fan_torque_limit = $row_node['fan_torque_limit'];  
                                            $fan_speed1 = $row_node['fan_speed1'];  
                                            $fan_speed2= $row_node['fan_speed2'];
                                            $fan_speed3 = $row_node['fan_speed3'];  
                                            $fan_speed4= $row_node['fan_speed4'];
                                            $fan_speed5 = $row_node['fan_speed5'];  
                                            $fan_speed6= $row_node['fan_speed6'];
                                            $fan_speed7 = $row_node['fan_speed7'];  
                                            $fan_button= $row_node['fan_button'];
                                             } 
                                       
                                             
                                            
                                            
                                            
                                      
                                       $tz_properties=$row_node['TZ_properties'];
                                       $br_properties=$row_node['BR_properties'];
                                       $umi_properties=$row_node['UMI_properties'];
                                       $ec_properties=$row_node['EC_properties'];
                                       $cm_properties=$row_node['CM_properties'];
                                       $tu_properties=$row_node['TU_properties'];
                                       $remote_pair_properties=$row_node['remote_pair_properties'];
                                       $display_properties=$row_node['display_properties'];
                                       $swpcp_1ph_properties=$row_node['swpcp_1ph_properties'];
                                       $fan_properties=$row_node['fan_properties'];
                                       $lc_properties=$row_node['LC_properties'];
                                       $cooker_properties=$row_node['cooker_properties'];
                                       



                                     $baudrate=$row_node['baudrate'];
                                     $uartmsgid=$row_node['uartmsgid'];
                                     $time=$row_node['time'];
                                     $errorcode=$row_node['errorcode'];
                                     $comm_mode=$row_node['comm_mode'];













/**********************************************************************************************/



//$query = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.1' AND user_id = '177'";
$query = "SELECT * FROM product_api WHERE model_name = '$model_name'";

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

        



 
//$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFACT V1.1' AND user_id = '177'";
$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";

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
  $query_param = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.1' AND user_id = '177' AND parameter_name='$param1'";

 $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND parameter_name='$param1'";



        $params = $db->get_results($query_param); 
        $num_param = $db->num_rows($query_param);
        $row_param = $db->get_row($query_param); 
        $parameter_name = $row_param['parameter_name'];

        $brand_name = $row_param['brand_name'];
        $brand_logo = $row_param['brand_logo'];
        $product_name = $row_param['product_name'];
       // $model_name = $row_param['model_name'];
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



$baudrate_1 = "34";
$direction = "8";


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
}


elseif ($param1 =='Led Color') {
    $as_per_ui_value =  $hue;
}




 elseif ($param1 =='name') {

if($name_dev == 'NULL')
{

$curr_name=$curr_devicetype;

}

else{


$curr_name=$name_dev;
}






    $as_per_ui_value =  $curr_name;
} elseif ($param1 =='Current') {
    $as_per_ui_value =  $current;
} elseif ($param1 =='Voltage') {
    $as_per_ui_value =  $bus_voltage;
  //  $as_per_ui_value =  $voltage;
} elseif ($param1 =='rpm') {
    $as_per_ui_value =  $rpm;
} elseif ($param1 =='Watt') {
    $as_per_ui_value =  $Watt;
} 

elseif ($param1 =='Skipmode') {
    $as_per_ui_value =  true;
}

elseif ($param1 =='Cooker mode') {
    $as_per_ui_value =  $cookermode;
}


elseif ($param1 =='Recipee') {
    $as_per_ui_value =  $recipe;
}

elseif ($param1 =='remaining time') {
    $as_per_ui_value =  $cooktime;
}


elseif ($param1 =='running temperature') {
    $as_per_ui_value =  $cook_run_temp;
}



elseif ($param1 =='Nurse') {
    $as_per_ui_value = $nurse;
}


elseif ($param1 =='Nurse_2') {
    $as_per_ui_value = $nurse_resp;
}


elseif ($param1 =='Doctor') {
    $as_per_ui_value = $doctormanage;
}


elseif ($param1 =='Doctor_2') {
    $as_per_ui_value = $doctormanage_resp;
}


elseif ($param1 =='PA_2') {
    $as_per_ui_value = $personal_assistant_resp;
}

elseif ($param1 =='PA') {
    $as_per_ui_value = $personal_assistant;
}



elseif ($param1 =='Auto Clean') {
    $as_per_ui_value = $hood_autoclean;
}


elseif ($param1 =='Air Flow') {
    $as_per_ui_value = $hood_speed;
}

elseif ($param1 =='Lamp') {
    $as_per_ui_value = $hood_lamp;
}




else {
   $as_per_ui_value =''; 
}













/*


if($model_name = 'EFACT V1.1')
{


if ($cmd_defaultvalue[$i] =='resistance') {
    $as_per_ui_value =  $fan_resistance;
} elseif ($cmd_defaultvalue[$i] =='lmin') {
      $as_per_ui_value = $fan_lmin;
} elseif ($cmd_defaultvalue[$i] =='lmax') {
      $as_per_ui_value = $fan_lmax;
} elseif ($cmd_defaultvalue[$i] =='frequency') {
      $as_per_ui_value = $fan_frequency;
} elseif ($cmd_defaultvalue[$i] =='pole pairs') {
      $as_per_ui_value = $fan_pairs;
} elseif ($cmd_defaultvalue[$i] =='Startup Current') {
      $as_per_ui_value = $fan_startup_current;
} elseif ($cmd_defaultvalue[$i] =='max current') {
      $as_per_ui_value = $fan_max_current;
} elseif ($cmd_defaultvalue[$i] =='min rpm') {
      $as_per_ui_value = $fan_min_rpm;
} elseif ($cmd_defaultvalue[$i] =='max rpm') {
      $as_per_ui_value = $fan_max_rpm;
} elseif ($cmd_defaultvalue[$i] =='current bw') {
      $as_per_ui_value = $fan_current_bw;
} elseif ($cmd_defaultvalue[$i] =='Bemf freq') {
      $as_per_ui_value = $fan_bemf_freq;
} elseif ($cmd_defaultvalue[$i] =='Bemf Volt') {
      $as_per_ui_value = $fan_bemf_volt;
} elseif ($cmd_defaultvalue[$i] =='Speed L1') {
      $as_per_ui_value = $fan_spped_l1;
} elseif ($cmd_defaultvalue[$i] =='Speed L2') {
      $as_per_ui_value = $fan_spped_l2;
} elseif ($cmd_defaultvalue[$i] =='Speed L3') {
      $as_per_ui_value = $fan_spped_l3;
} elseif ($cmd_defaultvalue[$i] =='Speed L4') {
      $as_per_ui_value = $fan_spped_l4;
}




}

*/



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
  $integer_value = (int)$as_per_ui_value;
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

//$devv_type = 'Cooker';
//$hex_ckvalue = 'A301';
if($param1 == 'Recipee'){


$field_cmd = [
    "default_name" => $cmd_defaultvalue[$i],
    "asperui_value" => $integervalue,  
    "value" => $asper_value_hex,  
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
 }else{



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


if($cmd_uitype[$i] == 'Auto Trigger with Timer')
{
$cmd_valueinhexmin[$i]= (int)$get_status_button;

}




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

if($name_dev == 'NULL')
{

$curr_name=$curr_devicetype;

}

else{


$curr_name=$name_dev;
}







    $as_per_ui_value =  $curr_name;
} elseif ($resp_defaultvalue[$i] =='current') {
    $as_per_ui_value =  $current;
} elseif ($resp_defaultvalue[$i] =='voltage') {
    $as_per_ui_value =  $bus_voltage;
} elseif ($resp_defaultvalue[$i] =='rpm') {
    $as_per_ui_value =  $rpm;
} else {
   $as_per_ui_value =''; 
}



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


 /*
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
*/

$unique_params = [];

for ($pages = 0; $pages <= $number_of_pages; $pages++) {
  
    $parameter_name = $param['parameter_name'];

    if (!in_array($parameter_name, $unique_params)) {
        $unique_params[] = $parameter_name;

        $para = [
             
            "frame" => [
            "page_name" => $page_names,
                "page" => (int)$number_of_pages,
                "seq" => (int)$sequence,
                "name" => $parameter_name,
                "multiple_ui" => [$cmd_multipleui, $resp_multipleui, $error_multipleui],
                "trigger_ui" => [$cmd_triggerui, $resp_triggerui, $error_triggerui],
                "commands" => $fields_cmd,
                "response" => $fields_resp,
                "error" => $fields_error
            ]
        ];

        $result[] = $para;
        usort($result, function($a, $b) {
    return $a['frame']['seq'] <=> $b['frame']['seq'];
});
    }
}



usort($result, function($a, $b) {
    return $a['frame']['page'] <=> $b['frame']['page'];
});
 
 $final_result = array_map(function($item) {
    $frame = $item['frame'];
    $item['frame'] = $frame;
    return $item;
}, $result);  
  
     
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



//$para_result = [];

//$para_result = $result;


$para_result = [];

foreach ($final_result as $item) {
    $page = $item['frame']['page_name'];

    if (!isset($para_result[$page])) {
        $para_result[$page] = [];
    }

    $para_result[$page][] = $item;
}

foreach ($para_result as $page => &$items) {
    usort($items, function($a, $b) {
        return $a['frame']['seq'] <=> $b['frame']['seq'];
    });
}

foreach ($para_result as $page => &$items) {
    foreach ($items as &$item) {
        $frame = $item['frame'];
        unset($frame['page']);
         unset($frame['page_name']);
        unset($frame['seq']);
        $item['frame'] = $frame;
    }
}

//unset($item);
//unset($items);


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
$devicetype=$row_inter['device_type'];





$type="efactor.device.$devicetype";



if($name_dev == 'NULL')
{
$curr_name=$curr_devicetype;

}

else{

$curr_name=$name_dev;
}




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
                "pages" =>  $para_result,
                   
               
                "primary" => "power",
                "type" => $type,
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
            "connected" => $net_conn_1,
            "timestamp" => 1717898131809
        )
    )
);


/*

$access_token='eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJwaG9uZV92ZXJpZmllZCI6dHJ1ZSwiY3VzdG9tOnVzZXJfaWQiOiIxNzciLCJ0b2tlbl91c2UiOiJpZCIsImF1dGhfdGltZSI6MTY2NzAyMjI0MiwiZXhwIjoxNjY3MDI1ODQyLCJpYXQiOjE2NjcwMjIyNDIsInBob25lIjoiOTM5MTA0Mzc4MSJ9.q9CrbVNL-PRfAjnvvLGMmazodCk2Y1Lid9gch5ey0R4';






$node = json_encode(array("url"=>"action=test_details",
                      "token"=>$access_token,
                      "next_id"=>$nextid,
                      "total"=>$num_dev,
                      "node_details"=>array($node_details),
                       "nodes"=>$node_id
                        ));

*/




if($node_id)
{


$node = json_encode(array("url"=>"action=test_details",
                      "token"=>$val,
                      "next_id"=>$nextid,
                      "total"=>$num_dev,
                      "node_details"=>array($node_details),
                       "nodes"=>$node_id
                        ));

}

else{


$node = json_encode(array("url"=>"action=test_details",
                      "token"=>$val,
                      "next_id"=>$nextid,
                      "total"=>$num_dev,
                      "node_details"=>"",
                       "nodes"=>$node_id
                        ));



}








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
