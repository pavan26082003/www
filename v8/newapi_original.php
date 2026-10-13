<?php

$startTime = microtime(true);

$response = array();
include('config.php');
//include('resolve_url.php');
include('functions.php');
include('phpMQTT.php');
header('Content-Type: application/json');

header('Content-Type: application/x-www-form-urlencoded');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

/*-----------------------------------------------------*/
$req = file_get_contents ( 'php://input' );
$rawdata = $HTTP_RAW_POST_DATA;
//$json_data = json_decode($req);

header('Content-Type: application/json');
header('Authorization: API Key');

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



function hexToFloat($hex) {  
    $hex = str_pad($hex, 8, '0', STR_PAD_LEFT);  
    $reversedHex = implode('', array_reverse(str_split($hex, 2)));  
    $binary = pack('H*', $reversedHex);  
    $float = unpack('f', $binary);  
    return $float[1];  
}



function floatToHex($float) {
    $binary = pack('f', $float);

    $hexArray = unpack('H*', $binary);

    $hexString = strtoupper($hexArray[1]);

    $hexString = str_pad($hexString, 8, '0', STR_PAD_LEFT);

    $reversedHexString = implode('', array_reverse(str_split($hexString, 2)));

    return $reversedHexString;
}



function intToHex($intValue) {
    if($intValue === 'true'){
 return 01;
    }elseif($intValue === 'false'){
 return 00;
    }else{
     return dechex(intval($intValue));  
    }
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


/********************************************************************************/
/********************************************************************************/

if (isset($_GET['action']) && $_GET['action']=='test_details')
{

$startid =$_GET['start_id'];

$start_time = microtime(true);
                        
			       
			$header = apache_request_headers();  
			foreach ($header as $key => $val) {
			
			$query_dev="SELECT node_id FROM efactorproduct where access_token='$val'";
			$doctors = $db->get_results($query_dev); 
			$num_dev = $db->num_rows($query_dev);
                        $row_dev = $db->get_row($query_dev);    
		        $node_id=$row_dev['node_id'];


$db->query("update efactorproduct set app_version='v8' where access_token='$val'");


if(!empty($_GET["start_id"]))
 {



$node_id=$startid;



$query_4 = "SELECT * FROM efactorproduct where node_id='$node_id'";
							   
							   $row_4 = $db->get_row($query_4);
						          // $node_id=$row['node_id'];
                                                           $device_type=$row_4['device_type'];
                                                           $model_name=$row_4['model_number'];




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


    $query_3 = "SELECT * FROM efactorproduct where node_id='$node_id'";
							    
							   $row_3 = $db->get_row($query_3);
						           $node_id=$row_3['node_id'];
                                                           $device_type=$row_3['device_type'];
                                                           $model_name=$row_3['model_number'];




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



if($model_name == 'EFDEFLT V1.0'){
    $query_node="SELECT * FROM efactorproduct where node_id='$node_id'";
}else{
$query_node="SELECT * FROM `$model_name` where node_id='$node_id'";
}
			$doctors_node = $db->get_results($query_node); 
			
                        $dbcker = $db->get_row($query_node);    
                       // print_r($dbcker);
		        $nodeid=$dbcker['node_id'];
                        $user_id=$dbcker['user_id'];
                        $curr_device_type=$dbcker['current_devicetype'];
                        $devicetype=$dbcker['device_type'];
                        $mortgage=$dbcker['Mortgage_page'];
                           $status=$dbcker['status_page'];
                           $device_comm_page=$dbcker['device_comm_page'];
                           $remote_pair_pairing=$dbcker['Remote_Pair_Pairing'];
                           $config_config=$dbcker['Configure_Config'];
                           $remote_address=$dbcker['Remote_Address'];



 $type="efactor.device.$devicetype"; 






/*-----------------------------------------------*/

// Determine device image
        if ($model_name == 'EF-003FMN03' || $model_name == 'EF-002FMN02' || $model_name == 'EF-001FMN01' || $model_name == 'BAJAJ-001FMN01') {
            $deviceimage = 'fan-EF-003FMN03.jpg';
        } elseif ($model_name == 'EFCKER V1.0') {
            $deviceimage = 'efact_cooker.jpg';
        } elseif ($model_name == 'EF-001LIGHT01' || $model_name == 'EF-002CWLIGHT02' || $model_name == 'EF-001RGBLIGHT01' || $model_name == 'EF-003WLIGHT03') {
            $deviceimage = 'light_org.jpg';
        } elseif ($model_name == 'EF-001OULT01' || $model_name == 'EF-001SOCKET_6A01' || $model_name == 'EF-001SOCKET_10A01') {
            $deviceimage = 'outlet.jpg';
        } elseif ($model_name == 'EFSWPCP V1.0') {
            $deviceimage = 'swpcp.jpg';
        } elseif ($model_name == 'INVENDIS_MFM_V1') {
            $deviceimage = 'invendis_device.jpg';
        }

elseif ($model_name == 'EF_RF_GATEW V1.0') {
            $deviceimage = 'gateway_org.jpg';
        }




else {
            $deviceimage = 'input_output.jpg';
        }


 $device_image = "https://efactor.tech/files/brand_logo/" . $deviceimage;



/*-----------------------------------------------*/


                        
//$query = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND user_id = '177'";
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

        



     //   EFDEFLT V1.0
   
//$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFDEFLT V1.1'";
//$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";
$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name' AND parameter_name IN ('name', 'power')";

//$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";

//$result_1 = $db->get_results($query_1); 
$result_2 = $db->get_results($query_2); 

//$result_3 = $db->num_rows($query_1);
$result_4 = $db->num_rows($query_2);

//$result_5 = $db->get_row($query_1); 
$result_6 = $db->get_row($query_2); 


//$doctors = $result_2;

$doctors = $result_2;
$num_11 = $result_4;

$row = $result_2;

$parameter_name = $row['parameter_name'];

   $result = [];







 
foreach ($doctors as $param) {
   
    $fields_cmd = [];
    $fields_resp = [];
    $fields_error = [];

$param1=$param['parameter_name'];

//$query_param1 = "SELECT * FROM product_api WHERE model_name = 'EFDEFLT V1.1' AND parameter_name='$param1'";
$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
  

//$result_1 = $db->get_results($query_param1); 
$result_2 = $db->get_results($query_param); 

//$result_3 = $db->num_rows($query_param1);
$result_4 = $db->num_rows($query_param);

//$row_param1 = $db->get_row($query_param1); 
$row_param2 = $db->get_row($query_param); 
  

$params = array_merge($result_1, $result_2);
$num_param = $result_3 + $result_4;
//$row_param = array_merge((array)$row_param1, (array)$row_param2);
$row_param = array_merge((array)$row_param1, (array)$row_param2);

 
 //$row_param = array_merge($result_5, $result_6);
/*
    $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";

        $params = $db->get_results($query_param); 
        $num_param = $db->num_rows($query_param);
          $row_param = $db->get_row($query_param); 
      //  print_r($row_param1);
*/
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
      /*  $resp_dataflow = $row_param['resp_dataflow'];
        $resp_framesize = $row_param['resp_framesize'];
        $resp_frametrigger = $row_param['resp_frametrigger'];
        $error_dataflow = $row_param['error_dataflow'];
        $error_framesize = $row_param['error_framesize'];
        $error_frametrigger = $row_param['error_frametrigger'];*/

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
            $cmd_sno_asperui = explode(',', $row_param['cmd_sno_asperui']);
            $cmd_asperui_values = explode(',', $row_param['cmd_asperui_values']);

            

           


                        


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






  //$local_control='enable';



        
 

 
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
            "local_control" => $local_control,
            "ui_ids"   => (int)$cmd_uiid[$i],
            "ui_description" => $cmd_uidescriptioninout[$i]
            ];


//$fields_cmd[] = $field_cmd;



          }elseif($cmd_uitype[$i] == 'None'){ 
            $field_cmd = [
               "default_name" =>$cmd_defaultvalue[$i],
               "value" => $cmd_valueinhexmin[$i],
               "Field_description" => $cmd_fielddescription[$i],
               "no_of_bytes" => (int)$cmd_noofbytes[$i],
               "byte_start_position" => (int)$cmd_startbytePosition[$i] + 27,
               "ui_type"  => $cmd_uitype[$i],
               "local_control" => $local_control

        ]; 

      }else{




        
$as_per_ui = $cmd_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

if (empty($as_per_ui_array_from_formatted) || $as_per_ui_array_from_formatted === ['']) {
    $as_per_ui_array_from_formatted = [];  
}

$sno_as_per_ui = $cmd_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
}

$asperui_values = $cmd_asperui_values[$i];
$asperui_values_array = explode('][', trim($asperui_values, '[]'));
$formatted_asperui_values = implode(',', $asperui_values_array);
$asperui_values_array_from_formatted = explode(',', $formatted_asperui_values);
 
if (empty($asperui_values_array_from_formatted) || $asperui_values_array_from_formatted === ['']) {
    $asperui_values_array_from_formatted = [];  
}



 
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($cmd_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }
 


if($cmd_uitype[$i] === 'Toggle Switch'){
// print_r($as_per_ui);
 //print_r($asperui_values);
//print_r($as_per_ui_value);
$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);
$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';    
    }elseif ($cmd_uitype[$i] === 'Slider' || $cmd_uitype[$i] === 'Hue Slider'  || $cmd_uitype[$i] === 'Hue Slider Custom'  || $cmd_uitype[$i] === 'Brightness Slider'  || $cmd_uitype[$i] === 'Airflow Slider') {
     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
        } /*elseif ($cmd_datatype[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $integervalue = "null";  
            }
        }*/
    
    }elseif ($cmd_uitype[$i]  === 'Text') {
     
        if ($cmd_datatype[$i] === 'String') {
                if(!empty($as_per_ui_value)){
                    $integervalue = $as_per_ui_value;
                }else{
                    $integervalue = "null";  
            }  
         }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
         }elseif ($cmd_datatype[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $integervalue = "null";  
            }
         }else{
            if(!empty($as_per_ui_value)){
                $integervalue = $as_per_ui_value;  
            }else{
                $integervalue = "null";  
            }
         }
    
    }else{
        if(!empty($as_per_ui_value)){
            $integervalue = $as_per_ui_value;  
        }else{
            $integervalue = "null";  
        }
    }
 


   /* if ($cmd_datatype[$i] === 'Float') {
        // $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
         // $integervalue = hexToFloat($integer_value);
         // $integervalue = empty($integer_value) ? (float)$integer_value :  0.0;
         $cmd_value2 = floatToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
         $cmd_sliderstep = (float)$cmd_sliderstepvalue[$i];
        $display_min = (float)$cmd_displayvaluemin[$i];
        $display_max = (float)$cmd_displayvaluemax[$i];
     }else*/if ($cmd_datatype[$i] === 'UnsignedInt') {
        if($cmd_uitype[$i] == 'Toggle Switch'){
            $ui_toggle2 = explode('][', trim($as_per_ui, '[]'));
    $ui_toggle_value2 = explode('][', trim($asperui_values, '[]'));
            $dropdown_map22 = array_combine($ui_toggle2,$ui_toggle_value2);  
            $cmd_value = isset($dropdown_map22[$integervalue]) ? $dropdown_map22[$integervalue] : '';  
            //print_r($cmd_value);
            $display_min = (int)$cmd_displayvaluemin[$i];
            $display_max = (int)$cmd_displayvaluemax[$i];
        }else{
      //  $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
      //   $integervalue = hexToInt($integer_value);
         $cmd_value2 = intToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
        $cmd_sliderstep = (int)$cmd_sliderstepvalue[$i];
        $display_min = (int)$cmd_displayvaluemin[$i];
        $display_max = (int)$cmd_displayvaluemax[$i];
        }
    }elseif ($cmd_datatype[$i] === 'String') {
      // $integervalue = (string)$integer_value;
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
     
      if($page_names == 'Configure' || $page_names == 'Remote Pair' || $page_names == 'Configure.Config' || $page_names == 'Set Max Current' || $page_names == 'Device Setting'){

if($parameter_name == 'wifi config' || $parameter_name == 'DAC')
{
$local_control ='disable';       //disable

}
else{
//$local_control ='disable';       //disable
   $local_control ='enable';
}



}else{


$local_control='enable';



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
    "local_control" => $local_control,
    "ui_ids"   => (int)$cmd_uiid[$i],
    "asperui" => $as_per_ui_array_from_formatted,  
    "sno_asperui" => $sno_as_per_ui_array_from_formatted,  
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
              "local_control" => $local_control
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
            "local_control" => $local_control,
            "ui_description" => $cmd_uidescriptioninout[$i],
            "ui_ids"   => (int)$cmd_uiid[$i] 
               
           
                  ];

$fields_cmd[] = $field_cmd;


}

}





    }




}









 
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


$para_default_result = $result_default;


unset($para_result['']);

/*
if($model_name == 'EF-003FMN03')
{

// Remove the unwanted pages after processing
//unset($para_result['Device Communication']);
//unset($para_result['Status']);



unset($para_result['Configure.Config']);
unset($para_result['Remote Address']);
unset($para_result['Remote Pair.Pairing']);

}

  //02-06-25*/



if ($mortgage === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Mortgage') {
 unset($para_result[$key]);
                   }
    }
}

if ($status === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Status') {
 unset($para_result[$key]);
                   }
    }
}




if ($device_comm_page === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Device Communication') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_pair_pairing === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Pair.Pairing') {
 unset($para_result[$key]);
                   }
    }
}


if ($config_config === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Configure.Config') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_address === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Address') {
 unset($para_result[$key]);
                   }
    }
}




/**************************/





/*-------------------------------------------------------------*/
/*************************************************************/



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
//$devicetype=$row_inter['device_type'];
$curr_devicetype=$row_inter['current_devicetype'];
$ssid=$row_inter['ssid'];
$vendor_name=$row_inter['vendor_name'];



if($vendor_name == 'EFACTOR')

{
$brandname='efactor';
}
if($vendor_name == 'BAJAJ')

{
$brandname='Bajaj';


}

if($vendor_name == 'DHRIO')

{
$brandname='dhrio';


}

if($vendor_name == 'Skytec')

{
$brandname='Skytec';


}


if($vendor_name == 'Invendis')

{
$brandname='Invendis';


}



if($vendor_name == 'exhaust_hood')

{
$brandname='exhaust';


}




//print_r($doctors_inter);

$new_prefix = "EWIFI";
$parts = explode("_", $curr_devicetype, 2);
$access_point = $new_prefix . "_" . $parts[1];
//echo $updated_devicetype; 


$type="efactor.device.$devicetype";





$params = http_build_query([
    'model_name' => $model_number,
    'user_id' => $user_id,
    'node_id' => $node_id
]);




$url_params = "model_name={$model_number}&user_id={$user_id}";
$auto_url_params = "model_name={$model_number}&user_id={$user_id}&node_id={$node_id}";








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
                "name" => $curr_device_type,
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
               "Device Id"=> $node_id,
                "pages" =>  $para_result,
                   
               
                "primary" => "power",
                "type" => $type,
                "brand_logo" => "https://efactor.tech/files/brand_logo/" . $brandname . ".jpg",
               "device_p2p_control"=>"disable",
                "Device Communication"=>"disable",
                "Status"=>"enable"

            )
        ),




"info" => array(
            "fw_version" => "1.5.1",
            "model" => $model_number,
            "name" => $curr_devicetype,
            "type" => $moduletype,
            "SSID" => $ssid,
            "Access_Point" => $access_point
                    ),
















        "node_id" => $node_id
    ),
    "role" => "primary",
    "status" => array(
        "connectivity" => array(
            "connected" => ($net_conn_1 === "true") ? true : false,
            "timestamp" => $timestamp
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




$end_time = microtime(true);
$execution_time = $end_time - $start_time;
//echo "Execution time: " . $execution_time . " seconds"; 




//echo json_encode(array("node_details" => array($node_details), "nodes" => $node_id));

echo $node;





exit;



/************************************************************/

    }

}


/******************************************************************************/

if (isset($_GET['action']) && $_GET['action']=='auto_test_details')
{

$startid =$_GET['start_id'];

$start_time = microtime(true);
                        
			       
			$header = apache_request_headers();  
			foreach ($header as $key => $val) {
			
			$query_dev="SELECT node_id FROM efactorproduct where access_token='$val'";
			$doctors = $db->get_results($query_dev); 
			$num_total = $db->num_rows($query_dev);
                      //  $row_dev = $db->get_row($query_dev);    
		      //  $node_id=$row_dev['node_id'];





if(empty($_GET["start_id"]))
{






// First check for nodes with add_gateway not NULL and is_added=1
$query_dev = "SELECT node_id FROM efactorproduct 
              WHERE access_token='$val' 
              AND add_gateway IS NOT NULL 
              AND add_gateway != 'NULL' 
              AND is_added=1 
              LIMIT 1";

$doctors = $db->get_results($query_dev);
$num_dev = $db->num_rows($query_dev);

if ($num_dev > 0) {



    // Found node with gateway conditions
    $row_dev = $db->get_row($query_dev);
    $node_id = $row_dev['node_id'];
} else {


    // Fall back to normal query if no gateway nodes found
    $query_dev = "SELECT node_id FROM efactorproduct 
                 WHERE access_token='$val' 
                 LIMIT 1";
    $doctors = $db->get_results($query_dev);
    $num_dev = $db->num_rows($query_dev);
    
    if ($num_dev > 0) {
        $row_dev = $db->get_row($query_dev);
        $node_id = $row_dev['node_id'];

/*---------------------------*/

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

    $nextid = $row['node_id'];
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
        $nextid = $row['node_id'];
    } else {
        $nextid = null; // Handle no result found
    }
}






/*---------------------------*/

    } else {
        $node_id = null; // No nodes found at all
    }
}






}


else
{




$node_id=$startid;
$query_4 = "SELECT * FROM efactorproduct where node_id='$node_id'";
							   
	$row_4 = $db->get_row($query_4);
	// $node_id=$row['node_id'];
        $device_type=$row_4['device_type'];
        $model_name=$row_4['model_number'];
        $add_gateway=$row_4['add_gateway'];
        $is_added=$row_4['is_added'];



if(!$add_gateway)
               
{


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





}


}


$db->query("update efactorproduct set app_version='v8' where access_token='$val'");


if (!empty($_GET["start_id"])) {
    // Case 1: When start_id is provided
    $current_node_id = $_GET["start_id"];
    
    // Get current device info
    $query_current = "SELECT * FROM efactorproduct WHERE node_id='$current_node_id'";
    $row_current = $db->get_row($query_current);
    
    if ($row_current) {
        $device_type = $row_current['device_type'];
        $model_name = $row_current['model_number'];
        
        // Determine if current device is part of a gateway group
        $gateway_id = ($row_current['is_added'] == '1' && $row_current['add_gateway'] == $current_node_id)
                    ? $current_node_id
                    : $row_current['add_gateway'];
        
        // Find next device that's not in current gateway group
        $query_next = "
            SELECT node_id 
            FROM efactorproduct 
            WHERE access_token = '$val' 
              AND (
                  (add_gateway IS NULL OR add_gateway = 'NULL' OR add_gateway != '$gateway_id')
                  OR 
                  (is_added = '1' AND add_gateway = node_id AND node_id != '$gateway_id')
              )
              AND node_id > '$current_node_id'
            ORDER BY node_id ASC
            LIMIT 1
        ";
        $row_next = $db->get_row($query_next);
        $nextid = $row_next ? $row_next['node_id'] : null;
    }
} else {
    // Case 2: When no start_id is provided
    
    $query_gateway = "
    SELECT node_id, device_type, model_number 
    FROM efactorproduct 
    WHERE access_token = '$val' 
      AND is_added = '1' 
      AND add_gateway = node_id
    ORDER BY node_id ASC
    LIMIT 1
";
$row_gateway = $db->get_row($query_gateway);

if ($row_gateway) {
   // echo 'Gateway found - use as current device';
    $device_id = $row_gateway['node_id'];
    $device_type = $row_gateway['device_type'];
    $model_name = $row_gateway['model_number'];
    
    // Try to find first linked device
    $query_linked = "
        SELECT node_id 
        FROM efactorproduct 
        WHERE access_token = '$val' 
          AND add_gateway = '$device_id'
          AND node_id != '$device_id'
        ORDER BY node_id ASC
        LIMIT 1
    ";
    $row_linked = $db->get_row($query_linked);
    
    if ($row_linked) {
        //echo 'Linked device found';
        $nextid = $row_linked['node_id'];
        
        // Now check if this nextid is linked to any gateway
        $check_gateway_link = "
            SELECT add_gateway 
            FROM efactorproduct 
            WHERE node_id = '$nextid'
            LIMIT 1
        ";
        $row_check = $db->get_row($check_gateway_link);
        
        // If linked to gateway, keep searching for next non-linked device
        if ($row_check && ($row_check['add_gateway'] !== null && $row_check['add_gateway'] != 'NULL')) {
         //   echo 'Linked device is connected to gateway, finding next non-linked device';
            
            $query_next_non_linked = "
                SELECT node_id 
                FROM efactorproduct 
                WHERE access_token = '$val' 
                  AND node_id > '$nextid'
                  AND (add_gateway IS NULL OR add_gateway = 'NULL')
                ORDER BY node_id ASC
                LIMIT 1
            ";
            $row_non_linked = $db->get_row($query_next_non_linked);
            $nextid = $row_non_linked ? $row_non_linked['node_id'] : null;
        }
    } else {
      //  echo 'No linked devices - find next device in database';
        $query_next = "
            SELECT node_id 
            FROM efactorproduct 
            WHERE access_token = '$val' 
              AND node_id > '$device_id'
              AND (add_gateway IS NULL OR add_gateway = 'NULL')
            ORDER BY node_id ASC
            LIMIT 1
        ";
        $row_next = $db->get_row($query_next);
        $nextid = $row_next ? $row_next['node_id'] : null;
    }
}
}






/*-------------------------------------------------------------*/
/*************************************************************/


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
//$devicetype=$row_inter['device_type'];
$curr_devicetype=$row_inter['current_devicetype'];
$ssid=$row_inter['ssid'];
$mac_value=$row_inter['mac'];
$is_added=$row_inter['is_added'];
$add_gateway=$row_inter['add_gateway'];
$model_name=$row_inter['model_number'];





$vendor_name=$row_inter['vendor_name'];

switch ($vendor_name) {
    case 'EFACTOR':
        $brandname = 'efactor';
        break;
    case 'BAJAJ':
        $brandname = 'Bajaj';
        break;
    case 'DHRIO':
        $brandname = 'dhrio';
        break;
    case 'Invendis':
        $brandname = 'Invendis';
        break;
    case 'Skytec':
        $brandname = 'Skytec';
        break;
    case 'exhaust_hood':
        $brandname = 'exhaust';
        break;
    default:
        $brandname = 'efactor';
        break;
}

/*

// Determine device image
        if ($model_name == 'EF-003FMN03' || $model_name == 'EF-002FMN02' || $model_name == 'EF-001FMN01' || $model_name == 'BAJAJ-001FMN01') {
            $deviceimage = 'fan-EF-003FMN03.jpg';
        } elseif ($model_name == 'EFCKER V1.0') {
            $deviceimage = 'efact_cooker.jpg';
        } elseif ($model_name == 'EF-001LIGHT01' || $model_name == 'EF-002CWLIGHT02' || $model_name == 'EF-001RGBLIGHT01' || $model_name == 'EF-003WLIGHT03') {
            $deviceimage = 'light_org.jpg';
        } elseif ($model_name == 'EF-001OULT01' || $model_name == 'EF-001SOCKET_6A01' || $model_name == 'EF-001SOCKET_10A01') {
            $deviceimage = 'outlet.jpg';
        } elseif ($model_name == 'EFSWPCP V1.0') {
            $deviceimage = 'swpcp.jpg';
        } elseif ($model_name == 'INVENDIS_MFM_V1') {
            $deviceimage = 'invendis_device.jpg';
        }

elseif ($model_name == 'EF_RF_GATEW V1.0') {
            $deviceimage = 'gateway_org.jpg';
        }

else {
            $deviceimage = 'input_output.jpg';
        }


 $device_image = "https://efactor.tech/files/brand_logo/" . $deviceimage;

*/
$new_prefix = "EWIFI";
$parts = explode("_", $curr_devicetype, 2);
$access_point = $new_prefix . "_" . $parts[1];
//echo $updated_devicetype; 


//$type="efactor.device.$devicetype";








// Initialize devices array
$devices = [];

if ($add_gateway == 'NULL') {








if($model_name == 'EFDEFLT V1.0'){
    $query_node="SELECT * FROM efactorproduct where node_id='$node_id'";
}else{
$query_node="SELECT * FROM `$model_name` where node_id='$node_id'";
}
			$doctors_node = $db->get_results($query_node); 
			
                        $dbcker = $db->get_row($query_node);    
                       // print_r($dbcker);
		        $nodeid=$dbcker['node_id'];
                        $user_id=$dbcker['user_id'];
                        $curr_device_type=$dbcker['current_devicetype'];
                         $devicetype=$dbcker['device_type'];
                        $mortgage=$dbcker['Mortgage_page'];
                           $status=$dbcker['status_page'];
                           $device_comm_page=$dbcker['device_comm_page'];
                           $remote_pair_pairing=$dbcker['Remote_Pair_Pairing'];
                           $config_config=$dbcker['Configure_Config'];
                           $remote_address=$dbcker['Remote_Address'];


                          
    $type="efactor.device.$devicetype"; 



/*-----------------------------------------------*/

// Determine device image
        if ($model_name == 'EF-003FMN03' || $model_name == 'EF-002FMN02' || $model_name == 'EF-001FMN01' || $model_name == 'BAJAJ-001FMN01') {
            $deviceimage = 'fan-EF-003FMN03.jpg';
        } elseif ($model_name == 'EFCKER V1.0') {
            $deviceimage = 'efact_cooker.jpg';
        } elseif ($model_name == 'EF-001LIGHT01' || $model_name == 'EF-002CWLIGHT02' || $model_name == 'EF-001RGBLIGHT01' || $model_name == 'EF-003WLIGHT03') {
            $deviceimage = 'light_org.jpg';
        } elseif ($model_name == 'EF-001OULT01' || $model_name == 'EF-001SOCKET_6A01' || $model_name == 'EF-001SOCKET_10A01') {
            $deviceimage = 'outlet.jpg';
        } elseif ($model_name == 'EFSWPCP V1.0') {
            $deviceimage = 'swpcp.jpg';
        } elseif ($model_name == 'INVENDIS_MFM_V1') {
            $deviceimage = 'invendis_device.jpg';
        }

elseif ($model_name == 'EF_RF_GATEW V1.0') {
            $deviceimage = 'gateway_org.jpg';
        }




else {
            $deviceimage = 'input_output.jpg';
        }


 $device_image = "https://efactor.tech/files/brand_logo/" . $deviceimage;



/*-----------------------------------------------*/


                        
                        
//$query = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND user_id = '177'";
$query = "SELECT * FROM product_api WHERE model_name = '$model_name'";

$result = $db->get_results($query);
$num = count($result); // Count the number of rows in the results
$row = $result; // Get the first row of the results


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

        



      //   EFDEFLT V1.0
   
$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFDEFLT V1.1'";
$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";
//$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";

$result_1 = $db->get_results($query_1); 
$result_2 = $db->get_results($query_2); 

$result_3 = $db->num_rows($query_1);
$result_4 = $db->num_rows($query_2);

$result_5 = $db->get_row($query_1); 
$result_6 = $db->get_row($query_2); 

$doctors = array_merge($result_1, $result_2);
$num_11 = $result_3 + $result_4;
$row = array_merge($result_1, $result_2);
$parameter_name = $row['parameter_name'];
  
$result = [];

foreach ($doctors as $param) {
   
    $fields_cmd = [];
    $fields_resp = [];
    $fields_error = [];

$param1=$param['parameter_name'];

//echo $param1;
 
//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFFN02' AND user_id = '177' AND parameter_name='$param1'";
$query_param1 = "SELECT * FROM product_api WHERE model_name = 'EFDEFLT V1.1' AND parameter_name='$param1'";
$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
  //$query_param = "SELECT * FROM product_api WHERE model_name = 'EFACTDFLT' AND parameter_name='$param1'";

 //$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND parameter_name='$param1'";

$result_1 = $db->get_results($query_param1); 
$result_2 = $db->get_results($query_param); 

$result_3 = $db->num_rows($query_param1);
$result_4 = $db->num_rows($query_param);

$row_param1 = $db->get_row($query_param1); 
$row_param2 = $db->get_row($query_param); 
  

$params = array_merge($result_1, $result_2);
$num_param = $result_3 + $result_4;
$row_param = array_merge((array)$row_param1, (array)$row_param2);
 
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
            $cmd_sno_asperui = explode(',', $row_param['cmd_sno_asperui']);
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
            $resp_sno_asperui = explode(',', $row_param['resp_sno_asperui']);
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
            $error_sno_asperui = explode(',', $row_param['error_sno_asperui']);
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
 
        $cmd_multipleui = $row_param['cmd_multipleui'];
        $resp_multipleui = $row_param['resp_multipleui'];
        $error_multipleui = $row_param['error_multipleui'];


        $cmd_triggerui = $row_param['cmd_triggerui'];
        $resp_triggerui = $row_param['resp_triggerui'];
        $error_triggerui = $row_param['error_triggerui'];

      
        $selected = $row_param['selected'];



//if($page_names == 'Configure' || $page_names == 'Remote Pair')
 if($page_names == 'Configure' || $page_names == 'Remote Pair' || $page_names == 'Configure.Config' || $page_names == 'Set Max Current' || $page_names == 'Device Setting'){

if($parameter_name == 'wifi config' || $parameter_name == 'DAC')
{
$local_control ='disable';       //disable

}
else{
 $local_control ='enable';       //disable

}

}else{

$local_control ='enable';
}



if ($param1 != 'PUSH') {



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
            "local_control" => $local_control,
            "ui_ids"   => (int)$cmd_uiid[$i],
            "ui_description" => $cmd_uidescriptioninout[$i]
            ];


//$fields_cmd[] = $field_cmd;



          }elseif($cmd_uitype[$i] == 'None'){ 
            $field_cmd = [
               "default_name" =>$cmd_defaultvalue[$i],
               "value" => $cmd_valueinhexmin[$i],
               "Field_description" => $cmd_fielddescription[$i],
               "no_of_bytes" => (int)$cmd_noofbytes[$i],
               "byte_start_position" => (int)$cmd_startbytePosition[$i] + 27,
               "ui_type"  => $cmd_uitype[$i],
               "local_control" => $local_control
        ]; 

      }else{




        
$as_per_ui = $cmd_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

if (empty($as_per_ui_array_from_formatted) || $as_per_ui_array_from_formatted === ['']) {
    $as_per_ui_array_from_formatted = [];  
}

 

$sno_as_per_ui = $cmd_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
}

$asperui_values = $cmd_asperui_values[$i];
$asperui_values_array = explode('][', trim($asperui_values, '[]'));
$formatted_asperui_values = implode(',', $asperui_values_array);
$asperui_values_array_from_formatted = explode(',', $formatted_asperui_values);
 
if (empty($asperui_values_array_from_formatted) || $asperui_values_array_from_formatted === ['']) {
    $asperui_values_array_from_formatted = [];  
}

 
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($cmd_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }
 
if($cmd_uitype[$i] === 'Toggle Switch' || $cmd_uitype[$i] === 'Notification'){
// print_r($as_per_ui);
 //print_r($asperui_values);
//print_r($as_per_ui_value);
$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';    
    }elseif ($cmd_uitype[$i] === 'Slider' || $cmd_uitype[$i] === 'Hue Slider'  || $cmd_uitype[$i] === 'Hue Slider Custom'  || $cmd_uitype[$i] === 'Brightness Slider'  || $cmd_uitype[$i] === 'Airflow Slider') {
     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
        } elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);     
            }else{ 
                $integervalue = "null";  
            }
        }
    
    }elseif ($cmd_uitype[$i] === 'Drop Down') {


     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexdec($as_per_ui_value);  
               //print_r($sno_as_per_ui);
               //echo $as_per_ui_value;
 

$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

$ui_toggle_drop = explode('][', trim($asperui_values, '[]')); 
$ui_toggle_value_drop = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map_drop = array_combine($ui_toggle_value_drop,$ui_toggle_drop);

$as_per_ui_value =isset($dropdown_map_drop[$as_per_ui_value]) ? $dropdown_map_drop[$as_per_ui_value] : '';

  
  
            }else{ 
                $integervalue = "null";  
            }
        } elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);    
            }else{ 
                $integervalue = "null";  
            }
        }
    
    }elseif ($cmd_uitype[$i]  === 'Text') {
     
        if ($cmd_datatype[$i] === 'String') {
                if($as_per_ui_value !== 'NULL'){
                  //  $integervalue = $as_per_ui_value;

           //   if($as_per_ui != 'NULL')
                 if(!empty($as_per_ui))
                   {  


                   if($cmd_defaultvalue[$i] == 'name')
                      {
                         
                        $integervalue=$as_per_ui_value;
                      }

                      else{




                     $ui_toggle = explode('][', trim($as_per_ui, '[]'));
                     $ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
                     $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                     $as_per_ui_value = (string)$as_per_ui_value;  
                     $as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;
                     $integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

                      }



                   }
                   else{
                    $integervalue = $as_per_ui_value;
                   }




                        
                }else{
                    $integervalue = "null";  
            }  
         }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
         }elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
              //  $integervalue = hexToFloat($as_per_ui_value);  
                 $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);   
            }else{ 
                $integervalue = "null";  
            }
         }else{
            if($as_per_ui_value !== 'NULL'){
                $integervalue = $as_per_ui_value;  
            }else{
                $integervalue = "null";  
            }
         }
    
    }else{
        if($as_per_ui_value !== 'NULL'){
            $integervalue = $as_per_ui_value;  
        }else{
            $integervalue = "null";  
        }
    }
 


    if ($cmd_datatype[$i] === 'Float') {
        // $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
         // $integervalue = hexToFloat($integer_value);
         // $integervalue = empty($integer_value) ? (float)$integer_value :  0.0;
         $cmd_value2 = floatToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
         $cmd_sliderstep = (float)$cmd_sliderstepvalue[$i];
        $display_min = (float)$cmd_displayvaluemin[$i];
        $display_max = (float)$cmd_displayvaluemax[$i];
     }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
        if($cmd_uitype[$i] == 'Toggle Switch'){
            $ui_toggle2 = explode('][', trim($as_per_ui, '[]'));
    $ui_toggle_value2 = explode('][', trim($asperui_values, '[]'));
            $dropdown_map22 = array_combine($ui_toggle2,$ui_toggle_value2);  
            $cmd_value = isset($dropdown_map22[$integervalue]) ? $dropdown_map22[$integervalue] : '';  
            //print_r($cmd_value);
            $display_min = (int)$cmd_displayvaluemin[$i];
            $display_max = (int)$cmd_displayvaluemax[$i];
        }else{
      //  $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
      //   $integervalue = hexToInt($integer_value);
         $cmd_value2 = intToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
        $cmd_sliderstep = (int)$cmd_sliderstepvalue[$i];
        $display_min = (int)$cmd_displayvaluemin[$i];
        $display_max = (int)$cmd_displayvaluemax[$i];
        }
    }elseif ($cmd_datatype[$i] === 'String') {
      // $integervalue = (string)$integer_value;
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
    "value" => $as_per_ui_value,
    "Field_description" => $cmd_fielddescription[$i],
    "type" => $cmd_valuedefinition[$i],
    "data_type" => $cmd_datatype[$i],
    "no_of_bytes" => (int)$cmd_noofbytes[$i],
    "byte_start_position" => (int)$cmd_startbytePosition[$i],
    "ui_type"  => $cmd_uitype[$i],
    "local_control" => $local_control,
    "ui_ids"   => (int)$cmd_uiid[$i],
    "asperui" => $as_per_ui_array_from_formatted,  
    "sno_asperui" => $sno_as_per_ui_array_from_formatted,  
    "asperui_values" => $asperui_values_array_from_formatted,
    "ui_description" => $cmd_uidescriptioninout[$i],
    "slider_step_value" => $cmd_sliderstep
];
          	
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
            "local_control" => $local_control
                     
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
            "local_control" => $local_control,
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
                    "local_control" => $local_control,
                    "ui_ids" => (int)$resp_uiId[$i],
                    "ui_description" => $resp_uiDescriptionInOut[$i]
                ];
            } else {
                


$resp_value = floatToHex($resp_asui_val[$i]);
$as_per_ui = $resp_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

if (empty($as_per_ui_array_from_formatted) || $as_per_ui_array_from_formatted === ['']) {
    $as_per_ui_array_from_formatted = [];  
}

$sno_as_per_ui = $resp_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
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
 
 
   
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($resp_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }



if(($resp_uiType[$i] === 'Toggle Switch') || ($resp_uiType[$i] === 'Notification')) {
// print_r($as_per_ui);
 //print_r($asperui_values);
//print_r($as_per_ui_value);
$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);
  
$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';  
    
    }elseif ($resp_uiType[$i] === 'Slider' || $resp_uiType[$i] === 'Hue Slider'  || $resp_uiType[$i] === 'Hue Slider Custom'  || $resp_uiType[$i] === 'Brightness Slider'  || $resp_uiType[$i] === 'Airflow Slider') {


     
        if ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i] === 'Drop Down') {

     
        if ($resp_dataType[$i] === 'UnsignedInt') {

            if($as_per_ui_value !== 'NULL'){
               
$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

$ui_toggle_drop = explode('][', trim($asperui_values, '[]')); 
$ui_toggle_value_drop = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map_drop = array_combine($ui_toggle_value_drop,$ui_toggle_drop);

$resp_value =isset($dropdown_map_drop[$as_per_ui_value]) ? $dropdown_map_drop[$as_per_ui_value] : '';

  
            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $resp_integervalue= sprintf("%.2f", $cmdfloat_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i]  === 'Text') {
     
        if ($resp_dataType[$i] === 'String') {
                if(!empty($as_per_ui_value)){
                  //  $resp_integervalue = $as_per_ui_value;



                 // if($as_per_ui != 'NULL')
                    if(!empty($as_per_ui))
                   {  
                     $ui_toggle = explode('][', trim($as_per_ui, '[]'));
                     $ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
                     $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                     $as_per_ui_value = (string)$as_per_ui_value;  
                     $as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;
                     $resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 
                   }
                   else{
                    $resp_integervalue = $as_per_ui_value;
                   }









                }else{
                    $resp_integervalue = "null";  
            }  
         }elseif ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
         }elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
         }else{
            if(!empty($as_per_ui_value)){
                $resp_integervalue = $as_per_ui_value;  
            }else{
                $resp_integervalue = "null";  
            }
         }
    
    }else{
        if(!empty($as_per_ui_value)){
            $resp_integervalue = $as_per_ui_value;  
        }else{
            $resp_integervalue = "null";  
        }
    }
   
  
    if ($resp_dataType[$i] === 'Float') {
        //$asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (float)$integer_value;
        $resp_value2 = floatToHex($resp_integervalue);
         $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderstep = (float)$resp_sliderstepvalue[$i];
    } elseif ($resp_dataType[$i] === 'UnsignedInt') {
       // $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (int)$integer_value;

 if($resp_uiType[$i] != 'Drop Down')
         {  

         $resp_value2 = intToHex($resp_integervalue);
          $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderStep = (int)$resp_sliderStepValue[$i];
       }
    }elseif($resp_dataType[$i] === 'String'){
     // $respintegervalue = (string)$integer_value;
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
                    "local_control" => $local_control,
                    "ui_ids" => (int)$resp_uiId[$i],
                    "asperui" => $as_per_ui_array_from_formatted,
                    "sno_asperui" => $sno_as_per_ui_array_from_formatted,
                    "asperui_values" => $asperui_values_array_from_formatted,
                    "ui_description" => $resp_uiDescriptionInOut[$i],
                    "slider_step_value" => $resp_sliderStep
                ];

            
          

}
            $fields_resp[] = $field_resp;
        } 









