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

if (isset($_GET['action']) && $_GET['action']=='test_details')
{

$startid =$_GET['start_id'];
                        
			$header = apache_request_headers();  
			foreach ($header as $key => $val) {




                         if(!empty($_GET["start_id"]))
                      {



                         $query_dev="SELECT node_id FROM efactorproduct where access_token='$val'";
			$doctors = $db->get_results($query_dev); 
			$num_dev = $db->num_rows($query_dev);
                        $row_dev = $db->get_row($query_dev);    
		        $node_id=$row_dev['node_id'];

                         
                        $arr=array();
			foreach ($doctors as $key => $doctor) 
			{
			  array_push($arr,array("id"=>$doctor['id'],"node_id"=>$doctor['node_id'],"device_type"=>$doctor['device_type']));
			}
  
			 $device_id=$arr[0]['node_id'];	  
			 $next_id_2=$arr[1]['node_id'];
		         $nextid = $next_id_2;

                        $query_node="SELECT * FROM efactorproduct where node_id='$node_id'";
			$doctors_node = $db->get_results($query_node); 
			$num_node = $db->num_rows($query_node);
                        $row_node = $db->get_row($query_node);    
		        $nodeid=$row_node['node_id'];
                        $user_id=$row_node['user_id'];
                        $model_name=$row_node['model_number'];

                        $moduletype= $row_node['module_type'];
		        $name=$row_node['name'];
                        $mac=$row_node['mac'];
		        $speed=$row_node['speed'];
			$hue=$row_node['Hue'];
		        $direction=$row_node['direction'];
		        //$power=$row_node['power'];
			$baudrate=$row_node['baudrate'];
			//$led=$row_node['led_indication'];
	                $power=filter_var($row_node['power'], FILTER_VALIDATE_BOOLEAN);
                        $led = filter_var($row_node['led_indication'], FILTER_VALIDATE_BOOLEAN);
                                           
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







                       }

                     else
{



                    $query_dev="SELECT node_id FROM efactorproduct where access_token='$val' AND is_added='1'";
			$doctors = $db->get_results($query_dev); 
			$num_dev = $db->num_rows($query_dev);
                        $row_dev = $db->get_row($query_dev);    
		        $node_id=$row_dev['node_id'];


                        
                        
                        
                        $query_node_id="SELECT node_id FROM `efactorproduct` WHERE access_token='$val' AND add_gateway='$node_id'";
			$doctors_node_id = $db->get_results($query_node_id); 
			$num_node_id = $db->num_rows($query_node_id);
                        $row_node_id = $db->get_row($query_node_id);    
		      //  $nodeid=$row_node_id['node_id'];




$arr=array();

   
//print_r($arr);

for ($pos = 0; $pos < $num_node_id; $pos++) {  
    $link_dev = $doctors_node_id[$pos]['node_id'];
    
/*-------------for link_dev start------------------------------------*/


$query_node = "SELECT * FROM `efactorproduct` WHERE access_token='$val' AND node_id='$link_dev'";
		    $num_node = $db->num_rows($query_node);
		    $row_node = $db->get_row($query_node); 



                        $user_id=$row_node['user_id'];
                        $model_name=$row_node['model_number'];
                        $moduletype= $row_node['module_type'];
		        $name=$row_node['name'];
                        $mac=$row_node['mac'];
		        $speed=$row_node['speed'];
			$hue=$row_node['Hue'];
		        $direction=$row_node['direction'];
		        //$power=$row_node['power'];
			$baudrate=$row_node['baudrate'];
			//$led=$row_node['led_indication'];
	                $power=filter_var($row_node['power'], FILTER_VALIDATE_BOOLEAN);
                        $led = filter_var($row_node['led_indication'], FILTER_VALIDATE_BOOLEAN);
                                           
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











//$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name' AND user_id = '$user_id'";
$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFACT V1.0' AND user_id = '177'";
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

$param1 = null; 

$param1=$param['parameter_name'];

echo $param1;

//$query_param = "SELECT * FROM product_api WHERE model_name = 'EFFN02' AND user_id = '177' AND parameter_name='$param1'";
  $query_param = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.0' AND user_id = '177' AND parameter_name='$param1'";
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
            $cmd_defaultvalue = explode(',', $row_param['cmd_defaultvalue']);
            $cmd_developernotes = explode(',', $row_param['cmd_developernotes']);
            $cmd_noofbytes = explode(',', $row_param['cmd_numberOfbytes']);
           // $cmd_asperui = explode(',', $row_param['cmd_asperui']);
           // $cmd_asperui_values = explode(',', $row_param['cmd_asperui_values']);

            

             
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
          //  $resp_asperui = explode(',', $row_param['resp_asperui']);
          //  $resp_asperui_values = explode(',', $row_param['resp_asperui_values']);

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
         //   $error_asperui = explode(',', $row_param['error_asperui']);
         //   $error_asperui_values = explode(',', $row_param['error_asperui_values']);



                        


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


        
 

if ($param1 == 'BAUDRATE') {
   $cmd_asperui_val = array('', '30', '');
   $cmd_asui_val = array('', '4800', '');
    $cmd_asperui = [[], ["4800", "9600", "57600", "38400", "115200", "230400"], [], []];
    $cmd_asperui_values = [[], ["30", "31", "32", "33", "34", "35"], [], []];
}
        $cmd_multipleui = $row_param['cmd_multipleui'];
        $resp_multipleui = $row_param['resp_multipleui'];
        $error_multipleui = $row_param['error_multipleui'];


        $cmd_triggerui = $row_param['cmd_triggerui'];
        $resp_triggerui = $row_param['resp_triggerui'];
        $error_triggerui = $row_param['error_triggerui'];

       



        $selected = $row_param['selected'];





if($param1 =='Speed')
{
 $param_value="4";
$param_val='speed';


$cmd_asperui_val = array('', '', '', '');
 $cmd_asui_val =  array('', '', '', '');
$cmd_asperui = [[], [], [], []];
    $cmd_asperui_values = [[], [], [], []];

}






else if($param1 =='BAUDRATE')
{
 $param_value="9600";
}


else if($param1 =='power')
{
 $param_value="on";
$param_val="power";

 $cmd_asperui_val = array('', '', '00', '');
$cmd_asui_val =  array('', '', 'false', '');
$cmd_asperui = [[], [], ["false", "true"], []];
    $cmd_asperui_values = [[], [] ,["00", "01"], []];;

}

else if($param1 =='Buzzer')
{
 $param_value="true";
 $cmd_asperui_val = array('', '', '00', '');
$cmd_asui_val =  array('', '', 'false', '');

$cmd_asperui = [[], [], ["false", "true"], []];
    $cmd_asperui_values = [[], [] ,["00", "01"], []];;


}

else if($param1 =='Direction')
{
 $param_value="1";
$cmd_asperui_val = array('', '', '08', '');
$cmd_asui_val =  array('', '', '0', '');

$cmd_asperui = [[], [], ["0", "1"], []];
    $cmd_asperui_values = [[], [] ,["08", "08"], []];

}





else if($param1 =='Uart msg ID')
{
 $param_value="00";
$cmd_asperui_val = array('', '00', '');
$cmd_asui_val =  array('', 'disable', '');

$cmd_asperui = [[], [ "disable", "enable"], []];
    $cmd_asperui_values = [[] ,["00", "01"], []];

}




else if($param1 =='Get status')
{
 $param_value="00";
$resp_asperui_val = array('','','00','','08','','','','','','','','','','','','00','');
$resp_asui_val =  array('','','false','','00','','','','','','','','','','','','false','');       
$resp_asperui = [[],[],["false","true"],[],["00","01"],[],[],[],[],[],[],[],[],[],[],[],["false","true"],[]];
$resp_asperui_values = [[],[],["00","01"],[],["08","08"],[],[],[],[],[],[],[],[],[],[],[],["00","01"],[]];

}


else if($param1 =='Command Get Status')
{
 $param_value="00";
$resp_asperui_val = array('','','00','','08','','','','','','','','','','','','00','');
$resp_asui_val =  array('','','false','','00','','','','','','','','','','','','false','');       
$resp_asperui = [[],[],["false","true"],[],["00","01"],[],[],[],[],[],[],[],[],[],[],[],["false","true"],[]];
$resp_asperui_values = [[],[],["00","01"],[],["08","08"],[],[],[],[],[],[],[],[],[],[],[],["00","01"],[]];

}



else if($param1 =='Tuning')
{
 $param_value="00";
$cmd_asperui_val = array('','','','','','','','','','','','','','','','','','','');
$cmd_asui_val =  array('','','','','','','','','','','','','','','','','','','');       
$cmd_asperui = [[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[]];
$cmd_asperui_values = [[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[]];

}





else if($param1 =='Error Code')
{
 $param_value="00";
$cmd_asperui_val = array('', '00', '');
$cmd_asui_val =  array('', 'disable', '');

$cmd_asperui = [[], [ "disable", "enable"], []];
    $cmd_asperui_values = [[], ["00", "01"], []];;

}


else {
    $cmd_asperui_val = '';
$cmd_asui_val =  '';       
$cmd_asperui = [];
$cmd_asperui_values = [];

}








/*--------------command------------------------------*/



 if($cmd_framesize < 1)
{

$fields_cmd=array();


}

else{



for ($i = 0; $i < $cmd_framesize; $i++) {

if($param1 == 'Tuning')
{
$cmd_default_name= array();
$cmd_default_name=array('','','resistance', 'lmin', 'lmax', 'frequency', 'pole pairs', 'open loop current', 'power limit', 'torque limit', 'speed1', 'speed2', 'speed3', 'speed4', 'speed5', 'speed6', 'speed7');




}
else{
$cmd_default_name="";
}


if($cmd_defaultvalue[$i] == '')
{


$cmd_defaultvalue[$i]= $cmd_fielddescription[$i];
}
else{
$cmd_defaultvalue[$i]= $cmd_defaultvalue[$i];
}


    if($cmd_fielddescription[$i] == 'Data')
{


       if($cmd_uitype[$i] == 'Text(Default)')

         {


              
                $field_cmd = [
            "default name" =>$cmd_defaultvalue[$i],
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

            $field_cmd = [
            "default name" =>$cmd_defaultvalue[$i],
            "as per ui value" => $cmd_asui_val[$i],
            "value" => $cmd_valueinhexmin[$i],
            "Field_description" => $cmd_fielddescription[$i],
            "type" => $cmd_valuedefinition[$i],
            "data_type" => $cmd_datatype[$i],
            "no_of_bytes" => (int)$cmd_noofbytes[$i],
            "byte_start_position" => (int)$cmd_startbytePosition[$i],
            "ui_type"  => $cmd_uitype[$i],
            "ui_ids"   => (int)$cmd_uiid[$i],
            "as per ui" => $cmd_asperui[$i],
            "asperui values" => $cmd_asperui_values[$i],
            "ui_description" => $cmd_uidescriptioninout[$i],
            "slider step value"=> $cmd_sliderstepvalue[$i]
            ];

            $field_cmd["value_in_hex"] = [
                "min" => $cmd_valueinhexmin[$i],
                "max" => $cmd_valueinhexmax[$i]
            ];


         if ($cmd_datatype[$i] == 'UnsignedInt') {
    $field_cmd["display_value"] = [
        "min" => (int)$cmd_displayvaluemin[$i],
        "max" => (int)$cmd_displayvaluemax[$i]
    ];
} elseif ($cmd_datatype[$i] == 'Float') {
    $field_cmd["display_value"] = [
        "min" => (float)$cmd_displayvaluemin[$i],
        "max" => (float)$cmd_displayvaluemax[$i]
    ];
} else {
    $field_cmd["display_value"] = [
        "min" => $cmd_displayvaluemin[$i],
        "max" => $cmd_displayvaluemax[$i]
    ];
}

       // $fields_cmd[] = $field_cmd;


}

        $fields_cmd[] = $field_cmd;

}

else
{

           $field_cmd = [
                         
            "default name" =>$cmd_defaultvalue[$i],
            "value" => $cmd_valueinhexmin[$i],
            "Field_description" => $cmd_fielddescription[$i],
            "no_of_bytes" => (int)$cmd_noofbytes[$i],
            "byte_start_position" => (int)$cmd_startbytePosition[$i],
            "ui_type"  => $cmd_uitype[$i],
           
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
        if ($param1 == 'get status') {
            $resp_default_name = array('', '', 'power on/off', 'speed', 'Direction', 'color', 'lamp', 'timer', 'dimming level', 'fan enable/disable', 'Remote lock/unlock', 'rpm', 'power', 'current', 'voltage', 'vacrms', 'buzzer');
        } else {
            $resp_default_name = "";
        }


        if ($param1 == 'Tuning') {
           $resp_asperui= [[],[],["successfully set tuning parameters", "failed to set tuning parameters"],[]];
           $resp_asui_val = array('', '', 'successfully set tuning parameters', '');
     
    $resp_asperui_values = [[], [], ["00", "01"], []];
        } else {
            $resp_asperui= "";
            $resp_asperui_values= "";
        }





        if ($resp_defaultvalue[$i] == '') {
            $resp_defaultvalue[$i] = $resp_fieldDescription[$i];
        } else {
            $resp_defaultvalue[$i] = $resp_defaultvalue[$i];
        }


  

        if ($resp_fieldDescription[$i] == 'Data') {
            if ($resp_uiType[$i] == 'Text(Default)') {
                $field_resp = [
                    "default name" => $resp_defaultvalue[$i],
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
                $field_resp = [
                    "default name" => $resp_defaultvalue[$i],
                    "as per ui value" => $resp_asui_val[$i],
                    "value" => $resp_valueInHexMin[$i],
                    "Field_description" => $resp_fieldDescription[$i],
                    "type" => $resp_valueDefinition[$i],
                    "data_type" => $resp_dataType[$i],
                    "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                    "byte_start_position" => (int)$resp_startBytePosition[$i],
                    "ui_type" => $resp_uiType[$i],
                    "ui_ids" => (int)$resp_uiId[$i],
                    "as per ui" => $resp_asperui[$i],
                    "asperui values" => $resp_asperui_values[$i],
                    "ui_description" => $resp_uiDescriptionInOut[$i],
                    "slider step value" => $resp_sliderStepValue[$i]
                ];

                $field_resp["value_in_hex"] = [
                    "min" => $resp_valueInHexMin[$i],
                    "max" => $resp_valueInHexMax[$i]
                ];

                if ($resp_dataType[$i] == 'UnsignedInt') {
                    $field_resp["display_value"] = [
                        "min" => (int)$resp_displayValueMin[$i],
                        "max" => (int)$resp_displayValueMax[$i]
                    ];
                } elseif ($resp_dataType[$i] == 'Float') {
                    $field_resp["display_value"] = [
                        "min" => (float)$resp_displayValueMin[$i],
                        "max" => (float)$resp_displayValueMax[$i]
                    ];
                } else {
                    $field_resp["display_value"] = [
                        "min" => $resp_displayValueMin[$i],
                        "max" => $resp_displayValueMax[$i]
                    ];
                }
            }
            $fields_resp[] = $field_resp;
        } else {
            $field_resp = [
                "default name" => $resp_defaultvalue[$i],
                "value" => $resp_valueInHexMin[$i],
                "Field_description" => $resp_fieldDescription[$i],
                "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                "byte_start_position" => (int)$resp_startBytePosition[$i],
                "ui_type" => $resp_uiType[$i],
            ];
            $fields_resp[] = $field_resp;
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







        // Construct $field_error
        $field_error = [
              "default name" =>$error_defaultvalue[$i],
               "as per ui value" => $error_asui_val[$i],
            "value" => $error_valueInHexMin[$i],
            "Field_description" => $error_fieldDescription[$i],
            "type" => $error_valueDefinition[$i],
            "data_type" => $error_dataType[$i],
            "no_of_bytes" => (int) $error_numberOfBytes[$i],
            "byte_start_position" => (int) $error_startBytePosition[$i],
            "ui_type"  => $error_uiType[$i],
            "ui_ids"   => (int) $error_uiId[$i],
             "as per ui" => $error_asperui[$i],
            "asperui values" => $error_asperui_values[$i],
            "ui_description" => $error_uiDescriptionInOut[$i],
            "slider step value"=> $error_sliderStepValue[$i]
        ];

        // Add display_value and value_in_hex for Slider type
       /* if ($error_uiType[$i] == 'Slider') {
           /* $field_error["display_value"] = [
                "min" => $error_displayValueMin[$i],
                "max" => $error_displayValueMax[$i]
            ];*/
            $field_error["value_in_hex"] = [
                "min" => $error_valueInHexMin[$i],
                "max" => $error_valueInHexMax[$i]
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












      /*  } else {
            $field_error["value_in_hex"] = [
                "value" => $error_valueInHexMin[$i]
            ];
        }*/

        $fields_error[] = $field_error;
    }


}



/*

$page0 = [];
$para_result = [];
$page2 = [];
$page3 = [];

*/










echo $param1;











   // if (($param['parameter_name'] == "BAUDRATE") || ($param['parameter_name'] =="Uart msg ID" ) ||($param['parameter_name'] =="Error Code")){

if ($number_of_pages == 0) {

  echo 'hi_0';

        $result_single0 = [
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
    } elseif ($number_of_pages == 2) {
echo 'hi_2';

        $result_single2 = [
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
    } elseif ($number_of_pages == 3) {
echo 'hi_3';

        $result_single3 = [
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
    } elseif ($number_of_pages == 1 && $param['parameter_name'] !== "BAUDRATE") {

echo 'hi_1';


       if(($param1 == "power") || ($param1 == "name")){


       $para = [
            "frame" => [
                "name" => $param['parameter_name'],
                "multiple_ui" => [$cmd_multipleui, $resp_multipleui, $error_multipleui],
                "trigger_ui" => [$cmd_triggerui, $resp_triggerui, $error_triggerui],
                "commands" => $fields_cmd,
                "response" => $fields_resp,
                "error" => $fields_error
            ]
        ];
        $result[] = $para;




        }
else{
        $para = [
            "frame" => [
                "name" => $param['parameter_name'],
                "multiple_ui" => [$cmd_multipleui, $resp_multipleui, $error_multipleui],
                "trigger_ui" => [$cmd_triggerui, $resp_triggerui, $error_triggerui],
                "commands" => $fields_cmd,
                "response" => $fields_resp,
                "error" => $fields_error
            ]
        ];
        $result[] = $para;

}

    }



 /*if(($param1 == "power") || ($param1 == "name")){


       $para = [
            "frame" => [
                "name" => $param['parameter_name'],
                "multiple_ui" => [$cmd_multipleui, $resp_multipleui, $error_multipleui],
                "trigger_ui" => [$cmd_triggerui, $resp_triggerui, $error_triggerui],
                "commands" => $fields_cmd,
                "response" => $fields_resp,
                "error" => $fields_error
            ]
        ];
        $result[] = $para;




        }

*/







$para_result = $result;

$devices = array(
    "name" => $curr_devicetype,
    "total_pages" => $number_of_pages,
    "attributes" => array(
        array(
            "name" => "Device Id",
            "value" => $link_dev
        ),
        array(
            "name" => "mac",
            "value" => "CC:50:E3:16:1D:6F"
        )
    ),
    "pages" => array(
        "page0" => $page0,
        "page1" => $para_result,
        "page2" => $page2,
        "page3" => $page3
    ),
    "primary" => "power",
    "type" => "efactor.device.fan",
    "brand_logo" => "https://efactor.tech/files/brand_logo/efactor.jpg"
);




/*

$para_result = $result;

$devices = array(
    "name" => $curr_devicetype,
    "total_pages" => $number_of_pages,
    "attributes" => array(
        array(
            "name" => "Device Id",
            "value" => $link_dev
        ),
        array(
            "name" => "mac",
            "value" => "CC:50:E3:16:1D:6F"
        )
    ),
    "pages" => array(
"page1" => $para_result
            ),
    "primary" => "power",
    "type" => "efactor.device.fan",
    "brand_logo" => "https://efactor.tech/files/brand_logo/efactor.jpg"
);


 */  



     $devices_list[]=$devices;










}

















 
 







/*-------------for link_dev start------------------------------------*/


}


/*------------------else start_id end-------------------*/
}



/**********************************************************************************/







			
			

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
        "devices" =>  $devices_list,
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
		        $node_id=$row_dev['node_id'];

                        $query_node="SELECT * FROM efactorproduct where node_id='$node_id'";
			$doctors_node = $db->get_results($query_node); 
			$num_node = $db->num_rows($query_node);
                        $row_node = $db->get_row($query_node);    
		        $nodeid=$row_node['node_id'];
                        $user_id=$row_node['user_id'];
                        $model_name=$row_node['model_number'];

                        $moduletype= $row_node['module_type'];
		        $name=$row_node['name'];
                        $mac=$row_node['mac'];
		        $speed=$row_node['speed'];
			$hue=$row_node['Hue'];
		        $direction=$row_node['direction'];
		        //$power=$row_node['power'];
			$baudrate=$row_node['baudrate'];
			//$led=$row_node['led_indication'];
	                $power=filter_var($row_node['power'], FILTER_VALIDATE_BOOLEAN);
                        $led = filter_var($row_node['led_indication'], FILTER_VALIDATE_BOOLEAN);
                                           
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



//$node_id = 'FFFFFFFFFFFFFFFF7EC2000000004200';



$query = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.0' AND user_id = '177'";
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

        



 
$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFACT V1.0' AND user_id = '177'";
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
  $query_param = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.0' AND user_id = '177' AND parameter_name='$param1'";
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
            $cmd_defaultvalue = explode(',', $row_param['cmd_defaultvalue']);
            $cmd_developernotes = explode(',', $row_param['cmd_developernotes']);
            $cmd_noofbytes = explode(',', $row_param['cmd_numberOfbytes']);
           // $cmd_asperui = explode(',', $row_param['cmd_asperui']);
           // $cmd_asperui_values = explode(',', $row_param['cmd_asperui_values']);

            

             
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
          //  $resp_asperui = explode(',', $row_param['resp_asperui']);
          //  $resp_asperui_values = explode(',', $row_param['resp_asperui_values']);

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
         //   $error_asperui = explode(',', $row_param['error_asperui']);
         //   $error_asperui_values = explode(',', $row_param['error_asperui_values']);



                        


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


        
 

if ($param1 == 'BAUDRATE') {
   $cmd_asperui_val = array('', '30', '');
   $cmd_asui_val = array('', '4800', '');
    $cmd_asperui = [[], ["4800", "9600", "57600", "38400", "115200", "230400"], [], []];
    $cmd_asperui_values = [[], ["30", "31", "32", "33", "34", "35"], [], []];
}
        $cmd_multipleui = $row_param['cmd_multipleui'];
        $resp_multipleui = $row_param['resp_multipleui'];
        $error_multipleui = $row_param['error_multipleui'];


        $cmd_triggerui = $row_param['cmd_triggerui'];
        $resp_triggerui = $row_param['resp_triggerui'];
        $error_triggerui = $row_param['error_triggerui'];

       



        $selected = $row_param['selected'];





if($param1 =='Speed')
{
 $param_value="4";
$param_val='speed';


$cmd_asperui_val = array('', '', '', '');
 $cmd_asui_val =  array('', '', '', '');
$cmd_asperui = [[], [], [], []];
    $cmd_asperui_values = [[], [], [], []];

}






else if($param1 =='BAUDRATE')
{
 $param_value="9600";
}


else if($param1 =='power')
{
 $param_value="on";
$param_val="power";

 $cmd_asperui_val = array('', '', '00', '');
$cmd_asui_val =  array('', '', 'false', '');
$cmd_asperui = [[], [], ["false", "true"], []];
    $cmd_asperui_values = [[], [] ,["00", "01"], []];;

}

else if($param1 =='Buzzer')
{
 $param_value="true";
 $cmd_asperui_val = array('', '', '00', '');
$cmd_asui_val =  array('', '', 'false', '');

$cmd_asperui = [[], [], ["false", "true"], []];
    $cmd_asperui_values = [[], [] ,["00", "01"], []];;


}

else if($param1 =='Direction')
{
 $param_value="1";
$cmd_asperui_val = array('', '', '08', '');
$cmd_asui_val =  array('', '', '0', '');

$cmd_asperui = [[], [], ["0", "1"], []];
    $cmd_asperui_values = [[], [] ,["08", "08"], []];

}





else if($param1 =='Uart msg ID')
{
 $param_value="00";
$cmd_asperui_val = array('', '00', '');
$cmd_asui_val =  array('', 'disable', '');

$cmd_asperui = [[], [ "disable", "enable"], []];
    $cmd_asperui_values = [[] ,["00", "01"], []];

}




else if($param1 =='Get status')
{
 $param_value="00";
$resp_asperui_val = array('','','00','','08','','','','','','','','','','','','00','');
$resp_asui_val =  array('','','false','','00','','','','','','','','','','','','false','');       
$resp_asperui = [[],[],["false","true"],[],["00","01"],[],[],[],[],[],[],[],[],[],[],[],["false","true"],[]];
$resp_asperui_values = [[],[],["00","01"],[],["08","08"],[],[],[],[],[],[],[],[],[],[],[],["00","01"],[]];

}


else if($param1 =='Command Get Status')
{
 $param_value="00";
$resp_asperui_val = array('','','00','','08','','','','','','','','','','','','00','');
$resp_asui_val =  array('','','false','','00','','','','','','','','','','','','false','');       
$resp_asperui = [[],[],["false","true"],[],["00","01"],[],[],[],[],[],[],[],[],[],[],[],["false","true"],[]];
$resp_asperui_values = [[],[],["00","01"],[],["08","08"],[],[],[],[],[],[],[],[],[],[],[],["00","01"],[]];

}



else if($param1 =='Tuning')
{
 $param_value="00";
$cmd_asperui_val = array('','','','','','','','','','','','','','','','','','','');
$cmd_asui_val =  array('','','','','','','','','','','','','','','','','','','');       
$cmd_asperui = [[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[]];
$cmd_asperui_values = [[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[],[]];

}





else if($param1 =='Error Code')
{
 $param_value="00";
$cmd_asperui_val = array('', '00', '');
$cmd_asui_val =  array('', 'disable', '');

$cmd_asperui = [[], [ "disable", "enable"], []];
    $cmd_asperui_values = [[], ["00", "01"], []];;

}


else {
    $cmd_asperui_val = '';
$cmd_asui_val =  '';       
$cmd_asperui = [];
$cmd_asperui_values = [];

}





if ($param1 != 'PUSH') {



/*--------------command------------------------------*/



 if($cmd_framesize < 1)
{

$fields_cmd=array();


}

else{



for ($i = 0; $i < $cmd_framesize; $i++) {

if($param1 == 'tuning')
{
$cmd_default_name= array();
$cmd_default_name=array('','','resistance', 'lmin', 'lmax', 'frequency', 'pole pairs', 'open loop current', 'power limit', 'torque limit', 'speed1', 'speed2', 'speed3', 'speed4', 'speed5', 'speed6', 'speed7');
}
else{
$cmd_default_name="";
}


if($cmd_defaultvalue[$i] == '')
{


$cmd_defaultvalue[$i]= $cmd_fielddescription[$i];
}
else{
$cmd_defaultvalue[$i]= $cmd_defaultvalue[$i];
}


    if($cmd_fielddescription[$i] == 'Data')
{


       if($cmd_uitype[$i] == 'Text(Default)')

         {


              
                $field_cmd = [
            "default name" =>$cmd_defaultvalue[$i],
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


          }


else{

            $field_cmd = [
            "default name" =>$cmd_defaultvalue[$i],
            "as per ui value" => $cmd_asui_val[$i],
            "value" => $cmd_valueinhexmin[$i],
            "Field_description" => $cmd_fielddescription[$i],
            "type" => $cmd_valuedefinition[$i],
            "data_type" => $cmd_datatype[$i],
            "no_of_bytes" => (int)$cmd_noofbytes[$i],
            "byte_start_position" => (int)$cmd_startbytePosition[$i],
            "ui_type"  => $cmd_uitype[$i],
            "ui_ids"   => (int)$cmd_uiid[$i],
            "as per ui" => $cmd_asperui[$i],
            "asperui values" => $cmd_asperui_values[$i],
            "ui_description" => $cmd_uidescriptioninout[$i],
            "slider step value"=> $cmd_sliderstepvalue[$i]
            ];

            $field_cmd["value_in_hex"] = [
                "min" => $cmd_valueinhexmin[$i],
                "max" => $cmd_valueinhexmax[$i]
            ];


         if ($cmd_datatype[$i] == 'UnsignedInt') {
    $field_cmd["display_value"] = [
        "min" => (int)$cmd_displayvaluemin[$i],
        "max" => (int)$cmd_displayvaluemax[$i]
    ];
} elseif ($cmd_datatype[$i] == 'Float') {
    $field_cmd["display_value"] = [
        "min" => (float)$cmd_displayvaluemin[$i],
        "max" => (float)$cmd_displayvaluemax[$i]
    ];
} else {
    $field_cmd["display_value"] = [
        "min" => $cmd_displayvaluemin[$i],
        "max" => $cmd_displayvaluemax[$i]
    ];
}

}

        $fields_cmd[] = $field_cmd;

}

else
{

           $field_cmd = [
                         
            "default name" =>$cmd_defaultvalue[$i],
            "value" => $cmd_valueinhexmin[$i],
            "Field_description" => $cmd_fielddescription[$i],
            "no_of_bytes" => (int)$cmd_noofbytes[$i],
            "byte_start_position" => (int)$cmd_startbytePosition[$i],
            "ui_type"  => $cmd_uitype[$i],
           
                  ];

$fields_cmd[] = $field_cmd;


}





    }




}







/***************************************************************/




 
 if ($resp_framesize <= 1) {
    $fields_resp = array();
} else {
    // Loop through $resp_framesize and build $fields_resp
    for ($i = 0; $i < $resp_framesize; $i++) {
        if ($param1 == 'get status') {
            $resp_default_name = array('', '', 'power on/off', 'speed', 'Direction', 'color', 'lamp', 'timer', 'dimming level', 'fan enable/disable', 'Remote lock/unlock', 'rpm', 'power', 'current', 'voltage', 'vacrms', 'buzzer');
        } else {
            $resp_default_name = "";
        }

        if ($resp_defaultvalue[$i] == '') {
            $resp_defaultvalue[$i] = $resp_fieldDescription[$i];
        } else {
            $resp_defaultvalue[$i] = $resp_defaultvalue[$i];
        }

        if ($resp_fieldDescription[$i] == 'Data') {
            if ($resp_uiType[$i] == 'Text(Default)') {
                $field_resp = [
                    "default name" => $resp_defaultvalue[$i],
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
                $field_resp = [
                    "default name" => $resp_defaultvalue[$i],
                    "as per ui value" => $resp_asui_val[$i],
                    "value" => $resp_valueInHexMin[$i],
                    "Field_description" => $resp_fieldDescription[$i],
                    "type" => $resp_valueDefinition[$i],
                    "data_type" => $resp_dataType[$i],
                    "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                    "byte_start_position" => (int)$resp_startBytePosition[$i],
                    "ui_type" => $resp_uiType[$i],
                    "ui_ids" => (int)$resp_uiId[$i],
                    "as per ui" => $resp_asperui[$i],
                    "asperui values" => $resp_asperui_values[$i],
                    "ui_description" => $resp_uiDescriptionInOut[$i],
                    "slider step value" => $resp_sliderStepValue[$i]
                ];

                $field_resp["value_in_hex"] = [
                    "min" => $resp_valueInHexMin[$i],
                    "max" => $resp_valueInHexMax[$i]
                ];

                if ($resp_dataType[$i] == 'UnsignedInt') {
                    $field_resp["display_value"] = [
                        "min" => (int)$resp_displayValueMin[$i],
                        "max" => (int)$resp_displayValueMax[$i]
                    ];
                } elseif ($resp_dataType[$i] == 'Float') {
                    $field_resp["display_value"] = [
                        "min" => (float)$resp_displayValueMin[$i],
                        "max" => (float)$resp_displayValueMax[$i]
                    ];
                } else {
                    $field_resp["display_value"] = [
                        "min" => $resp_displayValueMin[$i],
                        "max" => $resp_displayValueMax[$i]
                    ];
                }
            }
            $fields_resp[] = $field_resp;
        } else {
            $field_cmd = [
                "default name" => $resp_defaultvalue[$i],
                "value" => $resp_valueInHexMin[$i],
                "Field_description" => $resp_fieldDescription[$i],
                "no_of_bytes" => (int)$resp_numberOfBytes[$i],
                "byte_start_position" => (int)$resp_startBytePosition[$i],
                "ui_type" => $resp_uiType[$i],
            ];
            $fields_cmd[] = $field_cmd;
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







        // Construct $field_error
        $field_error = [
              "default name" =>$error_defaultvalue[$i],
               "as per ui value" => $error_asui_val[$i],
            "value" => $error_valueInHexMin[$i],
            "Field_description" => $error_fieldDescription[$i],
            "type" => $error_valueDefinition[$i],
            "data_type" => $error_dataType[$i],
            "no_of_bytes" => (int) $error_numberOfBytes[$i],
            "byte_start_position" => (int) $error_startBytePosition[$i],
            "ui_type"  => $error_uiType[$i],
            "ui_ids"   => (int) $error_uiId[$i],
             "as per ui" => $error_asperui[$i],
            "asperui values" => $error_asperui_values[$i],
            "ui_description" => $error_uiDescriptionInOut[$i],
            "slider step value"=> $error_sliderStepValue[$i]
        ];

        // Add display_value and value_in_hex for Slider type
       /* if ($error_uiType[$i] == 'Slider') {
           /* $field_error["display_value"] = [
                "min" => $error_displayValueMin[$i],
                "max" => $error_displayValueMax[$i]
            ];*/
            $field_error["value_in_hex"] = [
                "min" => $error_valueInHexMin[$i],
                "max" => $error_valueInHexMax[$i]
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












      /*  } else {
            $field_error["value_in_hex"] = [
                "value" => $error_valueInHexMin[$i]
            ];
        }*/

        $fields_error[] = $field_error;
    }


}


 
    if (($param['parameter_name'] == "BAUDRATE") || ($param['parameter_name'] =="Uart msg ID" ) ||($param['parameter_name'] =="Error Code")){
     // if ($param['parameter_name'] == 0) {
        $result_single0 = [
           
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
        
    } 
     elseif ($number_of_pages == 2) {
        $result_single2 = [
             
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
    }

   elseif ($number_of_pages == 3) {
        $result_single3 = [
             
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
    }

   elseif ($number_of_pages == 1 && $param['parameter_name'] !== "BAUDRATE") {
    $name = $param['parameter_name'];
    
        $para = [
            
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
                    "page0" => $page0,
                    "page1" => $para_result,
                    "page2" => $page2,
                    "page3" => $page3
                    
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
