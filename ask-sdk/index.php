<?php
require 'vendor/autoload.php';
require_once('includes/config.php');
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use Bluerhinos\phpMQTT;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$app = new App();


	$app->post('/api/alexa', function (Request $request, Response $response) use ($db){
    $data = $request->getParsedBody();
    error_log('Incoming request: ' . json_encode($data)); 
     //global $db;
    $requestType = $data['requestType'] ?? null;

if ($requestType === 'authorization') {
	
	  // Accessing the user profile details
    $userinfo  = $data['userProfile']['sub'] ?? null;
	
    $parts = explode('|', $userinfo);
    $user_id = $parts[1]; // This will be '9'

    /*$name = $data['userProfile']['name'] ?? null;
    $postal_code = $data['userProfile']['postal_code'] ?? null;
    $email = $data['userProfile']['email'] ?? null;*/
	

	$query = "SELECT * FROM `alexa` WHERE `user_id` = ?";
    $stmt = $db->link->prepare($query);

if (!$stmt) {
    error_log("Error preparing query for user_id: " . $db->link->error);
} else {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        $query = "INSERT INTO `alexa` (`user_id`) VALUES (?)";
		
        $stmt = $db->link->prepare($query);
        if (!$stmt) {
            error_log("Error preparing query for inserting user info: " . $db->link->error);
        } else {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
        }
    } /*else {
        $query = "UPDATE `alexa` SET `name` = ?, `email` = ?, `postal_code` = ? WHERE `user_id` = ?";
        $stmt = $db->link->prepare($query);
        if (!$stmt) {
            error_log("Error preparing query for updating user info: " . $db->link->error);
        } else {
            $stmt->bind_param("ssss", $name, $email, $postal_code, $user_id);
            $stmt->execute();
        }
    }*/
}
    $query = "SELECT * FROM `users` WHERE `id` = ?";
    $stmt = $db->link->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        error_log("No user found with user_id " . $user_id);
    } else {
        $row = $result->fetch_assoc();
        $access_token = $row['access_token'];
        if (is_null($access_token)) {
            error_log("User id does not exist");
        } else {
            $query = "UPDATE `alexa` SET `access_token` = ? WHERE `user_id` = ?";
            $stmt = $db->link->prepare($query);
            $stmt->bind_param("si", $access_token, $user_id);
            $stmt->execute();
            error_log("Access token for user with user_id $user_id updated in alexa table");
        }
    }


	  $response_data = [
        'status' => 'success',
        'message' => 'Tokens and user profile received successfully',
    ];

    return $response->withJson($response_data);
}
else{
	$user_id_temp = $data['user_id'];
	$parts = explode('|', $user_id_temp);
    $user_id = $parts[1];
	 $header = $data['header'];
	 $namespace = $header['namespace'];
     $name = $header['name'];

		// Check if 'instance' key is set in the header array
	if (isset($header['instance'])) {
			$instance = $header['instance'];
		}
		else
			$instance=null;
	// Check if 'endpoint' key is set in the data array
	if (isset($data['endpoint'])) {
		$endpoint = $data['endpoint'];

		// Check if 'endpointId' key is set in the endpoint array
		if (isset($endpoint['endpointId'])) {
			$endpointId = $endpoint['endpointId'];
		}
	}


}




    if ($namespace === 'Alexa.Discovery' && $name === 'Discover') {
	  return $response->withJson(generateDiscoveryResponse($db,$user_id));
	 }
	/*elseif($namespace === 'Alexa' && $name === 'ReportState'){
		
		
	}*/
	else if ($namespace === 'Alexa.PowerController') {
		
     $powerState = $name === 'TurnOn' ? 'ON' : 'OFF';
	 
     $success = performDeviceControl($db,$endpointId, $powerState,$user_id,$namespace,$name,$instance);
      if ($success) {
        return $response->withJson(['status' => 'success', 'powerState' => $powerState]);
      } else {
        return $response->withJson(['status' => 'error', 'message' => 'An error occurred while controlling the device.']);
      }
	}
	elseif ($header['namespace'] === 'Alexa.RangeController') {
   
     $rangeValue = $data['payload']['rangeValue'];
	 $success = performDeviceControl($db,$endpointId, $rangeValue,$user_id,$namespace,$name,$instance);
      if ($success) {
        return $response->withJson(['status' => 'success', 'rangeValue' => $rangeValue]);
      } else {
        return $response->withJson(['status' => 'error', 'message' => 'An error occurred while controlling the device.']);
      }
    }
	elseif ($header['namespace'] === 'Alexa.BrightnessController') {
   
     $brightness = $data['payload']['brightness'];

	 $success = performDeviceControl($db,$endpointId, $brightness,$user_id,$namespace,$name,$instance);
      if ($success) {
        return $response->withJson(['status' => 'success', 'brightness' => $brightness]);
      } else {
        return $response->withJson(['status' => 'error', 'message' => 'An error occurred while controlling the device.']);
      }
    }
    elseif ($header['namespace'] === 'Alexa.ColorController') {
     
	  $hue = $data['payload']['color']['hue'];
	  $saturation=$data['payload']['color']['saturation'];
	  $brightness=$data['payload']['color']['brightness'];
	  if($hue== 0 && $saturation==0 && $brightness==1 )
	  {$color=0;}//white
		 else if($hue >= 330 && $hue <= 360 && $brightness==1 )
		  {$color=1;}//pink
		 else if($hue >= 45 && $hue <= 75   && $brightness==1)
		  {$color=4;}  //yellow or orange
		 else if($hue== 0 && $saturation==1 && $brightness==1 )
		  {$color=5;}  //red
		 else if($hue== 180 && $saturation==1 && $brightness==1)
		  {$color=2;}  //cyan
		 else if($hue== 240 && $saturation==1&& $brightness==1 )
		  {$color=3;}  //blue
		 else if($hue== 120 && $saturation==1 && $brightness==1)
		  {$color=6;}   //green
		 else if($hue== 0 && $saturation==0 && $brightness==0 )
	  {$color=7;}//black or off
      if($color!== null)
	  {$success = performDeviceControl($db,$endpointId, $color,$user_id,$namespace,$name,$instance);
      if ($success) {
        return $response->withJson(['status' => 'success', 'color' => $color]);
      } else {
        return $response->withJson(['status' => 'error', 'message' => 'An error occurred while controlling the device.']);
	  }}
	  else
	   return $response->withJson(['status' => 'error', 'message' => 'An error occurred while controlling the device.']);
      
    }
	elseif ($header['namespace'] === 'Alexa.ModeController') {
         $mode = $data['payload']['mode'];
     $success = performDeviceControl($db,$endpointId, $mode,$user_id,$namespace,$name,$instance);
      if ($success) {
        return $response->withJson(['status' => 'success', 'mode' => $mode]);
      } else {
        return $response->withJson(['status' => 'error', 'message' => 'An error occurred while controlling the device.']);
      }
    }
	elseif ($header['namespace'] === 'Alexa.ToggleController') {
		     $toggleState = $name === 'TurnOn' ? 'ON' : 'OFF';
     $success = performDeviceControl($db,$endpointId, $toggleState,$user_id,$namespace,$name,$instance);
      if ($success) {
        return $response->withJson(['status' => 'success', 'toggleState' => $toggleState]);
      } else {
        return $response->withJson(['status' => 'error', 'message' => 'An error occurred while controlling the device.']);
      }

    }
});

