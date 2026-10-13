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

/****************************************************************/


/************************************************************/
if (isset($_GET['action']) && $_GET['action']=='node_details')
{




			$startid =$_GET['start_id'];
                        
			$header = apache_request_headers();  
			foreach ($header as $key => $val) {
			/*$query = "SELECT * FROM `users` where access_token='$val'";
			//$doctors = $db->get_results($query); 
			$num = $db->num_rows($query);
                        $row = $db->get_row($query);
                        $phone= $row['phone'];*/

			$query_dev="SELECT node_id FROM efactorproduct where access_token='$val'";
			$doctors = $db->get_results($query_dev); 
			$num_dev = $db->num_rows($query_dev);

                       
                        
                        
                        //$timestamp=strtotime("now");
                        //$timestamp=round(microtime(true) * 1000);
			
                          // $db->query("update efactorproduct set app_version='v3' where access_token='$val'");
	 if($num_dev>0)
	   {
          
			
           		            /*    $query_1 = "SELECT * FROM efactorproduct where access_token='$val' and add_gateway='NULL'";
					$doctors = $db->get_results($query_1); 
					$row = $db->get_row($query_1); 
					$arr=array();
					//$id=$device_id;
                                        $num_start = $db->num_rows($query_1);*/

                    if(!empty($_GET["start_id"]))
                      {
		  
                        







                     //  echo 'empty($_GET["start_id"]';

                               $query_share = "SELECT * FROM efactorproduct where node_id='$startid' AND is_added='1'";
				        //$share = $db->get_results($query_share);
                                        $num_share = $db->num_rows($query_share); 
				       					

                             
                                       if($num_share>0)

                                      {     
                                          
                                // echo '$num_share>0'; 
                                            
                                
                                         $device_id=$startid;   
                                          
					/*$query_4 = "SELECT * FROM `efactorproduct` WHERE access_token='$val' AND is_added='1'";
					$doctors = $db->get_results($query_4); 
					$num_4 = $db->num_rows($query_4);
					$row_4 = $db->get_row($query_4); 
					$nodeid=$row_4['node_id'];
                                        //$num_gate = $db->num_rows($query_4);
                                       
                                       
                                        
                                        
                                        $node_id=$startid; 
                                        $address=$startid;
                                        $device_gateway=$row_4['device_type'];
                                        $moduletype= $row_4['module_type']; 
                                        $model_number=$row_4['model_number'];  
                                        $vendor_name=$row_4['vendor_name'];  
                                        $devicename=$row_4['device_name'];*/
                                                                          
					$arr=array();
				  
					$query_5 = "SELECT node_id FROM `efactorproduct` WHERE access_token='$val' AND add_gateway='$node_id'";
					//$doctors = $db->get_results($query_5); 
					$num_5 = $db->num_rows($query_5);
					$row_5 = $db->get_row($query_5); 
                                        //$device_type_5=$row_6['device_type'];

              


                                        /**********************************/
                                        /**********************************/


						foreach ($doctors as $key => $doctor) 
						 {
						   array_push($arr,array("id"=>$doctor['id'],"node_id"=>$doctor['node_id'],"device_type"=>$doctor['device_type'],"name"=>$doctor['name']));
							
 						 }

					    for ($pos = 0; $pos <= $num_5; $pos++) 
						  {   
					
							

							   $link_dev =$arr[$pos]['node_id'];
                                                          //echo $link_dev;  
                                                                  
							   $query_6 = "SELECT * FROM `efactorproduct` WHERE access_token='$val' AND node_id='$link_dev'";
							  // $doctors = $db->get_results($query_6); 
							   $num_6 = $db->num_rows($query_6);
							   $row_6 = $db->get_row($query_6); 
							   $device_type_5=$row_6['device_type'];
                                                           $devicetype=$row_6['device_type'];
                                                           $addstatus_5=$row_6['req_status'];
                                                           $linkcommmode=$row_6['link_comm_mode'];
					                   $linkdevices=$row_6['link_devices'];
							   // $link_name=$row_6['name']; 
   
                                                           $model_number=$row_6['model_number'];
                                                           $devicename=$row_6['device_name'];
                                                           $vendor_name=$row_6['vendor_name'];
                                                           $moduletype=$row_6['module_type'];
                                                           $net_conn_1=$row_6['internet'];
                                                           $access_token=$row_6['access_token'];



                                                          if($device_type_5 == 'exhaust_hood')
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



                                           
                                           
                                      
                                      /**************************/
                                                    //$moduletype= $row_6['module_type'];
					             $name=$row_6['name'];
                                                    $mac=$row_6['mac'];
					             $speed=$row_6['speed'];
					             $hue=$row_6['Hue'];
					             $direction=$row_6['direction'];
					             //$power=$row_6['power'];
					             $baudrate=$row_6['baudrate'];
					             //$led=$row_6['led_indication'];
					             $power=filter_var($row_6['power'], FILTER_VALIDATE_BOOLEAN);
					             $led = filter_var($row_6['led_indication'], FILTER_VALIDATE_BOOLEAN);
                                           
                                               $devicename=$row['device_name']; 
                                               $curr_devicetype=$row_6['current_devicetype'];
                                             $recipe=$row_6['recipe'];
                                              $user_priority=$row_6['user_priority'];
                                             $cookermode=$row_6['cookermode'];
                                             $cooker_led=$row_6['cooker_led'];
                                             $cooktime=$row_6['cook_time'];
                                             $cook_run_temp=$row_6['cook_run_temp'];
                                             $current=$row_6['current'];
                                             $voltage=$row_6['ac_float'];
                                             $bus_voltage=$row_6['bus_voltage'];
                                              $rpm=$row_6['rpm'];
                                              $timer=$row['timer'];
                                             $watt=$row_6['power_watt'];
                                             $rem_time=$row_6['rem_time'];
                                              $hood_autoclean=$row_6['hood_autoclean'];
                                              $hood_speed=$row_6['hood_speed'];
                                              $hood_lamp=$row_6['hood_lamp'];

                                           /*   $nurse=$row_6['nurse'];
                                              $personal_assistant=$row_6['personal_assistant'];
                                              $doctormanage=$row_6['doctor'];
                                              $nurse_resp=$row_6['nurse_resp'];
                                              $personal_assistant_resp=$row_6['personal_assistant_resp'];
                                              $doctormanage_resp=$row_6['doctor_resp'];*/

                                           $nurse=filter_var($row_6['nurse'], FILTER_VALIDATE_BOOLEAN);
$personal_assistant=filter_var($row_6['personal_assistant'], FILTER_VALIDATE_BOOLEAN);
$doctormanage=filter_var($row_6['doctor'], FILTER_VALIDATE_BOOLEAN);
$nurse_resp=$row_6['nurse_resp'];      
$personal_assistant_resp=$row_6['personal_assistant_resp'];                    
$doctormanage_resp=$row_6['doctor_resp'];                      




                                      if($device_type_5 == 'swpcp')
                                              {
                                           //swpcp
                                            $starter_relay_off_timer = $row_6['starter_relay_off_timer'];  
                                            $start_high_current_cutoff = $row_6['start_high_current_cutoff'];  
                                            $start_low_current_cutoff= $row_6['start_low_current_cutoff'];  
                                            $Run_high_current_cutoff = $row_6['Run_high_current_cutoff'];  
                                            $Run_low_current_cutoff = $row_6['Run_low_current_cutoff'];  
                                            $high_voltage_cutoff = $row_6['high_voltage_cutoff'];  
                                            $low_voltage_cutoff = $row_6['low_voltage_cutoff'];  
                                            $ultra_high_voltage_cutoff = $row_6['ultra_high_voltage_cutoff'];  
                                            $ultra_high_current_cutoff = $row_6['ultra_high_current_cutoff'];  
                                            $USER_SET_MAX_CURRENT_CUTOFF_LIMIT = $row_6['USER_SET_MAX_CURRENT_CUTOFF_LIMIT'];  
                                            $USER_AND_DEVICE_MAX_CURRENT_LIMIT = $row_6['USER_AND_DEVICE_MAX_CURRENT_LIMIT'];  
                                            $LOCK_ENABLE_DISABLE = (bool)$row_6['LOCK_ENABLE_DISABLE'];  
                                         }



if($device_type_5 == 'cooker')
                                              {
                                           //cooker

                                            $cooker_timer = $row_6['cooker_timer_1'];  
                                           $cooking_temperature = $row_6['cooker_max_temperature_1'];  
                                            $Keep_warm= $row_6['Keep_warm_1'];  
                                            $Pre_timer_constant_mode = $row_6['Pre_timer_constant_mode'];  
                                            $Pre_timer = $row_6['Pre_timer'];  
                                            $Pre_temperature = $row_6['Pre_temperature'];  
                                            $Coooker_max_temperature = $row_6['Coooker_max_temperature'];  
                                            $Cooker_constant_mode = $row_6['Cooker_constant_mode'];  
                                            $Keep_Warm_timer = $row_6['Keep_Warm_timer_1'];  
                                            $Dishes = $row_6['dish'];
                                            $pre_timer_wait_temperature_constant_mode = $row_6['pre_timer_wait_temperature_constant_mode'];  
                                            $pre_timer_waiting_time = $row_6['pre_timer_waiting_time'];
                                            $pre_timer_Waiting_temperature = $row_6['pre_timer_Waiting_temperature'];  
                                            $warm_constant_cut_off = $row_6['warm_constant_cut_off'];  
                                            $cook_wait_temp_constant_mode = $row_6['cook_wait_temp_constant_mode'];  
                                            $cook_waiting_time = $row_6['cook_waiting_time'];  
                                            $cook_Waiting_temperature = $row_6['cook_Waiting_temperature'];  
                                            $warm_wait_temp_constant_mode = $row_6['warm_wait_temp_constant_mode'];  
                                            $warm_waiting_time = $row_6['warm_waiting_time'];  
                                            $warm_Waiting_temperature = $row_6['warm_Waiting_temperature']; 
                                            $cooker_ok_button = $row_6['cooker_ok_button']; 
                                            $reload_default = $row_6['reload_default']; 
                                             


                                         }


                                     // echo 'echo 1';
                                         
                                          

                                             if($device_type_5 == 'fan')
                                              {
                                             //fan
                                            $fan_resistance = $row_6['fan_resistance'];  
                                            $fan_lmin = $row_6['fan_lmin'];  
                                            $fan_lmax= $row_6['fan_lmax'];  
                                            $fan_frequency = $row_6['fan_frequency'];  
                                            $fan_pairs = $row_6['fan_pairs'];  
                                            $fan_open_loop_current = $row_6['fan_open_loop_current'];  
                                            $fan_power_limit = $row_6['fan_power_limit'];  
                                            $fan_torque_limit = $row_6['fan_torque_limit'];  
                                            $fan_speed1 = $row_6['fan_speed1'];  
                                            $fan_speed2= $row_6['fan_speed2'];
                                            $fan_speed3 = $row_6['fan_speed3'];  
                                            $fan_speed4= $row_6['fan_speed4'];
                                            $fan_speed5 = $row_6['fan_speed5'];  
                                            $fan_speed6= $row_6['fan_speed6'];
                                            $fan_speed7 = $row_6['fan_speed7'];  
                                            $fan_button= $row_6['fan_button'];
                                             } 
                                       
                                             
                                            
                                            
                                            
                                       $dev_paras=array($name=>$device_type_5);

                                       $tz_properties=$row_6['TZ_properties'];
                                       $br_properties=$row_6['BR_properties'];
                                       $umi_properties=$row_6['UMI_properties'];
                                       $ec_properties=$row_6['EC_properties'];
                                       $cm_properties=$row_6['CM_properties'];
                                       $tu_properties=$row_6['TU_properties'];
                                       $remote_pair_properties=$row_6['remote_pair_properties'];
                                       $display_properties=$row_6['display_properties'];
                                       $swpcp_1ph_properties=$row_6['swpcp_1ph_properties'];
                                       $fan_properties=$row_6['fan_properties'];
                                       $lc_properties=$row_6['LC_properties'];
                                       $cooker_properties=$row_6['cooker_properties'];
                                       



                                     $baudrate=$row_6['baudrate'];
                                     $uartmsgid=$row_6['uartmsgid'];
                                     $time=$row_6['time'];
                                     $errorcode=$row_6['errorcode'];
                                     $comm_mode=$row_6['comm_mode'];



                                     /*$model_number=$row_6['model_number'];
                                     $vendor_name=$row_6['vendor_name'];
                                     $moduletype= $row_6['module_type']; 
                                     $model_number=$row_6['model_number'];  
                                     $vendor_name=$row_6['vendor_name'];  
                                     $devicename=$row_6['device_name']; */                                       

                                           
                                              $chunks= str_split($linkdevices, 32);


                                         for ($i = 0; $i < count($chunks); $i++) {
                                                  ${"chunk" . ($i+1)} = $chunks[$i];
                                               $size = count($chunks);
                                              
                                               $query_7 = "SELECT * FROM `efactorproduct` WHERE access_token='$val' AND node_id='$chunks[$i]'";
							   $doctors = $db->get_results($query_7); 
							   $num_7 = $db->num_rows($query_7);
							   $row_7 = $db->get_row($query_7);

                                              $linkname[$i]=$row_7['name'];

                                             
                                              //  echo $linkname;
                          
                                                  }

                                                 
                          
                                      
								
                                      /*************************/

								if($device_type_5 == 'Gateway' && $addstatus_5== 'confirmed')
								 {
                                          
                                   
                                                $gate_attr_1 = json_encode(["name"=>"Device Id",
														  "value"=>$device_id]);

								$gate_attr_2 = json_encode(["name"=>"mac",
														  "value"=>$mac]);
								$gate_attri[] = json_decode($gate_attr_1, true);
								$gate_attri[] = json_decode($gate_attr_2, true);

								$gate_att=json_encode($fan_attri);



                                /*********************************/






									$G_Params = json_encode(["name" => "name",
												 "type"=> "efactor.param.name",
											    "data_type"=>"String",
											  "properties"=> ["read","write"]]);

                                           $G_Params_2=json_encode(["name"=> "power",
                                                             "data_type"=> "bool",
                                                             "properties"=>["read","write"],
                                                            "type"=>"efactor.param.power",
                                                             "ui_type"=>"efactor.ui.toggle"]);


									$G_p_1[] = json_decode($G_Params, true);
                                                                       $G_p_1[] = json_decode($G_Params_2, true);

									$Gate_Dev= json_encode([  "name" => $curr_devicetype,
                                                                       "attributes"=>$gate_attri,
															   "type"=> "efactor.device.gateway",
															 "params"=> $G_p_1]);

								    $devices_list[] = json_decode($Gate_Dev, true);


                                                                     $device_para_gateway_1 = array("power"=> $power,
                                                                        "output" => true,
                                                                         "name"=>$name);

                                           $device_para_gateway_3 = array($curr_devicetype => $device_para_gateway_1);
                                                $device_para[$pos]=$device_para_gateway_3;




								}


                                if($device_type_5 == 'fan' && $addstatus_5== 'confirmed')


                                 {


                                  
      
                                        

                                           unset($fan_attri);
                                           unset($fan_p);         
                                           unset($device_para_fan_1);
                                          

								
                                                  

$fan_attri = array();


$fan_attri[] = array(
    "name" => "Device Id",
    "value" => $link_dev
);


$fan_attri[] = array(
    "name" => "mac",
    "value" => $mac
);


foreach ($linkname as $linkDevice) {
    $fan_attri[] = array(
        "name" => $linkcommmode,
        "value" => $linkDevice
    );
}


//$json = json_encode(array("attributes" => $fan_attri), JSON_PRETTY_PRINT);
//echo $json;


 $fan_att=json_encode($fan_attri);

                                           
                                                                        

                                                                    

  
								  
                                       
                                           
								   $fan_P_1 = json_encode(["name" => "name",
														  "data_type"=> "String",
														 "properties"=>["read","write"],
															   "type"=> "efactor.param.name"]);


								  $fan_P_2 = json_encode(["name" => "power",
													  "data_type"=> "bool",
													 "properties"=> ["read","write"],
														   "type"=>"efactor.param.power",
                                                                    "type"=>"efactor.param.power",
														"ui_type"=>"efactor.ui.toggle"]);

								  


                                                                  if($model_number == "EFACT V1.2")
                                                                      {

								  $fan_p[]= json_decode($fan_P_1, true);
								  $fan_p[]= json_decode($fan_P_2, true);
                                                                  								  
                                                                    }



                                                                else{
                                                                  $fan_p[]= json_decode($fan_P_1, true);
								  $fan_p[]= json_decode($fan_P_2, true);
                                                               



                                                                   }



								  $fan_par=json_encode($fan_p);

        
									
                                                                        if($model_number == "EFACT V1.2")
                                                                      {
									$devices_1 = json_encode(["name"=>$curr_devicetype,
												"attributes"=>$fan_attri,
                                                                                             
												"params"=>$fan_p,
												"primary"=>"power",
											       "type"=>"efactor.device.fan","brand_logo"=>$url]);

                                                                       }
                                                                       else{

                                                                            $devices_1 = json_encode(["name"=>$curr_devicetype,
												"attributes"=>$fan_attri,           
												"params"=>$fan_p,
												"primary"=>"power",
											       "type"=>"efactor.device.fan","brand_logo"=>$url]);

                                                                      }






									//$devices_fan[] = json_decode($devices_1, true);

                                            $devices_list[] = json_decode($devices_1, true);


									/*****************/

                                                                if($power == '0')
                                                                           {
                                                                             $led = $power;
                                                                             }

								

                                     /*  $device_para_array_1 = array("Direction"=>intval($direction),
					                             "led colour"=>intval($hue),
							                 "output"=>true,
									"power"=>$power,
									"Speed"=>intval($speed),
									"led"=>$led,
									"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                         "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm,
                                                                         "buzzer"=>(bool)$fan_buzzer);*/



                                              if($model_number == "EFACT V1.2")
                                                                      {
                                                    $device_para_array_1 = array("Direction"=>intval($direction),
					                             "led colour"=>intval($hue),
							                 "output"=>true,
									"power"=>$power,
									"Speed"=>intval($speed),
									"led"=>$led,
									"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                         "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm,
                                                                         "buzzer"=>(bool)$fan_buzzer);
                                                                        

                                                                  }

                                                 else{

                                       $device_para_array_1 = array("Direction"=>intval($direction),
					                             "led colour"=>intval($hue),
							                 "output"=>true,
									"power"=>$power,
									"Speed"=>intval($speed),
									"led"=>$led,
									"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                         "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm);

                                                 }
                                             



                                             
                                             

                                           $device_para[$pos]= array($curr_devicetype=>$device_para_array_1);    //14-8





                                           $devices_config_para = array("resistance"=>$fan_resistance,
                        "lmin"=>$fan_lmin,
                        "lmax"=>$fan_lmax,
                        "frequency"=>$fan_frequency,
                        "pole pairs"=>$fan_pole_pairs,
                        "open loop current"=>$fan_open_loop_current,
                        "power limit"=>$fan_power_limit,
                        "torque limit"=>$fan_torque_limit,
                        "speed1"=>$fan_speed1,
                        "speed2"=>$fan_speed2,
                        "speed3"=>$fan_speed3,
                        "speed4"=>$fan_speed4,
                         "speed5"=>$fan_speed5,
                         "speed6"=>$fan_speed6,
                           "speed7"=>$fan_speed7,
                           "button"=>$fan_button);
                                                                        




                                 
                                                

                                           
                                         

                        }


                             else if($device_type_5 == 'light' && $addstatus_5== 'confirmed')

                              {
                                            

								$light_attr_1 = json_encode(["name"=>"Device Id",
															"value"=>$link_dev]);

								$light_attr_2 = json_encode(["name"=>"mac",
															"value"=>$mac]);
								$light_attri[] = json_decode($light_attr_1, true);
								$light_attri[] = json_decode($light_attr_2, true);
								$light_att=json_encode($light_attri);
								

								$light_P_1 = json_encode(["name" => "name",
													  "data_type"=> "String",
													 "properties"=> ["read","write"],
														   "type"=>"efactor.param.name"]);

								$light_P_2 = json_encode(["name" => "power",
													  "data_type"=> "bool",
													 "properties"=> ["read","write"],
                                                                  "type"=>"efactor.param.power",
														"ui_type"=>"efactor.ui.toggle"]);

								
								$light_p[]= json_decode($light_P_1, true);
								$light_p[]= json_decode($light_P_2, true);
								

						

								$light_par=json_encode($light_p);

								$devices_1 = json_encode(["name"=>$curr_devicetype,
													"attributes"=>$light_attri,
														"params"=>$light_p,
													   "primary"=>"power",
														  "type"=>"efactor.device.lightbulb","brand_logo"=>$url]);
								$devices_list[] = json_decode($devices_1, true);

								
								/********************/
                                       

								$device_para_light_1= array("brightness" => intval($brightness),
                                                                       "output" => true,
                                                                      "power"=>$power,
                                                                       "name"=>$name);
                                             

                                           $device_para[$pos] = array($curr_devicetype => $device_para_light_1);
                                           // $device_para_light[]=$device_para_light_3;
                                           // $device_para_arr[] = json_decode($device_para_light_2, true);


				/*******************/
     
                              }

                             else if($device_type_5 == 'switch' && $addstatus_5== 'confirmed')

                              {
                                      
								/*-----------------switch-----------------*/

								$switch_attr_1 = json_encode(["name"=>"Device Id",
															 "value"=>$link_dev]);

								$switch_attr_2 = json_encode(["name"=>"mac",
															 "value"=>$mac]);
								$switch_attri[] = json_decode($switch_attr_1, true);
								$switch_attri[] = json_decode($switch_attr_2, true);

								$switch_att=json_encode($switch_attri);

								$switch_P_1 = json_encode(["name" => "name",
													   "data_type"=> "String",
													  "properties"=> ["read","write"],
															"type"=>"efactor.param.name"]);

								$switch_P_2 = json_encode(["name" => "power",
													   "data_type"=> "bool",
													  "properties"=> ["read","write"],
                                                                   "type"=>"efactor.param.power",
														 "ui_type"=>"efactor.ui.toggle"]);

								$switch_p[]= json_decode($switch_P_1, true);
								$switch_p[]= json_decode($switch_P_2, true);
								$switch_par=json_encode($switch_p);

								$devices_1 = json_encode(["name"=>$curr_devicetype,
													"attributes"=>$switch_attri,
														"params"=>$switch_p,
													   "primary"=>"power",
														  "type"=>"efactor.device.switch","brand_logo"=>$url]);

								$devices_list[] = json_decode($devices_1, true);


								

					/****************************/ 

								//$device_para_switch = array("power"=> true,
                                                                //                               "output" => true);


                                          $device_para_switch_1 = array("power"=> $power,
                                                                        "output" => true,
                                                                         "name"=>$name);

                                           $device_para_switch_3 = array($curr_devicetype => $device_para_switch_1);
                                                $device_para[$pos]=$device_para_switch_3;

                                         // $device_para_switch[] = json_decode($device_para_switch_2, true);

					/***************************/


 
	                          }
   

                                  else if($device_type_5 == 'cooker' && $addstatus_5== 'confirmed')

                              {


                                                  
                                      
								/*-----------------cooker-----------------*/



 

								
                                                                

                                                                $cooker_attr_1 = json_encode(["name"=>"Device Id",
											    "value"=>$link_dev]);

								$cooker_attr_2 = json_encode(["name"=>"mac",
											     "value"=>$mac]);
								$cooker_attri[] = json_decode($cooker_attr_1, true);
								$cooker_attri[] = json_decode($cooker_attr_2, true);
                                                               

								$cooker_att=json_encode($cooker_attri);




/*------------------------------------------------*/
                                                                    


                                                                    


                                                            













								$cooker_P_1 = json_encode(["name" => "name",
													   "data_type"=> "String",
													  "properties"=> ["read","write"],
													"type"=>"efactor.param.name"]);

								$cooker_P_2 = json_encode(["name" => "power",
											 "data_type"=> "bool",
											 "properties"=> ["read","write"],
                                                                                             "type"=>"efactor.param.power",
											 "ui_type"=>"efactor.ui.toggle"]);

								      

									$cooker_p[]= json_decode($cooker_P_1, true);
									$cooker_p[]= json_decode($cooker_P_2, true);
                                                                      									$cooker_par=json_encode($cooker_p);





								$devices_1 = json_encode(["name"=>$curr_devicetype,
													"attributes"=>$cooker_attri,
                                                                                                          
														"params"=>$cooker_p,
													   "primary"=>"power",
														  "type"=>"efactor.device.cooker","brand_logo"=>$url]);

								$devices_list[] = json_decode($devices_1, true);


								

								/****************************/ 

								//$device_para_switch = array("power"=> true,
                                         //                               "output" => true);


                                          $device_para_cooker_1 = array("power"=> $power,
                                                                        "output" => true,
                                                                        "skipmode"=>true,
                                                                         "cookermode"=>$cookermode,
                                                                            
                                                                         "recipe"=>$recipe,
                                                                         "name"=>$name,
                                                                        "remaining time"=>$cooktime,
                                                                        "running temperature"=>$cook_run_temp);

                                           $device_para_cooker_3 = array($curr_devicetype => $device_para_cooker_1);
                                                $device_para[$pos]=$device_para_cooker_3;

                                         // $device_para_switch[] = json_decode($device_para_switch_2, true);

								/***************************/


                                                          /*----------------------------------*/


														
                                               $devices_config_para = array("Cooker Timer"=>(int)$cooker_timer,
                                                                           "Cooking temperature"=>(int)$cooking_temperature,
                                                                           "Keep warm temperature"=>(int)$Keep_warm,
                                                                           "Pre-timer temp constant mode"=>$Pre_timer_constant_mode,
                                                                           "Pre-timer"=>(int)$Pre_timer,
                                                                           "Pre – temperature"=>(int)$Pre_temperature,
                                                                           "Coooker max-temperature"=>(int)$Coooker_max_temperature,
                                                                           "Cooker temp constant mode"=>(bool)$Cooker_constant_mode,
                                                                           "Keep Warm timer"=>(int)$Keep_Warm_timer,
                                                                           "pre-timer wait temperature constant mode"=>(bool)$pre_timer_wait_temperature_constant_mode,
                                                                           "pre-timer waiting time"=>(int)$pre_timer_waiting_time,
                                                                           "pre-timer Waiting temperature"=>(int)$pre_timer_Waiting_temperature,
                                                                           "Keep warm temp constant mode"=>(bool)$warm_constant_cut_off,
                                                                           "cook wait temp constant mode"=>(int)$cook_wait_temp_constant_mode,
                                                                           "cook waiting time"=>(int)$cook_waiting_time,
                                                                           "cook Waiting- temperature"=>(int)$cook_Waiting_temperature,
                                                                           "warm-wait temp constant mode"=>(bool)$warm_wait_temp_constant_mode,
                                                                           "warm waiting time"=>(int)$warm_waiting_time,
                                                                           "warm Waiting temperature"=>(int)$warm_Waiting_temperature,
                                                                             "OK" => (bool)$cooker_ok_button,
                                                                           "Reload Default" => (bool)$reload_default,
                                                                            "Dishes" => $recipe);




                                                                        


							/*-----------------------------------*/	




 
	                          }

                                      

                                   /**********scanner***************21-4-23***********************/


                                           else if($device_type_5 == 'scanner' && $addstatus_5== 'confirmed')

                              {
                                      
								/*-----------------cooker-----------------*/

								$scanner_attr_1 = json_encode(["name"=>"Device Id",
															 "value"=>$link_dev]);

								$scanner_attr_2 = json_encode(["name"=>"mac",
															 "value"=>$mac]);
								$scanner_attri[] = json_decode($scanner_attr_1, true);
								$scanner_attri[] = json_decode($scanner_attr_2, true);

								$scanner_att=json_encode($scanner_attri);

								$scanner_P_1 = json_encode(["name" => "name",
													   "data_type"=> "String",
													  "properties"=> ["read","write"],
													"type"=>"efactor.param.name"]);

								$scanner_P_2 = json_encode(["name" => "power",
											 "data_type"=> "bool",
											 "properties"=> ["read","write"],
                                                                                             "type"=>"efactor.param.power",
											 "ui_type"=>"efactor.ui.toggle"]);

								$scanner_p[]= json_decode($scanner_P_1, true);
								$scanner_p[]= json_decode($scanner_P_2, true);
								$scanner_par=json_encode($scanner_p);

								$devices_1 = json_encode(["name"=>$curr_devicetype,
													"attributes"=>$scanner_attri,
														"params"=>$scanner_p,
													   "primary"=>"power",
														  "type"=>"efactor.device.qr-scanner","brand_logo"=>$url]);

								$devices_list[] = json_decode($devices_1, true);


								

								/****************************/ 

								//$device_para_switch = array("power"=> true,
                                         //                               "output" => true);


                                          $device_para_scanner_1 = array("power"=> $power,
                                                                        "output" => true,
                                                                         "name"=>$name);

                                           $device_para_scanner_3 = array($curr_devicetype => $device_para_scanner_1);
                                                $device_para[$pos]=$device_para_scanner_3;

                                         // $device_para_switch[] = json_decode($device_para_switch_2, true);

								/***************************/


 
	                          }










                             

                             else if($device_type_5 == 'outlet' && $addstatus_5== 'confirmed')

                              {
                                      // $outlet_attri = array();

                                             $outlet_attri = [];
                                                   $outlet_p = [];
                                      
								/*-----------------outlet-----------------*/

								$outlet_attr_1 = json_encode(["name"=>"Device Id",
															 "value"=>$link_dev]);

								$outlet_attr_2 = json_encode(["name"=>"mac",
															 "value"=>$mac]);
								$outlet_attri[] = json_decode($outlet_attr_1, true);
								$outlet_attri[] = json_decode($outlet_attr_2, true);

								$outlet_att=json_encode($outlet_attri);

								$outlet_P_1 = json_encode(["name" => "name",
													   "data_type"=> "String",
													  "properties"=> ["read","write"],
															"type"=>"efactor.param.name"]);

								$outlet_P_2 = json_encode(["name" => "power",
													   "data_type"=> "bool",
													  "properties"=> ["read","write"],
                                                                   "type"=>"efactor.param.power",
														 "ui_type"=>"efactor.ui.toggle"]);

								$outlet_p[]= json_decode($outlet_P_1, true);
								$outlet_p[]= json_decode($outlet_P_2, true);
								$outlet_par=json_encode($outlet_p);

								$devices_1 = json_encode(["name"=>$curr_devicetype,
													"attributes"=>$outlet_attri,
														"params"=>$outlet_p,
													   "primary"=>"power",
														  "type"=>"efactor.device.outlet","brand_logo"=>$url]);

								$devices_list[] = json_decode($devices_1, true);


								

								/****************************/ 

								


                                          $device_para_outlet_1 = array("power"=> $power,
                                                                        "output" => true,
                                                                         "name"=>$name);

                                           $device_para_outlet_3 = array($curr_devicetype => $device_para_outlet_1);
                                                $device_para[$pos]=$device_para_outlet_3;

                                         // $device_para_switch[] = json_decode($device_para_switch_2, true);

								/***************************/


 
	                          }




                              else if($device_type_5 == 'other' && $addstatus_5== 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$other_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$link_dev]);

									$other_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$other_attri[] = json_decode($other_attr_1, true);
									$other_attri[] = json_decode($other_attr_2, true);

									$other_att=json_encode($other_attri);

									$other_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$other_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$other_p[]= json_decode($other_P_1, true);
									$other_p[]= json_decode($other_P_2, true);
									$other_par=json_encode($other_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$other_attri,
															"params"=>$other_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.other","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


										}




                                                else if($device_type_5 == 'HBMDNC' && $addstatus_5== 'confirmed')

                                   {


                                                      $hbmdnc_attri = array();
                                                      $hbmdnc_p = array();

									/*-----------------outlet--------------*/
									$hbmdnc_attr_1 = json_encode(["name"=>"Device Id",
											        "value"=>$link_dev]);

									$hbmdnc_attr_2 = json_encode(["name"=>"mac",
											         "value"=>$mac]);
									$hbmdnc_attri[] = json_decode($hbmdnc_attr_1, true);
									$hbmdnc_attri[] = json_decode($hbmdnc_attr_2, true);

									$hbmdnc_att=json_encode($hbmdnc_attri);

									$hbmdnc_P_1 = json_encode(["name" => "name",
												 "data_type"=> "String",
											"properties"=> ["read","write"],
											"type"=>"efactor.param.name"]);

									

                                                         

									$hbmdnc_p[]= json_decode($hbmdnc_P_1, true);
									

									$hbmdnc_par=json_encode($hbmdnc_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$hbmdnc_attri,
															"params"=>$hbmdnc_p,
														   
															  "type"=>"efactor.device.hbmdnc",
                                                                                                                      "brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 
									$devices_para_arr = array("Doctor"=>(bool)$doctormanage,
                                                                                                  "Personal Assist"=>(bool)$personal_assistant,
                                                                                                   "nurse"=>(bool)$nurse,
                                                                                                    "Doctor status"=>$doctormanage_resp,
                                                                                                    "PA status"=>$personal_assistant_resp,
                                                                                                   "Nurse_Status"=>$nurse_resp,
                                                                                                    "name"=>$name);


									/***************************/


										}















               else if($device_type_5 == 'input-output' && $addstatus_5== 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$other_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$link_dev]);

									$other_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$other_attri[] = json_decode($other_attr_1, true);
									$other_attri[] = json_decode($other_attr_2, true);

									$other_att=json_encode($other_attri);

									$other_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$other_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$other_p[]= json_decode($other_P_1, true);
									$other_p[]= json_decode($other_P_2, true);
									$other_par=json_encode($other_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$other_attri,
															"params"=>$other_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.input-output","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


										}




                                        else if($device_type_5 == 'exhaust_hood' && $addstatus_5== 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$exh_attr_1 = json_encode(["name"=>"Device Id",
													"value"=>$link_dev]);

									$exh_attr_2 = json_encode(["name"=>"mac",
												"value"=>$mac]);
									$exh_attri[] = json_decode($exh_attr_1, true);
									$exh_attri[] = json_decode($exh_attr_2, true);

									$exh_att=json_encode($exh_attri);

									$exh_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$exh_P_2 = json_encode(["name" => "power",
												"data_type"=> "bool",
												"properties"=> ["read","write"],
                                                                                                   "type"=>"efactor.param.power",
												 "ui_type"=>"efactor.ui.toggle"]);





                                                                     



                                                                      


									$exh_p[]= json_decode($exh_P_1, true);
									$exh_p[]= json_decode($exh_P_2, true);
                                                                       
									
									$exh_par=json_encode($exh_p);
                                                                       
									

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$exh_attri,
															"params"=>$exh_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.exhaust-hood","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

                                                                     /* $devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);*/

									$devices_para_arr = array("power" => $power,
                                                                                                   "output" => true,
                                                                                                   "name" => $name,
                                                                                               "autoclean" =>(bool)$hood_autoclean,
                                                                                                  "airflow" => (int)$hood_speed,
                                                                                                    "lamp" =>(bool)$hood_lamp);

									/***************************/


										}




                                              else if($device_type_5 == 'swpcp' && $addstatus_5== 'confirmed')

                                   {

									
									$wp_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$link_dev]);

									$wp_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$wp_attri[] = json_decode($wp_attr_1, true);
									$wp_attri[] = json_decode($wp_attr_2, true);

									$wp_att=json_encode($wp_attri);


                                                                      







									$wp_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$wp_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                                                               "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);


                                                                       $max_2 = array("max"=>240,
										          "min"=>0);
                                                                
                                                                
                                                                      

                                                                         



                                                                          
                
									$wp_p[]= json_decode($wp_P_1, true);
									$wp_p[]= json_decode($wp_P_2, true);
                                                                       									//$wp_par=json_encode($wp_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$wp_attri,
                                                                                                            
															"params"=>$wp_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.swpcp","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name,
                                                                        "current(A)"=>$current,
                                                                        "voltage(V)"=>$voltage,
                                                                        "remaining time"=>$rem_time,
                                                                        "total watt"=>$watt);

									/***************************/
  

                                                                         $devices_config_para = array("starter relay off timer"=>$starter_relay_off_timer,
                        "start high current cutoff"=>$start_high_current_cutoff,
                        "start low current cutoff"=>$start_low_current_cutoff,
                        "Run high current cutoff"=>$Run_high_current_cutoff,
                        "Run low current cutoff"=>$Run_low_current_cutoff,
                        "high voltage cutoff"=>$high_voltage_cutoff,
                        "low voltage cutoff"=>$low_voltage_cutoff,
                        "ultra high voltage cutoff"=>$ultra_high_voltage_cutoff,
                        "ultra high current cutoff"=>$ultra_high_current_cutoff,
                        "USER SET MAX CURRENT CUTOFF LIMIT"=>$USER_SET_MAX_CURRENT_CUTOFF_LIMIT,
                        "USER AND DEVICE MAX CURRENT LIMIT"=>$USER_AND_DEVICE_MAX_CURRENT_LIMIT,
                        "LOCK ENABLE/DISABLE"=>$LOCK_ENABLE_DISABLE);
                                                                        
                                                                    /*********************************/


										}

                  

                                $devices=array($devices_gate, $devices_fan, $devices_light, $devices_switch);        //8-2
                               
                            
                                
                               /****************************************/

$query_id = "SELECT * FROM efactorproduct WHERE node_id='$node_id'";
$num_id = $db->num_rows($query_id);
                        $row_id = $db->get_row($query_id);
                        $id= $row_id['id'];




//$query_next_id = "SELECT node_id FROM efactorproduct WHERE access_token = '$val' AND is_added = '1' AND node_id > '$node_id' ORDER BY node_id ASC LIMIT 1";
$query_next_id = "SELECT node_id FROM efactorproduct WHERE access_token = '$val' AND is_added = '1' AND id > '$id' ORDER BY id ASC";
$row = $db->get_row($query_next_id);

if ($row) {
    
    $device_id_next = $row['node_id'];
} else {
   
//echo $node_id;
    $query_next_id = "SELECT node_id FROM efactorproduct WHERE access_token = '$val' AND is_added='NULL' AND add_gateway = 'NULL'";
    $row = $db->get_row($query_next_id);

    if ($row) {
        $device_id_next = $row['node_id'];
    } else {
        
        $device_id_next = null; // or any other desired action
    }
}

$nextid = $device_id_next;






                       
                                 /*****************************************/



                       


                               /*$query_next_id = "SELECT * FROM efactorproduct WHERE access_token='$val' AND add_gateway='NULL' OR add_gateway !='NULL'";
                                $result_next_id = $db->get_results($query_next_id);
                                $num_next = $db->num_rows($query_next_id);

                                foreach ($result_next_id as $row) {
                                          $is_linked_next = $row['is_added'];
                                          $device_id_next = $row['node_id'];
                                          $nextid = $device_id_next;
                                          // Print or perform operations with $nextid
                                         // echo $nextid . "<br>";
                                       }*/


                                 /* $query_next_id = "SELECT * FROM efactorproduct WHERE access_token='$val'AND add_gateway='NULL' OR add_gateway !='NULL'";
                                  $result_next_id = $db->get_results($query_next_id);
                                  $num_next = $db->num_rows($query_next_id);

                                  foreach ($result_next_id as $row) {
                                               $is_linked_next = $row['is_added'];
                                               $device_id_next = $row['node_id'];
                                               $nextid = $device_id_next;
                                             //  echo $nextid . "<br>";
                                           }*/






                              

$params = json_encode([$name=>$dev_paras]);
//$params_1[]=json_decode($params,true);
//echo $params;


            }
                                  

                                     }