else {


if($resp_uiType[$i] == 'None')
{
            $field_resp = [
                "default_name" => $resp_defaultvalue[$i],
                "value" => $resp_valueInHexMin[$i],
                "Field_description" => $resp_fieldDescription[$i],
                "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                "byte_start_position" => (int)$resp_startBytePosition[$i],
                "ui_type" => $resp_uiType[$i],
                "local_control" => $local_control
              
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
                "local_control" => $local_control,
                "ui_description" => $resp_uiDescriptionInOut[$i],
               "ui_ids" => (int)$resp_uiId[$i] 
            ];
            $fields_resp[] = $field_resp;




}
        }
    }
}




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

   

$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($error_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }


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
            "local_control" => $local_control,
            "ui_ids"   => (int) $error_uiId[$i],
             "asperui" => $as_per_ui_array_from_formatted,
            "asperui_values" => $asperui_values_array_converted,
            "ui_description" => $error_uiDescriptionInOut[$i],
            "slider_step_value"=> $error_sliderStepValue[$i]
        ];

       
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


        $fields_error[] = $field_error;
    }


}


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

unset($para_result['']);



/*
if($model_name == 'EF-003FMN03')
{

// Remove the unwanted pages after processing
//unset($para_result['Device Communication']);
//unset($para_result['Status']);



unset($para_result['Configure.Config']);
unset($para_result['Remote Address']);
unset($para_result['Remote Pair.Pairing']);

}
 //02-06-25

$para_default_result = $result_default;


*/



if ($mortgage === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Mortgage') {
 unset($para_result[$key]);
                   }
    }
}

if ($status === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Status') {
 unset($para_result[$key]);
                   }
    }
}




if ($device_comm_page === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Device Communication') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_pair_pairing === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Pair.Pairing') {
 unset($para_result[$key]);
                   }
    }
}


if ($config_config === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Configure.Config') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_address === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Address') {
 unset($para_result[$key]);
                   }
    }
}




/**************************/





$attributes = [
    [
        "name" => "Device Id",
        "value" => $node_id
    ],
    [
        "name" => "mac",
        "value" => $mac_value
    ]
];






  $query_unread = "SELECT COUNT(*) AS `unread_count` FROM call_service WHERE node_id = '$nodeid' AND status = 'unread'";
$row_unread = $db->get_row($query_unread);

// Convert object to array and access key safely
$unread_count = '0';
if ($row_unread) {
    foreach ($row_unread as $key => $val) {
        if (trim($key) === 'unread_count') {
            $unread_count = trim((string)$val); // Ensure it's a trimmed string
        }
    }
}


if ($unread_count === '' || $unread_count === null) {
    $unread_count = '0';
}









    $devices = [
        [
            "name" => $curr_device_type,
            "total_pages" => $number_of_pages,
            "attributes" => $attributes,
            "Device Id" => $node_id,
            "pages" => $para_result,               
            "primary" => "power",
            "type" => $type,
            "brand_logo" => "https://efactor.tech/files/brand_logo/" . $brandname . ".jpg",
            "device_image" => $device_image,
            "device_p2p_control"=>"disable",
            "chat_status"=>$unread_count,
                "Device Communication"=>"disable",
                "Status"=>"disable"

        ]
    ];
} else {



    if ($is_added == 1) {


        $query_gateway = "SELECT * FROM `efactorproduct` WHERE add_gateway ='$add_gateway' AND is_added='$is_added'"; 
        $single_node_dynamic_gateway = $db->get_results($query_gateway);
        $row_node_gateway = $db->get_row($query_gateway);
        $node_id = $row_node_gateway["node_id"];
   
        $module_type_gate = $row_node_gateway["module_type"];

        $is_added_gateway=$row_node_gateway["is_added"];

        $query_gateway_11 = "SELECT node_id FROM `efactorproduct` WHERE add_gateway ='$add_gateway' AND access_token='$val'"; 
        $all_devices_dynamic = $db->get_results($query_gateway_11);



        foreach ($all_devices_dynamic as $node_gate) {



            $nodeid = $node_gate["node_id"];

            $query_node = "SELECT * FROM efactorproduct WHERE node_id='$nodeid'";
            $single_node_dynamic = $db->get_results($query_node);
            $row_node = $db->get_row($query_node);

            $model_name = $row_node['model_number'];
            $current_devicetype = $row_node['current_devicetype'];
            $module_type = $row_node['module_type'];
            $device_type = $row_node['device_type'];
            $nett_conn = $row_node['internet'];
            $access_token = $row_node['access_token'];
            $vendor_name = $row_node['vendor_name'];
            $add_gateway = $row_node['add_gateway'];
            $is_added = $row_node['is_added'];
            $userid = $row_node['user_id'];

            $node_id = $row_node['node_id'];






if($model_name == 'EFDEFLT V1.0'){
    $query_node="SELECT * FROM efactorproduct where node_id='$node_id'";
}else{
$query_node="SELECT * FROM `$model_name` where node_id='$node_id'";
}
			$doctors_node = $db->get_results($query_node); 
			
                        $dbcker = $db->get_row($query_node);    
                       // print_r($dbcker);
		        $nodeid=$dbcker['node_id'];
                        $user_id=$dbcker['user_id'];
                        $curr_device_type=$dbcker['current_devicetype'];
                        $devicetype=$dbcker['device_type'];
                        $mortgage=$dbcker['Mortgage_page'];
                           $status=$dbcker['status_page'];
                           $device_comm_page=$dbcker['device_comm_page'];
                           $remote_pair_pairing=$dbcker['Remote_Pair_Pairing'];
                           $config_config=$dbcker['Configure_Config'];
                           $remote_address=$dbcker['Remote_Address'];


 $type="efactor.device.$devicetype"; 


/*-----------------------------------------------*/

// Determine device image
        if ($model_name == 'EF-003FMN03' || $model_name == 'EF-002FMN02' || $model_name == 'EF-001FMN01' || $model_name == 'BAJAJ-001FMN01') {
            $deviceimage = 'fan-EF-003FMN03.jpg';
        } elseif ($model_name == 'EFCKER V1.0') {
            $deviceimage = 'efact_cooker.jpg';
        } elseif ($model_name == 'EF-001LIGHT01' || $model_name == 'EF-002CWLIGHT02' || $model_name == 'EF-001RGBLIGHT01' || $model_name == 'EF-003WLIGHT03') {
            $deviceimage = 'light_org.jpg';
        } elseif ($model_name == 'EF-001OULT01' || $model_name == 'EF-001SOCKET_6A01' || $model_name == 'EF-001SOCKET_10A01') {
            $deviceimage = 'outlet.jpg';
        } elseif ($model_name == 'EFSWPCP V1.0') {
            $deviceimage = 'swpcp.jpg';
        } elseif ($model_name == 'INVENDIS_MFM_V1') {
            $deviceimage = 'invendis_device.jpg';
        }

elseif ($model_name == 'EF_RF_GATEW V1.0') {
            $deviceimage = 'gateway_org.jpg';
        }




else {
            $deviceimage = 'input_output.jpg';
        }


 $device_image = "https://efactor.tech/files/brand_logo/" . $deviceimage;



/*-----------------------------------------------*/


                        
//$query = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND user_id = '177'";
$query = "SELECT * FROM product_api WHERE model_name = '$model_name'";

$result = $db->get_results($query);
$num = count($result); // Count the number of rows in the results
$row = $result; // Get the first row of the results


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

        



      //   EFDEFLT V1.0
   
$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFDEFLT V1.1'";
$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";
//$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";

$result_1 = $db->get_results($query_1); 
$result_2 = $db->get_results($query_2); 

$result_3 = $db->num_rows($query_1);
$result_4 = $db->num_rows($query_2);

$result_5 = $db->get_row($query_1); 
$result_6 = $db->get_row($query_2); 

$doctors = array_merge($result_1, $result_2);
$num_11 = $result_3 + $result_4;
$row = array_merge($result_1, $result_2);
$parameter_name = $row['parameter_name'];
  
$result = [];

foreach ($doctors as $param) {
   
    $fields_cmd = [];
    $fields_resp = [];
    $fields_error = [];

$param1=$param['parameter_name'];

//echo $param1;
 
//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFFN02' AND user_id = '177' AND parameter_name='$param1'";
$query_param1 = "SELECT * FROM product_api WHERE model_name = 'EFDEFLT V1.1' AND parameter_name='$param1'";
$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
  //$query_param = "SELECT * FROM product_api WHERE model_name = 'EFACTDFLT' AND parameter_name='$param1'";

 //$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND parameter_name='$param1'";

$result_1 = $db->get_results($query_param1); 
$result_2 = $db->get_results($query_param); 

$result_3 = $db->num_rows($query_param1);
$result_4 = $db->num_rows($query_param);

$row_param1 = $db->get_row($query_param1); 
$row_param2 = $db->get_row($query_param); 
  

$params = array_merge($result_1, $result_2);
$num_param = $result_3 + $result_4;
$row_param = array_merge((array)$row_param1, (array)$row_param2);
 
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
            $cmd_sno_asperui = explode(',', $row_param['cmd_sno_asperui']);
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
            $resp_sno_asperui = explode(',', $row_param['resp_sno_asperui']);
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
            $error_sno_asperui = explode(',', $row_param['error_sno_asperui']);
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
 
        $cmd_multipleui = $row_param['cmd_multipleui'];
        $resp_multipleui = $row_param['resp_multipleui'];
        $error_multipleui = $row_param['error_multipleui'];


        $cmd_triggerui = $row_param['cmd_triggerui'];
        $resp_triggerui = $row_param['resp_triggerui'];
        $error_triggerui = $row_param['error_triggerui'];

      
        $selected = $row_param['selected'];



//if($page_names == 'Configure' || $page_names == 'Remote Pair')
 if($page_names == 'Configure' || $page_names == 'Remote Pair' || $page_names == 'Configure.Config' || $page_names == 'Set Max Current' || $page_names == 'Device Setting'){

if($parameter_name == 'wifi config' || $parameter_name == 'DAC')
{
$local_control ='disable';       //disable

}
else{
 $local_control ='enable';       //disable

}

}else{

$local_control ='enable';
}



if ($param1 != 'PUSH') {



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
            "local_control" => $local_control,
            "ui_ids"   => (int)$cmd_uiid[$i],
            "ui_description" => $cmd_uidescriptioninout[$i]
            ];


//$fields_cmd[] = $field_cmd;



          }elseif($cmd_uitype[$i] == 'None'){ 
            $field_cmd = [
               "default_name" =>$cmd_defaultvalue[$i],
               "value" => $cmd_valueinhexmin[$i],
               "Field_description" => $cmd_fielddescription[$i],
               "no_of_bytes" => (int)$cmd_noofbytes[$i],
               "byte_start_position" => (int)$cmd_startbytePosition[$i] + 27,
               "ui_type"  => $cmd_uitype[$i],
               "local_control" => $local_control
        ]; 

      }else{




        
$as_per_ui = $cmd_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

if (empty($as_per_ui_array_from_formatted) || $as_per_ui_array_from_formatted === ['']) {
    $as_per_ui_array_from_formatted = [];  
}

 

$sno_as_per_ui = $cmd_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
}

$asperui_values = $cmd_asperui_values[$i];
$asperui_values_array = explode('][', trim($asperui_values, '[]'));
$formatted_asperui_values = implode(',', $asperui_values_array);
$asperui_values_array_from_formatted = explode(',', $formatted_asperui_values);
 
if (empty($asperui_values_array_from_formatted) || $asperui_values_array_from_formatted === ['']) {
    $asperui_values_array_from_formatted = [];  
}

 
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($cmd_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }
 
if($cmd_uitype[$i] === 'Toggle Switch' || $cmd_uitype[$i] === 'Notification'){
// print_r($as_per_ui);
 //print_r($asperui_values);
//print_r($as_per_ui_value);
$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';    
    }elseif ($cmd_uitype[$i] === 'Slider' || $cmd_uitype[$i] === 'Hue Slider'  || $cmd_uitype[$i] === 'Hue Slider Custom'  || $cmd_uitype[$i] === 'Brightness Slider'  || $cmd_uitype[$i] === 'Airflow Slider') {
     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
        } elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);     
            }else{ 
                $integervalue = "null";  
            }
        }
    
    }elseif ($cmd_uitype[$i] === 'Drop Down') {


     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexdec($as_per_ui_value);  
               //print_r($sno_as_per_ui);
               //echo $as_per_ui_value;
 

$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

$ui_toggle_drop = explode('][', trim($asperui_values, '[]')); 
$ui_toggle_value_drop = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map_drop = array_combine($ui_toggle_value_drop,$ui_toggle_drop);

$as_per_ui_value =isset($dropdown_map_drop[$as_per_ui_value]) ? $dropdown_map_drop[$as_per_ui_value] : '';

  
  
            }else{ 
                $integervalue = "null";  
            }
        } elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);    
            }else{ 
                $integervalue = "null";  
            }
        }
    
    }elseif ($cmd_uitype[$i]  === 'Text') {
     
        if ($cmd_datatype[$i] === 'String') {
                if($as_per_ui_value !== 'NULL'){
                  //  $integervalue = $as_per_ui_value;

           //   if($as_per_ui != 'NULL')
                 if(!empty($as_per_ui))
                   {  
                     $ui_toggle = explode('][', trim($as_per_ui, '[]'));
                     $ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
                     $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                     $as_per_ui_value = (string)$as_per_ui_value;  
                     $as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;
                     $integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 
                   }
                   else{
                    $integervalue = $as_per_ui_value;
                   }




                        
                }else{
                    $integervalue = "null";  
            }  
         }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
         }elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
              //  $integervalue = hexToFloat($as_per_ui_value);  
                 $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);   
            }else{ 
                $integervalue = "null";  
            }
         }else{
            if($as_per_ui_value !== 'NULL'){
                $integervalue = $as_per_ui_value;  
            }else{
                $integervalue = "null";  
            }
         }
    
    }else{
        if($as_per_ui_value !== 'NULL'){
            $integervalue = $as_per_ui_value;  
        }else{
            $integervalue = "null";  
        }
    }
 


    if ($cmd_datatype[$i] === 'Float') {
        // $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
         // $integervalue = hexToFloat($integer_value);
         // $integervalue = empty($integer_value) ? (float)$integer_value :  0.0;
         $cmd_value2 = floatToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
         $cmd_sliderstep = (float)$cmd_sliderstepvalue[$i];
        $display_min = (float)$cmd_displayvaluemin[$i];
        $display_max = (float)$cmd_displayvaluemax[$i];
     }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
        if($cmd_uitype[$i] == 'Toggle Switch'){
            $ui_toggle2 = explode('][', trim($as_per_ui, '[]'));
    $ui_toggle_value2 = explode('][', trim($asperui_values, '[]'));
            $dropdown_map22 = array_combine($ui_toggle2,$ui_toggle_value2);  
            $cmd_value = isset($dropdown_map22[$integervalue]) ? $dropdown_map22[$integervalue] : '';  
            //print_r($cmd_value);
            $display_min = (int)$cmd_displayvaluemin[$i];
            $display_max = (int)$cmd_displayvaluemax[$i];
        }else{
      //  $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
      //   $integervalue = hexToInt($integer_value);
         $cmd_value2 = intToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
        $cmd_sliderstep = (int)$cmd_sliderstepvalue[$i];
        $display_min = (int)$cmd_displayvaluemin[$i];
        $display_max = (int)$cmd_displayvaluemax[$i];
        }
    }elseif ($cmd_datatype[$i] === 'String') {
      // $integervalue = (string)$integer_value;
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
    "value" => $as_per_ui_value,
    "Field_description" => $cmd_fielddescription[$i],
    "type" => $cmd_valuedefinition[$i],
    "data_type" => $cmd_datatype[$i],
    "no_of_bytes" => (int)$cmd_noofbytes[$i],
    "byte_start_position" => (int)$cmd_startbytePosition[$i],
    "ui_type"  => $cmd_uitype[$i],
    "local_control" => $local_control,
    "ui_ids"   => (int)$cmd_uiid[$i],
    "asperui" => $as_per_ui_array_from_formatted,  
    "sno_asperui" => $sno_as_per_ui_array_from_formatted,  
    "asperui_values" => $asperui_values_array_from_formatted,
    "ui_description" => $cmd_uidescriptioninout[$i],
    "slider_step_value" => $cmd_sliderstep
];
          	
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
            "local_control" => $local_control
                     
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
            "local_control" => $local_control,
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
                    "local_control" => $local_control,
                    "ui_ids" => (int)$resp_uiId[$i],
                    "ui_description" => $resp_uiDescriptionInOut[$i]
                ];
            } else {
                


$resp_value = floatToHex($resp_asui_val[$i]);
$as_per_ui = $resp_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

if (empty($as_per_ui_array_from_formatted) || $as_per_ui_array_from_formatted === ['']) {
    $as_per_ui_array_from_formatted = [];  
}

$sno_as_per_ui = $resp_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
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
 
 
   
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($resp_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }



if(($resp_uiType[$i] === 'Toggle Switch') || ($resp_uiType[$i] === 'Notification')) {
// print_r($as_per_ui);
 //print_r($asperui_values);
//print_r($as_per_ui_value);
$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);
  
$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';  
    
    }elseif ($resp_uiType[$i] === 'Slider' || $resp_uiType[$i] === 'Hue Slider'  || $resp_uiType[$i] === 'Hue Slider Custom'  || $resp_uiType[$i] === 'Brightness Slider'  || $resp_uiType[$i] === 'Airflow Slider') {


     
        if ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i] === 'Drop Down') {

     
        if ($resp_dataType[$i] === 'UnsignedInt') {

            if($as_per_ui_value !== 'NULL'){
               
$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

$ui_toggle_drop = explode('][', trim($asperui_values, '[]')); 
$ui_toggle_value_drop = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map_drop = array_combine($ui_toggle_value_drop,$ui_toggle_drop);

$resp_value =isset($dropdown_map_drop[$as_per_ui_value]) ? $dropdown_map_drop[$as_per_ui_value] : '';

  
            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $resp_integervalue= sprintf("%.2f", $cmdfloat_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i]  === 'Text') {
     
        if ($resp_dataType[$i] === 'String') {
                if(!empty($as_per_ui_value)){
                  //  $resp_integervalue = $as_per_ui_value;



                 // if($as_per_ui != 'NULL')
                    if(!empty($as_per_ui))
                   {  
                     $ui_toggle = explode('][', trim($as_per_ui, '[]'));
                     $ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
                     $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                     $as_per_ui_value = (string)$as_per_ui_value;  
                     $as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;
                     $resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 
                   }
                   else{
                    $resp_integervalue = $as_per_ui_value;
                   }









                }else{
                    $resp_integervalue = "null";  
            }  
         }elseif ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
         }elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
         }else{
            if(!empty($as_per_ui_value)){
                $resp_integervalue = $as_per_ui_value;  
            }else{
                $resp_integervalue = "null";  
            }
         }
    
    }else{
        if(!empty($as_per_ui_value)){
            $resp_integervalue = $as_per_ui_value;  
        }else{
            $resp_integervalue = "null";  
        }
    }
   
  
    if ($resp_dataType[$i] === 'Float') {
        //$asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (float)$integer_value;
        $resp_value2 = floatToHex($resp_integervalue);
         $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderstep = (float)$resp_sliderstepvalue[$i];
    } elseif ($resp_dataType[$i] === 'UnsignedInt') {
       // $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (int)$integer_value;

 if($resp_uiType[$i] != 'Drop Down')
         {  

         $resp_value2 = intToHex($resp_integervalue);
          $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderStep = (int)$resp_sliderStepValue[$i];
       }
    }elseif($resp_dataType[$i] === 'String'){
     // $respintegervalue = (string)$integer_value;
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
                    "local_control" => $local_control,
                    "ui_ids" => (int)$resp_uiId[$i],
                    "asperui" => $as_per_ui_array_from_formatted,
                    "sno_asperui" => $sno_as_per_ui_array_from_formatted,
                    "asperui_values" => $asperui_values_array_from_formatted,
                    "ui_description" => $resp_uiDescriptionInOut[$i],
                    "slider_step_value" => $resp_sliderStep
                ];

            
          

}
            $fields_resp[] = $field_resp;
        } 









else {


if($resp_uiType[$i] == 'None')
{
            $field_resp = [
                "default_name" => $resp_defaultvalue[$i],
                "value" => $resp_valueInHexMin[$i],
                "Field_description" => $resp_fieldDescription[$i],
                "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                "byte_start_position" => (int)$resp_startBytePosition[$i],
                "ui_type" => $resp_uiType[$i],
                "local_control" => $local_control
              
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
                "local_control" => $local_control,
                "ui_description" => $resp_uiDescriptionInOut[$i],
               "ui_ids" => (int)$resp_uiId[$i] 
            ];
            $fields_resp[] = $field_resp;




}
        }
    }
}




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

   

$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($error_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }


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
            "local_control" => $local_control,
            "ui_ids"   => (int) $error_uiId[$i],
             "asperui" => $as_per_ui_array_from_formatted,
            "asperui_values" => $asperui_values_array_converted,
            "ui_description" => $error_uiDescriptionInOut[$i],
            "slider_step_value"=> $error_sliderStepValue[$i]
        ];

       
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


        $fields_error[] = $field_error;
    }


}


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


unset($para_result['']);


/*
if($model_name == 'EF-003FMN03')
{

// Remove the unwanted pages after processing
//unset($para_result['Device Communication']);
//unset($para_result['Status']);



unset($para_result['Configure.Config']);
unset($para_result['Remote Address']);
unset($para_result['Remote Pair.Pairing']);

} //  02-06-25

*/


if ($mortgage === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Mortgage') {
 unset($para_result[$key]);
                   }
    }
}

if ($status === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Status') {
 unset($para_result[$key]);
                   }
    }
}




if ($device_comm_page === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Device Communication') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_pair_pairing === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Pair.Pairing') {
 unset($para_result[$key]);
                   }
    }
}


if ($config_config === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Configure.Config') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_address === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Address') {
 unset($para_result[$key]);
                   }
    }
}




/**************************/





$para_default_result = $result_default;














 $nodeid = $node_gate["node_id"];

            $query_node = "SELECT * FROM efactorproduct WHERE node_id='$nodeid'";
            $single_node_dynamic = $db->get_results($query_node);
            $row_node = $db->get_row($query_node);

            $model_name = $row_node['model_number'];
            $current_devicetype = $row_node['current_devicetype'];
            $module_type = $row_node['module_type'];
            $device_type = $row_node['device_type'];
            $nett_conn = $row_node['internet'];
            $access_token = $row_node['access_token'];
            $vendor_name = $row_node['vendor_name'];
            $add_gateway = $row_node['add_gateway'];
            $is_added = $row_node['is_added'];
            $userid = $row_node['user_id'];




  $query_unread = "SELECT COUNT(*) AS `unread_count` FROM call_service WHERE node_id = '$nodeid' AND status = 'unread'";
$row_unread = $db->get_row($query_unread);

// Convert object to array and access key safely
$unread_count = '0';
if ($row_unread) {
    foreach ($row_unread as $key => $val) {
        if (trim($key) === 'unread_count') {
            $unread_count = trim((string)$val); // Ensure it's a trimmed string
        }
    }
}


if ($unread_count === '' || $unread_count === null) {
    $unread_count = '0';
}



$attributes = [
    [
        "name" => "Device Id",
        "value" => $nodeid
    ],
    [
        "name" => "mac",
        "value" => $mac_value
    ]
];

            $devices_gate = [
                
                    "name" => $curr_device_type,
                    "total_pages" => $number_of_pages,
                    "attributes" => $attributes,
                    "Device Id" => $nodeid,
                    "pages" => $para_result,               
                    "primary" => "power",
                    "type" => $type,
                    "brand_logo" => "https://efactor.tech/files/brand_logo/" . $brandname . ".jpg",
                    "device_image" => $device_image,
                     "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
                     "chat_status"=>$unread_count,
                    "device_p2p_control"=>"disable",
                "Device Communication"=>"disable",
                "Status"=>"disable"

                
            ];




            $devices[] = $devices_gate; // Add the device to the devices array
        }
    }
}



$url_params = "model_name={$model_number}&user_id={$user_id}";
$auto_url_params = "model_name={$model_number}&user_id={$user_id}&node_id={$node_id}";



if($is_added_gateway != 'NULL')

{

$node_details = array(
    "id" => $add_gateway,
    
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
        "devices" => $devices,
        "info" => array(
            "fw_version" => "1.5.1",
            "model" => $model_number,
            "name" => $curr_devicetype,
            "type" => $moduletype,
            "SSID" => $ssid,
            "Access_Point" => $access_point,
            "Guarantee_Card" => "https://efactor.tech/v8/api.php?action=guarantee_card_display&{$url_params}",
            "Video" => "https://efactor.tech/v8/user_guide_support.php",
            "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
            "chat_status"=>$unread_count,
            "Auto_Test" => "https://efactor.tech/v8/auto_test.php?{$auto_url_params}"

        ),


        "node_id" => $add_gateway
    ),
    "role" => "primary",
    "status" => array(
        "connectivity" => array(
            "connected" => ($net_conn_1 === "true") ? true : false,
            "timestamp" => $timestamp
        )
    )
);

}

else{




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
        "devices" => $devices,
        "info" => array(
            "fw_version" => "1.5.1",
            "model" => $model_number,
            "name" => $curr_devicetype,
            "type" => $moduletype,
            "SSID" => $ssid,
            "Access_Point" => $access_point,
            "Guarantee_Card" => "https://efactor.tech/v8/api.php?action=guarantee_card_display&{$url_params}",
            "Video" => "https://efactor.tech/v8/user_guide_support.php",
            "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
            "chat_status"=>$unread_count,
            "Auto_Test" => "https://efactor.tech/v8/auto_test.php?{$auto_url_params}"

        ),


        "node_id" => $node_id
    ),
    "role" => "primary",
    "status" => array(
        "connectivity" => array(
            "connected" => ($net_conn_1 === "true") ? true : false,
            "timestamp" => $timestamp
        )
    )
);






}



if($is_added_gateway != 'NULL')

{
  $node_id = $add_gateway;
}

else
{
  $node_id = $node_id;
}






if($node_id)
{


$node = json_encode(array("url"=>"action=auto_test_details",
                      "token"=>$access_token,
                      "next_id"=>$nextid,
                      "total"=>$num_total,
                      "node_details"=>array($node_details),
                       "nodes"=>$node_id
                        ));

}

else{


$node = json_encode(array("url"=>"action=auto_test_details",
                      "token"=>$access_token,
                      "next_id"=>$nextid,
                      "total"=>$num_total,
                      "node_details"=>"",
                       "nodes"=>$node_id
                        ));



}

/*}

else{


header('HTTP/1.1 401');
        echo json_encode(['status' => 'failure', 'error_code' => '100002', 'description' => 'Access token missing']);
        exit;




}*/


$end_time = microtime(true);
$execution_time = $end_time - $start_time;
//echo "Execution time: " . $execution_time . " seconds"; 




//echo json_encode(array("node_details" => array($node_details), "nodes" => $node_id));

echo $node;





exit;



/************************************************************/

    }

}






















/******************************************************************************/

















/********************************************************************************/