function mqttSendData($node_id,$mqtt_payload,$gateway_id,$connectivity,$module_type,$timestamp,$user_id,$name,$to,$param,$value)
{
	       error_log("mqtt start...");
	       // MQTT broker details
			$server = 'efactor.tech';
			$port = 1883;
			$username = 'efactorpw';
			$password = '123456';

			// MQTT topic details
			$efactor = 'Efactor';
			$things = 'things';
			$command = 'command';
			$node_id=$node_id;
			$ef_module_type=$module_type;
			$ef_gateway_id=$gateway_id;
			$ef_timestamp=$timestamp;
			$ef_user_id=$user_id;
			$ef_name=$name;
			$ef_to=$to;
			$ef_param=$param;
			$ef_value=$value;
			// MQTT payload
			$payload = $mqtt_payload;
			if($ef_module_type=='WIFI')
			{
			
			$topic = "$efactor/$things/$node_id/$command";
			$mqtt_json = [
				'command_type' => 'SET_DEVICE_STATUS',
				'address' => $node_id,
				'device_id'=>$node_id,
				'payload' => $payload
			];
			}
			else
			{
				
			$topic = "$efactor/$things/$ef_gateway_id/$command";
			$mqtt_json = [
				'command_type' => 'SET_DEVICE_STATUS',
				'gateway_id' => $ef_gateway_id,
				'device_id'=>$node_id,
				'payload' => $payload
			];
			}
			$mqtt_json_1 = json_encode($mqtt_json);
			// create MQTT client
			$mqtt = new Bluerhinos\phpMQTT($server, $port, 'phpMQTT-publisher');
			// connect to MQTT broker
			if ($mqtt->connect(true, NULL, $username, $password)) {
				// publish message
				$mqtt->publish($topic, $mqtt_json_1);
				{
					// set the URL of the FCM endpoint
                   $url = 'https://fcm.googleapis.com/fcm/send';
							 $event_data = array(
					"event_version" => "v1",
					"id" => "1",
					"event_type" => "efactor.event.node_params_changed",
					"timestamp" => $ef_timestamp,
					"description" => "$ef_name  $ef_param  updated",
					"event_data" => array(
						"user_id" => $ef_user_id,
						"node_id" => $node_id,
						"payload" => array(
							$ef_name => array(
								$ef_param => $ef_value
							)
						)
					)
				);

				$data = array(
					'to' => $to,
					'data' => array(
						'title' => 'Efactor SmartThings',
						'body' => $ef_name . ' ' . $ef_param . ' updated',
						'event_data_payload' => json_encode($event_data)
					)
				);

			  

				error_log( 'Message published successfully!');
				} 
				// close MQTT connection
				$mqtt->close();
				error_log("Data sent in the request: " . json_encode($data));

											  // set the headers for the request
								$headers = array(
					'Authorization: key=AAAAtliCgqw:APA91bH5lLMRevBI3pgwbWKgTjXOKiIDaI8BWwta4xMSywZzPPo418reyc2DGboJDklZuyLxfkXS4i-uwAATEKS2jc-BnCEPeTVfKPU3JIdRaavhrqrAfLT0ld-fOdZBmC8GnnU7L3SX',
					'Content-Type: application/json'
				);

				// initialize a curl session
				$curl = curl_init($url);

				// set the options for the curl session
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

				// Log the data sent in the request
				error_log("Data sent in the request: " . json_encode($data));

				// execute the curl session
				$response = curl_exec($curl);

				// Log the response received
				error_log("Response received: " . $response);

				// check for errors
				if (curl_errno($curl)) {
					$error_msg = curl_error($curl);
					error_log("cURL error: " . $error_msg);
				}

				// close the curl session
				curl_close($curl);

			} else {
				error_log( 'MQTT connection failed.');
			}
}
function performDeviceControl($db,$endpointId, $value,$user_id,$namespace,$name,$instance) {
	$query = "SELECT * FROM `users` WHERE `id` = ?";
    $stmt = $db->link->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        error_log("No user found with email " . $email);
    } else {
        $row = $result->fetch_assoc();
        $access_token = $row['access_token'];
        if (is_null($access_token)) {
            error_log("User id does not exist");
        }
	    else
		{
		    $query = "SELECT * FROM `efactorproduct` WHERE `access_token` = ?";
			$stmt = $db->link->prepare($query);
            $stmt->bind_param("s", $access_token);
            $stmt->execute();
            $result = $stmt->get_result();

              if (!$result) {
			  error_log("No device found " . $access_token);}
			  else {
            while ($row = $result->fetch_assoc()){
			if($row['node_id']==$endpointId)
			{ 
				$connectivity=$row['internet'];
				$gateway_id=$row['add_gateway'];
				$module_type=$row['module_type'];
				$to=$row['mob_device_token'];
                $device_name=$row['name'];
                $ef_user_id=9;
                $timestamp=round(microtime(true) * 1000);
			if ($name=='SetRangeValue' && $namespace=='Alexa.RangeController')
			{   
		        $node_id=$endpointId;
				$Speed=$value;
				$payload = "A1C00" . $Speed . "0A";
				$param='Speed';
				mqttSendData($node_id,$payload,$gateway_id,$connectivity,$module_type,$timestamp,$ef_user_id,$device_name,$to,$param,intval($Speed));
			/*$query = "UPDATE `efactorproduct` SET `speed` = ? ,`payload`= ? WHERE `node_id` = ?";
            $stmt = $db->link->prepare($query);
            $stmt->bind_param("iss", $Speed,$payload, $node_id);
            $stmt->execute();*/
            error_log("speed for  node_id $node_id updated in efactorproduct table");
			return true;
			}
			elseif ($name=='SetBrightness' && $namespace=='Alexa.BrightnessController')
			{   $node_id=$endpointId;
				$brightness=$value;
				$param='brightness';
				if($brightness==10||$brightness==20)
				 $value=1;
				else if($brightness==40)
					$value=2; 
				elseif($brightness==60)
					$value=3; 
				elseif($brightness==80)
					$value=4; 
				elseif($brightness==100)
					$value=5; 
			    $payload = "A1C40" . $value . "0A";
				mqttSendData($node_id,$payload,$gateway_id,$connectivity,$module_type,$timestamp,$ef_user_id,$device_name,$to,$param,$brightness);
			/*$query = "UPDATE `efactorproduct` SET `brightness` = ?,`payload`= ? WHERE `node_id` = ?";
            $stmt = $db->link->prepare($query);
            $stmt->bind_param("iss", $brightness,$payload, $node_id);
            $stmt->execute();*/
            error_log("brightness for  node_id $node_id updated in efactorproduct table");
			return true;
			}
			elseif ($namespace == 'Alexa.PowerController') {
              $node_id = $endpointId;
              $power = ($value == 'ON') ? "true" : "false";
			  if($value=="ON")
                     $payload='A1C2010A';
				 else
					 $payload='A1C2000A';
				 $param='power';
				
				mqttSendData($node_id,$payload,$gateway_id,$connectivity,$module_type,$timestamp,$ef_user_id,$device_name,$to,$param,filter_var($power, FILTER_VALIDATE_BOOLEAN));
				/*$query = "UPDATE `efactorproduct` SET `power` = ?,`payload`=? WHERE `node_id` = ?";
				$stmt = $db->link->prepare($query);
				$stmt->bind_param("sss", $power,$payload, $node_id);
				$stmt->execute();*/

				error_log("power for node_id $node_id updated in efactorproduct table");
				return true;
            }
              elseif ($namespace == 'Alexa.ColorController') {
                $node_id = $endpointId;
			    $Hue = $value;
			    $payload = "A1C30" . $Hue . "0A";
				 $param='led_colour';
                mqttSendData($node_id,$payload,$gateway_id,$connectivity,$module_type,$timestamp,$ef_user_id,$device_name,$to,$param,intval($Hue));
				if($Hue>=7)
				{
				$led_indication=$Hue;
				/*$query = "UPDATE `efactorproduct` SET `led_indication`=?,`payload` = ? WHERE `node_id` = ?";
				$stmt = $db->link->prepare($query);
				$stmt->bind_param("sss", $led_indication,$payload, $node_id);
				$stmt->execute();}
				else
				{$query = "UPDATE `efactorproduct` SET `Hue` = ?,`payload`= ? WHERE `node_id` = ?";
				$stmt = $db->link->prepare($query);
				$stmt->bind_param("iss", $Hue,$payload, $node_id);
			    $stmt->execute();*/
				}

				error_log("color for node_id $node_id updated in efactorproduct table");
				return true;
            }
					
			 elseif ($namespace == 'Alexa.ModeController' && $instance=='EFACTOR.Fan.Direction') {
              $node_id = $endpointId;
			  if($value=='CLOCKWISE')
			  {$Direction = 0;
		      $payload='A1C0080A';}
		      else
			  { $Direction = 1;  
		      $payload='A1C0080A';}
                $param='Direction';
				mqttSendData($node_id,$payload,$gateway_id,$connectivity,$module_type,$timestamp,$ef_user_id,$device_name,$to,$param,intval($Direction));
			/*	$query = "UPDATE `efactorproduct` SET `Direction` = ?,`payload`=? WHERE `node_id` = ?";
				$stmt = $db->link->prepare($query);
				$stmt->bind_param("iss", $Direction,$payload, $node_id);
				$stmt->execute();*/

				error_log("Direction $value for node_id $node_id updated in efactorproduct table");
				return true;
            }
			elseif ($namespace == 'Alexa.ToggleController'  && $instance =='EFACTOR.Fan.Light') {
              $node_id = $endpointId;
			  $color_value=$row['Hue'];
              $led_indication = ($value == 'ON') ? "true" : "false";
			  $param='led';
			  if($led_indication=="true" && $color_value!=7)
			  $payload="A1C30". $color_value ."0A";
		       else
			  $payload='A1C3070A'; 
              mqttSendData($node_id,$payload,$gateway_id,$connectivity,$module_type,$timestamp,$ef_user_id,$device_name,$to,$param,filter_var($led_indication, FILTER_VALIDATE_BOOLEAN));
			/*	$query = "UPDATE `efactorproduct` SET `led_indication`=?,`payload` = ? WHERE `node_id` = ?";
				$stmt = $db->link->prepare($query);
				$stmt->bind_param("sss", $led_indication,$payload, $node_id);
				$stmt->execute();*/

				error_log("led_indication for node_id $node_id updated in efactorproduct table");
				return true;
            }
			
			}
			}
		}
	}
  
}
}
function generateDiscoveryResponse($db,$user_id) {
	
    $query = "SELECT * FROM `alexa` WHERE `user_id` = ?";
    $stmt = $db->link->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        error_log("No user found with user_id " . $user_id);
    } else {
        $row = $result->fetch_assoc();
        $access_token = $row['access_token'];
        if (is_null($access_token)) {
            error_log("User id does not exist");
        }
	    else
		{
		$query = "SELECT * FROM `efactorproduct` WHERE `access_token` = ?";
			$stmt = $db->link->prepare($query);
            $stmt->bind_param("s", $access_token);
            $stmt->execute();
            $result = $stmt->get_result();

              if (!$result) {
			  error_log("No device found " . $access_token);}
			  else {
			 // $row = $result->fetch_assoc();
			 $devices = [];
            while ($row = $result->fetch_assoc()){
            $endpointId=$row['node_id'];
            $friendlyName=$row['name'];
           
            $manufacturerName='Factor Enovations Pvt Ltd';
            $device_type=$row['device_type'];
            $displayCategories = [];
           if (strcasecmp($device_type, 'FAN') == 0) {
           $displayCategories = ['FAN'];
		    $description='Smart Fan with remote control features and adjustable speed ,color, direction';
           } elseif (strcasecmp($device_type, 'LIGHT') == 0) {
             $displayCategories = ['LIGHT'];
			 $description='Smart Light with remote control features and adjustable color,  brightness';
           } elseif (strcasecmp($device_type, 'SWITCH') == 0) {
            $displayCategories = ['SWITCH'];
			$description = 'Smart switch with remote control features';
            }elseif (strcasecmp($device_type, 'OUTLET') == 0) {
            $displayCategories = ['SMARTPLUG'];
			$description = 'Smart plug with remote control and energy monitoring features';
            }

    else {
        $displayCategories = ['OTHER'];
		$description = 'Smart device with remote control and energy monitoring features';
    }
    $device = [
        "endpointId" => $endpointId,
        "friendlyName" => $friendlyName,
        "description" => $description,
        "manufacturerName" => $manufacturerName,
        "displayCategories" => $displayCategories
    ];
    
    $devices[] = $device; 
}
 
$endpoints = [];

foreach ($devices as $device) {
    $capabilities = [];

    // Add different capabilities for lights and fans
    if ($device["displayCategories"][0] === "LIGHT") {
        $capabilities = [
            [
                "type" => "AlexaInterface",
                "interface" => "Alexa.PowerController",
                "version" => "3",
				 "instance" => "EFACTOR.Light.Power",
                "properties" => [
                    "supported" => [
                        ["name" => "powerState"]
                    ],
                    "proactivelyReported" => false,
                    "retrievable" => false
                ]
            ],
            [
                "type" => "AlexaInterface",
                "interface" => "Alexa.BrightnessController",
                "version" => "3",
				 "instance" => "EFACTOR.Light.Brightness",
                "properties" => [
                    "supported" => [
                        ["name" => "brightness"]
                    ],
                    "proactivelyReported" => false,
                    "retrievable" => false
                ]
            ],
			 [
            "type" => "AlexaInterface",
            "interface" => "Alexa.ColorController",
            "version" => "3",
            "instance" => "EFACTOR.Light.Color",
            "properties" => [
                "supported" => [
                    ["name" => "color"]
                ],
                "proactivelyReported" => false,
                "retrievable" => false
            ]
			]
        ];
    } elseif ($device["displayCategories"][0] === "SWITCH" ||$device["displayCategories"][0] === "OTHER") {
        $capabilities = [
            [
                "type" => "AlexaInterface",
                "interface" => "Alexa.PowerController",
                "version" => "3",
				 "instance" => "EFACTOR.Switch.Power",
                "properties" => [
                    "supported" => [
                        ["name" => "powerState"]
                    ],
                    "proactivelyReported" => false,
                    "retrievable" => false
                ]
            ]
        ];
    }elseif ($device["displayCategories"][0] === "SMARTPLUG") {
        $capabilities = [
            [
                "type" => "AlexaInterface",
                "interface" => "Alexa.PowerController",
                "version" => "3",
				 "instance" => "EFACTOR.Smartplug.Power",
                "properties" => [
                    "supported" => [
                        ["name" => "powerState"]
                    ],
                    "proactivelyReported" => false,
                    "retrievable" => false
                ]
            ]
        ];
    }
	elseif ($device["displayCategories"][0] === "FAN") {
        $capabilities = [
            [ 
                "type" => "AlexaInterface",
                "interface" => "Alexa.PowerController",
                "version" => "3",
				 "instance" => "EFACTOR.Fan.Power",
                "properties" => [
                    "supported" => [
                        ["name" => "powerState"]
                    ],
                    "proactivelyReported" => false,
                    "retrievable" => false
                ]
            ],
            [
               "type" => "AlexaInterface",
        "interface" => "Alexa.ModeController",
        "version" => "3",
        "instance" => "EFACTOR.Fan.Direction",
        "capabilityResources" => [
            "friendlyNames" => [
                [
                    "@type" => "text",
                    "value" => [
                        "text" => "Direction",
                        "locale" => "en-IN"
                    ]
                ]
            ]
        ],
        "properties" => [
            "proactivelyReported" => false,
            "retrievable" => false,
            "supported" => [
                [
                    "name" => "mode"
                ]
            ]
        ],
        "configuration" => [
            "ordered" => false,
            "supportedModes" => [
                [
                    "value" => "CLOCKWISE",
                    "modeResources" => [
                        "friendlyNames" => [
                            [
                                "@type" => "text",
                                "value" => [
                                    "text" => "Clockwise",
                                    "locale" => "en-IN"
                                ]
                            ],
							 [
                                "@type" => "text",
                                "value" => [
                                    "text" => "Forward",
                                    "locale" => "en-IN"
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "value" => "COUNTERCLOCKWISE",
                    "modeResources" => [
                        "friendlyNames" => [
                            [
                                "@type" => "text",
                                "value" => [
                                    "text" => "Counterclockwise",
                                    "locale" => "en-IN"
                                ]
                            ],
							 [
                                "@type" => "text",
                                "value" => [
                                    "text" => "Reverse",
                                    "locale" => "en-IN"
                                ]
                            ]
                        ]
                    ]
                ]
            ]
            ]
         ],
		 [
               "type" => "AlexaInterface",
        "interface" => "Alexa.ToggleController",
        "version" => "3",
        "instance" => "EFACTOR.Fan.Light",
        "capabilityResources" => [
            "friendlyNames" => [
                [
                    "@type" => "text",
                    "value" => [
                        "text" => "Light",
                        "locale" => "en-IN"
                    ]
                ]
            ]
        ],
        "properties" => [
            "proactivelyReported" => false,
            "retrievable" => false,
            "supported" => [
                [
                    "name" => "toggleState"
                ]
            ]
        ]
       ],
	     [
               "type" => "AlexaInterface",
        "interface" => "Alexa.RangeController",
        "version" => "3",
        "instance" => "EFACTOR.Fan.Speed",
        "capabilityResources" => [
            "friendlyNames" => [
			    [
                    "@type" => "asset",
                    "value" => [
                        "assetId" => "Alexa.Setting.FanSpeed",
                        
                    ]
                ],
                [
                    "@type" => "text",
                    "value" => [
                        "text" => "Speed",
                        "locale" => "en-IN"
                    ]
                ]
            ]
        ],
        "properties" => [
            "proactivelyReported" => false,
            "retrievable" => false,
            "supported" => [
                [
                    "name" => "rangeValue"
                ]
            ]
        ],
        "configuration" => [
            "ordered" => false,
            "supportedRange" => [
                
                "minimumValue"=>1,
                "maximumValue"=>5,
                 "precision"=>1	
            ],
		 "presets"=>[
		 [
		 "rangeValue"=>5,
		 "presetResources"=>[
		 "friendlyNames"=>[
		 [
		 "@type"=>"asset",
		 "value"=>[
		 "assetId"=>"Alexa.Value.Maximum"]
		 ],
		 
		 [
		 "@type"=>"asset",
		 "value"=>[
		 "assetId"=>"Alexa.Value.High"]
		 ],
		 [
		 "@type"=>"text",
		 "value"=>[
		 "text"=>"Highest",
		 "locale"=>"en-IN"]
		 ]
		 ]
		 ]
		 ],
		  [
		 "rangeValue"=>1,
		 "presetResources"=>[
		 "friendlyNames"=>[
		 [
		 "@type"=>"asset",
		 "value"=>[
		 "assetId"=>"Alexa.Value.Minimum"]
		 ],
		 
		 [
		 "@type"=>"asset",
		 "value"=>[
		 "assetId"=>"Alexa.Value.Low"]
		 ],
		 [
		 "@type"=>"text",
		 "value"=>[
		 "text"=>"Lowest",
		 "locale"=>"en-IN"]]]]]]]
		 ],
		[
		"type"=> "AlexaInterface",
		"interface"=> "Alexa.ColorController",
		"instance"=> "EFACTOR.Fan.Color",
		"version"=> "3",
		"properties"=> [
			"supported"=> [
				[
					"name"=>"color"
				]
			],
			"proactivelyReported"=> false,
			"retrievable"=> false
		]
        ] 
        ];
    }

    $endpoints[] = [
        "endpointId" => $device["endpointId"],
        "friendlyName" => $device["friendlyName"],
        "description" => $device["description"],
        "manufacturerName" => $device["manufacturerName"],
        "displayCategories" => $device["displayCategories"],
		"cookie" => new stdClass(),
        "capabilities" => $capabilities
        
    ];
}
$discoveryResponse = [
    "event" => [
        "header" => [
            "namespace" => "Alexa.Discovery",
            "name" => "Discover.Response",
            "payloadVersion" => "3",
            "messageId" => uniqid()
        ],
        "payload" => [
            "endpoints" => $endpoints
        ]
    ]
];

//echo json_encode($discoveryResponse,JSON_PRETTY_PRINT );
	return $discoveryResponse;		  
			  }
			  
		}
	
	
	}
}
	$app->run();
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	