else{


               

      


                     /* $query_2 = "SELECT * FROM efactorproduct where access_token='$val' AND is_added='1'";
                      // $query_2 = "SELECT * FROM efactorproduct where access_token='$val' AND add_gateway='NULL'";
		     // $row = $db->get_row($query_2);
	              //$is_added=$row['is_linked'];
                      $num_link = $db->num_rows($query_2);*/


/*$endTime_1 = microtime(true);

$executionTime_1 = $endTime_1 - $startTime;

echo $executionTime_1;  */
						
          
		                  $arr=array();

							foreach ($doctors as $key => $doctor) 
							  {
								array_push($arr,array("id"=>$doctor['id'],"node_id"=>$doctor['node_id'],"device_type"=>$doctor['device_type']));
								
							  }

							/*************************/
							/* for ($pos = 0; $pos <= $num_dev; $pos++) 
								  {      
								   if($arr[$pos]['node_id']==$startid)
									{
									 
									  $next_id_1 =$arr[$pos+1]['node_id'];
									 
									}
									 
								   
								 }*/
							/************************/

/*

for ($pos = 0; $pos < $num_dev; $pos++) {      
               
if ($arr[$pos]['node_id'] == $startid) {
        
if (isset($arr[$pos + 1])) {
           

$next_id_1 = $arr[$pos + 1]['node_id'];      
echo $next_id_1;
$query_11 = "SELECT * FROM efactorproduct where node_id='$next_id_1'";
						  $doctors = $db->get_results($query_11); 
						  $row_11 = $db->get_row($query_11); 
						  $isadded=$row_11['is_added'];
                                                  $add_gateway=$row_11['add_gateway'];



if($isadded == '1' || $add_gateway != 'NULL')
{

$next_id_1 = $arr[$pos + 2]['node_id'];
}



        }
    }
}

*/


for ($pos = 0; $pos < $num_dev; $pos++) {
    if ($arr[$pos]['node_id'] == $startid) {
        if (isset($arr[$pos + 1])) {
            $next_id_1 = $arr[$pos + 1]['node_id'];
           // echo $next_id_1;

            do {
                $query_11 = "SELECT * FROM efactorproduct WHERE node_id='$next_id_1'";
                $row_11 = $db->get_row($query_11);

                $isadded = $row_11['is_added'];
                $add_gateway = $row_11['add_gateway'];

                if ($isadded == '1' || $add_gateway != 'NULL') {
                    $next_id_1 = isset($arr[$pos + 2]) ? $arr[$pos + 2]['node_id'] : null;
                    $pos++;
                } else {
                    break;
                }
            } while ($next_id_1 != null);
        }
    }
}























                                                    /**************************/
                   
							$arr=array();
							$device_id=$startid;
							$nextid =$next_id_1;
							//$id=$device_id;
          
                                                       /***********************/

                                                  $query_1 = "SELECT * FROM efactorproduct where access_token='$val' AND node_id='$startid'";
						  //$doctors = $db->get_results($query_1); 
						  $row = $db->get_row($query_1); 
						  $device_id=$row['node_id'];
						  $node_id=$row['node_id'];
                                                  $address=$startid;
                                                  // echo $address;
						  $moduletype= $row['module_type'];
						  $devicetype_2=$row['device_type'];
                                                  $devicetype=$row['device_type'];
                                                  $addstatus_2=$row['req_status'];
                                                  $share_req_status=$row['share_req_status'];
						  $name=$row['name'];
						  $speed=$row['speed'];
						  $hue=$row['Hue'];
						  $mac=$row['mac'];
						  $direction=$row['direction'];
                                                  $brightness=$row['brightness'];
                                                  $curr_devicetype=$row['current_devicetype'];
                                                  $recipe=$row['recipe'];
                                                  $user_priority=$row['user_priority'];
                                                  $cookermode=$row['cookermode'];
                                                  $cooker_led=$row['cooker_led'];
                                                  $cooktime=$row['cook_time'];
                                             $cook_run_temp=$row['cook_run_temp'];
                                                $current=$row['current'];  
                                                 $voltage=$row['ac_float'];
                                                 $bus_voltage=$row['bus_voltage'];
                                             $rpm=$row['rpm']; 
                                             $timer=$row['timer'];
                                              $watt=$row['power_watt'];
                                             $rem_time=$row['rem_time'];
                                             $hood_autoclean=$row['hood_autoclean'];
                                              $hood_speed=$row['hood_speed'];
                                              $hood_lamp=$row['hood_lamp'];

                                           /*   $nurse=$row['nurse'];
                                              $personal_assistant=$row['personal_assistant'];
                                              $doctormanage=$row['doctor'];
                                               $nurse_resp=$row['nurse_resp'];
                                              $personal_assistant_resp=$row['personal_assistant_resp'];
                                              $doctormanage_resp=$row['doctor_resp'];*/

                                             $nurse=filter_var($row['nurse'], FILTER_VALIDATE_BOOLEAN);
$personal_assistant=filter_var($row['personal_assistant'], FILTER_VALIDATE_BOOLEAN);
$doctormanage=filter_var($row['doctor'], FILTER_VALIDATE_BOOLEAN);
$nurse_resp=$row['nurse_resp'];      
$personal_assistant_resp=$row['personal_assistant_resp'];                    
$doctormanage_resp=$row['doctor_resp'];                      






                                              $model_number=$row['model_number'];
                                                           $devicename=$row['device_name'];
                                                           $vendor_name=$row['vendor_name'];
                                                           $moduletype=$row['module_type'];
                                                           $net_conn_1=$row['internet'];
                                                       $access_token=$row['access_token'];

                                                    if($devicetype_2 == 'exhaust_hood')
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




                                           //swpcp
                                       if($devicetype_2 == 'swpcp')
                                            {

                                            $starter_relay_off_timer = $row['starter_relay_off_timer'];  
$start_high_current_cutoff = $row['start_high_current_cutoff'];  
$start_low_current_cutoff= $row['start_low_current_cutoff'];  
$Run_high_current_cutoff = $row['Run_high_current_cutoff'];  
$Run_low_current_cutoff = $row['Run_low_current_cutoff'];  
$high_voltage_cutoff = $row['high_voltage_cutoff'];  
$low_voltage_cutoff = $row['low_voltage_cutoff'];  
$ultra_high_voltage_cutoff = $row['ultra_high_voltage_cutoff'];  
$ultra_high_current_cutoff = $row['ultra_high_current_cutoff'];  
$USER_SET_MAX_CURRENT_CUTOFF_LIMIT = $row['USER_SET_MAX_CURRENT_CUTOFF_LIMIT'];  
$USER_AND_DEVICE_MAX_CURRENT_LIMIT = $row['USER_AND_DEVICE_MAX_CURRENT_LIMIT'];  
$LOCK_ENABLE_DISABLE = (bool)$row['LOCK_ENABLE_DISABLE']; 
}


if($devicetype_2 == 'cooker')
                                              {
                                           //cooker

                                            $cooker_timer = $row['cooker_timer_1'];  
                                            $cooking_temperature = $row['cooker_max_temperature_1'];   
                                            $Keep_warm= $row['Keep_warm_1'];  
                                            $Pre_timer_constant_mode = $row['Pre_timer_constant_mode'];  
                                            $Pre_timer = $row['Pre_timer'];  
                                            $Pre_temperature = $row['Pre_temperature'];  
                                            $Coooker_max_temperature = $row['Coooker_max_temperature'];  
                                            $Cooker_constant_mode = $row['Cooker_constant_mode'];  
                                            $Keep_Warm_timer = $row['Keep_Warm_timer_1'];  
                                            $Dishes = $row['dish'];
                                            $pre_timer_wait_temperature_constant_mode = $row['pre_timer_wait_temperature_constant_mode'];  
                                            $pre_timer_waiting_time = $row['pre_timer_waiting_time'];
                                            $pre_timer_Waiting_temperature = $row['pre_timer_Waiting_temperature'];  
                                            $warm_constant_cut_off = $row['warm_constant_cut_off'];  
                                            $cook_wait_temp_constant_mode = $row['cook_wait_temp_constant_mode'];  
                                            $cook_waiting_time = $row['cook_waiting_time'];  
                                            $cook_Waiting_temperature = $row['cook_Waiting_temperature'];  
                                            $warm_wait_temp_constant_mode = $row['warm_wait_temp_constant_mode'];  
                                            $warm_waiting_time = $row['warm_waiting_time'];  
                                            $warm_Waiting_temperature = $row['warm_Waiting_temperature'];  
                                            $cooker_ok_button = $row['cooker_ok_button']; 
                                            $reload_default = $row['reload_default'];


                                         }



                                            //    echo 'echo 2';
                                            //   echo $Coooker_max_temperature;




                                          if($devicetype_2 == 'fan')
                                            {
                                              
                                               //fan
                                            $fan_resistance = $row['fan_resistance'];  
                                            $fan_lmin = $row['fan_lmin'];  
                                            $fan_lmax= $row['fan_lmax'];  
                                            $fan_frequency = $row['fan_frequency'];  
                                            $fan_pole_pairs = $row['fan_pairs'];  
                                            $fan_open_loop_current = $row['fan_open_loop_current'];  
                                            $fan_power_limit = $row['fan_power_limit'];  
                                            $fan_torque_limit = $row['fan_torque_limit'];  
                                            $fan_speed1 = $row['fan_speed1'];  
                                            $fan_speed2= $row['fan_speed2'];
                                            $fan_speed3 = $row['fan_speed3'];  
                                            $fan_speed4= $row['fan_speed4'];
                                            $fan_speed5 = $row['fan_speed5'];  
                                            $fan_speed6= $row['fan_speed6'];
                                            $fan_speed7 = $row['fan_speed7'];  
                                            $fan_button= $row['fan_button']; 
                                          }

						  //$power=$row['power'];
						  $baudrate=$row['baudrate'];
						  //$led=$row['led_indication'];
						  $power=filter_var($row['power'], FILTER_VALIDATE_BOOLEAN);
						  $led = filter_var($row['led_indication'], FILTER_VALIDATE_BOOLEAN);
                                                  $devicename=$row['device_name'];

                                      $tz_properties=$row['TZ_properties'];
                                      $br_properties=$row['BR_properties'];
                                      $umi_properties=$row['UMI_properties'];
                                      $ec_properties=$row['EC_properties'];
                                      $cm_properties=$row['CM_properties'];
                                      $tu_properties=$row['TU_properties'];
                                      $remote_pair_properties=$row['remote_pair_properties'];
                                       $display_properties=$row['display_properties'];
                                       $swpcp_1ph_properties=$row['swpcp_1ph_properties'];
                                       $fan_properties=$row['fan_properties'];
                                      $lc_properties=$row['LC_properties'];
                                      $cooker_properties=$row['cooker_properties'];
                                       
       
                                     $baudrate=$row['baudrate'];
                                     $uartmsgid=$row['uartmsgid'];
                                     $time=$row['time'];
                                     $errorcode=$row['errorcode'];
                                     $comm_mode=$row['comm_mode'];
                                    

                                   /* $model_number=$row['model_number'];
                                     $vendor_name=$row['vendor_name'];*/





              



                               /********************************/

                           if($devicetype_2 == 'Gateway' && $addstatus_2 == 'confirmed')
                             {


                                     /******************************/
                                           $gate_attr_1 = json_encode(["name"=>"Device Id",
				                                        "value"=>$device_id]);

								$gate_attr_2 = json_encode(["name"=>"mac",
										            "value"=>$mac]);
								$gate_attri[] = json_decode($gate_attr_1, true);
								$gate_attri[] = json_decode($gate_attr_2, true);

								$gate_att=json_encode($fan_attri);



                                /*********************************/






								$G_Params = json_encode(["name" => "name",
											 "type"=> "efactor.param.name",
										      "data_type"=>"string",
										    "properties"=> ["read","write"]]);


                                      $G_Params_2=json_encode(["name"=> "power",
                                                             "data_type"=> "bool",
                                                             "properties"=>["read","write"],
                                                                "type"=>"efactor.param.power",
                                                             "ui_type"=>"efactor.ui.toggle"]);


							   $G_p_1[] = json_decode($G_Params, true);
                                      $G_p_1[] = json_decode($G_Params_2, true);

							   $Gate_Dev= json_encode(["name" => $curr_devicetype,
                                                                                  "attributes"=>$gate_attri,
										"type"=> "efactor.device.gateway",
                                                                                "params"=> $G_p_1]);
                                  

							   $devices_list[] = json_decode($Gate_Dev, true);


                                                      



                             }


                            else if($devicetype_2 == 'fan' && $addstatus_2 == 'confirmed')

                             {
        


                                            $fan_attri = array();


								$fan_attr_1 = json_encode(["name"=>"Device Id",
											"value"=>$device_id]);

								$fan_attr_2 = json_encode(["name"=>"mac",
											  "value"=>$mac]);
								$fan_attri[] = json_decode($fan_attr_1, true);
								$fan_attri[] = json_decode($fan_attr_2, true);

								$fan_att=json_encode($fan_attri);


    

                                                                         

                                                                        

                                                                   
                                                                   

								  $fan_P_1 = json_encode(["name" => "name",
														  "data_type"=> "String",
														 "properties"=>["read","write"],
															   "type"=> "efactor.param.name"]);


								  $fan_P_2 = json_encode(["name" => "power",
													  "data_type"=> "bool",
													 "properties"=> ["read","write"],
														   "type"=>"efactor.param.power",
                                                                    "type"=>"efactor.param.power",
														"ui_type"=>"efactor.ui.toggle"]);
                                                                         



                                                                  if($model_number == "EFACT V1.2")
                                                                      {

								  $fan_p[]= json_decode($fan_P_1, true);
								  $fan_p[]= json_decode($fan_P_2, true);
                                                                 								  
                                                                    }



                                                                else{
                                                                  $fan_p[]= json_decode($fan_P_1, true);
								  $fan_p[]= json_decode($fan_P_2, true);
                                                              




                                                                   }



								  

								  $fan_par=json_encode($fan_p);





								  
									

									
                                                                     if($model_number == "EFACT V1.2")
                                                                      {
									$devices_1 = json_encode(["name"=>$curr_devicetype,
												"attributes"=>$fan_attri,
                                                                                               												"params"=>$fan_p,
												"primary"=>"power",
											       "type"=>"efactor.device.fan","brand_logo"=>$url]);

                                                                       }
                                                                       else{

                                                                            $devices_1 = json_encode(["name"=>$curr_devicetype,
												"attributes"=>$fan_attri,           
												"params"=>$fan_p,
												"primary"=>"power",
											       "type"=>"efactor.device.fan","brand_logo"=>$url]);

                                                                      }



									$devices_list[] = json_decode($devices_1, true);

							        /*****************/

                                                                         if($power == '0')
                                                                           {
                                                                             $led = $power;
                                                                             }

									/*$device_type_param = array("Direction" =>intval($direction),
												 "led colour"=>intval($hue),
												  "output"=>true,
											           "power"=>$power,
												   "Speed"=>intval($speed),
												      "led"=>$led,
													"name"=>$name);*/

									/*******************/

                                       /*  $devices_para_arr = array("Direction" =>intval($direction),
								"led colour"=>intval($hue),
									"output"=>true,
													  "power"=>$power,
													  "Speed"=>intval($speed),
														"led"=>$led,
													"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                          "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm,
                                                                         "buzzer"=>(bool)$fan_buzzer);*/





                                     if($model_number == "EFACT V1.2")
                                                                      {
                                                    $devices_para_arr = array("Direction"=>intval($direction),
					                             "led colour"=>intval($hue),
							                 "output"=>true,
									"power"=>$power,
									"Speed"=>intval($speed),
									"led"=>$led,
									"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                         "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm,
                                                                         "buzzer"=>(bool)$fan_buzzer);
                                                                        

                                                                  }

                                                 else{

                                       $devices_para_arr = array("Direction"=>intval($direction),
					                             "led colour"=>intval($hue),
							                 "output"=>true,
									"power"=>$power,
									"Speed"=>intval($speed),
									"led"=>$led,
									"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                         "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm);

                                                 }







                                                        $devices_config_para = array("resistance"=>$fan_resistance,
                        "lmin"=>$fan_lmin,
                        "lmax"=>$fan_lmax,
                        "frequency"=>$fan_frequency,
                        "pole pairs"=>$fan_pole_pairs,
                        "open loop current"=>$fan_open_loop_current,
                        "power limit"=>$fan_power_limit,
                        "torque limit"=>$fan_torque_limit,
                        "speed1"=>$fan_speed1,
                        "speed2"=>$fan_speed2,
                        "speed3"=>$fan_speed3,
                        "speed4"=>$fan_speed4,
                         "speed5"=>$fan_speed5,
                         "speed6"=>$fan_speed6,
                           "speed7"=>$fan_speed7,
                           "button"=>$fan_button);
                                                                    

                                          /***********************/
                                      



                                }


                               else if($devicetype_2 == 'light' && $addstatus_2 == 'confirmed')

                                 {
       

									$light_attr_1 = json_encode(["name"=>"Device Id",
																"value"=>$device_id]);

									$light_attr_2 = json_encode(["name"=>"mac",
																"value"=>$mac]);
									$light_attri[] = json_decode($light_attr_1, true);
									$light_attri[] = json_decode($light_attr_2, true);
									$light_att=json_encode($light_attri);
									

									$light_P_1 = json_encode(["name" => "name",
														  "data_type"=> "String",
														 "properties"=> ["read","write"],
															   "type"=>"efactor.param.name"]);

									$light_P_2 = json_encode(["name" => "power",
														  "data_type"=> "bool",
														 "properties"=> ["read","write"],
                                                                        "type"=>"efactor.param.power",
															"ui_type"=>"efactor.ui.toggle"]);

																		$light_p[]= json_decode($light_P_1, true);
									$light_p[]= json_decode($light_P_2, true);
									

									$light_par=json_encode($light_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$light_attri,
															"params"=>$light_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.lightbulb","brand_logo"=>$url]);
									$devices_list[] = json_decode($devices_1, true);


									/********************/

									$devices_para_arr= array("brightness" => intval($brightness),
                                                                       "output" => true,
                                                                        "power"=>$power,
                                                                       "name"=>$name);

									/*******************/
										 
                                  }

                                  else if($devicetype_2 == 'switch' && $addstatus_2 == 'confirmed')

                                   {

									/*-----------------switch--------------*/
									$switch_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$switch_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$switch_attri[] = json_decode($switch_attr_1, true);
									$switch_attri[] = json_decode($switch_attr_2, true);

									$switch_att=json_encode($switch_attri);

									$switch_P_1 = json_encode(["name" => "name",
												 "data_type"=> "String",
												"properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$switch_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$switch_p[]= json_decode($switch_P_1, true);
									$switch_p[]= json_decode($switch_P_2, true);
									$switch_par=json_encode($switch_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$switch_attri,
															"params"=>$switch_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.switch","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


										}




                                              else if($devicetype_2 == 'cooker' && $addstatus_2 == 'confirmed')

                                   {

                                                    

									/*-----------------switch--------------*/
									$cooker_attr_1 = json_encode(["name"=>"Device Id",
											    "value"=>$device_id]);

								$cooker_attr_2 = json_encode(["name"=>"mac",
											     "value"=>$mac]);
								$cooker_attri[] = json_decode($cooker_attr_1, true);
								$cooker_attri[] = json_decode($cooker_attr_2, true);

                                                             









                                                               /*-------------------------------------------------*/
									$cooker_att=json_encode($cooker_attri);

									$cooker_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$cooker_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                                                                    "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

                                                                                 

									$cooker_p[]= json_decode($cooker_P_1, true);
									$cooker_p[]= json_decode($cooker_P_2, true);
                                                                      									$cooker_par=json_encode($cooker_p);



									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$cooker_attri,
                                                                                                                															"params"=>$cooker_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.cooker","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                        "skipmode"=>true,
                                                                        "cookermode"=>$cookermode,
                                                                        
                                                                        "recipe"=>$recipe,
                                                                         "name"=>$name,
                                                                         "remaining time"=>$cooktime,
                                                                        "running temperature"=>$cook_run_temp);

									/***************************/



                                                                     /*----------------------------------*/


														
                                               $devices_config_para = array("Cooker Timer"=>(int)$cooker_timer,
                                                                           "Cooking temperature"=>(int)$cooking_temperature,
                                                                           "Keep warm temperature"=>(int)$Keep_warm,
                                                                           "Pre-timer temp constant mode"=>$Pre_timer_constant_mode,
                                                                           "Pre-timer"=>(int)$Pre_timer,
                                                                           "Pre – temperature"=>(int)$Pre_temperature,
                                                                           "Coooker max-temperature"=>(int)$Coooker_max_temperature,
                                                                           "Cooker temp constant mode"=>(bool)$Cooker_constant_mode,
                                                                           "Keep Warm timer"=>(int)$Keep_Warm_timer,
                                                                           "pre-timer wait temperature constant mode"=>(bool)$pre_timer_wait_temperature_constant_mode,
                                                                           "pre-timer waiting time"=>(int)$pre_timer_waiting_time,
                                                                           "pre-timer Waiting temperature"=>(int)$pre_timer_Waiting_temperature,
                                                                           "Keep warm temp constant mode"=>(bool)$warm_constant_cut_off,
                                                                           "cook wait temp constant mode"=>(int)$cook_wait_temp_constant_mode,
                                                                           "cook waiting time"=>(int)$cook_waiting_time,
                                                                           "cook Waiting- temperature"=>(int)$cook_Waiting_temperature,
                                                                           "warm-wait temp constant mode"=>(bool)$warm_wait_temp_constant_mode,
                                                                           "warm waiting time"=>(int)$warm_waiting_time,
                                                                           "warm Waiting temperature"=>(int)$warm_Waiting_temperature,
                                                                             "OK" => (bool)$cooker_ok_button,
                                                                           "Reload Default" => (bool)$reload_default,
                                                                            "Dishes" => $recipe);




                                                                        


							/*-----------------------------------*/	




										}

                                         /**********scanner***************21-4-23***********************/


                                           else if($devicetype_2 == 'scanner' && $addstatus_2 == 'confirmed')

                                   {

									/*-----------------switch--------------*/
									$scanner_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$scanner_attr_2 = json_encode(["name"=>"mac",
														"value"=>$mac]);
									$scanner_attri[] = json_decode($scanner_attr_1, true);
									$scanner_attri[] = json_decode($scanner_attr_2, true);

									$scanner_att=json_encode($scanner_attri);

									$scanner_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$scanner_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$scanner_p[]= json_decode($scanner_P_1, true);
									$scanner_p[]= json_decode($scanner_P_2, true);
									$scanner_par=json_encode($scanner_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$scanner_attri,
															"params"=>$scanner_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.qr-scanner","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


										}



                                      /*******************************************************/


                                 else if($devicetype_2 == 'outlet' && $addstatus_2 == 'confirmed')

                                   {
                                       //  $outlet_attri = array();

                                                         $outlet_attri = [];
                                                   $outlet_p = [];

									/*-----------------outlet--------------*/
									$outlet_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$outlet_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$outlet_attri[] = json_decode($outlet_attr_1, true);
									$outlet_attri[] = json_decode($outlet_attr_2, true);

									$outlet_att=json_encode($outlet_attri);

									$outlet_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$outlet_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$outlet_p[]= json_decode($outlet_P_1, true);
									$outlet_p[]= json_decode($outlet_P_2, true);
									$outlet_par=json_encode($outlet_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$outlet_attri,
															"params"=>$outlet_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.outlet","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


										}


												
									//$devices=array($devices_gate, $devices_fan, $devices_light, $devices_switch);


									/*******************************/









		



 else if($devicetype_2 == 'other' && $addstatus_2 == 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$other_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$other_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$other_attri[] = json_decode($other_attr_1, true);
									$other_attri[] = json_decode($other_attr_2, true);

									$other_att=json_encode($other_attri);

									$other_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$other_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$other_p[]= json_decode($other_P_1, true);
									$other_p[]= json_decode($other_P_2, true);
									$other_par=json_encode($other_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$other_attri,
															"params"=>$other_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.other","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


										}




    else if($devicetype_2 == 'HBMDNC' && $addstatus_2== 'confirmed')

                                   {


                                                     $hbmdnc_attri = array();
$hbmdnc_p = array();

									/*-----------------outlet--------------*/
									$hbmdnc_attr_1 = json_encode(["name"=>"Device Id",
											        "value"=>$device_id]);

									$hbmdnc_attr_2 = json_encode(["name"=>"mac",
											         "value"=>$mac]);
									$hbmdnc_attri[] = json_decode($hbmdnc_attr_1, true);
									$hbmdnc_attri[] = json_decode($hbmdnc_attr_2, true);

									$hbmdnc_att=json_encode($hbmdnc_attri);

									$hbmdnc_P_1 = json_encode(["name" => "name",
												 "data_type"=> "String",
											"properties"=> ["read","write"],
											"type"=>"efactor.param.name"]);

									
                                                         

									$hbmdnc_p[]= json_decode($hbmdnc_P_1, true);
									
									$hbmdnc_par=json_encode($hbmdnc_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$hbmdnc_attri,
															"params"=>$hbmdnc_p,
														   
															  "type"=>"efactor.device.hbmdnc",
                                                                                                                      "brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("Doctor"=>(bool)$doctormanage,
                                                                                                  "Personal Assist"=>(bool)$personal_assistant,
                                                                                                   "nurse"=>(bool)$nurse,
                                                                                                    "Doctor status"=>$doctormanage_resp,
                                                                                                    "PA status"=>$personal_assistant_resp,
                                                                                                   "Nurse_Status"=>$nurse_resp,
                                                                                                    "name"=>$name);



									/***************************/


										}






















else if($devicetype_2 == 'input-output' && $addstatus_2 == 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$other_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$other_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$other_attri[] = json_decode($other_attr_1, true);
									$other_attri[] = json_decode($other_attr_2, true);

									$other_att=json_encode($other_attri);

									$other_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$other_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$other_p[]= json_decode($other_P_1, true);
									$other_p[]= json_decode($other_P_2, true);
									$other_par=json_encode($other_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$other_attri,
															"params"=>$other_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.input-output","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


										}





                         else if($devicetype_2 == 'exhaust_hood' && $addstatus_2== 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$exh_attr_1 = json_encode(["name"=>"Device Id",
													"value"=>$device_id]);

									$exh_attr_2 = json_encode(["name"=>"mac",
												"value"=>$mac]);
									$exh_attri[] = json_decode($exh_attr_1, true);
									$exh_attri[] = json_decode($exh_attr_2, true);

									$exh_att=json_encode($exh_attri);


                                                                      
									$exh_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$exh_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									 



                                                                      


									$exh_p[]= json_decode($exh_P_1, true);
									$exh_p[]= json_decode($exh_P_2, true);
                                                                       
									
									$exh_par=json_encode($exh_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$exh_attri,
															"params"=>$exh_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.exhaust-hood","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 
                                                                    /*   $devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);*/
									


                                                                        $devices_para_arr = array("power" => $power,
                                                                                                   "output" => true,
                                                                                                   "name" => $name,
                                                                                               "autoclean" =>(bool)$hood_autoclean,
                                                                                                  "airflow" => (int)$hood_speed,
                                                                                                    "lamp" =>(bool)$hood_lamp);


									/***************************/


										}






                                             else if($devicetype_2 == 'swpcp' && $addstatus_2 == 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$wp_attr_1 = json_encode(["name"=>"Device Id",
												"value"=>$device_id]);

									$wp_attr_2 = json_encode(["name"=>"mac",
												"value"=>$mac]);
									$wp_attri[] = json_decode($wp_attr_1, true);
									$wp_attri[] = json_decode($wp_attr_2, true);

									$wp_att=json_encode($wp_attri);

                                                                      








                                                                    


                                                                     $wp_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$wp_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                                                               "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

                                                                      

                                                                         
                
									$wp_p[]= json_decode($wp_P_1, true);
									$wp_p[]= json_decode($wp_P_2, true);
                                                                      									//$wp_par=json_encode($wp_p);



                        


                       


									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$wp_attri,
                                                                                                            															"params"=>$wp_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.swpcp","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name,
                                                                        "Timer"=>$timer,
                                                                        "current(A)"=>$current,
                                                                        "voltage(V)"=>$voltage,
                                                                        "remaining time"=>$rem_time,
                                                                        "total watt"=>$watt);


									/***************************/


                                                                         $devices_config_para = array("starter relay off timer"=>$starter_relay_off_timer,
                        "start high current cutoff"=>$start_high_current_cutoff,
                        "start low current cutoff"=>$start_low_current_cutoff,
                        "Run high current cutoff"=>$Run_high_current_cutoff,
                        "Run low current cutoff"=>$Run_low_current_cutoff,
                        "high voltage cutoff"=>$high_voltage_cutoff,
                        "low voltage cutoff"=>$low_voltage_cutoff,
                        "ultra high voltage cutoff"=>$ultra_high_voltage_cutoff,
                        "ultra high current cutoff"=>$ultra_high_current_cutoff,
                        "USER SET MAX CURRENT CUTOFF LIMIT"=>$USER_SET_MAX_CURRENT_CUTOFF_LIMIT,
                        "USER AND DEVICE MAX CURRENT LIMIT"=>$USER_AND_DEVICE_MAX_CURRENT_LIMIT,
                        "LOCK ENABLE/DISABLE"=>$LOCK_ENABLE_DISABLE);
                                                                        
                                                                    /*********************************/


										}


		
										
									









}			

                   



     }       









else 
{

//echo 'else else';
			$query_2 = "SELECT * FROM efactorproduct where access_token='$val' AND is_added='1'";
			$row = $db->get_row($query_2);
			$is_added=$row['is_added'];
                      //  $num_gate = $db->num_rows($query_2);


/*$endTime_1 = microtime(true);

$executionTime_1 = $endTime_1 - $startTime;

echo $executionTime_1;*/




            
             


			if($is_added=='1')
			{



					$device_id=$row['node_id'];   
					$query_4 = "SELECT * FROM `efactorproduct` WHERE access_token='$val' AND is_added='1'";
					$doctors = $db->get_results($query_4); 
					$num_4 = $db->num_rows($query_4);
                                        $num_gate = $db->num_rows($query_4);

					$row_4 = $db->get_row($query_4); 
					$node_id=$row_4['node_id']; 
                                        $address=$row_4['node_id']; 
                                        
                                        
                                        $device_gateway=$row_4['device_type'];
                                        $moduletype= $row_4['module_type']; 
                                         
                                        $model_number=$row_4['model_number'];  
                                        $vendor_name=$row_4['vendor_name'];  
                                        $devicename=$row_4['device_name'];
                                        $access_token=$row_4['access_token']; 

                                           if($device_type_5 == 'exhaust_hood')
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





                                                                             
					$arr=array();
				  
					$query_5 = "SELECT node_id FROM `efactorproduct` WHERE access_token='$val' AND add_gateway='$node_id'";
					$doctors = $db->get_results($query_5); 
					$num_5 = $db->num_rows($query_5);
					$row_5 = $db->get_row($query_5); 
                                        $access_token_2=$row_5['access_token'];
                                        //$device_type_5=$row_6['device_type'];

                                         //echo $access_token;
                                        // echo $access_token_2;



                                        /**********************************/


						foreach ($doctors as $key => $doctor) 
						 {
						   array_push($arr,array("id"=>$doctor['id'],"node_id"=>$doctor['node_id'],"device_type"=>$doctor['device_type'],"name"=>$doctor['name']));
							
 						 }

					    for ($pos = 0; $pos <= $num_5; $pos++) 
						  {   
					
							

							   $link_dev =$arr[$pos]['node_id'];
                                                                                                                    
							   $query_6 = "SELECT * FROM `efactorproduct` WHERE access_token='$val' AND node_id='$link_dev'";
							   //$doctors = $db->get_results($query_6); 
							   $num_6 = $db->num_rows($query_6);
							   $row_6 = $db->get_row($query_6); 
							   $device_type_5=$row_6['device_type'];
                                                           $devicetype=$row_6['device_type'];
                                                           $addstatus_5=$row_6['req_status'];
                                                           $linkcommmode=$row_6['link_comm_mode'];
					                   $linkdevices=$row_6['link_devices'];
							   // $link_name=$row_6['name'];   


                                                           $model_number=$row_6['model_number'];
                                                           $devicename=$row_6['device_name'];
                                                           $vendor_name=$row_6['vendor_name'];
                                                           $moduletype=$row_6['module_type'];
                                                           $net_conn_1=$row_6['internet']; 
                                                        //   $access_token=$row_6['access_token'];


                                                                 if($device_type_5 == 'exhaust_hood')
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



                                           
                                                      //    echo $access_token;
                                      /**************************/
                                                    //$moduletype= $row_6['module_type'];
					             $name=$row_6['name'];
                                                    $mac=$row_6['mac'];
					             $speed=$row_6['speed'];
					             $hue=$row_6['Hue'];
					             $direction=$row_6['direction'];
					             //$power=$row_6['power'];
					             $baudrate=$row_6['baudrate'];
					             //$led=$row_6['led_indication'];
					             $power=filter_var($row_6['power'], FILTER_VALIDATE_BOOLEAN);
					             $led = filter_var($row_6['led_indication'], FILTER_VALIDATE_BOOLEAN);
                                           
                                           $devicename=$row['device_name']; 
                                            $curr_devicetype=$row_6['current_devicetype'];
                                             $recipe=$row_6['recipe'];
                                              $user_priority=$row_6['user_priority'];
                                             $cookermode=$row_6['cookermode'];
                                             $cooker_led=$row_6['cooker_led'];
                                             $cooktime=$row_6['cook_time'];
                                             $cook_run_temp=$row_6['cook_run_temp'];
                                            $current=$row_6['current'];
                                             $voltage=$row_6['ac_float'];
                                             $bus_voltage=$row_6['bus_voltage'];
                                             $rpm=$row_6['rpm'];
                                             $timer=$row['timer'];
                                              $watt=$row_6['power_watt'];
                                             $rem_time=$row_6['rem_time'];
                                              $hood_autoclean=$row_6['hood_autoclean'];
                                              $hood_speed=$row_6['hood_speed'];
                                              $hood_lamp=$row_6['hood_lamp'];

                                          /*   $nurse=$row_6['nurse'];
                                              $personal_assistant=$row_6['personal_assistant'];
                                              $doctormanage=$row_6['doctor'];
                                              $nurse_resp=$row_6['nurse_resp'];
                                              $personal_assistant_resp=$row_6['personal_assistant_resp'];
                                              $doctormanage_resp=$row_6['doctor_resp'];*/

                                             $nurse=filter_var($row_6['nurse'], FILTER_VALIDATE_BOOLEAN);
$personal_assistant=filter_var($row_6['personal_assistant'], FILTER_VALIDATE_BOOLEAN);
$doctormanage=filter_var($row_6['doctor'], FILTER_VALIDATE_BOOLEAN);
$nurse_resp=$row_6['nurse_resp'];      
$personal_assistant_resp=$row_6['personal_assistant_resp'];                    
$doctormanage_resp=$row_6['doctor_resp'];                      







                                               
                                         if($device_type_5 == 'swpcp')
                                             {

                                            //swpcp
                                             $starter_relay_off_timer = $row_6['starter_relay_off_timer'];  
$start_high_current_cutoff = $row_6['start_high_current_cutoff'];  
$start_low_current_cutoff= $row_6['start_low_current_cutoff'];  
$Run_high_current_cutoff = $row_6['Run_high_current_cutoff'];  
$Run_low_current_cutoff = $row_6['Run_low_current_cutoff'];  
$high_voltage_cutoff = $row_6['high_voltage_cutoff'];  
$low_voltage_cutoff = $row_6['low_voltage_cutoff'];  
$ultra_high_voltage_cutoff = $row_6['ultra_high_voltage_cutoff'];  
$ultra_high_current_cutoff = $row_6['ultra_high_current_cutoff'];  
$USER_SET_MAX_CURRENT_CUTOFF_LIMIT = $row_6['USER_SET_MAX_CURRENT_CUTOFF_LIMIT'];  
$USER_AND_DEVICE_MAX_CURRENT_LIMIT = $row_6['USER_AND_DEVICE_MAX_CURRENT_LIMIT'];  
$LOCK_ENABLE_DISABLE = (bool)$row_6['LOCK_ENABLE_DISABLE'];  

}



if($device_type_5 == 'cooker')
                                              {
                                           //cooker

                                            $cooker_timer = $row_6['cooker_timer_1'];  
                                            $cooking_temperature = $row_6['cooker_max_temperature_1'];   
                                            $Keep_warm= $row_6['Keep_warm_1'];  
                                            $Pre_timer_constant_mode = $row_6['Pre_timer_constant_mode'];  
                                            $Pre_timer = $row_6['Pre_timer'];  
                                            $Pre_temperature = $row_6['Pre_temperature'];  
                                            $Coooker_max_temperature = $row_6['Coooker_max_temperature'];  
                                            $Cooker_constant_mode = $row_6['Cooker_constant_mode'];  
                                            $Keep_Warm_timer = $row_6['Keep_Warm_timer_1'];  
                                            $Dishes = $row_6['dish'];  
                                            $pre_timer_wait_temperature_constant_mode = $row_6['pre_timer_wait_temperature_constant_mode'];  
                                            $pre_timer_waiting_time = $row_6['pre_timer_waiting_time'];
                                            $pre_timer_Waiting_temperature = $row_6['pre_timer_Waiting_temperature'];  
                                            $warm_constant_cut_off = $row_6['warm_constant_cut_off'];  
                                            $cook_wait_temp_constant_mode = $row_6['cook_wait_temp_constant_mode'];  
                                            $cook_waiting_time = $row_6['cook_waiting_time'];  
                                            $cook_Waiting_temperature = $row_6['cook_Waiting_temperature'];  
                                            $warm_wait_temp_constant_mode = $row_6['warm_wait_temp_constant_mode'];  
                                            $warm_waiting_time = $row_6['warm_waiting_time'];  
                                            $warm_Waiting_temperature = $row_6['warm_Waiting_temperature'];  
                                            $cooker_ok_button = $row_6['cooker_ok_button']; 
                                            $reload_default = $row_6['reload_default'];


                                         }



                //   echo 'echo 3';



                                            if($device_type_5 == 'fan')
                                             {
                                        //fan
                                            $fan_resistance = $row_6['fan_resistance'];  
                                            $fan_lmin = $row_6['fan_lmin'];  
                                            $fan_lmax= $row_6['fan_lmax'];  
                                            $fan_frequency = $row_6['fan_frequency'];  
                                            $fan_pole_pairs = $row_6['fan_pairs'];  
                                            $fan_open_loop_current = $row_6['fan_open_loop_current'];  
                                            $fan_power_limit = $row_6['fan_power_limit'];  
                                            $fan_torque_limit = $row_6['fan_torque_limit'];  
                                            $fan_speed1 = $row_6['fan_speed1'];  
                                            $fan_speed2= $row_6['fan_speed2'];
                                            $fan_speed3 = $row_6['fan_speed3'];  
                                            $fan_speed4= $row_6['fan_speed4'];
                                            $fan_speed5 = $row_6['fan_speed5'];  
                                            $fan_speed6= $row_6['fan_speed6'];
                                            $fan_speed7 = $row_6['fan_speed7'];  
                                            $fan_button= $row_6['fan_button'];
                                            }
                                          
                                       $dev_paras=array($name=>$device_type_5);

                                       $tz_properties=$row_6['TZ_properties'];
                                       $br_properties=$row_6['BR_properties'];
                                       $umi_properties=$row_6['UMI_properties'];
                                       $ec_properties=$row_6['EC_properties'];
                                       $cm_properties=$row_6['CM_properties'];
                                       $tu_properties=$row_6['TU_properties'];
                                       $remote_pair_properties=$row_6['remote_pair_properties'];
                                       $display_properties=$row_6['display_properties'];
                                       $swpcp_1ph_properties=$row_6['swpcp_1ph_properties'];
                                       $fan_properties=$row_6['fan_properties'];
                                       $lc_properties=$row_6['LC_properties'];
                                       $cooker_properties=$row_6['cooker_properties'];
                                       



                                     $baudrate=$row_6['baudrate'];
                                     $uartmsgid=$row_6['uartmsgid'];
                                     $time=$row_6['time'];
                                     $errorcode=$row_6['errorcode'];
                                     $comm_mode=$row_6['comm_mode'];


                                /*     $model_number=$row_6['model_number'];
                                     $vendor_name=$row_6['vendor_name'];
                                     $moduletype= $row_6['module_type']; 
                                     $model_number=$row_6['model_number'];  
                                     $vendor_name=$row_6['vendor_name'];  
                                     $devicename=$row_6['device_name'];*/


/*if ($device_type_5 != 'Gateway') {
    $device_id = $link_dev;
}*/






                                     
                                        

                                           
                                              $chunks= str_split($linkdevices, 32);


                                         for ($i = 0; $i < count($chunks); $i++) {
                                                  ${"chunk" . ($i+1)} = $chunks[$i];
                                               $size = count($chunks);
                                              

                                               $query_7 = "SELECT * FROM `efactorproduct` WHERE access_token='$val' AND node_id='$chunks[$i]'";
							   $doctors = $db->get_results($query_7); 
							   $num_7 = $db->num_rows($query_7);
							   $row_7 = $db->get_row($query_7);

                                              $linkname[$i]=$row_7['name'];

                                             
                                              //  echo $linkname;
                          
                                                  }

                                                 
                          
                                      
								
                                      /*************************/

								if($device_type_5 == 'Gateway' && $addstatus_5== 'confirmed')
								 {
                                          
                                   
                                                $gate_attr_1 = json_encode(["name"=>"Device Id",
														  "value"=>$device_id]);

								$gate_attr_2 = json_encode(["name"=>"mac",
														  "value"=>$mac]);
								$gate_attri[] = json_decode($gate_attr_1, true);
								$gate_attri[] = json_decode($gate_attr_2, true);

								$gate_att=json_encode($fan_attri);



                                /*********************************/






									$G_Params = json_encode(["name" => "name",
												 "type"=> "efactor.param.name",
											    "data_type"=>"String",
											  "properties"=> ["read","write"]]);

                                           $G_Params_2=json_encode(["name"=> "power",
                                                             "data_type"=> "bool",
                                                             "properties"=>["read","write"],
                                                            "type"=>"efactor.param.power",
                                                             "ui_type"=>"efactor.ui.toggle"]);


									$G_p_1[] = json_decode($G_Params, true);
                                                                       $G_p_1[] = json_decode($G_Params_2, true);

									$Gate_Dev= json_encode([  "name" => $curr_devicetype,
                                                                       "attributes"=>$gate_attri,
															   "type"=> "efactor.device.gateway",
															 "params"=> $G_p_1]);

								    $devices_list[] = json_decode($Gate_Dev, true);

                                                                   $device_para_gateway_1 = array("power"=> $power,
                                                                        "output" => true,
                                                                         "name"=>$name);

                                           $device_para_gateway_3 = array($curr_devicetype => $device_para_gateway_1);
                                                $device_para[$pos]=$device_para_gateway_3;

								}


                                if($device_type_5 == 'fan' && $addstatus_5== 'confirmed')


                                 {


                               

                                        

                                           unset($fan_attri);
                                           unset($fan_p);
                                          // unset($device_para_fan);
                                           unset($device_para_fan_1);
                                          // unset($device_para_fan_3)

								 /*  $fan_attri[] = array( "name"=>"device_id",
									                         "value"=>$link_dev);

								   $fan_attri[] = array("name"=>"mac",
												      "value"=>$mac);

                                                  if($linkcommmode != 'NULL')
                                                   {
                                                  
                                               //   $fan_attr= json_encode(["name"=>$linkcommmode,
								//           "value"=>$linkname]);
                                           $linkdevices_1 = array("fan1", "fan2", "fan3");

                                                foreach ($linkname as $device) {
                                               
                                                      //$data[] = array("name" => $linkcommmode, "value" => $linkname);
                                                  $fan_attri[]= array("name"=>$linkcommmode, "value"=>$linkname);
                                                        }
                                                  

                                                      //   }
                                                    }*/
                                                   /*$fan_attr_3= json_encode(["name"=>'fan',
								"value"=>'Destination Enable']);*/
                                                    
								 //  $fan_attri[]= json_decode($fan_attr_1, true);
								  // $fan_attri[] = json_decode($fan_attr_2, true);
                                                /*   if($linkcommmode != 'NULL')
                                                   { 
                                                  
                                                   $fan_attri[] = json_decode($fan_attr, true);
                                                    }*/

                                             /***********************************/



$fan_attri = array();


$fan_attri[] = array(
    "name" => "Device Id",
    "value" => $link_dev
);


$fan_attri[] = array(
    "name" => "mac",
    "value" => $mac
);


foreach ($linkname as $linkDevice) {
    $fan_attri[] = array(
        "name" => $linkcommmode,
        "value" => $linkDevice
    );
}


//$json = json_encode(array("attributes" => $fan_attri), JSON_PRETTY_PRINT);
//echo $json;




                                            /***********************************/

								   $fan_att=json_encode($fan_attri);


 





                                                                     
								   $fan_P_1 = json_encode(["name" => "name",
														  "data_type"=> "String",
														 "properties"=>["read","write"],
															   "type"=> "efactor.param.name"]);


								  $fan_P_2 = json_encode(["name" => "power",
													  "data_type"=> "bool",
													 "properties"=> ["read","write"],
														   "type"=>"efactor.param.power",
                                                                    "type"=>"efactor.param.power",
														"ui_type"=>"efactor.ui.toggle"]);

								                                                                            



                                                                  if($model_number == "EFACT V1.2")
                                                                      {

								  $fan_p[]= json_decode($fan_P_1, true);
								  $fan_p[]= json_decode($fan_P_2, true);
                                                                  								  
                                                                    }



                                                                else{
                                                                  $fan_p[]= json_decode($fan_P_1, true);
								  $fan_p[]= json_decode($fan_P_2, true);
                                                              


                                                                   }



								  

								  $fan_par=json_encode($fan_p);





								     
									
                                                                      if($model_number == "EFACT V1.2")
                                                                      {
									$devices_1 = json_encode(["name"=>$curr_devicetype,
												"attributes"=>$fan_attri,
                                                                                               
												"params"=>$fan_p,
												"primary"=>"power",
											       "type"=>"efactor.device.fan","brand_logo"=>$url]);

                                                                       }
                                                                       else{

                                                                            $devices_1 = json_encode(["name"=>$curr_devicetype,
												"attributes"=>$fan_attri,           
												"params"=>$fan_p,
												"primary"=>"power",
											       "type"=>"efactor.device.fan","brand_logo"=>$url]);

                                                                      }




                                            $devices_list[] = json_decode($devices_1, true);


									/*****************/

                                                                if($power == '0')
                                                                           {
                                                                             $led = $power;
                                                                             }

								

                                    /*   $device_para_array_1 = array("Direction"=>intval($direction),
					                             "led colour"=>intval($hue),
							                 "output"=>true,
									"power"=>$power,
									"Speed"=>intval($speed),
									"led"=>$led,
									"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                         "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm,
                                                                         "buzzer"=>(bool)$fan_buzzer);*/


                                           if($model_number == "EFACT V1.2")
                                                                      {
                                                    $device_para_array_1 = array("Direction"=>intval($direction),
					                             "led colour"=>intval($hue),
							                 "output"=>true,
									"power"=>$power,
									"Speed"=>intval($speed),
									"led"=>$led,
									"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                         "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm,
                                                                         "buzzer"=>(bool)$fan_buzzer);
                                                                        

                                                                  }

                                                 else{

                                       $device_para_array_1 = array("Direction"=>intval($direction),
					                             "led colour"=>intval($hue),
							                 "output"=>true,
									"power"=>$power,
									"Speed"=>intval($speed),
									"led"=>$led,
									"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                         "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm);

                                                 }
                                             








                                                             $devices_config_para = array("resistance"=>$fan_resistance,
                        "lmin"=>$fan_lmin,
                        "lmax"=>$fan_lmax,
                        "frequency"=>$fan_frequency,
                        "pole pairs"=>$fan_pole_pairs,
                        "open loop current"=>$fan_open_loop_current,
                        "power limit"=>$fan_power_limit,
                        "torque limit"=>$fan_torque_limit,
                        "speed1"=>$fan_speed1,
                        "speed2"=>$fan_speed2,
                        "speed3"=>$fan_speed3,
                        "speed4"=>$fan_speed4,
                         "speed5"=>$fan_speed5,
                         "speed6"=>$fan_speed6,
                           "speed7"=>$fan_speed7,
                           "button"=>$fan_button);
                                                                    


                                             
                                             

                                           $device_para[$pos]= array($curr_devicetype=>$device_para_array_1);    //14-8
                                          
                                          
                                                

                                           
                                         

                        }


                             else if($device_type_5 == 'light' && $addstatus_5== 'confirmed')

                              {
                                            

								$light_attr_1 = json_encode(["name"=>"Device Id",
															"value"=>$link_dev]);

								$light_attr_2 = json_encode(["name"=>"mac",
															"value"=>$mac]);
								$light_attri[] = json_decode($light_attr_1, true);
								$light_attri[] = json_decode($light_attr_2, true);
								$light_att=json_encode($light_attri);
								

								$light_P_1 = json_encode(["name" => "name",
													  "data_type"=> "String",
													 "properties"=> ["read","write"],
														   "type"=>"efactor.param.name"]);

								$light_P_2 = json_encode(["name" => "power",
													  "data_type"=> "bool",
													 "properties"=> ["read","write"],
                                                                  "type"=>"efactor.param.power",
														"ui_type"=>"efactor.ui.toggle"]);

								$max_1 = array("max"=>100,
											   "min" =>0);
								//$max2 = json_encode($max_1);

								

								$light_p[]= json_decode($light_P_1, true);
								$light_p[]= json_decode($light_P_2, true);
								

						

								$light_par=json_encode($light_p);

								$devices_1 = json_encode(["name"=>$curr_devicetype,
													"attributes"=>$light_attri,
														"params"=>$light_p,
													   "primary"=>"power",
														  "type"=>"efactor.device.lightbulb","brand_logo"=>$url]);
								$devices_list[] = json_decode($devices_1, true);

								
								/********************/
                                       

								$device_para_light_1= array("brightness" => intval($brightness),
                                                                       "output" => true,
                                                                      "power"=>$power,
                                                                       "name"=>$name);
                                             

                                           $device_para[$pos] = array($curr_devicetype => $device_para_light_1);
                                           // $device_para_light[]=$device_para_light_3;
                                           // $device_para_arr[] = json_decode($device_para_light_2, true);


				/*******************/
     
                              }

                             else if($device_type_5 == 'switch' && $addstatus_5== 'confirmed')

                              {
                                      
								/*-----------------switch-----------------*/

								$switch_attr_1 = json_encode(["name"=>"Device Id",
															 "value"=>$link_dev]);

								$switch_attr_2 = json_encode(["name"=>"mac",
															 "value"=>$mac]);
								$switch_attri[] = json_decode($switch_attr_1, true);
								$switch_attri[] = json_decode($switch_attr_2, true);

								$switch_att=json_encode($switch_attri);

								$switch_P_1 = json_encode(["name" => "name",
													   "data_type"=> "String",
													  "properties"=> ["read","write"],
															"type"=>"efactor.param.name"]);

								$switch_P_2 = json_encode(["name" => "power",
													   "data_type"=> "bool",
													  "properties"=> ["read","write"],
                                                                   "type"=>"efactor.param.power",
														 "ui_type"=>"efactor.ui.toggle"]);

								$switch_p[]= json_decode($switch_P_1, true);
								$switch_p[]= json_decode($switch_P_2, true);
								$switch_par=json_encode($switch_p);

								$devices_1 = json_encode(["name"=>$curr_devicetype,
													"attributes"=>$switch_attri,
														"params"=>$switch_p,
													   "primary"=>"power",
														  "type"=>"efactor.device.switch","brand_logo"=>$url]);

								$devices_list[] = json_decode($devices_1, true);


								

					/****************************/ 

								//$device_para_switch = array("power"=> true,
                                                                //                               "output" => true);


                                          $device_para_switch_1 = array("power"=> $power,
                                                                        "output" => true,
                                                                         "name"=>$name);

                                           $device_para_switch_3 = array($curr_devicetype => $device_para_switch_1);
                                                $device_para[$pos]=$device_para_switch_3;

                                         // $device_para_switch[] = json_decode($device_para_switch_2, true);

					/***************************/


 
	                          }
   

                                  else if($device_type_5 == 'cooker' && $addstatus_5== 'confirmed')

                              {
                                                                                               
								/*-----------------cooker-----------------*/

								$cooker_attr_1 = json_encode(["name"=>"Device Id",
											    "value"=>$link_dev]);

								$cooker_attr_2 = json_encode(["name"=>"mac",
											     "value"=>$mac]);
								$cooker_attri[] = json_decode($cooker_attr_1, true);
								$cooker_attri[] = json_decode($cooker_attr_2, true);


								$cooker_att=json_encode($cooker_attri);


/*------------------------------------------------*/
                                                                    


                                                                     












								$cooker_P_1 = json_encode(["name" => "name",
													   "data_type"=> "String",
													  "properties"=> ["read","write"],
													"type"=>"efactor.param.name"]);

								$cooker_P_2 = json_encode(["name" => "power",
											 "data_type"=> "bool",
											 "properties"=> ["read","write"],
                                                                                             "type"=>"efactor.param.power",
											 "ui_type"=>"efactor.ui.toggle"]);

								         

									$cooker_p[]= json_decode($cooker_P_1, true);
									$cooker_p[]= json_decode($cooker_P_2, true);
                                                                      
									$cooker_par=json_encode($cooker_p);







								$devices_1 = json_encode(["name"=>$curr_devicetype,
													"attributes"=>$cooker_attri,
                                                                                                         														"params"=>$cooker_p,
													   "primary"=>"power",
														  "type"=>"efactor.device.cooker","brand_logo"=>$url]);

								$devices_list[] = json_decode($devices_1, true);


								

								/****************************/ 

								//$device_para_switch = array("power"=> true,
                                         //                               "output" => true);


                                          $device_para_cooker_1 = array("power"=> $power,
                                                                        "output" => true,
                                                                        "skipmode"=>true,
                                                                         "cookermode"=>$cookermode,
                                                                         
                                                                         "recipe"=>$recipe,
                                                                         "name"=>$name,
                                                                      "remaining time"=>$cooktime,
                                                                        "running temperature"=>$cook_run_temp);

                                           $device_para_cooker_3 = array($curr_devicetype => $device_para_cooker_1);
                                                $device_para[$pos]=$device_para_cooker_3;

                                         // $device_para_switch[] = json_decode($device_para_switch_2, true);

								/***************************/

                                           /*----------------------------------*/


														
                                             $devices_config_para = array("Cooker Timer"=>(int)$cooker_timer,
                                                                           "Cooking temperature"=>(int)$cooking_temperature,
                                                                           "Keep warm temperature"=>(int)$Keep_warm,
                                                                           "Pre-timer temp constant mode"=>$Pre_timer_constant_mode,
                                                                           "Pre-timer"=>(int)$Pre_timer,
                                                                           "Pre – temperature"=>(int)$Pre_temperature,
                                                                           "Coooker max-temperature"=>(int)$Coooker_max_temperature,
                                                                           "Cooker temp constant mode"=>(bool)$Cooker_constant_mode,
                                                                           "Keep Warm timer"=>(int)$Keep_Warm_timer,
                                                                           "pre-timer wait temperature constant mode"=>(bool)$pre_timer_wait_temperature_constant_mode,
                                                                           "pre-timer waiting time"=>(int)$pre_timer_waiting_time,
                                                                           "pre-timer Waiting temperature"=>(int)$pre_timer_Waiting_temperature,
                                                                           "Keep warm temp constant mode"=>(bool)$warm_constant_cut_off,
                                                                           "cook wait temp constant mode"=>(int)$cook_wait_temp_constant_mode,
                                                                           "cook waiting time"=>(int)$cook_waiting_time,
                                                                           "cook Waiting- temperature"=>(int)$cook_Waiting_temperature,
                                                                           "warm-wait temp constant mode"=>(bool)$warm_wait_temp_constant_mode,
                                                                           "warm waiting time"=>(int)$warm_waiting_time,
                                                                           "warm Waiting temperature"=>(int)$warm_Waiting_temperature,
                                                                             "OK" => (bool)$cooker_ok_button,
                                                                           "Reload Default" => (bool)$reload_default,
                                                                            "Dishes" => $recipe);




                                                                        


							/*-----------------------------------*/	





 
	                          }

                                      

                                   /**********scanner***************21-4-23***********************/


                                           else if($device_type_5 == 'scanner' && $addstatus_5== 'confirmed')

                              {
                                      
								/*-----------------cooker-----------------*/

								$scanner_attr_1 = json_encode(["name"=>"Device Id",
															 "value"=>$link_dev]);

								$scanner_attr_2 = json_encode(["name"=>"mac",
															 "value"=>$mac]);
								$scanner_attri[] = json_decode($scanner_attr_1, true);
								$scanner_attri[] = json_decode($scanner_attr_2, true);

								$scanner_att=json_encode($scanner_attri);

								$scanner_P_1 = json_encode(["name" => "name",
													   "data_type"=> "String",
													  "properties"=> ["read","write"],
													"type"=>"efactor.param.name"]);

								$scanner_P_2 = json_encode(["name" => "power",
											 "data_type"=> "bool",
											 "properties"=> ["read","write"],
                                                                                             "type"=>"efactor.param.power",
											 "ui_type"=>"efactor.ui.toggle"]);

								$scanner_p[]= json_decode($scanner_P_1, true);
								$scanner_p[]= json_decode($scanner_P_2, true);
								$scanner_par=json_encode($scanner_p);

								$devices_1 = json_encode(["name"=>$curr_devicetype,
													"attributes"=>$scanner_attri,
														"params"=>$scanner_p,
													   "primary"=>"power",
														  "type"=>"efactor.device.qr-scanner","brand_logo"=>$url]);

								$devices_list[] = json_decode($devices_1, true);


								

								/****************************/ 

								//$device_para_switch = array("power"=> true,
                                         //                               "output" => true);


                                          $device_para_scanner_1 = array("power"=> $power,
                                                                        "output" => true,
                                                                         "name"=>$name);

                                           $device_para_scanner_3 = array($curr_devicetype => $device_para_scanner_1);
                                                $device_para[$pos]=$device_para_scanner_3;

                                         // $device_para_switch[] = json_decode($device_para_switch_2, true);

								/***************************/


 
	                          }










                             

                             else if($device_type_5 == 'outlet' && $addstatus_5== 'confirmed')

                              {
                                       //    $outlet_attri = array();

                                                     $outlet_attri = [];
                                                   $outlet_p = [];
                                      
								/*-----------------outlet-----------------*/

								$outlet_attr_1 = json_encode(["name"=>"Device Id",
															 "value"=>$link_dev]);

								$outlet_attr_2 = json_encode(["name"=>"mac",
															 "value"=>$mac]);
								$outlet_attri[] = json_decode($outlet_attr_1, true);
								$outlet_attri[] = json_decode($outlet_attr_2, true);

								$outlet_att=json_encode($outlet_attri);

								$outlet_P_1 = json_encode(["name" => "name",
													   "data_type"=> "String",
													  "properties"=> ["read","write"],
															"type"=>"efactor.param.name"]);

								$outlet_P_2 = json_encode(["name" => "power",
													   "data_type"=> "bool",
													  "properties"=> ["read","write"],
                                                                   "type"=>"efactor.param.power",
														 "ui_type"=>"efactor.ui.toggle"]);

								$outlet_p[]= json_decode($outlet_P_1, true);
								$outlet_p[]= json_decode($outlet_P_2, true);
								$outlet_par=json_encode($outlet_p);

								$devices_1 = json_encode(["name"=>$curr_devicetype,
													"attributes"=>$outlet_attri,
														"params"=>$outlet_p,
													   "primary"=>"power",
														  "type"=>"efactor.device.outlet","brand_logo"=>$url]);

								$devices_list[] = json_decode($devices_1, true);


								

								/****************************/ 

								


                                          $device_para_outlet_1 = array("power"=> $power,
                                                                        "output" => true,
                                                                         "name"=>$name);

                                           $device_para_outlet_3 = array($curr_devicetype => $device_para_outlet_1);
                                                $device_para[$pos]=$device_para_outlet_3;

                                         // $device_para_switch[] = json_decode($device_para_switch_2, true);

								/***************************/


 
	                          }


else if($device_type_5 == 'other' && $addstatus_5== 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$other_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$link_dev]);

									$other_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$other_attri[] = json_decode($other_attr_1, true);
									$other_attri[] = json_decode($other_attr_2, true);

									$other_att=json_encode($other_attri);

									$other_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$other_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$other_p[]= json_decode($other_P_1, true);
									$other_p[]= json_decode($other_P_2, true);
									$other_par=json_encode($other_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$other_attri,
															"params"=>$other_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.other","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


										}


                                  else if($device_type_5 == 'HBMDNC' && $addstatus_5== 'confirmed')

                                   {



                                                                    $hbmdnc_attri = array();
                                                                    $hbmdnc_p = array();

									/*-----------------outlet--------------*/
									$hbmdnc_attr_1 = json_encode(["name"=>"Device Id",
											        "value"=>$link_dev]);

									$hbmdnc_attr_2 = json_encode(["name"=>"mac",
											         "value"=>$mac]);
									$hbmdnc_attri[] = json_decode($hbmdnc_attr_1, true);
									$hbmdnc_attri[] = json_decode($hbmdnc_attr_2, true);

									$hbmdnc_att=json_encode($hbmdnc_attri);

									$hbmdnc_P_1 = json_encode(["name" => "name",
												 "data_type"=> "String",
											"properties"=> ["read","write"],
											"type"=>"efactor.param.name"]);

									
                                                         

									$hbmdnc_p[]= json_decode($hbmdnc_P_1, true);
									

									$hbmdnc_par=json_encode($hbmdnc_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$hbmdnc_attri,
															"params"=>$hbmdnc_p,
														   
															  "type"=>"efactor.device.hbmdnc",
                                                                                                                      "brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 
									$devices_para_arr = array("Doctor"=>(bool)$doctormanage,
                                                                                                  "Personal Assist"=>(bool)$personal_assistant,
                                                                                                   "nurse"=>(bool)$nurse,
                                                                                                    "Doctor status"=>$doctormanage_resp,
                                                                                                    "PA status"=>$personal_assistant_resp,
                                                                                                   "Nurse_Status"=>$nurse_resp,
                                                                                                    "name"=>$name);




									/***************************/



                                                                     
                                           $device_para_hbmdnc_1 = array($curr_devicetype => $devices_para_arr);
                                                $device_para[$pos]=$device_para_hbmdnc_1;

                                         







										}













else if($device_type_5 == 'input-output' && $addstatus_5== 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$other_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$link_dev]);

									$other_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$other_attri[] = json_decode($other_attr_1, true);
									$other_attri[] = json_decode($other_attr_2, true);

									$other_att=json_encode($other_attri);

									$other_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$other_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$other_p[]= json_decode($other_P_1, true);
									$other_p[]= json_decode($other_P_2, true);
									$other_par=json_encode($other_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$other_attri,
															"params"=>$other_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.input-output","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


										}




                                         else if($device_type_5 == 'exhaust_hood' && $addstatus_5== 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$exh_attr_1 = json_encode(["name"=>"Device Id",
													"value"=>$link_dev]);

									$exh_attr_2 = json_encode(["name"=>"mac",
												"value"=>$mac]);
									$exh_attri[] = json_decode($exh_attr_1, true);
									$exh_attri[] = json_decode($exh_attr_2, true);

									$exh_att=json_encode($exh_attri);
                                                                        
									$exh_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$exh_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									



                                                                       

									$exh_p[]= json_decode($exh_P_1, true);
									$exh_p[]= json_decode($exh_P_2, true);
                                                                       
																		$exh_par=json_encode($exh_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$exh_attri,
															"params"=>$exh_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.exhaust-hood","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									/* $devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);*/



                                                                     $devices_para_arr = array("power" => $power,
                                                                                                   "output" => true,
                                                                                                   "name" => $name,
                                                                                               "autoclean" =>(bool)$hood_autoclean,
                                                                                                  "airflow" => (int)$hood_speed,
                                                                                                    "lamp" =>(bool)$hood_lamp);


									/***************************/


										}




                                                        else if($device_type_5 == 'swpcp' && $addstatus_5== 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$wp_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$link_dev]);

									$wp_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$wp_attri[] = json_decode($wp_attr_1, true);
									$wp_attri[] = json_decode($wp_attr_2, true);

									$wp_att=json_encode($wp_attri);

                                                                    







									$wp_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$wp_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                                                               "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);



                                                                     
                                                                         
                                                                          
                
									$wp_p[]= json_decode($wp_P_1, true);
									$wp_p[]= json_decode($wp_P_2, true);
                                                                       									$wp_par=json_encode($wp_p);


									$devices_1 = json_encode(["name"=>$curr_devicetype,
												  "attributes"=>$wp_attri,
                                                                                                
												  "params"=>$wp_p,
											           "primary"=>"power",
															  "type"=>"efactor.device.swpcp","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name,
                                                                         "Timer"=>$timer,
                                                                        "current(A)"=>$current,
                                                                        "voltage(V)"=>$voltage,
                                                                        "remaining time"=>$rem_time,
                                                                        "total watt"=>$watt);


									/***************************/
                                                                      $devices_config_para = array("starter relay off timer"=>$starter_relay_off_timer,
                        "start high current cutoff"=>$start_high_current_cutoff,
                        "start low current cutoff"=>$start_low_current_cutoff,
                        "Run high current cutoff"=>$Run_high_current_cutoff,
                        "Run low current cutoff"=>$Run_low_current_cutoff,
                        "high voltage cutoff"=>$high_voltage_cutoff,
                        "low voltage cutoff"=>$low_voltage_cutoff,
                        "ultra high voltage cutoff"=>$ultra_high_voltage_cutoff,
                        "ultra high current cutoff"=>$ultra_high_current_cutoff,
                        "USER SET MAX CURRENT CUTOFF LIMIT"=>$USER_SET_MAX_CURRENT_CUTOFF_LIMIT,
                        "USER AND DEVICE MAX CURRENT LIMIT"=>$USER_AND_DEVICE_MAX_CURRENT_LIMIT,
                        "LOCK ENABLE/DISABLE"=>$LOCK_ENABLE_DISABLE);
                                                                        
                                                                    /*********************************/


										}







                  

                                $devices=array($devices_gate, $devices_fan, $devices_light, $devices_switch);        //8-2
                               
                            
                                
                           




                                  /****************************************/

$query_next_id = "SELECT node_id FROM efactorproduct WHERE access_token = '$val' AND is_added = '1' AND node_id > '$node_id' ORDER BY node_id ASC LIMIT 1";
$row = $db->get_row($query_next_id);

if ($row) {
    
    $device_id_next = $row['node_id'];
} else {

//SELECT id FROM efactorproduct WHERE id > 1 ORDER BY id ASC;
   
  //  $query_next_id = "SELECT node_id FROM efactorproduct WHERE access_token = '$val' AND node_id > '$node_id' ORDER BY node_id ASC LIMIT 1";
$query_next_id = "SELECT node_id FROM efactorproduct WHERE access_token = '$val' AND node_id > '$node_id' ORDER BY id ASC";
//$query_next_id = "SELECT id FROM efactorproduct WHERE id > 1 ORDER BY id ASC";
    $row = $db->get_row($query_next_id);

    if ($row) {
        $device_id_next = $row['node_id'];
    } else {
        
        $device_id_next = null; // or any other desired action
    }
}

$nextid = $device_id_next;






                       
                                 /*****************************************/

                               /*$query_next_id = "SELECT * FROM efactorproduct WHERE access_token='$val' AND add_gateway='NULL' OR add_gateway !='NULL'";
                                $result_next_id = $db->get_results($query_next_id);
                                $num_next = $db->num_rows($query_next_id);

                                foreach ($result_next_id as $row) {
                                          $is_linked_next = $row['is_added'];
                                          $device_id_next = $row['node_id'];
                                          $nextid = $device_id_next;
                                          // Print or perform operations with $nextid
                                         // echo $nextid . "<br>";
                                       }*/


                                 /* $query_next_id = "SELECT * FROM efactorproduct WHERE access_token='$val'AND add_gateway='NULL' OR add_gateway !='NULL'";
                                  $result_next_id = $db->get_results($query_next_id);
                                  $num_next = $db->num_rows($query_next_id);

                                  foreach ($result_next_id as $row) {
                                               $is_linked_next = $row['is_added'];
                                               $device_id_next = $row['node_id'];
                                               $nextid = $device_id_next;
                                             //  echo $nextid . "<br>";
                                           }*/






                              

$params = json_encode([$name=>$dev_paras]);
//$params_1[]=json_decode($params,true);
//echo $params;

           
                              

                             }



								
					
			 }

			 else
				 
			  {

//echo 'else_3';
   
                                        $query_share = "SELECT * FROM efactorproduct WHERE access_token='$val' OR (share_user='$phone'/* AND share_req_status='accept'*/)";
				      //  $share = $db->get_results($query_share);
                                        $num_share = $db->num_rows($query_share); 
				      //  $row_share = $db->get_row($query_share);    
				      //  $shareuser=$row_share['share_user'];
	/*$endTime_1 = microtime(true);

$executionTime_1 = $endTime_1 - $startTime;

echo $executionTime_1;*/
						

                                       
                                       if($num_share>0)

                                      {

                                     
                                      
                                   

                                                  $query_3 = "SELECT * FROM efactorproduct WHERE access_token='$val' OR (share_user='$phone'/* AND share_req_status='accept'*/)";

							 //  $doctors = $db->get_results($query_3); 
							   $row = $db->get_row($query_3);
						           $device_id=$row['node_id'];
                                                           $address=$row['node_id']; 
							   $devicetype_1=$row['device_type'];
                                                           $devicetype=$row['device_type'];
                                                           $addstatus_1=$row['req_status'];
                                                           $share_req_status=$row['share_req_status'];
							   $moduletype= $row['module_type'];
							   $name=$row['name'];
							   $speed=$row['speed'];
							   $hue=$row['Hue'];
							   $direction=$row['direction'];
							   $mac=$row['mac'];
                                                           $shareuser=$row['share_user'];
                                                           
							   //$power=$row['power'];
							   
							   $power=filter_var($row['power'], FILTER_VALIDATE_BOOLEAN);
							   $led = filter_var($row['led_indication'], FILTER_VALIDATE_BOOLEAN);

                                      $devicename=$row['device_name'];
                                      $curr_devicetype=$row['current_devicetype'];
                                      $recipe=$row['recipe'];
                                      $user_priority=$row['user_priority'];
                                      $cookermode=$row['cookermode'];
                                      $cooker_led=$row['cooker_led'];
                                      $cooktime=$row['cook_time'];
                                      $cook_run_temp=$row['cook_run_temp'];
                                      $current=$row['current']; 
                                       $voltage=$row['ac_float'];
                                       $bus_voltage=$row['bus_voltage'];
                                             $rpm=$row['rpm'];  
                                             $timer=$row['timer'];
                                           $watt=$row['power_watt'];
                                             $rem_time=$row['rem_time'];  
                                            $hood_autoclean=$row['hood_autoclean'];
                                              $hood_speed=$row['hood_speed'];
                                              $hood_lamp=$row['hood_lamp'];

                                            /* $nurse=$row['nurse'];
                                              $personal_assistant=$row['personal_assistant'];
                                              $doctormanage=$row['doctor'];
                                              $nurse_resp=$row['nurse_resp'];
                                              $personal_assistant_resp=$row['personal_assistant_resp'];
                                              $doctormanage_resp=$row['doctor_resp'];*/

                                             $nurse=filter_var($row['nurse'], FILTER_VALIDATE_BOOLEAN);
$personal_assistant=filter_var($row['personal_assistant'], FILTER_VALIDATE_BOOLEAN);
$doctormanage=filter_var($row['doctor'], FILTER_VALIDATE_BOOLEAN);
$nurse_resp=$row['nurse_resp'];      
$personal_assistant_resp=$row['personal_assistant_resp'];                    
$doctormanage_resp=$row['doctor_resp'];                      





                                               $model_number=$row['model_number'];
                                                           $devicename=$row['device_name'];
                                                           $vendor_name=$row['vendor_name'];
                                                           $moduletype=$row['module_type'];
                                                           $net_conn_1=$row['internet'];
                                                           $access_token=$row['access_token'];


                                                           if($devicetype_1 == 'exhaust_hood')
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





                                      if($devicetype_1 == 'swpcp')
                                           {
                                         //swpcp
                                         $starter_relay_off_timer = $row['starter_relay_off_timer'];  
$start_high_current_cutoff = $row['start_high_current_cutoff'];  
$start_low_current_cutoff= $row['start_low_current_cutoff'];  
$Run_high_current_cutoff = $row['Run_high_current_cutoff'];  
$Run_low_current_cutoff = $row['Run_low_current_cutoff'];  
$high_voltage_cutoff = $row['high_voltage_cutoff'];  
$low_voltage_cutoff = $row['low_voltage_cutoff'];  
$ultra_high_voltage_cutoff = $row['ultra_high_voltage_cutoff'];  
$ultra_high_current_cutoff = $row['ultra_high_current_cutoff'];  
$USER_SET_MAX_CURRENT_CUTOFF_LIMIT = $row['USER_SET_MAX_CURRENT_CUTOFF_LIMIT'];  
$USER_AND_DEVICE_MAX_CURRENT_LIMIT = $row['USER_AND_DEVICE_MAX_CURRENT_LIMIT'];  
$LOCK_ENABLE_DISABLE = (bool)$row['LOCK_ENABLE_DISABLE'];  
                                         }








                                            if($devicetype_1 == 'cooker')
                                              {
                                           //cooker

                                            $cooker_timer = $row['cooker_timer_1'];  
                                            $cooking_temperature = $row['cooker_max_temperature_1'];    
                                            $Keep_warm= $row['Keep_warm_1'];  
                                            $Pre_timer_constant_mode = $row['Pre_timer_constant_mode'];  
                                            $Pre_timer = $row['Pre_timer'];  
                                            $Pre_temperature = $row['Pre_temperature'];  
                                            $Coooker_max_temperature = $row['Coooker_max_temperature'];  
                                            $Cooker_constant_mode = $row['Cooker_constant_mode'];  
                                            $Keep_Warm_timer = $row['Keep_Warm_timer_1'];  
                                            $Dishes = $row['dish'];  
                                            $pre_timer_wait_temperature_constant_mode = $row['pre_timer_wait_temperature_constant_mode'];  
                                            $pre_timer_waiting_time = $row['pre_timer_waiting_time'];
                                            $pre_timer_Waiting_temperature = $row['pre_timer_Waiting_temperature'];  
                                            $warm_constant_cut_off = $row['warm_constant_cut_off'];  
                                            $cook_wait_temp_constant_mode = $row['cook_wait_temp_constant_mode'];  
                                            $cook_waiting_time = $row['cook_waiting_time'];  
                                            $cook_Waiting_temperature = $row['cook_Waiting_temperature'];  
                                            $warm_wait_temp_constant_mode = $row['warm_wait_temp_constant_mode'];  
                                            $warm_waiting_time = $row['warm_waiting_time'];  
                                            $warm_Waiting_temperature = $row['warm_Waiting_temperature'];  
                                            $cooker_ok_button = $row['cooker_ok_button']; 
                                            $reload_default = $row['reload_default'];


                                         }


                           //  echo 'echo 4';



                                          if($devicetype_1 == 'fan')
                                           {
                                     //fan
                                            $fan_resistance = $row['fan_resistance'];  
                                            $fan_lmin = $row['fan_lmin'];  
                                            $fan_lmax= $row['fan_lmax'];  
                                            $fan_frequency = $row['fan_frequency'];  
                                            $fan_pole_pairs = $row['fan_pairs'];  
                                            $fan_open_loop_current = $row['fan_open_loop_current'];  
                                            $fan_power_limit = $row['fan_power_limit'];  
                                            $fan_torque_limit = $row_6['fan_torque_limit'];  
                                            $fan_speed1 = $row['fan_speed1'];  
                                            $fan_speed2= $row['fan_speed2'];
                                            $fan_speed3 = $row['fan_speed3'];  
                                            $fan_speed4= $row['fan_speed4'];
                                            $fan_speed5 = $row['fan_speed5'];  
                                            $fan_speed6= $row['fan_speed6'];
                                            $fan_speed7 = $row['fan_speed7'];  
                                            $fan_button= $row['fan_button'];

                                       }
          
                                       
                                      $tz_properties=$row['TZ_properties'];
                                      $br_properties=$row['BR_properties'];
                                      $umi_properties=$row['UMI_properties'];
                                      $ec_properties=$row['EC_properties'];
                                      $cm_properties=$row['CM_properties'];
                                      $tu_properties=$row['TU_properties'];
                                      $remote_pair_properties=$row['remote_pair_properties'];
                                      $display_properties=$row['display_properties'];
                                      $swpcp_1ph_properties=$row['swpcp_1ph_properties'];
                                      $fan_properties=$row['fan_properties'];
                                      $lc_properties=$row['LC_properties'];
                                      $cooker_properties=$row['cooker_properties'];
                                    
                                      $baudrate=$row['baudrate'];
                                      $uartmsgid=$row['uartmsgid'];
                                      $time=$row['time'];
                                      $errorcode=$row['errorcode'];
                                      $comm_mode=$row['comm_mode'];

                                     /*
                                      $model_number=$row['model_number'];
                                      $vendor_name=$row['vendor_name'];*/














                                       






                              

			

								$arr=array();
								foreach ($doctors as $key => $doctor) 
								 {
								  array_push($arr,array("id"=>$doctor['id'],"node_id"=>$doctor['node_id'],"device_type"=>$doctor['device_type']));
								 }
  

							         $device_id=$arr[0]['node_id'];	  
								  $next_id_2=$arr[1]['node_id'];
					         		  $nextid = $next_id_2;
							       


			                  /******************************/


                              if($devicetype_1 == 'Gateway' && $addstatus_1 == 'confirmed'/* && $share_req_status == 'accept'*/)
                               
                                 {


                                /******************************/
                                                                          $gate_attr_1 = json_encode(["name"=>"Device Id",
													  "value"=>$device_id]);
								$gate_attr_2 = json_encode(["name"=>"mac",
													  "value"=>$mac]);
								$gate_attri[] = json_decode($gate_attr_1, true);
								$gate_attri[] = json_decode($gate_attr_2, true);
								$gate_att=json_encode($fan_attri);

                                        /*********************************/

								$G_Params = json_encode(["name" => "name",
														  "type"=> "efactor.param.name",
													 "data_type"=>"string",
													"properties"=> ["read","write"]]);

                                       $G_Params_2=json_encode(["name"=> "power",
                                                             "data_type"=> "bool",
                                                             "properties"=>["read","write"],
                                                             "type"=>"efactor.param.power",
                                                             "ui_type"=>"efactor.ui.toggle"]);
								$G_p_1[] = json_decode($G_Params, true);
                                        $G_p_1[] = json_decode($G_Params_2, true);

								$Gate_Dev= json_encode(["name" => $curr_devicetype,
                                                                "attributes"=>$gate_attri,
														 "type"=> "efactor.device.gateway",
													   "params"=> $G_p_1]);
								$devices_list[] = json_decode($Gate_Dev, true);

                               }




                               else if($devicetype_1 == 'fan' && $addstatus_1 == 'confirmed')

                                {
        
                                                           $fan_attri = array();
					 				$fan_attr_1 = json_encode(["name"=>"Device Id",
														   "value"=>$device_id]);

									$fan_attr_2 = json_encode(["name"=>"mac",
															  "value"=>$mac]);
									$fan_attri[] = json_decode($fan_attr_1, true);
									$fan_attri[] = json_decode($fan_attr_2, true);

									$fan_att=json_encode($fan_attri);

                                                           /***************************************/





                                                                   
  

								   $fan_P_1 = json_encode(["name" => "name",
														  "data_type"=> "String",
														 "properties"=>["read","write"],
															   "type"=> "efactor.param.name"]);


								  $fan_P_2 = json_encode(["name" => "power",
													  "data_type"=> "bool",
													 "properties"=> ["read","write"],
														   "type"=>"efactor.param.power",
                                                                    "type"=>"efactor.param.power",
														"ui_type"=>"efactor.ui.toggle"]);

								                                                                           



                                                                  if($model_number == "EFACT V1.2")
                                                                      {

								  $fan_p[]= json_decode($fan_P_1, true);
								  $fan_p[]= json_decode($fan_P_2, true);
                                                                 								  
                                                                    }



                                                                else{
                                                                  $fan_p[]= json_decode($fan_P_1, true);
								  $fan_p[]= json_decode($fan_P_2, true);
                                                              


                                                                   }



								  

								  $fan_par=json_encode($fan_p);





								

										if($model_number == "EFACT V1.2")
                                                                      {
									$devices_1 = json_encode(["name"=>$curr_devicetype,
												"attributes"=>$fan_attri,
                                                                                              
												"params"=>$fan_p,
												"primary"=>"power",
											       "type"=>"efactor.device.fan","brand_logo"=>$url]);

                                                                       }
                                                                       else{

                                                                            $devices_1 = json_encode(["name"=>$curr_devicetype,
												"attributes"=>$fan_attri,           
												"params"=>$fan_p,
												"primary"=>"power",
											       "type"=>"efactor.device.fan","brand_logo"=>$url]);

                                                                      }




										$devices_list[] = json_decode($devices_1, true);
										/*****************/

										/*$device_type_param = array("Direction" =>intval($direction),
													 "led colour"=>intval($hue),
														 "output"=>true,
														  "power"=>$power,
														  "Speed"=>intval($speed),
															"led"=>$led,
														"name"=>$name);*/

										/*****************/

                                                                          if($power == '0')
                                                                           {
                                                                             $led = $power;
                                                                             }


                                                                    
                                              /* $devices_para_arr = array("Direction" =>intval($direction),
												 "led colour"=>intval($hue),
													 "output"=>true,
													  "power"=>$power,
													  "Speed"=>intval($speed),
														"led"=>$led,
													"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                         "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm,
                                                                         "buzzer"=>(bool)$fan_buzzer);*/


                                           if($model_number == "EFACT V1.2")
                                                                      {
                                                    $devices_para_arr = array("Direction"=>intval($direction),
					                             "led colour"=>intval($hue),
							                 "output"=>true,
									"power"=>$power,
									"Speed"=>intval($speed),
									"led"=>$led,
									"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                         "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm,
                                                                         "buzzer"=>(bool)$fan_buzzer);
                                                                        

                                                                  }

                                                 else{

                                       $devices_para_arr = array("Direction"=>intval($direction),
					                             "led colour"=>intval($hue),
							                 "output"=>true,
									"power"=>$power,
									"Speed"=>intval($speed),
									"led"=>$led,
									"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                         "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm);

                                                 }
                                             







                                               $devices_config_para = array("resistance"=>$fan_resistance,
                        "lmin"=>$fan_lmin,
                        "lmax"=>$fan_lmax,
                        "frequency"=>$fan_frequency,
                        "pole pairs"=>$fan_pole_pairs,
                        "open loop current"=>$fan_open_loop_current,
                        "power limit"=>$fan_power_limit,
                        "torque limit"=>$fan_torque_limit,
                        "speed1"=>$fan_speed1,
                        "speed2"=>$fan_speed2,
                        "speed3"=>$fan_speed3,
                        "speed4"=>$fan_speed4,
                         "speed5"=>$fan_speed5,
                         "speed6"=>$fan_speed6,
                           "speed7"=>$fan_speed7,
                           "button"=>$fan_button);
                                                                    


                                             
                                             

                                                 /*****************/


                                    }


                                  else if($devicetype_1 == 'light' && $addstatus_1 == 'confirmed'/* && $share_req_status == 'accept'*/)

                                   {
       

									$light_attr_1 = json_encode(["name"=>"Device Id",
																"value"=>$device_id]);

									$light_attr_2 = json_encode(["name"=>"mac",
																"value"=>$mac]);
									$light_attri[] = json_decode($light_attr_1, true);
									$light_attri[] = json_decode($light_attr_2, true);
									$light_att=json_encode($light_attri);
									//echo $light_att;

									$light_P_1 = json_encode(["name" => "name",
														  "data_type"=> "String",
														 "properties"=> ["read","write"],
															   "type"=>"efactor.param.name"]);

									$light_P_2 = json_encode(["name" => "power",
														  "data_type"=> "bool",
														 "properties"=> ["read","write"],
                                                                        "type"=>"efactor.param.power",
															"ui_type"=>"efactor.ui.toggle"]);

									$max_1 = array("max"=>100,
												   "min" =>0);
									//$max2 = json_encode($max_1);

									

									$light_p[]= json_decode($light_P_1, true);
									$light_p[]= json_decode($light_P_2, true);
																		//echo $light_p;

									$light_par=json_encode($light_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$light_attri,
															"params"=>$light_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.lightbulb","brand_logo"=>$url]);
									$devices_list[] = json_decode($devices_1, true);

									//echo $devices;

									/********************/

									$devices_para_arr= array("brightness" => intval($brightness),
                                                                       "output" => true,
                                                                        "power"=>$power,
                                                                       "name"=>$name);



									/*******************/

	                               

                                            /********************/
     
                                  }

                                 else if($devicetype_1 == 'switch' && $addstatus_1 == 'confirmed'/* && $share_req_status == 'accept'*/)

                                  {

									/*-----------------switch--------------*/
									$switch_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$switch_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$switch_attri[] = json_decode($switch_attr_1, true);
									$switch_attri[] = json_decode($switch_attr_2, true);

									$switch_att=json_encode($switch_attri);

									$switch_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$switch_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                           "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$switch_p[]= json_decode($switch_P_1, true);
									$switch_p[]= json_decode($switch_P_2, true);
									$switch_par=json_encode($switch_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$switch_attri,
															"params"=>$switch_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.switch","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


									//echo $devices_switch;

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


	                              }




                                          else if($devicetype_1 == 'cooker' && $addstatus_1 == 'confirmed'/* && $share_req_status == 'accept'*/)

                                  {

                                                                        									/*-----------------switch--------------*/
									$cooker_attr_1 = json_encode(["name"=>"Device Id",
											    "value"=>$device_id]);

								$cooker_attr_2 = json_encode(["name"=>"mac",
											     "value"=>$mac]);
								$cooker_attri[] = json_decode($cooker_attr_1, true);
								$cooker_attri[] = json_decode($cooker_attr_2, true);


									$cooker_att=json_encode($cooker_attri);














									$cooker_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$cooker_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                                                          "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

								           

									$cooker_p[]= json_decode($cooker_P_1, true);
									$cooker_p[]= json_decode($cooker_P_2, true);
                                                                       									$cooker_par=json_encode($cooker_p);





									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$cooker_attri,
                                                                                                                   															"params"=>$cooker_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.cooker","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


									//echo $devices_switch;

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                        "skipmode"=>true,
                                                                         "cookermode"=>$cookermode,
                                                                          
                                                                          "recipe"=>$recipe,
                                                                         "name"=>$name,
                                                                        "remaining time"=>$cooktime,
                                                                        "running temperature"=>$cook_run_temp);

									/***************************/


                                      /*----------------------------------*/


					$devices_config_para = array("Cooker Timer"=>(int)$cooker_timer,
                                                                           "Cooking temperature"=>(int)$cooking_temperature,
                                                                           "Keep warm temperature"=>(int)$Keep_warm,
                                                                           "Pre-timer temp constant mode"=>$Pre_timer_constant_mode,
                                                                           "Pre-timer"=>(int)$Pre_timer,
                                                                           "Pre – temperature"=>(int)$Pre_temperature,
                                                                           "Coooker max-temperature"=>(int)$Coooker_max_temperature,
                                                                           "Cooker temp constant mode"=>(bool)$Cooker_constant_mode,
                                                                           "Keep Warm timer"=>(int)$Keep_Warm_timer,
                                                                           "pre-timer wait temperature constant mode"=>(bool)$pre_timer_wait_temperature_constant_mode,
                                                                           "pre-timer waiting time"=>(int)$pre_timer_waiting_time,
                                                                           "pre-timer Waiting temperature"=>(int)$pre_timer_Waiting_temperature,
                                                                           "Keep warm temp constant mode"=>(bool)$warm_constant_cut_off,
                                                                           "cook wait temp constant mode"=>(int)$cook_wait_temp_constant_mode,
                                                                           "cook waiting time"=>(int)$cook_waiting_time,
                                                                           "cook Waiting- temperature"=>(int)$cook_Waiting_temperature,
                                                                           "warm-wait temp constant mode"=>(bool)$warm_wait_temp_constant_mode,
                                                                           "warm waiting time"=>(int)$warm_waiting_time,
                                                                           "warm Waiting temperature"=>(int)$warm_Waiting_temperature,
                                                                             "OK" => (bool)$cooker_ok_button,
                                                                           "Reload Default" => (bool)$reload_default,
                                                                           "Dishes" => $recipe);




                                                                        


							/*-----------------------------------*/	

















	                              }



                           /**********scanner***************21-4-23***********************/


                                           else if($devicetype_1 == 'scanner' && $addstatus_1 == 'confirmed'/* && $share_req_status == 'accept'*/)

                                   {

									/*-----------------switch--------------*/
									$scanner_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$scanner_attr_2 = json_encode(["name"=>"mac",
														"value"=>$mac]);
									$scanner_attri[] = json_decode($scanner_attr_1, true);
									$scanner_attri[] = json_decode($scanner_attr_2, true);

									$scanner_att=json_encode($scanner_attri);

									$scanner_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$scanner_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$scanner_p[]= json_decode($scanner_P_1, true);
									$scanner_p[]= json_decode($scanner_P_2, true);
									$scanner_par=json_encode($scanner_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$scanner_attri,
															"params"=>$scanner_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.qr-scanner","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


										}



                                   



                                    else if($devicetype_1 == 'outlet' && $addstatus_1 == 'confirmed'/* && $share_req_status == 'accept'*/)

                              {

                                           //  $outlet_attri = array();


                                                       $outlet_attri = [];
                                                   $outlet_p = [];
                                      
								/*-----------------outlet-----------------*/

								$outlet_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$outlet_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$outlet_attri[] = json_decode($outlet_attr_1, true);
									$outlet_attri[] = json_decode($outlet_attr_2, true);

									$outlet_att=json_encode($outlet_attri);

									$outlet_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$outlet_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                           "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$outlet_p[]= json_decode($outlet_P_1, true);
									$outlet_p[]= json_decode($outlet_P_2, true);
									$outlet_par=json_encode($outlet_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$outlet_attri,
															"params"=>$outlet_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.outlet","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


									//echo $devices_switch;

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


								


 
	                          }




else if($devicetype_1 == 'other' && $addstatus_1== 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$other_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$other_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$other_attri[] = json_decode($other_attr_1, true);
									$other_attri[] = json_decode($other_attr_2, true);

									$other_att=json_encode($other_attri);

									$other_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$other_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$other_p[]= json_decode($other_P_1, true);
									$other_p[]= json_decode($other_P_2, true);
									$other_par=json_encode($other_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$other_attri,
															"params"=>$other_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.other","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


										}







else if($devicetype_1 == 'HBMDNC' && $addstatus_1== 'confirmed')

                                   {
                                                   $hbmdnc_attri = array();
                                                   $hbmdnc_p = array();

									/*-----------------outlet--------------*/
									$hbmdnc_attr_1 = json_encode(["name"=>"Device Id",
											        "value"=>$device_id]);

									$hbmdnc_attr_2 = json_encode(["name"=>"mac",
											         "value"=>$mac]);
									$hbmdnc_attri[] = json_decode($hbmdnc_attr_1, true);
									$hbmdnc_attri[] = json_decode($hbmdnc_attr_2, true);

									$hbmdnc_att=json_encode($hbmdnc_attri);

									$hbmdnc_P_1 = json_encode(["name" => "name",
												 "data_type"=> "String",
											"properties"=> ["read","write"],
											"type"=>"efactor.param.name"]);

									
                                                         

									$hbmdnc_p[]= json_decode($hbmdnc_P_1, true);
									

									$hbmdnc_par=json_encode($hbmdnc_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$hbmdnc_attri,
															"params"=>$hbmdnc_p,
														   
															  "type"=>"efactor.device.hbmdnc",
                                                                                                                      "brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 
									$devices_para_arr = array("Doctor"=>(bool)$doctormanage,
                                                                                                  "Personal Assist"=>(bool)$personal_assistant,
                                                                                                   "nurse"=>(bool)$nurse,
                                                                                                    "Doctor status"=>$doctormanage_resp,
                                                                                                    "PA status"=>$personal_assistant_resp,
                                                                                                   "Nurse_Status"=>$nurse_resp,
                                                                                                    "name"=>$name);



									

									/***************************/


										}



else if($devicetype_1 == 'input-output' && $addstatus_1== 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$other_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$other_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$other_attri[] = json_decode($other_attr_1, true);
									$other_attri[] = json_decode($other_attr_2, true);

									$other_att=json_encode($other_attri);

									$other_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$other_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$other_p[]= json_decode($other_P_1, true);
									$other_p[]= json_decode($other_P_2, true);
									$other_par=json_encode($other_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$other_attri,
															"params"=>$other_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.input-output","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


										}















else if($devicetype_1 == 'exhaust_hood' && $addstatus_1== 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$exh_attr_1 = json_encode(["name"=>"Device Id",
													"value"=>$device_id]);

									$exh_attr_2 = json_encode(["name"=>"mac",
												"value"=>$mac]);
									$exh_attri[] = json_decode($exh_attr_1, true);
									$exh_attri[] = json_decode($exh_attr_2, true);

									$exh_att=json_encode($exh_attri);
                                                                         
									$exh_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$exh_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									



                                                                      

									$exh_p[]= json_decode($exh_P_1, true);
									$exh_p[]= json_decode($exh_P_2, true);
                                                                        
																		$exh_par=json_encode($exh_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$exh_attri,
															"params"=>$exh_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.exhaust-hood","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

								/*	 $devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);*/



                                                                      $devices_para_arr = array("power" => $power,
                                                                                                   "output" => true,
                                                                                                   "name" => $name,
                                                                                               "autoclean" =>(bool)$hood_autoclean,
                                                                                                  "airflow" => (int)$hood_speed,
                                                                                                    "lamp" =>(bool)$hood_lamp);


									/***************************/


										}





                 else if($devicetype_1 == 'swpcp' && $addstatus_1== 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$wp_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$wp_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$wp_attri[] = json_decode($wp_attr_1, true);
									$wp_attri[] = json_decode($wp_attr_2, true);

									$wp_att=json_encode($wp_attri);
                                                                           $max_1 = array("max"=>60,
										          "min"=>1);

                                                                       

									$wp_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$wp_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                                                               "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);



                                                                     
  
                                                                          
                
									$wp_p[]= json_decode($wp_P_1, true);
									$wp_p[]= json_decode($wp_P_2, true);
                                                                        									$wp_par=json_encode($wp_p);


									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$wp_attri,
                                                                                                               
															"params"=>$wp_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.swpcp","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name,
                                                                         "Timer"=>$timer,
                                                                        "current(A)"=>$current,
                                                                        "voltage(V)"=>$voltage,
                                                                        "remaining time"=>$rem_time,
                                                                        "total watt"=>$watt);


									/***************************/

                                                                       $devices_config_para = array("starter relay off timer"=>$starter_relay_off_timer,
                        "start high current cutoff"=>$start_high_current_cutoff,
                        "start low current cutoff"=>$start_low_current_cutoff,
                        "Run high current cutoff"=>$Run_high_current_cutoff,
                        "Run low current cutoff"=>$Run_low_current_cutoff,
                        "high voltage cutoff"=>$high_voltage_cutoff,
                        "low voltage cutoff"=>$low_voltage_cutoff,
                        "ultra high voltage cutoff"=>$ultra_high_voltage_cutoff,
                        "ultra high current cutoff"=>$ultra_high_current_cutoff,
                        "USER SET MAX CURRENT CUTOFF LIMIT"=>$USER_SET_MAX_CURRENT_CUTOFF_LIMIT,
                        "USER AND DEVICE MAX CURRENT LIMIT"=>$USER_AND_DEVICE_MAX_CURRENT_LIMIT,
                        "LOCK ENABLE/DISABLE"=>$LOCK_ENABLE_DISABLE);
                                                                        
                                                                    /*********************************/


										}














                    }




else{



                                                                           $query_3 = "SELECT * FROM efactorproduct where access_token='$val'";
							//   $doctors = $db->get_results($query_3); 
							   $row = $db->get_row($query_3);
						           $device_id=$row['node_id'];
                                                           $address= $row['node_id'];
							   $devicetype_1=$row['device_type'];
                                                           $devicetype=$row['device_type'];
                                                           $addstatus_1=$row['req_status'];
                                                            $share_req_status=$row['share_req_status'];
							   $moduletype= $row['module_type'];
							   $name=$row['name'];
							   $speed=$row['speed'];
							   $hue=$row['Hue'];
							   $direction=$row['direction'];
							   $mac=$row['mac'];
                                                           
							   //$power=$row['power'];
							   
							   $power=filter_var($row['power'], FILTER_VALIDATE_BOOLEAN);
							   $led = filter_var($row['led_indication'], FILTER_VALIDATE_BOOLEAN);

                                      $devicename=$row['device_name'];
                                      $curr_devicetype=$row['current_devicetype'];
                                      $recipe=$row['recipe'];
                                      $user_priority=$row['user_priority'];
                                      $cookermode=$row['cookermode'];
                                      $cooker_led=$row['cooker_led'];
                                      $cooktime=$row['cook_time'];
                                      $cook_run_temp=$row['cook_run_temp'];
                                      $current=$row['current'];
                                      $voltage=$row['ac_float'];
                                     $bus_voltage=$row['bus_voltage'];
                                             $rpm=$row['rpm'];   
                                             $timer=$row['timer']; 
                                              $watt=$row['power_watt'];
                                             $rem_time=$row['rem_time'];
                                             $hood_autoclean=$row['hood_autoclean'];
                                              $hood_speed=$row['hood_speed'];
                                              $hood_lamp=$row['hood_lamp'];


                                         /*     $nurse=$row['nurse'];
                                              $personal_assistant=$row['personal_assistant'];
                                              $doctormanage=$row['doctor'];
                                              $nurse_resp=$row['nurse_resp'];
                                              $personal_assistant_resp=$row['personal_assistant_resp'];
                                              $doctormanage_resp=$row['doctor_resp'];*/

                                            $nurse=filter_var($row['nurse'], FILTER_VALIDATE_BOOLEAN);
$personal_assistant=filter_var($row['personal_assistant'], FILTER_VALIDATE_BOOLEAN);
$doctormanage=filter_var($row['doctor'], FILTER_VALIDATE_BOOLEAN);
$nurse_resp=$row['nurse_resp'];      
$personal_assistant_resp=$row['personal_assistant_resp'];                    
$doctormanage_resp=$row['doctor_resp'];                      






                                             $model_number=$row['model_number'];
                                                           $devicename=$row['device_name'];
                                                           $vendor_name=$row['vendor_name'];
                                                           $moduletype=$row['module_type'];
                                                           $net_conn_1=$row['internet'];
                                                           $access_token=$row['access_token'];


                                                                  if($devicetype_1 == 'exhaust_hood')
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



                                      if($devicetype_1 == 'swpcp')
                                         {  
                                           //swpcp

                                            $starter_relay_off_timer = $row['starter_relay_off_timer'];  
$start_high_current_cutoff = $row['start_high_current_cutoff'];  
$start_low_current_cutoff= $row['start_low_current_cutoff'];  
$Run_high_current_cutoff = $row['Run_high_current_cutoff'];  
$Run_low_current_cutoff = $row['Run_low_current_cutoff'];  
$high_voltage_cutoff = $row['high_voltage_cutoff'];  
$low_voltage_cutoff = $row['low_voltage_cutoff'];  
$ultra_high_voltage_cutoff = $row['ultra_high_voltage_cutoff'];  
$ultra_high_current_cutoff = $row['ultra_high_current_cutoff'];  
$USER_SET_MAX_CURRENT_CUTOFF_LIMIT = $row['USER_SET_MAX_CURRENT_CUTOFF_LIMIT'];  
$USER_AND_DEVICE_MAX_CURRENT_LIMIT = $row['USER_AND_DEVICE_MAX_CURRENT_LIMIT'];  
$LOCK_ENABLE_DISABLE = (bool)$row['LOCK_ENABLE_DISABLE'];  
             }




                                            if($devicetype_1 == 'cooker')
                                              {
                                           //cooker

                                            $cooker_timer = $row['cooker_timer_1'];  
                                            $cooking_temperature = $row['cooker_max_temperature_1'];  
                                            $Keep_warm= $row['Keep_warm_1'];  
                                            $Pre_timer_constant_mode = $row['Pre_timer_constant_mode'];  
                                            $Pre_timer = $row['Pre_timer'];  
                                            $Pre_temperature = $row['Pre_temperature'];  
                                            $Coooker_max_temperature = $row['Coooker_max_temperature'];  
                                            $Cooker_constant_mode = $row['Cooker_constant_mode'];  
                                            $Keep_Warm_timer = $row['Keep_Warm_timer_1'];  
                                            $Dishes = $row['dish'];   
                                            $pre_timer_wait_temperature_constant_mode = $row['pre_timer_wait_temperature_constant_mode'];  
                                            $pre_timer_waiting_time = $row['pre_timer_waiting_time'];
                                            $pre_timer_Waiting_temperature = $row['pre_timer_Waiting_temperature'];  
                                            $warm_constant_cut_off = $row['warm_constant_cut_off'];  
                                            $cook_wait_temp_constant_mode = $row['cook_wait_temp_constant_mode'];  
                                            $cook_waiting_time = $row['cook_waiting_time'];  
                                            $cook_Waiting_temperature = $row['cook_Waiting_temperature'];  
                                            $warm_wait_temp_constant_mode = $row['warm_wait_temp_constant_mode'];  
                                            $warm_waiting_time = $row['warm_waiting_time'];  
                                            $warm_Waiting_temperature = $row['warm_Waiting_temperature'];  
                                            $cooker_ok_button = $row['cooker_ok_button']; 
                                            $reload_default = $row['reload_default'];


                                         }


                                         //    echo 'echo 5';

                              

                                        if($devicetype_1 == 'fan')
                                         {
                                     //fan
                                            $fan_resistance = $row['fan_resistance'];  
                                            $fan_lmin = $row['fan_lmin'];  
                                            $fan_lmax= $row['fan_lmax'];  
                                            $fan_frequency = $row['fan_frequency'];  
                                            $fan_pole_pairs = $row['fan_pairs'];  
                                            $fan_open_loop_current = $row['fan_open_loop_current'];  
                                            $fan_power_limit = $row['fan_power_limit'];  
                                            $fan_torque_limit = $row['fan_torque_limit'];  
                                            $fan_speed1 = $row['fan_speed1'];  
                                            $fan_speed2= $row['fan_speed2'];
                                            $fan_speed3 = $row['fan_speed3'];  
                                            $fan_speed4= $row['fan_speed4'];
                                            $fan_speed5 = $row['fan_speed5'];  
                                            $fan_speed6= $row['fan_speed6'];
                                            $fan_speed7 = $row['fan_speed7'];  
                                            $fan_button= $row['fan_button'];

                                    }
                                     
                                       
                                      $tz_properties=$row['TZ_properties'];
                                      $br_properties=$row['BR_properties'];
                                      $umi_properties=$row['UMI_properties'];
                                      $ec_properties=$row['EC_properties'];
                                      $cm_properties=$row['CM_properties'];
                                      $tu_properties=$row['TU_properties'];
                                      $remote_pair_properties=$row['remote_pair_properties'];
                                      $display_properties=$row['display_properties'];
                                      $swpcp_1ph_properties=$row['swpcp_1ph_properties'];
                                      $fan_properties=$row['fan_properties'];
                                      $lc_properties=$row['LC_properties'];
                                      $cooker_properties=$row['cooker_properties'];
                                    
                                      $baudrate=$row['baudrate'];
                                      $uartmsgid=$row['uartmsgid'];
                                      $time=$row['time'];
                                      $errorcode=$row['errorcode'];
                                      $comm_mode=$row['comm_mode'];

                                     /*
                                      $model_number=$row['model_number'];
                                      $vendor_name=$row['vendor_name'];*/








			

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
							       //$id=$device_id;
							       //echo $devicetype_1;


			                  /******************************/


                              if($devicetype_1 == 'Gateway' && $addstatus_1 == 'confirmed')
                               
                                 {


                                         /******************************/
                                                                 $gate_attr_1 = json_encode(["name"=>"device_id",
											     "value"=>$device_id]);
								$gate_attr_2 = json_encode(["name"=>"mac",
											   "value"=>$mac]);
								$gate_attri[] = json_decode($gate_attr_1, true);
								$gate_attri[] = json_decode($gate_attr_2, true);
								$gate_att=json_encode($fan_attri);

                                        /*********************************/

								$G_Params = json_encode(["name" => "name",
											"type"=> "efactor.param.name",
										     "data_type"=>"string",
										    "properties"=> ["read","write"]]);

                                       $G_Params_2=json_encode(["name"=> "power",
                                                             "data_type"=> "bool",
                                                             "properties"=>["read","write"],
                                                             "type"=>"efactor.param.power",
                                                             "ui_type"=>"efactor.ui.toggle"]);
								$G_p_1[] = json_decode($G_Params, true);
                                        $G_p_1[] = json_decode($G_Params_2, true);

								$Gate_Dev= json_encode(["name" => $curr_devicetype,
                                                                "attributes"=>$gate_attri,
														 "type"=> "efactor.device.gateway",
													   "params"=> $G_p_1]);
								$devices_list[] = json_decode($Gate_Dev, true);

                               }




                               else if($devicetype_1 == 'fan' && $addstatus_1 == 'confirmed')

                                {


                                                                        $fan_attri = array();
									$fan_attr_1 = json_encode(["name"=>"Device Id",
														   "value"=>$device_id]);

									$fan_attr_2 = json_encode(["name"=>"mac",
															  "value"=>$mac]);
									$fan_attri[] = json_decode($fan_attr_1, true);
									$fan_attri[] = json_decode($fan_attr_2, true);

									$fan_att=json_encode($fan_attri);

                                                              
                                                                



								  $fan_P_1 = json_encode(["name" => "name",
														  "data_type"=> "String",
														 "properties"=>["read","write"],
															   "type"=> "efactor.param.name"]);


								  $fan_P_2 = json_encode(["name" => "power",
													  "data_type"=> "bool",
													 "properties"=> ["read","write"],
														   "type"=>"efactor.param.power",
                                                                    "type"=>"efactor.param.power",
														"ui_type"=>"efactor.ui.toggle"]);

								 


                                                                  if($model_number == "EFACT V1.2")
                                                                      {

								  $fan_p[]= json_decode($fan_P_1, true);
								  $fan_p[]= json_decode($fan_P_2, true);
                                                                                                                                   }



                                                                else{
                                                                  $fan_p[]= json_decode($fan_P_1, true);
								  $fan_p[]= json_decode($fan_P_2, true);
                                                                




                                                                   }



								  

								  $fan_par=json_encode($fan_p);





										//echo $fan_att;

										if($model_number == "EFACT V1.2")
                                                                      {
									$devices_1 = json_encode(["name"=>$curr_devicetype,
												"attributes"=>$fan_attri,
                                                                                              												"params"=>$fan_p,
												"primary"=>"power",
											       "type"=>"efactor.device.fan","brand_logo"=>$url]);

                                                                       }
                                                                       else{

                                                                            $devices_1 = json_encode(["name"=>$curr_devicetype,
												"attributes"=>$fan_attri,           
												"params"=>$fan_p,
												"primary"=>"power",
											       "type"=>"efactor.device.fan","brand_logo"=>$url]);

                                                                      }



										$devices_list[] = json_decode($devices_1, true);
										/*****************/

										/*$device_type_param = array("Direction" =>intval($direction),
													 "led colour"=>intval($hue),
														 "output"=>true,
														  "power"=>$power,
														  "Speed"=>intval($speed),
															"led"=>$led,
														"name"=>$name);*/

										/*****************/

                                                                          if($power == '0')
                                                                           {
                                                                             $led = $power;
                                                                             }


                                                                    
                                              /* $devices_para_arr = array("Direction" =>intval($direction),
												 "led colour"=>intval($hue),
													 "output"=>true,
													  "power"=>$power,
													  "Speed"=>intval($speed),
														"led"=>$led,
													"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                         "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm,
                                                                         "buzzer"=>(bool)$fan_buzzer);*/



                                                 if($model_number == "EFACT V1.2")
                                                                      {
                                                    $devices_para_arr = array("Direction"=>intval($direction),
					                             "led colour"=>intval($hue),
							                 "output"=>true,
									"power"=>$power,
									"Speed"=>intval($speed),
									"led"=>$led,
									"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                         "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm,
                                                                         "buzzer"=>(bool)$fan_buzzer);
                                                                        

                                                                  }

                                                 else{

                                       $devices_para_arr = array("Direction"=>intval($direction),
					                             "led colour"=>intval($hue),
							                 "output"=>true,
									"power"=>$power,
									"Speed"=>intval($speed),
									"led"=>$led,
									"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                         "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm);

                                                 }
                                             






                                                              $devices_config_para = array("resistance"=>$fan_resistance,
                        "lmin"=>$fan_lmin,
                        "lmax"=>$fan_lmax,
                        "frequency"=>$fan_frequency,
                        "pole_pairs"=>$fan_pole_pairs,
                        "open loop current"=>$fan_open_loop_current,
                        "power limit"=>$fan_power_limit,
                        "torque limit"=>$fan_torque_limit,
                        "speed1"=>$fan_speed1,
                        "speed2"=>$fan_speed2,
                        "speed3"=>$fan_speed3,
                        "speed4"=>$fan_speed4,
                         "speed5"=>$fan_speed5,
                         "speed6"=>$fan_speed6,
                           "speed7"=>$fan_speed7,
                           "button"=>$fan_button);
                                                                    


                                             
                                             

                                                 /*****************/


                                    }


                                  else if($devicetype_1 == 'light' && $addstatus_1 == 'confirmed')

                                   {
       

									$light_attr_1 = json_encode(["name"=>"Device Id",
																"value"=>$device_id]);

									$light_attr_2 = json_encode(["name"=>"mac",
																"value"=>$mac]);
									$light_attri[] = json_decode($light_attr_1, true);
									$light_attri[] = json_decode($light_attr_2, true);
									$light_att=json_encode($light_attri);
									//echo $light_att;

									$light_P_1 = json_encode(["name" => "name",
														  "data_type"=> "String",
														 "properties"=> ["read","write"],
															   "type"=>"efactor.param.name"]);

									$light_P_2 = json_encode(["name" => "power",
														  "data_type"=> "bool",
														 "properties"=> ["read","write"],
                                                                        "type"=>"efactor.param.power",
															"ui_type"=>"efactor.ui.toggle"]);

									
									$light_p[]= json_decode($light_P_1, true);
									$light_p[]= json_decode($light_P_2, true);
									
									//echo $light_p;

									$light_par=json_encode($light_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$light_attri,
															"params"=>$light_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.lightbulb","brand_logo"=>$url]);
									$devices_list[] = json_decode($devices_1, true);

									//echo $devices;

									/********************/

									$devices_para_arr= array("brightness" => intval($brightness),
                                                                       "output" => true,
                                                                        "power"=>$power,
                                                                       "name"=>$name);



									/*******************/

	                               

                                            /********************/
     
                                  }

                                 else if($devicetype_1 == 'switch' && $addstatus_1 == 'confirmed')

                                  {

									/*-----------------switch--------------*/
									$switch_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$switch_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$switch_attri[] = json_decode($switch_attr_1, true);
									$switch_attri[] = json_decode($switch_attr_2, true);

									$switch_att=json_encode($switch_attri);

									$switch_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$switch_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                           "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$switch_p[]= json_decode($switch_P_1, true);
									$switch_p[]= json_decode($switch_P_2, true);
									$switch_par=json_encode($switch_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$switch_attri,
															"params"=>$switch_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.switch","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


									//echo $devices_switch;

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


	                              }




                                          else if($devicetype_1 == 'cooker' && $addstatus_1 == 'confirmed')

                                  {
                                                        
									/*-----------------switch--------------*/
								$cooker_attr_1 = json_encode(["name"=>"Device Id",
											    "value"=>$device_id]);

								$cooker_attr_2 = json_encode(["name"=>"mac",
											     "value"=>$mac]);
								$cooker_attri[] = json_decode($cooker_attr_1, true);
								$cooker_attri[] = json_decode($cooker_attr_2, true);


									$cooker_att=json_encode($cooker_attri);












									$cooker_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$cooker_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                                                          "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									             

									$cooker_p[]= json_decode($cooker_P_1, true);
									$cooker_p[]= json_decode($cooker_P_2, true);
                                                                       									$cooker_par=json_encode($cooker_p);






									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$cooker_attri,
                                                                                                                 															"params"=>$cooker_p,
                                                                                                                     
														   "primary"=>"power",
															  "type"=>"efactor.device.cooker","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


									//echo $devices_switch;

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                        "skipmode"=>true,
                                                                         "cookermode"=>$cookermode,
                                                                         
                                                                          "recipe"=>$recipe,
                                                                         "name"=>$name,
                                                                        "remaining time"=>$cooktime,
                                                                        "running temperature"=>$cook_run_temp);

									/***************************/


                                                              /*----------------------------------*/





                                                   
														
                                            $devices_config_para = array("Cooker Timer"=>(int)$cooker_timer,
                                                                           "Cooking temperature"=>(int)$cooking_temperature,
                                                                           "Keep warm temperature"=>(int)$Keep_warm,
                                                                           "Pre-timer temp constant mode"=>$Pre_timer_constant_mode,
                                                                           "Pre-timer"=>(int)$Pre_timer,
                                                                           "Pre – temperature"=>(int)$Pre_temperature,
                                                                           "Coooker max-temperature"=>(int)$Coooker_max_temperature,
                                                                           "Cooker temp constant mode"=>(bool)$Cooker_constant_mode,
                                                                           "Keep Warm timer"=>(int)$Keep_Warm_timer,
                                                                           "pre-timer wait temperature constant mode"=>(bool)$pre_timer_wait_temperature_constant_mode,
                                                                           "pre-timer waiting time"=>(int)$pre_timer_waiting_time,
                                                                           "pre-timer Waiting temperature"=>(int)$pre_timer_Waiting_temperature,
                                                                           "Keep warm temp constant mode"=>(bool)$warm_constant_cut_off,
                                                                           "cook wait temp constant mode"=>(int)$cook_wait_temp_constant_mode,
                                                                           "cook waiting time"=>(int)$cook_waiting_time,
                                                                           "cook Waiting- temperature"=>(int)$cook_Waiting_temperature,
                                                                           "warm-wait temp constant mode"=>(bool)$warm_wait_temp_constant_mode,
                                                                           "warm waiting time"=>(int)$warm_waiting_time,
                                                                           "warm Waiting temperature"=>(int)$warm_Waiting_temperature,
                                                                             "OK" => (bool)$cooker_ok_button,
                                                                           "Reload Default" => (bool)$reload_default,
                                                                            "Dishes" => $recipe);



                                                                        


							/*-----------------------------------*/	














	                              }



                           /**********scanner***************21-4-23***********************/


                                           else if($devicetype_1 == 'scanner' && $addstatus_1 == 'confirmed')

                                   {

									/*-----------------switch--------------*/
									$scanner_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$scanner_attr_2 = json_encode(["name"=>"mac",
														"value"=>$mac]);
									$scanner_attri[] = json_decode($scanner_attr_1, true);
									$scanner_attri[] = json_decode($scanner_attr_2, true);

									$scanner_att=json_encode($scanner_attri);

									$scanner_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$scanner_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$scanner_p[]= json_decode($scanner_P_1, true);
									$scanner_p[]= json_decode($scanner_P_2, true);
									$scanner_par=json_encode($scanner_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$scanner_attri,
															"params"=>$scanner_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.qr-scanner","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


										}



                                      /*******************************************************/

        



                                    else if($devicetype_1 == 'outlet' && $addstatus_1 == 'confirmed')

                              {

                                          //   $outlet_attri = array();

                                                            $outlet_attri = [];
                                                   $outlet_p = [];
                                      
								/*-----------------outlet-----------------*/

								$outlet_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$outlet_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$outlet_attri[] = json_decode($outlet_attr_1, true);
									$outlet_attri[] = json_decode($outlet_attr_2, true);

									$outlet_att=json_encode($outlet_attri);

									$outlet_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$outlet_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                           "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$outlet_p[]= json_decode($outlet_P_1, true);
									$outlet_p[]= json_decode($outlet_P_2, true);
									$outlet_par=json_encode($outlet_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$outlet_attri,
															"params"=>$outlet_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.outlet","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


									//echo $devices_switch;

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


								


 
	                          }

else if($devicetype_1 == 'other' && $addstatus_1== 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$other_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$other_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$other_attri[] = json_decode($other_attr_1, true);
									$other_attri[] = json_decode($other_attr_2, true);

									$other_att=json_encode($other_attri);

									$other_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$other_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$other_p[]= json_decode($other_P_1, true);
									$other_p[]= json_decode($other_P_2, true);
									$other_par=json_encode($other_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$other_attri,
															"params"=>$other_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.other","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


										}


else if($devicetype_1 == 'HBMDNC' && $addstatus_1== 'confirmed')

                                   {
                                                          $hbmdnc_attri = array();
$hbmdnc_p = array();

									/*-----------------outlet--------------*/
									$hbmdnc_attr_1 = json_encode(["name"=>"Device Id",
											        "value"=>$device_id]);

									$hbmdnc_attr_2 = json_encode(["name"=>"mac",
											         "value"=>$mac]);
									$hbmdnc_attri[] = json_decode($hbmdnc_attr_1, true);
									$hbmdnc_attri[] = json_decode($hbmdnc_attr_2, true);

									$hbmdnc_att=json_encode($hbmdnc_attri);

									$hbmdnc_P_1 = json_encode(["name" => "name",
												 "data_type"=> "String",
											"properties"=> ["read","write"],
											"type"=>"efactor.param.name"]);

									
                                                         

									$hbmdnc_p[]= json_decode($hbmdnc_P_1, true);
									


									$hbmdnc_par=json_encode($hbmdnc_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$hbmdnc_attri,
															"params"=>$hbmdnc_p,
														   
															  "type"=>"efactor.device.hbmdnc",
                                                                                                                      "brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 
									$devices_para_arr = array("Doctor"=>(bool)$doctormanage,
                                                                                                  "Personal Assist"=>(bool)$personal_assistant,
                                                                                                   "nurse"=>(bool)$nurse,
                                                                                                    "Doctor status"=>$doctormanage_resp,
                                                                                                    "PA status"=>$personal_assistant_resp,
                                                                                                   "Nurse_Status"=>$nurse_resp,
                                                                                                    "name"=>$name);


									/***************************/



										}




else if($devicetype_1 == 'input-output' && $addstatus_1== 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$other_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$other_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$other_attri[] = json_decode($other_attr_1, true);
									$other_attri[] = json_decode($other_attr_2, true);

									$other_att=json_encode($other_attri);

									$other_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$other_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$other_p[]= json_decode($other_P_1, true);
									$other_p[]= json_decode($other_P_2, true);
									$other_par=json_encode($other_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$other_attri,
															"params"=>$other_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.input-output","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									/***************************/


										}







else if($devicetype_1 == 'exhaust_hood' && $addstatus_1== 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$exh_attr_1 = json_encode(["name"=>"Device Id",
													"value"=>$device_id]);

									$exh_attr_2 = json_encode(["name"=>"mac",
												"value"=>$mac]);
									$exh_attri[] = json_decode($exh_attr_1, true);
									$exh_attri[] = json_decode($exh_attr_2, true);

									$exh_att=json_encode($exh_attri);

                                                                       
									$exh_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$exh_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									




                                                                        

									$exh_p[]= json_decode($exh_P_1, true);
									$exh_p[]= json_decode($exh_P_2, true);
                                                                        
																		$exh_par=json_encode($exh_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$exh_attri,
															"params"=>$exh_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.exhaust-hood","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 


                                                                   /*  $devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);*/

									
                                                                     $devices_para_arr = array("power" => $power,
                                                                                                   "output" => true,
                                                                                                   "name" => $name,
                                                                                               "autoclean" =>(bool)$hood_autoclean,
                                                                                                  "airflow" => (int)$hood_speed,
                                                                                                    "lamp" =>(bool)$hood_lamp);


									/***************************/


										}









                                               else if($devicetype_1 == 'swpcp' && $addstatus_1== 'confirmed')

                                   {

									/*-----------------outlet--------------*/
									$wp_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$wp_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$wp_attri[] = json_decode($wp_attr_1, true);
									$wp_attri[] = json_decode($wp_attr_2, true);

									$wp_att=json_encode($wp_attri);


                                                                         



									$wp_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$wp_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                                                               "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

                                                                     
                                                                       
                                                                          
                
									$wp_p[]= json_decode($wp_P_1, true);
									$wp_p[]= json_decode($wp_P_2, true);
                                                                      									$wp_par=json_encode($wp_p);



									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$wp_attri,
                                                                                                              															"params"=>$wp_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.swpcp","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name,
                                                                         "Timer"=>$timer,
                                                                        "current(A)"=>$current,
                                                                        "voltage(V)"=>$voltage,
                                                                        "remaining time"=>$rem_time,
                                                                        "total watt"=>$watt);


									/***************************/
                                                                 
                                                                       $devices_config_para = array("starter relay off timer"=>$starter_relay_off_timer,
                        "start high current cutoff"=>$start_high_current_cutoff,
                        "start low current cutoff"=>$start_low_current_cutoff,
                        "Run high current cutoff"=>$Run_high_current_cutoff,
                        "Run low current cutoff"=>$Run_low_current_cutoff,
                        "high voltage cutoff"=>$high_voltage_cutoff,
                        "low voltage cutoff"=>$low_voltage_cutoff,
                        "ultra high voltage cutoff"=>$ultra_high_voltage_cutoff,
                        "ultra high current cutoff"=>$ultra_high_current_cutoff,
                        "USER SET MAX CURRENT CUTOFF LIMIT"=>$USER_SET_MAX_CURRENT_CUTOFF_LIMIT,
                        "USER AND DEVICE MAX CURRENT LIMIT"=>$USER_AND_DEVICE_MAX_CURRENT_LIMIT,
                        "LOCK ENABLE/DISABLE"=>$LOCK_ENABLE_DISABLE);
                                                                        
                                                                    /*********************************/

										}












                    }









}


}



/***************device_type***************/
/*****************params******************/


if($is_added=='1')
{


$query_gatelink = "SELECT * FROM `efactorproduct` WHERE access_token='$val' AND node_id='$device_id'";
					$doctors = $db->get_results($query_gatelink); 
					$num_gatelink = $db->num_rows($query_gatelink);
					$row_gatelink = $db->get_row($query_gatelink);



                                $tz_properties_gatelink=$row_gatelink['TZ_properties'];
                                $br_properties_gatelink=$row_gatelink['BR_properties'];
                                $umi_properties_gatelink=$row_gatelink['UMI_properties'];
                                $ec_properties_gatelink=$row_gatelink['EC_properties'];
                                $cm_properties_gatelink=$row_gatelink['CM_properties'];
                                $tu_properties_gatelink=$row_gatelink['TU_properties'];
                                $remote_pair_gatelink=$row_gatelink['remote_pair_properties'];
                                $display_properties_gatelink=$row_gatelink['display_properties'];
                                $swpcp_1ph_properties_gatelink=$row_gatelink['swpcp_1ph_properties'];
                                $fan_properties_gatelink=$row_gatelink['fan_properties'];
                                $lc_properties_gatelink=$row_gatelink['LC_properties'];

                                     $baudrate_gatelink=$row_gatelink['baudrate'];
                                     $uartmsgid_gatelink=$row_gatelink['uartmsgid'];
                                     $time_gatelink=$row_gatelink['time'];
                                     $errorcode_gatelink=$row_gatelink['errorcode'];
                                     $comm_mode_gatelink=$row_gatelink['comm_mode'];


/************************/




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
             "Efactor Protocol Posix"=>"0");

$tuning = array("Motor Tuning"=>"Enable");

$cooker_config = array("Cooker Configure"=> "Enable");


$display = array("DisplayResponse"=> "Enable");

$swpcp_1ph = array("swpcp_1ph Configure"=> "Enable");

$fan_config = array("fan Configure"=> "Enable");

$remote_pair_config = array("remote pairing"=> "Enable");





/************************/
$sub_field_1 = array("Local Control"=>$localcontrol,
                   "Time" =>$timezone,
              "Baud_Rate" =>$Baud_rate,  
            "Uart_Msg_Id" =>$uart_msg_id,
             "Error_Code" =>$error_code,
     "Communication_Mode" =>$comm_mode,
                 "Tuning" =>$tuning,
          "Cooker Configure"=>$cooker_config,
             "Display"   => $display,
             "swpcp_1ph"  =>$swpcp_1ph,
              "fan_config"  =>$fan_config,
              "remote_pairing" =>$remote_pair_config);

//$sub_field = array_merge_recursive($device_para_fan[0],$device_para_light[0],$device_para_switch[0],$sub_field_1);



/********************************************/

/*******************************************/



$device_para[$num_5]=	$sub_field_1;

						  
$sub_field = array();


foreach ($device_para as $device) {
    foreach ($device as $name => $values) {
        $sub_field[$name] = $values;
    }
}






/*******************************************/
//$sub_field = $device_para;

/***************/
$s_p_1_1 = json_encode(["data_type"=> "string",           
                             "name"=> "TZ",
                       "properties"=> explode(",",$tz_properties_gatelink),
                             "type"=> "efactor.param.tz"]);

$s_p_1_2 = json_encode(["data_type"=> "string",
                             "name"=> "TZ-POSIX",
                       "properties"=> explode(",",$tz_properties_gatelink),
                             "type"=> "efactor.param.tz_posix"]);



$s_p_1_3[] = json_decode($s_p_1_1, true);
$s_p_1_3[] = json_decode($s_p_1_2, true);

$s_p_1=  json_encode(["name"=> "Time",           
                    "params"=> $s_p_1_3,
                      "type"=> "efactor.service.time"]);

//echo $s_p_1;


//baud rate
$s_p_2_1 = json_encode(["data_type"=> "string",           
                             "name"=> "BR",
                       "properties"=> explode(",",$br_properties_gatelink),
                             "type"=> "efactor.param.br"]);                        

$s_p_2_2 = json_encode(["data_type"=> "string",
                             "name"=> "BR-POSIX",
                       "properties"=> explode(",",$br_properties_gatelink),
                             "type"=> "efactor.param.br_posix"]);


$s_p_2_3[] = json_decode($s_p_2_1, true);
$s_p_2_3[] = json_decode($s_p_2_2, true);

$s_p_2=  json_encode(["name"=> "Baud_Rate",           
                    "params"=> $s_p_2_3,
                      "type"=> "efactor.service.baudrate"]);

//echo $s_p_2;


//Uart MSG ID
$s_p_3_1 = json_encode(["data_type" => "string",           
                             "name" => "UART_MSG_ID",
                       "properties" => explode(",",$umi_properties_gatelink),
                             "type" => "efactor.param.uart_msg_id"]);                           

$s_p_3_2 = json_encode(["data_type"=> "string",
                             "name"=> "UART_MSG_ID POSIX",
                       "properties"=> explode(",",$umi_properties_gatelink),
                             "type"=> "efactor.param.uart_msg_id_posix"]);

$s_p_3_3[] = json_decode($s_p_3_1, true);
$s_p_3_3[] = json_decode($s_p_3_2, true);

$s_p_3=  json_encode(["name"=> "Uart_Msg_Id",           
                    "params"=> $s_p_3_3,
                     "type"=> "efactor.service.uartmsgid"]);

//echo $s_p_3;


//Error code
$s_p_4_1 = json_encode(["data_type"=> "string",           
                    "name"=> "ERROR CODE",
              "properties"=> explode(",",$ec_properties_gatelink),
                     "type"=> "efactor.param.error code"]);   
                        

$s_p_4_2 = json_encode(["data_type"=> "string",
                      "name"=> "ERROR CODE POSIX",
                "properties"=> explode(",",$ec_properties_gatelink),
                      "type"=> "efactor.param.error code posix"]);

$s_p_4_3[] = json_decode($s_p_4_1, true);
$s_p_4_3[] = json_decode($s_p_4_2, true);

$s_p_4=  json_encode(["name"=> "Error_Code",           
                    "params"=> $s_p_4_3,
                     "type"=> "efactor.service.errorcode"]);

//echo $s_p_4;


//Communication Mode
$s_p_5_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Efactor Protocol",
              "properties"=> explode(",",$cm_properties_gatelink),
                     "type"=> "efactor.param.efactor protocol"]);  

$s_p_5_2 = json_encode(["data_type"=> "string",
                      "name"=> "Efactor Protocol Posix",
                "properties"=> explode(",",$cm_properties_gatelink),
                      "type"=> "efactor.param.efactor protocol posix"]);


$s_p_5_3[] = json_decode($s_p_5_1, true);
$s_p_5_3[] = json_decode($s_p_5_2, true);

$s_p_5=  json_encode(["name"=> "Communication_Mode",           
                    "params"=> $s_p_5_3,
                     "type"=> "efactor.service.communicationmode"]);

//echo $s_p_5;
//tuning
$s_p_6_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Motor Tuning",
              "properties"=> explode(",",$tu_properties_gatelink),
                     "type"=> "efactor.param.motor tuning"]); 

                            
$s_p_6_3[] = json_decode($s_p_6_1, true);

$s_p_6=  json_encode(["name"=> "Tuning",           
                    "params"=> $s_p_6_3,
                     "type"=> "efactor.service.Tuning"]);

//echo $s_p_6;


//Local Control
$s_p_7_1 = json_encode(["data_type"=> "string",           
                    "name"=> "POP",
              "properties"=> explode(",",$lc_properties_gatelink),
                     "type"=> "efactor.param.local_control_pop"]); 

$s_p_7_2 = json_encode(["data_type"=> "int",
                      "name"=> "Type",
                "properties"=> explode(",",$lc_properties_gatelink),
                      "type"=> "efactor.param.local_control_type"]);

$s_p_7_3[] = json_decode($s_p_7_1, true);
$s_p_7_3[] = json_decode($s_p_7_2, true);

$s_p_7=  json_encode(["name"=> "Local_Control",           
                    "params"=> $s_p_7_3,
                     "type"=> "efactor.service.local_control"]);

//echo $s_p_7;


//display
$s_p_9_1 = json_encode(["data_type"=> "string",           
                    "name"=> "DisplayResponse",
              "properties"=> explode(",",$display_properties),
                     "type"=> "efactor.param.displayresponse"]); 

                            
$s_p_9_3[] = json_decode($s_p_9_1, true);

$s_p_9=  json_encode(["name"=> "DisplayResponse",           
                    "params"=> $s_p_9_3,
                     "type"=> "efactor.service.displayresponse"]);




//swpcp_1ph Configure
$s_p_10_1 = json_encode(["data_type"=> "string",           
                    "name"=> "SWP Single phase Configure",
              "properties"=> explode(",",$swpcp_1ph_properties),
                     "type"=> "efactor.param.swpcp_1ph_configure"]); 

                            
$s_p_10_3[] = json_decode($s_p_10_1, true);

$s_p_10=  json_encode(["name"=> "swpcp_1ph Configure",           
                    "params"=> $s_p_10_3,
                     "type"=> "efactor.service.swpcp_1ph_configure"]);



//fan Configure
$s_p_11_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Fan Configure",
              "properties"=> explode(",",$fan_properties),
                     "type"=> "efactor.param.fan_configure"]); 

                            
$s_p_11_3[] = json_decode($s_p_11_1, true);

$s_p_11=  json_encode(["name"=> "Fan Configure",           
                    "params"=> $s_p_11_3,
                     "type"=> "efactor.service.fan_configure"]);



//remote_pairing Configure
$s_p_12_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Remote Pairing Configure",
              "properties"=> explode(",",$remote_pair_properties),
                     "type"=> "efactor.param.remote_pairing"]); 

                            
$s_p_12_3[] = json_decode($s_p_12_1, true);

$s_p_12=  json_encode(["name"=> "Remote Pairing",           
                    "params"=> $s_p_12_3,
                     "type"=> "efactor.service.remote_pairing"]);



$s_p[] = json_decode($s_p_1, true);
$s_p[] = json_decode($s_p_2, true);
$s_p[] = json_decode($s_p_3, true);
$s_p[] = json_decode($s_p_4, true);
$s_p[] = json_decode($s_p_5, true);
$s_p[] = json_decode($s_p_6, true);
$s_p[] = json_decode($s_p_7, true);

$s_p[] = json_decode($s_p_9, true);
$s_p[] = json_decode($s_p_10, true);
$s_p[] = json_decode($s_p_11, true);
$s_p[] = json_decode($s_p_12, true);


/************/
}

else
{


if($name=='Gateway')

{


$localcontrol =array("POP"=> "a8d8e5ce",
                    "Type"=> 0);

$timezone = array("TZ"=>$time,
        "TZ-POSIX"=>"PST8PDT,M3.2.0,M11.1.0");


$Baud_rate = array("BR"=>$baudrate,
             "BR-POSIX"=>"0");

$uart_msg_id = array("UART_MSG_ID"=>$uartmsgid,
               "UART_MSG_ID POSIX"=>"0");

$error_code = array("ERROR CODE"=>$errorcode,
              "ERROR CODE POSIX"=>"0");


$comm_mode = array("Efactor Protocol"=>$comm_mode,
             "Efactor Protocol Posix"=>"0");

$tuning = array("Motor Tuning"=>"Enable");

$gateway_par =array("power"=> $power,
                    "output"=> true,
                    "name"=>$name);

$display = array("DisplayResponse"=> "Enable");

$swpcp_1ph = array("Single Phase Motor Configure"=> "Enable");


$remote_pair_config = array("remote pairing"=> "Enable");




if(curr_devicetype != 'NULL')
{


/************************/





  $sub_field = array($curr_devicetype=>$gateway_par,
                 "Local_Control"=>$localcontrol,
                   "Time" =>$timezone,
              "Baud_Rate" =>$Baud_rate,  
            "Uart_Msg_Id" =>$uart_msg_id,
             "Error_Code" =>$error_code,
     "Communication_Mode" =>$comm_mode,
                 "Tuning" =>$tuning,
                "Display"=>$display,
   "device_config_params"=>array($curr_devicetype=>$devices_config_para),
   "remote_pairing" =>$remote_pair_config );

}

else{

$sub_field = array($name=>$gateway_par,
                 "Local_Control"=>$localcontrol,
                   "Time" =>$timezone,
              "Baud_Rate" =>$Baud_rate,  
            "Uart_Msg_Id" =>$uart_msg_id,
             "Error_Code" =>$error_code,
     "Communication_Mode" =>$comm_mode,
                 "Tuning" =>$tuning,
                "Display"=>$display,
   "device_config_params"=>array($curr_devicetype=>$devices_config_para),
    "remote_pairing" =>$remote_pair_config);

}

/**********************/

$s_p_1_1 = json_encode(["data_type"=> "string",           
                             "name"=> "TZ",
                       "properties"=> explode(",",$tz_properties),
                             "type"=> "efactor.param.tz"]);

$s_p_1_2 = json_encode(["data_type"=> "string",
                             "name"=> "TZ-POSIX",
                       "properties"=> explode(",",$tz_properties),
                             "type"=> "efactor.param.tz_posix"]);



$s_p_1_3[] = json_decode($s_p_1_1, true);
$s_p_1_3[] = json_decode($s_p_1_2, true);

$s_p_1=  json_encode(["name"=> "Time",           
                    "params"=> $s_p_1_3,
                      "type"=> "efactor.service.time"]);

//echo $s_p_1;


//baud rate
$s_p_2_1 = json_encode(["data_type"=> "string",           
                             "name"=> "BR",
                       "properties"=> explode(",",$br_properties),
                             "type"=> "efactor.param.br"]);                        

$s_p_2_2 = json_encode(["data_type"=> "string",
                             "name"=> "BR-POSIX",
                       "properties"=> explode(",",$br_properties),
                             "type"=> "efactor.param.br_posix"]);


$s_p_2_3[] = json_decode($s_p_2_1, true);
$s_p_2_3[] = json_decode($s_p_2_2, true);

$s_p_2=  json_encode(["name"=> "Baud_Rate",           
                    "params"=> $s_p_2_3,
                      "type"=> "efactor.service.baudrate"]);

//echo $s_p_2;


//Uart MSG ID
$s_p_3_1 = json_encode(["data_type" => "string",           
                             "name" => "UART_MSG_ID",
                       "properties" => explode(",",$umi_properties),
                             "type" => "efactor.param.uart_msg_id"]);                           

$s_p_3_2 = json_encode(["data_type"=> "string",
                             "name"=> "UART_MSG_ID POSIX",
                       "properties"=> explode(",",$umi_properties),
                             "type"=> "efactor.param.uart_msg_id_posix"]);

$s_p_3_3[] = json_decode($s_p_3_1, true);
$s_p_3_3[] = json_decode($s_p_3_2, true);

$s_p_3=  json_encode(["name"=> "Uart_Msg_Id",           
                    "params"=> $s_p_3_3,
                     "type"=> "efactor.service.uartmsgid"]);

//echo $s_p_3;


//Error code
$s_p_4_1 = json_encode(["data_type"=> "string",           
                    "name"=> "ERROR CODE",
              "properties"=> explode(",",$ec_properties),
                     "type"=> "efactor.param.error code"]);   
                        

$s_p_4_2 = json_encode(["data_type"=> "string",
                      "name"=> "ERROR CODE POSIX",
                "properties"=> explode(",",$ec_properties),
                      "type"=> "efactor.param.error code posix"]);

$s_p_4_3[] = json_decode($s_p_4_1, true);
$s_p_4_3[] = json_decode($s_p_4_2, true);

$s_p_4=  json_encode(["name"=> "Error_Code",           
                    "params"=> $s_p_4_3,
                     "type"=> "efactor.service.errorcode"]);

//echo $s_p_4;


//Communication Mode
$s_p_5_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Efactor Protocol",
              "properties"=> explode(",",$cm_properties),
                     "type"=> "efactor.param.efactor protocol"]);  

$s_p_5_2 = json_encode(["data_type"=> "string",
                      "name"=> "Efactor Protocol Posix",
                "properties"=> explode(",",$cm_properties),
                      "type"=> "efactor.param.efactor protocol posix"]);


$s_p_5_3[] = json_decode($s_p_5_1, true);
$s_p_5_3[] = json_decode($s_p_5_2, true);

$s_p_5=  json_encode(["name"=> "Communication_Mode",           
                    "params"=> $s_p_5_3,
                     "type"=> "efactor.service.communicationmode"]);

//echo $s_p_5;
//tuning
$s_p_6_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Motor Tuning",
              "properties"=> explode(",",$tu_properties),
                     "type"=> "efactor.param.motor tuning"]); 

                            
$s_p_6_3[] = json_decode($s_p_6_1, true);

$s_p_6=  json_encode(["name"=> "Tuning",           
                    "params"=> $s_p_6_3,
                     "type"=> "efactor.service.Tuning"]);

//echo $s_p_6;


//Local Control
$s_p_7_1 = json_encode(["data_type"=> "string",           
                    "name"=> "POP",
              "properties"=> explode(",",$lc_properties),
                     "type"=> "efactor.param.local_control_pop"]); 

$s_p_7_2 = json_encode(["data_type"=> "int",
                      "name"=> "Type",
                "properties"=> explode(",",$lc_properties),
                      "type"=> "efactor.param.local_control_type"]);

$s_p_7_3[] = json_decode($s_p_7_1, true);
$s_p_7_3[] = json_decode($s_p_7_2, true);

$s_p_7=  json_encode(["name"=> "Local_Control",           
                    "params"=> $s_p_7_3,
                     "type"=> "efactor.service.local_control"]);

//echo $s_p_7;


//display
$s_p_9_1 = json_encode(["data_type"=> "string",           
                    "name"=> "DisplayResponse",
              "properties"=> explode(",",$display_properties),
                     "type"=> "efactor.param.displayresponse"]); 

                            
$s_p_9_3[] = json_decode($s_p_9_1, true);

$s_p_9=  json_encode(["name"=> "DisplayResponse",           
                    "params"=> $s_p_9_3,
                     "type"=> "efactor.service.displayresponse"]);



//swpcp_1ph Configure
$s_p_10_1 = json_encode(["data_type"=> "string",           
                    "name"=> "SWP Single phase Configure",
              "properties"=> explode(",",$swpcp_1ph_properties),
                     "type"=> "efactor.param.swpcp_1ph_configure"]); 

                            
$s_p_10_3[] = json_decode($s_p_10_1, true);

$s_p_10=  json_encode(["name"=> "swpcp_1ph Configure",           
                    "params"=> $s_p_10_3,
                     "type"=> "efactor.service.swpcp_1ph_configure"]);



//fan Configure
$s_p_11_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Fan Configure",
              "properties"=> explode(",",$fan_properties),
                     "type"=> "efactor.param.fan_configure"]); 

                            
$s_p_11_3[] = json_decode($s_p_11_1, true);

$s_p_11=  json_encode(["name"=> "Fan Configure",           
                    "params"=> $s_p_11_3,
                     "type"=> "efactor.service.fan_configure"]);




//remote_pairing Configure
$s_p_12_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Remote Pairing Configure",
              "properties"=> explode(",",$remote_pair_properties),
                     "type"=> "efactor.param.remote_pairing"]); 

                            
$s_p_12_3[] = json_decode($s_p_12_1, true);

$s_p_12=  json_encode(["name"=> "Remote Pairing",           
                    "params"=> $s_p_12_3,
                     "type"=> "efactor.service.remote_pairing"]);



$s_p[] = json_decode($s_p_1, true);
$s_p[] = json_decode($s_p_2, true);
$s_p[] = json_decode($s_p_3, true);
$s_p[] = json_decode($s_p_4, true);
$s_p[] = json_decode($s_p_5, true);
$s_p[] = json_decode($s_p_6, true);
$s_p[] = json_decode($s_p_7, true);

$s_p[] = json_decode($s_p_9, true);
$s_p[] = json_decode($s_p_10, true);
$s_p[] = json_decode($s_p_11, true);
$s_p[] = json_decode($s_p_12, true);

/*********************/
}
else{


$localcontrol =array("POP"=> "a8d8e5ce",
                    "Type"=> 0);

$timezone = array("TZ"=>$time,
        "TZ-POSIX"=>"PST8PDT,M3.2.0,M11.1.0");


$Baud_rate = array("BR"=>$baudrate,
             "BR-POSIX"=>"0");

$uart_msg_id = array("UART_MSG_ID"=>$uartmsgid,
               "UART_MSG_ID POSIX"=>"0");

$error_code = array("ERROR CODE"=>$errorcode,
              "ERROR CODE POSIX"=>"0");


$comm_mode = array("Efactor Protocol"=>$comm_mode,
             "Efactor Protocol Posix"=>"0");

$tuning = array("Motor Tuning"=>"Enable");

$cooker_config = array("Cooker Configure"=> "Enable");

$display = array("DisplayResponse"=> "Enable");

$swpcp_1ph = array("Single Phase Motor Configure"=> "Enable");

$remote_pair_config = array("remote pairing"=> "Enable");



if(curr_devicetype != 'NULL')
{
$sub_field = array($curr_devicetype=>$devices_para_arr,
                   "Local_Control"=>$localcontrol,
                   "Time" =>$timezone,
              "Baud_Rate" =>$Baud_rate,  
            "Uart_Msg_Id" =>$uart_msg_id,
             "Error_Code" =>$error_code,
     "Communication_Mode" =>$comm_mode,
                 "Tuning" =>$tuning,
                 "Cooker Configure"=>$cooker_config,
                 "Display" => $display,
   "device_config_params"=>array($curr_devicetype=>$devices_config_para),
    "remote_pairing" => $remote_pair_config);




}
else{
$sub_field = array($devicetype=>$devices_para_arr,
                   "Local_Control"=>$localcontrol,
                   "Time" =>$timezone,
              "Baud_Rate" =>$Baud_rate,  
            "Uart_Msg_Id" =>$uart_msg_id,
             "Error_Code" =>$error_code,
     "Communication_Mode" =>$comm_mode,
                 "Tuning" =>$tuning,
"Cooker Configure"=>$cooker_config,
         "Display"=>$display,
   "device_config_params"=>array($curr_devicetype=>$devices_config_para),
     "remote_pairing" => $remote_pair_config);

}
/************************/

$s_p_1_1 = json_encode(["data_type"=> "string",           
                             "name"=> "TZ",
                       "properties"=> explode(",",$tz_properties),
                             "type"=> "efactor.param.tz"]);

$s_p_1_2 = json_encode(["data_type"=> "string",
                             "name"=> "TZ-POSIX",
                       "properties"=> explode(",",$tz_properties),
                             "type"=> "efactor.param.tz_posix"]);



$s_p_1_3[] = json_decode($s_p_1_1, true);
$s_p_1_3[] = json_decode($s_p_1_2, true);

$s_p_1=  json_encode(["name"=> "Time",           
                    "params"=> $s_p_1_3,
                      "type"=> "efactor.service.time"]);

//echo $s_p_1;


//baud rate
$s_p_2_1 = json_encode(["data_type"=> "string",           
                             "name"=> "BR",
                       "properties"=> explode(",",$br_properties),
                             "type"=> "efactor.param.br"]);                        

$s_p_2_2 = json_encode(["data_type"=> "string",
                             "name"=> "BR-POSIX",
                       "properties"=> explode(",",$br_properties),
                             "type"=> "efactor.param.br_posix"]);


$s_p_2_3[] = json_decode($s_p_2_1, true);
$s_p_2_3[] = json_decode($s_p_2_2, true);

$s_p_2=  json_encode(["name"=> "Baud_Rate",           
                    "params"=> $s_p_2_3,
                      "type"=> "efactor.service.baudrate"]);

//echo $s_p_2;


//Uart MSG ID
$s_p_3_1 = json_encode(["data_type" => "string",           
                             "name" => "UART_MSG_ID",
                       "properties" => explode(",",$umi_properties),
                             "type" => "efactor.param.uart_msg_id"]);                           

$s_p_3_2 = json_encode(["data_type"=> "string",
                             "name"=> "UART_MSG_ID POSIX",
                       "properties"=> explode(",",$umi_properties),
                             "type"=> "efactor.param.uart_msg_id_posix"]);

$s_p_3_3[] = json_decode($s_p_3_1, true);
$s_p_3_3[] = json_decode($s_p_3_2, true);

$s_p_3=  json_encode(["name"=> "Uart_Msg_Id",           
                    "params"=> $s_p_3_3,
                     "type"=> "efactor.service.uartmsgid"]);

//echo $s_p_3;


//Error code
$s_p_4_1 = json_encode(["data_type"=> "string",           
                    "name"=> "ERROR CODE",
              "properties"=> explode(",",$ec_properties),
                     "type"=> "efactor.param.error code"]);   
                        

$s_p_4_2 = json_encode(["data_type"=> "string",
                      "name"=> "ERROR CODE POSIX",
                "properties"=> explode(",",$ec_properties),
                      "type"=> "efactor.param.error code posix"]);

$s_p_4_3[] = json_decode($s_p_4_1, true);
$s_p_4_3[] = json_decode($s_p_4_2, true);

$s_p_4=  json_encode(["name"=> "Error_Code",           
                    "params"=> $s_p_4_3,
                     "type"=> "efactor.service.errorcode"]);

//echo $s_p_4;


//Communication Mode
$s_p_5_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Efactor Protocol",
              "properties"=> explode(",",$cm_properties),
                     "type"=> "efactor.param.efactor protocol"]);  