if (isset($_GET['action']) && $_GET['action']=='auto_test_details_3-4-25')
{

$startid =$_GET['start_id'];

$start_time = microtime(true);
                        
			       
			$header = apache_request_headers();  
			foreach ($header as $key => $val) {
			
			$query_dev="SELECT node_id FROM efactorproduct where access_token='$val'";
			$doctors = $db->get_results($query_dev); 
			$num_dev = $db->num_rows($query_dev);
                        $row_dev = $db->get_row($query_dev);    
		        $node_id=$row_dev['node_id'];


$db->query("update efactorproduct set app_version='v8' where access_token='$val'");


/***************************************/
/*
if (!empty($_GET["start_id"])) {
    $node_id = $_GET["start_id"];
    
    // Get current device info
    $query_4 = "SELECT * FROM efactorproduct WHERE node_id='$node_id'";
    $row_4 = $db->get_row($query_4);
    $device_type = $row_4['device_type'];
    $model_name = $row_4['model_number'];

    // First try to find next device with add_gateway NOT NULL and is_added=1
    $query_next_id = "
        SELECT node_id 
        FROM efactorproduct 
        WHERE access_token = '$val' 
          AND is_added = '1' 
          AND add_gateway IS NOT NULL 
          AND add_gateway != 'NULL'
          AND node_id > '$node_id' 
        ORDER BY node_id ASC 
        LIMIT 1
    ";
    $row = $db->get_row($query_next_id);

    if ($row) {
        $device_id_next = $row['node_id'];
    } else {
        // If no gateway devices found, find the next device regardless of gateway status
        $query_next_id = "
            SELECT node_id 
            FROM efactorproduct 
            WHERE access_token = '$val' 
              AND node_id > '$node_id' 
            ORDER BY node_id ASC 
            LIMIT 1
        ";
        $row = $db->get_row($query_next_id);
        
        $device_id_next = $row ? $row['node_id'] : null;
    }

    $nextid = $device_id_next;
} else {
    // When no start_id is provided
    $query_3 = "SELECT * FROM efactorproduct WHERE node_id='$node_id'";
    $row_3 = $db->get_row($query_3);
    $node_id = $row_3['node_id'];
    $device_type = $row_3['device_type'];
    $model_name = $row_3['model_number'];

    // First try to find devices with add_gateway NOT NULL and is_added=1
    $query_priority_devices = "
        SELECT id, node_id, device_type 
        FROM efactorproduct 
        WHERE access_token = '$val'
          AND is_added = '1'
          AND add_gateway IS NOT NULL
          AND add_gateway != 'NULL'
        ORDER BY node_id ASC
    ";
    $priority_devices = $db->get_results($query_priority_devices);

    if (count($priority_devices) > 0) {
        // Use the first priority device
        $device_id = $priority_devices[0]['node_id'];
        $nextid = isset($priority_devices[1]['node_id']) ? $priority_devices[1]['node_id'] : null;
    } else {
        // Fall back to all devices if no priority devices found
        $all_devices = $db->get_results("SELECT id, node_id, device_type FROM efactorproduct WHERE access_token = '$val' ORDER BY node_id ASC");
        $device_id = $all_devices[0]['node_id'];
        $nextid = isset($all_devices[1]['node_id']) ? $all_devices[1]['node_id'] : null;
    }
}


*/

/****************************************/


if(!empty($_GET["start_id"]))
 {



$node_id=$startid;



$query_4 = "SELECT * FROM efactorproduct where node_id='$node_id'";
							   
							   $row_4 = $db->get_row($query_4);
						          // $node_id=$row['node_id'];
                                                           $device_type=$row_4['device_type'];
                                                           $model_name=$row_4['model_number'];




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


    $query_3 = "SELECT * FROM efactorproduct where node_id='$node_id'";
							    
							   $row_3 = $db->get_row($query_3);
						           $node_id=$row_3['node_id'];
                                                           $device_type=$row_3['device_type'];
                                                           $model_name=$row_3['model_number'];




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



if($model_name == 'EFDEFLT V1.0'){
    $query_node="SELECT * FROM efactorproduct where node_id='$node_id'";
}else{
$query_node="SELECT * FROM `$model_name` where node_id='$node_id'";
}
			$doctors_node = $db->get_results($query_node); 
			
                        $dbcker = $db->get_row($query_node);    
                       // print_r($dbcker);
		        $nodeid=$dbcker['node_id'];
                        $user_id=$dbcker['user_id'];
                        $curr_device_type=$dbcker['current_devicetype'];
                        $devicetype=$dbcker['device_type'];
                        $mortgage=$dbcker['Mortgage_page'];
                           $status=$dbcker['status_page'];
                           $device_comm_page=$dbcker['device_comm_page'];
                           $remote_pair_pairing=$dbcker['Remote_Pair_Pairing'];
                           $config_config=$dbcker['Configure_Config'];
                           $remote_address=$dbcker['Remote_Address'];


 $type="efactor.device.$devicetype"; 


/*-----------------------------------------------*/

// Determine device image
        if ($model_name == 'EF-003FMN03' || $model_name == 'EF-002FMN02' || $model_name == 'EF-001FMN01' || $model_name == 'BAJAJ-001FMN01') {
            $deviceimage = 'fan-EF-003FMN03.jpg';
        } elseif ($model_name == 'EFCKER V1.0') {
            $deviceimage = 'efact_cooker.jpg';
        } elseif ($model_name == 'EF-001LIGHT01' || $model_name == 'EF-002CWLIGHT02' || $model_name == 'EF-001RGBLIGHT01' || $model_name == 'EF-003WLIGHT03') {
            $deviceimage = 'light_org.jpg';
        } elseif ($model_name == 'EF-001OULT01' || $model_name == 'EF-001SOCKET_6A01' || $model_name == 'EF-001SOCKET_10A01') {
            $deviceimage = 'outlet.jpg';
        } elseif ($model_name == 'EFSWPCP V1.0') {
            $deviceimage = 'swpcp.jpg';
        } elseif ($model_name == 'INVENDIS_MFM_V1') {
            $deviceimage = 'invendis_device.jpg';
        }

elseif ($model_name == 'EF_RF_GATEW V1.0') {
            $deviceimage = 'gateway_org.jpg';
        }




else {
            $deviceimage = 'input_output.jpg';
        }


 $device_image = "https://efactor.tech/files/brand_logo/" . $deviceimage;



/*-----------------------------------------------*/


                        
                        
//$query = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND user_id = '177'";
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

        



      //   EFDEFLT V1.0
   
$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFDEFLT V1.1'";
$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";
//$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";

$result_1 = $db->get_results($query_1); 
$result_2 = $db->get_results($query_2); 

$result_3 = $db->num_rows($query_1);
$result_4 = $db->num_rows($query_2);

$result_5 = $db->get_row($query_1); 
$result_6 = $db->get_row($query_2); 

$doctors = array_merge($result_1, $result_2);
$num_11 = $result_3 + $result_4;
$row = array_merge($result_1, $result_2);
$parameter_name = $row['parameter_name'];
   /*
        $query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";

      $doctors = $db->get_results($query_1); 
        $num_11 = $db->num_rows($query_1);
        $row = $db->get_row($query_1); 
       $parameter_name = $row['parameter_name'];

    */
       

//print_r($row);

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
$query_param1 = "SELECT * FROM product_api WHERE model_name = 'EFDEFLT V1.1' AND parameter_name='$param1'";
$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
  //$query_param = "SELECT * FROM product_api WHERE model_name = 'EFACTDFLT' AND parameter_name='$param1'";

 //$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND parameter_name='$param1'";

$result_1 = $db->get_results($query_param1); 
$result_2 = $db->get_results($query_param); 

$result_3 = $db->num_rows($query_param1);
$result_4 = $db->num_rows($query_param);

$row_param1 = $db->get_row($query_param1); 
$row_param2 = $db->get_row($query_param); 
  

$params = array_merge($result_1, $result_2);
$num_param = $result_3 + $result_4;
$row_param = array_merge((array)$row_param1, (array)$row_param2);
 
 //$row_param = array_merge($result_5, $result_6);
/*
    $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";

        $params = $db->get_results($query_param); 
        $num_param = $db->num_rows($query_param);
          $row_param = $db->get_row($query_param); 
      //  print_r($row_param1);
*/
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
            $cmd_sno_asperui = explode(',', $row_param['cmd_sno_asperui']);
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
            $resp_sno_asperui = explode(',', $row_param['resp_sno_asperui']);
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
            $error_sno_asperui = explode(',', $row_param['error_sno_asperui']);
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



//if($page_names == 'Configure' || $page_names == 'Remote Pair')
 if($page_names == 'Configure' || $page_names == 'Remote Pair' || $page_names == 'Configure.Config' || $page_names == 'Set Max Current' || $page_names == 'Device Setting'){

if($parameter_name == 'wifi config' || $parameter_name == 'DAC')
{
$local_control ='disable';       //disable

}
else{
 $local_control ='enable';       //disable

}

}else{

$local_control ='enable';
}



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
            "local_control" => $local_control,
            "ui_ids"   => (int)$cmd_uiid[$i],
            "ui_description" => $cmd_uidescriptioninout[$i]
            ];


//$fields_cmd[] = $field_cmd;



          }elseif($cmd_uitype[$i] == 'None'){ 
            $field_cmd = [
               "default_name" =>$cmd_defaultvalue[$i],
               "value" => $cmd_valueinhexmin[$i],
               "Field_description" => $cmd_fielddescription[$i],
               "no_of_bytes" => (int)$cmd_noofbytes[$i],
               "byte_start_position" => (int)$cmd_startbytePosition[$i] + 27,
               "ui_type"  => $cmd_uitype[$i],
               "local_control" => $local_control
        ]; 

      }else{




        
$as_per_ui = $cmd_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

if (empty($as_per_ui_array_from_formatted) || $as_per_ui_array_from_formatted === ['']) {
    $as_per_ui_array_from_formatted = [];  
}

 

$sno_as_per_ui = $cmd_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
}

$asperui_values = $cmd_asperui_values[$i];
$asperui_values_array = explode('][', trim($asperui_values, '[]'));
$formatted_asperui_values = implode(',', $asperui_values_array);
$asperui_values_array_from_formatted = explode(',', $formatted_asperui_values);
 
if (empty($asperui_values_array_from_formatted) || $asperui_values_array_from_formatted === ['']) {
    $asperui_values_array_from_formatted = [];  
}


/*
if ($param1 =='Speed') {
    $as_per_ui_value =  $speed;
} 
*/
 
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($cmd_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }
 


/*
if($cmd_datatype[$i] == 'Float' && $cmd_valuedefinition[$i] == 'Fixed Value'){
  $integer_value = $as_per_ui_value;
}elseif($cmd_datatype[$i] == 'UnsignedInt' && $cmd_valuedefinition[$i] == 'Fixed Value') {
  $integer_value = $as_per_ui_value;
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
*/
if($cmd_uitype[$i] === 'Toggle Switch' || $cmd_uitype[$i] === 'Notification'){
// print_r($as_per_ui);
 //print_r($asperui_values);
//print_r($as_per_ui_value);
$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';    
    }elseif ($cmd_uitype[$i] === 'Slider' || $cmd_uitype[$i] === 'Hue Slider'  || $cmd_uitype[$i] === 'Hue Slider Custom'  || $cmd_uitype[$i] === 'Brightness Slider'  || $cmd_uitype[$i] === 'Airflow Slider') {
     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
        } elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);     
            }else{ 
                $integervalue = "null";  
            }
        }
    
    }elseif ($cmd_uitype[$i] === 'Drop Down') {


     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexdec($as_per_ui_value);  
               //print_r($sno_as_per_ui);
               //echo $as_per_ui_value;
  /*             $ui_toggle = explode('][', trim($asperui_values, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;        
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;


//$as_per_ui_value = (string)$asperui_values[$i];        
//$as_per_ui_value = strlen($asperui_values[$i]) == 1 ? '0' . $asperui_values[$i] : $asperui_values[$i];


$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

*/


$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

$ui_toggle_drop = explode('][', trim($asperui_values, '[]')); 
$ui_toggle_value_drop = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map_drop = array_combine($ui_toggle_value_drop,$ui_toggle_drop);

$as_per_ui_value =isset($dropdown_map_drop[$as_per_ui_value]) ? $dropdown_map_drop[$as_per_ui_value] : '';







   
  
            }else{ 
                $integervalue = "null";  
            }
        } elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);    
            }else{ 
                $integervalue = "null";  
            }
        }
    
    }elseif ($cmd_uitype[$i]  === 'Text') {
     
        if ($cmd_datatype[$i] === 'String') {
                if($as_per_ui_value !== 'NULL'){
                  //  $integervalue = $as_per_ui_value;

           //   if($as_per_ui != 'NULL')
                 if(!empty($as_per_ui))
                   {  
                     $ui_toggle = explode('][', trim($as_per_ui, '[]'));
                     $ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
                     $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                     $as_per_ui_value = (string)$as_per_ui_value;  
                     $as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;
                     $integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 
                   }
                   else{
                    $integervalue = $as_per_ui_value;
                   }




                        
                }else{
                    $integervalue = "null";  
            }  
         }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
         }elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
              //  $integervalue = hexToFloat($as_per_ui_value);  
                 $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);   
            }else{ 
                $integervalue = "null";  
            }
         }else{
            if($as_per_ui_value !== 'NULL'){
                $integervalue = $as_per_ui_value;  
            }else{
                $integervalue = "null";  
            }
         }
    
    }else{
        if($as_per_ui_value !== 'NULL'){
            $integervalue = $as_per_ui_value;  
        }else{
            $integervalue = "null";  
        }
    }
 


    if ($cmd_datatype[$i] === 'Float') {
        // $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
         // $integervalue = hexToFloat($integer_value);
         // $integervalue = empty($integer_value) ? (float)$integer_value :  0.0;
         $cmd_value2 = floatToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
         $cmd_sliderstep = (float)$cmd_sliderstepvalue[$i];
        $display_min = (float)$cmd_displayvaluemin[$i];
        $display_max = (float)$cmd_displayvaluemax[$i];
     }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
        if($cmd_uitype[$i] == 'Toggle Switch'){
            $ui_toggle2 = explode('][', trim($as_per_ui, '[]'));
    $ui_toggle_value2 = explode('][', trim($asperui_values, '[]'));
            $dropdown_map22 = array_combine($ui_toggle2,$ui_toggle_value2);  
            $cmd_value = isset($dropdown_map22[$integervalue]) ? $dropdown_map22[$integervalue] : '';  
            //print_r($cmd_value);
            $display_min = (int)$cmd_displayvaluemin[$i];
            $display_max = (int)$cmd_displayvaluemax[$i];
        }else{
      //  $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
      //   $integervalue = hexToInt($integer_value);
         $cmd_value2 = intToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
        $cmd_sliderstep = (int)$cmd_sliderstepvalue[$i];
        $display_min = (int)$cmd_displayvaluemin[$i];
        $display_max = (int)$cmd_displayvaluemax[$i];
        }
    }elseif ($cmd_datatype[$i] === 'String') {
      // $integervalue = (string)$integer_value;
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
    "value" => $as_per_ui_value,
    "Field_description" => $cmd_fielddescription[$i],
    "type" => $cmd_valuedefinition[$i],
    "data_type" => $cmd_datatype[$i],
    "no_of_bytes" => (int)$cmd_noofbytes[$i],
    "byte_start_position" => (int)$cmd_startbytePosition[$i],
    "ui_type"  => $cmd_uitype[$i],
    "local_control" => $local_control,
    "ui_ids"   => (int)$cmd_uiid[$i],
    "asperui" => $as_per_ui_array_from_formatted,  
    "sno_asperui" => $sno_as_per_ui_array_from_formatted,  
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
            "local_control" => $local_control
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
            "local_control" => $local_control,
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
                    "local_control" => $local_control,
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

$sno_as_per_ui = $resp_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
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
 
 
   
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($resp_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }



/*
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
*/
if(($resp_uiType[$i] === 'Toggle Switch') || ($resp_uiType[$i] === 'Notification')) {
// print_r($as_per_ui);
 //print_r($asperui_values);
//print_r($as_per_ui_value);
$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);
  
$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';  
    
    }elseif ($resp_uiType[$i] === 'Slider' || $resp_uiType[$i] === 'Hue Slider'  || $resp_uiType[$i] === 'Hue Slider Custom'  || $resp_uiType[$i] === 'Brightness Slider'  || $resp_uiType[$i] === 'Airflow Slider') {


     
        if ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i] === 'Drop Down') {

     
        if ($resp_dataType[$i] === 'UnsignedInt') {

            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexdec($as_per_ui_value);  
               //print_r($sno_as_per_ui);
               //echo $as_per_ui_value;
       /*       $ui_toggle = explode('][', trim($asperui_values, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;


//$as_per_ui_value = (string)$asperui_values[$i];        
//$as_per_ui_value = strlen($asperui_values[$i]) == 1 ? '0' . $asperui_values[$i] : $asperui_values[$i];


//$resp_integervalue = isset($dropdown_map[asperui_values[$i]]) ? $dropdown_map[asperui_values[$i]] : '';    
$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';    
*/



$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

$ui_toggle_drop = explode('][', trim($asperui_values, '[]')); 
$ui_toggle_value_drop = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map_drop = array_combine($ui_toggle_value_drop,$ui_toggle_drop);

$resp_value =isset($dropdown_map_drop[$as_per_ui_value]) ? $dropdown_map_drop[$as_per_ui_value] : '';






  
            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $resp_integervalue= sprintf("%.2f", $cmdfloat_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i]  === 'Text') {
     
        if ($resp_dataType[$i] === 'String') {
                if(!empty($as_per_ui_value)){
                  //  $resp_integervalue = $as_per_ui_value;



                 // if($as_per_ui != 'NULL')
                    if(!empty($as_per_ui))
                   {  
                     $ui_toggle = explode('][', trim($as_per_ui, '[]'));
                     $ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
                     $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                     $as_per_ui_value = (string)$as_per_ui_value;  
                     $as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;
                     $resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 
                   }
                   else{
                    $resp_integervalue = $as_per_ui_value;
                   }









                }else{
                    $resp_integervalue = "null";  
            }  
         }elseif ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
         }elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
         }else{
            if(!empty($as_per_ui_value)){
                $resp_integervalue = $as_per_ui_value;  
            }else{
                $resp_integervalue = "null";  
            }
         }
    
    }else{
        if(!empty($as_per_ui_value)){
            $resp_integervalue = $as_per_ui_value;  
        }else{
            $resp_integervalue = "null";  
        }
    }
   
  
    if ($resp_dataType[$i] === 'Float') {
        //$asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (float)$integer_value;
        $resp_value2 = floatToHex($resp_integervalue);
         $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderstep = (float)$resp_sliderstepvalue[$i];
    } elseif ($resp_dataType[$i] === 'UnsignedInt') {
       // $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (int)$integer_value;

 if($resp_uiType[$i] != 'Drop Down')
         {  

         $resp_value2 = intToHex($resp_integervalue);
          $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderStep = (int)$resp_sliderStepValue[$i];
       }
    }elseif($resp_dataType[$i] === 'String'){
     // $respintegervalue = (string)$integer_value;
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
                    "local_control" => $local_control,
                    "ui_ids" => (int)$resp_uiId[$i],
                    "asperui" => $as_per_ui_array_from_formatted,
                    "sno_asperui" => $sno_as_per_ui_array_from_formatted,
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
        } 









else {


if($resp_uiType[$i] == 'None')
{
            $field_resp = [
                "default_name" => $resp_defaultvalue[$i],
                "value" => $resp_valueInHexMin[$i],
                "Field_description" => $resp_fieldDescription[$i],
                "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                "byte_start_position" => (int)$resp_startBytePosition[$i],
                "ui_type" => $resp_uiType[$i],
                "local_control" => $local_control
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
                "local_control" => $local_control,
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

   

$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($error_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }


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
            "local_control" => $local_control,
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

unset($para_result['']);

/*
if($model_name == 'EF-003FMN03')
{

// Remove the unwanted pages after processing
//unset($para_result['Device Communication']);
//unset($para_result['Status']);


unset($para_result['Configure.Config']);
unset($para_result['Remote Address']);
unset($para_result['Remote Pair.Pairing']);

}
   //02-06-25*/




if ($mortgage === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Mortgage') {
 unset($para_result[$key]);
                   }
    }
}

if ($status === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Status') {
 unset($para_result[$key]);
                   }
    }
}




if ($device_comm_page === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Device Communication') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_pair_pairing === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Pair.Pairing') {
 unset($para_result[$key]);
                   }
    }
}


if ($config_config === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Configure.Config') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_address === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Address') {
 unset($para_result[$key]);
                   }
    }
}




/**************************/


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
//$devicetype=$row_inter['device_type'];
$curr_devicetype=$row_inter['current_devicetype'];
$ssid=$row_inter['ssid'];



$vendor_name=$row_inter['vendor_name'];



if($vendor_name == 'EFACTOR')

{
$brandname='efactor';
}
if($vendor_name == 'BAJAJ')

{
$brandname='Bajaj';


}

if($vendor_name == 'DHRIO')

{
$brandname='dhrio';


}


if($vendor_name == 'Invendis')

{
$brandname='Invendis';


}



if($vendor_name == 'Skytec')

{
$brandname='Skytec';


}


if($vendor_name == 'exhaust_hood')

{
$brandname='exhaust';


}




$new_prefix = "EWIFI";
$parts = explode("_", $curr_devicetype, 2);
$access_point = $new_prefix . "_" . $parts[1];
//echo $updated_devicetype; 


$type="efactor.device.$devicetype";

/*

if($name_dev == 'NULL')
{

$curr_name=$curr_devicetype;

}

else{


$curr_name=$name_dev;
}
*/

$url_params = "model_name={$model_number}&user_id={$user_id}";
$auto_url_params = "model_name={$model_number}&user_id={$user_id}&node_id={$node_id}";



 $query_unread = "SELECT COUNT(*) AS `unread_count` FROM call_service WHERE node_id = '$node_id' AND status = 'unread'";
$row_unread = $db->get_row($query_unread);

// Convert object to array and access key safely
$unread_count = '0';
if ($row_unread) {
    foreach ($row_unread as $key => $val) {
        if (trim($key) === 'unread_count') {
            $unread_count = trim((string)$val); // Ensure it's a trimmed string
        }
    }
}


if ($unread_count === '' || $unread_count === null) {
    $unread_count = '0';
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
                "name" => $curr_device_type,
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
                "Device Id"=> $node_id,
                "pages" =>  $para_result,
                   
               
                "primary" => "power",
                "type" => $type,
                "brand_logo" => "https://efactor.tech/files/brand_logo/" . $brandname . ".jpg",
                "device_image"=> "https://efactor.tech/files/brand_logo/fan-EF-003FMN03.jpg",
                 "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
                "chat_status"=>$unread_count,
                "device_p2p_control"=>"disable",
                "Device Communication"=>"disable",
                "Status"=>"enable"
            )
        ),
        "info" => array(
            "fw_version" => "1.5.1",
            "model" => $model_number,
            "name" => $curr_devicetype,
            "type" => $moduletype,
            "SSID" => $ssid,
            "Access_Point" => $access_point,
            "Guarantee_Card" => "https://efactor.tech/v8/api.php?action=guarantee_card_display&{$url_params}",
           // "Video" => "https://efactor.tech/v8/api.php?action=app_video&{$url_params}",
              "Video" => "https://efactor.tech/v8/user_guide_support.php",
            "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
            "chat_status"=>$unread_count,
            "Auto_Test" => "https://efactor.tech/v8/auto_test.php?{$auto_url_params}"

        ),




        "node_id" => $node_id
    ),
    "role" => "primary",
    "status" => array(
        "connectivity" => array(
            "connected" => ($net_conn_1 === "true") ? true : false,
            "timestamp" => $timestamp
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




$end_time = microtime(true);
$execution_time = $end_time - $start_time;
//echo "Execution time: " . $execution_time . " seconds"; 




//echo json_encode(array("node_details" => array($node_details), "nodes" => $node_id));

echo $node;





exit;



/************************************************************/

    }

}






















/******************************************************************************/


































/******************************************************************************/


if (isset($_GET['action']) && $_GET['action']=='test_details_11111')
{

$startid =$_GET['start_id'];

//$start_time = microtime(true);
                        
	$header = apache_request_headers();  
			foreach ($header as $key => $val) {
			
			$query_dev="SELECT node_id FROM efactorproduct where access_token='$val'";
			 $doctors = $db->get_results($query_dev);
			$num_dev = $db->num_rows($query_dev);
                        $row_dev = $db->get_row($query_dev);    
		        $node_id=$row_dev['node_id'];


$db->query("update efactorproduct set app_version='v8' where access_token='$val'");


if(!empty($_GET["start_id"]))
 {



$node_id=$startid;



$query_4 = "SELECT * FROM efactorproduct where node_id='$node_id'";
							   $doctors_4 = $db->get_results($query_4);   
							   $row_4 = $db->get_row($query_4);
						                                                                     $device_type=$row_4['device_type'];
                                                           $model_name=$row_4['model_number'];




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


    $query_3 = "SELECT * FROM efactorproduct where node_id='$node_id'";
							    
							   $row_3 = $db->get_row($query_3);
						           $node_id=$row_3['node_id'];
                                                           $device_type=$row_3['device_type'];
                                                           $model_name=$row_3['model_number'];




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



if($model_name == 'EFDEFLT V1.0'){
    $query_node="SELECT * FROM efactorproduct where node_id='$node_id'";
}else{
$query_node="SELECT * FROM `$model_name` where node_id='$node_id'";
}
			
			$num_node = $db->num_rows($query_node);
                           
                       // print_r($dbcker);
		        $nodeid=$dbcker['node_id'];
                        $user_id=$dbcker['user_id'];
                        $curr_device_type=$dbcker['current_devicetype'];
                         $devicetype=$dbcker['device_type'];
                       $mortgage=$dbcker['Mortgage_page'];
                           $status=$dbcker['status_page'];
                           $device_comm_page=$dbcker['device_comm_page'];
                           $remote_pair_pairing=$dbcker['Remote_Pair_Pairing'];
                           $config_config=$dbcker['Configure_Config'];
                           $remote_address=$dbcker['Remote_Address'];


 $type="efactor.device.$devicetype"; 







/*-----------------------------------------------*/

// Determine device image
        if ($model_name == 'EF-003FMN03' || $model_name == 'EF-002FMN02' || $model_name == 'EF-001FMN01' || $model_name == 'BAJAJ-001FMN01') {
            $deviceimage = 'fan-EF-003FMN03.jpg';
        } elseif ($model_name == 'EFCKER V1.0') {
            $deviceimage = 'efact_cooker.jpg';
        } elseif ($model_name == 'EF-001LIGHT01' || $model_name == 'EF-002CWLIGHT02' || $model_name == 'EF-001RGBLIGHT01' || $model_name == 'EF-003WLIGHT03') {
            $deviceimage = 'light_org.jpg';
        } elseif ($model_name == 'EF-001OULT01' || $model_name == 'EF-001SOCKET_6A01' || $model_name == 'EF-001SOCKET_10A01') {
            $deviceimage = 'outlet.jpg';
        } elseif ($model_name == 'EFSWPCP V1.0') {
            $deviceimage = 'swpcp.jpg';
        } elseif ($model_name == 'INVENDIS_MFM_V1') {
            $deviceimage = 'invendis_device.jpg';
        }

elseif ($model_name == 'EF_RF_GATEW V1.0') {
            $deviceimage = 'gateway_org.jpg';
        }




else {
            $deviceimage = 'input_output.jpg';
        }


 $device_image = "https://efactor.tech/files/brand_logo/" . $deviceimage;



/*-----------------------------------------------*/


                        
                        
//$query = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND user_id = '177'";
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


        


 
$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name' AND parameter_name IN ('name', 'power')";
$result_2 = $db->get_results($query_2); 
$result_4 = $db->num_rows($query_2);
$result_6 = $db->get_row($query_2); 

$doctors = $result_2;
$num_11 = $result_4;
$row = $result_2;
$parameter_name = $row['parameter_name'];
$result = [];

//$end_time = microtime(true);
//$execution_time = $end_time - $start_time;
//echo "Execution time: " . $execution_time . " seconds"; 


 
foreach ($doctors as $param) {
   
    $fields_cmd = [];
   // $fields_resp = [];
   // $fields_error = [];

$param1=$param['parameter_name'];

//$query_param1 = "SELECT * FROM product_api WHERE model_name = 'EFDEFLT V1.1' AND parameter_name='$param1'";
$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
  

//$result_1 = $db->get_results($query_param1); 
$result_2 = $db->get_results($query_param); 

//$result_3 = $db->num_rows($query_param1);
$result_4 = $db->num_rows($query_param);

//$row_param1 = $db->get_row($query_param1); 
$row_param2 = $db->get_row($query_param); 
  

$params = array_merge($result_1, $result_2);
$num_param = $result_3 + $result_4;
//$row_param = array_merge((array)$row_param1, (array)$row_param2);
$row_param = array_merge((array)$row_param1, (array)$row_param2);

 
 //$row_param = array_merge($result_5, $result_6);
/*
    $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";

        $params = $db->get_results($query_param); 
        $num_param = $db->num_rows($query_param);
          $row_param = $db->get_row($query_param); 
      //  print_r($row_param1);
*/
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
            $cmd_sno_asperui = explode(',', $row_param['cmd_sno_asperui']);
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
            $resp_sno_asperui = explode(',', $row_param['resp_sno_asperui']);
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
            $error_sno_asperui = explode(',', $row_param['error_sno_asperui']);
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



          }elseif($cmd_uitype[$i] == 'None'){ 
            $field_cmd = [
               "default_name" =>$cmd_defaultvalue[$i],
               "value" => $cmd_valueinhexmin[$i],
               "Field_description" => $cmd_fielddescription[$i],
               "no_of_bytes" => (int)$cmd_noofbytes[$i],
               "byte_start_position" => (int)$cmd_startbytePosition[$i] + 27,
               "ui_type"  => $cmd_uitype[$i],
        ]; 

      }else{




        
$as_per_ui = $cmd_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

if (empty($as_per_ui_array_from_formatted) || $as_per_ui_array_from_formatted === ['']) {
    $as_per_ui_array_from_formatted = [];  
}

$sno_as_per_ui = $cmd_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
}

$asperui_values = $cmd_asperui_values[$i];
$asperui_values_array = explode('][', trim($asperui_values, '[]'));
$formatted_asperui_values = implode(',', $asperui_values_array);
$asperui_values_array_from_formatted = explode(',', $formatted_asperui_values);
 
if (empty($asperui_values_array_from_formatted) || $asperui_values_array_from_formatted === ['']) {
    $asperui_values_array_from_formatted = [];  
}


/*
if ($param1 =='Speed') {
    $as_per_ui_value =  $speed;
} 
*/
 
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($cmd_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }
 


/*
if($cmd_datatype[$i] == 'Float' && $cmd_valuedefinition[$i] == 'Fixed Value'){
  $integer_value = $as_per_ui_value;
}elseif($cmd_datatype[$i] == 'UnsignedInt' && $cmd_valuedefinition[$i] == 'Fixed Value') {
  $integer_value = $as_per_ui_value;
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
*/
if($cmd_uitype[$i] === 'Toggle Switch'){
// print_r($as_per_ui);
 //print_r($asperui_values);
//print_r($as_per_ui_value);
$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);
$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';    
    }elseif ($cmd_uitype[$i] === 'Slider' || $cmd_uitype[$i] === 'Hue Slider'  || $cmd_uitype[$i] === 'Hue Slider Custom'  || $cmd_uitype[$i] === 'Brightness Slider'  || $cmd_uitype[$i] === 'Airflow Slider') {
     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
        } elseif ($cmd_datatype[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $integervalue = "null";  
            }
        }
    
    }elseif ($cmd_uitype[$i]  === 'Text') {
     
        if ($cmd_datatype[$i] === 'String') {
                if(!empty($as_per_ui_value)){
                    $integervalue = $as_per_ui_value;
                }else{
                    $integervalue = "null";  
            }  
         }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
         }elseif ($cmd_datatype[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $integervalue = "null";  
            }
         }else{
            if(!empty($as_per_ui_value)){
                $integervalue = $as_per_ui_value;  
            }else{
                $integervalue = "null";  
            }
         }
    
    }else{
        if(!empty($as_per_ui_value)){
            $integervalue = $as_per_ui_value;  
        }else{
            $integervalue = "null";  
        }
    }
 


    if ($cmd_datatype[$i] === 'Float') {
        // $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
         // $integervalue = hexToFloat($integer_value);
         // $integervalue = empty($integer_value) ? (float)$integer_value :  0.0;
         $cmd_value2 = floatToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
         $cmd_sliderstep = (float)$cmd_sliderstepvalue[$i];
        $display_min = (float)$cmd_displayvaluemin[$i];
        $display_max = (float)$cmd_displayvaluemax[$i];
     }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
        if($cmd_uitype[$i] == 'Toggle Switch'){
            $ui_toggle2 = explode('][', trim($as_per_ui, '[]'));
    $ui_toggle_value2 = explode('][', trim($asperui_values, '[]'));
            $dropdown_map22 = array_combine($ui_toggle2,$ui_toggle_value2);  
            $cmd_value = isset($dropdown_map22[$integervalue]) ? $dropdown_map22[$integervalue] : '';  
            //print_r($cmd_value);
            $display_min = (int)$cmd_displayvaluemin[$i];
            $display_max = (int)$cmd_displayvaluemax[$i];
        }else{
      //  $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
      //   $integervalue = hexToInt($integer_value);
         $cmd_value2 = intToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
        $cmd_sliderstep = (int)$cmd_sliderstepvalue[$i];
        $display_min = (int)$cmd_displayvaluemin[$i];
        $display_max = (int)$cmd_displayvaluemax[$i];
        }
    }elseif ($cmd_datatype[$i] === 'String') {
      // $integervalue = (string)$integer_value;
       $cmd_sliderstep = '';
          $display_min = (int)$cmd_displayvaluemin[$i];
        $display_max = (int)$cmd_displayvaluemax[$i];

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
    "sno_asperui" => $sno_as_per_ui_array_from_formatted,  
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

$sno_as_per_ui = $resp_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
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
 
 
   
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($resp_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }



/*
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
*/
if(($resp_uiType[$i] === 'Toggle Switch') || ($resp_uiType[$i] === 'Notification')) {

$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);
  
$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';  
    
    }elseif ($resp_uiType[$i] === 'Slider' || $resp_uiType[$i] === 'Hue Slider'  || $resp_uiType[$i] === 'Hue Slider Custom'  || $resp_uiType[$i] === 'Brightness Slider'  || $resp_uiType[$i] === 'Airflow Slider') {
     
        if ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i] === 'Drop Down') {


     
        if ($resp_dataType[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexdec($as_per_ui_value);  
               //print_r($sno_as_per_ui);
               //echo $as_per_ui_value;
          /*     $ui_toggle = explode('][', trim($asperui_values, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;


//$resp_integervalue = isset($dropdown_map[asperui_values[$i]]) ? $dropdown_map[asperui_values[$i]] : '';    

$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';   */



$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

$ui_toggle_drop = explode('][', trim($asperui_values, '[]')); 
$ui_toggle_value_drop = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map_drop = array_combine($ui_toggle_value_drop,$ui_toggle_drop);

$resp_value =isset($dropdown_map_drop[$as_per_ui_value]) ? $dropdown_map_drop[$as_per_ui_value] : '';


 
  
            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $resp_integervalue= sprintf("%.2f", $cmdfloat_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i]  === 'Text') {
     
        if ($resp_dataType[$i] === 'String') {
                if(!empty($as_per_ui_value)){
                 //   $resp_integervalue = $as_per_ui_value;



              //  if($as_per_ui != 'NULL')
                  if(!empty($as_per_ui))
                   {  
                     $ui_toggle = explode('][', trim($as_per_ui, '[]'));
                     $ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
                     $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                     $as_per_ui_value = (string)$as_per_ui_value;  
                     $as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;
                     $resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 
                   }
                   else{
                    $resp_integervalue = $as_per_ui_value;
                   }






                }else{
                    $resp_integervalue = "null";  
            }  
         }elseif ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
         }elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
         }else{
            if(!empty($as_per_ui_value)){
                $resp_integervalue = $as_per_ui_value;  
            }else{
                $resp_integervalue = "null";  
            }
         }
    
    }else{
        if(!empty($as_per_ui_value)){
            $resp_integervalue = $as_per_ui_value;  
        }else{
            $resp_integervalue = "null";  
        }
    }
   
  
    if ($resp_dataType[$i] === 'Float') {
        //$asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (float)$integer_value;
        $resp_value2 = floatToHex($resp_integervalue);
         $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderstep = (float)$resp_sliderstepvalue[$i];
    } elseif ($resp_dataType[$i] === 'UnsignedInt') {
       // $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (int)$integer_value;
            if($resp_uiType[$i] != 'Drop Down')
         {  

         $resp_value2 = intToHex($resp_integervalue);
          $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderStep = (int)$resp_sliderStepValue[$i];
        }
    }elseif($resp_dataType[$i] === 'String'){
     // $respintegervalue = (string)$integer_value;
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
                    "sno_asperui" => $sno_as_per_ui_array_from_formatted,
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
        } 









else {


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

   

$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($error_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }


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


$para_default_result = $result_default;

unset($para_result['']);

/*
if($model_name == 'EF-003FMN03')
{

// Remove the unwanted pages after processing
//unset($para_result['Device Communication']);
//unset($para_result['Status']);


unset($para_result['Configure.Config']);
unset($para_result['Remote Address']);
unset($para_result['Remote Pair.Pairing']);

}
  //02-06-25
*/




if ($mortgage === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Mortgage') {
 unset($para_result[$key]);
                   }
    }
}

if ($status === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Status') {
 unset($para_result[$key]);
                   }
    }
}




if ($device_comm_page === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Device Communication') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_pair_pairing === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Pair.Pairing') {
 unset($para_result[$key]);
                   }
    }
}


if ($config_config === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Configure.Config') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_address === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Address') {
 unset($para_result[$key]);
                   }
    }
}




/**************************/


/*-------------------------------------------------------------*/
/*************************************************************/



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
//$devicetype=$row_inter['device_type'];
$curr_devicetype=$row_inter['current_devicetype'];
$ssid=$row_inter['ssid'];
$vendor_name=$row_inter['vendor_name'];



if($vendor_name == 'EFACTOR')

{
$brandname='efactor';
}
if($vendor_name == 'BAJAJ')

{
$brandname='Bajaj';

}

if($vendor_name == 'DHRIO')

{
$brandname='dhrio';


}


if($vendor_name == 'Invendis')

{
$brandname='Invendis';


}



if($vendor_name == 'Skytec')

{
$brandname='Skytec';


}


if($vendor_name == 'exhaust_hood')

{
$brandname='exhaust';


}




$new_prefix = "EWIFI";
$parts = explode("_", $curr_devicetype, 2);
$access_point = $new_prefix . "_" . $parts[1];
//echo $updated_devicetype; 


$type="efactor.device.$devicetype";





$params = http_build_query([
    'model_name' => $model_number,
    'user_id' => $user_id,
    'node_id' => $node_id
]);




$url_params = "model_name={$model_number}&user_id={$user_id}";
$auto_url_params = "model_name={$model_number}&user_id={$user_id}&node_id={$node_id}";


 $query_unread = "SELECT COUNT(*) AS `unread_count` FROM call_service WHERE node_id = '$node_id' AND status = 'unread'";
$row_unread = $db->get_row($query_unread);

// Convert object to array and access key safely
$unread_count = '0';
if ($row_unread) {
    foreach ($row_unread as $key => $val) {
        if (trim($key) === 'unread_count') {
            $unread_count = trim((string)$val); // Ensure it's a trimmed string
        }
    }
}


if ($unread_count === '' || $unread_count === null) {
    $unread_count = '0';
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
                "name" => $curr_device_type,
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
                 "Device Id"=> $node_id,
                "pages" =>  $para_result,
                   
               
                "primary" => "power",
                "type" => $type,
                "brand_logo" => "https://efactor.tech/files/brand_logo/" .$brandname. ".jpg",
                "device_image"=> "https://efactor.tech/files/brand_logo/fan-EF-003FMN03.jpg",
                 "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
                 "chat_status"=>$unread_count,
                "device_p2p_control"=>"disable",
                "Device Communication"=>"disable",
                "Status"=>"enable"
            )
        ),
 



"info" => array(
            "fw_version" => "1.5.1",
            "model" => $model_number,
            "name" => $curr_devicetype,
            "type" => $moduletype,
            "SSID" => $ssid,
            "Access_Point" => $access_point
                    ),




        "node_id" => $node_id
    ),
    "role" => "primary",
    "status" => array(
        "connectivity" => array(
            "connected" => ($net_conn_1 === "true") ? true : false,
            "timestamp" => $timestamp
        )
    )
);






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




/***************************************************************/


if (isset($_GET['action']) && $_GET['action']=='test_nodes')
{

//$startid =$_GET['start_id'];


$node_id =$_GET['node_id'];

$start_time = microtime(true);
                        
			       
			$header = apache_request_headers();  
			foreach ($header as $key => $val) {
			
			$query_dev="SELECT * FROM efactorproduct where node_id='$node_id'";
			$doctors = $db->get_results($query_dev); 
			$num_dev = $db->num_rows($query_dev);
                        $row_dev = $db->get_row($query_dev);    
		       $device_type=$row_dev['device_type'];
                       $model_name=$row_dev['model_number'];
                       $is_added_gate=$row_dev['is_added'];








$db->query("update efactorproduct set app_version='v8' where access_token='$val'");



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
//$devicetype=$row_inter['device_type'];
$curr_devicetype=$row_inter['current_devicetype'];
$ssid=$row_inter['ssid'];
$mac_value=$row_inter['mac'];
$is_added=$row_inter['is_added'];
$add_gateway=$row_inter['add_gateway'];





$vendor_name=$row_inter['vendor_name'];

switch ($vendor_name) {
    case 'EFACTOR':
        $brandname = 'efactor';
        break;
    case 'BAJAJ':
        $brandname = 'Bajaj';
        break;
    case 'DHRIO':
        $brandname = 'dhrio';
        break;
    case 'Invendis':
        $brandname = 'Invendis';
        break;
    case 'Skytec':
        $brandname = 'Skytec';
        break;
    case 'exhaust_hood':
        $brandname = 'exhaust';
        break;
    default:
        $brandname = 'efactor';
        break;
}


/*

// Determine device image
        if ($model_name == 'EF-003FMN03' || $model_name == 'EF-002FMN02' || $model_name == 'EF-001FMN01' || $model_name == 'BAJAJ-001FMN01') {
            $deviceimage = 'fan-EF-003FMN03.jpg';
        } elseif ($model_name == 'EFCKER V1.0') {
            $deviceimage = 'efact_cooker.jpg';
        } elseif ($model_name == 'EF-001LIGHT01' || $model_name == 'EF-002CWLIGHT02' || $model_name == 'EF-001RGBLIGHT01' || $model_name == 'EF-003WLIGHT03') {
            $deviceimage = 'light_org.jpg';
        } elseif ($model_name == 'EF-001OULT01' || $model_name == 'EF-001SOCKET_6A01' || $model_name == 'EF-001SOCKET_10A01') {
            $deviceimage = 'outlet.jpg';
        } elseif ($model_name == 'EFSWPCP V1.0') {
            $deviceimage = 'swpcp.jpg';
        } elseif ($model_name == 'INVENDIS_MFM_V1') {
            $deviceimage = 'invendis_device.jpg';
        }

elseif ($model_name == 'EF_RF_GATEW V1.0') {
            $deviceimage = 'gateway_org.jpg';
        }




else {
            $deviceimage = 'input_output.jpg';
        }


 $device_image = "https://efactor.tech/files/brand_logo/" . $deviceimage;

*/
$new_prefix = "EWIFI";
$parts = explode("_", $curr_devicetype, 2);
$access_point = $new_prefix . "_" . $parts[1];
//echo $updated_devicetype; 











// Initialize devices array
$devices = [];

if ($add_gateway == 'NULL') {








if($model_name == 'EFDEFLT V1.0'){
    $query_node="SELECT * FROM efactorproduct where node_id='$node_id'";
}else{
$query_node="SELECT * FROM `$model_name` where node_id='$node_id'";
}
			$doctors_node = $db->get_results($query_node); 
			//$num_node = $db->num_rows($query_node);
                        $dbcker = $db->get_row($query_node);    
                       // print_r($dbcker);
		      //  $nodeid=$dbcker['node_id'];
                      //  $user_id=$dbcker['user_id'];
                        $model_name=$dbcker['model_name'];
                           $curr_device_type=$dbcker['current_devicetype'];
                           $devicetype=$dbcker['device_type'];
                           $mortgage=$dbcker['Mortgage_page'];
                           $status=$dbcker['status_page'];
                           $device_comm_page=$dbcker['device_comm_page'];
                           $remote_pair_pairing=$dbcker['Remote_Pair_Pairing'];
                           $config_config=$dbcker['Configure_Config'];
                           $remote_address=$dbcker['Remote_Address'];
                               

       $type="efactor.device.$devicetype";    


/*-----------------------------------------------*/

// Determine device image
        if ($model_name == 'EF-003FMN03' || $model_name == 'EF-002FMN02' || $model_name == 'EF-001FMN01' || $model_name == 'BAJAJ-001FMN01') {
            $deviceimage = 'fan-EF-003FMN03.jpg';
        } elseif ($model_name == 'EFCKER V1.0') {
            $deviceimage = 'efact_cooker.jpg';
        } elseif ($model_name == 'EF-001LIGHT01' || $model_name == 'EF-002CWLIGHT02' || $model_name == 'EF-001RGBLIGHT01' || $model_name == 'EF-003WLIGHT03') {
            $deviceimage = 'light_org.jpg';
        } elseif ($model_name == 'EF-001OULT01' || $model_name == 'EF-001SOCKET_6A01' || $model_name == 'EF-001SOCKET_10A01') {
            $deviceimage = 'outlet.jpg';
        } elseif ($model_name == 'EFSWPCP V1.0') {
            $deviceimage = 'swpcp.jpg';
        } elseif ($model_name == 'INVENDIS_MFM_V1') {
            $deviceimage = 'invendis_device.jpg';
        }

elseif ($model_name == 'EF_RF_GATEW V1.0') {
            $deviceimage = 'gateway_org.jpg';
        }




else {
            $deviceimage = 'input_output.jpg';
        }


 $device_image = "https://efactor.tech/files/brand_logo/" . $deviceimage;



/*-----------------------------------------------*/





/*-----------------------------------------------*/

// Determine device image
        if ($model_name == 'EF-003FMN03' || $model_name == 'EF-002FMN02' || $model_name == 'EF-001FMN01' || $model_name == 'BAJAJ-001FMN01') {
            $deviceimage = 'fan-EF-003FMN03.jpg';
        } elseif ($model_name == 'EFCKER V1.0') {
            $deviceimage = 'efact_cooker.jpg';
        } elseif ($model_name == 'EF-001LIGHT01' || $model_name == 'EF-002CWLIGHT02' || $model_name == 'EF-001RGBLIGHT01' || $model_name == 'EF-003WLIGHT03') {
            $deviceimage = 'light_org.jpg';
        } elseif ($model_name == 'EF-001OULT01' || $model_name == 'EF-001SOCKET_6A01' || $model_name == 'EF-001SOCKET_10A01') {
            $deviceimage = 'outlet.jpg';
        } elseif ($model_name == 'EFSWPCP V1.0') {
            $deviceimage = 'swpcp.jpg';
        } elseif ($model_name == 'INVENDIS_MFM_V1') {
            $deviceimage = 'invendis_device.jpg';
        }

elseif ($model_name == 'EF_RF_GATEW V1.0') {
            $deviceimage = 'gateway_org.jpg';
        }




else {
            $deviceimage = 'input_output.jpg';
        }


 $device_image = "https://efactor.tech/files/brand_logo/" . $deviceimage;



/*-----------------------------------------------*/
                       
                                         
  

//$query = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND user_id = '177'";
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

        



     //   EFDEFLT V1.0
   
$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFDEFLT V1.1'";
$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";
//$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";

$result_1 = $db->get_results($query_1); 
$result_2 = $db->get_results($query_2); 

$result_3 = $db->num_rows($query_1);
$result_4 = $db->num_rows($query_2);

$result_5 = $db->get_row($query_1); 
$result_6 = $db->get_row($query_2); 

$doctors = array_merge($result_1, $result_2);
$num_11 = $result_3 + $result_4;
$row = array_merge($result_1, $result_2);
$parameter_name = $row['parameter_name'];
   /*
        $query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";

      $doctors = $db->get_results($query_1); 
        $num_11 = $db->num_rows($query_1);
        $row = $db->get_row($query_1); 
       $parameter_name = $row['parameter_name'];

    */
       

//print_r($row);

//$parameters = ['power', 'speed', 'led', 'direction'];

// Array to store the final result
$result = [];



 
foreach ($doctors as $param) {
   
    $fields_cmd = [];
    $fields_resp = [];
    $fields_error = [];

$param1=$param['parameter_name'];


 
//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFFN02' AND user_id = '177' AND parameter_name='$param1'";
$query_param1 = "SELECT * FROM product_api WHERE model_name = 'EFDEFLT V1.1' AND parameter_name='$param1'";
$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
  //$query_param = "SELECT * FROM product_api WHERE model_name = 'EFACTDFLT' AND parameter_name='$param1'";

 //$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND parameter_name='$param1'";

$result_1 = $db->get_results($query_param1); 
$result_2 = $db->get_results($query_param); 

$result_3 = $db->num_rows($query_param1);
$result_4 = $db->num_rows($query_param);

$row_param1 = $db->get_row($query_param1); 
$row_param2 = $db->get_row($query_param); 
  

$params = array_merge($result_1, $result_2);
$num_param = $result_3 + $result_4;
$row_param = array_merge((array)$row_param1, (array)$row_param2);
 
 //$row_param = array_merge($result_5, $result_6);
/*
    $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";

        $params = $db->get_results($query_param); 
        $num_param = $db->num_rows($query_param);
          $row_param = $db->get_row($query_param); 
      //  print_r($row_param1);
*/
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
            $cmd_sno_asperui = explode(',', $row_param['cmd_sno_asperui']);
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
            $resp_sno_asperui = explode(',', $row_param['resp_sno_asperui']);
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
            $error_sno_asperui = explode(',', $row_param['error_sno_asperui']);
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



//if($page_names == 'Configure' || $page_names == 'Remote Pair')
 if($page_names == 'Configure' || $page_names == 'Remote Pair' || $page_names == 'Configure.Config' || $page_names == 'Set Max Current' || $page_names == 'Device Setting'){

if($parameter_name == 'wifi config' || $parameter_name == 'DAC')
{
$local_control ='disable';       //disable

}
else{
 $local_control ='enable';       //disable

}

}else{

$local_control ='enable';
}



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
            "local_control" => $local_control,
            "ui_ids"   => (int)$cmd_uiid[$i],
            "ui_description" => $cmd_uidescriptioninout[$i]
            ];


//$fields_cmd[] = $field_cmd;



          }elseif($cmd_uitype[$i] == 'None'){ 
            $field_cmd = [
               "default_name" =>$cmd_defaultvalue[$i],
               "value" => $cmd_valueinhexmin[$i],
               "Field_description" => $cmd_fielddescription[$i],
               "no_of_bytes" => (int)$cmd_noofbytes[$i],
               "byte_start_position" => (int)$cmd_startbytePosition[$i] + 27,
               "ui_type"  => $cmd_uitype[$i],
               "local_control" => $local_control
        ]; 

      }else{




        
$as_per_ui = $cmd_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

if (empty($as_per_ui_array_from_formatted) || $as_per_ui_array_from_formatted === ['']) {
    $as_per_ui_array_from_formatted = [];  
}

 

$sno_as_per_ui = $cmd_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
}

$asperui_values = $cmd_asperui_values[$i];
$asperui_values_array = explode('][', trim($asperui_values, '[]'));
$formatted_asperui_values = implode(',', $asperui_values_array);
$asperui_values_array_from_formatted = explode(',', $formatted_asperui_values);
 
if (empty($asperui_values_array_from_formatted) || $asperui_values_array_from_formatted === ['']) {
    $asperui_values_array_from_formatted = [];  
}


/*
if ($param1 =='Speed') {
    $as_per_ui_value =  $speed;
} 
*/
 
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($cmd_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }
 


/*
if($cmd_datatype[$i] == 'Float' && $cmd_valuedefinition[$i] == 'Fixed Value'){
  $integer_value = $as_per_ui_value;
}elseif($cmd_datatype[$i] == 'UnsignedInt' && $cmd_valuedefinition[$i] == 'Fixed Value') {
  $integer_value = $as_per_ui_value;
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
*/
if($cmd_uitype[$i] === 'Toggle Switch' || $cmd_uitype[$i] === 'Notification'){


$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';    
    }


elseif ($cmd_uitype[$i] === 'Slider' || $cmd_uitype[$i] === 'Hue Slider'  || $cmd_uitype[$i] === 'Hue Slider Custom'  || $cmd_uitype[$i] === 'Brightness Slider'  || $cmd_uitype[$i] === 'Airflow Slider') {
     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
        } elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);     
            }else{ 
                $integervalue = "null";  
            }
        }
    
    }elseif ($cmd_uitype[$i] === 'Drop Down') {



     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
           // if($as_per_ui_value !== 'NULL'){
           

$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

$ui_toggle_drop = explode('][', trim($asperui_values, '[]')); 
$ui_toggle_value_drop = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map_drop = array_combine($ui_toggle_value_drop,$ui_toggle_drop);

$as_per_ui_value =isset($dropdown_map_drop[$as_per_ui_value]) ? $dropdown_map_drop[$as_per_ui_value] : '';



  
           /* }else{ 
                $integervalue = "null";  
            }*/
        } elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);    
            }else{ 
                $integervalue = "null";  
            }
        }
    
    }elseif ($cmd_uitype[$i]  === 'Text') {
     
        if ($cmd_datatype[$i] === 'String') {
                if($as_per_ui_value !== 'NULL'){
                 //  echo $as_per_ui;
                   // if($as_per_ui != 'NULL')
                     if(!empty($as_per_ui))
                   {  
                  

                     if($cmd_defaultvalue[$i] == 'name')
                      {
                         
                        $integervalue=$as_per_ui_value;
                      }

                      else{



                     $ui_toggle = explode('][', trim($as_per_ui, '[]'));
                     $ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
                     $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                     $as_per_ui_value = (string)$as_per_ui_value;  
                     $as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;
                     $integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

                       }

                   }
                   else{
                    $integervalue = $as_per_ui_value;
                   }
                }else{
                    $integervalue = "null";  
            }  
         }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
         }elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){


              //  $integervalue = hexToFloat($as_per_ui_value);  
                 $cmdfloat_value = hexToFloat($as_per_ui_value); 
                // $integervalue= $cmdfloat_value; 
              // $integervalue= sprintf("%.2f", $cmdfloat_value);   
                $integervalue = number_format($cmdfloat_value, 6, '.', '');
            }else{ 

            
                $integervalue = "null";  
            }
         }else{
            if($as_per_ui_value !== 'NULL'){
                $integervalue = $as_per_ui_value;  
            }else{
                $integervalue = "null";  
            }
         }
    
    }else{
        if($as_per_ui_value !== 'NULL'){
            $integervalue = $as_per_ui_value;  
        }else{
            $integervalue = "null";  
        }
    }
 


    if ($cmd_datatype[$i] === 'Float') {
        // $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
         // $integervalue = hexToFloat($integer_value);
         // $integervalue = empty($integer_value) ? (float)$integer_value :  0.0;
         $cmd_value2 = floatToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
         $cmd_sliderstep = (float)$cmd_sliderstepvalue[$i];
        $display_min = (float)$cmd_displayvaluemin[$i];
        $display_max = (float)$cmd_displayvaluemax[$i];
     }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
        if($cmd_uitype[$i] == 'Toggle Switch'){
            $ui_toggle2 = explode('][', trim($as_per_ui, '[]'));
    $ui_toggle_value2 = explode('][', trim($asperui_values, '[]'));
            $dropdown_map22 = array_combine($ui_toggle2,$ui_toggle_value2);  
            $cmd_value = isset($dropdown_map22[$integervalue]) ? $dropdown_map22[$integervalue] : '';  
            //print_r($cmd_value);
            $display_min = (int)$cmd_displayvaluemin[$i];
            $display_max = (int)$cmd_displayvaluemax[$i];
        }else{
      //  $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
      //   $integervalue = hexToInt($integer_value);
         $cmd_value2 = intToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
        $cmd_sliderstep = (int)$cmd_sliderstepvalue[$i];
        $display_min = (int)$cmd_displayvaluemin[$i];
        $display_max = (int)$cmd_displayvaluemax[$i];
        }
    }elseif ($cmd_datatype[$i] === 'String') {
      // $integervalue = (string)$integer_value;
$cmd_sliderstep = '';
          $display_min = (int)$cmd_displayvaluemin[$i];
        $display_max = (int)$cmd_displayvaluemax[$i];

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
    "value" => $as_per_ui_value,
    "Field_description" => $cmd_fielddescription[$i],
    "type" => $cmd_valuedefinition[$i],
    "data_type" => $cmd_datatype[$i],
    "no_of_bytes" => (int)$cmd_noofbytes[$i],
    "byte_start_position" => (int)$cmd_startbytePosition[$i],
    "ui_type"  => $cmd_uitype[$i],
    "local_control" => $local_control,
    "ui_ids"   => (int)$cmd_uiid[$i],
    "asperui" => $as_per_ui_array_from_formatted,  
    "sno_asperui" => $sno_as_per_ui_array_from_formatted,  
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
            "local_control" => $local_control
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
            "local_control" => $local_control,
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
                    "local_control" => $local_control,
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

$sno_as_per_ui = $resp_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
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
 
 
   
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($resp_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }



/*
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
*/
if(($resp_uiType[$i] === 'Toggle Switch') || ($resp_uiType[$i] === 'Notification')) {
// print_r($as_per_ui);
 //print_r($asperui_values);
//print_r($as_per_ui_value);
$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));

$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);
  
$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';  
    
    }elseif ($resp_uiType[$i] === 'Slider' || $resp_uiType[$i] === 'Hue Slider'  || $resp_uiType[$i] === 'Hue Slider Custom'  || $resp_uiType[$i] === 'Brightness Slider'  || $resp_uiType[$i] === 'Airflow Slider') {
     
        if ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i] === 'Drop Down') {
   
        if ($resp_dataType[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                              
      /*         $ui_toggle = explode('][', trim($asperui_values, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));



$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);



$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;


//$as_per_ui_value = (string)$asperui_values[$i];        
//$as_per_ui_value = strlen($asperui_values[$i]) == 1 ? '0' . $asperui_values[$i] : $asperui_values[$i];


//$resp_integervalue = isset($dropdown_map[asperui_values[$i]]) ? $dropdown_map[asperui_values[$i]] : '';    


$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

*/

$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 



$ui_toggle_drop = explode('][', trim($asperui_values, '[]')); 
$ui_toggle_value_drop = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map_drop = array_combine($ui_toggle_value_drop,$ui_toggle_drop);





$resp_value =isset($dropdown_map_drop[$as_per_ui_value]) ? $dropdown_map_drop[$as_per_ui_value] : '';




//echo $resp_value;



            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $resp_integervalue= sprintf("%.2f", $cmdfloat_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i]  === 'Text') {
     
        if ($resp_dataType[$i] === 'String') {
                if(!empty($as_per_ui_value)){
                 //   $resp_integervalue = $as_per_ui_value;

                       if(!empty($as_per_ui))
                   {  
                     $ui_toggle = explode('][', trim($as_per_ui, '[]'));
                     $ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
                     $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                     $as_per_ui_value = (string)$as_per_ui_value;  
                     $as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;
                     $resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 
                   }
                   else{
                    $resp_integervalue = $as_per_ui_value;
                   }





                }else{
                    $resp_integervalue = "null";  
            }  
         }elseif ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
         }elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
         }else{
            if(!empty($as_per_ui_value)){
                $resp_integervalue = $as_per_ui_value;  
            }else{
                $resp_integervalue = "null";  
            }
         }
    
    }else{
        if(!empty($as_per_ui_value)){
            $resp_integervalue = $as_per_ui_value;  
        }else{
            $resp_integervalue = "null";  
        }
    }
   
  
    if ($resp_dataType[$i] === 'Float') {
        //$asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (float)$integer_value;
        $resp_value2 = floatToHex($resp_integervalue);
         $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderstep = (float)$resp_sliderstepvalue[$i];
    } elseif ($resp_dataType[$i] === 'UnsignedInt') {
       // $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (int)$integer_value;

         if($resp_uiType[$i] != 'Drop Down')
         {  
             
         $resp_value2 = intToHex($resp_integervalue);
          $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderStep = (int)$resp_sliderStepValue[$i];

         }
    }elseif($resp_dataType[$i] === 'String'){
     // $respintegervalue = (string)$integer_value;
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
                    "local_control" => $local_control,
                    "ui_ids" => (int)$resp_uiId[$i],
                    "asperui" => $as_per_ui_array_from_formatted,
                    "sno_asperui" => $sno_as_per_ui_array_from_formatted,
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
        } 









else {


if($resp_uiType[$i] == 'None')
{
            $field_resp = [
                "default_name" => $resp_defaultvalue[$i],
                "value" => $resp_valueInHexMin[$i],
                "Field_description" => $resp_fieldDescription[$i],
                "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                "byte_start_position" => (int)$resp_startBytePosition[$i],
                "ui_type" => $resp_uiType[$i],
                "local_control" => $local_control
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
                "local_control" => $local_control,
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

   

$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($error_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }


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
            "local_control" => $local_control,
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



unset($para_result['']);


/*

if($model_name == 'EF-003FMN03')
{




// Remove the unwanted pages after processing

//unset($para_result['Device Communication']);
//unset($para_result['Status']);

unset($para_result['Configure.Config']);
unset($para_result['Remote Address']);
unset($para_result['Remote Pair.Pairing']);




$status='disable';

}
else {

$status='enable';
//$status='disable';
}


 //02-06-25



if($model_name == 'EF-003FMN03')
{



$status='enable';                           //test_nodes--status

}
else {

$status='enable';
//$status='disable';
}
*/


/***************************/

if ($mortgage === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Mortgage') {
 unset($para_result[$key]);
                   }
    }
}

if ($status === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Status') {
 unset($para_result[$key]);
                   }
    }
}




if ($device_comm_page === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Device Communication') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_pair_pairing === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Pair.Pairing') {
 unset($para_result[$key]);
                   }
    }
}


if ($config_config === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Configure.Config') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_address === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Address') {
 unset($para_result[$key]);
                   }
    }
}




/**************************/

 $query_unread = "SELECT COUNT(*) AS `unread_count` FROM call_service WHERE node_id = '$node_id' AND status = 'unread'";
$row_unread = $db->get_row($query_unread);

// Convert object to array and access key safely
$unread_count = '0';
if ($row_unread) {
    foreach ($row_unread as $key => $val) {
        if (trim($key) === 'unread_count') {
            $unread_count = trim((string)$val); // Ensure it's a trimmed string
        }
    }
}


if ($unread_count === '' || $unread_count === null) {
    $unread_count = '0';
}



$attributes = [
    [
        "name" => "Device Id",
        "value" => $node_id
    ],
    [
        "name" => "mac",
        "value" => $mac_value
    ]
];



    $devices = [
        [
            "name" => $curr_device_type,
            "total_pages" => $number_of_pages,
            "attributes" => $attributes,
            "Device Id" => $node_id,
            "pages" => $para_result,               
            "primary" => "power",
            "type" => $type,
            "brand_logo" => "https://efactor.tech/files/brand_logo/" . $brandname . ".jpg",
             "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
            "chat_status"=>$unread_count,
            "device_image" => $device_image,
            "device_p2p_control"=>"disable",
             
                "Device Communication"=>$device_comm_page,
                "Status"=>$status

        ]
    ];
} else {



    if ($is_added) {


        $query_gateway = "SELECT * FROM `efactorproduct` WHERE add_gateway ='$add_gateway' AND is_added='$is_added'"; 
        $single_node_dynamic_gateway = $db->get_results($query_gateway);
        $row_node_gateway = $db->get_row($query_gateway);
        $node_id = $row_node_gateway["node_id"];
        $module_type_gate = $row_node_gateway["module_type"];

        $query_gateway_11 = "SELECT node_id FROM `efactorproduct` WHERE add_gateway ='$add_gateway'"; 
        $all_devices_dynamic = $db->get_results($query_gateway_11);

//print_r($all_devices_dynamic);

        foreach ($all_devices_dynamic as $node_gate) {



            $nodeid = $node_gate["node_id"];

            $query_node = "SELECT * FROM efactorproduct WHERE node_id='$nodeid'";
            $single_node_dynamic = $db->get_results($query_node);
            $row_node = $db->get_row($query_node);

            $model_name = $row_node['model_number'];
            $current_devicetype = $row_node['current_devicetype'];
            $module_type = $row_node['module_type'];
            $device_type = $row_node['device_type'];
            $nett_conn = $row_node['internet'];
            $access_token = $row_node['access_token'];
            $vendor_name = $row_node['vendor_name'];
            $add_gateway = $row_node['add_gateway'];
            $is_added = $row_node['is_added'];
            $userid = $row_node['user_id'];

            $node_id = $row_node['node_id'];






if($model_name == 'EFDEFLT V1.0'){
    $query_node="SELECT * FROM efactorproduct where node_id='$node_id'";
}else{
$query_node="SELECT * FROM `$model_name` where node_id='$node_id'";
}
			$doctors_node = $db->get_results($query_node); 
			//$num_node = $db->num_rows($query_node);
                        $dbcker = $db->get_row($query_node);    
                       // print_r($dbcker);
		      //  $nodeid=$dbcker['node_id'];
                      //  $user_id=$dbcker['user_id'];
                       // $model_name=$dbcker['model_name'];
                           $curr_device_type=$dbcker['current_devicetype'];
                           $devicetype=$dbcker['device_type'];
                           $mortgage=$dbcker['Mortgage_page'];
                           $status=$dbcker['status_page'];
                           $device_comm_page=$dbcker['device_comm_page'];
                           $remote_pair_pairing=$dbcker['Remote_Pair_Pairing'];
                           $config_config=$dbcker['Configure_Config'];
                           $remote_address=$dbcker['Remote_Address'];


                               
    $type="efactor.device.$devicetype"; 




/*-----------------------------------------------*/

// Determine device image
        if ($model_name == 'EF-003FMN03' || $model_name == 'EF-002FMN02' || $model_name == 'EF-001FMN01' || $model_name == 'BAJAJ-001FMN01') {
            $deviceimage = 'fan-EF-003FMN03.jpg';
        } elseif ($model_name == 'EFCKER V1.0') {
            $deviceimage = 'efact_cooker.jpg';
        } elseif ($model_name == 'EF-001LIGHT01' || $model_name == 'EF-002CWLIGHT02' || $model_name == 'EF-001RGBLIGHT01' || $model_name == 'EF-003WLIGHT03') {
            $deviceimage = 'light_org.jpg';
        } elseif ($model_name == 'EF-001OULT01' || $model_name == 'EF-001SOCKET_6A01' || $model_name == 'EF-001SOCKET_10A01') {
            $deviceimage = 'outlet.jpg';
        } elseif ($model_name == 'EFSWPCP V1.0') {
            $deviceimage = 'swpcp.jpg';
        } elseif ($model_name == 'INVENDIS_MFM_V1') {
            $deviceimage = 'invendis_device.jpg';
        }

elseif ($model_name == 'EF_RF_GATEW V1.0') {
            $deviceimage = 'gateway_org.jpg';
        }




else {
            $deviceimage = 'input_output.jpg';
        }


 $device_image = "https://efactor.tech/files/brand_logo/" . $deviceimage;



/*-----------------------------------------------*/













//echo $type;                        
            
                                
                                         
  

//$query = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND user_id = '177'";
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

        



     //   EFDEFLT V1.0
   
$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFDEFLT V1.1'";
$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";
//$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";

$result_1 = $db->get_results($query_1); 
$result_2 = $db->get_results($query_2); 

$result_3 = $db->num_rows($query_1);
$result_4 = $db->num_rows($query_2);

$result_5 = $db->get_row($query_1); 
$result_6 = $db->get_row($query_2); 

$doctors = array_merge($result_1, $result_2);
$num_11 = $result_3 + $result_4;
$row = array_merge($result_1, $result_2);
$parameter_name = $row['parameter_name'];
   /*
        $query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";

      $doctors = $db->get_results($query_1); 
        $num_11 = $db->num_rows($query_1);
        $row = $db->get_row($query_1); 
       $parameter_name = $row['parameter_name'];

    */
       

//print_r($row);

//$parameters = ['power', 'speed', 'led', 'direction'];

// Array to store the final result
$result = [];



 
foreach ($doctors as $param) {
   
    $fields_cmd = [];
    $fields_resp = [];
    $fields_error = [];

$param1=$param['parameter_name'];


 
//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFFN02' AND user_id = '177' AND parameter_name='$param1'";
$query_param1 = "SELECT * FROM product_api WHERE model_name = 'EFDEFLT V1.1' AND parameter_name='$param1'";
$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
  //$query_param = "SELECT * FROM product_api WHERE model_name = 'EFACTDFLT' AND parameter_name='$param1'";

 //$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND parameter_name='$param1'";

$result_1 = $db->get_results($query_param1); 
$result_2 = $db->get_results($query_param); 

$result_3 = $db->num_rows($query_param1);
$result_4 = $db->num_rows($query_param);

$row_param1 = $db->get_row($query_param1); 
$row_param2 = $db->get_row($query_param); 
  

$params = array_merge($result_1, $result_2);
$num_param = $result_3 + $result_4;
$row_param = array_merge((array)$row_param1, (array)$row_param2);
 
 //$row_param = array_merge($result_5, $result_6);
/*
    $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";

        $params = $db->get_results($query_param); 
        $num_param = $db->num_rows($query_param);
          $row_param = $db->get_row($query_param); 
      //  print_r($row_param1);
*/
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
            $cmd_sno_asperui = explode(',', $row_param['cmd_sno_asperui']);
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
            $resp_sno_asperui = explode(',', $row_param['resp_sno_asperui']);
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
            $error_sno_asperui = explode(',', $row_param['error_sno_asperui']);
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



//if($page_names == 'Configure' || $page_names == 'Remote Pair')
 if($page_names == 'Configure' || $page_names == 'Remote Pair' || $page_names == 'Configure.Config' || $page_names == 'Set Max Current' || $page_names == 'Device Setting'){

if($parameter_name == 'wifi config' || $parameter_name == 'DAC')


{
$local_control ='disable';       //disable

}
else{
 $local_control ='enable';       //disable

}

}else{

$local_control ='enable';
}



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
            "local_control" => $local_control,
            "ui_ids"   => (int)$cmd_uiid[$i],
            "ui_description" => $cmd_uidescriptioninout[$i]
            ];


//$fields_cmd[] = $field_cmd;



          }elseif($cmd_uitype[$i] == 'None'){ 
            $field_cmd = [
               "default_name" =>$cmd_defaultvalue[$i],
               "value" => $cmd_valueinhexmin[$i],
               "Field_description" => $cmd_fielddescription[$i],
               "no_of_bytes" => (int)$cmd_noofbytes[$i],
               "byte_start_position" => (int)$cmd_startbytePosition[$i] + 27,
               "ui_type"  => $cmd_uitype[$i],
               "local_control" => $local_control
        ]; 

      }else{




        
$as_per_ui = $cmd_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

if (empty($as_per_ui_array_from_formatted) || $as_per_ui_array_from_formatted === ['']) {
    $as_per_ui_array_from_formatted = [];  
}

 

$sno_as_per_ui = $cmd_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
}

$asperui_values = $cmd_asperui_values[$i];
$asperui_values_array = explode('][', trim($asperui_values, '[]'));
$formatted_asperui_values = implode(',', $asperui_values_array);
$asperui_values_array_from_formatted = explode(',', $formatted_asperui_values);
 
if (empty($asperui_values_array_from_formatted) || $asperui_values_array_from_formatted === ['']) {
    $asperui_values_array_from_formatted = [];  
}


/*
if ($param1 =='Speed') {
    $as_per_ui_value =  $speed;
} 
*/
 
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($cmd_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }
 


/*
if($cmd_datatype[$i] == 'Float' && $cmd_valuedefinition[$i] == 'Fixed Value'){
  $integer_value = $as_per_ui_value;
}elseif($cmd_datatype[$i] == 'UnsignedInt' && $cmd_valuedefinition[$i] == 'Fixed Value') {
  $integer_value = $as_per_ui_value;
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
*/


if($cmd_uitype[$i] === 'Toggle Switch' || $cmd_uitype[$i] === 'Notification'){
$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';    
    }


elseif ($cmd_uitype[$i] === 'Slider' || $cmd_uitype[$i] === 'Hue Slider'  || $cmd_uitype[$i] === 'Hue Slider Custom'  || $cmd_uitype[$i] === 'Brightness Slider'  || $cmd_uitype[$i] === 'Airflow Slider') {
     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
        } elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);     
            }else{ 
                $integervalue = "null";  
            }
        }
    
    }elseif ($cmd_uitype[$i] === 'Drop Down') {



     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
           // if($as_per_ui_value !== 'NULL'){

        //  echo $as_per_ui_value;   

$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

$ui_toggle_drop = explode('][', trim($asperui_values, '[]')); 
$ui_toggle_value_drop = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map_drop = array_combine($ui_toggle_value_drop,$ui_toggle_drop);

$as_per_ui_value =isset($dropdown_map_drop[$as_per_ui_value]) ? $dropdown_map_drop[$as_per_ui_value] : '';



  
           /* }else{ 
                $integervalue = "null";  
            }*/
        } elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);    
            }else{ 
                $integervalue = "null";  
            }
        }
    
    }elseif ($cmd_uitype[$i]  === 'Text') {
     
        if ($cmd_datatype[$i] === 'String') {
                if($as_per_ui_value !== 'NULL'){
                 //  echo $as_per_ui;
                   // if($as_per_ui != 'NULL')
                     if(!empty($as_per_ui))
                   {  
                  

                     if($cmd_defaultvalue[$i] == 'name')
                      {
                         
                        $integervalue=$as_per_ui_value;
                      }

                      else{



                     $ui_toggle = explode('][', trim($as_per_ui, '[]'));
                     $ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
                     $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                     $as_per_ui_value = (string)$as_per_ui_value;  
                     $as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;
                     $integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

                       }

                   }
                   else{
                    $integervalue = $as_per_ui_value;
                   }
                }else{
                    $integervalue = "null";  
            }  
         }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
         }elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){


              //  $integervalue = hexToFloat($as_per_ui_value);  
                 $cmdfloat_value = hexToFloat($as_per_ui_value); 
                // $integervalue= $cmdfloat_value; 
              // $integervalue= sprintf("%.2f", $cmdfloat_value);   
                $integervalue = number_format($cmdfloat_value, 6, '.', '');
            }else{ 

            
                $integervalue = "null";  
            }
         }else{
            if($as_per_ui_value !== 'NULL'){
                $integervalue = $as_per_ui_value;  
            }else{
                $integervalue = "null";  
            }
         }
    
    }else{
        if($as_per_ui_value !== 'NULL'){
            $integervalue = $as_per_ui_value;  
        }else{
            $integervalue = "null";  
        }
    }
 


    if ($cmd_datatype[$i] === 'Float') {
        // $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
         // $integervalue = hexToFloat($integer_value);
         // $integervalue = empty($integer_value) ? (float)$integer_value :  0.0;
         $cmd_value2 = floatToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
         $cmd_sliderstep = (float)$cmd_sliderstepvalue[$i];
        $display_min = (float)$cmd_displayvaluemin[$i];
        $display_max = (float)$cmd_displayvaluemax[$i];
     }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
        if($cmd_uitype[$i] == 'Toggle Switch'){
            $ui_toggle2 = explode('][', trim($as_per_ui, '[]'));
    $ui_toggle_value2 = explode('][', trim($asperui_values, '[]'));
            $dropdown_map22 = array_combine($ui_toggle2,$ui_toggle_value2);  
            $cmd_value = isset($dropdown_map22[$integervalue]) ? $dropdown_map22[$integervalue] : '';  
            //print_r($cmd_value);
            $display_min = (int)$cmd_displayvaluemin[$i];
            $display_max = (int)$cmd_displayvaluemax[$i];
        }else{
      //  $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
      //   $integervalue = hexToInt($integer_value);
         $cmd_value2 = intToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
        $cmd_sliderstep = (int)$cmd_sliderstepvalue[$i];
        $display_min = (int)$cmd_displayvaluemin[$i];
        $display_max = (int)$cmd_displayvaluemax[$i];
        }
    }elseif ($cmd_datatype[$i] === 'String') {
      // $integervalue = (string)$integer_value;
$cmd_sliderstep = '';
          $display_min = (int)$cmd_displayvaluemin[$i];
        $display_max = (int)$cmd_displayvaluemax[$i];

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
    "value" => $as_per_ui_value,
    "Field_description" => $cmd_fielddescription[$i],
    "type" => $cmd_valuedefinition[$i],
    "data_type" => $cmd_datatype[$i],
    "no_of_bytes" => (int)$cmd_noofbytes[$i],
    "byte_start_position" => (int)$cmd_startbytePosition[$i],
    "ui_type"  => $cmd_uitype[$i],
    "local_control" => $local_control,
    "ui_ids"   => (int)$cmd_uiid[$i],
    "asperui" => $as_per_ui_array_from_formatted,  
    "sno_asperui" => $sno_as_per_ui_array_from_formatted,  
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
            "local_control" => $local_control
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
            "local_control" => $local_control,
            "ui_description" => $cmd_uidescriptioninout[$i],
            "ui_ids"   => (int)$cmd_uiid[$i] 
           
                  ];