$s_p_5_2 = json_encode(["data_type"=> "string",
                      "name"=> "Efactor Protocol Posix",
                "properties"=> explode(",",$cm_properties),
                      "type"=> "efactor.param.efactor protocol posix"]);


$s_p_5_3[] = json_decode($s_p_5_1, true);
$s_p_5_3[] = json_decode($s_p_5_2, true);

$s_p_5=  json_encode(["name"=> "Communication_Mode",           
                    "params"=> $s_p_5_3,
                     "type"=> "efactor.service.communicationmode"]);

//echo $s_p_5;
//tuning
$s_p_6_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Motor Tuning",
              "properties"=> explode(",",$tu_properties),
                     "type"=> "efactor.param.motor tuning"]); 

                            
$s_p_6_3[] = json_decode($s_p_6_1, true);

$s_p_6=  json_encode(["name"=> "Tuning",           
                    "params"=> $s_p_6_3,
                     "type"=> "efactor.service.Tuning"]);

//echo $s_p_6;


//Local Control
$s_p_7_1 = json_encode(["data_type"=> "string",           
                    "name"=> "POP",
              "properties"=> explode(",",$lc_properties),
                     "type"=> "efactor.param.local_control_pop"]); 

$s_p_7_2 = json_encode(["data_type"=> "int",
                      "name"=> "Type",
                "properties"=> explode(",",$lc_properties),
                      "type"=> "efactor.param.local_control_type"]);