$fields_cmd[] = $field_cmd;


}

}

}

}




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
                    "local_control" => $local_control,
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

$sno_as_per_ui = $resp_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
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
 
 
   
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($resp_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }



/*
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
*/
if(($resp_uiType[$i] === 'Toggle Switch') || ($resp_uiType[$i] === 'Notification')) {
// print_r($as_per_ui);
 //print_r($asperui_values);
//print_r($as_per_ui_value);
$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));

$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);
  
$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';  
    
    }elseif ($resp_uiType[$i] === 'Slider' || $resp_uiType[$i] === 'Hue Slider'  || $resp_uiType[$i] === 'Hue Slider Custom'  || $resp_uiType[$i] === 'Brightness Slider'  || $resp_uiType[$i] === 'Airflow Slider') {
     
        if ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i] === 'Drop Down') {
   
        if ($resp_dataType[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                              
      /*         $ui_toggle = explode('][', trim($asperui_values, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));



$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);



$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;


//$as_per_ui_value = (string)$asperui_values[$i];        
//$as_per_ui_value = strlen($asperui_values[$i]) == 1 ? '0' . $asperui_values[$i] : $asperui_values[$i];


//$resp_integervalue = isset($dropdown_map[asperui_values[$i]]) ? $dropdown_map[asperui_values[$i]] : '';    


$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

*/

$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 



$ui_toggle_drop = explode('][', trim($asperui_values, '[]')); 
$ui_toggle_value_drop = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map_drop = array_combine($ui_toggle_value_drop,$ui_toggle_drop);





$resp_value =isset($dropdown_map_drop[$as_per_ui_value]) ? $dropdown_map_drop[$as_per_ui_value] : '';




//echo $resp_value;



            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $resp_integervalue= sprintf("%.2f", $cmdfloat_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i]  === 'Text') {
     
        if ($resp_dataType[$i] === 'String') {
                if(!empty($as_per_ui_value)){
                 //   $resp_integervalue = $as_per_ui_value;

                       if(!empty($as_per_ui))
                   {  
                     $ui_toggle = explode('][', trim($as_per_ui, '[]'));
                     $ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
                     $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                     $as_per_ui_value = (string)$as_per_ui_value;  
                     $as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;
                     $resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 
                   }
                   else{
                    $resp_integervalue = $as_per_ui_value;
                   }





                }else{
                    $resp_integervalue = "null";  
            }  
         }elseif ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
         }elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
         }else{
            if(!empty($as_per_ui_value)){
                $resp_integervalue = $as_per_ui_value;  
            }else{
                $resp_integervalue = "null";  
            }
         }
    
    }else{
        if(!empty($as_per_ui_value)){
            $resp_integervalue = $as_per_ui_value;  
        }else{
            $resp_integervalue = "null";  
        }
    }
   
  
    if ($resp_dataType[$i] === 'Float') {
        //$asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (float)$integer_value;
        $resp_value2 = floatToHex($resp_integervalue);
         $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderstep = (float)$resp_sliderstepvalue[$i];
    } elseif ($resp_dataType[$i] === 'UnsignedInt') {
       // $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (int)$integer_value;

         if($resp_uiType[$i] != 'Drop Down')
         {  
             
         $resp_value2 = intToHex($resp_integervalue);
          $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderStep = (int)$resp_sliderStepValue[$i];

         }
    }elseif($resp_dataType[$i] === 'String'){
     // $respintegervalue = (string)$integer_value;
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
                    "local_control" => $local_control,
                    "ui_ids" => (int)$resp_uiId[$i],
                    "asperui" => $as_per_ui_array_from_formatted,
                    "sno_asperui" => $sno_as_per_ui_array_from_formatted,
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
        } 









else {


if($resp_uiType[$i] == 'None')
{
            $field_resp = [
                "default_name" => $resp_defaultvalue[$i],
                "value" => $resp_valueInHexMin[$i],
                "Field_description" => $resp_fieldDescription[$i],
                "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                "byte_start_position" => (int)$resp_startBytePosition[$i],
                "ui_type" => $resp_uiType[$i],
                "local_control" => $local_control
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
                "local_control" => $local_control,
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

   

$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($error_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }


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
            "local_control" => $local_control,
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

unset($para_result['']);

/*
if($model_name == 'EF-003FMN03')
{

// Remove the unwanted pages after processing
//unset($para_result['Device Communication']);
//unset($para_result['Status']);

unset($para_result['Configure.Config']);
unset($para_result['Remote Address']);
unset($para_result['Remote Pair.Pairing']);

if($mortgage === 'disable')
{



unset($para_result['Mortgage_page']);

}

$status='disable';

}

else{

$status='enable';

}

  //02-06-25


if($model_name == 'EF-003FMN03')
{

$status='enable';            //test_nodes--status

}

else{

$status='enable';

}

*/



if ($mortgage === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Mortgage') {
 unset($para_result[$key]);
                   }
    }
}

if ($status === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Status') {
 unset($para_result[$key]);
                   }
    }
}




if ($device_comm_page === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Device Communication') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_pair_pairing === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Pair.Pairing') {
 unset($para_result[$key]);
                   }
    }
}


if ($config_config === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Configure.Config') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_address === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Address') {
 unset($para_result[$key]);
                   }
    }
}




/**************************/






 $nodeid = $node_gate["node_id"];

            $query_node = "SELECT * FROM efactorproduct WHERE node_id='$nodeid'";
            $single_node_dynamic = $db->get_results($query_node);
            $row_node = $db->get_row($query_node);

            $model_name = $row_node['model_number'];
            $current_devicetype = $row_node['current_devicetype'];
            $module_type = $row_node['module_type'];
            $device_type = $row_node['device_type'];
            $nett_conn = $row_node['internet'];
            $access_token = $row_node['access_token'];
            $vendor_name = $row_node['vendor_name'];
            $add_gateway = $row_node['add_gateway'];
           // $is_added = $row_node['is_added'];
            $userid = $row_node['user_id'];












$query_unread = "SELECT COUNT(*) AS `unread_count` FROM call_service WHERE node_id = '$node_id' AND status = 'unread'";
$row_unread = $db->get_row($query_unread);

// Convert object to array and access key safely
$unread_count = '0';
if ($row_unread) {
    foreach ($row_unread as $key => $val) {
        if (trim($key) === 'unread_count') {
            $unread_count = trim((string)$val); // Ensure it's a trimmed string
        }
    }
}


if ($unread_count === '' || $unread_count === null) {
    $unread_count = '0';
}






$attributes = [
    [
        "name" => "Device Id",
        "value" => $nodeid
    ],
    [
        "name" => "mac",
        "value" => $mac_value
    ]
];

            $devices_gate = [
                
                    "name" => $curr_device_type,
                    "total_pages" => $number_of_pages,
                    "attributes" => $attributes,
                    "Device Id" => $nodeid,
                    "pages" => $para_result,               
                    "primary" => "power",
                    "type" => $type,
                    "brand_logo" => "https://efactor.tech/files/brand_logo/" . $brandname . ".jpg",
                     "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
                     "chat_status"=>$unread_count,
                    "device_image" => $device_image,
                    "device_p2p_control"=>"disable",
                "Device Communication"=>$device_comm_page,
                "Status"=>$status
                
            ];




            $devices[] = $devices_gate; // Add the device to the devices array
        }
    }
}



$url_params = "model_name={$model_number}&user_id={$user_id}";
$auto_url_params = "model_name={$model_number}&user_id={$user_id}&node_id={$node_id}";


 $query_unread = "SELECT COUNT(*) AS `unread_count` FROM call_service WHERE node_id = '$node_id' AND status = 'unread'";
$row_unread = $db->get_row($query_unread);

// Convert object to array and access key safely
$unread_count = '0';
if ($row_unread) {
    foreach ($row_unread as $key => $val) {
        if (trim($key) === 'unread_count') {
            $unread_count = trim((string)$val); // Ensure it's a trimmed string
        }
    }
}


if ($unread_count === '' || $unread_count === null) {
    $unread_count = '0';
}






if($is_added_gate != 'NULL')

{

$node_details = array(
    "id" => $add_gateway,
    
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
        "devices" => $devices,
        "info" => array(
            "fw_version" => "1.5.1",
            "model" => $model_number,
            "name" => $curr_devicetype,
            "type" => $moduletype,
            "SSID" => $ssid,
            "Access_Point" => $access_point,
            "Guarantee_Card" => "https://efactor.tech/v8/api.php?action=guarantee_card_display&{$url_params}",
           // "Video" => "https://efactor.tech/v8/api.php?action=app_video&{$url_params}",
            "Video" => "https://efactor.tech/v8/user_guide_support.php",
            "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
            "chat_status"=>$unread_count,
            "Auto_Test" => "https://efactor.tech/v8/auto_test.php?{$auto_url_params}"

        ),


        "node_id" => $add_gateway
    ),
    "role" => "primary",
    "status" => array(
        "connectivity" => array(
            "connected" => ($net_conn_1 === "true") ? true : false,
            "timestamp" => $timestamp
        )
    )
);



}

else
{



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
        "devices" => $devices,
        "info" => array(
            "fw_version" => "1.5.1",
            "model" => $model_number,
            "name" => $curr_devicetype,
            "type" => $moduletype,
            "SSID" => $ssid,
            "Access_Point" => $access_point,
            "Guarantee_Card" => "https://efactor.tech/v8/api.php?action=guarantee_card_display&{$url_params}",
           // "Video" => "https://efactor.tech/v8/api.php?action=app_video&{$url_params}",
            "Video" => "https://efactor.tech/v8/user_guide_support.php",
            "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
            "chat_status"=>$unread_count,
            "Auto_Test" => "https://efactor.tech/v8/auto_test.php?{$auto_url_params}"

        ),


        "node_id" => $node_id
    ),
    "role" => "primary",
    "status" => array(
        "connectivity" => array(
            "connected" => ($net_conn_1 === "true") ? true : false,
            "timestamp" => $timestamp
        )
    )
);


}




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




$end_time = microtime(true);
$execution_time = $end_time - $start_time;
//echo "Execution time: " . $execution_time . " seconds"; 



if($is_added_gate)

{
echo json_encode(array("node_details" => array($node_details), "nodes" => $add_gateway));
}
else
{

echo json_encode(array("node_details" => array($node_details), "nodes" => $node_id));
}

//echo $node;





exit;



/************************************************************/

    }

}






















/******************************************************************************/







if (isset($_GET['action']) && $_GET['action']=='test_nodes_app')
{

//$startid =$_GET['start_id'];


$node_id =$_GET['node_id'];

$start_time = microtime(true);
                        
			       
			$header = apache_request_headers();  
			foreach ($header as $key => $val) {
			
			$query_dev="SELECT * FROM efactorproduct where node_id='$node_id'";
			$doctors = $db->get_results($query_dev); 
			$num_dev = $db->num_rows($query_dev);
                        $row_dev = $db->get_row($query_dev);    
		       $device_type=$row_dev['device_type'];
                       $model_name=$row_dev['model_number'];



/*

if(empty($_GET["start_id"]))
{

// First check for nodes with add_gateway not NULL and is_added=1
$query_dev = "SELECT node_id FROM efactorproduct 
              WHERE access_token='$val' 
              AND add_gateway IS NOT NULL 
              AND add_gateway != 'NULL' 
              AND is_added=1 
              LIMIT 1";

$doctors = $db->get_results($query_dev);
$num_dev = $db->num_rows($query_dev);

if ($num_dev > 0) {
    // Found node with gateway conditions
    $row_dev = $db->get_row($query_dev);
    $node_id = $row_dev['node_id'];
} else {
    // Fall back to normal query if no gateway nodes found
    $query_dev = "SELECT node_id FROM efactorproduct 
                 WHERE access_token='$val' 
                 LIMIT 1";
    $doctors = $db->get_results($query_dev);
    $num_dev = $db->num_rows($query_dev);
    
    if ($num_dev > 0) {
        $row_dev = $db->get_row($query_dev);
        $node_id = $row_dev['node_id'];
    } else {
        $node_id = null; // No nodes found at all
    }
}
}


else
{



$node_id=$startid;
$query_4 = "SELECT * FROM efactorproduct where node_id='$node_id'";
							   
	$row_4 = $db->get_row($query_4);
	// $node_id=$row['node_id'];
        $device_type=$row_4['device_type'];
        $model_name=$row_4['model_number'];
        $add_gateway=$row_4['add_gateway'];
        $is_added=$row_4['is_added'];



if(!$add_gateway)
               
{
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





}


}
*/




$db->query("update efactorproduct set app_version='v8' where access_token='$val'");

/*
if (!empty($_GET["start_id"])) {
    // Case 1: When start_id is provided
    $current_node_id = $_GET["start_id"];
    
    // Get current device info
    $query_current = "SELECT * FROM efactorproduct WHERE node_id='$current_node_id'";
    $row_current = $db->get_row($query_current);
    
    if ($row_current) {
        $device_type = $row_current['device_type'];
        $model_name = $row_current['model_number'];
        
        // Determine if current device is part of a gateway group
        $gateway_id = ($row_current['is_added'] == '1' && $row_current['add_gateway'] == $current_node_id)
                    ? $current_node_id
                    : $row_current['add_gateway'];
        
        // Find next device that's not in current gateway group
        $query_next = "
            SELECT node_id 
            FROM efactorproduct 
            WHERE access_token = '$val' 
              AND (
                  (add_gateway IS NULL OR add_gateway = 'NULL' OR add_gateway != '$gateway_id')
                  OR 
                  (is_added = '1' AND add_gateway = node_id AND node_id != '$gateway_id')
              )
              AND node_id > '$current_node_id'
            ORDER BY node_id ASC
            LIMIT 1
        ";
        $row_next = $db->get_row($query_next);
        $nextid = $row_next ? $row_next['node_id'] : null;
    }
} else {
    // Case 2: When no start_id is provided
    
    $query_gateway = "
    SELECT node_id, device_type, model_number 
    FROM efactorproduct 
    WHERE access_token = '$val' 
      AND is_added = '1' 
      AND add_gateway = node_id
    ORDER BY node_id ASC
    LIMIT 1
";
$row_gateway = $db->get_row($query_gateway);

if ($row_gateway) {
   // echo 'Gateway found - use as current device';
    $device_id = $row_gateway['node_id'];
    $device_type = $row_gateway['device_type'];
    $model_name = $row_gateway['model_number'];
    
    // Try to find first linked device
    $query_linked = "
        SELECT node_id 
        FROM efactorproduct 
        WHERE access_token = '$val' 
          AND add_gateway = '$device_id'
          AND node_id != '$device_id'
        ORDER BY node_id ASC
        LIMIT 1
    ";
    $row_linked = $db->get_row($query_linked);
    
    if ($row_linked) {
        //echo 'Linked device found';
        $nextid = $row_linked['node_id'];
        
        // Now check if this nextid is linked to any gateway
        $check_gateway_link = "
            SELECT add_gateway 
            FROM efactorproduct 
            WHERE node_id = '$nextid'
            LIMIT 1
        ";
        $row_check = $db->get_row($check_gateway_link);
        
        // If linked to gateway, keep searching for next non-linked device
        if ($row_check && ($row_check['add_gateway'] !== null && $row_check['add_gateway'] != 'NULL')) {
         //   echo 'Linked device is connected to gateway, finding next non-linked device';
            
            $query_next_non_linked = "
                SELECT node_id 
                FROM efactorproduct 
                WHERE access_token = '$val' 
                  AND node_id > '$nextid'
                  AND (add_gateway IS NULL OR add_gateway = 'NULL')
                ORDER BY node_id ASC
                LIMIT 1
            ";
            $row_non_linked = $db->get_row($query_next_non_linked);
            $nextid = $row_non_linked ? $row_non_linked['node_id'] : null;
        }
    } else {
      //  echo 'No linked devices - find next device in database';
        $query_next = "
            SELECT node_id 
            FROM efactorproduct 
            WHERE access_token = '$val' 
              AND node_id > '$device_id'
              AND (add_gateway IS NULL OR add_gateway = 'NULL')
            ORDER BY node_id ASC
            LIMIT 1
        ";
        $row_next = $db->get_row($query_next);
        $nextid = $row_next ? $row_next['node_id'] : null;
    }
}
}


*/



/*-------------------------------------------------------------*/
/*************************************************************/


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
//$devicetype=$row_inter['device_type'];
$curr_devicetype=$row_inter['current_devicetype'];
$ssid=$row_inter['ssid'];
$mac_value=$row_inter['mac'];
$is_added=$row_inter['is_added'];
$add_gateway=$row_inter['add_gateway'];





$vendor_name=$row_inter['vendor_name'];

switch ($vendor_name) {
    case 'EFACTOR':
        $brandname = 'efactor';
        break;
    case 'BAJAJ':
        $brandname = 'Bajaj';
        break;
    case 'DHRIO':
        $brandname = 'dhrio';
        break;
    case 'Invendis':
        $brandname = 'Invendis';
        break;
    case 'Skytec':
        $brandname = 'Skytec';
        break;
    case 'exhaust_hood':
        $brandname = 'exhaust';
        break;
    default:
        $brandname = 'efactor';
        break;
}


/*

// Determine device image
        if ($model_name == 'EF-003FMN03' || $model_name == 'EF-002FMN02' || $model_name == 'EF-001FMN01' || $model_name == 'BAJAJ-001FMN01') {
            $deviceimage = 'fan-EF-003FMN03.jpg';
        } elseif ($model_name == 'EFCKER V1.0') {
            $deviceimage = 'efact_cooker.jpg';
        } elseif ($model_name == 'EF-001LIGHT01' || $model_name == 'EF-002CWLIGHT02' || $model_name == 'EF-001RGBLIGHT01' || $model_name == 'EF-003WLIGHT03') {
            $deviceimage = 'light_org.jpg';
        } elseif ($model_name == 'EF-001OULT01' || $model_name == 'EF-001SOCKET_6A01' || $model_name == 'EF-001SOCKET_10A01') {
            $deviceimage = 'outlet.jpg';
        } elseif ($model_name == 'EFSWPCP V1.0') {
            $deviceimage = 'swpcp.jpg';
        } elseif ($model_name == 'INVENDIS_MFM_V1') {
            $deviceimage = 'invendis_device.jpg';
        }

elseif ($model_name == 'EF_RF_GATEW V1.0') {
            $deviceimage = 'gateway_org.jpg';
        }



else {
            $deviceimage = 'input_output.jpg';
        }


 $device_image = "https://efactor.tech/files/brand_logo/" . $deviceimage;
*/

$new_prefix = "EWIFI";
$parts = explode("_", $curr_devicetype, 2);
$access_point = $new_prefix . "_" . $parts[1];
//echo $updated_devicetype; 











// Initialize devices array
$devices = [];

if ($add_gateway == 'NULL') {








if($model_name == 'EFDEFLT V1.0'){
    $query_node="SELECT * FROM efactorproduct where node_id='$node_id'";
}else{
$query_node="SELECT * FROM `$model_name` where node_id='$node_id'";
}
			$doctors_node = $db->get_results($query_node); 
			//$num_node = $db->num_rows($query_node);
                        $dbcker = $db->get_row($query_node);    
                       // print_r($dbcker);
		      //  $nodeid=$dbcker['node_id'];
                      //  $user_id=$dbcker['user_id'];
                       // $model_name=$dbcker['model_name'];
                           $curr_device_type=$dbcker['current_devicetype'];
                           $devicetype=$dbcker['device_type'];
                           $mortgage=$dbcker['Mortgage_page'];
                           $status=$dbcker['status_page'];
                           $device_comm_page=$dbcker['device_comm_page'];
                           $remote_pair_pairing=$dbcker['Remote_Pair_Pairing'];
                           $config_config=$dbcker['Configure_Config'];
                           $remote_address=$dbcker['Remote_Address'];


                               

    $type="efactor.device.$devicetype"; 




/*-----------------------------------------------*/

// Determine device image
        if ($model_name == 'EF-003FMN03' || $model_name == 'EF-002FMN02' || $model_name == 'EF-001FMN01' || $model_name == 'BAJAJ-001FMN01') {
            $deviceimage = 'fan-EF-003FMN03.jpg';
        } elseif ($model_name == 'EFCKER V1.0') {
            $deviceimage = 'efact_cooker.jpg';
        } elseif ($model_name == 'EF-001LIGHT01' || $model_name == 'EF-002CWLIGHT02' || $model_name == 'EF-001RGBLIGHT01' || $model_name == 'EF-003WLIGHT03') {
            $deviceimage = 'light_org.jpg';
        } elseif ($model_name == 'EF-001OULT01' || $model_name == 'EF-001SOCKET_6A01' || $model_name == 'EF-001SOCKET_10A01') {
            $deviceimage = 'outlet.jpg';
        } elseif ($model_name == 'EFSWPCP V1.0') {
            $deviceimage = 'swpcp.jpg';
        } elseif ($model_name == 'INVENDIS_MFM_V1') {
            $deviceimage = 'invendis_device.jpg';
        }

elseif ($model_name == 'EF_RF_GATEW V1.0') {
            $deviceimage = 'gateway_org.jpg';
        }




else {
            $deviceimage = 'input_output.jpg';
        }


 $device_image = "https://efactor.tech/files/brand_logo/" . $deviceimage;



/*-----------------------------------------------*/
















//echo $type;                        
                                         
  

//$query = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND user_id = '177'";
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

        



     //   EFDEFLT V1.0
   
$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFDEFLT V1.1'";
$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";
//$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";

$result_1 = $db->get_results($query_1); 
$result_2 = $db->get_results($query_2); 

$result_3 = $db->num_rows($query_1);
$result_4 = $db->num_rows($query_2);

$result_5 = $db->get_row($query_1); 
$result_6 = $db->get_row($query_2); 

$doctors = array_merge($result_1, $result_2);
$num_11 = $result_3 + $result_4;
$row = array_merge($result_1, $result_2);
$parameter_name = $row['parameter_name'];
   /*
        $query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";

      $doctors = $db->get_results($query_1); 
        $num_11 = $db->num_rows($query_1);
        $row = $db->get_row($query_1); 
       $parameter_name = $row['parameter_name'];

    */
       

//print_r($row);

//$parameters = ['power', 'speed', 'led', 'direction'];

// Array to store the final result
$result = [];



 
foreach ($doctors as $param) {
   
    $fields_cmd = [];
    $fields_resp = [];
    $fields_error = [];

$param1=$param['parameter_name'];


 
//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFFN02' AND user_id = '177' AND parameter_name='$param1'";
$query_param1 = "SELECT * FROM product_api WHERE model_name = 'EFDEFLT V1.1' AND parameter_name='$param1'";
$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
  //$query_param = "SELECT * FROM product_api WHERE model_name = 'EFACTDFLT' AND parameter_name='$param1'";

 //$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND parameter_name='$param1'";

$result_1 = $db->get_results($query_param1); 
$result_2 = $db->get_results($query_param); 

$result_3 = $db->num_rows($query_param1);
$result_4 = $db->num_rows($query_param);

$row_param1 = $db->get_row($query_param1); 
$row_param2 = $db->get_row($query_param); 
  

$params = array_merge($result_1, $result_2);
$num_param = $result_3 + $result_4;
$row_param = array_merge((array)$row_param1, (array)$row_param2);
 
 //$row_param = array_merge($result_5, $result_6);
/*
    $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";

        $params = $db->get_results($query_param); 
        $num_param = $db->num_rows($query_param);
          $row_param = $db->get_row($query_param); 
      //  print_r($row_param1);
*/
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
            $cmd_sno_asperui = explode(',', $row_param['cmd_sno_asperui']);
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
            $resp_sno_asperui = explode(',', $row_param['resp_sno_asperui']);
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
            $error_sno_asperui = explode(',', $row_param['error_sno_asperui']);
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



//if($page_names == 'Configure' || $page_names == 'Remote Pair')
 if($page_names == 'Configure' || $page_names == 'Remote Pair' || $page_names == 'Configure.Config' || $page_names == 'Set Max Current' || $page_names == 'Device Setting'){

if($parameter_name == 'wifi config' || $parameter_name == 'DAC')
{
$local_control ='disable';       //disable

}
else{
 $local_control ='enable';       //disable

}

}else{

$local_control ='enable';
}



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
            "local_control" => $local_control,
            "ui_ids"   => (int)$cmd_uiid[$i],
            "ui_description" => $cmd_uidescriptioninout[$i]
            ];


//$fields_cmd[] = $field_cmd;



          }elseif($cmd_uitype[$i] == 'None'){ 
            $field_cmd = [
               "default_name" =>$cmd_defaultvalue[$i],
               "value" => $cmd_valueinhexmin[$i],
               "Field_description" => $cmd_fielddescription[$i],
               "no_of_bytes" => (int)$cmd_noofbytes[$i],
               "byte_start_position" => (int)$cmd_startbytePosition[$i] + 27,
               "ui_type"  => $cmd_uitype[$i],
               "local_control" => $local_control
        ]; 

      }else{




        
$as_per_ui = $cmd_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

if (empty($as_per_ui_array_from_formatted) || $as_per_ui_array_from_formatted === ['']) {
    $as_per_ui_array_from_formatted = [];  
}

 

$sno_as_per_ui = $cmd_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
}

$asperui_values = $cmd_asperui_values[$i];
$asperui_values_array = explode('][', trim($asperui_values, '[]'));
$formatted_asperui_values = implode(',', $asperui_values_array);
$asperui_values_array_from_formatted = explode(',', $formatted_asperui_values);
 
if (empty($asperui_values_array_from_formatted) || $asperui_values_array_from_formatted === ['']) {
    $asperui_values_array_from_formatted = [];  
}


/*
if ($param1 =='Speed') {
    $as_per_ui_value =  $speed;
} 
*/
 
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($cmd_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }
 


/*
if($cmd_datatype[$i] == 'Float' && $cmd_valuedefinition[$i] == 'Fixed Value'){
  $integer_value = $as_per_ui_value;
}elseif($cmd_datatype[$i] == 'UnsignedInt' && $cmd_valuedefinition[$i] == 'Fixed Value') {
  $integer_value = $as_per_ui_value;
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
*/
if($cmd_uitype[$i] === 'Toggle Switch' || $cmd_uitype[$i] === 'Notification'){


$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';    
    }


elseif ($cmd_uitype[$i] === 'Slider' || $cmd_uitype[$i] === 'Hue Slider'  || $cmd_uitype[$i] === 'Hue Slider Custom'  || $cmd_uitype[$i] === 'Brightness Slider'  || $cmd_uitype[$i] === 'Airflow Slider') {
     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
        } elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);     
            }else{ 
                $integervalue = "null";  
            }
        }
    
    }elseif ($cmd_uitype[$i] === 'Drop Down') {



     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
           // if($as_per_ui_value !== 'NULL'){
           

$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

$ui_toggle_drop = explode('][', trim($asperui_values, '[]')); 
$ui_toggle_value_drop = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map_drop = array_combine($ui_toggle_value_drop,$ui_toggle_drop);

$as_per_ui_value =isset($dropdown_map_drop[$as_per_ui_value]) ? $dropdown_map_drop[$as_per_ui_value] : '';



  
           /* }else{ 
                $integervalue = "null";  
            }*/
        } elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);    
            }else{ 
                $integervalue = "null";  
            }
        }
    
    }elseif ($cmd_uitype[$i]  === 'Text') {
     
        if ($cmd_datatype[$i] === 'String') {
                if($as_per_ui_value !== 'NULL'){
                 //  echo $as_per_ui;
                   // if($as_per_ui != 'NULL')
                     if(!empty($as_per_ui))
                   {  
                  

                     if($cmd_defaultvalue[$i] == 'name')
                      {
                         
                        $integervalue=$as_per_ui_value;
                      }

                      else{



                     $ui_toggle = explode('][', trim($as_per_ui, '[]'));
                     $ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
                     $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                     $as_per_ui_value = (string)$as_per_ui_value;  
                     $as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;
                     $integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

                       }

                   }
                   else{
                    $integervalue = $as_per_ui_value;
                   }
                }else{
                    $integervalue = "null";  
            }  
         }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
         }elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){


              //  $integervalue = hexToFloat($as_per_ui_value);  
                 $cmdfloat_value = hexToFloat($as_per_ui_value); 
                // $integervalue= $cmdfloat_value; 
              // $integervalue= sprintf("%.2f", $cmdfloat_value);   
                $integervalue = number_format($cmdfloat_value, 6, '.', '');
            }else{ 

            
                $integervalue = "null";  
            }
         }else{
            if($as_per_ui_value !== 'NULL'){
                $integervalue = $as_per_ui_value;  
            }else{
                $integervalue = "null";  
            }
         }
    
    }else{
        if($as_per_ui_value !== 'NULL'){
            $integervalue = $as_per_ui_value;  
        }else{
            $integervalue = "null";  
        }
    }
 


    if ($cmd_datatype[$i] === 'Float') {
        // $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
         // $integervalue = hexToFloat($integer_value);
         // $integervalue = empty($integer_value) ? (float)$integer_value :  0.0;
         $cmd_value2 = floatToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
         $cmd_sliderstep = (float)$cmd_sliderstepvalue[$i];
        $display_min = (float)$cmd_displayvaluemin[$i];
        $display_max = (float)$cmd_displayvaluemax[$i];
     }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
        if($cmd_uitype[$i] == 'Toggle Switch'){
            $ui_toggle2 = explode('][', trim($as_per_ui, '[]'));
    $ui_toggle_value2 = explode('][', trim($asperui_values, '[]'));
            $dropdown_map22 = array_combine($ui_toggle2,$ui_toggle_value2);  
            $cmd_value = isset($dropdown_map22[$integervalue]) ? $dropdown_map22[$integervalue] : '';  
            //print_r($cmd_value);
            $display_min = (int)$cmd_displayvaluemin[$i];
            $display_max = (int)$cmd_displayvaluemax[$i];
        }else{
      //  $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
      //   $integervalue = hexToInt($integer_value);
         $cmd_value2 = intToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
        $cmd_sliderstep = (int)$cmd_sliderstepvalue[$i];
        $display_min = (int)$cmd_displayvaluemin[$i];
        $display_max = (int)$cmd_displayvaluemax[$i];
        }
    }elseif ($cmd_datatype[$i] === 'String') {
      // $integervalue = (string)$integer_value;
$cmd_sliderstep = '';
          $display_min = (int)$cmd_displayvaluemin[$i];
        $display_max = (int)$cmd_displayvaluemax[$i];

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
    "value" => $as_per_ui_value,
    "Field_description" => $cmd_fielddescription[$i],
    "type" => $cmd_valuedefinition[$i],
    "data_type" => $cmd_datatype[$i],
    "no_of_bytes" => (int)$cmd_noofbytes[$i],
    "byte_start_position" => (int)$cmd_startbytePosition[$i],
    "ui_type"  => $cmd_uitype[$i],
    "local_control" => $local_control,
    "ui_ids"   => (int)$cmd_uiid[$i],
    "asperui" => $as_per_ui_array_from_formatted,  
    "sno_asperui" => $sno_as_per_ui_array_from_formatted,  
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
            "local_control" => $local_control
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
            "local_control" => $local_control,
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
                    "local_control" => $local_control,
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

$sno_as_per_ui = $resp_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
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
 
 
   
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($resp_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }



/*
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
*/
if(($resp_uiType[$i] === 'Toggle Switch') || ($resp_uiType[$i] === 'Notification')) {
// print_r($as_per_ui);
 //print_r($asperui_values);
//print_r($as_per_ui_value);
$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));

$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);
  
$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';  
    
    }elseif ($resp_uiType[$i] === 'Slider' || $resp_uiType[$i] === 'Hue Slider'  || $resp_uiType[$i] === 'Hue Slider Custom'  || $resp_uiType[$i] === 'Brightness Slider'  || $resp_uiType[$i] === 'Airflow Slider') {
     
        if ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i] === 'Drop Down') {
   
        if ($resp_dataType[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                              
      /*         $ui_toggle = explode('][', trim($asperui_values, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));



$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);



$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;


//$as_per_ui_value = (string)$asperui_values[$i];        
//$as_per_ui_value = strlen($asperui_values[$i]) == 1 ? '0' . $asperui_values[$i] : $asperui_values[$i];


//$resp_integervalue = isset($dropdown_map[asperui_values[$i]]) ? $dropdown_map[asperui_values[$i]] : '';    


$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

*/

$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 



$ui_toggle_drop = explode('][', trim($asperui_values, '[]')); 
$ui_toggle_value_drop = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map_drop = array_combine($ui_toggle_value_drop,$ui_toggle_drop);





$resp_value =isset($dropdown_map_drop[$as_per_ui_value]) ? $dropdown_map_drop[$as_per_ui_value] : '';




//echo $resp_value;



            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $resp_integervalue= sprintf("%.2f", $cmdfloat_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i]  === 'Text') {
     
        if ($resp_dataType[$i] === 'String') {
                if(!empty($as_per_ui_value)){
                 //   $resp_integervalue = $as_per_ui_value;

                       if(!empty($as_per_ui))
                   {  
                     $ui_toggle = explode('][', trim($as_per_ui, '[]'));
                     $ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
                     $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                     $as_per_ui_value = (string)$as_per_ui_value;  
                     $as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;
                     $resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 
                   }
                   else{
                    $resp_integervalue = $as_per_ui_value;
                   }





                }else{
                    $resp_integervalue = "null";  
            }  
         }elseif ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
         }elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
         }else{
            if(!empty($as_per_ui_value)){
                $resp_integervalue = $as_per_ui_value;  
            }else{
                $resp_integervalue = "null";  
            }
         }
    
    }else{
        if(!empty($as_per_ui_value)){
            $resp_integervalue = $as_per_ui_value;  
        }else{
            $resp_integervalue = "null";  
        }
    }
   
  
    if ($resp_dataType[$i] === 'Float') {
        //$asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (float)$integer_value;
        $resp_value2 = floatToHex($resp_integervalue);
         $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderstep = (float)$resp_sliderstepvalue[$i];
    } elseif ($resp_dataType[$i] === 'UnsignedInt') {
       // $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (int)$integer_value;

         if($resp_uiType[$i] != 'Drop Down')
         {  
             
         $resp_value2 = intToHex($resp_integervalue);
          $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderStep = (int)$resp_sliderStepValue[$i];

         }
    }elseif($resp_dataType[$i] === 'String'){
     // $respintegervalue = (string)$integer_value;
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
                    "local_control" => $local_control,
                    "ui_ids" => (int)$resp_uiId[$i],
                    "asperui" => $as_per_ui_array_from_formatted,
                    "sno_asperui" => $sno_as_per_ui_array_from_formatted,
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
        } 









else {


if($resp_uiType[$i] == 'None')
{
            $field_resp = [
                "default_name" => $resp_defaultvalue[$i],
                "value" => $resp_valueInHexMin[$i],
                "Field_description" => $resp_fieldDescription[$i],
                "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                "byte_start_position" => (int)$resp_startBytePosition[$i],
                "ui_type" => $resp_uiType[$i],
                "local_control" => $local_control
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
                "local_control" => $local_control,
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

   

$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($error_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }


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
            "local_control" => $local_control,
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

unset($para_result['']);


/*

if($model_name == 'EF-003FMN03')
{

// Remove the unwanted pages after processing
//unset($para_result['Device Communication']);
//unset($para_result['Status']);


unset($para_result['Configure.Config']);
unset($para_result['Remote Address']);
unset($para_result['Remote Pair.Pairing']);

$status='disable';

}
else {

$status='enable';
//$status='disable';
}
 //02-06-25




if($model_name == 'EF-003FMN03')
{

$status='enable';    //test_nodes--status

}
else {

$status='enable';
//$status='disable';
}

*/



if ($mortgage === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Mortgage') {
 unset($para_result[$key]);
                   }
    }
}

if ($status === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Status') {
 unset($para_result[$key]);
                   }
    }
}




if ($device_comm_page === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Device Communication') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_pair_pairing === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Pair.Pairing') {
 unset($para_result[$key]);
                   }
    }
}


if ($config_config === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Configure.Config') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_address === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Address') {
 unset($para_result[$key]);
                   }
    }
}




/**************************/



 $query_unread = "SELECT COUNT(*) AS `unread_count` FROM call_service WHERE node_id = '$node_id' AND status = 'unread'";
$row_unread = $db->get_row($query_unread);

// Convert object to array and access key safely
$unread_count = '0';
if ($row_unread) {
    foreach ($row_unread as $key => $val) {
        if (trim($key) === 'unread_count') {
            $unread_count = trim((string)$val); // Ensure it's a trimmed string
        }
    }
}


if ($unread_count === '' || $unread_count === null) {
    $unread_count = '0';
}



$attributes = [
    [
        "name" => "Device Id",
        "value" => $node_id
    ],
    [
        "name" => "mac",
        "value" => $mac_value
    ]
];



    $devices = [
        [
            "name" => $curr_device_type,
            "total_pages" => $number_of_pages,
            "attributes" => $attributes,
            "Device Id" => $node_id,
            "pages" => $para_result,               
            "primary" => "power",
            "type" => $type,
            "brand_logo" => "https://efactor.tech/files/brand_logo/" . $brandname . ".jpg",
             "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
            "chat_status"=>$unread_count,
            "device_image" => $device_image,
            "device_p2p_control"=>"disable",
                "Device Communication"=>"disable",
                "Status"=>$status

        ]
    ];
} else {



    if ($is_added == 1) {


        $query_gateway = "SELECT * FROM `efactorproduct` WHERE add_gateway ='$add_gateway' AND is_added='$is_added'"; 
        $single_node_dynamic_gateway = $db->get_results($query_gateway);
        $row_node_gateway = $db->get_row($query_gateway);
        $node_id = $row_node_gateway["node_id"];
        $module_type_gate = $row_node_gateway["module_type"];

        $query_gateway_11 = "SELECT node_id FROM `efactorproduct` WHERE add_gateway ='$add_gateway' AND access_token='$val'"; 
        $all_devices_dynamic = $db->get_results($query_gateway_11);



        foreach ($all_devices_dynamic as $node_gate) {



            $nodeid = $node_gate["node_id"];

            $query_node = "SELECT * FROM efactorproduct WHERE node_id='$nodeid'";
            $single_node_dynamic = $db->get_results($query_node);
            $row_node = $db->get_row($query_node);

            $model_name = $row_node['model_number'];
            $current_devicetype = $row_node['current_devicetype'];
            $module_type = $row_node['module_type'];
            $device_type = $row_node['device_type'];
            $nett_conn = $row_node['internet'];
            $access_token = $row_node['access_token'];
            $vendor_name = $row_node['vendor_name'];
            $add_gateway = $row_node['add_gateway'];
            $is_added = $row_node['is_added'];
            $userid = $row_node['user_id'];

            $node_id = $row_node['node_id'];






if($model_name == 'EFDEFLT V1.0'){
    $query_node="SELECT * FROM efactorproduct where node_id='$node_id'";
}else{
$query_node="SELECT * FROM `$model_name` where node_id='$node_id'";
}
			$doctors_node = $db->get_results($query_node); 
			//$num_node = $db->num_rows($query_node);
                        $dbcker = $db->get_row($query_node);    
                       // print_r($dbcker);
		      //  $nodeid=$dbcker['node_id'];
                      //  $user_id=$dbcker['user_id'];
                       // $model_name=$dbcker['model_name'];
                           $curr_device_type=$dbcker['current_devicetype'];
                           $devicetype=$dbcker['device_type'];
                           $mortgage=$dbcker['Mortgage_page'];
                           $status=$dbcker['status_page'];
                           $device_comm_page=$dbcker['device_comm_page'];
                           $remote_pair_pairing=$dbcker['Remote_Pair_Pairing'];
                           $config_config=$dbcker['Configure_Config'];
                           $remote_address=$dbcker['Remote_Address'];


                               
 $type="efactor.device.$devicetype"; 


/*-----------------------------------------------*/

// Determine device image
        if ($model_name == 'EF-003FMN03' || $model_name == 'EF-002FMN02' || $model_name == 'EF-001FMN01' || $model_name == 'BAJAJ-001FMN01') {
            $deviceimage = 'fan-EF-003FMN03.jpg';
        } elseif ($model_name == 'EFCKER V1.0') {
            $deviceimage = 'efact_cooker.jpg';
        } elseif ($model_name == 'EF-001LIGHT01' || $model_name == 'EF-002CWLIGHT02' || $model_name == 'EF-001RGBLIGHT01' || $model_name == 'EF-003WLIGHT03') {
            $deviceimage = 'light_org.jpg';
        } elseif ($model_name == 'EF-001OULT01' || $model_name == 'EF-001SOCKET_6A01' || $model_name == 'EF-001SOCKET_10A01') {
            $deviceimage = 'outlet.jpg';
        } elseif ($model_name == 'EFSWPCP V1.0') {
            $deviceimage = 'swpcp.jpg';
        } elseif ($model_name == 'INVENDIS_MFM_V1') {
            $deviceimage = 'invendis_device.jpg';
        }

elseif ($model_name == 'EF_RF_GATEW V1.0') {
            $deviceimage = 'gateway_org.jpg';
        }




else {
            $deviceimage = 'input_output.jpg';
        }


 $device_image = "https://efactor.tech/files/brand_logo/" . $deviceimage;



/*-----------------------------------------------*/

                                
                                         
  

//$query = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND user_id = '177'";
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

        



     //   EFDEFLT V1.0
   
$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFDEFLT V1.1'";
$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";
//$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";

$result_1 = $db->get_results($query_1); 
$result_2 = $db->get_results($query_2); 

$result_3 = $db->num_rows($query_1);
$result_4 = $db->num_rows($query_2);

$result_5 = $db->get_row($query_1); 
$result_6 = $db->get_row($query_2); 

$doctors = array_merge($result_1, $result_2);
$num_11 = $result_3 + $result_4;
$row = array_merge($result_1, $result_2);
$parameter_name = $row['parameter_name'];
   /*
        $query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";

      $doctors = $db->get_results($query_1); 
        $num_11 = $db->num_rows($query_1);
        $row = $db->get_row($query_1); 
       $parameter_name = $row['parameter_name'];

    */
       

//print_r($row);

//$parameters = ['power', 'speed', 'led', 'direction'];

// Array to store the final result
$result = [];



 
foreach ($doctors as $param) {
   
    $fields_cmd = [];
    $fields_resp = [];
    $fields_error = [];

$param1=$param['parameter_name'];


 
//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFFN02' AND user_id = '177' AND parameter_name='$param1'";
$query_param1 = "SELECT * FROM product_api WHERE model_name = 'EFDEFLT V1.1' AND parameter_name='$param1'";
$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
  //$query_param = "SELECT * FROM product_api WHERE model_name = 'EFACTDFLT' AND parameter_name='$param1'";

 //$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND parameter_name='$param1'";

$result_1 = $db->get_results($query_param1); 
$result_2 = $db->get_results($query_param); 

$result_3 = $db->num_rows($query_param1);
$result_4 = $db->num_rows($query_param);

$row_param1 = $db->get_row($query_param1); 
$row_param2 = $db->get_row($query_param); 
  

$params = array_merge($result_1, $result_2);
$num_param = $result_3 + $result_4;
$row_param = array_merge((array)$row_param1, (array)$row_param2);
 
 //$row_param = array_merge($result_5, $result_6);
/*
    $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";

        $params = $db->get_results($query_param); 
        $num_param = $db->num_rows($query_param);
          $row_param = $db->get_row($query_param); 
      //  print_r($row_param1);
*/
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
            $cmd_sno_asperui = explode(',', $row_param['cmd_sno_asperui']);
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
            $resp_sno_asperui = explode(',', $row_param['resp_sno_asperui']);
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
            $error_sno_asperui = explode(',', $row_param['error_sno_asperui']);
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



//if($page_names == 'Configure' || $page_names == 'Remote Pair')
 if($page_names == 'Configure' || $page_names == 'Remote Pair' || $page_names == 'Configure.Config' || $page_names == 'Set Max Current' || $page_names == 'Device Setting'){

if($parameter_name == 'wifi config' || $parameter_name == 'DAC')


{
$local_control ='disable';       //disable

}
else{
 $local_control ='enable';       //disable

}

}else{

$local_control ='enable';
}



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
            "local_control" => $local_control,
            "ui_ids"   => (int)$cmd_uiid[$i],
            "ui_description" => $cmd_uidescriptioninout[$i]
            ];


//$fields_cmd[] = $field_cmd;



          }elseif($cmd_uitype[$i] == 'None'){ 
            $field_cmd = [
               "default_name" =>$cmd_defaultvalue[$i],
               "value" => $cmd_valueinhexmin[$i],
               "Field_description" => $cmd_fielddescription[$i],
               "no_of_bytes" => (int)$cmd_noofbytes[$i],
               "byte_start_position" => (int)$cmd_startbytePosition[$i] + 27,
               "ui_type"  => $cmd_uitype[$i],
               "local_control" => $local_control
        ]; 

      }else{




        
$as_per_ui = $cmd_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

if (empty($as_per_ui_array_from_formatted) || $as_per_ui_array_from_formatted === ['']) {
    $as_per_ui_array_from_formatted = [];  
}

 

$sno_as_per_ui = $cmd_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
}

$asperui_values = $cmd_asperui_values[$i];
$asperui_values_array = explode('][', trim($asperui_values, '[]'));
$formatted_asperui_values = implode(',', $asperui_values_array);
$asperui_values_array_from_formatted = explode(',', $formatted_asperui_values);
 
if (empty($asperui_values_array_from_formatted) || $asperui_values_array_from_formatted === ['']) {
    $asperui_values_array_from_formatted = [];  
}


/*
if ($param1 =='Speed') {
    $as_per_ui_value =  $speed;
} 
*/
 
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($cmd_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }
 


/*
if($cmd_datatype[$i] == 'Float' && $cmd_valuedefinition[$i] == 'Fixed Value'){
  $integer_value = $as_per_ui_value;
}elseif($cmd_datatype[$i] == 'UnsignedInt' && $cmd_valuedefinition[$i] == 'Fixed Value') {
  $integer_value = $as_per_ui_value;
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
*/
if($cmd_uitype[$i] === 'Toggle Switch' || $cmd_uitype[$i] === 'Notification'){


$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';    
    }


elseif ($cmd_uitype[$i] === 'Slider' || $cmd_uitype[$i] === 'Hue Slider'  || $cmd_uitype[$i] === 'Hue Slider Custom'  || $cmd_uitype[$i] === 'Brightness Slider'  || $cmd_uitype[$i] === 'Airflow Slider') {
     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
        } elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);     
            }else{ 
                $integervalue = "null";  
            }
        }
    
    }elseif ($cmd_uitype[$i] === 'Drop Down') {



     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
           // if($as_per_ui_value !== 'NULL'){

        //  echo $as_per_ui_value;   

$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

$ui_toggle_drop = explode('][', trim($asperui_values, '[]')); 
$ui_toggle_value_drop = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map_drop = array_combine($ui_toggle_value_drop,$ui_toggle_drop);

$as_per_ui_value =isset($dropdown_map_drop[$as_per_ui_value]) ? $dropdown_map_drop[$as_per_ui_value] : '';



  
           /* }else{ 
                $integervalue = "null";  
            }*/
        } elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);    
            }else{ 
                $integervalue = "null";  
            }
        }
    
    }elseif ($cmd_uitype[$i]  === 'Text') {
     
        if ($cmd_datatype[$i] === 'String') {
                if($as_per_ui_value !== 'NULL'){
                 //  echo $as_per_ui;
                   // if($as_per_ui != 'NULL')
                     if(!empty($as_per_ui))
                   {  
                  

                     if($cmd_defaultvalue[$i] == 'name')
                      {
                         
                        $integervalue=$as_per_ui_value;
                      }

                      else{



                     $ui_toggle = explode('][', trim($as_per_ui, '[]'));
                     $ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
                     $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                     $as_per_ui_value = (string)$as_per_ui_value;  
                     $as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;
                     $integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

                       }

                   }
                   else{
                    $integervalue = $as_per_ui_value;
                   }
                }else{
                    $integervalue = "null";  
            }  
         }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
         }elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){


              //  $integervalue = hexToFloat($as_per_ui_value);  
                 $cmdfloat_value = hexToFloat($as_per_ui_value); 
                // $integervalue= $cmdfloat_value; 
              // $integervalue= sprintf("%.2f", $cmdfloat_value);   
                $integervalue = number_format($cmdfloat_value, 6, '.', '');
            }else{ 

            
                $integervalue = "null";  
            }
         }else{
            if($as_per_ui_value !== 'NULL'){
                $integervalue = $as_per_ui_value;  
            }else{
                $integervalue = "null";  
            }
         }
    
    }else{
        if($as_per_ui_value !== 'NULL'){
            $integervalue = $as_per_ui_value;  
        }else{
            $integervalue = "null";  
        }
    }
 


    if ($cmd_datatype[$i] === 'Float') {
        // $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
         // $integervalue = hexToFloat($integer_value);
         // $integervalue = empty($integer_value) ? (float)$integer_value :  0.0;
         $cmd_value2 = floatToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
         $cmd_sliderstep = (float)$cmd_sliderstepvalue[$i];
        $display_min = (float)$cmd_displayvaluemin[$i];
        $display_max = (float)$cmd_displayvaluemax[$i];
     }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
        if($cmd_uitype[$i] == 'Toggle Switch'){
            $ui_toggle2 = explode('][', trim($as_per_ui, '[]'));
    $ui_toggle_value2 = explode('][', trim($asperui_values, '[]'));
            $dropdown_map22 = array_combine($ui_toggle2,$ui_toggle_value2);  
            $cmd_value = isset($dropdown_map22[$integervalue]) ? $dropdown_map22[$integervalue] : '';  
            //print_r($cmd_value);
            $display_min = (int)$cmd_displayvaluemin[$i];
            $display_max = (int)$cmd_displayvaluemax[$i];
        }else{
      //  $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
      //   $integervalue = hexToInt($integer_value);
         $cmd_value2 = intToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
        $cmd_sliderstep = (int)$cmd_sliderstepvalue[$i];
        $display_min = (int)$cmd_displayvaluemin[$i];
        $display_max = (int)$cmd_displayvaluemax[$i];
        }
    }elseif ($cmd_datatype[$i] === 'String') {
      // $integervalue = (string)$integer_value;
$cmd_sliderstep = '';
          $display_min = (int)$cmd_displayvaluemin[$i];
        $display_max = (int)$cmd_displayvaluemax[$i];

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
    "value" => $as_per_ui_value,
    "Field_description" => $cmd_fielddescription[$i],
    "type" => $cmd_valuedefinition[$i],
    "data_type" => $cmd_datatype[$i],
    "no_of_bytes" => (int)$cmd_noofbytes[$i],
    "byte_start_position" => (int)$cmd_startbytePosition[$i],
    "ui_type"  => $cmd_uitype[$i],
    "local_control" => $local_control,
    "ui_ids"   => (int)$cmd_uiid[$i],
    "asperui" => $as_per_ui_array_from_formatted,  
    "sno_asperui" => $sno_as_per_ui_array_from_formatted,  
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
            "local_control" => $local_control
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
            "local_control" => $local_control,
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
                    "local_control" => $local_control,
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

$sno_as_per_ui = $resp_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
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
 
 
   
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($resp_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }



/*
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
*/
if(($resp_uiType[$i] === 'Toggle Switch') || ($resp_uiType[$i] === 'Notification')) {
// print_r($as_per_ui);
 //print_r($asperui_values);
//print_r($as_per_ui_value);
$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));

$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);
  
$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';  
    
    }elseif ($resp_uiType[$i] === 'Slider' || $resp_uiType[$i] === 'Hue Slider'  || $resp_uiType[$i] === 'Hue Slider Custom'  || $resp_uiType[$i] === 'Brightness Slider'  || $resp_uiType[$i] === 'Airflow Slider') {
     
        if ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i] === 'Drop Down') {
   
        if ($resp_dataType[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                              
      /*         $ui_toggle = explode('][', trim($asperui_values, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));



$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);



$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;


//$as_per_ui_value = (string)$asperui_values[$i];        
//$as_per_ui_value = strlen($asperui_values[$i]) == 1 ? '0' . $asperui_values[$i] : $asperui_values[$i];


//$resp_integervalue = isset($dropdown_map[asperui_values[$i]]) ? $dropdown_map[asperui_values[$i]] : '';    


$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

*/

$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 



$ui_toggle_drop = explode('][', trim($asperui_values, '[]')); 
$ui_toggle_value_drop = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map_drop = array_combine($ui_toggle_value_drop,$ui_toggle_drop);





$resp_value =isset($dropdown_map_drop[$as_per_ui_value]) ? $dropdown_map_drop[$as_per_ui_value] : '';




//echo $resp_value;



            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $resp_integervalue= sprintf("%.2f", $cmdfloat_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i]  === 'Text') {
     
        if ($resp_dataType[$i] === 'String') {
                if(!empty($as_per_ui_value)){
                 //   $resp_integervalue = $as_per_ui_value;

                       if(!empty($as_per_ui))
                   {  
                     $ui_toggle = explode('][', trim($as_per_ui, '[]'));
                     $ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
                     $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                     $as_per_ui_value = (string)$as_per_ui_value;  
                     $as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;
                     $resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 
                   }
                   else{
                    $resp_integervalue = $as_per_ui_value;
                   }





                }else{
                    $resp_integervalue = "null";  
            }  
         }elseif ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
         }elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
         }else{
            if(!empty($as_per_ui_value)){
                $resp_integervalue = $as_per_ui_value;  
            }else{
                $resp_integervalue = "null";  
            }
         }
    
    }else{
        if(!empty($as_per_ui_value)){
            $resp_integervalue = $as_per_ui_value;  
        }else{
            $resp_integervalue = "null";  
        }
    }
   
  
    if ($resp_dataType[$i] === 'Float') {
        //$asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (float)$integer_value;
        $resp_value2 = floatToHex($resp_integervalue);
         $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderstep = (float)$resp_sliderstepvalue[$i];
    } elseif ($resp_dataType[$i] === 'UnsignedInt') {
       // $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (int)$integer_value;

         if($resp_uiType[$i] != 'Drop Down')
         {  
             
         $resp_value2 = intToHex($resp_integervalue);
          $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderStep = (int)$resp_sliderStepValue[$i];

         }
    }elseif($resp_dataType[$i] === 'String'){
     // $respintegervalue = (string)$integer_value;
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
                    "local_control" => $local_control,
                    "ui_ids" => (int)$resp_uiId[$i],
                    "asperui" => $as_per_ui_array_from_formatted,
                    "sno_asperui" => $sno_as_per_ui_array_from_formatted,
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
        } 









else {


if($resp_uiType[$i] == 'None')
{
            $field_resp = [
                "default_name" => $resp_defaultvalue[$i],
                "value" => $resp_valueInHexMin[$i],
                "Field_description" => $resp_fieldDescription[$i],
                "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                "byte_start_position" => (int)$resp_startBytePosition[$i],
                "ui_type" => $resp_uiType[$i],
                "local_control" => $local_control
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
                "local_control" => $local_control,
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

   

$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($error_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }


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
            "local_control" => $local_control,
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


unset($para_result['']);


/*
if($model_name == 'EF-003FMN03')
{

// Remove the unwanted pages after processing
//unset($para_result['Device Communication']);
//unset($para_result['Status']);



unset($para_result['Configure.Config']);
unset($para_result['Remote Address']);
unset($para_result['Remote Pair.Pairing']);

$status='disable';

}

else{

$status='enable';

}

 //02-06-25


if($model_name == 'EF-003FMN03')
{


$status='enable';    //tset_nodes--status

}

else{

$status='enable';

}

*/




if ($mortgage === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Mortgage') {
 unset($para_result[$key]);
                   }
    }
}

if ($status === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Status') {
 unset($para_result[$key]);
                   }
    }
}




if ($device_comm_page === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Device Communication') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_pair_pairing === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Pair.Pairing') {
 unset($para_result[$key]);
                   }
    }
}


if ($config_config === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Configure.Config') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_address === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Address') {
 unset($para_result[$key]);
                   }
    }
}




/**************************/





 $nodeid = $node_gate["node_id"];

            $query_node = "SELECT * FROM efactorproduct WHERE node_id='$nodeid'";
            $single_node_dynamic = $db->get_results($query_node);
            $row_node = $db->get_row($query_node);

            $model_name = $row_node['model_number'];
            $current_devicetype = $row_node['current_devicetype'];
            $module_type = $row_node['module_type'];
            $device_type = $row_node['device_type'];
            $nett_conn = $row_node['internet'];
            $access_token = $row_node['access_token'];
            $vendor_name = $row_node['vendor_name'];
            $add_gateway = $row_node['add_gateway'];
            $is_added = $row_node['is_added'];
            $userid = $row_node['user_id'];









$query = $this->db->query(
    "SELECT COUNT(*) AS unread_count 
     FROM call_service 
     WHERE node_id = ? 
     AND status = 'unread'", 
    [$nodeid]  // Parameter binding prevents SQL injection
);


$result = $query->row();
$unread_count = $result->unread_count;


$query_unread = "SELECT COUNT(*) AS `unread_count` FROM call_service WHERE node_id = '$node_id' AND status = 'unread'";
$row_unread = $db->get_row($query_unread);

// Convert object to array and access key safely
$unread_count = '0';
if ($row_unread) {
    foreach ($row_unread as $key => $val) {
        if (trim($key) === 'unread_count') {
            $unread_count = trim((string)$val); // Ensure it's a trimmed string
        }
    }
}


if ($unread_count === '' || $unread_count === null) {
    $unread_count = '0';
}






$attributes = [
    [
        "name" => "Device Id",
        "value" => $nodeid
    ],
    [
        "name" => "mac",
        "value" => $mac_value
    ]
];

            $devices_gate = [
                
                    "name" => $curr_device_type,
                    "total_pages" => $number_of_pages,
                    "attributes" => $attributes,
                    "Device Id" => $nodeid,
                    "pages" => $para_result,               
                    "primary" => "power",
                    "type" => $type,
                    "brand_logo" => "https://efactor.tech/files/brand_logo/" . $brandname . ".jpg",
                     "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
                     "chat_status"=>$unread_count,
                    "device_image" => $device_image,
                    "device_p2p_control"=>"disable",
                "Device Communication"=>"disable",
                "Status"=>$status
                
            ];




            $devices[] = $devices_gate; // Add the device to the devices array
        }
    }
}



$url_params = "model_name={$model_number}&user_id={$user_id}";
$auto_url_params = "model_name={$model_number}&user_id={$user_id}&node_id={$node_id}";


 $query_unread = "SELECT COUNT(*) AS `unread_count` FROM call_service WHERE node_id = '$node_id' AND status = 'unread'";
$row_unread = $db->get_row($query_unread);

// Convert object to array and access key safely
$unread_count = '0';
if ($row_unread) {
    foreach ($row_unread as $key => $val) {
        if (trim($key) === 'unread_count') {
            $unread_count = trim((string)$val); // Ensure it's a trimmed string
        }
    }
}


if ($unread_count === '' || $unread_count === null) {
    $unread_count = '0';
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
        "devices" => $devices,
        "info" => array(
            "fw_version" => "1.5.1",
            "model" => $model_number,
            "name" => $curr_devicetype,
            "type" => $moduletype,
            "SSID" => $ssid,
            "Access_Point" => $access_point,
            "Guarantee_Card" => "https://efactor.tech/v8/api.php?action=guarantee_card_display&{$url_params}",
           // "Video" => "https://efactor.tech/v8/api.php?action=app_video&{$url_params}",
            "Video" => "https://efactor.tech/v8/user_guide_support.php",
            "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
            "chat_status"=>$unread_count,
            "Auto_Test" => "https://efactor.tech/v8/auto_test.php?{$auto_url_params}"

        ),


        "node_id" => $node_id
    ),
    "role" => "primary",
    "status" => array(
        "connectivity" => array(
            "connected" => ($net_conn_1 === "true") ? true : false,
            "timestamp" => $timestamp
        )
    )
);








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




$end_time = microtime(true);
$execution_time = $end_time - $start_time;
//echo "Execution time: " . $execution_time . " seconds"; 




echo json_encode(array("node_details" => array($node_details), "nodes" => $node_id));

//echo $node;





exit;



/************************************************************/

    }

}






















/******************************************************************************/




































/**************************************************************/