$s_p_7_3[] = json_decode($s_p_7_1, true);
$s_p_7_3[] = json_decode($s_p_7_2, true);

$s_p_7=  json_encode(["name"=> "Local_Control",           
                    "params"=> $s_p_7_3,
                     "type"=> "efactor.service.local_control"]);

//echo $s_p_7;










//cooker_config
$s_p_8_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Cooker Configure",
              "properties"=> explode(",",$cooker_properties),
                     "type"=> "efactor.param.Cooker Configure"]); 

                            
$s_p_8_3[] = json_decode($s_p_8_1, true);

$s_p_8=  json_encode(["name"=> "Cooker Configure",           
                    "params"=> $s_p_8_3,
                     "type"=> "efactor.service.cooker_configure"]);

//echo $s_p_6;




//display
$s_p_9_1 = json_encode(["data_type"=> "string",           
                    "name"=> "DisplayResponse",
              "properties"=> explode(",",$display_properties),
                     "type"=> "efactor.param.displayresponse"]); 

                            
$s_p_9_3[] = json_decode($s_p_9_1, true);

$s_p_9=  json_encode(["name"=> "DisplayResponse",           
                    "params"=> $s_p_9_3,
                     "type"=> "efactor.service.displayresponse"]);



 


//swpcp_1ph Configure
$s_p_10_1 = json_encode(["data_type"=> "string",           
                    "name"=> "SWP Single phase Configure",
              "properties"=> explode(",",$swpcp_1ph_properties),
                     "type"=> "efactor.param.swpcp_1ph_configure"]); 

                            
$s_p_10_3[] = json_decode($s_p_10_1, true);

$s_p_10=  json_encode(["name"=> "swpcp_1ph Configure",           
                    "params"=> $s_p_10_3,
                     "type"=> "efactor.service.swpcp_1ph_configure"]);




//remote_pairing Configure
$s_p_12_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Remote Pairing Configure",
              "properties"=> explode(",",$remote_pair_properties),
                     "type"=> "efactor.param.remote_pairing"]); 

                            
$s_p_12_3[] = json_decode($s_p_12_1, true);

$s_p_12=  json_encode(["name"=> "Remote Pairing",           
                    "params"=> $s_p_12_3,
                     "type"=> "efactor.service.remote_pairing"]);



$s_p[] = json_decode($s_p_1, true);
$s_p[] = json_decode($s_p_2, true);
$s_p[] = json_decode($s_p_3, true);
$s_p[] = json_decode($s_p_4, true);
$s_p[] = json_decode($s_p_5, true);
$s_p[] = json_decode($s_p_6, true);
$s_p[] = json_decode($s_p_7, true);

$s_p[] = json_decode($s_p_9, true);
$s_p[] = json_decode($s_p_10, true);
$s_p[] = json_decode($s_p_12, true);
/***********************/
}


}







/************config*****************/
/***********dev_param**************/