if (isset($_GET['action']) && $_GET['action']=='test_nodes_4-4-25')
{
    
$node_id =$_GET['node_id'];




                        
			$header = apache_request_headers();  
			foreach ($header as $key => $val) {
			
			$query_dev="SELECT * FROM efactorproduct where node_id='$node_id'";
			$doctors = $db->get_results($query_dev); 
			$num_dev = $db->num_rows($query_dev);
                        $row_dev = $db->get_row($query_dev);    
		       $device_type=$row_dev['device_type'];
                       $model_name=$row_dev['model_number'];

//$node_id=$startid;


//echo $model_name;

if($model_name == 'EFDEFLT V1.0'){
    $query_node="SELECT * FROM efactorproduct where node_id='$node_id'";
}else{
$query_node="SELECT * FROM `$model_name` where node_id='$node_id'";
}
			$doctors_node = $db->get_results($query_node); 
			//$num_node = $db->num_rows($query_node);
                        $dbcker = $db->get_row($query_node);    
                       // print_r($dbcker);
		      //  $nodeid=$dbcker['node_id'];
                      //  $user_id=$dbcker['user_id'];
                       // $model_name=$dbcker['model_name'];
                           $curr_device_type=$dbcker['current_devicetype'];
                           $devicetype=$dbcker['device_type'];
                           $mortgage=$dbcker['Mortgage_page'];
                           $status=$dbcker['status_page'];
                           $device_comm_page=$dbcker['device_comm_page'];
                           $remote_pair_pairing=$dbcker['Remote_Pair_Pairing'];
                           $config_config=$dbcker['Configure_Config'];
                           $remote_address=$dbcker['Remote_Address'];


                               
 $type="efactor.device.$devicetype"; 





/*-----------------------------------------------*/

// Determine device image
        if ($model_name == 'EF-003FMN03' || $model_name == 'EF-002FMN02' || $model_name == 'EF-001FMN01' || $model_name == 'BAJAJ-001FMN01') {
            $deviceimage = 'fan-EF-003FMN03.jpg';
        } elseif ($model_name == 'EFCKER V1.0') {
            $deviceimage = 'efact_cooker.jpg';
        } elseif ($model_name == 'EF-001LIGHT01' || $model_name == 'EF-002CWLIGHT02' || $model_name == 'EF-001RGBLIGHT01' || $model_name == 'EF-003WLIGHT03') {
            $deviceimage = 'light_org.jpg';
        } elseif ($model_name == 'EF-001OULT01' || $model_name == 'EF-001SOCKET_6A01' || $model_name == 'EF-001SOCKET_10A01') {
            $deviceimage = 'outlet.jpg';
        } elseif ($model_name == 'EFSWPCP V1.0') {
            $deviceimage = 'swpcp.jpg';
        } elseif ($model_name == 'INVENDIS_MFM_V1') {
            $deviceimage = 'invendis_device.jpg';
        }

elseif ($model_name == 'EF_RF_GATEW V1.0') {
            $deviceimage = 'gateway_org.jpg';
        }




else {
            $deviceimage = 'input_output.jpg';
        }


 $device_image = "https://efactor.tech/files/brand_logo/" . $deviceimage;



/*-----------------------------------------------*/

                                
                                         
  

//$query = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND user_id = '177'";
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

        



     //   EFDEFLT V1.0
   
$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFDEFLT V1.1'";
$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";
//$query_2 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";

$result_1 = $db->get_results($query_1); 
$result_2 = $db->get_results($query_2); 

$result_3 = $db->num_rows($query_1);
$result_4 = $db->num_rows($query_2);

$result_5 = $db->get_row($query_1); 
$result_6 = $db->get_row($query_2); 

$doctors = array_merge($result_1, $result_2);
$num_11 = $result_3 + $result_4;
$row = array_merge($result_1, $result_2);
$parameter_name = $row['parameter_name'];
   /*
        $query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name'";

      $doctors = $db->get_results($query_1); 
        $num_11 = $db->num_rows($query_1);
        $row = $db->get_row($query_1); 
       $parameter_name = $row['parameter_name'];

    */
       

//print_r($row);

//$parameters = ['power', 'speed', 'led', 'direction'];

// Array to store the final result
$result = [];



 
foreach ($doctors as $param) {
   
    $fields_cmd = [];
    $fields_resp = [];
    $fields_error = [];

$param1=$param['parameter_name'];


 
//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFFN02' AND user_id = '177' AND parameter_name='$param1'";
$query_param1 = "SELECT * FROM product_api WHERE model_name = 'EFDEFLT V1.1' AND parameter_name='$param1'";
$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
  //$query_param = "SELECT * FROM product_api WHERE model_name = 'EFACTDFLT' AND parameter_name='$param1'";

 //$query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";
//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFCKER V1.0' AND parameter_name='$param1'";

$result_1 = $db->get_results($query_param1); 
$result_2 = $db->get_results($query_param); 

$result_3 = $db->num_rows($query_param1);
$result_4 = $db->num_rows($query_param);

$row_param1 = $db->get_row($query_param1); 
$row_param2 = $db->get_row($query_param); 
  

$params = array_merge($result_1, $result_2);
$num_param = $result_3 + $result_4;
$row_param = array_merge((array)$row_param1, (array)$row_param2);
 
 //$row_param = array_merge($result_5, $result_6);
/*
    $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param1'";

        $params = $db->get_results($query_param); 
        $num_param = $db->num_rows($query_param);
          $row_param = $db->get_row($query_param); 
      //  print_r($row_param1);
*/
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
            $cmd_sno_asperui = explode(',', $row_param['cmd_sno_asperui']);
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
            $resp_sno_asperui = explode(',', $row_param['resp_sno_asperui']);
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
            $error_sno_asperui = explode(',', $row_param['error_sno_asperui']);
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



//if($page_names == 'Configure' || $page_names == 'Remote Pair')
 if($page_names == 'Configure' || $page_names == 'Remote Pair' || $page_names == 'Configure.Config' || $page_names == 'Set Max Current' || $page_names == 'Device Setting'){

if($parameter_name == 'wifi config' || $parameter_name == 'DAC')
{
$local_control ='disable';       //disable

}
else{
 $local_control ='enable';       //disable

}

}else{

$local_control ='enable';
}



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
            "local_control" => $local_control,
            "ui_ids"   => (int)$cmd_uiid[$i],
            "ui_description" => $cmd_uidescriptioninout[$i]
            ];


//$fields_cmd[] = $field_cmd;



          }elseif($cmd_uitype[$i] == 'None'){ 
            $field_cmd = [
               "default_name" =>$cmd_defaultvalue[$i],
               "value" => $cmd_valueinhexmin[$i],
               "Field_description" => $cmd_fielddescription[$i],
               "no_of_bytes" => (int)$cmd_noofbytes[$i],
               "byte_start_position" => (int)$cmd_startbytePosition[$i] + 27,
               "ui_type"  => $cmd_uitype[$i],
               "local_control" => $local_control
        ]; 

      }else{




        
$as_per_ui = $cmd_asperui[$i];
$as_per_ui_array = explode('][', trim($as_per_ui, '[]'));
$formatted_as_per_ui = implode(',', $as_per_ui_array);
$as_per_ui_array_from_formatted = explode(',', $formatted_as_per_ui);

if (empty($as_per_ui_array_from_formatted) || $as_per_ui_array_from_formatted === ['']) {
    $as_per_ui_array_from_formatted = [];  
}

 

$sno_as_per_ui = $cmd_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
}

$asperui_values = $cmd_asperui_values[$i];
$asperui_values_array = explode('][', trim($asperui_values, '[]'));
$formatted_asperui_values = implode(',', $asperui_values_array);
$asperui_values_array_from_formatted = explode(',', $formatted_asperui_values);
 
if (empty($asperui_values_array_from_formatted) || $asperui_values_array_from_formatted === ['']) {
    $asperui_values_array_from_formatted = [];  
}


/*
if ($param1 =='Speed') {
    $as_per_ui_value =  $speed;
} 
*/
 
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($cmd_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }
 


/*
if($cmd_datatype[$i] == 'Float' && $cmd_valuedefinition[$i] == 'Fixed Value'){
  $integer_value = $as_per_ui_value;
}elseif($cmd_datatype[$i] == 'UnsignedInt' && $cmd_valuedefinition[$i] == 'Fixed Value') {
  $integer_value = $as_per_ui_value;
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
*/
if($cmd_uitype[$i] === 'Toggle Switch' || $cmd_uitype[$i] === 'Notification'){


$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';    
    }


elseif ($cmd_uitype[$i] === 'Slider' || $cmd_uitype[$i] === 'Hue Slider'  || $cmd_uitype[$i] === 'Hue Slider Custom'  || $cmd_uitype[$i] === 'Brightness Slider'  || $cmd_uitype[$i] === 'Airflow Slider') {
     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
        } elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);     
            }else{ 
                $integervalue = "null";  
            }
        }
    
    }elseif ($cmd_uitype[$i] === 'Drop Down') {



     
        if ($cmd_datatype[$i] === 'UnsignedInt') {
           // if($as_per_ui_value !== 'NULL'){

        //  echo $as_per_ui_value;   

$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

$ui_toggle_drop = explode('][', trim($asperui_values, '[]')); 
$ui_toggle_value_drop = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map_drop = array_combine($ui_toggle_value_drop,$ui_toggle_drop);

$as_per_ui_value =isset($dropdown_map_drop[$as_per_ui_value]) ? $dropdown_map_drop[$as_per_ui_value] : '';



  
           /* }else{ 
                $integervalue = "null";  
            }*/
        } elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $integervalue= sprintf("%.2f", $cmdfloat_value);    
            }else{ 
                $integervalue = "null";  
            }
        }
    
    }elseif ($cmd_uitype[$i]  === 'Text') {
     
        if ($cmd_datatype[$i] === 'String') {
                if($as_per_ui_value !== 'NULL'){
                 //  echo $as_per_ui;
                   // if($as_per_ui != 'NULL')
                     if(!empty($as_per_ui))
                   {  
                  

                     if($cmd_defaultvalue[$i] == 'name')
                      {
                         
                        $integervalue=$as_per_ui_value;
                      }

                      else{



                     $ui_toggle = explode('][', trim($as_per_ui, '[]'));
                     $ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
                     $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                     $as_per_ui_value = (string)$as_per_ui_value;  
                     $as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;
                     $integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

                       }

                   }
                   else{
                    $integervalue = $as_per_ui_value;
                   }
                }else{
                    $integervalue = "null";  
            }  
         }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                $integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $integervalue = "null";  
            }
         }elseif ($cmd_datatype[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){


              //  $integervalue = hexToFloat($as_per_ui_value);  
                 $cmdfloat_value = hexToFloat($as_per_ui_value); 
                // $integervalue= $cmdfloat_value; 
              // $integervalue= sprintf("%.2f", $cmdfloat_value);   
                $integervalue = number_format($cmdfloat_value, 6, '.', '');
            }else{ 

            
                $integervalue = "null";  
            }
         }else{
            if($as_per_ui_value !== 'NULL'){
                $integervalue = $as_per_ui_value;  
            }else{
                $integervalue = "null";  
            }
         }
    
    }else{
        if($as_per_ui_value !== 'NULL'){
            $integervalue = $as_per_ui_value;  
        }else{
            $integervalue = "null";  
        }
    }
 


    if ($cmd_datatype[$i] === 'Float') {
        // $asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
         // $integervalue = hexToFloat($integer_value);
         // $integervalue = empty($integer_value) ? (float)$integer_value :  0.0;
         $cmd_value2 = floatToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
         $cmd_sliderstep = (float)$cmd_sliderstepvalue[$i];
        $display_min = (float)$cmd_displayvaluemin[$i];
        $display_max = (float)$cmd_displayvaluemax[$i];
     }elseif ($cmd_datatype[$i] === 'UnsignedInt') {
        if($cmd_uitype[$i] == 'Toggle Switch'){
            $ui_toggle2 = explode('][', trim($as_per_ui, '[]'));
    $ui_toggle_value2 = explode('][', trim($asperui_values, '[]'));
            $dropdown_map22 = array_combine($ui_toggle2,$ui_toggle_value2);  
            $cmd_value = isset($dropdown_map22[$integervalue]) ? $dropdown_map22[$integervalue] : '';  
            //print_r($cmd_value);
            $display_min = (int)$cmd_displayvaluemin[$i];
            $display_max = (int)$cmd_displayvaluemax[$i];
        }else{
      //  $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
      //   $integervalue = hexToInt($integer_value);
         $cmd_value2 = intToHex($integervalue);
          $cmd_value = padHexValue($cmd_value2, (int)$cmd_noofbytes[$i]);
        $cmd_sliderstep = (int)$cmd_sliderstepvalue[$i];
        $display_min = (int)$cmd_displayvaluemin[$i];
        $display_max = (int)$cmd_displayvaluemax[$i];
        }
    }elseif ($cmd_datatype[$i] === 'String') {
      // $integervalue = (string)$integer_value;
$cmd_sliderstep = '';
          $display_min = (int)$cmd_displayvaluemin[$i];
        $display_max = (int)$cmd_displayvaluemax[$i];

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
    "value" => $as_per_ui_value,
    "Field_description" => $cmd_fielddescription[$i],
    "type" => $cmd_valuedefinition[$i],
    "data_type" => $cmd_datatype[$i],
    "no_of_bytes" => (int)$cmd_noofbytes[$i],
    "byte_start_position" => (int)$cmd_startbytePosition[$i],
    "ui_type"  => $cmd_uitype[$i],
    "local_control" => $local_control,
    "ui_ids"   => (int)$cmd_uiid[$i],
    "asperui" => $as_per_ui_array_from_formatted,  
    "sno_asperui" => $sno_as_per_ui_array_from_formatted,  
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
            "local_control" => $local_control
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
            "local_control" => $local_control,
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
                    "local_control" => $local_control,
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

$sno_as_per_ui = $resp_sno_asperui[$i];
$sno_as_per_ui_array = explode('][', trim($sno_as_per_ui, '[]'));
$formatted_sno_as_per_ui = implode(',', $sno_as_per_ui_array);
$sno_as_per_ui_array_from_formatted = explode(',', $formatted_sno_as_per_ui);

if (empty($sno_as_per_ui_array_from_formatted) || $sno_as_per_ui_array_from_formatted === ['']) {
    $sno_as_per_ui_array_from_formatted = [];  
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
 
 
   
$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($resp_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }



/*
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
*/
if(($resp_uiType[$i] === 'Toggle Switch') || ($resp_uiType[$i] === 'Notification')) {
// print_r($as_per_ui);
 //print_r($asperui_values);
//print_r($as_per_ui_value);
$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($asperui_values, '[]'));

$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);
  
$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : '';  
    
    }elseif ($resp_uiType[$i] === 'Slider' || $resp_uiType[$i] === 'Hue Slider'  || $resp_uiType[$i] === 'Hue Slider Custom'  || $resp_uiType[$i] === 'Brightness Slider'  || $resp_uiType[$i] === 'Airflow Slider') {
     
        if ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i] === 'Drop Down') {
   
        if ($resp_dataType[$i] === 'UnsignedInt') {
            if($as_per_ui_value !== 'NULL'){
                              
      /*         $ui_toggle = explode('][', trim($asperui_values, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));



$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);



$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;


//$as_per_ui_value = (string)$asperui_values[$i];        
//$as_per_ui_value = strlen($asperui_values[$i]) == 1 ? '0' . $asperui_values[$i] : $asperui_values[$i];


//$resp_integervalue = isset($dropdown_map[asperui_values[$i]]) ? $dropdown_map[asperui_values[$i]] : '';    


$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 

*/

$ui_toggle = explode('][', trim($as_per_ui, '[]'));
$ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

$as_per_ui_value = (string)$as_per_ui_value;  
$as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;

$resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 



$ui_toggle_drop = explode('][', trim($asperui_values, '[]')); 
$ui_toggle_value_drop = explode('][', trim($sno_as_per_ui, '[]'));
$dropdown_map_drop = array_combine($ui_toggle_value_drop,$ui_toggle_drop);





$resp_value =isset($dropdown_map_drop[$as_per_ui_value]) ? $dropdown_map_drop[$as_per_ui_value] : '';




//echo $resp_value;



            }else{ 
                $resp_integervalue = "null";  
            }
        } elseif ($resp_dataType[$i] === 'Float') {
            if($as_per_ui_value !== 'NULL'){
               // $integervalue = hexToFloat($as_per_ui_value); 
                $cmdfloat_value = hexToFloat($as_per_ui_value); 
               $resp_integervalue= sprintf("%.2f", $cmdfloat_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
        }
    
    }elseif ($resp_uiType[$i]  === 'Text') {
     
        if ($resp_dataType[$i] === 'String') {
                if(!empty($as_per_ui_value)){
                 //   $resp_integervalue = $as_per_ui_value;

                       if(!empty($as_per_ui))
                   {  
                     $ui_toggle = explode('][', trim($as_per_ui, '[]'));
                     $ui_toggle_value = explode('][', trim($sno_as_per_ui, '[]'));
                     $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                     $as_per_ui_value = (string)$as_per_ui_value;  
                     $as_per_ui_value = strlen($as_per_ui_value) == 1 ? '0' . $as_per_ui_value : $as_per_ui_value;
                     $resp_integervalue = isset($dropdown_map[$as_per_ui_value]) ? $dropdown_map[$as_per_ui_value] : ''; 
                   }
                   else{
                    $resp_integervalue = $as_per_ui_value;
                   }





                }else{
                    $resp_integervalue = "null";  
            }  
         }elseif ($resp_dataType[$i] === 'UnsignedInt') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexdec($as_per_ui_value);    
            }else{ 
                $resp_integervalue = "null";  
            }
         }elseif ($resp_dataType[$i] === 'Float') {
            if(!empty($as_per_ui_value)){
                $resp_integervalue = hexToFloat($as_per_ui_value);   
            }else{ 
                $resp_integervalue = "null";  
            }
         }else{
            if(!empty($as_per_ui_value)){
                $resp_integervalue = $as_per_ui_value;  
            }else{
                $resp_integervalue = "null";  
            }
         }
    
    }else{
        if(!empty($as_per_ui_value)){
            $resp_integervalue = $as_per_ui_value;  
        }else{
            $resp_integervalue = "null";  
        }
    }
   
  
    if ($resp_dataType[$i] === 'Float') {
        //$asperui_values_array_converted = array_map('floatval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (float)$integer_value;
        $resp_value2 = floatToHex($resp_integervalue);
         $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderstep = (float)$resp_sliderstepvalue[$i];
    } elseif ($resp_dataType[$i] === 'UnsignedInt') {
       // $asperui_values_array_converted = array_map('intval', $asperui_values_array_from_formatted);
       // $resp_integervalue = (int)$integer_value;

         if($resp_uiType[$i] != 'Drop Down')
         {  
             
         $resp_value2 = intToHex($resp_integervalue);
          $resp_value = padHexValue($resp_value2, (int)$resp_numberOfBytes[$i]);
        $resp_sliderStep = (int)$resp_sliderStepValue[$i];

         }
    }elseif($resp_dataType[$i] === 'String'){
     // $respintegervalue = (string)$integer_value;
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
                    "local_control" => $local_control,
                    "ui_ids" => (int)$resp_uiId[$i],
                    "asperui" => $as_per_ui_array_from_formatted,
                    "sno_asperui" => $sno_as_per_ui_array_from_formatted,
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
        } 









else {


if($resp_uiType[$i] == 'None')
{
            $field_resp = [
                "default_name" => $resp_defaultvalue[$i],
                "value" => $resp_valueInHexMin[$i],
                "Field_description" => $resp_fieldDescription[$i],
                "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                "byte_start_position" => (int)$resp_startBytePosition[$i],
                "ui_type" => $resp_uiType[$i],
                "local_control" => $local_control
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
                "local_control" => $local_control,
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

   

$cker = array();

foreach ($dbcker as $key => $value) {
    $cker[] = array(
        'name' => $key,
        'value' => $value
    );
}

 //print_r($cker);

    foreach ($cker as $entry) {
        if ($error_defaultvalue[$i] === $entry['name']) {
             $as_per_ui_value = $entry['value'];
            // echo "Matched value for $cmd_defaultvalue[$i]: " . $entry['value'] . "\n";
            break;  
        }
    }


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
            "local_control" => $local_control,
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

unset($para_result['']);

/*

if($model_name == 'EF-003FMN03')
{

// Remove the unwanted pages after processing
//unset($para_result['Device Communication']);
//unset($para_result['Status']);



unset($para_result['Configure.Config']);
unset($para_result['Remote Address']);
unset($para_result['Remote Pair.Pairing']);

}

   //02-06-25

*/



if ($mortgage === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Mortgage') {
 unset($para_result[$key]);
                   }
    }
}

if ($status === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Status') {
 unset($para_result[$key]);
                   }
    }
}




if ($device_comm_page === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Device Communication') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_pair_pairing === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Pair.Pairing') {
 unset($para_result[$key]);
                   }
    }
}


if ($config_config === 'disable') {
foreach ($para_result as $key => $value) {

if ($key === 'Configure.Config') {
 unset($para_result[$key]);
                   }
    }
}

if ($remote_address === 'disable') {
foreach ($para_result as $key => $value) {
if ($key === 'Remote Address') {
 unset($para_result[$key]);
                   }
    }
}




/**************************/









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
//$devicetype=$row_inter['device_type'];
$curr_devicetype=$row_inter['current_devicetype'];
$ssid=$row_inter['ssid'];
$vendor_name=$row_inter['vendor_name'];



if($vendor_name == 'EFACTOR')

{
$brandname='efactor';
}
if($vendor_name == 'BAJAJ')

{
$brandname='Bajaj';


}

if($vendor_name == 'DHRIO')

{
$brandname='dhrio';


}


if($vendor_name == 'Invendis')

{
$brandname='Invendis';


}



if($vendor_name == 'Skytec')

{
$brandname='Skytec';


}


if($vendor_name == 'exhaust_hood')

{
$brandname='exhaust';


}





$new_prefix = "EWIFI";
$parts = explode("_", $curr_devicetype, 2);
$access_point = $new_prefix . "_" . $parts[1];
//echo $updated_devicetype; 


$type="efactor.device.$devicetype";


















/*

if($name_dev == 'NULL')
{

$curr_name=$curr_devicetype;

}

else{


$curr_name=$name_dev;
}
*/

$url_params = "model_name={$model_number}&user_id={$user_id}";
$auto_url_params = "model_name={$model_number}&user_id={$user_id}&node_id={$node_id}";




 $query_unread = "SELECT COUNT(*) AS `unread_count` FROM call_service WHERE node_id = '$node_id' AND status = 'unread'";
$row_unread = $db->get_row($query_unread);

// Convert object to array and access key safely
$unread_count = '0';
if ($row_unread) {
    foreach ($row_unread as $key => $val) {
        if (trim($key) === 'unread_count') {
            $unread_count = trim((string)$val); // Ensure it's a trimmed string
        }
    }
}


if ($unread_count === '' || $unread_count === null) {
    $unread_count = '0';
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
                "name" => $curr_device_type,
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
                "Device Id"=> $node_id,
                "pages" =>  $para_result,
                   
               
                "primary" => "power",
                "type" => $type,
                "brand_logo" => "https://efactor.tech/files/brand_logo/" . $brandname . ".jpg",       
                "device_image"=> "https://efactor.tech/files/brand_logo/fan-EF-003FMN03.jpg",
                 "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
                 "chat_status"=>$unread_count,
                "device_p2p_control"=>"disable",
                "Device Communication"=>"disable",
                "Status"=>"disable"
            )
        ),
        "info" => array(
            "fw_version" => "1.5.1",
            "model" => $model_number,
            "name" => $curr_devicetype,
            "type" => $moduletype,
            "SSID" => $ssid,
            "Access_Point" => $access_point,
            "Guarantee_Card" => "https://efactor.tech/v8/api.php?action=guarantee_card_display&{$url_params}",
           // "Video" => "https://efactor.tech/v8/api.php?action=app_video&{$url_params}",
            "Video" => "https://efactor.tech/v8/user_guide_support.php",
            "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
            "chat_status"=>$unread_count,
            "Auto_Test" => "https://efactor.tech/v8/auto_test.php?{$auto_url_params}"

        ),



        "node_id" => $node_id
    ),
    "role" => "primary",
    "status" => array(
        "connectivity" => array(
            "connected" => ($net_conn_1 === "true") ? true : false,
            "timestamp" => $timestamp
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


//echo $node_details;

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





/************************************************************************************/

if (isset($_GET['action']) && $_GET['action']=='all_devices')
{

$startid =$_GET['start_id'];
                        
			$header = apache_request_headers();  
			foreach ($header as $key => $val) {

                      
$node='{
    "url": "action=auto_test_details",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJwaG9uZV92ZXJpZmllZCI6dHJ1ZSwiY3VzdG9tOnVzZXJfaWQiOiIxNzciLCJ0b2tlbl91c2UiOiJpZCIsImF1dGhfdGltZSI6MTY2NzAyMjI0MiwiZXhwIjoxNjY3MDI1ODQyLCJpYXQiOjE2NjcwMjIyNDIsInBob25lIjoiOTM5MTA0Mzc4MSJ9.q9CrbVNL-PRfAjnvvLGMmazodCk2Y1Lid9gch5ey0R4",
   
    "total": 2,


    "all_node_details": [
        


          {
          
             "id": "FFFFFFFFFFFFFFFF7EC2000000004800",
            "config": {
              "devices": [
                    {
                     "params":[
                      {
                        "name": "fan_7E000048",
                        "ui_type": "Text",
                        "page_name" : "Page1"
                        },
                        {
                        "power":true,
                        "ui_type": "Toggle",
                        "page_name" : "Page1"
                                                                         
                        }
                      ]
                   },
                "primary": "power",
                "type": "efactor.device.exhaust_hood",
                "brand_logo": "https://efactor.tech/files/brand_logo/efactor.jpg"
                    }
                ],
                
                "node_id": "FFFFFFFFFFFFFFFF7EC2000000004800"
            },
            "role": "primary",
            "status": {
                "connectivity": {
                    "connected": false,
                    "timestamp": 1737626019747
                }
            }
        }, 


        {
          
             "id": "FFFFFFFFFFFFFFFF7EC2000000004800",
            "config": {
              "devices": [
                    {
                      "params":[
                      {
                        "name": "fan_7E000048",
                        "ui_type": "Text",
                        "page_name" : "Page1"
                        },
                        {
                        "power":true,
                        "ui_type": "Toggle",
                        "page_name" : "Page1"
                                                                         
                        }
                      ]


                   },
                "primary": "power",
                "type": "efactor.device.exhaust_hood",
                "brand_logo": "https://efactor.tech/files/brand_logo/efactor.jpg"
                    }
                ],
                
                "node_id": "FFFFFFFFFFFFFFFF7EC2000000004800"
            },
            "role": "primary",
            "status": {
                "connectivity": {
                    "connected": false,
                    "timestamp": 1737626019747
                }
            }
        }



    ],
    "nodes": "FFFFFFFFFFFFFFFF7EC2000000004800"
}';



echo $node;

exit;



/************************************************************/

    }

}














/************************************************************/





/*************************************************************/


if (isset($_GET['action']) && $_GET['action'] == 'all_nodes_456') {

    // Retrieve start ID from GET request
    $startid = $_GET['start_id'];

    // Get request headers
    $header = apache_request_headers();
    
    // Extract access token from headers
    $access_token = "";
    foreach ($header as $key => $val) {
        $access_token = $val;
        break; // Use the first header value
    }

    // Query to get all nodes based on access token
    $query_dev = "SELECT node_id FROM efactorproduct WHERE access_token='$access_token'";
    $all_nodes_dynamic = $db->get_results($query_dev);

    // Count number of devices
    $num_dev = $db->num_rows($query_dev);

    // Initialize nodes array
    $all_nodes = [];

    // Loop through each node_id
    foreach ($all_nodes_dynamic as $node_id) {
       $nodeid = $node_id["node_id"];



       $query_node="SELECT * FROM efactorproduct where node_id='$nodeid'";
$single_node_dynamic = $db->get_results($query_node);
$row_node = $db->get_row($query_node);
$model_name=$row_node['model_number'];
$current_devicetype=$row_node['current_devicetype'];
$module_type=$row_node['module_type'];
$device_type=$row_node['device_type'];
$nett_conn=$row_node['internet'];

$vendor_name=$row_node['vendor_name'];



if($vendor_name == 'EFACTOR')

{
$brandname='efactor';
}
if($vendor_name == 'BAJAJ')

{
$brandname='Bajaj';


}

if($vendor_name == 'DHRIO')

{
$brandname='dhrio';


}


if($vendor_name == 'Invendis')

{
$brandname='Invendis';


}


if($vendor_name == 'Skytec')

{
$brandname='Skytec';


}


if($vendor_name == 'exhaust_hood')

{
$brandname='exhaust';


}




/*
if($model_name === 'EFDEFLT V1.0' || $model_name === 'Gateway')

{


 $query_node_efact="SELECT * FROM efactorproduct where node_id='$nodeid'";
$single_node_dynamic = $db->get_results($query_node_efact);
$row_node_efact = $db->get_row($query_node_efact);
$pagename=$row_node_efact['page_name'];

$power = $row_node_efact['power'];
$name = $row_node_efact['name'];






}

else{*/


$param_power = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='power'";
$param_power_dynamic = $db->get_results($param_power);
$row_power = $db->get_row($param_power);
$pagename=$row_power['page_names'];



/*

if($model_name === 'EFDEFLT V1.0' || $model_name === 'Gateway')

{


 $query_node_efact="SELECT * FROM efactorproduct where node_id='$nodeid'";
$single_node_dynamic = $db->get_results($query_node_efact);
$row_node_efact = $db->get_row($query_node_efact);
$pagename=$row_node_efact['page_name'];

$power = $row_node_efact['power'];
$name = $row_node_efact['name'];






}

else{*/

 /*$query_node_11="SELECT * FROM `model_name` where node_id='$nodeid'";
$single_node_dynamic_11 = $db->get_results($query_node_11);
$row_node_11 = $db->get_row($query_node_11);
$power_2=$row_node_11['power'];

*/

$query_node_11 = "SELECT * FROM `$model_name` WHERE node_id='$nodeid'"; 

$single_node_dynamic_11 = $db->get_results($query_node_11);
$row_node_11 = $db->get_row($query_node_11);


    $power_2 = $row_node_11['power'];
    $name = $row_node_11['name'];

    $power = ($power_2 == "01") ? true : false;
     
//}



        $local_control = "enable";
        $ui_description = "Read/Write";
        $primary = "power";
        $efactor = "efactor";
        $device = "device";
        $type = $efactor . "." . $device . "." . $device_type;
        $brand_logo = "https://efactor.tech/files/brand_logo/" . $brandname . ".jpg";

        // Device data structure
        $devices = [
            [
                "id" => $nodeid,
                "module_type"=>$module_type,
                "device_name" => $current_devicetype,
                "name" => $name,
                "power" => $power,
                "ui_type" => "Toggle Switch",
                "page_name" => $pagename,
                "ui_description" => $ui_description,
                "local_control" => $local_control,
                "primary" => $primary,
                "type" => $type,
                "brand_logo" => $brand_logo
            ]
        ];

        // Node data structure
        $nodeData = [
            "config" => [
                "node_id" => $nodeid,
                "node_type" => $module_type,
                "devices" => $devices
                            ],
            "role" => "primary", // You may change this dynamically
            "status" => [
                "connectivity" => [
                    "connected" => $nett_conn, // Change based on your logic
                    "timestamp" => time() * 1000
                ]
            ]
        ];

        // Append to all_nodes array
        $all_nodes[] = $nodeData;
    }

    // Final JSON response
    $response = [
        "url" => "action=all_nodes",
        "token" => $access_token,
        "total" => count($all_nodes),
        "all_nodes" => $all_nodes
    ];

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
exit;
}
/*-----------------------------------------------------------------------*/


if (isset($_GET['action']) && $_GET['action'] == 'all_nodes_31-03-25') {

    // Retrieve start ID from GET request
    $startid = $_GET['start_id'];

    // Get request headers
    $header = apache_request_headers();
    
    // Extract access token from headers
    $access_token = "";
    foreach ($header as $key => $val) {
        $access_token = $val;
        break; // Use the first header value
    }

    // Query to get all nodes based on access token
    $query_dev = "SELECT node_id FROM efactorproduct WHERE access_token='$access_token'";
    $all_nodes_dynamic = $db->get_results($query_dev);

    // Count number of devices
    $num_dev = $db->num_rows($query_dev);

    // Initialize nodes array
    $all_nodes = [];

    // Loop through each node_id
    foreach ($all_nodes_dynamic as $node) {
        $nodeid = $node["node_id"];

        $query_node = "SELECT * FROM efactorproduct WHERE node_id='$nodeid'";
        $single_node_dynamic = $db->get_results($query_node);
        $row_node = $db->get_row($query_node);
        $model_name = $row_node['model_number'];
        $current_devicetype = $row_node['current_devicetype'];
        $module_type = $row_node['module_type'];
        $device_type = $row_node['device_type'];
        $nett_conn = $row_node['internet'];
        $vendor_name = $row_node['vendor_name'];
        $add_gatway=$row_node['add_gatway'];


        if ($vendor_name == 'EFACTOR') {
            $brandname = 'efactor';
        } elseif ($vendor_name == 'BAJAJ') {
            $brandname = 'Bajaj';
        } elseif ($vendor_name == 'exhaust_hood') {
            $brandname = 'exhaust';
        }elseif($vendor_name == 'DHRIO') 
          {
            $brandname='dhrio';
          }

elseif($vendor_name == 'Skytec')

{
$brandname='Skytec';


}
elseif($vendor_name == 'Invendis')

{
$brandname='Invendis';


}




/************************************************/

    
if($model_name == 'EF-003FMN03' || $model_name == 'EF-002FMN02' || $model_name == 'EF-001FMN01' || $model_name == 'BAJAJ-001FMN01')
{

$deviceimage='fan-EF-003FMN03.jpg';
}

else if($model_name == 'EFCKER V1.0')
{

$deviceimage='efact_cooker.jpg';

}
else if ($model_name == 'EF-001LIGHT01' || $model_name == 'EF-002CWLIGHT02' || $model_name == 'EF-001RGBLIGHT01' || $model_name == 'EF-003WLIGHT03') 
{

$deviceimage='light_org.jpg';

}
else if($model_name == 'EF-001OULT01' || $model_name == 'EF-001SOCKET_6A01' || $model_name == 'EF-001SOCKET_10A01')
{


$deviceimage='outlet.jpg';

}
else if($model_name == 'EFSWPCP V1.0')
{
$deviceimage='swpcp.jpg';
}

else 
{
$deviceimage='input_output.jpg';
}


/**********************************************/



        $param_power = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='power'";
        $param_power_dynamic = $db->get_results($param_power);
        $row_power = $db->get_row($param_power);
        $pagename = $row_power['page_names'];

       /* if ($model_name === 'EFDEFLT V1.0' || $model_name === 'Gateway') {
            $query_node_efact = "SELECT * FROM efactorproduct WHERE node_id='$nodeid'";
            $single_node_dynamic = $db->get_results($query_node_efact);
            $row_node_efact = $db->get_row($query_node_efact);
            $pagename = $row_node_efact['page_name'];
            $power = $row_node_efact['power'];
            $name = $row_node_efact['name'];
        } else {*/
            $query_node_11 = "SELECT * FROM `$model_name` WHERE node_id='$nodeid'"; 
            $single_node_dynamic_11 = $db->get_results($query_node_11);
            $row_node_11 = $db->get_row($query_node_11);
            $power_2 = $row_node_11['power'];
            $name = $row_node_11['name'];
            $power = ($power_2 == "01") ? true : false;
       // }

        $local_control = "enable";
        $ui_description = "Read/Write";
        $primary = "power";
        $efactor = "efactor";
        $device = "device";
        $type = $efactor . "." . $device . "." . $device_type;
        $brand_logo = "https://efactor.tech/files/brand_logo/" . $brandname . ".jpg";
       // $device_image = "https://efactor.tech/files/brand_logo/fan-EF-003FMN03.jpg";
         $device_image = "https://efactor.tech/files/brand_logo/". $deviceimage;



 $query_unread = "SELECT COUNT(*) AS `unread_count` FROM call_service WHERE node_id = '$node_id' AND status = 'unread'";
$row_unread = $db->get_row($query_unread);

// Convert object to array and access key safely
$unread_count = '0';
if ($row_unread) {
    foreach ($row_unread as $key => $val) {
        if (trim($key) === 'unread_count') {
            $unread_count = trim((string)$val); // Ensure it's a trimmed string
        }
    }
}


if ($unread_count === '' || $unread_count === null) {
    $unread_count = '0';
}


        

        // Device data structure
        $devices = [
            [
                "id" => $nodeid,
                "module_type" => $module_type,
                "device_name" => $current_devicetype,
                "name" => $name,
                "power" => $power,
                "ui_type" => "Toggle Switch",
                "page_name" => $pagename,
                "ui_description" => $ui_description,
                "local_control" => $local_control,
                "primary" => $primary,
                "type" => $type,
                "brand_logo" => $brand_logo,
                "device_image" => $device_image,
                 "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
                 "chat_status"=>$unread_count,
                "device_p2p_control"=>"disable",
                "Device Communication"=>"disable",
                "Status"=>"disable"
            ]
        ];

        // Node data structure
        $nodeData = [
            "config" => [
                "node_id" => $nodeid,
                "node_type" => $module_type,
                "devices" => $devices
            ],
            "role" => "primary", // You may change this dynamically
            "status" => [
                "connectivity" => [
                    "connected" => $nett_conn, // Change based on your logic
                    "timestamp" => time() * 1000
                ]
            ]
        ];

        // Append to all_nodes array
        $all_nodes[] = $nodeData;
    }

    // Final JSON response
    $response = [
        "url" => "action=all_nodes",
        "token" => $access_token,
        "total" => count($all_nodes),
        "all_nodes" => $all_nodes
    ];

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}








/*----------------------------------------------------------------------*/


if (isset($_GET['action']) && $_GET['action'] == 'all_nodes') {
    $startid = $_GET['start_id'] ?? '';
    $header = apache_request_headers();

    // Extract access token
    $access_token = '';
    foreach ($header as $key => $val) {
        $access_token = $val;
        break;
    }

    // Fetch all devices
    $all = $db->get_results("SELECT * FROM efactorproduct WHERE access_token='$access_token'");
    $gateways = [];
    $individuals = [];

    foreach ($all as $device) {
        $node_id = $device['node_id'];
        $add_gateway = trim($device['add_gateway']);
        $is_added = $device['is_added'];

        if (!empty($add_gateway) && $add_gateway !== 'NULL') {
            // Group under the gateway
            if (!isset($gateways[$add_gateway])) {
                $gateways[$add_gateway] = [
                    'gateway' => null,
                    'devices' => []
                ];
            }

            if ($node_id == $add_gateway || $is_added == '1') {
                $gateways[$add_gateway]['gateway'] = $device;
            } else {
                $gateways[$add_gateway]['devices'][] = $device;
            }
        } else {
            $individuals[] = $device;
        }
    }

    $all_nodes = [];

    // Gateway with grouped devices
    foreach ($gateways as $gateway_id => $data) {
        if ($data['gateway']) {
            $devices = array_merge([$data['gateway']], $data['devices']);
            $all_nodes[] = buildNode($data['gateway'], $devices);
        } else {
            // Gateway missing but devices exist (add as individual devices)
            foreach ($data['devices'] as $dev) {
                $all_nodes[] = buildNode($dev, [$dev]);
            }
        }
    }

    // Standalone individual devices
    foreach ($individuals as $device) {
        $all_nodes[] = buildNode($device, [$device]);
    }

    // Final output
    echo json_encode([
        "url" => "action=all_nodes",
        "token" => $access_token,
        "total" => count($all_nodes),
        "all_nodes" => $all_nodes
    ], JSON_PRETTY_PRINT);
}

function buildNode($config_device, $devices_list) {
    global $db;
    $devices = [];

    foreach ($devices_list as $dev) {
        $model = $dev['model_number'];
        $device_type = $dev['device_type'];
        $device_name = $dev['name'];
        $power = getDevicePower($model, $dev['node_id'], $db);

        $devices[] = [
            "id" => $dev['node_id'],
            "module_type" => $dev['module_type'],
            "device_name" => $device_name,
            "name" => $device_name,
            "power" => $power,
            "ui_type" => "Toggle Switch",
            "page_name" => "Page1.Operation",
            "ui_description" => "Read/Write",
            "local_control" => "enable",
            "primary" => "power",
            "type" => "efactor.device.$device_type",
            "brand_logo" => "https://efactor.tech/files/brand_logo/efactor.jpg",
            "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=" . $dev['node_id'],
            "chat_status" => "0",
            "device_image" => getDeviceImage($model),
            "device_p2p_control" => "disable",
            "Device Communication" => "disable",
            "Status" => "disable"
        ];
    }

    return [
        "config" => [
            "node_id" => $config_device['node_id'],
            "node_type" => $config_device['module_type'],
            "devices" => $devices
        ],
        "role" => "primary",
        "status" => [
            "connectivity" => [
                "connected" => $config_device['internet'] ?? "false",
                "timestamp" => time() * 1000
            ]
        ]
    ];
}

function getDevicePower($model, $node_id, $db) {
    if ($model == 'EFDEFLT V1.0') {
        $row = $db->get_row("SELECT power FROM efactorproduct WHERE node_id='$node_id'");
    } else {
        $row = $db->get_row("SELECT power FROM `$model` WHERE node_id='$node_id'");
    }
    return isset($row['power']) && $row['power'] == '01';
}

function getDeviceImage($model) {
    switch ($model) {
        case 'EF-003FMN03':
        case 'EF-002FMN02':
        case 'EF-001FMN01':
        case 'BAJAJ-001FMN01':
            return "https://efactor.tech/files/brand_logo/fan-EF-003FMN03.jpg";
        case 'EFCKER V1.0':
            return "https://efactor.tech/files/brand_logo/efact_cooker.jpg";
        case 'EF-001LIGHT01':
        case 'EF-002CWLIGHT02':
        case 'EF-001RGBLIGHT01':
        case 'EF-003WLIGHT03':
            return "https://efactor.tech/files/brand_logo/light_org.jpg";
        case 'EF-001OULT01':
        case 'EF-001SOCKET_6A01':
        case 'EF-001SOCKET_10A01':
            return "https://efactor.tech/files/brand_logo/outlet.jpg";
        case 'EFSWPCP V1.0':
            return "https://efactor.tech/files/brand_logo/swpcp.jpg";
        case 'INVENDIS_MFM_V1':
            return "https://efactor.tech/files/brand_logo/invendis_device.jpg";
        case 'EF_RF_GATEW V1.0':
            return "https://efactor.tech/files/brand_logo/gateway_org.jpg";
        default:
            return "https://efactor.tech/files/brand_logo/input_output.jpg";
    }
}
















/***********************************************************/