/*$d_p_1 = json_encode(["data_type" => "String",
                                "name"=>"name",
                          "properties"=>["read","write"],
                                "type"=>"efactor.param.name"]);

$d_p_2 = json_encode(["data_type"=>"bool",
                                  "name"=>"power",
                            "properties"=>["read","write"],
                                  "type"=>"efactor.param.power",
                               "ui_type"=>"efactor.ui.toggle"]);

$max_1 = array("max"=>5,
               "min" =>0);
//$max1[] = json_decode($max_1, true);

$max2 = json_encode($max_1);

$d_p_3 = json_encode(["bounds"=>$max_1,
                      "data_type"=>"int",
                      "name"=>"Speed",
                      "properties"=>["read","write"],
                      "type"=>"efactor.param.speed",
                      "ui_type"=>"efactor.ui.slider"]);

$max_2 = array("max"=>1,
               "min" =>0);


//$max2[] = json_decode($max_2, true);
$max3 = json_encode($max_2);



$d_p_4 = json_encode(["bounds"=>$max_2,
                             "data_type"=>"int",
                            "name"=>"Directi    "type"=> "efactor.service.communicationmode"]);

//echo $s_p_5;
//tuning
$s_p_6_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Motor Tuning",
              "properties"=> explode(",",$tu_properties),
                     "type"=> "efactor.param.motor tuning"]); 

                            
$s_p_6_3[] = json_decode($s_p_6_1, true);

$s_p_6=  json_encode(["name"=> "Tuning",           
                    "params"=> $s_p_6_3,
                     "type"=> "efactor.service.Tuning"]);

//echo $s_p_6;


//Local Control
$s_p_7_1 = json_encode(["data_type"=> "string",           
                    "name"=> "POP",
              "properties"=> explode(",",$lc_properties),
                     "type"=> "efactor.param.local_control_pop"]); 

$s_p_7_2 = json_encode(["data_type"=> "int",
                      "name"=> "Type",
                "properties"=> explode(",",$lc_properties),
                      "type"=> "efactor.param.local_control_type"]);

$s_p_7_3[] = json_decode($s_p_7_1, true);
$s_p_7_3[] = json_decode($s_p_7_2, true);

$s_p_7=  json_encode(["name"=> "Local_Control",           
                    "params"=> $s_p_7_3,
                     "type"=> "efactor.service.local_control"]);

//echo $s_p_7;


//display
$s_p_9_1 = json_encode(["data_type"=> "string",           
                    "name"=> "DisplayResponse",
              "properties"=> explode(",",$display_properties),
                     "type"=> "efactor.param.displayresponse"]); 

                            
$s_p_9_3[] = json_decode($s_p_9_1, true);

$s_p_9=  json_encode(["name"=> "DisplayResponse",           
                    "params"=> $s_p_9_3,
                     "type"=> "efactor.service.displayresponse"]);


//swpcp_1ph Configure
$s_p_10_1 = json_encode(["data_type"=> "string",           
                    "name"=> "SWP Single phase Configure",
              "properties"=> explode(",",$swpcp_1ph_properties),
                     "type"=> "efactor.param.swpcp_1ph_configure"]); 

                            
$s_p_10_3[] = json_decode($s_p_10_1, true);

$s_p_10=  json_encode(["name"=> "swpcp_1ph Configure",           
                    "params"=> $s_p_10_3,
                     "type"=> "efactor.service.swpcp_1ph_configure"]);



//fan Configure
$s_p_11_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Fan Configure",
              "properties"=> explode(",",$fan_properties),
                     "type"=> "efactor.param.fan_configure"]); 

                            
$s_p_11_3[] = json_decode($s_p_11_1, true);

$s_p_11=  json_encode(["name"=> "Fan Configure",           
                    "params"=> $s_p_11_3,
                     "type"=> "efactor.service.fan_configure"]);


//remote_pairing Configure
$s_p_12_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Remote Pairing Configure",
              "properties"=> explode(",",$remote_pair_properties),
                     "type"=> "efactor.param.remote_pairing"]); 

                            
$s_p_12_3[] = json_decode($s_p_12_1, true);

$s_p_12=  json_encode(["name"=> "Remote Pairing",           
                    "params"=> $s_p_12_3,
                     "type"=> "efactor.service.remote_pairing"]);



$s_p[] = json_decode($s_p_1, true);
$s_p[] = json_decode($s_p_2, true);
$s_p[] = json_decode($s_p_3, true);
$s_p[] = json_decode($s_p_4, true);
$s_p[] = json_decode($s_p_5, true);
$s_p[] = json_decode($s_p_6, true);
$s_p[] = json_decode($s_p_7, true);

$s_p[] = json_decode($s_p_9, true);
$s_p[] = json_decode($s_p_10, true);
$s_p[] = json_decode($s_p_11, true);
$s_p[] = json_decode($s_p_12, true);


//echo json_encode($s_p);





/***********config*********/
$config_attr_1 = json_encode(["name"=>"serial_num",
                             "value"=>"1345"]);

$config_attr_2 = json_encode(["name"=>"model",
                             "value"=>"wifi"]);

$config_attr[] = json_decode($config_attr_1, true);
$config_attr[] = json_decode($config_attr_2, true);

$config_att=json_encode($config_attr);

if($moduletype=='RF' || $moduletype=='GATEWAY')
{
    $moduletype='Gateway';
}








$query_inter = "SELECT * FROM efactorproduct where node_id='$device_id'";
 $doctors = $db->get_results($query_inter); 

 $row_inter = $db->get_row($query_inter);
$model_number=$row_inter['model_number'];
$devicename=$row_inter['device_name'];
$vendor_name=$row_inter['vendor_name'];
$moduletype=$row_inter['module_type'];
$net_conn_1=$row_inter['internet'];

$info=array("fw_version"=>$model_number,
                        "model"=>$devicename,
                         "name"=>$vendor_name,
                         "type"=>$moduletype);

$config_en=array("attributes"=>$config_attr,
                        "config_version"=>"2020-03-20",
                         "devices"=>$devices_list,             
                          "info" => $info,
                         "node_id"=>$device_id,
                          "services"=>$s_p);                    //8-2

$config=json_encode($config_en);





$connectivity =array("connected"=>$net_conn_1,
                     "timestamp"=>$timestamp);

$connectivity_1[] = json_decode($connectivity, true);

$status_1 = array("connectivity" => $connectivity);


/***************************************************/



 if($shareuser == $phone)
{

//$role='secondary';
$role='primary';
}

else
{
$role='primary';
}


$node_details = json_encode(["id"=>$device_id,
                         "params"=>$sub_field,
                         "config"=>$config_en,
                           "role"=>$role,
                         "status"=>$status_1]);      //$sub_field

$node_details_1[] = json_decode($node_details, true);



if($is_added=='1')
			{



$total_1=$num_dev-$num_5;
$total=$total_1+$num_gate;

}
else if(!empty($_GET["start_id"]))
 {


if($num_link==0)

{

if($shareuser == $phone)

{



$total=$num_share;
}
else{


$total=$num_dev;}





}
else{





if($shareuser == $phone)

{

$total=$num_share;
}
else{


$total=$num_start+$num_link;}



   
}



}

else
{



if($shareuser == $phone)
{

$total=$num_share;
}
else{


$total=$num_dev;
}






}






$node = json_encode(["url"=>"action=node_details",
                      "token"=>$access_token,
                      "next_id"=>$nextid,
                       "total"=>$total,
                "node_details"=>$node_details_1,
                       "nodes"=>$device_id]);


//$endTime = microtime(true);

//$executionTime = $endTime - $startTime;






echo $node;

//echo "Execution time: " . $executionTime . " seconds";

  }


/***********************************/

//headers_remove("$Autherization");

else
{

$additionalemptyData = array(
    "url" => "action=node_details",
    "token" => $val,
   "next_id"=>null
   );


echo json_encode($additionalemptyData);
exit; 

}





}



}





/**************************************************************/




/**************************************************************/
/*********************user_nodes*******************************/
/**************************************************************/

if (isset($_GET['action']) && $_GET['action']=='user_nodes')
{

$startTime = microtime(true);
              $nodeid =$_GET['node_id'];
           
			
			$header = apache_request_headers();  
			foreach ($header as $key => $val) {
			$query = "SELECT * FROM `users` where access_token='$val'";
			//$doctors = $db->get_results($query); 
			$num = $db->num_rows($query);
			
                    //      $db->query("update efactorproduct set api_version='v3' where access_token='$val'");

	 if($num>0)
	   {
          
			
           		   
         
                                $query_1 = "SELECT * FROM efactorproduct where access_token='$val' AND node_id='$nodeid'";
						 // $doctors = $db->get_results($query_1); 
						  $row = $db->get_row($query_1); 
						  $device_id=$row['node_id'];
						  $node_id=$row['node_id'];
                                                  $address=$row['node_id'];
						  $moduletype= $row['module_type'];
                                                   
                                       /* $model_number=$row['model_number'];  
                                        $vendor_name=$row['vendor_name'];  
                                        $devicename=$row['device_name'];*/
						  $devicetype_2=$row['device_type'];
                                                  $devicetype=$row['device_type'];
						  $name=$row['name'];
						  $speed=$row['speed'];
						  $hue=$row['Hue'];
						  $mac=$row['mac'];
                                                   $curr_devicetype=$row['current_devicetype'];
                                                   $recipe=$row['recipe'];
                                                   $user_priority=$row['user_priority'];
                                                   $cookermode=$row['cookermode'];
                                                   $cooker_led=$row['cooker_led'];
                                                   $cooktime=$row['cook_time'];
                                             $cook_run_temp=$row['cook_run_temp'];
                                            $current=$row['current'];
                                            $voltage=$row['ac_float'];
                                           $bus_voltage=$row['bus_voltage'];
                                             $rpm=$row['rpm'];   
                                             $timer=$row['timer'];
                                              $watt=$row['power_watt'];
                                             $rem_time=$row['rem_time'];
                                             $hood_autoclean=$row['hood_autoclean'];
                                              $hood_speed=$row['hood_speed'];
                                              $hood_lamp=$row['hood_lamp']; 

                                            /*  $nurse=$row['nurse'];
                                              $personal_assistant=$row['personal_assistant'];
                                              $doctormanage=$row['doctor'];
                                              $nurse_resp=$row['nurse_resp'];
                                              $personal_assistant_resp=$row['personal_assistant_resp'];
                                              $doctormanage_resp=$row['doctor_resp'];*/


                                              $nurse=filter_var($row['nurse'], FILTER_VALIDATE_BOOLEAN);
$personal_assistant=filter_var($row['personal_assistant'], FILTER_VALIDATE_BOOLEAN);
$doctormanage=filter_var($row['doctor'], FILTER_VALIDATE_BOOLEAN);
$nurse_resp=$row['nurse_resp'];      
$personal_assistant_resp=$row['personal_assistant_resp'];                    
$doctormanage_resp=$row['doctor_resp'];                      



 


                                            $model_number=$row['model_number'];
                                                           $devicename=$row['device_name'];
                                                           $vendor_name=$row['vendor_name'];
                                                           $moduletype=$row['module_type'];
                                                           $net_conn_1=$row['internet'];
                                                           $access_token=$row['access_token'];


                                                                if($devicetype_2 == 'exhaust_hood')
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




                                           if($devicetype_2 == 'swpcp')
{
                                           //swpcp
                                            $starter_relay_off_timer = $row['starter_relay_off_timer'];  
$start_high_current_cutoff = $row['start_high_current_cutoff'];  
$start_low_current_cutoff= $row['start_low_current_cutoff'];  
$Run_high_current_cutoff = $row['Run_high_current_cutoff'];  
$Run_low_current_cutoff = $row['Run_low_current_cutoff'];  
$high_voltage_cutoff = $row['high_voltage_cutoff'];  
$low_voltage_cutoff = $row['low_voltage_cutoff'];  
$ultra_high_voltage_cutoff = $row['ultra_high_voltage_cutoff'];  
$ultra_high_current_cutoff = $row['ultra_high_current_cutoff'];  
$USER_SET_MAX_CURRENT_CUTOFF_LIMIT = $row['USER_SET_MAX_CURRENT_CUTOFF_LIMIT'];  
$USER_AND_DEVICE_MAX_CURRENT_LIMIT = $row['USER_AND_DEVICE_MAX_CURRENT_LIMIT'];  
$LOCK_ENABLE_DISABLE = (bool)$row['LOCK_ENABLE_DISABLE'];  
}


if($devicetype_2 == 'cooker')
                                              {
                                           //cooker

                                            $cooker_timer = $row['cooker_timer_1'];  
                                            $cooking_temperature = $row['cooker_max_temperature_1'];    
                                            $Keep_warm= $row['Keep_warm_1'];  
                                            $Pre_timer_constant_mode = $row['Pre_timer_constant_mode'];  
                                            $Pre_timer = $row['Pre_timer'];  
                                            $Pre_temperature = $row['Pre_temperature'];  
                                            $Coooker_max_temperature = $row['Coooker_max_temperature'];  
                                            $Cooker_constant_mode = $row['Cooker_constant_mode'];  
                                            $Keep_Warm_timer = $row['Keep_Warm_timer_1'];  
                                            $Dishes = $row['dish'];  
                                            $pre_timer_wait_temperature_constant_mode = $row['pre_timer_wait_temperature_constant_mode'];  
                                            $pre_timer_waiting_time = $row['pre_timer_waiting_time'];
                                            $pre_timer_Waiting_temperature = $row['pre_timer_Waiting_temperature'];  
                                            $warm_constant_cut_off = $row['warm_constant_cut_off'];  
                                            $cook_wait_temp_constant_mode = $row['cook_wait_temp_constant_mode'];  
                                            $cook_waiting_time = $row['cook_waiting_time'];  
                                            $cook_Waiting_temperature = $row['cook_Waiting_temperature'];  
                                            $warm_wait_temp_constant_mode = $row['warm_wait_temp_constant_mode'];  
                                            $warm_waiting_time = $row['warm_waiting_time'];  
                                            $warm_Waiting_temperature = $row['warm_Waiting_temperature'];
                                            $cooker_ok_button = $row['cooker_ok_button']; 
                                            $reload_default = $row['reload_default'];  


                                         }

                                                  //   echo 'echo 6';



                                           if($devicetype_2 == 'fan')
                                             {
                                           //fan
                                            $fan_resistance = $row['fan_resistance'];  
                                            $fan_lmin = $row['fan_lmin'];  
                                            $fan_lmax= $row['fan_lmax'];  
                                            $fan_frequency = $row['fan_frequency'];  
                                            $fan_pole_pairs = $row['fan_pairs'];  
                                            $fan_open_loop_current = $row['fan_open_loop_current'];  
                                            $fan_power_limit = $row['fan_power_limit'];  
                                            $fan_torque_limit = $row['fan_torque_limit'];  
                                            $fan_speed1 = $row['fan_speed1'];  
                                            $fan_speed2= $row['fan_speed2'];
                                            $fan_speed3 = $row['fan_speed3'];  
                                            $fan_speed4= $row['fan_speed4'];
                                            $fan_speed5 = $row['fan_speed5'];  
                                            $fan_speed6= $row['fan_speed6'];
                                            $fan_speed7 = $row['fan_speed7'];  
                                            $fan_button= $row['fan_button'];
                                             }
                                                  
                                                  
						  $direction=$row['direction'];
                                $brightness=$row['brightness'];
						  //$power=$row['power'];
						  $baudrate=$row['baudrate'];
						  //$led=$row['led_indication'];
						  $power=filter_var($row['power'], FILTER_VALIDATE_BOOLEAN);
						  $led = filter_var($row['led_indication'], FILTER_VALIDATE_BOOLEAN);
                                $devicename=$row['device_name'];

                                $tz_properties=$row['TZ_properties'];
                                $br_properties=$row['BR_properties'];
                                $umi_properties=$row['UMI_properties'];
                                $ec_properties=$row['EC_properties'];
                                $cm_properties=$row['CM_properties'];
                                $tu_properties=$row['TU_properties'];
                                $remote_pair_properties=$row['remote_pair_properties'];
                                $lc_properties=$row['LC_properties'];
                                $display_properties=$row['display_properties'];
                                 $swpcp_1ph_properties=$row['swpcp_1ph_properties'];
                                 $fan_properties=$row['fan_properties'];
                                $cooker_properties=$row['cooker_properties'];
       
                                  $baudrate=$row['baudrate'];
                                     $uartmsgid=$row['uartmsgid'];
                                     $time=$row['time'];
                                     $errorcode=$row['errorcode'];
                                     $comm_mode=$row['comm_mode'];
                                  /*   $model_number=$row['model_number'];
                                     $vendor_name=$row['vendor_name'];*/







    if($devicetype_2 == 'Gateway')
                             {


                                     
                                           $gate_attr_1 = json_encode(["name"=>"Device Id",
														  "value"=>$device_id]);

								$gate_attr_2 = json_encode(["name"=>"mac",
														  "value"=>$mac]);
								$gate_attri[] = json_decode($gate_attr_1, true);
								$gate_attri[] = json_decode($gate_attr_2, true);

								$gate_att=json_encode($fan_attri);



                               






								$G_Params = json_encode(["name" => "name",
														  "type"=> "efactor.param.name",
													 "data_type"=>"string",
													"properties"=> ["read","write"]]);


                                      $G_Params_2=json_encode(["name"=> "power",
                                                             "data_type"=> "bool",
                                                             "properties"=>["read","write"],
                                                                "type"=>"efactor.param.power",
                                                             "ui_type"=>"efactor.ui.toggle"]);


							   $G_p_1[] = json_decode($G_Params, true);
                                      $G_p_1[] = json_decode($G_Params_2, true);

							   $Gate_Dev= json_encode(["name" => $curr_devicetype,
                                                               "attributes"=>$gate_attri,
											      "type"=> "efactor.device.gateway",
                                                               "params"=> $G_p_1]);
                                  



							   $devices_list[] = json_decode($Gate_Dev, true);


                                           $device_para_gateway_1 = array("power"=> $power,
                                                                        "output" => true,
                                                                         "name"=>$name);


                                                  

                                           $devices_para_arr= array($curr_devicetype => $device_para_gateway_1);
                                      




                             }


                            


                                               



                            else if($devicetype_2 == 'fan')

                             {

                                                               $fan_attri = array();
        
								$fan_attr_1 = json_encode(["name"=>"Device Id",
														  "value"=>$device_id]);

								$fan_attr_2 = json_encode(["name"=>"mac",
														  "value"=>$mac]);
								$fan_attri[] = json_decode($fan_attr_1, true);
								$fan_attri[] = json_decode($fan_attr_2, true);

								$fan_att=json_encode($fan_attri);



                                                        $fan_DCP_1 = json_encode(["name" => "resistance",
											      "data_type"=> "float",
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.resistance",
                                                                                                  "ui_type"=> "efactor.ui.resistance"]);   

                                                                         
                                                                            

                                                                       $fan_DCP_3 = json_encode(["name" => "lmin",
											      "data_type"=> "float",
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.lmin",
                                                                                                  "ui_type"=> "efactor.ui.lmin"]);

                                                                        

                                                                         $fan_DCP_4 = json_encode(["name" => "lmax",
											      "data_type"=> "float",
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.lmax",
                                                                                                  "ui_type"=> "efactor.ui.lmax"]);

                                                                        


                                                                        $fan_DCP_5 = json_encode(["name" => "frequency",
											      "data_type"=> "int",               
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.frequency",
                                                                                                  "ui_type"=> "efactor.ui.frequency"]);

                                                                        

                                                                         $fan_DCP_6 = json_encode(["name" => "pole pairs",
											      "data_type"=> "short",
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.pole pairs",
                                                                                                  "ui_type"=> "efactor.ui.pole pairs"]);   

                                                                           
                                                                
                                                                
                                                                       $fan_DCP_7 = json_encode(["name" => "open loop current",
											      "data_type"=> "float",
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.open loop current",
                                                                                                  "ui_type"=> "efactor.ui.open loop current"]);   
                                                                           

                                                                       $fan_DCP_8 = json_encode(["name" => "power limit",
											      "data_type"=> "short",
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.power limit",
                                                                                                  "ui_type"=> "efactor.ui.power limit"]);

                                                                        

                                                                         $fan_DCP_9 = json_encode(["name" => "torque limit",
											      "data_type"=> "int",
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.torque limit",
                                                                                                  "ui_type"=> "efactor.ui.torque limit"]);

                                                                         

                                                                        $fan_DCP_10 = json_encode(["name" => "speed1",
											      "data_type"=> "short",
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.speed1",
                                                                                                  "ui_type"=> "efactor.ui.speed1"]);


                                                                        

                                                                         $fan_DCP_11 = json_encode(["name" => "speed2",
											      "data_type"=> "short",
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.speed2",
                                                                                                  "ui_type"=> "efactor.ui.speed2"]);

                                                                        

                                                                        $fan_DCP_12 = json_encode(["name" => "speed3",
											      "data_type"=> "short",
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.speed3",
                                                                                                  "ui_type"=> "efactor.ui.speed3"]);
                                                                     

                                                                        $fan_DCP_13 = json_encode(["name" => "speed4",
											      "data_type"=> "short",
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.speed4",
                                                                                                  "ui_type"=> "efactor.ui.speed4"]);



                                                                       $fan_DCP_14 = json_encode(["name" => "speed5",
											      "data_type"=> "short",
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.speed5",
                                                                                                  "ui_type"=> "efactor.ui.speed5"]);

                                                                      $fan_DCP_15 = json_encode(["name" => "speed6",
											      "data_type"=> "short",
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.speed6",
                                                                                                  "ui_type"=> "efactor.ui.speed6"]);

                                                                        $fan_DCP_16 = json_encode(["name" => "speed7",
											      "data_type"=> "short",
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.speed7",
                                                                                                  "ui_type"=> "efactor.ui.speed7"]);


                                                                        $fan_DCP_17 = json_encode(["name" => "button",
											      "data_type"=> "bool",
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.button",
                                                                                                  "ui_type"=> "efactor.ui.button"]);
$fan_dcp = [];
$fan_dcp_values = [$fan_DCP_1, $fan_DCP_3, $fan_DCP_4, $fan_DCP_5, $fan_DCP_6, $fan_DCP_7, $fan_DCP_8, $fan_DCP_9, $fan_DCP_10, $fan_DCP_11, $fan_DCP_12, $fan_DCP_13, $fan_DCP_14, $fan_DCP_15, $fan_DCP_16, $fan_DCP_17];

foreach ($fan_dcp_values as $fan_dcp_value) {
    $fan_dcp[] = json_decode($fan_dcp_value, true);
}

$fan_dcp_par = json_encode($fan_dcp);




                                

								   $fan_P_1 = json_encode(["name" => "name",
														  "data_type"=> "String",
														 "properties"=>["read","write"],
															   "type"=> "efactor.param.name"]);


								  $fan_P_2 = json_encode(["name" => "power",
													  "data_type"=> "bool",
													 "properties"=> ["read","write"],
														   "type"=>"efactor.param.power",
                                                                    "type"=>"efactor.param.power",
														"ui_type"=>"efactor.ui.toggle"]);

								   $max_1 = array("max"=>5,
												  "min" =>1);
								   //$max2 = json_encode($max_1);
								   $fan_P_3 = json_encode(["name" => "Speed",
														 "bounds" => $max_1,
													   "data_type"=> "int",
													  "properties"=> ["read","write"],
															"type"=>"efactor.param.speed",
														 "ui_type"=>"efactor.ui.slider"]);

                                                                 
                                                                   $max_1_1 = array("max"=>7,
												  "min" =>1);
								   //$max2 = json_encode($max_1);
								   $fan_P_3_1 = json_encode(["name" => "Speed",
														 "bounds" => $max_1_1,
													   "data_type"=> "int",
													  "properties"=> ["read","write"],
															"type"=>"efactor.param.speed",
														 "ui_type"=>"efactor.ui.slider"]);








								   $max_3 = array("max"=>6,
												  "min" =>0);

								   //$max4 = json_encode($max_1);

								   $fan_P_4 = json_encode(["name" => "led colour",
											  "bounds" => $max_3,
											 "data_type"=> "int",
											 "properties"=> ["read","write"],
											"type"=>"efactor.param.hue",
											"ui_type"=>"efactor.ui.hue-slider"]);

									$max_5 = array("max"=>1,
										       "min"=>0);

								   
                                                                 

								  $fan_P_5 = json_encode(["name" => "Direction",
														"bounds" => $max_5,
													  "data_type"=> "int",
													 "properties"=> ["read","write"],
														   "type"=>"efactor.param.direction",
														"ui_type"=>"efactor.ui.dropdown"]);


                                                                

								  $fan_P_6 = json_encode(["name" => "led",
													  "data_type"=> "bool",
													 "properties"=> ["read","write"],
														   "type"=>"efactor.param.led_indication",
														"ui_type"=>"efactor.ui.toggle"]);

                                                                 $fan_P_7 = json_encode(["name" => "buzzer",
													  "data_type"=> "bool",
													 "properties"=> ["read","write"],
														   "type"=>"efactor.param.buzzer",
														"ui_type"=>"efactor.ui.toggle"]);



                                                                    $fan_P_8 = json_encode(["name" => "current(A)",
														   "data_type"=> "String",
														  "properties"=> ["read"],
                                                                                                               "type"=>"efactor.param.power",
															 "type"=>"efactor.param.current"]);


                                                                      $fan_P_9 = json_encode(["name" => "voltage(V)",
														   "data_type"=> "String",
														  "properties"=> ["read"],
                                                                                                               "type"=>"efactor.param.power",
															 "type"=>"efactor.param.voltage"]);

                                                                       $fan_P_10 = json_encode(["name" => "rpm",
														   "data_type"=> "String",
														  "properties"=> ["read"],
                                                                                                               "type"=>"efactor.param.power",
															 "type"=>"efactor.param.rpm"]);
                                                                         



                                                                  if($model_number == "EFACT V1.2")
                                                                      {

								  $fan_p[]= json_decode($fan_P_1, true);
								  $fan_p[]= json_decode($fan_P_2, true);
                                                                  $fan_p[]= json_decode($fan_P_3_1, true);
                                                                  //$fan_p[]= json_decode($fan_P_4, true);
								  $fan_p[]= json_decode($fan_P_5, true);
								  $fan_p[]= json_decode($fan_P_6, true);
                                                                  $fan_p[]= json_decode($fan_P_7, true);
								  $fan_p[]= json_decode($fan_P_8, true);
								  $fan_p[]= json_decode($fan_P_9, true);
                                                                  $fan_p[]= json_decode($fan_P_10, true);
								  
                                                                    }



                                                                else{
                                                                  $fan_p[]= json_decode($fan_P_1, true);
								  $fan_p[]= json_decode($fan_P_2, true);
                                                                  $fan_p[]= json_decode($fan_P_3, true);
                                                                  $fan_p[]= json_decode($fan_P_4, true);
								  $fan_p[]= json_decode($fan_P_5, true);
								  $fan_p[]= json_decode($fan_P_6, true);
                                                                  //$fan_p[]= json_decode($fan_P_7, true);
								  $fan_p[]= json_decode($fan_P_8, true);
								  $fan_p[]= json_decode($fan_P_9, true);
                                                                  $fan_p[]= json_decode($fan_P_10, true);




                                                                   }



								  

								  $fan_par=json_encode($fan_p);





								 
									

									if($model_number == "EFACT V1.2")
                                                                      {
									$devices_1 = json_encode(["name"=>$curr_devicetype,
												"attributes"=>$fan_attri,
                                                                                               "device_config_params"=>$fan_dcp,
												"params"=>$fan_p,
												"primary"=>"power",
											       "type"=>"efactor.device.fan","brand_logo"=>$url]);

                                                                       }
                                                                       else{

                                                                            $devices_1 = json_encode(["name"=>$curr_devicetype,
												"attributes"=>$fan_attri,           
												"params"=>$fan_p,
												"primary"=>"power",
											       "type"=>"efactor.device.fan","brand_logo"=>$url]);

                                                                      }


									$devices_list[] = json_decode($devices_1, true);

							        

									

                                                          if($power == '0')
                                                                           {
                                                                             $led = $power;
                                                                             }



                                        /* $devices_para_arr = array("Direction" =>intval($direction),
												 "led colour"=>intval($hue),
													 "output"=>true,
													  "power"=>$power,
													  "Speed"=>intval($speed),
														"led"=>$led,
													"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                         "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm,
                                                                         "buzzer"=>(bool)$fan_buzzer);*/


                                       if($model_number == "EFACT V1.2")
                                                                      {
                                                    $devices_para_arr = array("Direction"=>intval($direction),
					                             "led colour"=>intval($hue),
							                 "output"=>true,
									"power"=>$power,
									"Speed"=>intval($speed),
									"led"=>$led,
									"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                         "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm,
                                                                         "buzzer"=>(bool)$fan_buzzer);
                                                                        

                                                                  }

                                                 else{

                                       $devices_para_arr = array("Direction"=>intval($direction),
					                             "led colour"=>intval($hue),
							                 "output"=>true,
									"power"=>$power,
									"Speed"=>intval($speed),
									"led"=>$led,
									"name"=>$name,
                                                                        "current(A)"=>$current,
                                                                         "voltage(V)"=>$bus_voltage,
                                                                           "rpm" => $rpm);

                                                 }
                                             






                                                               $devices_config_para = array("resistance"=>$fan_resistance,
                        "lmin"=>$fan_lmin,
                        "lmax"=>$fan_lmax,
                        "frequency"=>$fan_frequency,
                        "pole pairs"=>$fan_pole_pairs,
                        "open loop current"=>$fan_open_loop_current,
                        "power limit"=>$fan_power_limit,
                        "torque limit"=>$fan_torque_limit,
                        "speed1"=>$fan_speed1,
                        "speed2"=>$fan_speed2,
                        "speed3"=>$fan_speed3,
                        "speed4"=>$fan_speed4,
                         "speed5"=>$fan_speed5,
                         "speed6"=>$fan_speed6,
                           "speed7"=>$fan_speed7,
                           "button"=>$fan_button);

                                          



                                }


                               else if($devicetype_2 == 'light')

                                 {
       

									$light_attr_1 = json_encode(["name"=>"Device Id",
																"value"=>$device_id]);

									$light_attr_2 = json_encode(["name"=>"mac",
																"value"=>$mac]);
									$light_attri[] = json_decode($light_attr_1, true);
									$light_attri[] = json_decode($light_attr_2, true);
									$light_att=json_encode($light_attri);
									

									$light_P_1 = json_encode(["name" => "name",
														  "data_type"=> "String",
														 "properties"=> ["read","write"],
															   "type"=>"efactor.param.name"]);

									$light_P_2 = json_encode(["name" => "power",
														  "data_type"=> "bool",
														 "properties"=> ["read","write"],
                                                                        "type"=>"efactor.param.power",
															"ui_type"=>"efactor.ui.toggle"]);

									$max_1 = array("max"=>100,
												   "min" =>0);
									//$max2 = json_encode($max_1);

									$light_P_3 = json_encode(["name" => "brightness",
															"bounds" => $max_1,
														  "data_type"=> "int",
														 "properties"=> ["read","write"],
                                                                        "type"=>"efactor.param.brightness",
															"ui_type"=>"efactor.ui.slider"]);

									$light_p[]= json_decode($light_P_1, true);
									$light_p[]= json_decode($light_P_2, true);
									$light_p[]= json_decode($light_P_3, true);


									$light_par=json_encode($light_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$light_attri,
															"params"=>$light_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.lightbulb","brand_logo"=>$url]);
									$devices_list[] = json_decode($devices_1, true);


									

									$devices_para_arr= array("brightness" => intval($brightness),
                                                                       "output" => true,
                                                                        "power"=>$power,
                                                                       "name"=>$name);

									
										 
                                  }

                                  else if($devicetype_2 == 'switch')

                                   {

									
									$switch_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$switch_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$switch_attri[] = json_decode($switch_attr_1, true);
									$switch_attri[] = json_decode($switch_attr_2, true);

									$switch_att=json_encode($switch_attri);

									$switch_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$switch_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$switch_p[]= json_decode($switch_P_1, true);
									$switch_p[]= json_decode($switch_P_2, true);
									$switch_par=json_encode($switch_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$switch_attri,
															"params"=>$switch_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.switch","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									


										}




                                              else if($devicetype_2 == 'cooker')

                                   {


                                                       
									
									$cooker_attr_1 = json_encode(["name"=>"Device Id",
											    "value"=>$device_id]);

								$cooker_attr_2 = json_encode(["name"=>"mac",
											     "value"=>$mac]);
								$cooker_attri[] = json_decode($cooker_attr_1, true);
								$cooker_attri[] = json_decode($cooker_attr_2, true);


									$cooker_att=json_encode($cooker_attri);


/*------------------------------------------------*/
                                                                   


                                                                    


                                                          //1)Dishes
                                                                         $max_10 = array("max"=>255,
										          "min"=>0);
                                                   $cooker_DCP_1 = json_encode(["name" => "Dishes",
									         "data_type"=> "string",
                                                                                  "values" =>["RICE" ,"BROWNRICE", "FRY", "MILK", "SOUP", "BIRIYANI", "IDLY", "DHAL",                                                  "HARDVEG", "WATER", "DEEPFRY", "POPCORN", "CAKE", "DIY"],                                                                                           											                             "properties"=> ["read","write"],
									"type"=> "efactor.config_param.Dishes",
                                                                               "ui_type"=> "efactor.ui.dropdown"]);





                                                              //2)Cooker temp constant mode
                                                                             $max_8 = array("max"=>1,
										          "min"=>0);
                                                         $cooker_DCP_2 = json_encode(["name" => "Cooker temp constant mode",
											      "data_type"=> "bool",
                                                                                         "bounds"=> $max_8,
											 "properties"=> ["read","write"],
								"type"=> "efactor.config_param.Cooker constant mode",
                                                                                                  "ui_type"=> "efactor.ui.toggle"]);





                                                              //3) cooker timer  

                                                                $max_1 = array("max"=>255,
										 "min"=>0);

                                                                         $cooker_DCP_3 = json_encode(["name" => "Cooker Timer",
											      "data_type"=> "short",
                                                                                         "bounds"=> $max_1,
											 "properties"=> ["read","write"],
										"type"=> "efactor.config_param.Cooker Timer",
                                                                                                  "ui_type"=> "efactor.ui.slider"]); 



                                                                      // 4) Cooking temperature
                                                                       $max_2 = array("max"=>255,
										 "min"=>0);

                                                                         $cooker_DCP_4 = json_encode(["name" => "Cooking temperature",
											      "data_type"=> "short",
                                                                                         "bounds"=> $max_2,
											 "properties"=> ["read","write"],
										"type"=> "efactor.config_param.Cooker max temperature",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);

                                                                     

                                                                       //5) Pre-timer temp constant mode
                                                                        $max_4 = array("max"=>1,
										          "min"=>0);


                                                                         $cooker_DCP_5 = json_encode(["name" => "Pre-timer temp constant mode",
											      "data_type"=> "bool",
                                                                                               "bounds"=> $max_4,
											 "properties"=> ["read","write"],
									"type"=> "efactor.config_param.Pre-timer constant mode",
                                                                                  "ui_type"=> "efactor.ui.toggle"]);

                                                                         $max_5 = array("max"=>1,
										          "min"=>0);


                                                                       //6) Pre-timer
                                                                        $max_5 = array("max"=>255,
										          "min"=>0);
									  $cooker_DCP_6 = json_encode(["name" => "Pre-timer",
											      "data_type"=> "short",
                                                                                      "bounds"=> $max_5,
											 "properties"=> ["read","write"],
							"type"=> "efactor.config_param.Pre-timer",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);
                                                                                                  
                                                                                                  
                                                                                                  

                                                                        //7)Pre – temperature
                                                                        $max_6 = array("max"=>255,
										          "min"=>0);

                                                              $cooker_DCP_7 = json_encode(["name" => "Pre – temperature",
											      "data_type"=> "short",
                                                                                         "bounds"=> $max_6,
											 "properties"=> ["read","write"],
									"type"=> "efactor.config_param.Pre – temperature",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);   


                                                                    //8) Keep warm constant mode
                                                                        $max_14 = array("max"=>1,
										          "min"=>0);
                                                   $cooker_DCP_8 = json_encode(["name" => "Keep warm temp constant mode",
											      "data_type"=> "bool",
                                                                                                "bounds"=> $max_14,
											 "properties"=> ["read","write"],
								"type"=> "efactor.config_param.warm constant cut-off",
                                                                                                  "ui_type"=> "efactor.ui.toggle"]);


                                                                 //9)Keep Warm timer
                                                                        $max_9 = array("max"=>255,
										          "min"=>0);
                                                              $cooker_DCP_9 = json_encode(["name" => "Keep Warm timer",
											      "data_type"=> "short",
                                                                                               "bounds"=> $max_9,
											 "properties"=> ["read","write"],
								"type"=> "efactor.config_param.Keep Warm timer",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);



                                                                        //10) Keep warm temperature
                                                                         $max_3 = array("max"=>255,
										          "min"=>0);


                                                                       $cooker_DCP_10 = json_encode(["name" => "Keep warm temperature",
											      "data_type"=> "short",
                                                                                         "bounds"=> $max_3,
											 "properties"=> ["read","write"],
								"type"=> "efactor.config_param.Keep warm",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);

                                                              

                                                                          //11)Coooker max-temperature
                                                                           $max_7 = array("max"=>300,
										          "min"=>0);
                                                              $cooker_DCP_11 = json_encode(["name" => "Coooker max-temperature",
											      "data_type"=> "int",
                                                                                         "bounds"=> $max_7,
											 "properties"=> ["read","write"],
								"type"=> "efactor.config_param.Coooker max-temperature",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);  
  
                                                                   

                                                                         //12)  pre-timer wait temperature constant mode
                                                                         $max_11 = array("max"=>1,
										          "min"=>0);
                                                  $cooker_DCP_12 = json_encode(["name" => "pre-timer wait temperature constant mode",
											      "data_type"=> "bool",
                                                                                               "bounds"=> $max_11,
											 "properties"=> ["read","write"],
							"type"=> "efactor.config_param.pre-timer wait temperature constant mode",
                                                                                                  "ui_type"=> "efactor.ui.toggle"]);
                                                                                                  
                                                                                                  
                                                                                                  
                                                                                                  
                                                                                                  

                                                                         //13)pre-timer waiting time
                                                                         $max_12 = array("max"=>255,
										          "min"=>0);
                                                  $cooker_DCP_13 = json_encode(["name" => "pre-timer waiting time",
											      "data_type"=> "short",
                                                                                                "bounds"=> $max_12,
											 "properties"=> ["read","write"],
						"type"=> "efactor.config_param.pre-timer waiting time",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);
                                                                        $max_13 = array("max"=>40,
										          "min"=>0);



                                                                        //14)pre-timer Waiting temperature
                                                                        $max_13 = array("max"=>255,
										          "min"=>0);
                                                   $cooker_DCP_14 = json_encode(["name" => "pre-timer Waiting temperature",
											      "data_type"=> "short",
                                                                                                "bounds"=> $max_13,
											 "properties"=> ["read","write"],
								"type"=> "efactor.config_param.pre-timer Waiting temperature",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);
                                                                                                  
                                                                                                                                              
                                                                       
                                                                        //15)cook wait temp constant mode
                                                                        $max_15 = array("max"=>1,
										          "min"=>0);
                                                   $cooker_DCP_15 = json_encode(["name" => "cook wait temp constant mode",
											      "data_type"=> "bool",
                                                                                                "bounds"=> $max_15,
											 "properties"=> ["read","write"],
								"type"=> "efactor.config_param.cook wait temp constant mode",
                                                                                                  "ui_type"=> "efactor.ui.toggle"]);




                                                               //16)cook waiting time
                                                                         $max_16 = array("max"=>255,
										          "min"=>0);
                                                   $cooker_DCP_16 = json_encode(["name" => "cook waiting time",
											      "data_type"=> "short",
                                                                                                "bounds"=> $max_16,
											 "properties"=> ["read","write"],
								"type"=> "efactor.config_param.cook waiting time",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);
                                                                                                  
                                                                                                  
                                                                                                  
                                                                       //17)cook Waiting- temperature
                                                                       $max_17 = array("max"=>255,
										          "min"=>0);
                                                   $cooker_DCP_17 = json_encode(["name" => "cook Waiting- temperature",
											      "data_type"=> "short",
                                                                                                "bounds"=> $max_17,
											 "properties"=> ["read","write"],
								"type"=> "efactor.config_param.cook Waiting- temperature",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);


                                                      //18)warm-wait temp constant mode
                                                                     $max_18 = array("max"=>1,
										          "min"=>0);
                                                   $cooker_DCP_18 = json_encode(["name" => "warm-wait temp constant mode",
											      "data_type"=> "bool",
                                                                                                "bounds"=> $max_18,
											 "properties"=> ["read","write"],
								"type"=> "efactor.config_param.warm-wait temp constant mode",
                                                                                                  "ui_type"=> "efactor.ui.toggle"]);

               



                                                                      //19)warm waiting time
                                                                       $max_19 = array("max"=>255,
										          "min"=>0);
                                                   $cooker_DCP_19 = json_encode(["name" => "warm waiting time",
											      "data_type"=> "short",
                                                                                                "bounds"=> $max_19,
											 "properties"=> ["read","write"],
								"type"=> "efactor.config_param.warm waiting time",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);


                                                                      //20)warm Waiting temperature
                                                                          $max_20 = array("max"=>255,
										          "min"=>0);
                                                   $cooker_DCP_20 = json_encode(["name" => "warm Waiting temperature",
											      "data_type"=> "short",
                                                                                                "bounds"=> $max_20,
											 "properties"=> ["read","write"],
								"type"=> "efactor.config_param.warm Waiting temperature",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);
                                                                                                  
                          


                                                                     //21)ok
                                                                          $cooker_DCP_21 = json_encode(["name" => "OK",
											               "data_type"=> "bool",
											                "properties"=> ["read","write"],
								                                   "type"=> "efactor.config_param.OK",
                                                                                                  "ui_type"=> "efactor.ui.button"]);

                                                                      //22)Reload Default
                                                                          $cooker_DCP_22 = json_encode(["name" => "Reload Default",
											               "data_type"=> "bool",
											                "properties"=> ["read","write"],
								                                   "type"=> "efactor.config_param.Reload Default",
                                                                                                  "ui_type"=> "efactor.ui.button"]);





                                                                                                  
                                                                                                  
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_1, true);
									$cooker_dcp[]= json_decode($cooker_DCP_2, true);
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_3, true);
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_4, true);
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_5, true);
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_6, true);
									$cooker_dcp[]= json_decode($cooker_DCP_7, true);
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_8, true);
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_9, true);
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_10, true);
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_11, true);
									$cooker_dcp[]= json_decode($cooker_DCP_12, true);
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_13, true);
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_14, true);
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_15, true);
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_16, true);
									$cooker_dcp[]= json_decode($cooker_DCP_17, true);
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_18, true);
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_19, true);
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_20, true);
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_21, true);
                                                                        $cooker_dcp[]= json_decode($cooker_DCP_22, true);
                                                                       
									$cooker_dcp_par=json_encode($cooker_dcp);












                                                               /*-------------------------------------------------*/


									$cooker_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$cooker_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                                                               "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$cooker_P_3 = json_encode(["name" => "skipmode",
												"data_type"=> "bool",
												"properties"=> ["read","write"],
                                                                                                "type"=>"efactor.param.skipmode",
											        "ui_type"=>"efactor.ui.trigger"]);


									$cooker_P_4 = json_encode(["name" => "cookermode",
												"data_type"=> "string",
												"properties"=> ["read"],
                                                                                                "type"=>"efactor.param.cookermode"]);

                                                                    $cooker_P_5 = json_encode(["name" => "recipe",
												"data_type"=> "string",
                                                                                                "values" =>["RICE" ,"BROWNRICE", "FRY", "MILK", "SOUP", "BIRIYANI", "IDLY", "DHAL", "HARDVEG", "WATER", "DEEPFRY", "POPCORN", "CAKE", "DIY"],
                                                   
                                                                                             "values data" =>["A301000000003C8C0000A0013C2800B40000000000000000000A",	
"A301000001003C8C0000A0013C2800B40000000000000000000A",
"A3010000020150B4000FB4013C2801180000000000000000000A",	
"A301000003011964000F6E013C2800960000000000000000000A",
"A301000004001E960000B4013C2800B40000000000000000000A",	
"A301000005002DF00100B4013C2800B40000000000000000000A",	
"A3010000060028960000A0013C2800A00000000000000000000A",	
"A301000007002D960000A0013C2800960000000000000000000A",
"A3010000080128C80000F0013C2800F00000000000000000000A",
"A301000009001E960000A000002800A00000000000000000000A",	
"A30100000A0150DC0005B4013C2801180000000000000000000A",
"A30100000B0101E60006B400002801220000000000000000000A",
"A30100000C012DDC0005B400002801220000000000000000000A",
"A30100000D0150B40105B4013C28FF0000000000000000000A"],



												"properties"=> ["read","write"],
                                                                                                "type"=>"efactor.param.selectrecipe",
											        "ui_type"=>"efactor.ui.dropdown"]);



                                                                        






                                                                  $cooker_P_6 = json_encode(["name" => "remaining time",
													   "data_type"=> "String",
													  "properties"=> ["read"],
													"type"=>"efactor.param.name.remainingtime"]);
                                                       
                                                                 $cooker_P_7 = json_encode(["name" => "running temperature",
													   "data_type"=> "String",
													  "properties"=> ["read"],
													"type"=>"efactor.param.name.runningtemperature"]);             

									$cooker_p[]= json_decode($cooker_P_1, true);
									$cooker_p[]= json_decode($cooker_P_2, true);
                                                                        $cooker_p[]= json_decode($cooker_P_3, true);
                                                                        $cooker_p[]= json_decode($cooker_P_4, true);
                                                                        $cooker_p[]= json_decode($cooker_P_5, true);
                                                                        $cooker_p[]= json_decode($cooker_P_6, true);
                                                                        $cooker_p[]= json_decode($cooker_P_7, true);
									$cooker_par=json_encode($cooker_p);




									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$cooker_attri,
															"params"=>$cooker_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.cooker","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "skipmode"=>true,
                                                                          "cookermode"=>$cookermode,
                                                                          
                                                                         "recipe"=>$recipe,
                                                                         "name"=>$name,
                                                                        "remaining time"=>hexdec($cooktime),
                                                                        "running temperature"=>hexdec($cook_run_temp));

									


										}




                                             


                                           else if($devicetype_2 == 'scanner')

                                   {

									$scanner_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$scanner_attr_2 = json_encode(["name"=>"mac",
														"value"=>$mac]);
									$scanner_attri[] = json_decode($scanner_attr_1, true);
									$scanner_attri[] = json_decode($scanner_attr_2, true);

									$scanner_att=json_encode($scanner_attri);

									$scanner_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$scanner_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$scanner_p[]= json_decode($scanner_P_1, true);
									$scanner_p[]= json_decode($scanner_P_2, true);
									$scanner_par=json_encode($scanner_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$scanner_attri,
															"params"=>$scanner_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.qr-scanner","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									


										}



                                      
                                                 




                                 else if($devicetype_2 == 'outlet')

                                   {
                                       //  $outlet_attri = array();

                                                                $outlet_attri = [];
                                                   $outlet_p = [];

									
									$outlet_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$outlet_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$outlet_attri[] = json_decode($outlet_attr_1, true);
									$outlet_attri[] = json_decode($outlet_attr_2, true);

									$outlet_att=json_encode($outlet_attri);

									$outlet_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$outlet_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$outlet_p[]= json_decode($outlet_P_1, true);
									$outlet_p[]= json_decode($outlet_P_2, true);
									$outlet_par=json_encode($outlet_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$outlet_attri,
															"params"=>$outlet_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.outlet","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									

										}



                                                        else if($devicetype_2 == 'other')

                                   {

									
									$other_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$other_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$other_attri[] = json_decode($other_attr_1, true);
									$other_attri[] = json_decode($other_attr_2, true);

									$other_att=json_encode($other_attri);

									$other_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$other_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$other_p[]= json_decode($other_P_1, true);
									$other_p[]= json_decode($other_P_2, true);
									$other_par=json_encode($other_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$other_attri,
															"params"=>$other_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.other","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									


										}


else if($devicetype_2 == 'HBMDNC')

                                   {
                                              $hbmdnc_attri = array();
$hbmdnc_p = array();

									/*-----------------outlet--------------*/
									$hbmdnc_attr_1 = json_encode(["name"=>"Device Id",
											        "value"=>$device_id]);

									$hbmdnc_attr_2 = json_encode(["name"=>"mac",
											         "value"=>$mac]);
									$hbmdnc_attri[] = json_decode($hbmdnc_attr_1, true);
									$hbmdnc_attri[] = json_decode($hbmdnc_attr_2, true);

									$hbmdnc_att=json_encode($hbmdnc_attri);

									$hbmdnc_P_1 = json_encode(["name" => "name",
												 "data_type"=> "String",
											"properties"=> ["read","write"],
											"type"=>"efactor.param.name"]);

									$hbmdnc_P_2 = json_encode(["name" => "Doctor",
												"data_type"=> "bool",
												"properties"=> ["read","write"],
                                                                                               "type"=>"efactor.param.Doctor",
											 "ui_type"=>"efactor.ui.toggle"]);


                                                                        $hbmdnc_P_3 = json_encode(["name" => "Personal Assist",
												"data_type"=> "bool",
												"properties"=> ["read","write"],
                                                                                               "type"=>"efactor.param.Personal Assist",
											 "ui_type"=>"efactor.ui.toggle"]);

                                                                        $hbmdnc_P_4 = json_encode(["name" => "nurse",
												"data_type"=> "bool",
												"properties"=> ["read","write"],
                                                                                               "type"=>"efactor.param.nurse",
											 "ui_type"=>"efactor.ui.toggle"]);

                                                                       $hbmdnc_P_5 = json_encode(["name" => "Doctor Response",
												"data_type"=> "bool",
												"properties"=> ["read","write"],
                                                                                               "type"=>"efactor.param.Doctor Response",
											 "ui_type"=>"efactor.ui.trigger"]);


                                                                       $hbmdnc_P_6 = json_encode(["name" => "Doctor status",
											        "data_type"=> "string",
												"properties"=> ["read"],
                                                                                               "type"=>"efactor.param.Doctor status"]);


                                                                        $hbmdnc_P_7 = json_encode(["name" => "Personal Assist response",
												"data_type"=> "bool",
												"properties"=> ["read","write"],
                                                                                               "type"=>"efactor.param.Personal Assist response",
											 "ui_type"=>"efactor.ui.trigger"]);


                                                                       $hbmdnc_P_8 = json_encode(["name" => "PA status",
												"data_type"=> "string",
												"properties"=> ["read"],
                                                                                               "type"=>"efactor.param.PA status"]);


                                                                        $hbmdnc_P_9 = json_encode(["name" => "nurse response",
												"data_type"=> "bool",
												"properties"=> ["read","write"],
                                                                                               "type"=>"efactor.param.nurse response",
											 "ui_type"=>"efactor.ui.trigger"]);



                                                                        $hbmdnc_P_10 = json_encode(["name" => "Nurse_Status",
												"data_type"=> "string",
												"properties"=> ["read"],
                                                                                               "type"=>"efactor.param.Nurse_Status"]);

                                                         

									$hbmdnc_p[]= json_decode($hbmdnc_P_1, true);
									$hbmdnc_p[]= json_decode($hbmdnc_P_2, true);
                                                                        $hbmdnc_p[]= json_decode($hbmdnc_P_3, true);
									$hbmdnc_p[]= json_decode($hbmdnc_P_4, true);
                                                                        $hbmdnc_p[]= json_decode($hbmdnc_P_5, true);
                                                                        $hbmdnc_p[]= json_decode($hbmdnc_P_6, true);
									$hbmdnc_p[]= json_decode($hbmdnc_P_7, true);
                                                                        $hbmdnc_p[]= json_decode($hbmdnc_P_8, true);
                                                                        $hbmdnc_p[]= json_decode($hbmdnc_P_9, true);
									$hbmdnc_p[]= json_decode($hbmdnc_P_10, true);


									$hbmdnc_par=json_encode($hbmdnc_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$hbmdnc_attri,
															"params"=>$hbmdnc_p,
														   
															  "type"=>"efactor.device.hbmdnc",
                                                                                                                      "brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									/****************************/ 
									$devices_para_arr = array("Doctor"=>(bool)$doctormanage,
                                                                                                  "Personal Assist"=>(bool)$personal_assistant,
                                                                                                   "nurse"=>(bool)$nurse,
                                                                                                    "Doctor status"=>$doctormanage_resp,
                                                                                                    "PA status"=>$personal_assistant_resp,
                                                                                                   "Nurse_Status"=>$nurse_resp,
                                                                                                    "name"=>$name);




									/***************************/

										}




else if($devicetype_2 == 'input-output')

                                   {

									
									$other_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$other_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$other_attri[] = json_decode($other_attr_1, true);
									$other_attri[] = json_decode($other_attr_2, true);

									$other_att=json_encode($other_attri);

									$other_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$other_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

									$other_p[]= json_decode($other_P_1, true);
									$other_p[]= json_decode($other_P_2, true);
									$other_par=json_encode($other_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$other_attri,
															"params"=>$other_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.input-output","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name);

									


										}



















else if($devicetype_2 == 'exhaust_hood')

                                   {

									
									$exh_attr_1 = json_encode(["name"=>"Device Id",
													"value"=>$device_id]);

									$exh_attr_2 = json_encode(["name"=>"mac",
												"value"=>$mac]);
									$exh_attri[] = json_decode($exh_attr_1, true);
									$exh_attri[] = json_decode($exh_attr_2, true);

									$exh_att=json_encode($exh_attri);


                                                                      
									$exh_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$exh_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                       "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);

								




                                                                        $exh_P_4 = json_encode(["name" => "autoclean",
												"data_type"=> "bool",
												"properties"=> ["read","write"],
                                                                                                 "type"=>"efactor.param.autoclean",
												"ui_type"=>"efactor.ui.toggle"]);



                                                                       $max_1 = array("max"=>3,
												  "min" =>0);

                                                                        $exh_P_5 = json_encode(["name" => "airflow",
                                                                                                 "bounds" => $max_1,
												"data_type"=> "int",
												"properties"=> ["read","write"],
                                                                                                 "type"=>"efactor.param.airflow",
												"ui_type"=>"efactor.ui.slider"]);



                                                                        $exh_P_6 = json_encode(["name" => "lamp",
												"data_type"=> "bool",
											        "properties"=> ["read","write"],
                                                                                                "type"=>"efactor.param.lamp",
												"ui_type"=>"efactor.ui.toggle"]);



									$exh_p[]= json_decode($exh_P_1, true);
									$exh_p[]= json_decode($exh_P_2, true);
                                                                        
									$exh_p[]= json_decode($exh_P_4, true);
                                                                        $exh_p[]= json_decode($exh_P_5, true);
									$exh_p[]= json_decode($exh_P_6, true);
									$exh_par=json_encode($exh_p);

									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$exh_attri,
															"params"=>$exh_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.exhaust-hood","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									

									


                                                                    $devices_para_arr = array("power" => $power,
                                                                                                   "output" => true,
                                                                                                   "name" => $name,
                                                                                               "autoclean" =>(bool)$hood_autoclean,
                                                                                                  "airflow" => (int)$hood_speed,
                                                                                                    "lamp" =>(bool)$hood_lamp);


									


										}




                                                            else if($devicetype_2 == 'swpcp')

                                   {

									
									$wp_attr_1 = json_encode(["name"=>"Device Id",
																 "value"=>$device_id]);

									$wp_attr_2 = json_encode(["name"=>"mac",
																 "value"=>$mac]);
									$wp_attri[] = json_decode($wp_attr_1, true);
									$wp_attri[] = json_decode($wp_attr_2, true);

									$wp_att=json_encode($wp_attri);
                                                                        $max_1 = array("max"=>60,
										          "min"=>1);

                                                                         $wp_DCP_1 = json_encode(["name" => "starter relay off timer",
											      "data_type"=> "int",
                                                                                         "bounds"=> $max_1,
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.starter relay off timer",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);   

                                                                           
                                                                             $max_3 = array("max"=>40,
										          "min"=>0);


                                                                       $wp_DCP_3 = json_encode(["name" => "start high current cutoff",
											      "data_type"=> "int",
                                                                                         "bounds"=> $max_3,
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.start high current cutoff",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);

                                                                        $max_4 = array("max"=>40,
										          "min"=>0);


                                                                         $wp_DCP_4 = json_encode(["name" => "start low current cutoff",
											      "data_type"=> "int",
                                                                                               "bounds"=> $max_4,
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.start low current cutoff",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);

                                                                         $max_5 = array("max"=>1,
										          "min"=>0);



                                                                        $wp_DCP_5 = json_encode(["name" => "LOCK ENABLE/DISABLE",
											      "data_type"=> "bool",
                                                                                                "bounds"=> $max_5,
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.LOCK ENABLE/DISABLE",
                                                                                                  "ui_type"=> "efactor.ui.toggle"]);

                                                                        $max_6 = array("max"=>40,
										          "min"=>0);

                                                                         $wp_DCP_6 = json_encode(["name" => "Run high current cutoff",
											      "data_type"=> "int",
                                                                                         "bounds"=> $max_6,
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.Run high current cutoff",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);   

                                                                           $max_7 = array("max"=>400,
										          "min"=>0);
                                                                
                                                                
                                                                       $wp_DCP_7 = json_encode(["name" => "high voltage cutoff",
											      "data_type"=> "int",
                                                                                         "bounds"=> $max_7,
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.high voltage cutoff",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);   
                                                                             $max_8 = array("max"=>40,
										          "min"=>0);


                                                                       $wp_DCP_8 = json_encode(["name" => "USER SET MAX CURRENT CUTOFF LIMIT",
											      "data_type"=> "int",
                                                                                         "bounds"=> $max_8,
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.USER SET MAX CURRENT CUTOFF LIMIT",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);

                                                                        $max_9 = array("max"=>300,
										          "min"=>0);


                                                                         $wp_DCP_9 = json_encode(["name" => "low voltage cutoff",
											      "data_type"=> "int",
                                                                                               "bounds"=> $max_9,
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.low voltage cutoff",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);

                                                                         $max_10 = array("max"=>450,
										          "min"=>0);



                                                                        $wp_DCP_10 = json_encode(["name" => "ultra high voltage cutoff",
											      "data_type"=> "int",
                                                                                                "bounds"=> $max_10,
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.ultra high voltage cutoff",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);


                                                                         $max_11 = array("max"=>40,
										          "min"=>0);


                                                                         $wp_DCP_11 = json_encode(["name" => "ultra high current cutoff",
											      "data_type"=> "int",
                                                                                               "bounds"=> $max_11,
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.ultra high current cutoff",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);

                                                                         $max_12 = array("max"=>40,
										          "min"=>0);



                                                                        $wp_DCP_12 = json_encode(["name" => "USER AND DEVICE MAX CURRENT LIMIT",
											      "data_type"=> "int",
                                                                                                "bounds"=> $max_12,
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.USER AND DEVICE MAX CURRENT LIMIT",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);
                                                                        $max_13 = array("max"=>40,
										          "min"=>0);



                                                                        $wp_DCP_13 = json_encode(["name" => "Run low current cutoff",
											      "data_type"=> "int",
                                                                                                "bounds"=> $max_13,
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.Run low current cutoff",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);









                                                                        $wp_dcp[]= json_decode($wp_DCP_1, true);
									
                                                                        $wp_dcp[]= json_decode($wp_DCP_3, true);
                                                                        $wp_dcp[]= json_decode($wp_DCP_4, true);
                                                                        $wp_dcp[]= json_decode($wp_DCP_5, true);
                                                                        $wp_dcp[]= json_decode($wp_DCP_6, true);
									$wp_dcp[]= json_decode($wp_DCP_7, true);
                                                                        $wp_dcp[]= json_decode($wp_DCP_8, true);
                                                                        $wp_dcp[]= json_decode($wp_DCP_9, true);
                                                                        $wp_dcp[]= json_decode($wp_DCP_10, true);
                                                                        $wp_dcp[]= json_decode($wp_DCP_11, true);
									$wp_dcp[]= json_decode($wp_DCP_12, true);
                                                                        $wp_dcp[]= json_decode($wp_DCP_13, true);
                                                                       
									$wp_dcp_par=json_encode($wp_dcp);

                                                                       $wp_P_1 = json_encode(["name" => "name",
														   "data_type"=> "String",
														  "properties"=> ["read","write"],
																"type"=>"efactor.param.name"]);

									$wp_P_2 = json_encode(["name" => "power",
														   "data_type"=> "bool",
														  "properties"=> ["read","write"],
                                                                                                               "type"=>"efactor.param.power",
															 "ui_type"=>"efactor.ui.toggle"]);


                                                                         $max_2 = array("max"=>240,
										          "min"=>0);
                                                                
                                                                
                                                                       $wp_P_3 = json_encode(["name" => "Timer",
											      "data_type"=> "int",
                                                                                         "bounds"=> $max_2,
											 "properties"=> ["read","write"],
												"type"=> "efactor.config_param.Timer",
                                                                                                  "ui_type"=> "efactor.ui.slider"]);   





                                                                        $wp_P_4 = json_encode(["name" => "current(A)",
														   "data_type"=> "String",
														  "properties"=> ["read"],
                                                                                                               "type"=>"efactor.param.power",
															 "type"=>"efactor.param.current"]);


                                                                       $wp_P_5 = json_encode(["name" => "voltage(V)",
														   "data_type"=> "String",
														  "properties"=> ["read"],
                                                                                                               "type"=>"efactor.param.power",
															 "type"=>"efactor.param.voltage"]);

                                                                          $wp_P_6 = json_encode(["name" => "remaining time",
													   "data_type"=> "String",
													  "properties"=> ["read"],
													"type"=>"efactor.param.remainingtime"]);
                                                                       
                                                                        $wp_P_7 = json_encode(["name" => "total watt",
													   "data_type"=> "String",
													  "properties"=> ["read"],
													"type"=>"efactor.param.totalwatt"]);


                                                                          
                
									$wp_p[]= json_decode($wp_P_1, true);
									$wp_p[]= json_decode($wp_P_2, true);
                                                                        $wp_p[]= json_decode($wp_P_3, true);
                                                                        $wp_p[]= json_decode($wp_P_4, true);
                                                                        $wp_p[]= json_decode($wp_P_5, true);
                                                                        $wp_p[]= json_decode($wp_P_6, true);
                                                                        $wp_p[]= json_decode($wp_P_7, true);
									//$wp_par=json_encode($wp_p);




									$devices_1 = json_encode(["name"=>$curr_devicetype,
														"attributes"=>$wp_attri,
                                                                                                                "device_config_params"=>$wp_dcp,
															"params"=>$wp_p,
														   "primary"=>"power",
															  "type"=>"efactor.device.swpcp","brand_logo"=>$url]);

									$devices_list[] = json_decode($devices_1, true);


						

									

									$devices_para_arr = array("power"=>$power,
                                                                        "output" => true,
                                                                         "name"=>$name,
                                                                         "Timer"=>$timer,
                                                                        "current(A)"=>$current,
                                                                        "voltage(V)"=>$voltage,
                                                                        "remaining time"=>$rem_time,
                                                                        "total watt"=>$watt);


									

                                                                            $devices_config_para = array("starter relay off timer"=>$starter_relay_off_timer,
                        "start high current cutoff"=>$start_high_current_cutoff,
                        "start low current cutoff"=>$start_low_current_cutoff,
                        "Run high current cutoff"=>$Run_high_current_cutoff,
                        "Run low current cutoff"=>$Run_low_current_cutoff,
                        "high voltage cutoff"=>$high_voltage_cutoff,
                        "low voltage cutoff"=>$low_voltage_cutoff,
                        "ultra high voltage cutoff"=>$ultra_high_voltage_cutoff,
                        "ultra high current cutoff"=>$ultra_high_current_cutoff,
                        "USER SET MAX CURRENT CUTOFF LIMIT"=>$USER_SET_MAX_CURRENT_CUTOFF_LIMIT,
                        "USER AND DEVICE MAX CURRENT LIMIT"=>$USER_AND_DEVICE_MAX_CURRENT_LIMIT,
                        "LOCK ENABLE/DISABLE"=>$LOCK_ENABLE_DISABLE);
                                                                    


										} 

                                     







$localcontrol =array("POP"=> "a8d8e5ce",
                    "Type"=> 0);

$timezone = array("TZ"=>$time,
        "TZ-POSIX"=>"PST8PDT,M3.2.0,M11.1.0");


$Baud_rate = array("BR"=>$baudrate,
             "BR-POSIX"=>"0");

$uart_msg_id = array("UART_MSG_ID"=>$uartmsgid,
               "UART_MSG_ID POSIX"=>"0");

$error_code = array("ERROR CODE"=>$errorcode,
              "ERROR CODE POSIX"=>"0");


$comm_mode = array("Efactor Protocol"=>$comm_mode,
             "Efactor Protocol Posix"=>"0");

$tuning = array("Motor Tuning"=>"Enable");



$cooker_config = array("Cooker Configure"=> "Enable");

$display = array("DisplayResponse"=> "Enable");

$swpcp_1ph = array("Single Phase Motor Configure"=> "Enable");

$remote_pair_config = array("remote pairing"=> "Enable");

if(curr_devicetype != 'NULL')
{
$sub_field = array($curr_devicetype=>$devices_para_arr,
                   "Local_Control"=>$localcontrol,
                   "Time" =>$timezone,
              "Baud_Rate" =>$Baud_rate,  
            "Uart_Msg_Id" =>$uart_msg_id,
             "Error_Code" =>$error_code,
     "Communication_Mode" =>$comm_mode,
                 "Tuning" =>$tuning,
                 "Cooker Configure"=>$cooker_config,
		"Display"=>$display,
   "device_config_params"=>array($curr_devicetype=>$devices_config_para),
    "remote_pairing" => $remote_pair_config);




}
else{
$sub_field = array($devicetype=>$devices_para_arr,
                   "Local_Control"=>$localcontrol,
                   "Time" =>$timezone,
              "Baud_Rate" =>$Baud_rate,  
            "Uart_Msg_Id" =>$uart_msg_id,
             "Error_Code" =>$error_code,
     "Communication_Mode" =>$comm_mode,
                 "Tuning" =>$tuning,
"Cooker Configure"=>$cooker_config,
            "Display"=>$display,
   "device_config_params"=>array($curr_devicetype=>$devices_config_para),
    "remote_pairing" => $remote_pair_config);

}






$s_p_1_1 = json_encode(["data_type"=> "string",           
                             "name"=> "TZ",
                       "properties"=> explode(",",$tz_properties),
                             "type"=> "efactor.param.tz"]);

$s_p_1_2 = json_encode(["data_type"=> "string",
                             "name"=> "TZ-POSIX",
                       "properties"=> explode(",",$tz_properties),
                             "type"=> "efactor.param.tz_posix"]);



$s_p_1_3[] = json_decode($s_p_1_1, true);
$s_p_1_3[] = json_decode($s_p_1_2, true);

$s_p_1=  json_encode(["name"=> "Time",           
                    "params"=> $s_p_1_3,
                      "type"=> "efactor.service.time"]);

//echo $s_p_1;


//baud rate
$s_p_2_1 = json_encode(["data_type"=> "string",           
                             "name"=> "BR",
                       "properties"=> explode(",",$br_properties),
                             "type"=> "efactor.param.br"]);                        

$s_p_2_2 = json_encode(["data_type"=> "string",
                             "name"=> "BR-POSIX",
                       "properties"=> explode(",",$br_properties),
                             "type"=> "efactor.param.br_posix"]);


$s_p_2_3[] = json_decode($s_p_2_1, true);
$s_p_2_3[] = json_decode($s_p_2_2, true);

$s_p_2=  json_encode(["name"=> "Baud_Rate",           
                    "params"=> $s_p_2_3,
                      "type"=> "efactor.service.baudrate"]);

//echo $s_p_2;


//Uart MSG ID
$s_p_3_1 = json_encode(["data_type" => "string",           
                             "name" => "UART_MSG_ID",
                       "properties" => explode(",",$umi_properties),
                             "type" => "efactor.param.uart_msg_id"]);                           

$s_p_3_2 = json_encode(["data_type"=> "string",
                             "name"=> "UART_MSG_ID POSIX",
                       "properties"=> explode(",",$umi_properties),
                             "type"=> "efactor.param.uart_msg_id_posix"]);

$s_p_3_3[] = json_decode($s_p_3_1, true);
$s_p_3_3[] = json_decode($s_p_3_2, true);

$s_p_3=  json_encode(["name"=> "Uart_Msg_Id",           
                    "params"=> $s_p_3_3,
                     "type"=> "efactor.service.uartmsgid"]);

//echo $s_p_3;


//Error code
$s_p_4_1 = json_encode(["data_type"=> "string",           
                    "name"=> "ERROR CODE",
              "properties"=> explode(",",$ec_properties),
                     "type"=> "efactor.param.error code"]);   
                        

$s_p_4_2 = json_encode(["data_type"=> "string",
                      "name"=> "ERROR CODE POSIX",
                "properties"=> explode(",",$ec_properties),
                      "type"=> "efactor.param.error code posix"]);

$s_p_4_3[] = json_decode($s_p_4_1, true);
$s_p_4_3[] = json_decode($s_p_4_2, true);

$s_p_4=  json_encode(["name"=> "Error_Code",           
                    "params"=> $s_p_4_3,
                     "type"=> "efactor.service.errorcode"]);

//echo $s_p_4;


//Communication Mode
$s_p_5_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Efactor Protocol",
              "properties"=> explode(",",$cm_properties),
                     "type"=> "efactor.param.efactor protocol"]);  

$s_p_5_2 = json_encode(["data_type"=> "string",
                      "name"=> "Efactor Protocol Posix",
                "properties"=> explode(",",$cm_properties),
                      "type"=> "efactor.param.efactor protocol posix"]);


$s_p_5_3[] = json_decode($s_p_5_1, true);
$s_p_5_3[] = json_decode($s_p_5_2, true);

$s_p_5=  json_encode(["name"=> "Communication_Mode",           
                    "params"=> $s_p_5_3,
                     "type"=> "efactor.service.communicationmode"]);

//echo $s_p_5;
//tuning
$s_p_6_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Motor Tuning",
              "properties"=> explode(",",$tu_properties),
                     "type"=> "efactor.param.motor tuning"]); 

                            
$s_p_6_3[] = json_decode($s_p_6_1, true);

$s_p_6=  json_encode(["name"=> "Tuning",           
                    "params"=> $s_p_6_3,
                     "type"=> "efactor.service.Tuning"]);

//echo $s_p_6;


//Local Control
$s_p_7_1 = json_encode(["data_type"=> "string",           
                    "name"=> "POP",
              "properties"=> explode(",",$lc_properties),
                     "type"=> "efactor.param.local_control_pop"]); 

$s_p_7_2 = json_encode(["data_type"=> "int",
                      "name"=> "Type",
                "properties"=> explode(",",$lc_properties),
                      "type"=> "efactor.param.local_control_type"]);

$s_p_7_3[] = json_decode($s_p_7_1, true);
$s_p_7_3[] = json_decode($s_p_7_2, true);

$s_p_7=  json_encode(["name"=> "Local_Control",           
                    "params"=> $s_p_7_3,
                     "type"=> "efactor.service.local_control"]);

//echo $s_p_7;



//cooker_config
$s_p_8_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Cooker Configure",
              "properties"=> explode(",",$cooker_properties),
                     "type"=> "efactor.param.Cooker Configure"]); 

                            
$s_p_8_3[] = json_decode($s_p_8_1, true);

$s_p_8=  json_encode(["name"=> "Cooker Configure",           
                    "params"=> $s_p_8_3,
                     "type"=> "efactor.service.cooker_configure"]);

//echo $s_p_6;


//display
$s_p_9_1 = json_encode(["data_type"=> "string",           
                    "name"=> "DisplayResponse",
              "properties"=> explode(",",$display_properties),
                     "type"=> "efactor.param.displayresponse"]);                           
$s_p_9_3[] = json_decode($s_p_9_1, true);



$s_p_9=  json_encode(["name"=> "DisplayResponse",           
                    "params"=> $s_p_9_3,
                     "type"=> "efactor.service.displayresponse"]);

//swpcp_1ph Configure
$s_p_10_1 = json_encode(["data_type"=> "string",           
                    "name"=> "SWP Single phase Configure",
              "properties"=> explode(",",$swpcp_1ph_properties),
                     "type"=> "efactor.param.swpcp_1ph_configure"]); 

                            
$s_p_10_3[] = json_decode($s_p_10_1, true);

$s_p_10=  json_encode(["name"=> "swpcp_1ph Configure",           
                    "params"=> $s_p_10_3,
                     "type"=> "efactor.service.swpcp_1ph_configure"]);






//fan Configure
$s_p_11_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Fan Configure",
              "properties"=> explode(",",$fan_properties),
                     "type"=> "efactor.param.fan_configure"]); 

                            
$s_p_11_3[] = json_decode($s_p_11_1, true);

$s_p_11=  json_encode(["name"=> "Fan Configure",           
                    "params"=> $s_p_11_3,
                     "type"=> "efactor.service.fan_configure"]);



//remote_pairing Configure
$s_p_12_1 = json_encode(["data_type"=> "string",           
                    "name"=> "Remote Pairing Configure",
              "properties"=> explode(",",$remote_pair_properties),
                     "type"=> "efactor.param.remote_pairing"]); 

                            
$s_p_12_3[] = json_decode($s_p_12_1, true);

$s_p_12=  json_encode(["name"=> "Remote Pairing",           
                    "params"=> $s_p_12_3,
                     "type"=> "efactor.service.remote_pairing"]);



$s_p[] = json_decode($s_p_1, true);
$s_p[] = json_decode($s_p_2, true);
$s_p[] = json_decode($s_p_3, true);
$s_p[] = json_decode($s_p_4, true);
$s_p[] = json_decode($s_p_5, true);
$s_p[] = json_decode($s_p_6, true);
$s_p[] = json_decode($s_p_7, true);

$s_p[] = json_decode($s_p_9, true);
$s_p[] = json_decode($s_p_10, true);
$s_p[] = json_decode($s_p_11, true);
$s_p[] = json_decode($s_p_12, true);







/************config*****************/
/***********dev_param**************/


/***********config*********/
$config_attr_1 = json_encode(["name"=>"serial_num",
                             "value"=>"1345"]);

$config_attr_2 = json_encode(["name"=>"model",
                             "value"=>"wifi"]);

$config_attr[] = json_decode($config_attr_1, true);
$config_attr[] = json_decode($config_attr_2, true);

$config_att=json_encode($config_attr);

if($moduletype=='RF' || $moduletype=='GATEWAY')
{
    $moduletype='Gateway';
}


$query_inter = "SELECT * FROM efactorproduct where node_id='$device_id'";
 $doctors = $db->get_results($query_inter); 
 $row_inter = $db->get_row($query_inter);
$model_number=$row_inter['model_number'];
$devicename=$row_inter['device_name'];
$vendor_name=$row_inter['vendor_name'];
$moduletype=$row_inter['module_type'];
$net_conn_1=$row_inter['internet'];




$info=array("fw_version"=>$model_number,
                        "model"=>$devicename,
                         "name"=>$vendor_name,
                         "type"=>$moduletype);

$config_en=array("attributes"=>$config_attr,
                        "config_version"=>"2020-03-20",
                         "devices"=>$devices_list,             
                          "info" => $info,
                         "node_id"=>$device_id,
                          "services"=>$s_p);                    //8-2

$config=json_encode($config_en);






/*************************************/
$connectivity =array("connected"=>$net_conn_1,
                     "timestamp"=>$timestamp);

$connectivity_1[] = json_decode($connectivity, true);

$status_1 = array("connectivity" => $connectivity);

/***************************************************/

if($shareuser == $phone)
{
//$role='secondary';
$role='primary';
}
else
{
$role='primary';
}

$node_details = json_encode(["id"=>$device_id,
                         "params"=>$sub_field,
                         "config"=>$config_en,
                           "role"=>$role,
                         "status"=>$status_1]);      //$sub_field

$node_details_1[] = json_decode($node_details, true);



if($is_added=='1')
			{
$total_1=$num_dev-$num_5;
$total=$total_1+$num_gate;

}
else if(!empty($_GET["start_id"]))
 {
   $total=$num_start+$num_link;
}

else
{

$total=$num_dev;
}



$node = json_encode(["url"=>"action=user_nodes",
                      "token"=>$access_token,
                      "node_details"=>$node_details_1,
                       "nodes"=>$device_id]);


$devicename=$row['device_name'];

$moduletype=$row['module_type'];
$net_conn_1=$row['internet'];





                    
//echo $node;
//echo 'hi';



$endTime = microtime(true);

$executionTime = $endTime - $startTime;


                    
echo $node;
//echo "Execution time: " . $executionTime . " seconds";



  }

else{


$additionalemptyData = array(
    "url" => "action=user_nodes",
    "token" => $val
   );


echo json_encode($additionalemptyData);
exit; 




}




}


}


/************************************************************/
/************************user_nodes**************************/

/************************************************************/

//Output
echo json_encode($response);
//echo json_data;
exit;



?>