if (isset($_GET['action']) && $_GET['action'] == 'all_nodes_05-06-25') {

    // Retrieve start ID from GET request
    $startid = $_GET['start_id'];

    // Get request headers
    $header = apache_request_headers();
    
    // Extract access token from headers
    $access_token = "";
    foreach ($header as $key => $val) {
        $access_token = $val;
        break; // Use the first header value
    }

    // Query to get all nodes based on access token using prepared statement
   /* $query_dev = $db->prepare("SELECT node_id FROM efactorproduct WHERE access_token = '$val'");
    $query_dev->execute([$access_token]);
    $all_nodes_dynamic = $query_dev->fetchAll();*/


    $query_dev = "SELECT node_id FROM efactorproduct WHERE access_token='$access_token'";
    $all_nodes_dynamic = $db->get_results($query_dev);


    // Count number of devices
    $num_dev = count($all_nodes_dynamic);

    // Initialize nodes array
    $all_nodes = [];

    // Loop through each node_id
    foreach ($all_nodes_dynamic as $node) {
        $nodeid = $node["node_id"];



        // Use prepared statement for node query
      /*  $query_node = $db->prepare("SELECT * FROM efactorproduct WHERE node_id = ?");
        $query_node->execute([$nodeid]);
        $row_node = $query_node->fetch(PDO::FETCH_ASSOC);*/

        $query_node = "SELECT * FROM efactorproduct WHERE node_id='$nodeid'";
        $single_node_dynamic = $db->get_results($query_node);
        $row_node = $db->get_row($query_node);

        
        $model_name = $row_node['model_number'];
        $current_devicetype = $row_node['current_devicetype'];
        $module_type = $row_node['module_type'];
        $device_type = $row_node['device_type'];
        $nett_conn = $row_node['internet'];
        $access_token = $row_node['access_token'];
        $vendor_name = $row_node['vendor_name'];
        $add_gateway = $row_node['add_gateway'];
        $is_added = $row_node['is_added'];
        $userid = $row_node['user_id'];

switch ($vendor_name) {
    case 'EFACTOR':
        $brandname = 'efactor';
        break;
    case 'BAJAJ':
        $brandname = 'Bajaj';
        break;
    case 'DHRIO':
        $brandname = 'dhrio';
        break;
    case 'Invendis':
        $brandname = 'Invendis';
        break;
    case 'Skytec':
        $brandname = 'Skytec';
        break;
    case 'exhaust_hood':
        $brandname = 'exhaust';
        break;
    default:
        $brandname = 'efactor';
        break;
}


        // Determine device image
        if ($model_name == 'EF-003FMN03' || $model_name == 'EF-002FMN02' || $model_name == 'EF-001FMN01' || $model_name == 'BAJAJ-001FMN01') {
            $deviceimage = 'fan-EF-003FMN03.jpg';
        } elseif ($model_name == 'EFCKER V1.0') {
            $deviceimage = 'efact_cooker.jpg';
        } elseif ($model_name == 'EF-001LIGHT01' || $model_name == 'EF-002CWLIGHT02' || $model_name == 'EF-001RGBLIGHT01' || $model_name == 'EF-003WLIGHT03') {
            $deviceimage = 'light_org.jpg';
        } elseif ($model_name == 'EF-001OULT01' || $model_name == 'EF-001SOCKET_6A01' || $model_name == 'EF-001SOCKET_10A01') {
            $deviceimage = 'outlet.jpg';
        } elseif ($model_name == 'EFSWPCP V1.0') {
            $deviceimage = 'swpcp.jpg';
        } elseif ($model_name == 'INVENDIS_MFM_V1') {
            $deviceimage = 'invendis_device.jpg';
        }

elseif ($model_name == 'EF_RF_GATEW V1.0') {
            $deviceimage = 'gateway_org.jpg';
        }




else {
            $deviceimage = 'input_output.jpg';
        }

      


            $param_power = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='power'";
        $param_power_dynamic = $db->get_results($param_power);
        $row_power = $db->get_row($param_power);
        $pagename = $row_power['page_names'];


         $query_node_11 = "SELECT * FROM `$model_name` WHERE node_id='$nodeid'"; 
            $single_node_dynamic_11 = $db->get_results($query_node_11);
            $row_node_11 = $db->get_row($query_node_11);
        $power_2 = $row_node_11['power'];
        $name = $row_node_11['name'];
        $power = ($power_2 == "01") ? true : false;

        $local_control = "enable";
        $ui_description = "Read/Write";
        $primary = "power";
        $efactor = "efactor";
        $device = "device";
        $type = $efactor . "." . $device . "." . $device_type;
        $brand_logo = "https://efactor.tech/files/brand_logo/" . $brandname . ".jpg";
        $device_image = "https://efactor.tech/files/brand_logo/" . $deviceimage;


 $query_unread = "SELECT COUNT(*) AS `unread_count` FROM call_service WHERE node_id = '$node_id' AND status = 'unread'";
$row_unread = $db->get_row($query_unread);

// Convert object to array and access key safely
$unread_count = '0';
if ($row_unread) {
    foreach ($row_unread as $key => $val) {
        if (trim($key) === 'unread_count') {
            $unread_count = trim((string)$val); // Ensure it's a trimmed string
        }
    }
}


if ($unread_count === '' || $unread_count === null) {
    $unread_count = '0';
}



        if ($add_gateway == 'NULL') {



            $devices = [
                [
                    "id" => $nodeid,
                    "module_type" => $module_type,
                    "device_name" => $current_devicetype,
                    "name" => $name,
                    "power" => $power,
                    "ui_type" => "Toggle Switch",
                    "page_name" => $pagename,
                    "ui_description" => $ui_description,
                    "local_control" => $local_control,
                    "primary" => $primary,
                    "type" => $type,
                    "brand_logo" => $brand_logo,
                     "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
                     "chat_status"=>$unread_count,
                    "device_image" => $device_image,
                    "device_p2p_control"=>"disable",
                "Device Communication"=>"disable",
                "Status"=>"disable"
                ]
            ];

            $nodeData = [
                "config" => [
                    "node_id" => $nodeid,
                    "node_type" => $module_type,
                    "devices" => $devices
                ],
                "role" => "primary",
                "status" => [
                    "connectivity" => [
                        "connected" => $nett_conn,
                        "timestamp" => time() * 1000
                    ]
                ]
            ];




        } else {







if($is_added == 1)
{



$query_gateway = "SELECT * FROM `efactorproduct` WHERE add_gateway ='$add_gateway' and is_added='$is_added'"; 
            $single_node_dynamic_gateway = $db->get_results($query_gateway);
            $row_node_gateway = $db->get_row($query_gateway);
            $node_id = $row_node_gateway["node_id"];
                $module_type_gate = $row_node_gateway["module_type"];




             $query_gateway_11 = "SELECT node_id FROM `efactorproduct` WHERE add_gateway ='$add_gateway' and access_token='$access_token'"; 
            $all_devices_dynamic = $db->get_results($query_gateway_11);





            
            $all_devices = [];
            foreach ($all_devices_dynamic as $node_gate) {
                $nodeid = $node_gate["node_id"];
              //  $module_type_= $node_gate["module_type"];

    $query_node = "SELECT * FROM efactorproduct WHERE node_id='$nodeid'";
        $single_node_dynamic = $db->get_results($query_node);
        $row_node = $db->get_row($query_node);

        
        $model_name = $row_node['model_number'];
        $current_devicetype = $row_node['current_devicetype'];
        $module_type = $row_node['module_type'];
        $device_type = $row_node['device_type'];
        $nett_conn = $row_node['internet'];
        $access_token = $row_node['access_token'];
        $vendor_name = $row_node['vendor_name'];
        $add_gateway = $row_node['add_gateway'];
        $is_added = $row_node['is_added'];
        $userid = $row_node['user_id'];




 $param_power = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='power'";
        $param_power_dynamic = $db->get_results($param_power);
        $row_power = $db->get_row($param_power);
        $pagename = $row_power['page_names'];

        
         $query_node_11 = "SELECT * FROM `$model_name` WHERE node_id='$nodeid'"; 
            $single_node_dynamic_11 = $db->get_results($query_node_11);
            $row_node_11 = $db->get_row($query_node_11);
        $power_2 = $row_node_11['power'];
        $name = $row_node_11['name'];
        $power = ($power_2 == "01") ? true : false;

        $local_control = "enable";
        $ui_description = "Read/Write";
        $primary = "power";
        $efactor = "efactor";
        $device = "device";
        $type = $efactor . "." . $device . "." . $device_type;
        $brand_logo = "https://efactor.tech/files/brand_logo/" . $brandname . ".jpg";
        $device_image = "https://efactor.tech/files/brand_logo/" . $deviceimage;


 $query_unread = "SELECT COUNT(*) AS `unread_count` FROM call_service WHERE node_id = '$node_id' AND status = 'unread'";
$row_unread = $db->get_row($query_unread);

// Convert object to array and access key safely
$unread_count = '0';
if ($row_unread) {
    foreach ($row_unread as $key => $val) {
        if (trim($key) === 'unread_count') {
            $unread_count = trim((string)$val); // Ensure it's a trimmed string
        }
    }
}


if ($unread_count === '' || $unread_count === null) {
    $unread_count = '0';
}







                $devices = [
                    
                        "id" => $nodeid,
                        "module_type" => $module_type,
                        "device_name" => $current_devicetype,
                        "name" => $name,
                        "power" => $power,
                        "ui_type" => "Toggle Switch",
                        "page_name" => $pagename,
                        "ui_description" => $ui_description,
                        "local_control" => $local_control,
                        "primary" => $primary,
                        "type" => $type,
                        "brand_logo" => $brand_logo,
                         "Need_Help" => "https://efactor.tech/v8/call_services.php?node_id=$node_id",
                         "chat_status"=>$unread_count,
                        "device_image" => $device_image,
                        "device_p2p_control"=>"disable",
                "Device Communication"=>"disable",
                "Status"=>"disable"
                    
                ];

                $all_devices[] = $devices;
              //  print_r($all_devices)."\n";
            }

            if (!empty($all_devices_dynamic)) {
                $nodeData = [
                    "config" => [
                        "node_id" => $node_id,
                        "node_type" => $module_type_gate,
                        "devices" => $all_devices
                    ],
                    "role" => "primary",
                    "status" => [
                        "connectivity" => [
                            "connected" => $nett_conn,
                            "timestamp" => time() * 1000
                        ]
                    ]
                ];
             

}
 
            }
        }

        if (isset($nodeData)) {
            $all_nodes[] = $nodeData;
        }
    }

    // Final JSON response
    $response = [
        "url" => "action=all_nodes",
        "token" => $access_token,
        "total" => count($all_nodes),
        "all_nodes" => $all_nodes
    ];

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}










if (isset($_GET['action']) && $_GET['action'] == 'ws_add_node') {


$nodeid = $_GET['nodeid'];

$query_node="SELECT * FROM efactorproduct where node_id='$nodeid'";
$single_node_dynamic = $db->get_results($query_node);
$row_node = $db->get_row($query_node);
$access_token=$row_node['access_token'];



$response = [
        "url" => "action=add_node",
        "token" => $access_token,
        "node_id" => $nodeid,
        "device_id" => $nodeid
    ];



}





if (isset($_GET['action']) && $_GET['action'] == 'ws_remove_node') {


$nodeid = $_GET['nodeid'];

$query_node="SELECT * FROM efactorproduct where node_id='$nodeid'";
$single_node_dynamic = $db->get_results($query_node);
$row_node = $db->get_row($query_node);
$access_token=$row_node['access_token'];



$response = [
        "url" => "action=add_node",
        "token" => $access_token,
        "node_id" => $nodeid,
        "device_id" => $nodeid
    ];



}



/*************************************************************/


if (isset($_GET['action']) && $_GET['action'] == 'getAllNodesStatus_31-03-25') {

    
    // Get request headers
    $header = apache_request_headers();
    
    // Extract access token from headers
    $access_token = "";
    foreach ($header as $key => $val) {
        $access_token = $val;
        break; // Use the first header value
    }

    // Query to get all nodes based on access token
    $query_dev = "SELECT node_id FROM efactorproduct WHERE access_token='$access_token'";
    $all_nodes_dynamic = $db->get_results($query_dev);

    
    $num_dev = $db->num_rows($query_dev);

    
    $all_nodes = [];

 
    foreach ($all_nodes_dynamic as $node_id) {
       $nodeid = $node_id["node_id"];



       $query_node="SELECT * FROM efactorproduct where node_id='$nodeid'";
$single_node_dynamic = $db->get_results($query_node);
$row_node = $db->get_row($query_node);
$model_name=$row_node['model_number'];
//$current_devicetype=$row_node['current_devicetype'];
$device_type=$row_node['device_type'];
$nett_conn=$row_node['internet'];


$query_nodeid="SELECT * FROM `$model_name` where node_id='$nodeid'";
$single_nodeid_dynamic = $db->get_results($query_nodeid);
$row_nodeid = $db->get_row($query_nodeid);
$power_1=$row_nodeid['power'];
$name=$row_nodeid['name'];



$power = ($power_1 == "01") ? true : false;

       
        // Node data structure
        $nodeData = [
            "node_id" => $nodeid,

           "devices" => [
        [
            "id" => $nodeid,
            "power" => $power, 
            "name" => $name 
        ]
    ],



            "status" => [
                "connectivity" => [
                    "connected" => $nett_conn, // Change based on your logic
                    "timestamp" => time() * 1000
                ]
            ]
        ];

        // Append to all_nodes array
        $all_nodes[] = $nodeData;
    }

    // Final JSON response
    $response = [
        "url" => "action=getAllNodesStatus",
        "token" => $access_token,
        "all_nodes" => $all_nodes
    ];

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
}



/***********************************************************/


/*

if (isset($_GET['action']) && $_GET['action'] == 'getAllNodesStatus') {
    
    // Get request headers
    $header = apache_request_headers();
    
    // Extract access token from headers
    $access_token = "";
    foreach ($header as $key => $val) {
        $access_token = $val;
        break; // Use the first header value
    }

    // Query to get all nodes based on access token (using prepared statement)
  

 $query_dev = "SELECT node_id FROM efactorproduct WHERE access_token='$access_token'";
    $all_nodes_dynamic = $db->get_results($query_dev);

    $all_nodes = [];

    foreach ($all_nodes_dynamic as $node_id) {
        $nodeid = $node_id["node_id"];

        
 $query_node="SELECT * FROM efactorproduct where node_id='$nodeid'";
$single_node_dynamic = $db->get_results($query_node);
$row_node = $db->get_row($query_node);

        
        $model_name = $row_node['model_number'];
        $device_type = $row_node['device_type'];
        $nett_conn = $row_node['internet'];
        $add_gateway = $row_node['add_gateway'];
        $is_added = $row_node['is_added'];

      

$query_nodeid="SELECT * FROM `$model_name` where node_id='$nodeid'";
$single_nodeid_dynamic = $db->get_results($query_nodeid);
$row_nodeid = $db->get_row($query_nodeid);

        
        $power_1 = $row_nodeid['power'];
        $name = $row_nodeid['name'];
        $power = ($power_1 == "01") ? true : false;

        if ($add_gateway == 'NULL') {

            $nodeData = [
                "node_id" => $nodeid,
                "devices" => [
                    [
                        "id" => $nodeid,
                        "power" => $power, 
                        "name" => $name 
                    ]
                ],
                "status" => [
                    "connectivity" => [
                        "connected" => $nett_conn,
                        "timestamp" => time() * 1000
                    ]
                ]
            ];
        } else {


            if ($is_added != 'NULL') {
                

                $query_gateway = "SELECT * FROM `efactorproduct` WHERE add_gateway ='$add_gateway' and is_added='$is_added'"; 
            $single_node_dynamic_gateway = $db->get_results($query_gateway);
            $row_node_gateway = $db->get_row($query_gateway);

                               
                $node_id = $row_node_gateway["node_id"];
                $module_type_gate = $row_node_gateway["module_type"];

                


 $query_gateway_11 = "SELECT node_id FROM `efactorproduct` WHERE is_added ='1' and access_token='$access_token'"; 
            $all_devices_dynamic = $db->get_results($query_gateway_11);

    
//print_r($all_devices_dynamic);

                $all_devices = [];
                foreach ($all_devices_dynamic as $node_gate) {
                    $nodeid = $node_gate["node_id"];

                    $query_node="SELECT node_id FROM efactorproduct where add_gateway='$nodeid'";
                    $single_node_dynamic = $db->get_results($query_node);

//print_r($single_node_dynamic);

                   foreach ($single_node_dynamic as $node_dev) {

                    $row_node = $db->get_row($query_node);                    
                    $model_name = $row_node['model_number'];
                    $nett_conn = $row_node['internet'];
                     $name = $row_node['name'];

                    $query_nodeid="SELECT * FROM `$model_name` where node_id='$nodeid'";
                    $single_nodeid_dynamic_id = $db->get_results($query_nodeid);
                    $row_nodeid = $db->get_row($query_nodeid);
                    $power_1 = $row_nodeid['power'];
                   
                    $power = ($power_1 == "01") ? true : false;
               
                   


                    $devices = [
                        "id" => $node_dev,
                        "power" => $power, 
                        "name" => $name 
                    ];

                       $all_devices[] = $devices;

                  

                   }

                        


//print_r($all_devices);

        //  $query_node_gate="SELECT * FROM efactorproduct where node_id='$nodeid' and is_added='1'";


$nodeData = [
                    "node_id" => $nodeid,
                    "devices" => $all_devices,
                    "status" => [
                        "connectivity" => [
                            "connected" => $nett_conn,
                            "timestamp" => time() * 1000
                        ]
                    ]
                ];




                }

                
            }

        }



 



        if (isset($nodeData)) {
            $all_nodes[] = $nodeData;
        }
    }

    // Final JSON response
    $response = [
        "url" => "action=getAllNodesStatus",
        "token" => $access_token,
        "all_nodes" => $all_nodes
    ];

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}*/




if (isset($_GET['action']) && $_GET['action'] == 'getAllNodesStatus') {
    
    // Get request headers
    $header = apache_request_headers();
    
    // Extract access token from headers
    $access_token = "";
    foreach ($header as $key => $val) {
        $access_token = $val;
        break; // Use the first header value
    }

    // Query to get all nodes based on access token (using prepared statement)
  

 $query_dev = "SELECT node_id FROM efactorproduct WHERE access_token='$access_token'";
    $all_nodes_dynamic = $db->get_results($query_dev);



    $all_nodes = [];

    foreach ($all_nodes_dynamic as $node_id) {
        $nodeid = $node_id["node_id"];



        
 $query_node="SELECT * FROM efactorproduct where node_id='$nodeid'";
$single_node_dynamic = $db->get_results($query_node);
$row_node = $db->get_row($query_node);

        
        $model_name = $row_node['model_number'];
        $device_type = $row_node['device_type'];
        $nett_conn = $row_node['internet'];
        $add_gateway = $row_node['add_gateway'];
        $is_added = $row_node['is_added'];

      if($model_name == 'EFDEFLT V1.0')
{

$power_1 = $row_node['power'];
        $name = $row_node['name'];
        $power = ($power_1 == "01") ? true : false;



}
else{

$query_nodeid="SELECT * FROM `$model_name` where node_id='$nodeid'";
$single_nodeid_dynamic = $db->get_results($query_nodeid);
$row_nodeid = $db->get_row($query_nodeid);

        
        $power_1 = $row_nodeid['power'];
        $name = $row_nodeid['name'];
        $power = ($power_1 == "01") ? true : false;

}

        if ($add_gateway == 'NULL') {



            $nodeData = [
                "node_id" => $nodeid,
                "devices" => [
                    [
                        "id" => $nodeid,
                        "power" => $power, 
                        "name" => $name 
                    ]
                ],
                "status" => [
                    "connectivity" => [
                        "connected" => $nett_conn,
                        "timestamp" => time() * 1000
                    ]
                ]
            ];
        } else {




            if ($is_added != 'NULL') {
                

                $query_gateway = "SELECT * FROM `efactorproduct` WHERE add_gateway ='$add_gateway' and is_added='$is_added'"; 
            $single_node_dynamic_gateway = $db->get_results($query_gateway);
            $row_node_gateway = $db->get_row($query_gateway);

                               
                $node_id = $row_node_gateway["node_id"];
                $module_type_gate = $row_node_gateway["module_type"];

                


 $query_gateway_11 = "SELECT node_id FROM `efactorproduct` WHERE is_added ='1' and access_token='$access_token'"; 
            $all_devices_dynamic = $db->get_results($query_gateway_11);

    


                $all_devices = [];
                foreach ($all_devices_dynamic as $node_gate) {
                    $nodeid = $node_gate["node_id"];

                    $query_node="SELECT node_id FROM efactorproduct where add_gateway='$nodeid'";
                    $single_node_dynamic = $db->get_results($query_node);



                   foreach ($single_node_dynamic as $node_dev) {

                 $nodeid_gate = $node_dev["node_id"];

                    $query_node="SELECT * FROM efactorproduct where node_id='$nodeid_gate'";

                    $row_node = $db->get_row($query_node);                    
                    $model_name = $row_node['model_number'];
                    $nett_conn = $row_node['internet'];
                     $name = $row_node['name'];

                    $query_nodeid="SELECT * FROM `$model_name` where node_id='$nodeid_gate'";
                    $single_nodeid_dynamic_id = $db->get_results($query_nodeid);
                    $row_nodeid = $db->get_row($query_nodeid);
                    $power_1 = $row_nodeid['power'];
                   
                    $power = ($power_1 == "01") ? true : false;
               
                   


                    $devices = [
                        "id" => $nodeid_gate,
                        "power" => $power, 
                        "name" => $name 
                    ];

                       
                       $all_devices[] = $devices;
                  

                   }

                        
                  




$nodeData = [
                    "node_id" => $nodeid,
                    "devices" => $all_devices,
                    "status" => [
                        "connectivity" => [
                            "connected" => $nett_conn,
                            "timestamp" => time() * 1000
                        ]
                    ]
                ];




                }

                
            }

        }



 



        if (isset($nodeData)) {

          if (($add_gateway == 'NULL') || ($add_gateway != 'NULL') && ($is_added == '1'))
              {
          
            $all_nodes[] = $nodeData;

              }




        }
    }






/*
if (isset($nodeData)) {
            $all_nodes[] = $nodeData;
        }
*/




    // Final JSON response
    $response = [
        "url" => "action=getAllNodesStatus",
        "token" => $access_token,
        "all_nodes" => $all_nodes
    ];

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}


/************************************************************/






















/************************************************************/


/*
if (isset($_GET['action']) && $_GET['action'] == 'getAllNodesStatus') {

    // Get request headers
    $header = apache_request_headers();

    // Extract access token from headers
    $access_token = "";
    foreach ($header as $key => $val) {
        $access_token = $val;
        break; // Use the first header value
    }

    // Get all node_ids for the access token
    $query_dev = "SELECT node_id FROM efactorproduct WHERE access_token='$access_token'";
    $all_nodes_dynamic = $db->get_results($query_dev);

    $all_nodes = [];

    foreach ($all_nodes_dynamic as $node_id_row) {
        $nodeid = $node_id_row["node_id"];

        // Get basic info of the node
        $query_node = "SELECT * FROM efactorproduct WHERE node_id='$nodeid'";
        $row_node = $db->get_row($query_node);

        if (!$row_node) continue;

        $model_name = $row_node['model_number'];
        $device_type = $row_node['device_type'];
        $nett_conn = $row_node['internet'];
        $add_gateway = $row_node['add_gateway'];
        $is_added = $row_node['is_added'];

        // ✅ Skip this node if it is a child added under a gateway
        if ($add_gateway != 'NULL' && $is_added != 'NULL') {
            continue;
        }

        // 🟢 Standalone node or gateway
        // Get device info from model table
        $query_nodeid = "SELECT * FROM `$model_name` WHERE node_id='$nodeid'";
        $row_nodeid = $db->get_row($query_nodeid);

        $power_1 = $row_nodeid['power'] ?? '00';
        $name = $row_nodeid['name'] ?? 'unknown';
        $power = ($power_1 == "01");

        // Prepare device entry for this node
        $device_entry = [
            "id" => $nodeid,
            "power" => $power,
            "name" => $name
        ];

        $devices = [$device_entry]; // Initialize with current node

echo $nodeid;
echo $access_token;

        // 🟠 Check if it is a gateway with child nodes
        $query_gateway_11 = "SELECT node_id FROM efactorproduct WHERE add_gateway ='$nodeid' AND access_token='$access_token'";
        $all_devices_dynamic = $db->get_results($query_gateway_11);


print_r($all_devices_dynamic);

        if (!empty($all_devices_dynamic)) {
            foreach ($all_devices_dynamic as $node_gate_row) {
                $child_nodeid = $node_gate_row["node_id"];

                $query_node = "SELECT * FROM efactorproduct WHERE node_id='$child_nodeid'";
                $child_node = $db->get_row($query_node);

                if (!$child_node) continue;

                $child_model_name = $child_node['model_number'];
                $nett_conn = $child_node['internet'];
                $child_name = $child_node['name'];

                $query_child_model = "SELECT * FROM `$child_model_name` WHERE node_id='$child_nodeid'";
                $child_row_model = $db->get_row($query_child_model);

                $power_1 = $child_row_model['power'] ?? '00';
                $power = ($power_1 == "01");

                $devices[] = [
                    "id" => $child_nodeid,
                    "power" => $power,
                    "name" => $child_name
                ];
            }
        }

        // ✅ Final node data
        $nodeData = [
            "node_id" => $nodeid,
            "devices" => $devices,
            "status" => [
                "connectivity" => [
                    "connected" => $nett_conn,
                    "timestamp" => time() * 1000
                ]
            ]
        ];

        $all_nodes[] = $nodeData;
    }

    // Final JSON response
    $response = [
        "url" => "action=getAllNodesStatus",
        "token" => $access_token,
        "all_nodes" => $all_nodes
    ];

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}


*/
/*************************************************************/

if (isset($_GET['action']) && $_GET['action']=='all_nodes_static')
{

$startid =$_GET['start_id'];
                        
			$header = apache_request_headers();  
			foreach ($header as $key => $val) {


$node='{
    "url": "action=all_nodes",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJwaG9uZV92ZXJpZmllZCI6dHJ1ZSwiY3VzdG9tOnVzZXJfaWQiOiIyMzYiLCJ0b2tlbl91c2UiOiJpZCIsImF1dGhfdGltZSI6MTY2NzAyMjI0MiwiZXhwIjoxNjY3MDI1ODQyLCJpYXQiOjE2NjcwMjIyNDIsInBob25lIjoiNjMwNDE2MDQ2MyJ9.nVGJjN4iYj2JGUj20XeSeqiKoVBj9r5qQoVxFucaD58",
    "total": 2,
    "all_nodes": [
        {
            
		
            "config": {
                  "node_id": "FFFFFFFFFFFFFFFF7EC2000000036A00",
	          "devices":[
			   {
                             "id": "FFFFFFFFFFFFFFFF7EC2000000036A00",
				"device_name": "fan_7E00036A",
                "power": true,
                "ui_type": "Toggle Switch",
                "page_name": "Page1",
                "ui_description": "Read/Write",
                "local_control": "enable", 
                "primary": "power",
                "type": "efactor.device.fan",
				"brand_logo": "https://efactor.tech/files/brand_logo/" . $brandname . ".jpg"
			  }
			]
            },
            
            "role": "primary",
            "status": {
                "connectivity": {
                    "connected": false,
                    "timestamp": 1737626019747
                }
            }
        },
        {
            
            "config": {
                        "node_id": "FFFFFFFFFFFFFFFF7EC2000000037500",
			"devices":[ 
				{
                   "id": "FFFFFFFFFFFFFFFF7EC2000000037500",
                "device_name": "fan_7E000375",
               
                "power": true,
                "ui_type": "Toggle Switch",
                "page_name": "Page1",
                "ui_description": "Read/Write",
                "local_control": "enable", 
                "primary": "brightness",
                "type": "efactor.device.fan",
                "brand_logo": "https://efactor.tech/files/brand_logo/" . $brandname . ".jpg"  
				}
				]
            },
            
            "role": "secondary",
            "status": {
                "connectivity": {
                    "connected": true,
                    "timestamp": 1737627023456
                }
            }
        }
     ]
}';


echo $node;

exit;



/************************************************************/

    }

}



/*/all_nodes_10/*/

if (isset($_GET['action']) && $_GET['action']=='all_nodes_10')
{

$startid =$_GET['start_id'];
                        
			$header = apache_request_headers();  
			foreach ($header as $key => $val) {


$node='{
    "url": "action=all_nodes",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJwaG9uZV92ZXJpZmllZCI6dHJ1ZSwiY3VzdG9tOnVzZXJfaWQiOiIyMzYiLCJ0b2tlbl91c2UiOiJpZCIsImF1dGhfdGltZSI6MTY2NzAyMjI0MiwiZXhwIjoxNjY3MDI1ODQyLCJpYXQiOjE2NjcwMjIyNDIsInBob25lIjoiNjMwNDE2MDQ2MyJ9.nVGJjN4iYj2JGUj20XeSeqiKoVBj9r5qQoVxFucaD58",
    "total": 10,
    "all_nodes": [
        {
            "id": "FFFFFFFFFFFFFFFF7EC2000000036A00",
            "config": {
                "devices": [
                    {
                        "device_name": "fan_7E00036A",
                        "power": true,
                        "ui_type": "Toggle",
                        "page_name": "Page1",
                        "ui_description": "Read/Write",
                        "local_control": "enable",
                        "primary": "power",
                        "type": "efactor.device.fan",
                        "brand_logo": "https://efactor.tech/files/brand_logo/efactor.jpg"
                    }
                ]
            },
            "node_id": "FFFFFFFFFFFFFFFF7EC2000000036A00",
            "role": "primary",
            "status": {
                "connectivity": {
                    "connected": false,
                    "timestamp": 1737626019747
                }
            }
        },
        {
            "id": "FFFFFFFFFFFFFFFF7EC2000000037500",
            "config": {
                "devices": [
                    {
                        "device_name": "fan_7E000375",
                        "power": true,
                        "ui_type": "Toggle",
                        "page_name": "Page1",
                        "ui_description": "Read/Write",
                        "local_control": "enable",
                        "primary": "brightness",
                        "type": "efactor.device.fan",
                        "brand_logo": "https://efactor.tech/files/brand_logo/efactor_light.jpg"
                    }
                ]
            },
            "node_id": "FFFFFFFFFFFFFFFF7EC2000000037500",
            "role": "secondary",
            "status": {
                "connectivity": {
                    "connected": true,
                    "timestamp": 1737627023456
                }
            }
        },
        {
            "id": "FFFFFFFFFFFFFFFF7EC2000000036B00",
            "config": {
                "devices": [
                    {
                        "device_name": "fan_7E00036B",
                        "power": true,
                        "ui_type": "Toggle",
                        "page_name": "Page1",
                        "ui_description": "Read/Write",
                        "local_control": "enable",
                        "primary": "power",
                        "type": "efactor.device.fan",
                        "brand_logo": "https://efactor.tech/files/brand_logo/efactor.jpg"
                    }
                ]
            },
            "node_id": "FFFFFFFFFFFFFFFF7EC2000000036B00",
            "role": "primary",
            "status": {
                "connectivity": {
                    "connected": false,
                    "timestamp": 1737626019747
                }
            }
        },
        {
            "id": "FFFFFFFFFFFFFFFF7EC2000000037C00",
            "config": {
                "devices": [
                    {
                        "device_name": "fan_7E00037C",
                        "power": true,
                        "ui_type": "Toggle",
                        "page_name": "Page1",
                        "ui_description": "Read/Write",
                        "local_control": "enable",
                        "primary": "brightness",
                        "type": "efactor.device.fan",
                        "brand_logo": "https://efactor.tech/files/brand_logo/efactor_light.jpg"
                    }
                ]
            },
            "node_id": "FFFFFFFFFFFFFFFF7EC2000000037C00",
            "role": "secondary",
            "status": {
                "connectivity": {
                    "connected": true,
                    "timestamp": 1737627023456
                }
            }
        },
        {
            "id": "FFFFFFFFFFFFFFFF7EC2000000036D00",
            "config": {
                "devices": [
                    {
                        "device_name": "fan_7E00036D",
                        "power": true,
                        "ui_type": "Toggle",
                        "page_name": "Page1",
                        "ui_description": "Read/Write",
                        "local_control": "enable",
                        "primary": "power",
                        "type": "efactor.device.fan",
                        "brand_logo": "https://efactor.tech/files/brand_logo/efactor.jpg"
                    }
                ]
            },
            "node_id": "FFFFFFFFFFFFFFFF7EC2000000036D00",
            "role": "primary",
            "status": {
                "connectivity": {
                    "connected": false,
                    "timestamp": 1737626019747
                }
            }
        },
        {
            "id": "FFFFFFFFFFFFFFFF7EC2000000037100",
            "config": {
                "devices": [
                    {
                        "device_name": "fan_7E000371",
                        "power": true,
                        "ui_type": "Toggle",
                        "page_name": "Page1",
                        "ui_description": "Read/Write",
                        "local_control": "enable",
                        "primary": "brightness",
                        "type": "efactor.device.fan",
                        "brand_logo": "https://efactor.tech/files/brand_logo/efactor_light.jpg"
                    }
                ]
            },
            "node_id": "FFFFFFFFFFFFFFFF7EC2000000037100",
            "role": "secondary",
            "status": {
                "connectivity": {
                    "connected": true,
                    "timestamp": 1737627023456
                }
            }
        },
        {
            "id": "FFFFFFFFFFFFFFFF7EC2000000037200",
            "config": {
                "devices": [
                    {
                        "device_name": "fan_7E000372",
                        "power": true,
                        "ui_type": "Toggle",
                        "page_name": "Page1",
                        "ui_description": "Read/Write",
                        "local_control": "enable",
                        "primary": "power",
                        "type": "efactor.device.fan",
                        "brand_logo": "https://efactor.tech/files/brand_logo/efactor.jpg"
                    }
                ]
            },
            "node_id": "FFFFFFFFFFFFFFFF7EC2000000037200",
            "role": "primary",
            "status": {
                "connectivity": {
                    "connected": false,
                    "timestamp": 1737626019747
                }
            }
        },
        {
            "id": "FFFFFFFFFFFFFFFF7EC2000000037508",
            "config": {
                "devices": [
                    {
                        "device_name": "fan_7E000375",
                        "power": true,
                        "ui_type": "Toggle",
                        "page_name": "Page1",
                        "ui_description": "Read/Write",
                        "local_control": "enable",
                        "primary": "brightness",
                        "type": "efactor.device.fan",
                        "brand_logo": "https://efactor.tech/files/brand_logo/efactor_light.jpg"
                    }
                ]
            },
            "node_id": "FFFFFFFFFFFFFFFF7EC2000000037508",
            "role": "secondary",
            "status": {
                "connectivity": {
                    "connected": true,
                    "timestamp": 1737627023456
                }
            }
        },
        {
            "id": "FFFFFFFFFFFFFFFF7EC2000000036A09",
            "config": {
                "devices": [
                    {
                        "device_name": "fan_7E00036A",
                        "power": true,
                        "ui_type": "Toggle",
                        "page_name": "Page1",
                        "ui_description": "Read/Write",
                        "local_control": "enable",
                        "primary": "power",
                        "type": "efactor.device.fan",
                        "brand_logo": "https://efactor.tech/files/brand_logo/efactor.jpg"
                    }
                ]
            },
            "node_id": "FFFFFFFFFFFFFFFF7EC2000000036A09",
            "role": "primary",
            "status": {
                "connectivity": {
                    "connected": false,
                    "timestamp": 1737626019747
                }
            }
        },
        {
            "id": "FFFFFFFFFFFFFFFF7EC2000000037510",
            "config": {
                "devices": [
                    {
                        "device_name": "fan_7E000375",
                        "power": true,
                        "ui_type": "Toggle",
                        "page_name": "Page1",
                        "ui_description": "Read/Write",
                        "local_control": "enable",
                        "primary": "brightness",
                        "type": "efactor.device.fan",
                        "brand_logo": "https://efactor.tech/files/brand_logo/efactor_light.jpg"
                    }
                ]
            },
            "node_id": "FFFFFFFFFFFFFFFF7EC2000000037510",
            "role": "secondary",
            "status": {
                "connectivity": {
                    "connected": true,
                    "timestamp": 1737627023456
                }
            }
        }
    ]
}';


echo $node;

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
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        

