<?php


/***************************************************/


// Set the path for the log file
$logFile = '/var/www/html/api.log';

// Function to get all headers (for older PHP versions)
function get_all_headers() {
    if (!function_exists('getallheaders')) {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    } else {
        return getallheaders();
    }
}

date_default_timezone_set('Asia/Kolkata');

// Collect log data
$logData = [
    'timestamp'   => date('Y-m-d H:i:s'),
    'headers'     => get_all_headers(),
    'body'        => file_get_contents('php://input')
];

// Format log as JSON
$logEntry = json_encode($logData, JSON_PRETTY_PRINT);

// Append to log file
file_put_contents($logFile, $logEntry . "\n\n", FILE_APPEND);






/****************************************************/
$response = array();
include('config.php');
include('functions.php');
include('phpMQTT.php');
header('Content-Type: application/json');

header('Content-Type: application/x-www-form-urlencoded');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

/*
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', false);
while (ob_get_level()) {
    ob_end_flush();
}

// Set headers for real-time output
header('Content-Type: text/plain');
header('Cache-Control: no-cache');
header('X-Accel-Buffering: no'); // For Nginx servers

*/





/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

/*include('alexa_control.php');
include('alexa_endpoint.php');
include('alexa_response.php');
include('alexa_report.php');
include('alexa_const_errors.php');*/
/*-----------------------------------------------------*/
$req = file_get_contents( 'php://input' );
//$rawdata = file_get_contents('php://input');
//$rawdata = $HTTP_RAW_POST_DATA;
//echo $req; 
$json_data = json_decode($req);
//echo $json_data;
header('Content-Type: application/json');
//header('Authorization: Bearer {token}');
header('Authorization: API Key');
//$not_found = false; // change it to true if you wish device not found response
//$user_check_faild = false; // change to true if you wish user credential error response

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




/*********************************/


/*
function generateAppId($brandName) {
    // Clean brand name for prefix
    $prefix = preg_replace('/[^A-Z0-9]/', '_', strtoupper($brandName));
    
    // Generate random component (8 characters)
    $randomPart = bin2hex(random_bytes(4)); // 8 character hex string
    
    // Get timestamp in base36
    $timestamp = base_convert(time(), 10, 36);
    
    return $prefix . '_' . strtoupper($timestamp) . '_' . strtoupper($randomPart);
}

*/


/*******************************/
function stringToAscii($str) {
    $asciiArray = [];
    for ($i = 0; $i < strlen($str); $i++) {
        $asciiArray[] = ord($str[$i]); // Convert each character to ASCII
    }
    return implode(',', $asciiArray); // Join ASCII values with a comma
}



function asciiToHex($asciiString) {
    $asciiValues = explode(',', $asciiString); // Split ASCII values
    $hexArray = [];
    foreach ($asciiValues as $ascii) {
        $hexArray[] = dechex($ascii); // Convert each ASCII value to hex
    }
    return implode(' ', $hexArray); // Join hexadecimal values with space or comma
}



function logMessage($message) {
    echo "<pre>[LOG] " . $message . "</pre>";
}





/*******************************/
function handleResponse($topic, $message) {



    global $response_topic;

    if ($topic == 'Efactor/things/FFFFFFFFFFFFFFFF7EC2000000004200/event') {
        // Process the response message
        echo "Received response: " . $message . "\n";
        
        // Here, you can exit the loop or take further actions
        exit;
    }
}




/*
function hexToFloat($hex) { 
    $hex = str_pad($hex, 8, '0', STR_PAD_LEFT); 
    $reversedHex = implode('', array_reverse(str_split($hex, 2))); 
    $binary = pack('H*', $hex); 
    $float = unpack('f', $binary); 
    return $float[1];
}*/
function hexToFloat($hex) {  
    $hex = str_pad($hex, 8, '0', STR_PAD_LEFT);  
    $reversedHex = implode('', array_reverse(str_split($hex, 2)));  
    $binary = pack('H*', $reversedHex);  
    $float = unpack('f', $binary);  
    return $float[1];
}




function sendMqttMessage($mqtt, $nodeid, $deviceid, $payload) {
    global $efactor, $things, $command;


if($address != 'NULL')
{

    $topic = "$efactor/$things/$nodeid/$command";

    $mqtt_json = array(
        "command_type" => "GET_DEVICE_STATUS",
          "address" => $nodeid,
        "device_id" => $deviceid,
        "payload" => $payload
    );
    $mqtt_json_1 = json_encode($mqtt_json);

    if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
        $mqtt->publish($topic, $mqtt_json_1);
    }

}
else
{

if($deviceid != 'NULL')
{
$topic = "$efactor/$things/$deviceid/$command";

    $mqtt_json = array(
        "command_type" => "GET_DEVICE_STATUS",
          "address" => $deviceid,
        "device_id" => $deviceid,
        "payload" => $payload
    );
    $mqtt_json_1 = json_encode($mqtt_json);


    if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
        $mqtt->publish($topic, $mqtt_json_1);
    }



}

}

   // usleep(1000000); // Wait for 550 milliseconds
}

/*---------------------------------------------------------*/



function sendNotification($to, $title, $body, $eventDataPayload) {
    $url = 'https://fcm.googleapis.com/fcm/send';

    $data = array(
        'to' => $to,
        'data' => array(
            'title' => $title,
            'body' => $body,
            'event_data_payload' => $eventDataPayload
        )
    );

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

    // execute the curl session
    $response = curl_exec($curl);

    // check for errors
    if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
        // handle the error
    }

    // close the curl session
    curl_close($curl);
}


/*******************************APPID********************************************/




if (isset($_GET['action']) && $_GET['action'] == 'Generate_APP_Id') {
    


$brandName = $_GET['Brand_name'];


function generateAppId($brandName) {
    // Clean brand name for prefix
    $prefix = preg_replace('/[^A-Z0-9]/', '_', strtoupper($brandName));
    
    // Generate random component (8 characters)
    $randomPart = bin2hex(random_bytes(4)); // 8 character hex string
    
    // Get timestamp in base36
    $timestamp = base_convert(time(), 10, 36);
    
    return $prefix . '_' . strtoupper($timestamp) . '_' . strtoupper($randomPart);
}






$appId = generateAppId($brandName);

//echo $appId;


//$db->query("update users set APPID='$appId', App_register_at='$timestamp'  where brand_name='$brandName'");

$db->query("insert into app_register set Brand_name='$brand_name',app_name='$brand_name', APPID='$appId', created_at='$timestamp', app_version='$app_version'");

echo json_encode([
                    'status' => 'success',
                    'brand_name' => $brandName,
                    'app_id' => $appId,
                    'timestamp' => date('Y-m-d H:i:s')
                ]);

exit;



}





/*****************************records************************************/



if (isset($_GET['action']) && $_GET['action'] == 'device_records') {
    

    if (!isset($_GET['node_id']) || empty($_GET['node_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Node ID is required.'
        ]);
        exit;
    }

    $node_id = $_GET['node_id'];
    $log_file = '/var/www/html/device_status.log';

        if (!file_exists($log_file)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Log file not found.'
        ]);
        exit;
    }

    
    $last_operations = [];

    
    $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    
    foreach (array_reverse($lines) as $line) {
        if (strpos($line, "node ID $node_id") !== false) {
            $last_operations[] = $line;
            if (count($last_operations) == 10) {
                break; 
            }
        }
    }

    
    if (!empty($last_operations)) {
        echo json_encode([
            'status' => 'success',
            'data' => array_reverse($last_operations) 
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No operations found for the specified Node ID.'
        ]);
    }

} 










/******************************************************************/
/*-----------------------------forgotpwd--------------------------*/

if (isset($_GET['action']) && $_GET['action']=='forgotpassword_1') {

 /*$query = "SELECT * FROM `users` where (email='".$_POST['email']."' or phone='".$_POST['phone']."') and status='active' and deleted=0 and disable_login=0";*/


$req = file_get_contents( 'php://input' ); 
$json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['phone'])) {
    $phone = $json_data['phone'];
    
}

$query = "SELECT * FROM `users` where phone='$phone'";
  $num = $db->num_rows($query); 

  if($num>0)
  { 

/***********************************/
   $otp = rand(100000,999999);
	    //  $phone = "+917893325350"; // target number; includes ISD
	     // $api_key = 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX'; // API Key
	     // $req = "https://2factor.in/API/V1/".$api_key."/SMS/".$phone."/".$otp;

//$phone = '".$_POST['phone']."';
//$phone = $_POST['phone'];
//$email= $_POST['email'];

         $password = '&password=12345678';
         $type = '&type=TEXT';
         $mobile= '&mobile=';
         $sender = '&sender=EFACTR';
         $message_1 = '&message=Dear%20customer%2C%20Your%20OTP%20for%20sms%20notification%20is%20';  //%7B
         $message_2 = '%20.%20Use%20this%20OTP%20to%20register%20%20EFACTR';    //%7D
         $PEID = '&PEID=1201159585221010777';
         $HeaderId = '&HeaderId=1205165538224202626';
         $templateId = '&templateId=1207165579677863503';

         $req="http://sms.softpal.com/sendsms/sendsms.php?username=SPEFACTR".$password.$type.$mobile.$phone.$sender.$message_1.$otp.$message_2.$PEID.       $HeaderId.$templateId;


           //working
        

	      $sms = file_get_contents($req);
	      $sms_status = json_decode($sms, true);

           //echo $req; 
         //  $db->query("insert into otps set phone='$phone',otp='".$otp."',password='".$_POST['password']."'");

            $db->query("insert into otps set phone='$phone',otp='".$otp."'");


        $response['status']='success';
        $response['description']='Signup request successful. Verification code sent to phone';


       //  $response['otp']=$otp;
	      if($sms_status['Status'] !== 'Success') {
		  $err['error'] = 
                     'Could not send OTP to '.$phone;
	      } 
         else{
             echo 'success';
         
        }

      $response['status']='success';
      
  }
  else
  {
  	 
        header('HTTP/1.1 400');
  $response['status']='failure';
  $response['error_code']='101037';
  $response['description']='Not a regitered user';
  }

}


/*************************************************************/







/******************************************************************/
/*-----------------------------forgotpwd--------------------------*/

if (isset($_GET['action']) && $_GET['action']=='forgotpassword') {

 /*$query = "SELECT * FROM `users` where (email='".$_POST['email']."' or phone='".$_POST['phone']."') and status='active' and deleted=0 and disable_login=0";*/


$header = apache_request_headers();  
     foreach ($header as $key => $val) {

$req = file_get_contents( 'php://input' ); 
$json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['phone'])) {
    $phone = $json_data['phone'];
    
}


//$phone = $_POST['phone'] ?? '';

$query = "SELECT * FROM users WHERE phone = '$phone'";
$num = $db->num_rows($query); 

if ($num > 0) {

    /***********************************/
    $otp = rand(100000, 999999); // Only generate OTP once

    /***********************************/
    // SMS API parameters
    $password   = '&password=12345678';
    $type       = '&type=TEXT';
    $mobile     = '&mobile=' . $phone;
    $sender     = '&sender=FACTEF';
    $message_1  = '&message=Dear%20customer%2C%20Your%20OTP%20for%20resetting%20your%20password%20is%20';
    $message_2  = '%20.%20Please%20use%20this%20OTP%20to%20regain%20access%20to%20your%20account%20with%20-%20E-FACTOR%2E%20Thank%20you%21%20FACTEF';
    $PEID       = '&PEID=1701171402738797277';
    $HeaderId   = '&HeaderId=1705171438125776933';
    $templateId = '&templateId=1707171446494145690';

    // Final request string
    $req = "http://sms.softpal.com/sendsms/sendsms.php?username=SPEFACTR" . $password . $type . $mobile . $sender . $message_1 . $otp . $message_2 . $PEID . $HeaderId . $templateId;

    // Send the request
    $sms = file_get_contents($req);

    // Try to decode JSON (depends on actual API format)
    $sms_status = json_decode($sms, true);

    // Insert OTP into DB
    $db->query("INSERT INTO otps SET phone = '$phone', otp = '$otp'");

    // Response
    echo 'status_1';
    $response['status'] = 'success';
    $response['description'] = 'Forgot request successful. Verification code sent to phone';

} else {
    header('HTTP/1.1 400 Bad Request');
    $response['status'] = 'failure';
    $response['error_code'] = '101037';
    $response['description'] = 'Not a registered user';
}

exit;
}

}

































/*******************password_verify***************************/
if (isset($_GET['action']) && $_GET['action']=='resetpassword') {

$req = file_get_contents( 'php://input' ); 
$json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['phone']) && isset($json_data['password']) && isset($json_data['verification_code'])) {
    $phone = $json_data['phone'];
    $password = $json_data['password'];
    $verification_code = $json_data['verification_code'];
}


 if($phone!='' && $verification_code!='')
   {
$query = "SELECT * FROM `otps` where phone='$phone' and otp='$verification_code'";
$doctors = $db->get_results($query); 
$row = $db->get_row($query); 
//$password=$_POST['password'];
//echo $password;
$num = $db->num_rows($query); 

if($num==0)
{
  // $response['num']=$num;
  header('HTTP/1.1 400');
  $response['status']='failure';
  $response['error_code']='101037';
  $response['description']='Verification code is incorrect';
}
else
{

/*$db->query("insert into users set phone='".$_POST['phone']."',email='".$_POST['email']."',password='$password',user_type='staff',created_at='".$dt."'"); */


$db->query("update users set password='$password' where phone='$phone'");


$response['status']='success';
$response['description']='OTP Verification Done';
}

   }
   else
   {
        header('HTTP/1.1 400');
        $response['status']='failure';
        $response['error_code']='101037';
        $response['description']='phone number or verification_code is missing';

   }


}

/************************************************************/

/************************change password*********************/
if (isset($_GET['action']) && $_GET['action']=='password')
{

$header = apache_request_headers();  
foreach ($header as $key => $val) {


$req = file_get_contents( 'php://input' );
//echo $req; 
$json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['password']) && isset($json_data['newpassword'])) {
    $oldpassword = $json_data['password'];
    $newpassword = $json_data['newpassword'];
}


$query = "SELECT * FROM users where access_token='$val'";  
$doctors = $db->get_results($query); 
$num_dev = $db->num_rows($query);
$row = $db->get_row($query); 
$old_pw_db=$row['password'];

$arr=array();


//$oldpassword=$_POST['password'];
//$newpassword=$_POST['newpassword'];


//echo $oldpassword;
//echo $old_pw_db;

if(strcmp($oldpassword, $old_pw_db) !== 0)
{

$connectivity_1 =json_encode(["status"=>'failure',
                 "error_code"=>'101005',
              "description"=>'Old password not matched']);

header('HTTP/1.1 400');
exit($response['status']=$connectivity_1);


}

else
{

if ((mb_strlen($newpassword) <= 8) || (!preg_match("#[0-9]+#",$newpassword)) || (!preg_match("#[A-Z]+#",$newpassword)) || (!preg_match("#[a-z]+#",$newpassword)) || (!preg_match("#[\W]+#",$newpassword)))
{

         $connectivity_1 =json_encode(["status"=>'failure',
                 "error_code"=>'101005',
              "description"=>'Password length must be between 8 to 256 characters. It should be alpha-numeric without any whitespace. It should contain atleast one uppercase, one lowercase character and a number']);

header('HTTP/1.1 400');
exit($response['status']=$connectivity_1);

    }


else

{

$connectivity_2 =json_encode(["status"=>'success',
              "description"=>'Password changed successfully']);
$db->query("update users set password='$newpassword' where access_token='$val'");

exit($response['status']=$connectivity_2);

}

}


/*
if(strcmp($oldpassword, $old_pw_db) == 0)
//if($num_dev>0)
{
if ((mb_strlen($_POST["newpassword"]) <= 8) || (!preg_match("#[0-9]+#",$newpassword)) || (!preg_match("#[A-Z]+#",$newpassword)) || (!preg_match("#[a-z]+#",$newpassword)) || (!preg_match("#[\W]+#",$newpassword)))
{

         header('HTTP/1.1 400');
         $response['status']='failure';
         $response['error_code']='101004';
         $response['description']='Password length must be between 8 to 256 characters. It should be alpha-numeric without any whitespace. It should contain atleast one uppercase, one lowercase character and a number';
exit();

    }


   
    else 
    {


       $response['status']='success';
       $response['description']='Password changed successfully';
      //$db->query("update users set password='$newpassword' where access_token='$val'");
      exit();

     }


}
else
{

         header('HTTP/1.1 400');
         $response['status']='failure';
         $response['error_code']='101005';
         $response['description']='Old password not correct';
       exit();


      }

   */
  }
 
}


/***********************************************************/
/******************user_delete_request**********************/







if (isset($_GET['action']) && $_GET['action']=='user_delete_request')
{

$header = apache_request_headers();  
foreach ($header as $key => $val) {

$query = "SELECT * FROM users where access_token='$val'";  
$doctors = $db->get_results($query); 
$num_dev = $db->num_rows($query);
$row = $db->get_row($query); 
$phone=$row['phone'];
//echo $phone;

$arr=array();

/*-------------------------------------------------------*/

if($num_dev>0)
  { 

/***********************************/
 /*  $otp = rand(100000,999999);
	   

         $password = '&password=12345678';
         $type = '&type=TEXT';
         $mobile= '&mobile=';
         $sender = '&sender=EFACTR';
         $message_1 = '&message=Dear%20customer%2C%20Your%20OTP%20for%20sms%20notification%20is%20';  //%7B
         $message_2 = '%20.%20Use%20this%20OTP%20to%20register%20%20EFACTR';    //%7D
         $PEID = '&PEID=1201159585221010777';
         $HeaderId = '&HeaderId=1205165538224202626';
         $templateId = '&templateId=1707171446499577855';

         $req="http://sms.softpal.com/sendsms/sendsms.php?username=SPEFACTR".$password.$type.$mobile.$phone.$sender.$message_1.$otp.$message_2.$PEID.$HeaderId.$templateId;*/





$otp = rand(100000,999999);
//$phone_1 = $_POST['phone'];
$password = '&password=12345678';
$type = '&type=TEXT';
$mobile = '&mobile='; // Assuming you have a mobile number here
$sender = '&sender=FACTEF';
$message_1 = '&message=Dear%20customer%2C%20Your%20OTP%20for%20account%20deletion%20is%20';  //%7B
$message_2 = '%20.%20Please%20use%20this%20OTP%20to%20confirm%20the%20deletion%20of%20your%20account%20with%20E-FACTOR%2E%20Thank%20you%21%20FACTEF';    //%7D
$PEID = '&PEID=1701171402738797277';
$HeaderId = '&HeaderId=1705171438125776933';
$templateId = '&templateId=1707171446499577855';

$req = "http://sms.softpal.com/sendsms/sendsms.php?username=SPEFACTR" . $password . $type . $mobile . $phone . $sender . $message_1 . $otp . $message_2 . $PEID . $HeaderId . $templateId;



//working
        

	      $sms = file_get_contents($req);
	      $sms_status = json_decode($sms, true);

//echo $req; 
            $db->query("insert into otps set phone='".$_POST['phone']."',otp='".$otp."'");


       // $response['status']='success';
      //   $response['description']='Signup request successful. Verification code sent to phone';


    $response['status'] = 'success';
$response['description'] = 'Signup request successful. Verification code sent to phone';

echo json_encode($response); // Convert array to JSON string and output
exit; // Exit script








       /*//  $response['otp']=$otp;
	      if($sms_status['Status'] !== 'Success') {
		  $err['error'] = 
                     'Could not send OTP to '.$phone;
	      } 
         else{
             echo 'success'; 
                header('HTTP/1.1 200');
         $response['status']='success';
         //$response['error_code']='10100';
         //$response['description']='I do not have the permission to delete this account.';
     
             
         
        }

      $response['status']='success';*/



           
  }
  else
  {
  	
         header('HTTP/1.1 400');
         $response['status']='failure';
         $response['error_code']='10100';
         $response['description']='I do not have the permission to delete this account.';
  }

   

  }
 

}



/************************user_delete_confirm*********************/
if (isset($_GET['action']) && $_GET['action']=='user_delete_confirm')
{


$verification_code=$_GET['verification_code'];
$header = apache_request_headers();  
foreach ($header as $key => $val) {

 $query = "SELECT * FROM `otps` where otp='".$_GET['verification_code']."'";

$doctors = $db->get_results($query); 
$row = $db->get_row($query); 
/*$password=$row['password'];
echo $password;*/
$num = $db->num_rows($query);




if($num>0)
{

$db->query("UPDATE efactorproduct SET user_id = 'NULL', access_token = 'NULL', req_status = 'NULL' WHERE access_token='$val");

$db->query("delete from users where access_token='$val'");
 $response['request_status']='success';
 $response['description']='user account deleted successfully';
}
else 
{
        header('HTTP/1.1 400');
        $response['status']='failure';
        $response['error_code']='101037';
        $response['description']='verification_code is not verified';
}



  }
 

}




/***************************************user***********************************************/

if (isset($_GET['action']) && $_GET['action']=='user_123') {

$req = file_get_contents( 'php://input' );
//echo $req; 
$json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['phone'])) {
    $phone_1 = $json_data['phone'];
    
}
if (is_array($json_data) && isset($json_data['email'])) {
    $email_1 = $json_data['email'];
    
}else{
$email_1='';
}

if (is_array($json_data) && isset($json_data['password'])) {
    $password_1 = $json_data['password'];
    
}else{
$password_1='';
}



if (is_array($json_data) && isset($json_data['country_code'])) {
    $country_code = $json_data['country_code'];
    
}else{
$country_code='';
}



if (is_array($json_data) && isset($json_data['user_type'])) {
    $user_type = $json_data['user_type'];
    
}else{
$user_type='';
}





//echo $phone_1;

if(strlen($phone_1)>=10 && $phone_1>0)
    {
    



    // $country_code = $_POST["country_code"];
     $email = $email_1;
     $query = "SELECT * FROM `users` where phone='$phone_1'";
     $num = $db->num_rows($query);
       if($num>0)
      {
         header('HTTP/1.1 400');
         $response['status']='failure';
         $response['error_code']='101006';
         $response['description']=' User account already exist';

      }
      else
      {

    
      /************************/


  if(!empty($password_1) && isset($password_1)) {
   // $password = $_POST["password"];
   // $cpassword = $_POST["cpassword"];
    

if ((mb_strlen($password_1) <= 8) || (!preg_match("#[0-9]+#",$password_1)) || (!preg_match("#[A-Z]+#",$password_1)) || (!preg_match("#[a-z]+#",$password_1)) || (!preg_match("#[\W]+#",$password_1)))
{

         header('HTTP/1.1 400');
         $response['status']='failure';
         $response['error_code']='101004';
         $response['description']='Password length must be between 8 to 256 characters. It should be alpha-numeric without any whitespace. It should contain atleast one uppercase, one lowercase character and a number';

    }
   
    else 
    {
         
     if($country_code =='+91')

       {
         $otp = rand(100000,999999);
	 //  $phone = $_POST['phone'];
         $password = '&password=12345678';
         $type = '&type=TEXT';
         $mobile= '&mobile=';
         $sender = '&sender=EFACTR';
         $message_1 = '&message=Dear%20customer%2C%20Your%20OTP%20for%20sms%20notification%20is%20';  //%7B
         $message_2 = '%20.%20Use%20this%20OTP%20to%20register%20%20EFACTR';    //%7D
         $PEID = '&PEID=1201159585221010777';
         $HeaderId = '&HeaderId=1205165538224202626';
         $templateId = '&templateId=1207165579677863503';

         $req="http://sms.softpal.com/sendsms/sendsms.php?username=SPEFACTR".$password.$type.$mobile.$phone_1.$sender.$message_1.$otp.$message_2.$PEID.$HeaderId.$templateId;


	      $sms = file_get_contents($req);
	      $sms_status = json_decode($sms, true);
         //   $db->query("insert into otps set phone='".$_POST['phone']."',otp='".$otp."',password='".$_POST['password']."',email='".$_POST['email']."',country_code='+91'");
          //  $db->query("insert into otps set phone='$phone_1',otp='".$otp."',password='$password_1',email='$email_1',country_code='+91'");
              $db->query("insert into otps set phone='$phone_1',otp='".$otp."',password='$password_1',email='$email_1',user_type='$user_type',country_code='+91'");            


            $response['status']='success';
            $response['description']='Signup request successful. Verification code sent to phone';

	      if($sms_status['Status'] !== 'Success') {
		  $err['error'] = 
                     'Could not send OTP to '.$phone_1;
              //echo 'error';

                 /*header('HTTP/1.1 400');
                   $response['status']='failure';
                   $response['error_code']='101003';
                   $response['description']='Could not send OTP to phone';*/
	      } 
              else{
           //  echo 'success';
                      $response['status']='success';
            $response['description']='Signup request successful. Verification code sent to phone';
         
             }

           
           

$mail->setFrom('factorenovations23@gmail.com', 'efactor.tech');
$mail->addAddress($email);
$mail->Subject = 'Your One-Time Password';

$mail->Body = "Dear [Recipient's Name],\n\n"
    . "Thank you for using our services. As requested, here is your one-time password (OTP):\n\n"
    . "$otp\n\n"
    . "Please use this code within [Time Limit] minutes to complete your verification process.\n\n"
    . "If you did not request this OTP or have any concerns, please contact our support team immediately.\n\n"
    . "Thank you,\n"
    . "[EFACTOR]";

// Send the email
if ($mail->send()) {
   // echo 'Email sent successfully!';
} else {
    echo "Email could not be sent. Error: {$mail->ErrorInfo}";
}



          

       }

      else if($country_code =='+86')

       {
         
	  /*$user = "FACTORE";
         $key = "35f6747123XX";
         $mobileNumber = "13538084051"; 
          $senderid = "EFACTOR";
          $accusage = 3;
         $countryCode = "+86";
          $mobile = $countryCode . $mobileNumber; 
         //$message = urlencode("Dear Customer, your OTP for mobile verification is {$otp}");  Dear%20Customer%20your%20OTP%20for%20mobile%20verification%20is%
               $message="Dear%20Customer%20your%20OTP%20for%20mobile%20verification%20is%" . $otp;

//$req = "http://world.bulkssms.com/submitsms.jsp?user={$user}&key={$key}&mobile={$mobile}&message={$message}&senderid={$senderid}&accusage={$accusage}";
$req ="http://world.bulkssms.com/submitsms.jsp?user=FACTORE&key=35f6747123XX&mobile=+8613538084051&message=Dear%20Customer%20your%20OTP%20for%20mobile%20verification%20is%20856456&senderid=EFACTOR&accusage=3";*/



$otp = rand(100000,999999);
$user = 'FACTORE';
$key = '35f6747123XX';
$countryCode = '+86'; // Constant country code
//$phoneNumber = $_POST['phone']; // Dynamic phone number
$phoneNumber = $phone_1;

$message = urlencode('【EFACTOR】OTP ' . $otp . ' for your request');
//$message = 'Dear%20Customer%20your%20OTP%20for%20mobile%20verification%20is%20';
$senderid = 'EFACTOR';
$accusage = '3';
$unicode = '1';

$mobile = $countryCode . $phoneNumber;

$url = 'http://world.bulkssms.com/submitsms.jsp?user=' . $user . '&key=' . $key . '&mobile=' . $mobile . '&message=' . $message . '&senderid=' . $senderid . '&accusage=' . $accusage . '&unicode=' . $unicode;

/*$req ="http://world.bulkssms.com/submitsms.jsp?user=FACTORE&key=35f6747123XX&mobile=+8613538084051&message=Dear%20Customer%20your%20OTP%20for%20mobile%20verification%20is%20856456&senderid=EFACTOR&accusage=3";*/

//echo $url;

 $sms = file_get_contents($url);
	      $sms_status = json_decode($sms, true);
         //   $db->query("insert into otps set phone='".$_POST['phone']."',otp='".$otp."',password='".$_POST['password']."',email='".$_POST['email']."',country_code='+91'");
         //   $db->query("insert into otps set phone='$phone_1',otp='".$otp."',password='password_1',email='$email_1',country_code='+86'");
              $db->query("insert into otps set phone='$phone_1',otp='".$otp."',password='password_1',email='$email_1',user_type='$user_type',country_code='+86'");


              $response['status']='success';
              $response['description']='Signup request successful. Verification code sent to phone';

	      if($sms_status['Status'] !== 'Success') {
		  $err['error'] = 
                     'Could not send OTP to '.$phone;
               echo 'error';

                 /*      header('HTTP/1.1 400');
                   $response['status']='failure';
                   $response['error_code']='101003';
                   $response['description']='Could not send OTP to phone';*/
	      } 
              else{
           //  echo 'success';
                      $response['status']='success';
            $response['description']='Signup request successful. Verification code sent to phone';
         
             }

$mail->setFrom('factorenovations23@gmail.com', 'efactor.tech');
$mail->addAddress($email);
$mail->Subject = 'Your One-Time Password';

$mail->Body = "Dear [Recipient's Name],\n\n"
    . "Thank you for using our services. As requested, here is your one-time password (OTP):\n\n"
    . "$otp\n\n"
    . "Please use this code within [Time Limit] minutes to complete your verification process.\n\n"
    . "If you did not request this OTP or have any concerns, please contact our support team immediately.\n\n"
    . "Thank you,\n"
    . "[EFACTOR]";

// Send the email
if ($mail->send()) {
    echo 'Email sent successfully!';
} else {
    echo "Email could not be sent. Error: {$mail->ErrorInfo}";
}


	    


       }
      else
         {
              header('HTTP/1.1 400');
      $response['status']='failure';
      $response['error_code']='101003';
      $response['description']='country code not matching';
          //     echo 'country code not matching';
         }


     }

   
}
else {
      header('HTTP/1.1 400');
      $response['status']='failure';
      $response['error_code']='101003';
      $response['description']='Password or verification code is missing';
}

 /****************************/

      }

}

else 
{
      header('HTTP/1.1 400');
      $response['status']='failure';
      $response['error_code']='101004';
      $response['description']='Phone number is not in correct format';
}

}




/***************************************server_register***********************************************/

if (isset($_GET['action']) && $_GET['action'] == 'server_register') {

$header = apache_request_headers();

    $req = file_get_contents('php://input');
    $json_data = json_decode($req, true);

    $email = $json_data['email'] ?? '';
    $ip_address = $json_data['ip_address'] ?? '';
    $access_token = $val;
    $user_type = 'client';  // fixed as client
    $country_code = $json_data['country_code'] ?? '+91';

    if (empty($email) || empty($ip_address)) {
        header('HTTP/1.1 400');
        echo json_encode([
            'status' => 'failure',
            'error_code' => '101001',
            'description' => 'Missing required fields: email, ip_address or access_token'
        ]);
        exit;
    }

    // Check if server/email already exists
    $check_query = "SELECT * FROM `users` WHERE email='$email'";
    $exists = $db->num_rows($check_query);

  /*  if ($exists > 0) {
        header('HTTP/1.1 400');
        echo json_encode([
            'status' => 'failure',
            'error_code' => '101006',
            'description' => 'User already exists'
        ]);
       // exit;
    }*/

    // Generate OTP
    $otp = rand(100000, 999999);

    // Save OTP and registration details temporarily
 /*   $db->query("INSERT INTO otps 
        SET user_type='$user_type', 
            ip_address='$ip_address', 
            otp='$otp', 
            email='$email', 
            country_code='$country_code', 
            access_token='$access_token'");*/



$db->query("INSERT INTO otps 
        SET ip_address='$ip_address', 
            otp='$otp', 
            email='$email'");




    // Send OTP to email
    $mail->setFrom('factorenovations23@gmail.com', 'efactor.tech');
    $mail->addAddress($email);
    $mail->Subject = 'Your Server Registration OTP';

    $mail->Body = "Dear $email,\n\n"
        . "Your OTP to verify the server registration is:\n\n"
        . "$otp\n\n"
        . "Please enter this within 10 minutes to complete your setup.\n\n"
        . "Thanks,\nEFACTOR";

    if (!$mail->send()) {
        echo json_encode([
            'status' => 'failure',
            'error_code' => '101005',
            'description' => 'Could not send OTP email'
        ]);

     exit;
    } else {
        echo json_encode([
            'status' => 'success',
            'description' => 'Registration request received. OTP sent to email.'

        ]);
     exit;
    }
}


/*******************************************server_verify**************************************/



if (isset($_GET['action']) && $_GET['action']=='server_verify') {



$req = file_get_contents( 'php://input' );
//echo $req; 
$json_data = json_decode($req, true);
$dt=date("Y-m-d H:i:s");

if (is_array($json_data) && isset($json_data['email']) && isset($json_data['verification_code'])) {
    $email = $json_data['email'];
    $verificationcode = $json_data['verification_code'];
}



 if($email!='' && $verificationcode!='')
   {


     $query = "SELECT * FROM otps where email='$email' and otp='$verificationcode'";
     $doctors = $db->get_results($query); 
     $row = $db->get_row($query); 
     $password=$row['password'];

          $num = $db->num_rows($query); 

         if($num==0)
           {
            // $response['num']=$num;
             header('HTTP/1.1 400');
             $response['status']='failure';
             $response['error_code']='101037';
             $response['description']='Verification code is incorrect';
           }
        else
          {
           $query_verify = "SELECT * FROM `otps` where email='$email' and otp='$verificationcode'";
           $doctors = $db->get_results($query_verify); 
           $row_verify = $db->get_row($query_verify); 
           $email=$row_verify['email'];
           $user_type=$row_verify['user_type'];

           // $db->query("insert into users set phone='$phone',email='$email',password='$password',user_type='staff',created_at='".$dt."'");
          //  $db->query("insert into users set phone='$phone',email='$email',password='$password',user_type='$user_type',created_at='".$dt."',app_version='v8'");
            
            $response['status']='success';
            $response['description']='OTP Verification Done';
          }

   }
   else
   {
        header('HTTP/1.1 400');
        $response['status']='failure';
        $response['error_code']='101037';
        $response['description']='phone number or verification_code is missing';

   }


}





/*******************************************************************************/
/************************************userlogin**********************************/

if (isset($_GET['action']) && $_GET['action']=='userlogin') {

/********************/
$req = file_get_contents( 'php://input' );
$json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['username']) && isset($json_data['password']) && isset($json_data['user_type'])) {
    $username = $json_data['username'];
    $password_1 = $json_data['password'];
    $user_type = $json_data['user_type'];
}


//$query = "SELECT * FROM `users` where phone='".$_POST['username']."' and password='".$_POST['password']."'";

//$query = "SELECT * FROM `users` WHERE email = '$username' OR phone = '$username'";   // 5-11-24


$query = "SELECT * FROM `users` WHERE phone = '$username' AND user_type='$user_type'";


/*$query = "SELECT * FROM `users` where phone='".$_POST['phone']."' and status='active' and deleted=0 and disable_login=0";*/



/*$query = "SELECT * FROM `users` 
          WHERE (email = '$username' OR phone = '$username') 
          AND password = '$password_1' 
          AND user_type = '$user_type'";*/




  $num = $db->num_rows($query); 
  if($num>0)
  { 
    $row = $db->get_row($query); 
    $password=$row['password']; 
    $phone = $row['phone']; 
    $userid = $row['id']; 
    $email = $row['email'];
    $usertype = $row['user_type']; 



    if ($password == $password_1/*password_verify($_POST['password'], $row['password'])*/)
      
    {
      


$header = json_encode(['typ' => 'JWT', 
                       'alg' => 'HS256']);


$payload = json_encode([
       "phone_verified" => true,
       "custom:user_id" =>$userid,
       "token_use" => "id",
       "auth_time" => 1667022242,
       "exp" => 1667025842,
       "iat" => 1667022242,
       "phone" =>$phone
]);


$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
//$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);
/***************************idtoken*********************/

$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'Efactor', true);
$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

/*********************access_token**********************/

$signature_access = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'Efactor_access', true);
$base64UrlSignature_access = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature_access));
$jwt_access = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature_access;

/**********************refresh_token*******************/


$signature_refresh = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'Efactor_refresh', true);
$base64UrlSignature_refresh = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature_refresh));
$jwt_refresh = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature_refresh;

/*******************************************************/


$db->query("update users set id_token='$jwt',access_token='$jwt_access',refresh_token='$jwt_refresh' where id='$userid'");


      $response['status']='success';
      $response['description']='Login successful';
      $response['idtoken']=$jwt;
      $response['accesstoken']=$jwt_access;
      $response['refreshtoken']=$jwt_refresh; 
       $response['username']=$phone;
      $response['email']=$email; 
      $response['user_type']=$usertype;

      

    }
   else
    {
       $response['status']='error';
        $response['message']='Authentication Failed';
    }


  }
  else
  {
    /* $response['status']='error';
        $response['message']='Authentication Failed';*/

       header('HTTP/1.1 400');
        $response['status']='failure';
        $response['error_code']='101111';
        $response['description']='Authentication Failed';

  }




/*******************************************************************/

}



/*****************************apiversions***********************************/


if (isset($_GET['action']) && $_GET['action']=='apiversions') {


$sub_resp=array("version" => "v8",
                 "build_info" => "1.0-b8aef46_2023-05-05T12:00",
                 "release_notes" => "The release notes can be accessed here: https://efactor.tech/docs/release-notes.html");
                  


$resp = array("supported_versions" => ["v8"],
         "additional_info" => [$sub_resp]
          );




echo json_encode($resp);
//echo $resp;





}



if (isset($_GET['action']) && $_GET['action'] == 'check_popups') {

    // Simulated message - you can replace this with actual logic to fetch the popup message
 /*  $popup_message = "Notice:
The “Efactor IoE” Alexa skill is live, but currently not discoverable due to an Amazon publishing issue.
We’re actively working with Amazon to resolve this and restore visibility soon.
Thank you for your patience!";*/ // Replace with actual source of message
    $popup_message = ""; // Replace with actual source of message

    if (strpos($popup_message, "Notice:
The “Efactor IoE” Alexa skill is live, but currently not discoverable due to an Amazon publishing issue.
We’re actively working with Amazon to resolve this and restore visibility soon.
Thank you for your patience!") !== false) {
        // If problematic message is found
        $resp = array(
            "status" => "error",
            "message" => $popup_message
        );
    } else {
        // Normal success case
     /*   $sub_resp = array(
            "version" => "v8",
            "build_info" => "1.0-b8aef46_2023-05-05T12:00",
            "release_notes" => "The release notes can be accessed here: https://efactor.tech/docs/release-notes.html"
        );*/

        $resp = array(
            "status" => "success"
                     
        );
    }

    echo json_encode($resp);
exit;
}






if (isset($_GET['action']) && $_GET['action']=='get_user_type') {


                  


/*$resp = array("user_type" => ["Brand","User","Client"],
                  );*/

$resp = array("user_type" => ["Brand","User"],
                  );



echo json_encode($resp);
exit;
//echo $resp;





}


/*******************************************************************************/


/*****************************auth0userlogin***********************************/

/*
if (isset($_GET['action']) && $_GET['action']=='auth0Userlogin') {


$req = file_get_contents( 'php://input' );
$json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['identifier']) && isset($json_data['password'])) {
    $phone = $json_data['identifier'];
    $password = $json_data['password'];
   
}


  



 $query = "SELECT * FROM users where phone='$phone' AND password='$password'";
    $num = $db->num_rows($query); 
     $row = $db->get_row($query); 
     $user_id=$row['id'];


if($num>0)
{

$resp = array("id" => (int)$user_id);


$json_response = json_encode($resp, JSON_PRETTY_PRINT);

exit($json_response);
}

else
{

header('HTTP/1.1 400');
        $response['status']='failure';
        $response['error_code']='101111';
        $response['description']='Account does not exist';



}



}




*/






if (isset($_GET['action']) && $_GET['action'] == 'auth0Userlogin') {

    $req = file_get_contents('php://input');
    $json_data = json_decode($req, true);

    if (is_array($json_data) && isset($json_data['identifier']) && isset($json_data['password'])) {
        $identifier = $json_data['identifier'];
        $password = $json_data['password'];
        
        // Check if identifier is email or phone
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            // It's an email
            $query = "SELECT * FROM users WHERE email='$identifier' AND password='$password'";
        } else {
            // Assume it's a phone number
            $query = "SELECT * FROM users WHERE phone='$identifier' AND password='$password'";
        }

        $num = $db->num_rows($query); 
        $row = $db->get_row($query); 

        if ($num > 0 && $row) {
            $user_id = $row['id'];
            $resp = array("id" => (int)$user_id);
            $json_response = json_encode($resp, JSON_PRETTY_PRINT);
            exit($json_response);
        } else {
            header('HTTP/1.1 400');
            $response['status'] = 'failure';
            $response['error_code'] = '101111';
            $response['description'] = 'Account does not exist';
            echo json_encode($response);
            exit;
        }
    }
}



/****************************E_NETWORK_ENABLE_DISABLE***************************/


if (isset($_GET['action']) && $_GET['action']=='e_module_enable_disable') {

$commandtype =$_GET['command_type'];
$deviceid =$_GET['address'];


$query = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$doctors = $db->get_results($query); 
//$num_dev = $db->num_rows($query);
$row = $db->get_row($query); 
$device_type=$row['device_type'];
$addgateway=$row['add_gateway'];
$moduletype=$row['module_type'];




if($moduletype == 'Gateway' || $moduletype == 'WIFI')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$deviceid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;

if($commandtype == 'E_MODULE_ENABLE')
{
echo $topic;
$mqtt_json=array("command_type"=> "E_MODULE_ENABLE", 
"address"=>$deviceid);
$mqtt_json_1=json_encode($mqtt_json);
echo json_encode($mqtt_json)."\n";
}

else
{
echo $topic;
$mqtt_json=array("command_type"=> "E_MODULE_DISABLE", 
"address"=>$deviceid);
$mqtt_json_1=json_encode($mqtt_json);
echo json_encode($mqtt_json)."\n";
}



$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }


}



}



/*******************************************E_NETWORK_ENABLE_DISABLE***************************/


if (isset($_GET['action']) && $_GET['action']=='e_device_enable_disable') {

//$commandtype =$_GET['command_type'];
$deviceid =$_GET['address'];
$nodeid =$_GET['device_id'];
$payload=$_GET['payload'];

$query = "SELECT * FROM efactorproduct where node_id='$nodeid'";  
$doctors = $db->get_results($query); 
//$num_dev = $db->num_rows($query);
$row = $db->get_row($query); 
$device_type=$row['device_type'];
$addgateway=$row['add_gateway'];
$moduletype=$row['module_type'];




if($payload == 'enable')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$deviceid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;


echo $topic;
$mqtt_json=array("command_type"=> "SET_DEVICE_STATUS", 
"address"=>$deviceid,
"device_id"=>$nodeid,
"payload"=>'2701');
$mqtt_json_1=json_encode($mqtt_json);
echo json_encode($mqtt_json)."\n";


$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }


}


else if($payload == 'disable')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$deviceid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;


echo $topic;
$mqtt_json=array("command_type"=> "SET_DEVICE_STATUS", 
"address"=>$deviceid,
"device_id"=>$nodeid,
"payload"=>'2700');
$mqtt_json_1=json_encode($mqtt_json);
echo json_encode($mqtt_json)."\n";


$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }


}

else
{

  echo 'payload not found';
}




}


/***************************************device_lock/unlock*************************************/

if (isset($_GET['action']) && $_GET['action']=='device_lock_unlock') {

$deviceid =$_GET['address'];
$nodeid =$_GET['device_id'];
$payload=$_GET['payload'];

$query = "SELECT * FROM efactorproduct where node_id='$nodeid'";  
$doctors = $db->get_results($query); 
//$num_dev = $db->num_rows($query);
$row = $db->get_row($query); 
$device_type=$row['device_type'];
$addgateway=$row['add_gateway'];
$moduletype=$row['module_type'];




if($payload == 'lock')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$deviceid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;


echo $topic;
$mqtt_json=array("command_type"=> "SET_DEVICE_STATUS", 
"address"=>$deviceid,
"device_id"=>$nodeid,
"payload"=>'A1A5010A');
$mqtt_json_1=json_encode($mqtt_json);
echo json_encode($mqtt_json)."\n";


$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }


}


else if($payload == 'unlock')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$deviceid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;


echo $topic;
$mqtt_json=array("command_type"=> "SET_DEVICE_STATUS", 
"address"=>$deviceid,
"device_id"=>$nodeid,
"payload"=>'A1A5000A');
$mqtt_json_1=json_encode($mqtt_json);
echo json_encode($mqtt_json)."\n";


$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }


}

else
{

  echo 'payload not found';
}




}
/*****************************************************************************************/
/*-------------------------------user_get_device_response--------------------------------*/


if (isset($_GET['action']) && $_GET['action']=='user_get_device_response') {


$header = apache_request_headers();  
foreach ($header as $key => $val) {

$req = file_get_contents( 'php://input' );
    $json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['device_id'])) {
    $deviceid = $json_data['device_id'];
   
}

if (is_array($json_data) && isset($json_data['node_id'])) {
    $nodeid = $json_data['node_id'];
    
}
if (is_array($json_data) && isset($json_data['payload'])) {
    $payload = $json_data['payload'];
    
}




sendMqttMessage($mqtt, $nodeid, $deviceid, $payload);


usleep(2000000);




$query_rsp = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
//$doctors = $db->get_results($query); 
//$num_dev = $db->num_rows($query);


$row_rsp = $db->get_row($query_rsp); 
$device_type_rsp=$row_rsp['device_type'];
$addgateway_rsp=$row_rsp['add_gateway'];
$moduletype_rsp=$row_rsp['module_type'];
$current_devicetype=$row_rsp['current_devicetype'];
$payload_rsp=$row_rsp['payload'];
$ipaddress=$row_rsp['ipaddress'];
$mac=$row_rsp['mac'];


$response = array(
    "device_name" => $current_devicetype,
    "device_type" => "efactor.device.$device_type_rsp",
    "status" => 200,
    "payload" => $payload_rsp,
    "ipaddress" => $ipaddress,
    "mac" => $mac,
    "device_id" => $deviceid,
    "internet" => 1,
    "gateway_id" => '',
    "command_type" => '',
    "node_id" => $deviceid
);


$json_response = json_encode($response, JSON_PRETTY_PRINT);

exit($json_response);
}


}


/*---------------------------------------------------------------------------------------*/

/***********************************************************************************************/





if (isset($_GET['action']) && $_GET['action'] == 'get_profile_pic') {
    $header = apache_request_headers();  
foreach ($header as $key => $val) {


   $query = "SELECT * FROM users where access_token='$val'";  
$row = $db->get_row($query); 
$profile_pic=$row['profile_pic'];


    if ($profile_pic) {
        $fileUrl = 'https://efactor.tech/files/brand_logo/app_profile_pic/' . $profile_pic;
        echo json_encode([
            "status" => "success",
            "profile_pic_url" => $fileUrl
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Profile picture not found."
        ]);
    }
    exit;
}


}
















/***********************************************************************************************/

if (isset($_GET['action']) && $_GET['action'] == 'user_update_nodes_params') {

$nodeid =$_GET['node_id'];
//echo $nodeid;
/*
function floatToHexLE($float) { 
    $packed = pack('f', $float); 
    $reversed = strrev($packed); 
    $hex = bin2hex($reversed); 
    return $hex;
}*/
function float_to_hex_big_endian($float) { 
    $binary = pack('G', $float);  
    $hex = strtoupper(bin2hex($binary));  
    return $hex;
}


if (is_array($json_data) && isset($json_data['device_id'])) {
    $deviceid = $json_data['device_id'];
   
}


if (is_array($json_data) && isset($json_data['latitude'])) {
    $latitude = $json_data['latitude'];
    
}
if (is_array($json_data) && isset($json_data['longitude'])) {
    $longitude = $json_data['longitude'];
    
}

//print_r($json_data);

if (is_array($json_data) && isset($json_data['page_name'])) {
    $page_names = $json_data['page_name'];
    
}





  if (!empty($deviceid)) {


    $query = "SELECT * FROM efactorproduct WHERE node_id='$deviceid'";
    $result = $db->query($query);
    $row = $db->get_row($query); 
        
        $device = $row['device_type'];
        $model_name = $row['model_number'];
        $addgateway = $row['add_gateway'];


$query_model = "SELECT * FROM product_api WHERE model_name='EF-003FMN03' AND page_names='Configure.Config'";
    $result_model = $db->query($query_model);
    $row_model = $db->get_row($query_model); 
        
      /*  $device = $row['device_type'];
        $model_name = $row['model_number'];
        $addgateway = $row['add_gateway'];*/



     $cmd_multipleui = $row_model['cmd_multipleui'];
     $cmd_triggerui = $row_model['cmd_triggerui'];










       
$header = apache_request_headers();  
foreach ($header as $key => $val) {
   
$req = file_get_contents( 'php://input' );
    $json_data = json_decode($req, true);
// $db->query("UPDATE efactorproduct where node_id='$deviceid'");
    
}



    
//print_r($json_data);

$updateFields = [];
foreach ($json_data as $key => $value) {




    if($key === 'payload')
    {
        $payload=$value;

    //    echo $payload;
    }




if($device == 'input-output')
{

/*

if ($key == 'baudrate') {

    if ($value == '4800') $value = '01';
    elseif ($value == '9600') $value = '02';
    elseif ($value == '57600') $value = '04';
    elseif ($value == '38400') $value = '03';
    elseif ($value == '115200') $value = '05';
     elseif ($value == '230400') $value = '06';

$value=(string)$value;


$db->query("update efactorproduct set $key='$value' where node_id='$deviceid'");

}*/


if ($key == 'baudrate') {
      

$value=(string)$json_data['baudrate'];

if ($value == '0') $value = '00';
    elseif ($value == '1') $value = '01';
    elseif ($value == '2') $value = '02';
    elseif ($value == '3') $value = '03';
    elseif ($value == '4') $value = '04';
     elseif ($value == '5') $value = '05';


    
    
    $db->query("UPDATE efactorproduct SET $key='$value' WHERE node_id='$deviceid'");




}

if ($key == 'uartmsgid') {
      

$value=(string)$json_data['uartmsgid'];

if ($value == '0') $value = '00';
    elseif ($value == '1') $value = '01';


 
    
    $db->query("UPDATE efactorproduct SET $key='$value' WHERE node_id='$deviceid'");



}


if ($key == 'errorcode') {
      

$value=(string)$json_data['errorcode'];

if ($value == '0') $value = '00';
    elseif ($value == '1') $value = '01';
   

        
    $db->query("UPDATE efactorproduct SET $key='$value' WHERE node_id='$deviceid'");



}










else if($key == 'power')
{



//echo $json_data['payload'];;
if($json_data['payload'] == 'A1C2000A')
{

$log_message = "[" . date("Y-m-d H:i:s") . "] Device with node ID $deviceid is now " . $json_data['payload'] . ".\n";

//file_put_contents('/var/www/html/device_status.log', $log_message, FILE_APPEND);

$db->query("update efactorproduct set power='00' where node_id='$deviceid'");
//exit;

}
else
{

$log_message = "[" . date("Y-m-d H:i:s") . "] Device with node ID $deviceid is now " . $json_data['payload'] . ".\n";

//file_put_contents('/var/www/html/device_status.log', $log_message, FILE_APPEND);


//print_r($json_data);
$db->query("update efactorproduct set power='01' where node_id='$deviceid'");
//exit;

}


}
else if($key == 'name'){

//if (!empty($value)) {

$db->query("update efactorproduct set name='$value' where node_id='$deviceid'");
//}



}
else{

$db->query("update efactorproduct set $key='$value' where node_id='$deviceid'");

}
}

else
{




if(($cmd_multipleui == 'true') && ($cmd_triggerui == 'true'))

{

$value=(string)$json_data['payload'];







}








if ($key == 'baudrate') {
      

$value=(string)$json_data['baudrate'];

if ($value == '0') $value = '00';
    elseif ($value == '1') $value = '01';
    elseif ($value == '2') $value = '02';
    elseif ($value == '3') $value = '03';
    elseif ($value == '4') $value = '04';
     elseif ($value == '5') $value = '05';


    
    
    $db->query("UPDATE `$model_name` SET `$key` = '$value' WHERE node_id = '$deviceid'");




}

if ($key == 'uartmsgid') {
      

$value=(string)$json_data['uartmsgid'];

if ($value == '0') $value = '00';
    elseif ($value == '1') $value = '01';


 
    
   $db->query("UPDATE `$model_name` SET `$key` = '$value' WHERE node_id = '$deviceid'");



}


if ($key == 'errorcode') {
      

$value=(string)$json_data['errorcode'];

if ($value == '0') $value = '00';
    elseif ($value == '1') $value = '01';
   

        
    $db->query("UPDATE `$model_name` SET `$key` = '$value' WHERE node_id = '$deviceid'");



}












if ($key === 'power') {
            $value = $value ? '01' : '00';

if($value == '01')
{
$log_message = "[" . date("Y-m-d H:i:s") . "] Device with node ID $deviceid is now on.\n";

}
else
{
$log_message = "[" . date("Y-m-d H:i:s") . "] Device with node ID $deviceid is now off.\n";

}


//echo $log_message;

file_put_contents('/var/www/html/device_status.log', $log_message, FILE_APPEND);


        }

if ($key === 'Led') {
            $value = $value ? '01' : '00';
        }

if ($key === 'Direction') {
            $value = '08';
        }


/*
if ($key === 'baudrate') {
    if ($value == '4800') $value = '01';
    elseif ($value == '9600') $value = '02';
    elseif ($value == '57600') $value = '04';
    elseif ($value == '38400') $value = '03';
    elseif ($value == '115200') $value = '05';
     elseif ($value == '230400') $value = '06';

}
*/

/*
if($key === 'Recipee')
{
if ($value == 'RICE') $value = "00";
    elseif ($value == 'BROWNRICE') $value = "01";
    elseif ($value == 'FRY') $value = "02";
    elseif ($value == 'MILK') $value = "03";
    elseif ($value == 'SOUP') $value = "04";
     elseif ($value == 'BIRIYANI') $value = "05";
     elseif ($value == 'IDLY') $value = "06";
    elseif ($value == 'DHAL') $value = "07";
    elseif ($value == 'HARDVEG') $value = "08";
    elseif ($value == 'WATER') $value = "09";
     elseif ($value == 'POPCORN') $value = "0B";
    elseif ($value == 'CAKE') $value = "0C";
    elseif ($value == 'MAGGI') $value = "0D";
    elseif ($value == 'NOODLES') $value = "0E";
     elseif ($value == 'DIY') $value = "0F";

}
*/


if ($key === 'Recipee') {
    $recipeMapping = [
        'RICE' => '00',
        'BROWNRICE' => '01',
        'FRY' => '02',
        'MILK' => '03',
        'SOUP' => '04',
        'BIRIYANI' => '05',
        'IDLY' => '06',
        'DHAL' => '07',
        'HARDVEG' => '08',
        'WATER' => '09',
        'POPCORN' => '0B',
        'CAKE' => '0C',
        'MAGGI' => '0D',
        'NOODLES' => '0E',
        'DIY' => '0F',
    ];

    if (array_key_exists($value, $recipeMapping)) {
        $value = $recipeMapping[$value];
      //  echo "Mapped Value: $value\n"; // Debugging: Check the mapped value
    } else {
        $value = '00'; // Default value
      //  echo "Default Value: $value\n"; // Debugging: Check the default value
    }
}



if ($key === 'cooker-recipe') {
  /* $recipeMapping = [
        'RICE' => '00',
        'BROWNRICE' => '01',
        'FRY' => '02',
        'MILK' => '03',
        'SOUP' => '04',
        'BIRIYANI' => '05',
        'IDLY' => '06',
        'DHAL' => '07',
        'HARDVEG' => '08',
        'WATER' => '09',
        'POPCORN' => '0B',
        'CAKE' => '0C',
        'MAGGI' => '0D',
        'NOODLES' => '0E',
        'DIY' => '0F',
    ];

    if (array_key_exists($value, $recipeMapping)) {
        $value = $recipeMapping[$value];
      //  echo "Mapped Value: $value\n"; // Debugging: Check the mapped value
    } else {
        $value = '00'; // Default value
      //  echo "Default Value: $value\n"; // Debugging: Check the default value
    }*/



$value=(string)$json_data['payload'];


}



//echo "Value: $value\n";

 
if($key === 'device_id'){
    $value = $value;
}elseif($key === 'mac'){
    $value = $value;
}elseif($key === 'module_type'){
    $value = $value;
}elseif($key === 'device_name'){
    $value = $value;
}elseif($key === 'name'){

    $value = $value;
}
elseif($key === 'node_id'){
    $value = $value;
}elseif($key === 'page_name'){
    $value = $value;
}elseif($key === 'payload'){
    $value = $value;
}elseif($key === 'latitude'){
    $value = $value;
}elseif($key === 'longitude'){
    $value = $value;
}elseif($key === 'command_type'){
    $value = $value;
}elseif($key === 'address'){
    $value = $value; 
}elseif($key === 'Recipee'){
    $value = $value; 
}else{ 
    if (strpos($value, '.') !== false) { 
        $value = float_to_hex_big_endian($value);
    } else { 

             if (!ctype_xdigit($value)) {
         $value = dechex(intval($value));
     }
       
    } 
} 
 
  //  $log_message .= "Updated $key to $value for node_id $deviceid.\n";
   if($key !== 'payload')
{



 
if($value == '1')$value = "01";
if($value == '0')$value = "00";

if ($key !== 'device_id' && $key !== 'node_id') {
    $db->query("UPDATE `$model_name` SET `$key` = '$value' WHERE node_id = '$deviceid'");
}







}

//file_put_contents('/var/www/html/device_status.log', $log_message, FILE_APPEND);


}

}
}


/****************************mqtt***************************************/

if($device == 'input-output')
{

$query_efactgate = "SELECT * FROM efactorproduct WHERE node_id='$deviceid'";
    $result_efactgate = $db->query($query_efactgate);
    $row_efactgate = $db->get_row($query_efactgate); 
   // $addgateway = $row_efactgate['add_gateway'];
    $payload_1 = $row_efactgate['payload'];


}

else{

//echo $model_name;
//echo $deviceid;


$query_addgate = "SELECT * FROM `$model_name` WHERE node_id='$deviceid'";
//$query_addgate = "SELECT * FROM `EFCKER V1.0` WHERE node_id='FFFFFFFFFFFFFFFF7EC2000000052B00'";
    $result_addgate = $db->query($query_addgate);
    $row_addgate = $db->get_row($query_addgate); 
  //  $addgateway = $row_addgate['add_gateway'];
    $payload_1 = $row_addgate['payload'];
}

//echo $payload;



if($addgateway != 'NULL')

{



$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';
$topic=$efactor."/".$things."/".$addgateway."/".$command;
//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;

if($payload == 'A1E70A')
{

//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);


  $response['payload']=$payload_1;
      


   $mqtt->disconnect();
exit(json_encode($response));

       }

}






else{





if($payload == '2500')
{
$mqtt_json=array("command_type"=> "E_NETWORK_INFO", 
"address"=>$nodeid,
"device_id"=>$nodeid,
"payload"=>$payload);
}

else{

$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);

}


$mqtt_json_1=json_encode($mqtt_json);

$efactor='Efactor';
$things='things';
$command='command';




     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
}


$response['payload']="";

$mqtt->disconnect();

exit(json_encode($response));


}
}

else
{



$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$deviceid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;

//echo $payload_1;


if($payload == 'A1E70A')
{


$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";

//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);

//echo $model_name;
//echo $deviceid;

/*
$query_node_11="SELECT * FROM `EFCKER V1.0` where node_id='$deviceid'";
$single_node_dynamic_11 = $db->get_results($query_node_11);
$row_node_11 = $db->get_row($query_node_11);
$payload_1=$row_node_11['payload'];

*/


/*$query_result = "SELECT * FROM `model_name` where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload_1=$row_result['payload'];
//$addgateway=$row['add_gateway'];*/

//echo $payload_1;

  $response['payload']=$payload_1;
      


   $mqtt->disconnect();

exit(json_encode($response));


       }

}


else{



$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
}

$response['payload']="";


$mqtt->disconnect();

exit(json_encode($response));


}

}
/**********************************************************************/

/********************************mqtt_end*****************************************/
  
 
}

/**********************************************************************************/


/*--------------------------------------------------------------------------------------*/
if (isset($_GET['action']) && $_GET['action'] == 'guarantee_card_display_123') {   
    ob_start();   
    
    $model_name = $_GET['model_name'];
    $user_id = $_GET['user_id'];
     
    $query = "SELECT * FROM vendorproducts where modelname='$model_name' AND user_id = '$user_id'";  
$doctors = $db->get_results($query);  
$row = $db->get_row($query); 
$filename=$row['guarantee_card_file'];
//echo $filename;
 
if (empty($filename)|| $filename == "NULL" || $filename == "Null" || $filename == "null") {
    echo "No File Uploaded.";
} else { 
    if (pathinfo($filename, PATHINFO_EXTENSION) === 'txt') {
    //$file_path = 'C:/Program Files/Ampps\www/factor_17-05/files/brandLogo/'.$filename; 
    $file_path = '/var/www/html/files/specifications/'.$filename;   
    if (file_exists($file_path)) {  
        $file_content = file_get_contents($file_path);    
        $file_content = htmlspecialchars($file_content, ENT_QUOTES, 'UTF-8'); 
        $html_content = "<html>
        <head>
            <title>Guarantee Card</title>
            <style>
                /* General reset to ensure consistent styling */
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
        
                /* Body styling */
                body {
                    font-family: Arial, sans-serif;
                    background-color: #ffffff; /* White background */
                    color: #333; /* Dark text color for contrast */
                    display: flex;
                    justify-content: center;
                    align-items: flex-start; /* Align to the top so that the page is scrollable */
                    flex-direction: column; /* Allow the content to be stacked vertically */
                    min-height: 100vh; /* Make sure the body takes full viewport height */
                    padding: 20px;
                    overflow-y: auto; /* Enable vertical scrolling */
                    background: #ffffff;
                }
        
                /* Ensure the preformatted text block adapts to the screen */
                pre {
                    font-family: 'Sans serif';
                    background-color: #ffffff; /* Light gray background for preformatted text */
                    color: black; /* Dark text color */
                    padding: 20px;
                    border-radius: 8px;
                    width: 100%;
                    max-width: 100%; /* Prevent overflow */
                    height: auto;
                    overflow-x: auto; /* Enable horizontal scrolling if content overflows */
                    overflow-y: auto; /* Enable vertical scrolling for overflow content */
                    line-height: 1.5;
                    white-space: pre-wrap; /* Wrap long lines of code */
                    word-wrap: break-word; /* Break long words */
                    margin-bottom: 20px; /* Add some space below the content */
                    word-break: break-all; /* Ensure words break at the boundary on small screens */
                }
        
                /* Scrollbar styling */
                pre::-webkit-scrollbar {
                    width: 8px;
                }
        
                pre::-webkit-scrollbar-thumb {
                    background-color: rgba(0, 0, 0, 0.2);
                    border-radius: 4px;
                }
        
                pre::-webkit-scrollbar-track {
                    background-color: transparent;
                }
        
                /* Responsive design */
                @media only screen and (max-width: 768px) {
                    body {
                        padding: 10px;
                    }
        
                    pre {
                        padding: 15px;
                        font-size: 14px; /* Smaller font size for mobile devices */
                        word-wrap: break-word; /* Ensure words break on small screens */
                        white-space: normal; /* Allow the content to break lines normally */
                    }
                }
        
                @media only screen and (max-width: 480px) {
                    pre {
                        padding: 10px;
                        font-size: 20px; /* Further reduce font size for very small screens */
                        word-wrap: break-word; /* Break words in small screens */
                        white-space: normal; /* Allow the content to wrap properly */
                    }
                }
        
            </style>
        </head>
        <body>
            <pre>$file_content</pre>
        </body>
        </html>";
        echo $html_content;
                  //  echo $file_content; 
        include 'guarantee_card.php';   
    $content = ob_get_clean();  
    header('Content-Type: text/html');    
    echo $content;

    } else {   
        echo "File not found.";
    } 
}else{
    echo " Sorry, the file type $filename is not supported. Please upload a .txt file. ";
}
}
      
}




/******************------------------------------****************************/





if (isset($_GET['action']) && $_GET['action'] == 'guarantee_card_display') {   
    ob_start();   
    $model_name = $_GET['model_name'];
    $user_id = $_GET['user_id'];
     
    $query = "SELECT * FROM vendorproducts WHERE modelname='$model_name' AND user_id = '$user_id'";  
    $row = $db->get_row($query); 
    $filename = $row['guarantee_card_file'];
    $category = $row['category'];
 
    if (empty($filename) || strtolower($filename) == "null") {
        echo "No File Uploaded.";
    } else { 
        if (pathinfo($filename, PATHINFO_EXTENSION) === 'txt') {
            $file_path = '/var/www/html/files/specifications/' . $filename;   
            
            if (file_exists($file_path)) {  
                $file_content = file_get_contents($file_path);    
                $file_content = htmlspecialchars($file_content, ENT_QUOTES, 'UTF-8'); 
                $html_content = "<html>
                <head>
                    <title>Guarantee Card</title>
                    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                    <style>
                        /* General reset */
                        * { margin: 0; padding: 0; box-sizing: border-box; }

                        /* Body styling with scroll */
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #ffffff;
                            color: #333;
                            display: flex;
                            flex-direction: column;
                            min-height: 100vh;
                            overflow-y: auto;
                            padding: 0;
                        }
 #confirmButton {
            margin : 12px; 
            background-color: darkorange;
            color: white;
            border: none;
            width: 12%;   
            height: 30px; 
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }  
             #confirmButton:disabled {
            background-color: orange;
            color: white;
            cursor: not-allowed;
        } 
             form {
            width: 100%;   
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
        } 
                        /* Scrollable preformatted text */
                        .content {
                            flex: 1;
                            padding: 20px;
                            background-color: #ffffff;
                            overflow-y: auto;
                            white-space: pre-wrap;
                            word-break: break-word;
                            height: calc(100vh - 70px); /* Full height minus space for buttons */
                        }

                        /* Button container fixed at the bottom */
                        .button-container {
                            display: flex;
                            justify-content: center;
                            gap: 10px;
                            padding: 10px;
                            position: sticky;
                            bottom: 0;
                            background-color: #ffffff;
                            border-top: 1px solid #ddd;
                        }

                        /* Button styling */
                        .button {
                            background-color: #4CAF50;
                            color: white;
                            border: none;
                            padding: 15px 30px;
                            text-align: center;
                            font-size: 16px;
                            cursor: pointer;
                            border-radius: 5px;
                            flex: 1;
                            max-width: 150px;
                        }

                        /* Responsive adjustments */
                        @media only screen and (max-width: 600px) {
                            .content { font-size: 14px; padding: 10px; }
                            .button { padding: 12px; font-size: 14px; }
                              #confirmButton {
                font-size: 13px;
                height: 25px;
                width:60px;
            } 
            
                        }
                    </style>
                </head>
                <body>
                    <div class='content'>$file_content</div>
                   <form id='guaranteeForm' action='https://efactor.tech/v8/api.php?action=guarantee_card_success&model_name=$model_name&user_id=$user_id&category=$category' method='POST'>
    <label>
        <input type='checkbox' id='checkbox' onclick='toggleButton()'> Agree to the Terms and Conditions
    </label>    
    <div class='input-container'> 
        <button id='confirmButton' disabled type='submit'>Confirm</button>
    </div>
</form>
<script>
    function toggleButton() {
        const checkbox = document.getElementById('checkbox');
        const confirmButton = document.getElementById('confirmButton');
        confirmButton.disabled = !checkbox.checked;
    }
</script>
                </body>
                </html>";

                echo $html_content;
              //  include 'guarantee_card.php';   
                $content = ob_get_clean();  
                header('Content-Type: text/html');    
                echo $content;

            } else {   
                echo "File not found.";
            } 
        } else {
            echo "Sorry, the file type $filename is not supported. Please upload a .txt file.";
        }
    }
exit;
}



/***********************************notification******************************************/


if (isset($_GET['action']) && $_GET['action'] == 'manage_device_notification') {



$req = file_get_contents( 'php://input' );
//echo $req; 
$json_data = json_decode($req, true);


if (is_array($json_data) && isset($json_data['device_id'])) {
    $node_id = $json_data['device_id'];
    
}
if (is_array($json_data) && isset($json_data['Notification'])) {
    $notification = $json_data['Notification'];
    
}else{
$notification='';
}



$query = "SELECT * FROM efactorproduct where node_id='$node_id'";
     $num = $db->num_rows($query); 
     $row = $db->get_row($query);
     $model_name=$row['model_name'];
   //  $row = $db->get_row($query);



   $db->query("update efactorproduct set nodeid_notification='$notification' where node_id='$nodeid'");
   $db->query("update `$model_name` set nodeid_notification='$notification' where node_id='$nodeid'");
  
  
}


/*-----------------*/

if (isset($_GET['action']) && $_GET['action'] == 'set_device_notification') {


$manage_notifications = $_GET['manage_notifications'];


$header = apache_request_headers();  
     foreach ($header as $key => $val) {

     
   $db->query("update efactorproduct set nodeid_notification='false' where access_token='$val'");
   $db->query("update `$model_name` set nodeid_notification='false' where access_token='$val'");
  
  exit;
}



}






/*********************-----------------------------------*************************/

/*---------------------------------------------------------------------------------------*/
// 
if (isset($_GET['action']) && $_GET['action'] == 'guarantee_card_success_web') {
    $model_name = $_GET['model_name'];
    $user_id = $_GET['user_id'];
    $category = $_GET['category'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
        $email = $_POST['email'];
        file_put_contents('emails.txt', $email . PHP_EOL, FILE_APPEND);
        //echo "Email submitted successfully: " . htmlspecialchars($email);
        echo "Submited Successfully";
        header('Location: https://efactor.tech/index.php/items/callDisplayFunction/' . urlencode($model_name));
        exit;
    } else {
        echo " Failed to Submit";
        header('Location: https://efactor.tech/index.php/items/callDisplayFunction/' . urlencode($model_name));
        exit;
    }
}




if (isset($_GET['action']) && $_GET['action'] == 'guarantee_card_success') {

    $model_name = htmlspecialchars($_GET['model_name'] ?? 'Unknown Model');

    // Set Content-Type to text/plain for plain text output
    header('Content-Type: text/plain');

    // Output just the plain message
    echo strtoupper("You have successfully submitted your guarantee card for the model: $model_name");
    exit;
}





/*---------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------*/

/*---------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------*/
// 
if (isset($_GET['action']) && $_GET['action'] == 'call_services') {
    
    $req = file_get_contents('php://input');
    $json_data = json_decode($req, true);  
   // $model_name = $_GET['model_name'];
   // $user_id = $_GET['user_id'];
   // $node_id = $_GET['node_id'];

      $node_id = isset($json_data['device_id']) ? $json_data['device_id'] : null;
    $data_value = isset($json_data['data']) ? $json_data['data'] : null;



    $query = "SELECT * FROM efactorproduct where node_id='$node_id'";
     $num = $db->num_rows($query); 
     $row = $db->get_row($query);
     $model_name=$row['model_name'];
     $user_id=$row['user_id'];
    

    $last_5_node_id = substr($node_id, -5); 
    $query = "SELECT created_at, ticket_number 
              FROM call_service 
              WHERE model_name = '$model_name' AND node_id = '$node_id' 
              ORDER BY created_at DESC LIMIT 1";  
    $result = $db->get_row($query);

    if ($result) { 
        $created_at = $result['created_at'];
        $ticket_number = $result['ticket_number'];
 
        $current_time = new DateTime();
        $created_time = new DateTime($created_at);
 
        $interval = $current_time->diff($created_time);
        $hours_diff = $interval->h + ($interval->days * 24); 

       
        if ($hours_diff >= 24) { 
          
            $random_serial = str_pad(rand(0, 99999), 5, "0", STR_PAD_LEFT);  

            $ticket_number = $model_name . $last_5_node_id . $random_serial;
 
         /*   $db->query("INSERT INTO call_service (model_name, node_id, message, to_userid, from_userid, ticket_number, flag) 
                        VALUES ('$model_name', '$node_id', '$data_value', '1', '$user_id', '$ticket_number', '1')");*/


            $db->query("INSERT INTO message (model_name, node_id, message, to_user_id, from_user_id, ticket_number, flag) 
                    VALUES ('$model_name', '$node_id', '$data_value', '$user_id', '1', '$ticket_number', '1')");





          //  $response['ticket_number'] = $ticket_number;
          //    $response['message'] = $ticket_number;

            $ticket_1='Your ticket';
   $ticket_status='has been successfully registered. Thank you for reaching out; we will resolve this issue as soon as possible. For further information, feel free to email us at admin@efactor.io.';
  $space=' ';
 $response['message'] = $ticket_1.$space.$ticket_number.$space.$ticket_status;



        } else { 
            $response['message'] = 'Please try again later, 24 hours have not yet passed since the last request.';
        }


  /*      $ticket_1='Your ticket';
   $ticket_status='has been successfully registered. Thank you for reaching out; we will resolve this issue as soon as possible. For further information, feel free to email us at admin@efactor.io.';
  $space=' ';
 $response['message'] = $ticket_1.$space.$ticket_number.$space.$ticket_status;


*/

    //    $response['message'] = $ticket_number;



    } else { 
        // Generate a random 5-digit number
        $random_serial = str_pad(rand(0, 99999), 5, "0", STR_PAD_LEFT);  

        $ticket_number = $model_name . $last_5_node_id . $random_serial;
 
      /*  $db->query("INSERT INTO call_service (model_name, node_id, message, to_user_id, from_user_id, ticket_number, flag) 
                    VALUES ('$model_name', '$node_id', '$data_value', '$user_id', '1', '$ticket_number', '1')");*/

        $db->query("INSERT INTO message (model_name, node_id, message, to_user_id, from_user_id, ticket_number, flag) 
                    VALUES ('$model_name', '$node_id', '$data_value', '$user_id', '1', '$ticket_number', '1')");


        
$ticket_1='Your ticket';
$ticket_status='has been successfully registered. Thank you for reaching out; we will resolve this issue as soon as possible. For further information, feel free to email us at admin@efactor.io.';
$space=' ';
$response['message'] = $ticket_1.$space.$ticket_number.$space.$ticket_status;



    //    $response['message'] = $ticket_number;
    }
 
    echo json_encode($response);
exit;
}



/*********************************************************************************************/

/*
if (isset($_GET['action']) && $_GET['action'] == 'call_services_web') {
    
    $req = file_get_contents('php://input');
    $json_data = json_decode($req, true);  
   // $model_name = $_GET['model_name'];
   // $user_id = $_GET['user_id'];
    $node_id = $_GET['node_id'];


     $node_id = isset($_GET['node_id']) ? urldecode($_GET['node_id']) : null;



    //  $node_id = isset($json_data['device_id']) ? $json_data['device_id'] : null;
    $data_value = isset($json_data['message']) ? $json_data['message'] : null;
    $model_name = isset($json_data['model_name']) ? $json_data['model_name'] : null;





    $query = "SELECT * FROM efactorproduct where node_id='$node_id'";
     $num = $db->num_rows($query); 
     $row = $db->get_row($query);
     $model_name=$row['model_number'];
     $user_id=$row['user_id'];





     $random_serial = str_pad(rand(0, 99999), 5, "0", STR_PAD_LEFT);  

        $ticket_number = $model_name . $last_5_node_id . $random_serial;
 
       $db->query("INSERT INTO call_service (model_name, node_id, message, to_userid, from_userid, ticket_number, flag) 
                    VALUES ('$model_name', '$node_id', '$data_value', '1', '$user_id', '$ticket_number', '1')");


        


      $ticket_1='Your ticket';
   $ticket_status='has been successfully registered. Thank you for reaching out; we will resolve this issue as soon as possible. For further information, feel free to email us at admin@efactor.io.';
  $space=' ';
 $response['message'] = $ticket_1.$space.$ticket_number.$space.$ticket_status;


 
    echo json_encode($response);
exit;
}
*/


/*


if (isset($_GET['action']) && $_GET['action'] == 'call_services_web') {
    
    $req = file_get_contents('php://input');
    $json_data = json_decode($req, true);  
    $node_id = $_GET['node_id'];

    $node_id = isset($_GET['node_id']) ? urldecode($_GET['node_id']) : null;
    $data_value = isset($json_data['message']) ? $json_data['message'] : null;
    $model_name = isset($json_data['model_name']) ? $json_data['model_name'] : null;

    $query = "SELECT * FROM efactorproduct where node_id='$node_id'";
    $num = $db->num_rows($query); 
    $row = $db->get_row($query);
    $model_name = $row['model_number'];
    $user_id = $row['user_id'];

    // Check if ticket already exists for this device/user
    $existing_ticket_query = "SELECT * FROM call_service 
                             WHERE model_name = '$model_name' 
                             AND node_id = '$node_id' 
                             AND from_userid = '$user_id'
                             AND flag = '1'"; // assuming flag=1 means open ticket
    $existing_ticket_count = $db->num_rows($existing_ticket_query);



    // Generate new ticket if none exists
    $random_serial = str_pad(rand(0, 99999), 5, "0", STR_PAD_LEFT);  
    $last_5_node_id = substr($node_id, -5); // assuming you want last 5 chars
    $ticket_number = $model_name . $last_5_node_id . $random_serial;

    $db->query("INSERT INTO call_service (model_name, node_id, message, to_userid, from_userid, ticket_number, flag) 
                VALUES ('$model_name', '$node_id', '$data_value', '1', '$user_id', '$ticket_number', '1')");

    $ticket_1 = 'Your ticket';
    $ticket_status = 'has been successfully registered. Thank you for reaching out; we will resolve this issue as soon as possible. For further information, feel free to email us at admin@efactor.io.';
    $space = ' ';
    $response['message'] = $ticket_1.$space.$ticket_number.$space.$ticket_status;

    echo json_encode($response);
    exit;
}


*/

/************************************************************************/


if (isset($_GET['action']) && $_GET['action'] == 'call_services_web') {
    $req = file_get_contents('php://input');
    $json_data = json_decode($req, true);  
    $node_id = isset($_GET['node_id']) ? urldecode($_GET['node_id']) : null;
    
    $data_value = isset($json_data['message']) ? $json_data['message'] : null;
    $model_name = isset($json_data['model_name']) ? $json_data['model_name'] : null;
    $user_id = isset($json_data['user_id']) ? $json_data['user_id'] : null;
    $is_admin = isset($json_data['is_admin']) ? $json_data['is_admin'] : false;

    // Get device info
    $query = "SELECT * FROM efactorproduct where node_id='$node_id'";
    $num = $db->num_rows($query); 
    $row = $db->get_row($query);
    $model_name = $row['model_number'];
    $device_user_id = $row['user_id'];
    $brand_name = $row['vendor_name'];

    // Determine sender and receiver
    $from_userid = $is_admin ? 1 : $device_user_id;
    $to_userid = $is_admin ? $device_user_id : 1;

    // Generate ticket number if this is the first message
    $random_serial = str_pad(rand(0, 99999), 5, "0", STR_PAD_LEFT);  
    $last_5_node_id = substr($node_id, -5);
    $ticket_number = $model_name . $last_5_node_id . $random_serial;

    // Save message to database
    /*$db->query("INSERT INTO call_service (model_name, node_id, message, to_userid, from_userid, ticket_number, flag, created_at, Brand_name) 
                VALUES ('$model_name', '$node_id', '$data_value', '$to_userid', '$from_userid', '$ticket_number', '1', NOW()), '$brand_name'");*/


$currentDateTime = date('Y-m-d H:i:s');

//$db->query("SET time_zone = '+05:30';");


$db->query("INSERT INTO call_service 
            (model_name, node_id, message, to_userid, from_userid, ticket_number, flag, created_at, Brand_name) 
            VALUES ('$model_name', '$node_id', '$data_value', '$to_userid', '$from_userid', '$ticket_number', '1', '$currentDateTime', '$brand_name')");

    // For admin replies, we don't need to show the ticket number again
    if ($is_admin) {
        $response['message'] = "Admin message sent successfully.";
    } else {
        // For user messages, show ticket info if it's new
        $ticket_1 = 'Your ticket';
        $ticket_status = 'has been successfully registered. Thank you for reaching out; we will resolve this issue as soon as possible. For further information, feel free to email us at admin@efactor.io.';
        $space = ' ';
        $response['message'] = $ticket_1.$space.$ticket_number.$space.$ticket_status;
    }

   // echo json_encode($response);
    exit;
}





if (isset($_GET['action']) && $_GET['action'] == 'admin_services_web') {
    $req = file_get_contents('php://input');
    $json_data = json_decode($req, true);  
    $node_id = isset($_GET['node_id']) ? urldecode($_GET['node_id']) : null;
    
    $data_value = isset($json_data['message']) ? $json_data['message'] : null;
  //  $model_name = isset($json_data['model_name']) ? $json_data['model_name'] : null;
  //  $user_id = isset($json_data['user_id']) ? $json_data['user_id'] : null;
    $is_admin = isset($json_data['is_admin']) ? $json_data['is_admin'] : false;

    // Get device info
    $query = "SELECT * FROM efactorproduct where node_id='$node_id'";
    $num = $db->num_rows($query); 
    $row = $db->get_row($query);
    $model_name = $row['model_number'];
    $device_user_id = $row['user_id'];
    $brand_name = $row['Brand_name'];


    // Determine sender and receiver
    $from_userid = $is_admin ? 1 : $device_user_id;
    $to_userid = $is_admin ? $device_user_id : 1;

    // Generate ticket number if this is the first message
    $random_serial = str_pad(rand(0, 99999), 5, "0", STR_PAD_LEFT);  
    $last_5_node_id = substr($node_id, -5);
    $ticket_number = $model_name . $last_5_node_id . $random_serial;

    // Save message to database
  /*  $db->query("INSERT INTO call_service (model_name, node_id, message, to_userid, from_userid, ticket_number, flag, created_at, Brand_name) 
                VALUES ('$model_name', '$node_id', '$data_value', '$device_user_id', '1', '$ticket_number', '1', NOW()), '$brand_name'");  */


$currentDateTime = date('Y-m-d H:i:s');

//$db->query("SET time_zone = '+05:30';");


$db->query("INSERT INTO call_service (model_name, node_id, message, to_userid, from_userid, ticket_number, flag, created_at, Brand_name) 
                VALUES ('$model_name', '$node_id', '$data_value', '$device_user_id', '1', '$ticket_number', '1', '$currentDateTime', '$brand_name')");


    // For admin replies, we don't need to show the ticket number again
    if ($is_admin) {
        $response['message'] = "Admin message sent successfully.";
    } else {
        // For user messages, show ticket info if it's new
        $ticket_1 = 'Your ticket';
        $ticket_status = 'has been successfully registered. Thank you for reaching out; we will resolve this issue as soon as possible. For further information, feel free to email us at admin@efactor.io.';
        $space = ' ';
        $response['message'] = $ticket_1.$space.$ticket_number.$space.$ticket_status;
    }

   // echo json_encode($response);
    exit;
}





/*

if (isset($_GET['action']) && $_GET['action'] == 'brand_api') {

    header('Content-Type: application/json');

    $brand_query = "SELECT brand_name FROM `users` WHERE user_type = 'Brand' AND brand_name IS NOT NULL";

    // Get results using db library (e.g., ezSQL or WordPress $wpdb)
    $result = $db->get_results($brand_query);

    if ($result) {
        // Convert result objects to array
        $brands = array();
        foreach ($result as $row) {
            $brands[] = array("brand_name" => $row->brand_name);
        }

        echo json_encode($brands);
    } else {
        echo json_encode(array("status" => "error", "message" => "No brand data found."));
    }

    exit;
}


*/


if (isset($_GET['action']) && $_GET['action'] == 'brand_api') {

    header('Content-Type: application/json');

    // Adjust the column name if needed
    $brand_query = "SELECT * FROM `users` WHERE user_type = 'Brand' AND brand_name IS NOT NULL";

    $result = $db->get_results($brand_query);

    $brands = [];

    if ($result) {
        foreach ($result as $row) {
echo $row->brand_name;
            $brands[] = array(
                "id" => $row->id,
                "brand_name" => $row->brand_name
            );
        }

        echo json_encode($brands);
    } else {
        echo json_encode(array("status" => "error", "message" => "No brands found."));
    }

    exit;
}


if (isset($_GET['action']) && $_GET['action'] == 'get_chat_history_admin') {
    $node_id = isset($_GET['node_id']) ? urldecode($_GET['node_id']) : null;
    
    // Get device info
    $query = "SELECT * FROM efactorproduct where node_id='$node_id'";
    $row = $db->get_row($query);
    $device_user_id = $row['user_id'];
    $user_id = $row['user_id'];

    // Get all messages for this device (between admin and user)
    $chat_query = "SELECT * FROM call_service 
                  WHERE node_id = '$node_id' 
                  AND ((from_userid = '$device_user_id' AND to_userid = 1) 
                       OR (from_userid = 1 AND to_userid = '$device_user_id'))
                  ORDER BY created_at ASC";
    
    $messages = $db->get_results($chat_query);
    
    $response = array();
    foreach ($messages as $msg) {
        // Determine if sender is admin or user
        $sender_type = ($msg['from_userid'] == 1) ? 'admin' : 'user';
        
        $response[] = array(
            'sender' => $sender_type,
            'message' => $msg['message'],
            'timestamp' => $msg['created_at'],
            'status' => $msg['status'] ?? 'delivered', // assuming status field exists
            'id' => $msg['id'] // message ID for marking as read
        );
    }
    
    // Mark user messages as read when admin fetches them
    $db->query("UPDATE call_service SET status = 'read' 
               WHERE node_id = '$node_id' 
               AND from_userid = '$device_user_id' 
               AND to_userid = 1 
               AND status = 'delivered'");
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}





if (isset($_GET['action']) && $_GET['action'] == 'get_chat_history') {
    $node_id = isset($_GET['node_id']) ? urldecode($_GET['node_id']) : null;
   // $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
    
    // Get device info
    $query = "SELECT * FROM efactorproduct where node_id='$node_id'";
    $row = $db->get_row($query);
    $device_user_id = $row['user_id'];


//$db->query("update call_service set status='read' where node_id='$node_id'");

    // Get all messages for this device (between admin and user)
    $chat_query = "SELECT * FROM call_service 
                  WHERE node_id = '$node_id' 
                  AND ((from_userid = '$device_user_id' AND to_userid = 1) 
                       OR (from_userid = 1 AND to_userid = '$device_user_id'))
                  ORDER BY created_at ASC";
    
    $messages = $db->get_results($chat_query);
    
    // Debug output
    error_log(print_r($messages, true));
    
    $response = array();
    foreach ($messages as $msg) {
        // Determine if sender is admin or user
        $sender_type = ($msg['from_userid'] == 1) ? 'admin' : 'user';
        
        $response[] = array(
            'sender' => $sender_type,
            'message' => $msg['message'],
            'timestamp' => $msg['created_at']
        );
    }
    
    // Set proper JSON header
    header('Content-Type: application/json');
    
    // Debug output before sending
    error_log('Final response: ' . json_encode($response));
    
    echo json_encode($response);
    exit;
}





if (isset($_GET['action']) && $_GET['action'] == 'get_new_messages') {




$node_id = $_GET['node_id'] ?? '';
$user_id = $_GET['user_id'] ?? '';
//$since = $_GET['since'] ?? '';


// Validate required parameters
if (empty($node_id) || empty($user_id)) {
    echo json_encode(['error' => 'Missing required parameters (node_id and user_id are required)']);
    exit;
}

try {
    // Create database connection
  //  $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
  //  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the base query for call_service table
    $query = "SELECT 
                id, 
                model_name,
                node_id,
                message, 
                from_userid, 
                to_userid, 
                ticket_number,
                flag,
                created_at as timestamp
              FROM call_service 
              WHERE node_id = :node_id 
              AND to_userid = :user_id
              AND from_userid != :user_id"; // Messages sent to this user from others

    $params = [
        ':node_id' => $node_id,
        ':user_id' => $user_id
    ];

    // Add timestamp filter if provided
    if (!empty($since)) {
        $query .= " AND created_at > :since";
        $params[':since'] = $since;
    }

    // Only get unread messages (flag = 1 means unread)
    $query .= " AND flag = 1";

    // Order by timestamp (oldest first)
    $query .= " ORDER BY created_at ASC";

    // Execute the query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark messages as read (set flag = 0) if we found any
    if (!empty($messages)) {
        $message_ids = array_column($messages, 'id');
        $placeholders = implode(',', array_fill(0, count($message_ids), '?'));
        
        $update_query = "UPDATE call_service SET flag = 0 WHERE id IN ($placeholders)";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->execute($message_ids);
    }

    // Format the response to match expected frontend structure
    $formatted_messages = array_map(function($msg) {
        return [
            'id' => $msg['id'],
            'from_userid' => $msg['from_userid'],
            'to_userid' => $msg['to_userid'],
            'message' => $msg['message'],
            'timestamp' => $msg['timestamp'],
            'is_read' => $msg['flag'] == 0 ? 1 : 0, // Convert flag to is_read
            'ticket_number' => $msg['ticket_number'] ?? null
        ];
    }, $messages);

    // Return the messages
    echo json_encode([
        'success' => true,
        'messages' => $formatted_messages,
        'count' => count($formatted_messages),
        'last_timestamp' => !empty($formatted_messages) ? end($formatted_messages)['timestamp'] : null
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Database error',
        'details' => $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'error' => 'Server error',
        'details' => $e->getMessage()
    ]);
}




}




    /*---------------------------------------------------------------------------------------*/
    /*---------------------------------------------------------------------------------------*/
    /**********************************************************************************************/
    if (isset($_GET['action']) && $_GET['action'] == 'call_services_response') {
        $model_name = $_GET['model_name'];
        $user_id = $_GET['user_id'];
        $node_id = $_GET['node_id']; 
         
        $query = "SELECT * FROM call_service WHERE model_name = '$model_name' AND node_id = '$node_id'";
         
        $rows = $db->get_results($query); 
     
        if ($rows) {
            foreach ($rows as $row_param) {
                $ticket_name = $row_param['ticket_number'];
                $created_at = $row_param['created_at'];
                $message = $row_param['message'];
                $flag = $row_param['flag'];
                $db_node_id = $row_param['node_id'];
     
                if ($node_id === $db_node_id) {
                    $output = "<div class='ticket-container'>";
                    $output .= "<h3>Ticket Details</h3>";
                    $output .= "<p><strong>Ticket Number:</strong> <span>$ticket_name</span></p>";
                    $output .= "<p><strong>Date:</strong> <span>$created_at</span></p>";
                    $output .= "<p><strong>Issue:</strong> <span>$message</span></p>";
     
                    if ($flag == "1") {
                        $output .= "<p><strong>Status:</strong> <span class='status pending'>Pending</span></p>";
                    } else {
                        $output .= "<p><strong>Status:</strong> <span class='status resolved'>Successfully Resolved</span></p>";
                    }
                    $output .= "</div>";
                     
                    echo $output;
                }
            }
        } else {
            echo "No tickets found for this node.";
        }

exit;
    }

/***********************************************************************************/



if (isset($_GET['action']) && $_GET['action'] == 'call_services_response_for_app') {
   // $model_name = $_GET['model_name'];
   // $user_id = $_GET['user_id'];
    $node_id = $_GET['node_id']; 


$query_123 = "SELECT * FROM efactorproduct where node_id='$node_id'";
     $num = $db->num_rows($query_123); 
     $row = $db->get_row($query_123);
     $model_name=$row['model_name'];
     $user_id=$row['user_id'];

     
    $query = "SELECT * FROM call_service WHERE model_name = '$model_name' AND node_id = '$node_id'";
     
    $rows = $db->get_results($query); 
 
    if ($rows) {
        foreach ($rows as $row_param) {
            $ticket_name = $row_param['ticket_number'];
            $created_at = $row_param['created_at'];
            $message = $row_param['message'];
            $flag = $row_param['flag'];
            $db_node_id = $row_param['node_id'];
  
                if ($flag == "1") {
                    $flag_output = "Pending";
                } else {
                    $flag_output = "Successfully Resolved";
                }    
                $response[] = [
                    'ticket_name' => $ticket_name,
                    'created_at' => $created_at,
                    'message' => $message,
                    'flag_output' => $flag_output,
                ];         
        }
    } else {
        $response = [
            'Status' => "No tickets found for this node."
        ];
    }
    echo json_encode($response);
exit;
}





    /*---------------------------------------------------------------------------------------*/
    /**********************************************************************************************/
/**********************************************************************************************/
/*if (isset($_GET['action']) && $_GET['action'] == 'app_video') {
    $model_name = $_GET['model_name'];
    $user_id = $_GET['user_id'];
        
     $query = "SELECT * FROM vendorproducts where modelname='$model_name' AND user_id = '$user_id'";  
 $doctors = $db->get_results($query);   
 $row = $db->get_row($query); 
 $filename=$row['specific_vid_file']; 

 if (empty($filename)|| $filename == "NULL" || $filename == "Null" || $filename == "null") {
    echo "No File Uploaded.";
} else { 
 //echo 'http://localhost/factor_17-05/files/specifications/'.$filename;   
 $video_url = 'https://efactor.tech/files/specifications/'.$filename;
 //$video_url = 'http://localhost/factor_17-05/files/specifications/'.$filename; 
  
 header("Location: " . $video_url);
 exit();
}
 }*/






if (isset($_GET['action']) && $_GET['action'] == 'app_video') {
    session_start();
    $model_name = $_GET['model_name'];
    $user_id = $_GET['user_id'];  

    $query = "SELECT * FROM vendorproducts where modelname='$model_name' AND user_id = '$user_id'";  
    $row = $db->get_row($query); 
    $filename = $row['specific_vid_file']; 

    if (empty($filename)|| $filename == "NULL" || $filename == "Null" || $filename == "null") {
        echo json_encode(["error" => "No File Uploaded."]); 
    } else {
        $files = explode('][', trim($filename, '[]'));
        $filePath = "https://efactor.tech/files/specifications/";

        $fileUrls = [];
        foreach ($files as $file) { 
            $encodedFile = urlencode(trim($file));
            $fileUrl = $filePath . $encodedFile; 
          //  if (checkFileExists($fileUrl)) { 
                $fileUrls[] = $fileUrl;
           // }
        }
        //header("Location: " . $fileUrls);
       // header('Content-Type: application/json');


     //    echo json_encode($fileUrls);
      
       $_SESSION['fileUrls'] = $fileUrls;
 
       header("Location: show_files.php");
exit;
    }
}

function checkFileExists($url) {
    $headers = @get_headers($url);
    return strpos($headers[0], '200') !== false; 
}



/**********************************************************************************************/
/*---------------------------------------------------------------------------------------*/


if (isset($_GET['action']) && $_GET['action'] == 'Auto_test') {
    set_time_limit(0);  // Allow unlimited script execution time
    //$model_name = $_GET['model_name'];
    $model_name = $_GET['model_number'];
    $user_id = $_GET['user_id'];  
    $node_id = $_GET['node_id'];  
    // Fetch the command parameter names
   /* $query_mqtt = "SELECT cmd_param_names FROM autotest WHERE modelno='EF-002FMN02'";
    $results = $db->get_results($query_mqtt);

    if (empty($results)) {
        echo "No results found!";
        exit;
    }

    $cmd_param_names = $results[0]['cmd_param_names'];
    $cmd_param_names = trim($cmd_param_names, "[]");
    $individual_params = array_map('trim', explode("],[", $cmd_param_names));

    print_r($individual_params);*/

    $data['value'] = 'true';

    /*-------------------------------------------*/ 

$query_mqtt = "SELECT * FROM autotest WHERE modelno='$model_name'";
$results = $db->get_row($query_mqtt); 

if (empty($results)) {
    echo "No results found!";
    exit;
} 

$cmd_param_names = $results['cmd_param_names'];
$cmd_values = $results['cmd_values'];
$cmd_time = $results['cmd_time'];
$cmd_startbyte = $results['cmd_startbyte'];


$cmd_param_names_array = array_map('trim', explode(',', trim($cmd_param_names, '[]'))); 
$cmd_time_array = array_map('trim', explode(',', trim($cmd_time, '[]'))); 
$cmd_startbyte = trim($cmd_startbyte, '[]');
 
$cmd_startbyte_array = explode('],[' ,$cmd_startbyte); 
foreach ($cmd_startbyte_array as $key => $value) {
    if ($value === '') { 
        $cmd_startbyte_array[$key] = [];
    } else { 
        $cmd_startbyte_array[$key] = array_map('trim', explode(',', $value));
    }
}

$cmd_values = trim($cmd_values, '[]');
 
$cmd_values_array = explode('],[' ,$cmd_values); 
foreach ($cmd_values_array as $key => $value) {
    if ($value === '') { 
        $cmd_values_array[$key] = [];
    } else { 
        $cmd_values_array[$key] = array_map('trim', explode(',', $value));
    }
}
//print_r($cmd_startbyte_array);

/*
if (
    count($cmd_param_names_array) !== count($cmd_values_array) ||
    count($cmd_param_names_array) !== count($cmd_time_array) ||
    count($cmd_param_names_array) !== count($cmd_startbyte_array)
) {
    echo "Error: Array lengths do not match!";
   // exit;
} 
*/ 
$response_array = [];
$commands = [];
foreach ($cmd_param_names_array as $index => $param) {
    
    if (empty($param)) continue;

    $commands[] = [
        'parameter_name' => $param,
        'value' => $cmd_values_array[$index] ?? null,
        'time' => $cmd_time_array[$index] ?? null,
        'startbyte' => $cmd_startbyte_array[$index] ?? null,
    ];
    $param =  trim($param, '[]');
    //$cmd_values_array[$index] =  trim($cmd_values_array[$index], '[]');
    $cmd_time_array[$index] =  trim($cmd_time_array[$index], '[]');
    //$cmd_startbyte_array[$index] =  trim($cmd_startbyte_array[$index], '[]'); 
if (!empty($param)) { 
    //echo "Processing parameter: $param <br>";

    $query_mqtt = "SELECT * FROM product_api WHERE model_name='$model_name' AND parameter_name='$param'";
    
    $row_mqtt = $db->get_row($query_mqtt);
    
    if (empty($row_mqtt)) {
       // echo "No data found for parameter: $param<br>";
        continue;
    } 
    $parameter_name = $row_mqtt['parameter_name']; 

    $cmd_framesize = $row_mqtt['cmd_framesize'];


 $cmd_value_min = $row_mqtt['cmd_valueinhexmin'];
 $cmd_startbytes = $row_mqtt['cmd_startbytePosition'];
    $cmd_value_max = $row_mqtt['cmd_valueinhexmax'];
    $cmd_value_min_1 = str_replace(' ', '', $cmd_value_min);
    $cmd_value_max_1 = str_replace(' ', '', $cmd_value_max);
    $cmd_noofbytes_1 = str_replace(' ', '', $cmd_noofbytes);
    $cmd_value_min_array = explode(',', $cmd_value_min_1);
    $cmd_value_max_array = explode(',', $cmd_value_max_1);
    $cmd_db_startbyte = explode(',', $cmd_startbytes);

    // print_r($cmd_db_startbyte);
    // print_r($cmd_startbyte_array[$index]);
//print_r($cmd_value_min_array);
 //print_r($cmd_values_array[$index]);
 $form_startbyte = $cmd_startbyte_array[$index];
 $form_values = $cmd_values_array[$index]; 
 $form_wait_time = $cmd_time_array[$index];

 $db_assoc_array = [];
 
 foreach ($cmd_db_startbyte as $index => $key) { 
     if (isset($cmd_value_min_array[$index])) {
         $db_assoc_array[$key] = $cmd_value_min_array[$index];
     } else {
         $db_assoc_array[$key] = ''; 
     }
 } 
  //print_r($db_assoc_array);

 $form_assoc_array = [];
 
 foreach ($form_startbyte as $index => $key) { 
     if (isset($form_values[$index])) {
         $form_assoc_array[$key] = $form_values[$index];
     } else {
         $form_assoc_array[$key] = ''; 
     }
 } 
 //print_r($form_assoc_array);
 
 foreach ($db_assoc_array as $key => $value) { 
    if (isset($form_assoc_array[$key])) { 
        $db_assoc_array[$key] = $form_assoc_array[$key];
    }
} 
//print_r($db_assoc_array);

//print_r($cmd_value_min_array);
 /*   $mqtt_message = json_encode(array( 
        'model_name' => $model_name,
        'device_id' => $node_id,
        'payload' => $db_assoc_array, 
    ));*/


/*************/
$payload = '';
foreach ($db_assoc_array as $value) {
    $payload .= $value;  
}

 
//echo $payload;

$node_id='FFFFFFFFFFFFFFFF7EC2000000033F00';
//$address='';


    $mqtt_message = json_encode([
        'command_type' => 'GET_DEVICE_STATUS',
        'address' => $node_id,
        'device_id' => $node_id,
        'payload' => $payload,
    ]);




    
    $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

    
    if (!$mqtt->connect(true, NULL, $username, $password)) {
        echo "Failed to connect to MQTT broker.<br>";
        exit; // Exit if the connection fails
    }

    
    $topic = "Efactor/things/{$node_id}/command";

    
    echo "Publishing to topic: $topic<br>";
    echo "Message: $mqtt_message<br>";
    print_r($mqtt_message);
    
    $mqtt->publish($topic, $mqtt_message);
    $mqtt->close();


/***********/

   // https://efactor.tech/v8/auto_test_2-12.php?action=Auto_test&node_id=FFFFFFFFFFFFFFFF7EC2000000033F00&payload=A1C2010A&model_name=EF-002FMN02
   if ($form_wait_time > 0) {
   // echo "Waiting for $form_wait_time seconds before next iteration...<br>"; 
    sleep($form_wait_time);  
} 
//$node_id = 'FFFFFFFFFFFFFFFF7EC2000000004200';   
$query_testing = "SELECT auto_testing FROM `$model_name` WHERE node_id = '$node_id'";

$result = $db->get_row($query_testing);  
if ($result) {
    $auto_testing_value = $result['auto_testing'];   
    if (strtolower($auto_testing_value) == 'tested') {
        $response_array[] = "Success ".$param;
    } else {
        $response_array[] = "Failed ".$param;
    }
} 
}
}
 
$response_json = json_encode($response_array);
 
echo $response_json;
}
/*---------------------------------------------------------------------------------------*/
/*****************************************************************************************/

/**********************************************************************************************/
/*---------------------------------------------------------------------------------------*/


if (isset($_GET['action']) && $_GET['action'] == 'Product_config') {



function float_to_hex_big_endian($float) { 
    $binary = pack('G', $float);  
    $hex = strtoupper(bin2hex($binary));  
    return $hex;
}

    set_time_limit(0);

   $model_name = $_GET['model_number'];
//$model_name='EF-002FMN02';

    sleep(6);

/*

echo "TEST STARTED......\n";
    ob_flush();
    flush();

*/





    $multiple_nodes_query = "SELECT * FROM config_table WHERE model_name = '$model_name'";
    $nodes_query = $db->get_results($multiple_nodes_query);


//echo $node_data['mode'];
//print_r($nodes_query);

    foreach ($nodes_query as $node_data) {




     if($node_data['mode'] == 'Auto test'){
        $node_ids_string = $node_data['node_id'];
        $node_ids = explode(',', $node_ids_string);

 $user_id = $node_data['user_id'];
$modelSelect = $node_data['model_name'];  
$router = $node_data['router_id']; 

        foreach ($node_ids as $node_id) {
            echo $node_id . " once in a loop ";
            
            $query_mqtt = "SELECT * FROM autotest WHERE modelno='$model_name'";
            $results = $db->get_row($query_mqtt);

     

            if (empty($results)) {
                echo "No results found!";
                exit;
            }

            $cmd_param_names = $results['cmd_param_names'];
            $cmd_values = $results['cmd_values'];
            $cmd_time = $results['cmd_time'];
            $cmd_startbyte = $results['cmd_startbyte'];

            $cmd_param_names_array = array_map('trim', explode(',', trim($cmd_param_names, '[]')));
            $cmd_time_array = array_map('trim', explode(',', trim($cmd_time, '[]')));
            $cmd_startbyte = trim($cmd_startbyte, '[]');

            $cmd_startbyte_array = explode('],[', $cmd_startbyte);
            foreach ($cmd_startbyte_array as $key => $value) {
                $cmd_startbyte_array[$key] = $value === '' ? [] : array_map('trim', explode(',', $value));
            }

            $cmd_values = trim($cmd_values, '[]');
            $cmd_values_array = explode('],[', $cmd_values);
            foreach ($cmd_values_array as $key => $value) {
                $cmd_values_array[$key] = $value === '' ? [] : array_map('trim', explode(',', $value));
            }

            $response_array = [];
            $commands = [];

            foreach ($cmd_param_names_array as $index => $param) {
                if (empty($param)) continue;

                $commands[] = [
                    'parameter_name' => $param,
                    'value' => $cmd_values_array[$index] ?? null,
                    'time' => $cmd_time_array[$index] ?? null,
                    'startbyte' => $cmd_startbyte_array[$index] ?? null,
                ];

                $param = trim($param, '[]');
                $cmd_time_array[$index] = trim($cmd_time_array[$index], '[]');

                if (!empty($param)) {
                    $query_mqtt = "SELECT * FROM product_api WHERE model_name='$model_name' AND parameter_name='$param'";
                    $row_mqtt = $db->get_row($query_mqtt);

                    if (empty($row_mqtt)) continue;

                    $parameter_name = $row_mqtt['parameter_name'];
                    $cmd_framesize = $row_mqtt['cmd_framesize'];
                    $cmd_value_min = str_replace(' ', '', $row_mqtt['cmd_valueinhexmin']);
                    $cmd_value_max = str_replace(' ', '', $row_mqtt['cmd_valueinhexmax']);
                    $cmd_startbytes = $row_mqtt['cmd_startbytePosition'];
                    $cmd_value_min_array = explode(',', $cmd_value_min);
                    $cmd_value_max_array = explode(',', $cmd_value_max);
                    $cmd_db_startbyte = explode(',', $cmd_startbytes);

                    $form_startbyte = $cmd_startbyte_array[$index];
                    $form_values = $cmd_values_array[$index];
                    $form_wait_time = $cmd_time_array[$index];

                    $db_assoc_array = [];
                    foreach ($cmd_db_startbyte as $i => $key) {
                        $db_assoc_array[$key] = $cmd_value_min_array[$i] ?? '';
                    }

                    $form_assoc_array = [];
                    foreach ($form_startbyte as $i => $key) {
                        $form_assoc_array[$key] = $form_values[$i] ?? '';
                    }

                    foreach ($db_assoc_array as $key => $value) {
                        if (isset($form_assoc_array[$key])) {
                            $db_assoc_array[$key] = $form_assoc_array[$key];
                        }
                    }

                    $payload = '';
                    foreach ($db_assoc_array as $value) {
                        $payload .= $value;
                    }
 if($node_data['status'] == 'Online'){


                    $mqtt_message = json_encode([
                        'command_type' => 'GET_DEVICE_STATUS',
                        'payload' => $payload,
                    ]);

                    $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

                    if (!$mqtt->connect(true, NULL, $username, $password)) {
                        exit;
                    }

                    $topic = "Efactor/things/{$node_id}/command";
                    $mqtt->publish($topic, $mqtt_message);
                    $mqtt->close();
    }

else{



    // echo "Into Else Part";
    
$networkInfos = []; 
for ($i = 0; $i < 3; $i++) {
//echo "in to for loop";
sleep(3); 
    $query = "SELECT * FROM `$modelSelect` WHERE node_id = '$node_id1'";
   //echo $query; 
     $row_param =  $db->get_row($query); 
      
        $networkInfos[] = $row_param['network_info'];  
        $network = $row_param['network_info'];    
        if (strpos($network, 'OK') !== false) {
            $foundOK = true;
            break;  
        }
}
 
//print_r($networkInfos);

 if ($foundOK) {
       // echo "Done\n";
       $mqtt_message = json_encode([
        'command_type' => 'GET_DEVICE_STATUS',
        'address' => $router,
        'device_id' => $node_id1,
        'payload' => $payload
    ]);



   


    $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

     if (!$mqtt->connect(true, NULL, $username, $password)) {
        exit("Unable to connect to MQTT broker");
     }

    print_r($mqtt_message);

    $topic = "Efactor/things/{$node_id1}/command";

    $mqtt->publish($topic, $mqtt_message_off);
    sleep(3); 
    $mqtt->publish($topic, $mqtt_message);
sleep(3); 
    $mqtt->publish($topic, $mqtt_message_on);


    $mqtt->close(); // Close connection after each message
    } else {
        //echo "Exiting loop and starting a new one\n";
        break;  
    }
    
    }
                    if ($form_wait_time > 0) {
                        sleep($form_wait_time);
                    }

                    $query_testing = "SELECT auto_testing FROM `$model_name` WHERE node_id = '$node_id'";
                    $result = $db->get_row($query_testing);

                    if ($result) {
                        $auto_testing_value = $result['auto_testing'];
                        if (strtolower($auto_testing_value) == 'tested') {
                            $response_array[] = "Success " . $param;
                        } else {
                            $response_array[] = "Failed " . $param;
                        }
                    }
                }
            }

            $response_json = json_encode($response_array);
            echo $response_json;
        }
        }





else{



        
$keys2 = $node_data['config_keys'];
$values2 = $node_data['configValues'];
$user_id = $node_data['user_id'];
$modelSelect = $node_data['model_name']; 
$node_ids2 = $node_data['node_id']; 
$router = $node_data['router_id']; 





$keys = explode(',', $keys2);
$values = explode(',', $values2);  
$node_ids22 = explode(',', $node_ids2);  
$node_ids42 = "'" . implode("','", $node_ids22) . "'"; 
//print_r($node_ids);
$form_entered_values = array_combine($keys, $values);

$set_query = [];
foreach ($keys as $index => $key) {
    $value = $values[$index];
     
    if ($value === '') {
        $value = 'NULL';
    }
    
    if (strpos($key, ' ') !== false) {
        $set_query[] = "`$key` = '$value'";
    } else if (strpos($value, '.') !== false) { 
        $value = float_to_hex_big_endian($value);
        $set_query[] = "$key = '$value'";
    } else {
        $value = dechex(intval($value));
        $set_query[] = "$key = '$value'";
    }
}

$set_clause = implode(", ", $set_query);
$keys_placeholder = "'" . implode("', '", $keys) . "'";
//print_r($keys_placeholder);
$param_sql = "SELECT parameter_name, cmd_defaultvalue FROM product_api WHERE model_name = '$modelSelect'";
$result_221 = $db->get_results($param_sql);

$filteredResults = [];
foreach ($result_221 as $row) {
    $cmd_defaultvalue = $row->cmd_defaultvalue;
    foreach ($keys_placeholder as $key) { 
        if (preg_match('/(?:^|,)' . preg_quote($key, '/') . '(?:,|$)/', $cmd_defaultvalue)) {
            $filteredResults[] = $row;
            break;  
        }
    }
}
 



foreach ($result_221 as $index => &$item) { 
    $cmd_values = explode(',', $item['cmd_defaultvalue']);
     
    $cmd_values = array_filter($cmd_values, function($value) {
        return !empty($value);
    }); 
    $matches = array_intersect($cmd_values, $keys); 
    if (!empty($matches)) { 
        $item['cmd_defaultvalue'] = implode(',', $matches);
    } else { 
        unset($result_221[$index]);
    }
}
 
$result_221 = array_values($result_221);
foreach ($result_221 as &$item) { 
    $cmd_values = explode(',', $item['cmd_defaultvalue']); 
    foreach ($cmd_values as &$cmd_value) { 
        if (array_key_exists($cmd_value, $form_entered_values)) {
            $cmd_value = $form_entered_values[$cmd_value];
        }
    }
 
    $item['cmd_defaultvalue'] = implode(',', $cmd_values);
}
  
  //print_r($result_221);






$startByte_sql = "SELECT cmd_startbytePosition, cmd_endbytePosition, cmd_defaultvalue, cmd_datatype FROM product_api WHERE model_name = '$modelSelect'";
$result_222 = $db->get_results($startByte_sql);
$filteredResults = [];
//print_r($result_222);

foreach ($result_222 as &$item) {  
    $defaultValues = explode(',', $item['cmd_defaultvalue']);
    $startBytePositions = explode(',', $item['cmd_startbytePosition']);
    $endBytePositions = explode(',', $item['cmd_endbytePosition']);
    $dataTypes = explode(',', $item['cmd_datatype']);
      
    $filteredDefaultValues = [];
    $filteredStartBytePositions = [];
    $filteredEndBytePositions = []; // Corrected variable name
    $filteredDataTypes = [];
 
    foreach ($defaultValues as $index => $value) {
        if (!empty(trim($value))) { 
            $filteredDefaultValues[] = $value; 
            $filteredStartBytePositions[] = $startBytePositions[$index];
            $filteredEndBytePositions[] = $endBytePositions[$index]; // Corrected variable name
            $filteredDataTypes[] = $dataTypes[$index];
        }
    }
 
    $item['cmd_defaultvalue'] = implode(',', $filteredDefaultValues);
    $item['cmd_startbytePosition'] = implode(',', $filteredStartBytePositions);
    $item['cmd_endbytePosition'] = implode(',', $filteredEndBytePositions); // Corrected variable name
    $item['cmd_datatype'] = implode(',', $filteredDataTypes);
}
 
foreach ($result_222 as $index => &$item) {
    $cmd_values = explode(',', $item['cmd_defaultvalue']);
     
    $cmd_values = array_filter($cmd_values, function($value) {
        return !empty($value);
    });
 
    $matches = array_intersect($cmd_values, $keys);
 
    if (!empty($matches)) { 
        $item['cmd_defaultvalue'] = implode(',', $matches);
 
        $startbytePositions = explode(',', $item['cmd_startbytePosition']);
        $endbytePositions = explode(',', $item['cmd_endbytePosition']);
        $dataTypes = explode(',', $item['cmd_datatype']);
         
        $filteredStartPositions = []; // Corrected variable name
        $filteredEndPositions = []; // Corrected variable name
        $filteredTypes = [];

        foreach ($matches as $match) {
            $index = array_search($match, $cmd_values);
            if ($index !== false) {
                $filteredStartPositions[] = $startbytePositions[$index];
                $filteredEndPositions[] = $endbytePositions[$index];
                $filteredTypes[] = $dataTypes[$index];
            }
        }
 
        $item['cmd_startbytePosition'] = implode(',', $filteredStartPositions);
        $item['cmd_endbytePosition'] = implode(',', $filteredEndPositions);
        $item['cmd_datatype'] = implode(',', $filteredTypes);
    } else {
        unset($result_222[$index]);
    }
}
$result_222 = array_values($result_222);
 
foreach ($result_221 as $item) { 
    $parameters[] = $item['parameter_name']; 
    $cmd_defaultvalues[] = $item['cmd_defaultvalue'];
}
 
$cmd_startbyte = [];
$cmd_endbyte = [];
$cmd_datatype = []; 

foreach ($result_222 as $item) { 
    $cmd_startbyte[] = $item['cmd_startbytePosition'];
    $cmd_endbyte[] = $item['cmd_endbytePosition'];  
    $cmd_datatype[] = $item['cmd_datatype'];  
}

//print_r($cmd_startbyte);
//print_r($cmd_endbyte);
//print_r($cmd_defaultvalues);







foreach ($cmd_defaultvalues as $index => &$value) {
    if (is_string($value)) { 
        $value = explode(',', $value);
    }
} 



$cmd_defaultvalues_copy = $cmd_defaultvalues; 

 
foreach ($cmd_startbyte as $index => &$value) {
    if (is_string($value)) { 
        $value = explode(',', $value);
    }
} 
foreach ($cmd_defaultvalues as $index => &$value) {
    if (is_string($value)) { 
        $value = explode(',', $value);
    }
} 


foreach ($cmd_datatype as $index => $data_types) {


    foreach ($data_types as $key => $data_type) {
       /* if ($data_type === 2) {
            $cmd_datatype[$index][$key] = 'Float';
        }*/
    }
}

/*----------------*/

$processed_cmd_datatype = [];
foreach ($cmd_datatype as $index => $data_type) {
    if (strpos($data_type, ',') !== false) {
        $processed_cmd_datatype[$index] = explode(',', $data_type);
    } else {
        $processed_cmd_datatype[$index] = [$data_type];
    }
}

// Convert values based on data types

$converted_values = [];
foreach ($cmd_defaultvalues_copy as $index => $default_values) {
    $data_types = $processed_cmd_datatype[$index] ?? [];
    $converted_values[$index] = [];

    foreach ($default_values as $key => $value) {
        $data_type = $data_types[$key] ?? null; // Match sub-index type


 if ($data_type === 'UnsignedInt') {
             //echo 'UnsignedInt';

 //echo "Key: $key | Value: $value\n";

if($value == '16000' || $value == '7000')
{
    $converted_values[$index][$key] = str_pad(strtoupper(dechex($value)), 8, '0', STR_PAD_LEFT);
}
else{

             $converted_values[$index][$key] = str_pad(strtoupper(dechex($value)), 2, '0', STR_PAD_LEFT);

}
        }



  /*     
if ($data_type === 'UnsignedInt') {
    // Calculate byte length based on start and end byte positions
    $start_byte = $cmd_startbyte[$index];
    $end_byte = $cmd_endbyte[$index]; // End byte position
    $byte_length = $end_byte - $start_byte + 1; // Byte length

    // Convert UnsignedInt to hexadecimal with the correct byte length
    $hex_length = $byte_length * 2; // Each byte is 2 hexadecimal characters
    $converted_values[$index][$key] = str_pad(strtoupper(dechex($value)), $hex_length, '0', STR_PAD_LEFT);
}


*/

elseif ($data_type === 'Float') {
           // echo 'Float';


            $binary_float = pack('f', $value);

$binary_little_endian = strrev($binary_float);
$hex_float = bin2hex($binary_little_endian);




$converted_values[$index][$key] = strtoupper($hex_float);


        } else {
                //echo 'else';
            $converted_values[$index][$key] = $value; // Default to original value
        }
    }
}




/*


$converted_values = [];

foreach ($cmd_defaultvalues_copy as $index => $default_values) {
    $data_types = $processed_cmd_datatype[$index] ?? [];
    $start_byte_positions = explode(',', $cmd_startbyte[$index]); // Start byte positions
    $end_byte_positions = explode(',', $cmd_endbyte[$index]); // End byte positions
    $converted_values[$index] = [];


echo 'hi....';
print_r($start_byte_positions);

    foreach ($default_values as $key => $value) {
        $data_type = $data_types[$key] ?? null;
        $start_byte = (int)$start_byte_positions[$key];
        $end_byte = (int)$end_byte_positions[$key];
        $byte_length = $end_byte - $start_byte + 1; // Correct byte length calculation

        echo "Key: $key | Start Byte: $start_byte | End Byte: $end_byte | Byte Length: $byte_length\n";

        if ($data_type === 'UnsignedInt') {
            // Convert to hex and pad to the exact byte length
            $hex_value = str_pad(strtoupper(dechex($value)), $byte_length * 2, '0', STR_PAD_LEFT);
            $converted_values[$index][$start_byte] = substr($hex_value, -($byte_length * 2)); // Ensure exact byte length
        } elseif ($data_type === 'Float') {
            // Convert Float to IEEE 754 (4-byte) Little Endian format
            $binary_float = pack('f', $value);
            $hex_float = strtoupper(bin2hex(strrev($binary_float))); // Convert to IEEE 754 Little Endian
            $converted_values[$index][$start_byte] = substr($hex_float, -($byte_length * 2)); // Ensure exact length
        } else {
            $converted_values[$index][$start_byte] = $value;
        }
    }
}

// Print the final converted values, ensuring correct start byte placement
print_r($converted_values);
*/

/*--------------------------------------------------*/
//print_r($converted_values);
//print_r($cmd_defaultvalues_copy);
$node_ids = explode(',', $node_ids2);



/****************************E_SCAN******************************/


//$payload_123 = 'CD01';


$mqtt_message = json_encode([
        'command_type' => 'E_SCAN',
        'address' => $router,
        'device_id' => $router,
        'payload' => "SCAN"
    ]);


   



    $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

    if (!$mqtt->connect(true, NULL, $username, $password)) {
        exit("Unable to connect to MQTT broker");
    }

   // print_r($mqtt_message);

    $topic = "Efactor/things/{$router}/command";
    $mqtt->publish($topic, $mqtt_message);
    
$mqtt->close(); // Close connection after each message

$db->query("update efactorproduct set E_Scan='NULL' where node_id='$router'");


sleep(5); 



$E_Scan = []; 
     $router_ids = "SELECT * FROM efactorproduct WHERE node_id = '$router'";
     $router_id =  $db->get_row($router_ids); 
      
        $E_Scan[] = $router_id['E_Scan'];  
      
$match_found = false; 

$scan_values = explode(',', $E_Scan[0]);
$node_ids_values = explode(',', $node_ids);








$node_id_matched = [];
//foreach ($node_ids_values as $node_id21) {

foreach ($node_ids as $node_id21) {
    
    $node_middle_3 = substr($node_id21, -5, 3);   
   // echo "-node-".$node_middle_3."--"; 
    foreach ($scan_values as $scan) {
         
        $scan_last_3 = substr($scan, -3);
 //echo "-scan-".$scan_last_3."--";
        if ($scan_last_3 === $node_middle_3) {
           // echo "Match found: " . $scan . " for node ID " . $node_id21 . "\n";
            $node_id_matched[] = $node_id21;
           $match_found = true;
            break; 
        }
    }
}

// echo 'Hi';

echo "\nNode IDs Match Values :\n";

print_r($node_id_matched);
 //print_r($node_ids);





if (!$match_found) {
    echo "No match found\n";
}




print_r($node_ids);


/************************************************************/


foreach ($node_id_matched as $node_id1) {


//echo $node_id1;
   foreach ($parameters as $index => $param) {
   

/***************product_config*****************/

if (empty($param)) {
        continue;
    }

  if (isset($cmd_defaultvalues_copy[$index])) {
        $def_value = $converted_values[$index];
       
    } else {
        echo "Index $index does not exist in cmd_defaultvalues_copy.\n";
    }


//print_r($def_value);




    

    $commands[] = [
        'parameter_name' => $param,
        'value' => $def_value
    ];



    
                if (!empty($param)) {
                    $query_mqtt = "SELECT * FROM product_api WHERE model_name='$modelSelect' AND parameter_name='$param'";
                   
                    $row_mqtt = $db->get_row($query_mqtt);
                    if (empty($row_mqtt)) continue;

                    $parameter_name = $row_mqtt['parameter_name'];
                    $cmd_framesize = $row_mqtt['cmd_framesize'];
                    $cmd_value_min = str_replace(' ', '', $row_mqtt['cmd_valueinhexmin']);
                    $cmd_value_max = str_replace(' ', '', $row_mqtt['cmd_valueinhexmax']);
                    $cmd_startbytes = $row_mqtt['cmd_startbytePosition'];
                    $cmd_value_min_array = explode(',', $cmd_value_min);
                    $cmd_value_max_array = explode(',', $cmd_value_max);
                    $cmd_db_startbyte = explode(',', $cmd_startbytes);
                    //print_r($cmd_value_min_array );
                    $form_startbyte = $cmd_startbyte[$index];
                    $form_values = $def_value;

//print_r($form_values);
                    
                      

                    $db_assoc_array = [];
                    foreach ($cmd_db_startbyte as $i => $key) {
                        $db_assoc_array[$key] = $cmd_value_min_array[$i] ?? '';
                     //  echo $db_assoc_array[$key];
                    }
                   //print_r($db_assoc_array[$key]); 
                    $form_assoc_array = [];
                    foreach ($form_startbyte as $i => $key) {
                        $form_assoc_array[$key] = $form_values[$i] ?? '';
                     // echo $form_assoc_array[$key]."-";
                    }



                   //print_r($form_assoc_array[$key]); 
                    foreach ($db_assoc_array as $key => $value) {
                        if (isset($form_assoc_array[$key])) {
                            $db_assoc_array[$key] = $form_assoc_array[$key];
                        }
                    }

                    $payload = '';
                    foreach ($db_assoc_array as $value) {
                        $payload .= $value; 

                  //   echo $value;
                         
                    }
                   // echo $payload." ";






   if($node_data['status'] == 'Online'){


    $mqtt_message = json_encode([
        'command_type' => 'GET_DEVICE_STATUS',
        'address' => $node_id1,
        'device_id' => $node_id1,
        'payload' => $payload
    ]);

    $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

    if (!$mqtt->connect(true, NULL, $username, $password)) {
        exit("Unable to connect to MQTT broker");
    }

   

    $topic = "Efactor/things/{$node_id}/command";
    $mqtt->publish($topic, $mqtt_message);
    $mqtt->close(); // Close connection after each message
    }



else{



//echo 'else_part';




$db->query("update `EF-003FMN03` set network_info='FAIL' where node_id='$node_id1'");

$db->query("update `EF-003FMN03` set configure_node='FAIL' where node_id='$node_id1'");

  
    
$networkInfos = []; 
for ($i = 0; $i < 2; $i++) {

//echo $node_id1;

$segment9 = substr($node_id1, 16, 2);
$segment13 = substr($node_id1, 24, 2);
$segment14 = substr($node_id1, 26, 2);
$segment15 = substr($node_id1, 28, 2);

$underscore='_';

$wifitype='EWIFI';
    

$wifissid = $wifitype.$underscore. $segment9 . $segment13 . $segment14 . $segment15;



//echo $wifissid;

$ssidAscii = stringToAscii($wifissid);
               $result = $ssidAscii;
               $hexResult = asciiToHex($result);   
               $hexResultNoSpaces = strtoupper(str_replace(' ', '', $hexResult));
               $payload_123 = 'CD01' . $hexResultNoSpaces;








$mqtt_message = json_encode([
        'command_type' => 'GET_DEVICE_STATUS',
        'address' => $router,
        'device_id' => $node_id1,
        'payload' => $payload_123
    ]);


    $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

    if (!$mqtt->connect(true, NULL, $username, $password)) {
        exit("Unable to connect to MQTT broker");
    }

   // print_r($mqtt_message);

    $topic = "Efactor/things/{$router}/command";
    $mqtt->publish($topic, $mqtt_message);
    $mqtt->close(); // Close connection after each message

//$db->query("update `EF-003FMN03` set network_info='FAIL' where node_id='$nodeid1'");

//$db->query("update `EF-003FMN03` set configure_node='FAIL' where node_id='$nodeid1'");



sleep(30); 
    $query_network = "SELECT * FROM `$modelSelect` WHERE node_id = '$node_id1'";
    //echo $query; 
     $row_param_network =  $db->get_row($query_network); 
      
      //  $networkInfos[] = $row_param_network['network_info'];  
        $network = $row_param_network['network_info'];    
       if (strpos($network, 'OK') !== false) {
            $foundOK = true;
            break;  
        } 
}
 

echo $network;

$payload_1234='EC01'.$payload;

 if ($network) 

{
       // echo "Done\n";
       $mqtt_message = json_encode([
        'command_type' => 'GET_DEVICE_STATUS',
        'address' => $router,
        'device_id' => $node_id1,
        'payload' => $payload_1234
    ]);



$mqtt_message_off = json_encode([
        'command_type' => 'GET_DEVICE_STATUS',
        'address' => $router,
        'device_id' => $node_id1,
        'payload' => 'EC01A1C2000A'
    ]);


 $mqtt_message_on = json_encode([
        'command_type' => 'GET_DEVICE_STATUS',
        'address' => $router,
        'device_id' => $node_id1,
        'payload' => 'EC01A1C2010A'
    ]);



      $mqtt_message_speed1 = json_encode([
        'command_type' => 'GET_DEVICE_STATUS',
        'address' => $router,
        'device_id' => $node_id1,
        'payload' => 'EC01A1C0010A'
    ]);





$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

    if (!$mqtt->connect(true, NULL, $username, $password)) {
        exit("Unable to connect to MQTT broker");
    }

    //print_r($mqtt_message);

    $topic = "Efactor/things/{$router}/command";


    $mqtt->publish($topic, $mqtt_message_off);
    $mqtt->close(); // Close connection after each message




sleep(7);



    $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

    if (!$mqtt->connect(true, NULL, $username, $password)) {
        exit("Unable to connect to MQTT broker");
    }

    //print_r($mqtt_message);

    $topic = "Efactor/things/{$router}/command";


    $mqtt->publish($topic, $mqtt_message);
    $mqtt->close(); // Close connection after each message

$db->query("update `$modelSelect` set configure_node='FAIL' where node_id='$nodeid1'");


sleep(10);




$query_ack = "SELECT * FROM `$modelSelect` WHERE node_id = '$node_id1'";
    //echo $query; 
     $row_param_ack =  $db->get_row($query_ack); 
      
        $configure_node = $row_param_ack['configure_node'];  


        

if($configure_node == 'OK')
{

   $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

    if (!$mqtt->connect(true, NULL, $username, $password)) {
        exit("Unable to connect to MQTT broker");
    }

    //print_r($mqtt_message);

    $topic = "Efactor/things/{$router}/command";


    $mqtt->publish($topic, $mqtt_message_on);
    $mqtt->close(); // Close connection after each message


sleep(5);



   $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

    if (!$mqtt->connect(true, NULL, $username, $password)) {
        exit("Unable to connect to MQTT broker");
    }

    //print_r($mqtt_message);

    $topic = "Efactor/things/{$router}/command";


    $mqtt->publish($topic, $mqtt_message_speed1);
    $mqtt->close(); // Close connection after each message

sleep(3);

/*
$query_Rpm = "SELECT * FROM `$modelSelect` WHERE node_id = '$node_id1'";
    //echo $query; 
     $row_param_Rpm =  $db->get_row($query_Rpm); 
      
     $rpm = $row_param_Rpm['rpm'];  

*/

      logMessage($node_id1 . " ....Configured. \n");


}


else {
       // echo $node_id1."ACK Failed";
        logMessage($node_id1 . "ACK Failed\n");
        break;  
    }


    } else {
      //  echo $node_id1."network Failed";
        logMessage($node_id1 . "network Failed\n");
        break;  
    }
    
    }
}
 
                }
            }





$db->query("UPDATE `$modelSelect` SET $set_clause WHERE node_id IN ($node_ids42) AND user_id = '$user_id'");



        }  




$mqtt_message_stop = json_encode([
        'command_type' => 'E_SCAN',
        'address' => $router,
        'device_id' => $router,
        'payload' => "STOP"
    ]);


   



    $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

    if (!$mqtt->connect(true, NULL, $username, $password)) {
        exit("Unable to connect to MQTT broker");
    }

   // print_r($mqtt_message);

    $topic = "Efactor/things/{$router}/command";
    $mqtt->publish($topic, $mqtt_message_stop);
    
$mqtt->close(); // Close connection after each message




sleep(2); 








    }



}
/*---------------------------------------------------------------------------------------*/
/*****************************************************************************************/ 

/*****************************************************************************************/
/*-----------------------------------user-nodes-params---------------------------------*/

if (isset($_GET['action']) && $_GET['action']=='user_update_nodes_params_prev') {

$nodeid =$_GET['node_id'];
//$deviceid =$_POST['device_id'];

$header = apache_request_headers();  
foreach ($header as $key => $val) {

$req = file_get_contents( 'php://input' );
    $json_data = json_decode($req, true);



if (is_array($json_data) && isset($json_data['device_id'])) {
    $deviceid = $json_data['device_id'];
   
}


if (is_array($json_data) && isset($json_data['latitude'])) {
    $latitude = $json_data['latitude'];
    
}
if (is_array($json_data) && isset($json_data['longitude'])) {
    $longitude = $json_data['longitude'];
    
}








$query = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$doctors = $db->get_results($query); 
//$num_dev = $db->num_rows($query);
$row = $db->get_row($query); 
$device_type=$row['device_type'];
$addgateway=$row['add_gateway'];




if($device_type == 'fan')
{

$json_data = json_decode($req, true);


if (is_array($json_data)) {
    $deviceid = $json_data['device_id'];

     $speed = isset($json_data['Speed']) ? $json_data['Speed'] : null;
     $led = isset($json_data['led']) ? $json_data['led'] : null;
    $name = isset($json_data['name']) ? $json_data['name'] : null;
    $payload = isset($json_data['payload']) ? $json_data['payload'] : null;
    $baudrate = isset($json_data['Baud_Rate']) ? $json_data['Baud_Rate'] : null;
    $errorcode = isset($json_data['Error_Code']) ? $json_data['Error_Code'] : null;
    $comm_mode = isset($json_data['Communication_Mode']) ? $json_data['Communication_Mode'] : null;
    $uartmsgid = isset($json_data['Uart_Msg_Id']) ? $json_data['Uart_Msg_Id'] : null;
    $time = isset($json_data['Time']) ? $json_data['Time'] : null;
    $services = isset($json_data['services']) ? $json_data['services'] : null;
    $Hue = isset($json_data['led colour']) ? $json_data['led colour'] : null;
    $direction = isset($json_data['Direction']) && $json_data['Direction'] == "1" ? 1 : 0;
        

    $fan_resistance = isset($json_data['resistance']) ? $json_data['resistance'] : null;
$fan_lmin = isset($json_data['lmin']) ? $json_data['lmin'] : null;
$fan_lmax = isset($json_data['lmax']) ? $json_data['lmax'] : null;
$fan_frequency = isset($json_data['frequency']) ? $json_data['frequency'] : null;
$fan_pole_pairs = isset($json_data['pole pairs']) ? $json_data['pole pairs'] : null;
$fan_open_loop_current = isset($json_data['open loop current']) ? $json_data['open loop current'] : null;
$fan_power_limit = isset($json_data['power limit']) ? $json_data['power limit'] : null;
$fan_torque_limit = isset($json_data['torque limit']) ? $json_data['torque limit'] : null;
$fan_speed1 = isset($json_data['speed1']) ? $json_data['speed1'] : null;
$fan_speed2 = isset($json_data['speed2']) ? $json_data['speed2'] : null;
$fan_speed3 = isset($json_data['speed3']) ? $json_data['speed3'] : null;
$fan_speed4 = isset($json_data['speed4']) ? $json_data['speed4'] : null;
$fan_speed5 = isset($json_data['speed5']) ? $json_data['speed5'] : null;
$fan_speed6 = isset($json_data['speed6']) ? $json_data['speed6'] : null;
$fan_speed7 = isset($json_data['speed7']) ? $json_data['speed7'] : null;
$fan_button = isset($json_data['button']) ? $json_data['button'] : null;
$fan_buzzer = isset($json_data['buzzer']) ? $json_data['buzzer'] : null;




    // Update database based on conditions
    if (!is_null($speed)) {
        $db->query("UPDATE efactorproduct SET speed='$speed' WHERE node_id='$deviceid'");
       // $response['status'] = 'successfully updated speed';
    } 
    if (!is_null($payload)) {
      //  $db->query("UPDATE efactorproduct SET payload='$payload' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated payload';
    } 
    if (!is_null($name)) {
        $db->query("UPDATE efactorproduct SET name='$name' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 
    if (!is_null($led)) {
        $db->query("UPDATE efactorproduct SET led_indication='$led' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 

    if (!is_null($latitude)) {
        $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 

    if (!is_null($longitude)) {
        $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 



    if (!is_null($baudrate)) {
        $db->query("UPDATE efactorproduct SET baudrate='$baudrate' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated baudrate';
    } 
    if (!is_null($errorcode)) {
        $db->query("UPDATE efactorproduct SET errorcode='$errorcode' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated errorcode';
    } 
    if (!is_null($comm_mode)) {
        $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated comm_mode';
    } 
    if (!is_null($uartmsgid)) {
        $db->query("UPDATE efactorproduct SET uartmsgid='$uartmsgid' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated uartmsgid';
    } 
    if (!is_null($time)) {
        $db->query("UPDATE efactorproduct SET time='$time' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated time';
    } 

    if (!is_null($services)) {
        // Handle services update if needed
    } 
    if (!is_null($Hue)) {


        $db->query("UPDATE efactorproduct SET Hue='$Hue' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated hue';
    } 
    if (!is_null($direction)) {
        $db->query("UPDATE efactorproduct SET direction='$direction' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated direction';
    } 
    if (!is_null($fan_buzzer)) {
        $fan_buzzer_value = $fan_buzzer == "1" ? 'true' : 'false';
        $db->query("UPDATE efactorproduct SET fan_buzzer='$fan_buzzer_value' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated fan_buzzer';
    } 

if (!is_null($fan_resistance)) {
    $db->query("UPDATE efactorproduct SET fan_resistance='$fan_resistance' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated fan_resistance';
} 
if (!is_null($fan_lmin)) {
    $db->query("UPDATE efactorproduct SET fan_lmin='$fan_lmin' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated fan_lmin';
} 
if (!is_null($fan_lmax)) {
    $db->query("UPDATE efactorproduct SET fan_lmax='$fan_lmax' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated fan_lmax';
} 
if (!is_null($fan_frequency)) {
    $db->query("UPDATE efactorproduct SET fan_frequency='$fan_frequency' WHERE node_id='$deviceid'");
 //   $response['status'] = 'successfully updated fan_frequency';
} 
if (!is_null($fan_pole_pairs)) {
    $db->query("UPDATE efactorproduct SET fan_pairs='$fan_pole_pairs' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated fan_pole_pairs';
} 
if (!is_null($fan_open_loop_current)) {
    $db->query("UPDATE efactorproduct SET fan_open_loop_current='$fan_open_loop_current' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated fan_open_loop_current';
} 
if (!is_null($fan_power_limit)) {
    $db->query("UPDATE efactorproduct SET fan_power_limit='$fan_power_limit' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated fan_power_limit';
} 
  if (!is_null($fan_torque_limit)) {
    $db->query("UPDATE efactorproduct SET fan_torque_limit='$fan_torque_limit' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated fan_torque_limit';
} 
if (!is_null($fan_speed1)) {
    $db->query("UPDATE efactorproduct SET fan_speed1='$fan_speed1' WHERE node_id='$deviceid'");
 //   $response['status'] = 'successfully updated fan_speed1';
} 
if (!is_null($fan_speed2)) {
    $db->query("UPDATE efactorproduct SET fan_speed2='$fan_speed2' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated fan_speed2';
} 
if (!is_null($fan_speed3)) {
    $db->query("UPDATE efactorproduct SET fan_speed3='$fan_speed3' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated fan_speed3';
} 
if (!is_null($fan_speed4)) {
    $db->query("UPDATE efactorproduct SET fan_speed4='$fan_speed4' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated fan_speed4';
} 
if (!is_null($fan_speed5)) {
    $db->query("UPDATE efactorproduct SET fan_speed5='$fan_speed5' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated fan_speed5';
} 
if (!is_null($fan_speed6)) {
    $db->query("UPDATE efactorproduct SET fan_speed6='$fan_speed6' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated fan_speed6';
} 
if (!is_null($fan_speed7)) {
    $db->query("UPDATE efactorproduct SET fan_speed7='$fan_speed7' WHERE node_id='$deviceid'");
 //   $response['status'] = 'successfully updated fan_speed7';
} 
if (!is_null($fan_button)) {
    $db->query("UPDATE efactorproduct SET fan_button='$fan_button' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated fan_button';
} 
if (!is_null($fan_buzzer)) {
    $fan_buzzer_value = $fan_buzzer == "1" ? 'true' : 'false';
    $db->query("UPDATE efactorproduct SET fan_buzzer='$fan_buzzer_value' WHERE node_id='$deviceid'");
 //   $response['status'] = 'successfully updated fan_buzzer';
}

} 


/*****************************mqtt***************************************/
/****************************mqtt***************************************/
if($addgateway != 'NULL')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';
$topic=$efactor."/".$things."/".$addgateway."/".$command;
//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;

if($payload == 'A1E70A')
{

//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;
      


   $mqtt->disconnect();
exit(json_encode($response));

       }

}

else{


//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
}


$response['payload']="";

$mqtt->disconnect();

exit(json_encode($response));


}
}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$deviceid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;


if($payload == 'A1E70A')
{


$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;
      


   $mqtt->disconnect();

exit(json_encode($response));


       }

}


else{



$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
}

$response['payload']="";


$mqtt->disconnect();

exit(json_encode($response));


}

}
/**********************************************************************/

/********************************mqtt_end*****************************************/
}

else if($device_type == 'light')

{


$json_data = json_decode($req, true);


if (is_array($json_data)) {
    $deviceid = $json_data['device_id'];

     $power = isset($json_data['power']) ? $json_data['power'] : null;
     $name = isset($json_data['name']) ? $json_data['name'] : null;
    $brightness = isset($json_data['brightness']) ? $json_data['brightness'] : null;
    $led_colour = isset($json_data['led colour']) ? $json_data['led colour'] : null;
    $baudrate = isset($json_data['Baud_Rate']) ? $json_data['Baud_Rate'] : null;
    $errorcode = isset($json_data['Error_Code']) ? $json_data['Error_Code'] : null;
    $comm_mode = isset($json_data['Communication_Mode']) ? $json_data['Communication_Mode'] : null;
    $uartmsgid = isset($json_data['Uart_Msg_Id']) ? $json_data['Uart_Msg_Id'] : null;
    $time = isset($json_data['Time']) ? $json_data['Time'] : null;
    $payload = isset($json_data['payload']) ? $json_data['payload'] : null;
    $latitude = isset($json_data['latitude']) ? $json_data['latitude'] : null;
     $longitude = isset($json_data['longitude']) ? $json_data['longitude'] : null;
    $power = isset($json_data['power']) && $json_data['power'] == "1" ? "true" : "false";
    

    // Update database based on conditions
    if (!is_null($power)) {
        $db->query("UPDATE efactorproduct SET power='$power' WHERE node_id='$deviceid'");
       // $response['status'] = 'successfully updated power';
    } 
    


    if (!is_null($latitude)) {
        $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 

    if (!is_null($longitude)) {
        $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
       // $response['status'] = 'successfully updated name';
    } 



    if (!is_null($name)) {
        $db->query("UPDATE efactorproduct SET name='$name' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 
    if (!is_null($brightness)) {
        $db->query("UPDATE efactorproduct SET brightness='$brightness' WHERE node_id='$deviceid'");
       // $response['status'] = 'successfully updated brightness';
    } 

    if (!is_null($baudrate)) {
        $db->query("UPDATE efactorproduct SET baudrate='$baudrate' WHERE node_id='$deviceid'");
       // $response['status'] = 'successfully updated baudrate';
    } 
    if (!is_null($errorcode)) {
        $db->query("UPDATE efactorproduct SET errorcode='$errorcode' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated errorcode';
    } 
    if (!is_null($comm_mode)) {
        $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated comm_mode';
    } 
    if (!is_null($uartmsgid)) {
        $db->query("UPDATE efactorproduct SET uartmsgid='$uartmsgid' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated uartmsgid';
    } 
    if (!is_null($time)) {
        $db->query("UPDATE efactorproduct SET time='$time' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated time';
    } 

    if (!is_null($led_colour)) {
        $db->query("UPDATE efactorproduct SET led_colour='$led_colour' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated led_colour';
    } 
   

if (!is_null($latitude)) {
    $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
   // $response['status'] = 'successfully updated latitude';
} 
if (!is_null($longitude)) {
    $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
   // $response['status'] = 'successfully updated longitude';
} 
else {
        // Handle unrecognized parameters or provide an error message
      //  $response['status'] = 'error: unrecognized parameters';
    }
} else {
    // Handle invalid input or provide an error message
   // $response['status'] = 'error: invalid input';
}











/*****************************mqtt***************************************/

/****************************mqtt***************************************/
if($addgateway != 'NULL')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';
$topic=$efactor."/".$things."/".$addgateway."/".$command;
//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;

if($payload == 'A1E70A')
{

//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;
      


   $mqtt->disconnect();
exit(json_encode($response));

       }

}

else{


//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
}


$response['payload']="";

$mqtt->disconnect();

exit(json_encode($response));


}
}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$deviceid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;


if($payload == 'A1E70A')
{


$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;
      


   $mqtt->disconnect();

exit(json_encode($response));


       }

}


else{



$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
}

$response['payload']="";


$mqtt->disconnect();

exit(json_encode($response));


}

}
/**********************************************************************/

/********************************mqtt_end*****************************************/
}


else if($device_type == 'switch')

{




$json_data = json_decode($req, true);


if (is_array($json_data)) {
    $deviceid = $json_data['device_id'];

     $power = isset($json_data['power']) ? $json_data['power'] : null;
     $name = isset($json_data['name']) ? $json_data['name'] : null;
    $brightness = isset($json_data['brightness']) ? $json_data['brightness'] : null;
    $baudrate = isset($json_data['Baud_Rate']) ? $json_data['Baud_Rate'] : null;
    $errorcode = isset($json_data['Error_Code']) ? $json_data['Error_Code'] : null;
    $comm_mode = isset($json_data['Communication_Mode']) ? $json_data['Communication_Mode'] : null;
    $uartmsgid = isset($json_data['Uart_Msg_Id']) ? $json_data['Uart_Msg_Id'] : null;
    $time = isset($json_data['Time']) ? $json_data['Time'] : null;
    $payload = isset($json_data['payload']) ? $json_data['payload'] : null;
    $latitude = isset($json_data['latitude']) ? $json_data['latitude'] : null;
     $longitude = isset($json_data['longitude']) ? $json_data['longitude'] : null;
    $power = isset($json_data['power']) && $json_data['power'] == "1" ? "true" : "false";
    

   




    // Update database based on conditions
    if (!is_null($power)) {
        $db->query("UPDATE efactorproduct SET power='$power' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated power';
    } 
    
    if (!is_null($latitude)) {
        $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 

    if (!is_null($longitude)) {
        $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 


    if (!is_null($name)) {
        $db->query("UPDATE efactorproduct SET name='$name' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated name';
    } 
    if (!is_null($brightness)) {
        $db->query("UPDATE efactorproduct SET brightness='$brightness' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated brightness';
    } 

    if (!is_null($baudrate)) {
        $db->query("UPDATE efactorproduct SET baudrate='$baudrate' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated baudrate';
    } 
    if (!is_null($errorcode)) {
        $db->query("UPDATE efactorproduct SET errorcode='$errorcode' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated errorcode';
    } 
    if (!is_null($comm_mode)) {
        $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated comm_mode';
    } 
    if (!is_null($uartmsgid)) {
        $db->query("UPDATE efactorproduct SET uartmsgid='$uartmsgid' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated uartmsgid';
    } 
    if (!is_null($time)) {
        $db->query("UPDATE efactorproduct SET time='$time' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated time';
    } 

if (!is_null($latitude)) {
    $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
   // $response['status'] = 'successfully updated latitude';
} 
if (!is_null($longitude)) {
    $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
   // $response['status'] = 'successfully updated longitude';
} 
else {
        // Handle unrecognized parameters or provide an error message
       // $response['status'] = 'error: unrecognized parameters';
    }
} else {
    // Handle invalid input or provide an error message
   // $response['status'] = 'error: invalid input';
}

/*****************************mqtt***************************************/

/****************************mqtt***************************************/
if($addgateway != 'NULL')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';
$topic=$efactor."/".$things."/".$addgateway."/".$command;
//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;

if($payload == 'A1E70A')
{

//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;
      


   $mqtt->disconnect();
exit(json_encode($response));

       }

}

else{


//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
}


$response['payload']="";

$mqtt->disconnect();

exit(json_encode($response));


}
}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$deviceid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;


if($payload == 'A1E70A')
{


$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;
      


   $mqtt->disconnect();

exit(json_encode($response));


       }

}


else{



$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
}

$response['payload']="";


$mqtt->disconnect();

exit(json_encode($response));


}

}
/**********************************************************************/

/********************************mqtt_end*****************************************/



}
/***************cooker*****************/
else if($device_type == 'cooker')

{



$json_data = json_decode($req, true);

if (is_array($json_data)) {
    $deviceid = $json_data['device_id'];
    $response = [];

    if (isset($json_data['power'])) {
        $power = $json_data['power'] == "1" ? "true" : "false";
        $db->query("UPDATE efactorproduct SET power='$power' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated power';
    }

    if (isset($json_data['name'])) {
        $name = $json_data['name'];
        $db->query("UPDATE efactorproduct SET name='$name' WHERE node_id='$deviceid'");
       // $response['status'] = 'successfully updated name';
    }

      if (isset($json_data['payload'])) {
        $payload = $json_data['payload'];
      //  $db->query("UPDATE efactorproduct SET payload='$payload' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated payload';
    }


    if (isset($json_data['recipe'])) {
        $recipe = $json_data['recipe'];
        $db->query("UPDATE efactorproduct SET recipe='$recipe' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated recipe';
    }

    if (isset($json_data['cookermode'])) {
        $cookermode = $json_data['cookermode'];
        $db->query("UPDATE efactorproduct SET cookermode='$cookermode' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated cookermode';
    }

    if (isset($json_data['Baud_Rate'])) {
        $baudrate = $json_data['Baud_Rate'];
        $db->query("UPDATE efactorproduct SET baudrate='$baudrate' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated baudrate';
    }

    if (isset($json_data['Error_Code'])) {
        $errorcode = $json_data['Error_Code'];
        $db->query("UPDATE efactorproduct SET errorcode='$errorcode' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated errorcode';
    }

    if (isset($json_data['Communication_Mode'])) {
        $comm_mode = $json_data['Communication_Mode'];
        $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated comm_mode';
    }

    if (isset($json_data['Uart_Msg_Id'])) {
        $uartmsgid = $json_data['Uart_Msg_Id'];
        $db->query("UPDATE efactorproduct SET uartmsgid='$uartmsgid' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated uartmsgid';
    }

    if (isset($json_data['Time'])) {
        $time = $json_data['Time'];
        $db->query("UPDATE efactorproduct SET time='$time' WHERE node_id='$deviceid'");
    //    $response['status'] = 'successfully updated time';
    }

        if (isset($json_data['latitude'])) {
        $latitude = $json_data['latitude'];
        $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
    //    $response['status'] = 'successfully updated latitude';
    }

    if (isset($json_data['longitude'])) {
        $longitude = $json_data['longitude'];
        $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated longitude';
    }

    // Configuration parameters
    if (isset($json_data['Cooker Timer'])) {
    $cooker_timer = $json_data['Cooker Timer'];
    $db->query("UPDATE efactorproduct SET cooker_timer_1='$cooker_timer' WHERE node_id='$deviceid'");
   // $response['status'] = 'successfully updated cooker_timer';
} else if (isset($json_data['Cooking temperature'])) {
    $Cooking_temperature = $json_data['Cooking temperature'];
    $db->query("UPDATE efactorproduct SET cooker_max_temperature_1='$Cooking_temperature' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated cooker_max_temperature';
} else if (isset($json_data['Keep warm temperature'])) {
    $Keep_warm_temperature = $json_data['Keep warm temperature'];
    $db->query("UPDATE efactorproduct SET Keep_warm_1='$Keep_warm_temperature' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated Keep_warm';
} else if (isset($json_data['Pre-timer temp constant mode'])) {
    $Pre_timer_constant_mode = $json_data['Pre-timer temp constant mode'];
    $db->query("UPDATE efactorproduct SET Pre_timer_constant_mode='$Pre_timer_constant_mode' WHERE node_id='$deviceid'");
 //   $response['status'] = 'successfully updated Pre_timer_constant_mode';
} else if (isset($json_data['Pre-timer'])) {
    $Pre_timer = $json_data['Pre-timer'];
    $db->query("UPDATE efactorproduct SET Pre_timer='$Pre_timer' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated Pre_timer';
} else if (isset($json_data['Pre – temperature'])) {
    $Pre_temperature = $json_data['Pre – temperature'];
    $db->query("UPDATE efactorproduct SET Pre_temperature='$Pre_temperature' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated Pre_temperature';
} else if (isset($json_data['Coooker max-temperature'])) {
    $Coooker_max_temperature = $json_data['Coooker max-temperature'];
    $db->query("UPDATE efactorproduct SET Coooker_max_temperature='$Coooker_max_temperature' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated Coooker_max_temperature';
} else if (isset($json_data['Cooker temp constant mode'])) {
    $Cooker_constant_mode = $json_data['Cooker temp constant mode'];
    $db->query("UPDATE efactorproduct SET Cooker_constant_mode='$Cooker_constant_mode' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated Cooker_constant_mode';
} else if (isset($json_data['Keep Warm timer'])) {
    $Keep_Warm_timer = $json_data['Keep Warm timer'];
    $db->query("UPDATE efactorproduct SET Keep_Warm_timer_1='$Keep_Warm_timer' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated Keep_Warm_timer';
} else if (isset($json_data['Dishes'])) {
    $Dishes = $json_data['Dishes'];
    $db->query("UPDATE efactorproduct SET Dish='$Dishes' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated Dishes';
} else if (isset($json_data['pre-timer wait temperature constant mode'])) {
    $pre_timer_wait_temperature_constant_mode = $json_data['pre-timer wait temperature constant mode'];
    $db->query("UPDATE efactorproduct SET pre_timer_wait_temperature_constant_mode='$pre_timer_wait_temperature_constant_mode' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated pre_timer_wait_temperature_constant_mode';
} else if (isset($json_data['pre-timer waiting time'])) {
    $pre_timer_waiting_time = $json_data['pre-timer waiting time'];
    $db->query("UPDATE efactorproduct SET pre_timer_waiting_time='$pre_timer_waiting_time' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated pre_timer_waiting_time';
} else if (isset($json_data['pre-timer Waiting temperature'])) {
    $pre_timer_Waiting_temperature = $json_data['pre-timer Waiting temperature'];
    $db->query("UPDATE efactorproduct SET pre_timer_Waiting_temperature='$pre_timer_Waiting_temperature' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated pre_timer_Waiting_temperature';
} else if (isset($json_data['Keep warm temp constant mode'])) {
    $keep_warm_temp_constant_mode = $json_data['Keep warm temp constant mode'];
    $db->query("UPDATE efactorproduct SET keep_warm_temp_constant_mode='$keep_warm_temp_constant_mode' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated keep_warm_temp_constant_mode';
} else if (isset($json_data['cook wait temp constant mode'])) {
    $cook_wait_temp_constant_mode = $json_data['cook wait temp constant mode'];
    $db->query("UPDATE efactorproduct SET cook_wait_temp_constant_mode='$cook_wait_temp_constant_mode' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated cook_wait_temp_constant_mode';
} else if (isset($json_data['cook waiting time'])) {
    $cook_waiting_time = $json_data['cook waiting time'];
    $db->query("UPDATE efactorproduct SET cook_waiting_time='$cook_waiting_time' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated cook_waiting_time';
} else if (isset($json_data['cook Waiting- temperature'])) {
    $cook_Waiting_temperature = $json_data['cook Waiting- temperature'];
    $db->query("UPDATE efactorproduct SET cook_Waiting_temperature='$cook_Waiting_temperature' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated cook_Waiting_temperature';
} else if (isset($json_data['warm-wait temp constant mode'])) {
    $warm_wait_temp_constant_mode = $json_data['warm-wait temp constant mode'];
    $db->query("UPDATE efactorproduct SET warm_wait_temp_constant_mode='$warm_wait_temp_constant_mode' WHERE node_id='$deviceid'");
 //   $response['status'] = 'successfully updated warm_wait_temp_constant_mode';
} else if (isset($json_data['warm waiting time'])) {
    $warm_waiting_time = $json_data['warm waiting time'];
    $db->query("UPDATE efactorproduct SET warm_waiting_time='$warm_waiting_time' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated warm_waiting_time';
} else if (isset($json_data['warm Waiting temperature'])) {
    $warm_Waiting_temperature = $json_data['warm Waiting temperature'];
    $db->query("UPDATE efactorproduct SET warm_Waiting_temperature='$warm_Waiting_temperature' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated warm_Waiting_temperature';
} else if (isset($json_data['OK'])) {
    $cooker_ok_button = $json_data['OK'];
    $db->query("UPDATE efactorproduct SET cooker_ok_button='$cooker_ok_button' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated cooker_ok_button';
} else if (isset($json_data['Reload Default'])) {
    $reload_default = $json_data['Reload Default'];
    $db->query("UPDATE efactorproduct SET reload_default='$reload_default' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated reload_default';
} 
} 

/*****************************mqtt***************************************/

/****************************mqtt***************************************/
if($addgateway != 'NULL')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';
$topic=$efactor."/".$things."/".$addgateway."/".$command;
//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;

if($payload == 'A1E70A')
{

//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;
      


   $mqtt->disconnect();
exit(json_encode($response));

       }

}

else{


//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
}


$response['payload']="";

$mqtt->disconnect();

exit(json_encode($response));


}
}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$deviceid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;


if($payload == 'A1E70A')
{


$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;
      


   $mqtt->disconnect();

exit(json_encode($response));


       }

}


else{



$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
}

$response['payload']="";


$mqtt->disconnect();

exit(json_encode($response));


}

}
/**********************************************************************/

/********************************mqtt_end*****************************************/



}



/************************outlet********************/
else if($device_type == 'outlet')

{


$json_data = json_decode($req, true);


if (is_array($json_data)) {
    $deviceid = $json_data['device_id'];

     $power = isset($json_data['power']) ? $json_data['power'] : null;
     $name = isset($json_data['name']) ? $json_data['name'] : null;
    $baudrate = isset($json_data['Baud_Rate']) ? $json_data['Baud_Rate'] : null;
    $errorcode = isset($json_data['Error_Code']) ? $json_data['Error_Code'] : null;
    $comm_mode = isset($json_data['Communication_Mode']) ? $json_data['Communication_Mode'] : null;
    $uartmsgid = isset($json_data['Uart_Msg_Id']) ? $json_data['Uart_Msg_Id'] : null;
    $time = isset($json_data['Time']) ? $json_data['Time'] : null;
    $payload = isset($json_data['payload']) ? $json_data['payload'] : null;
    $latitude = isset($json_data['latitude']) ? $json_data['latitude'] : null;
     $longitude = isset($json_data['longitude']) ? $json_data['longitude'] : null;
    $power = isset($json_data['power']) && $json_data['power'] == "1" ? "true" : "false";
    

   




    // Update database based on conditions
    if (!is_null($power)) {
        $db->query("UPDATE efactorproduct SET power='$power' WHERE node_id='$deviceid'");
       // $response['status'] = 'successfully updated power';
    } 
    
    if (!is_null($latitude)) {
        $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 

    if (!is_null($longitude)) {
        $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 

    if (!is_null($name)) {
        $db->query("UPDATE efactorproduct SET name='$name' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 

    if (!is_null($baudrate)) {
        $db->query("UPDATE efactorproduct SET baudrate='$baudrate' WHERE node_id='$deviceid'");
       // $response['status'] = 'successfully updated baudrate';
    } 
    if (!is_null($errorcode)) {
        $db->query("UPDATE efactorproduct SET errorcode='$errorcode' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated errorcode';
    } 
    if (!is_null($comm_mode)) {
        $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated comm_mode';
    } 
    if (!is_null($uartmsgid)) {
        $db->query("UPDATE efactorproduct SET uartmsgid='$uartmsgid' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated uartmsgid';
    } 
    if (!is_null($time)) {
        $db->query("UPDATE efactorproduct SET time='$time' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated time';
    } 

if (!is_null($latitude)) {
    $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
   // $response['status'] = 'successfully updated latitude';
} 
if (!is_null($longitude)) {
    $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
   // $response['status'] = 'successfully updated longitude';
} 
else {
        // Handle unrecognized parameters or provide an error message
    //    $response['status'] = 'error: unrecognized parameters';
    }
} else {
    // Handle invalid input or provide an error message
  //  $response['status'] = 'error: invalid input';
}










/*****************************mqtt***************************************/

/****************************mqtt***************************************/
if($addgateway != 'NULL')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';
$topic=$efactor."/".$things."/".$addgateway."/".$command;
//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;

if($payload == 'A1E70A')
{

//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;
      


   $mqtt->disconnect();
exit(json_encode($response));

       }

}

else{


//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
}


$response['payload']="";

$mqtt->disconnect();

exit(json_encode($response));


}
}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$deviceid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;


if($payload == 'A1E70A')
{


$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;
      


   $mqtt->disconnect();

exit(json_encode($response));


       }

}


else{



$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
}

$response['payload']="";


$mqtt->disconnect();

exit(json_encode($response));


}

}
/**********************************************************************/

/********************************mqtt_end*****************************************/

}



/*******************outlet end**************/


/*************************HBMDNC**************************/

else if($device_type == 'HBMDNC')

{


$json_data = json_decode($req, true);


if (is_array($json_data)) {
    $deviceid = $json_data['device_id'];

    
     $name = isset($json_data['name']) ? $json_data['name'] : null;
    $baudrate = isset($json_data['Baud_Rate']) ? $json_data['Baud_Rate'] : null;
    $errorcode = isset($json_data['Error_Code']) ? $json_data['Error_Code'] : null;
    $comm_mode = isset($json_data['Communication_Mode']) ? $json_data['Communication_Mode'] : null;
    $uartmsgid = isset($json_data['Uart_Msg_Id']) ? $json_data['Uart_Msg_Id'] : null;
    $time = isset($json_data['Time']) ? $json_data['Time'] : null;
    $payload = isset($json_data['payload']) ? $json_data['payload'] : null;
    $latitude = isset($json_data['latitude']) ? $json_data['latitude'] : null;
     $longitude = isset($json_data['longitude']) ? $json_data['longitude'] : null;
    $power = isset($json_data['power']) && $json_data['power'] == "1" ? "true" : "false";
    

   

 $doctor = isset($json_data['Doctor']) ? $json_data['Doctor'] : null;
 $personal_assiatant = isset($json_data['Personal Assist']) ? $json_data['Personal Assist'] : null;
 $nurse = isset($json_data['nurse']) ? $json_data['nurse'] : null;
 $doctor_response = isset($json_data['Doctor Response']) ? $json_data['Doctor Response'] : null;
 $personal_assistant_resp = isset($json_data['Personal Assist response']) ? $json_data['Personal Assist response'] : null;
 $nurse_response = isset($json_data['nurse response']) ? $json_data['nurse response'] : null;





    // Update database based on conditions
    if (!is_null($power)) {
        $db->query("UPDATE efactorproduct SET power='$power' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated power';
    } 
    

/*----------------------------------------------*/


if (!is_null($doctor)) {
        $db->query("UPDATE efactorproduct SET doctor='$doctor' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated doctor';
    } 
    if (!is_null($personal_assiatant)) {
        $db->query("UPDATE efactorproduct SET personal_assiatant='$personal_assiatant' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated personal_assiatant';
    } 
if (!is_null($nurse)) {
        $db->query("UPDATE efactorproduct SET nurse='$nurse' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated nurse';
    } 
    if (!is_null($doctor_response)) {
        $db->query("UPDATE efactorproduct SET doctor_response='$doctor_response' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated doctor_response';
    } 
if (!is_null($personal_assistant_resp)) {
        $db->query("UPDATE efactorproduct SET personal_assistant_resp='$personal_assistant_resp' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated personal_assistant_resp';
    } 
    if (!is_null($nurse_response)) {
        $db->query("UPDATE efactorproduct SET nurse_response='$nurse_response' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated nurse_response';
    } 


/*----------------------------------------------*/

    if (!is_null($latitude)) {
        $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 

    if (!is_null($longitude)) {
        $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated name';
    } 

    if (!is_null($name)) {
        $db->query("UPDATE efactorproduct SET name='$name' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated name';
    } 

    if (!is_null($baudrate)) {
        $db->query("UPDATE efactorproduct SET baudrate='$baudrate' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated baudrate';
    } 
    if (!is_null($errorcode)) {
        $db->query("UPDATE efactorproduct SET errorcode='$errorcode' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated errorcode';
    } 
    if (!is_null($comm_mode)) {
        $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated comm_mode';
    } 
    if (!is_null($uartmsgid)) {
        $db->query("UPDATE efactorproduct SET uartmsgid='$uartmsgid' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated uartmsgid';
    } 
    if (!is_null($time)) {
        $db->query("UPDATE efactorproduct SET time='$time' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated time';
    } 

if (!is_null($latitude)) {
    $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
    //$response['status'] = 'successfully updated latitude';
} 
if (!is_null($longitude)) {
    $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
    //$response['status'] = 'successfully updated longitude';
} 
else {
        // Handle unrecognized parameters or provide an error message
     //   $response['status'] = 'error: unrecognized parameters';
    }
} else {
    // Handle invalid input or provide an error message
   // $response['status'] = 'error: invalid input';
}










/*****************************mqtt***************************************/

/****************************mqtt***************************************/
if($addgateway != 'NULL')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';
$topic=$efactor."/".$things."/".$addgateway."/".$command;
//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;

if($payload == 'A1E70A')
{

//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;
      


   $mqtt->disconnect();
exit(json_encode($response));

       }

}

else{


//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
}


$response['payload']="";

$mqtt->disconnect();

exit(json_encode($response));


}
}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$deviceid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;


if($payload == 'A1E70A')
{


$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;
      


   $mqtt->disconnect();

exit(json_encode($response));


       }

}


else{



$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
}

$response['payload']="";


$mqtt->disconnect();

exit(json_encode($response));


}

}
/**********************************************************************/
/********************************mqtt_end*****************************************/
}






/*************************HBMDNC END**************************/



/************************swpcp********************/
else if($device_type == 'swpcp')

{



$json_data = json_decode($req, true);


if (is_array($json_data)) {
    $deviceid = $json_data['device_id'];

     $power = isset($json_data['Speed']) ? $json_data['power'] : null;
    $name = isset($json_data['name']) ? $json_data['name'] : null;
    $payload = isset($json_data['payload']) ? $json_data['payload'] : null;
    $baudrate = isset($json_data['Baud_Rate']) ? $json_data['Baud_Rate'] : null;
    $errorcode = isset($json_data['Error_Code']) ? $json_data['Error_Code'] : null;
    $comm_mode = isset($json_data['Communication_Mode']) ? $json_data['Communication_Mode'] : null;
    $uartmsgid = isset($json_data['Uart_Msg_Id']) ? $json_data['Uart_Msg_Id'] : null;
    $time = isset($json_data['Time']) ? $json_data['Time'] : null;
    

    $mac = isset($json_data['mac']) ? $json_data['mac'] : null;
$module_type = isset($json_data['module_type']) ? $json_data['module_type'] : null;
$device_name = isset($json_data['device_name']) ? $json_data['device_name'] : null;
$latitude = isset($json_data['latitude']) ? $json_data['latitude'] : null;
$longitude = isset($json_data['longitude']) ? $json_data[' longitude'] : null;
$timer = isset($json_data['timer']) ? $json_data['timer'] : null;
$starter_relay_off_timer = isset($json_data['starter_relay_off_timer']) ? $json_data['starter_relay_off_timer'] : null;
$start_high_current_cutoff = isset($json_data['start_high_current_cutoff']) ? $json_data['start_high_current_cutoff'] : null;
$start_low_current_cutoff = isset($json_data['start_low_current_cutoff']) ? $json_data['start_low_current_cutoff'] : null;
$Run_high_current_cutoff = isset($json_data['Run_high_current_cutoff']) ? $json_data['Run_high_current_cutoff'] : null;
$Run_low_current_cutoff = isset($json_data['Run_low_current_cutoff']) ? $json_data['Run_low_current_cutoff'] : null;
$high_voltage_cutoff = isset($json_data['high_voltage_cutoff']) ? $json_data['high_voltage_cutoff'] : null;
$low_voltage_cutoff = isset($json_data['low_voltage_cutoff']) ? $json_data['low_voltage_cutoff'] : null;
$ultra_high_voltage_cutoff = isset($json_data['ultra_high_voltage_cutoff']) ? $json_data['ultra_high_voltage_cutoff'] : null;
$ultra_high_current_cutoff = isset($json_data['ultra_high_current_cutoff']) ? $json_data['ultra_high_current_cutoff'] : null;
$USER_SET_MAX_CURRENT_CUTOFF_LIMIT = isset($json_data['USER_SET_MAX_CURRENT_CUTOFF_LIMIT']) ?      
 $json_data['USER_SET_MAX_CURRENT_CUTOFF_LIMIT'] : null;
$USER_AND_DEVICE_MAX_CURRENT_LIMIT = isset($json_data['USER_AND_DEVICE_MAX_CURRENT_LIMIT']) ?     
 $json_data['USER_AND_DEVICE_MAX_CURRENT_LIMIT'] : null;
$LOCK_ENABLE_DISABLE = isset($json_data['LOCK_ENABLE_DISABLE']) ? $json_data['LOCK_ENABLE_DISABLE'] : null;
 $power = isset($json_data['power']) && $json_data['power'] == "1" ? "true" : "false";



    // Update database based on conditions
    if (!is_null($power)) {
        $db->query("UPDATE efactorproduct SET power='$power' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated power';
    } 
    
   if (!is_null($latitude)) {
        $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 

    if (!is_null($longitude)) {
        $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 

    if (!is_null($name)) {
        $db->query("UPDATE efactorproduct SET name='$name' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 
 
    if (!is_null($baudrate)) {
        $db->query("UPDATE efactorproduct SET baudrate='$baudrate' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated baudrate';
    } 
    if (!is_null($errorcode)) {
        $db->query("UPDATE efactorproduct SET errorcode='$errorcode' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated errorcode';
    } 
    if (!is_null($comm_mode)) {
        $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated comm_mode';
    } 
    if (!is_null($uartmsgid)) {
        $db->query("UPDATE efactorproduct SET uartmsgid='$uartmsgid' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated uartmsgid';
    } 
    if (!is_null($time)) {
        $db->query("UPDATE efactorproduct SET time='$time' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated time';
    } 

    
    if (!is_null($mac)) {


        $db->query("UPDATE efactorproduct SET mac='$mac' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated mac';
    } 
    if (!is_null($module_type)) {
        $db->query("UPDATE efactorproduct SET module_type='$module_type' WHERE node_id='$deviceid'");
    //    $response['status'] = 'successfully updated module_type';
    } 

if (!is_null($device_name)) {
    $db->query("UPDATE efactorproduct SET device_name='$device_name' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated device_name';
} 
if (!is_null($latitude)) {
    $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
   // $response['status'] = 'successfully updated latitude';
} 
if (!is_null($longitude)) {
    $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
   // $response['status'] = 'successfully updated longitude';
} 
if (!is_null($timer)) {
    $db->query("UPDATE efactorproduct SET timer='$timer' WHERE node_id='$deviceid'");
   // $response['status'] = 'successfully updated timer';
} 
if (!is_null($starter_relay_off_timer)) {
    $db->query("UPDATE efactorproduct SET starter_relay_off_timer='$starter_relay_off_timer' WHERE node_id='$deviceid'");
   // $response['status'] = 'successfully updated starter_relay_off_timer';
} 
if (!is_null($start_high_current_cutoff)) {
    $db->query("UPDATE efactorproduct SET start_high_current_cutoff='$start_high_current_cutoff' WHERE node_id='$deviceid'");
   // $response['status'] = 'successfully updated start_high_current_cutoff';
} 
if (!is_null($start_low_current_cutoff)) {
    $db->query("UPDATE efactorproduct SET start_low_current_cutoff='$start_low_current_cutoff' WHERE node_id='$deviceid'");
   // $response['status'] = 'successfully updated start_low_current_cutoff';
} 
  if (!is_null($Run_high_current_cutoff)) {
    $db->query("UPDATE efactorproduct SET Run_high_current_cutoff='$Run_high_current_cutoff' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated Run_high_current_cutoff';
} 
if (!is_null($Run_low_current_cutoff)) {
    $db->query("UPDATE efactorproduct SET Run_low_current_cutoff='$Run_low_current_cutoff' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated Run_low_current_cutoff';
} 
if (!is_null($high_voltage_cutoff)) {
    $db->query("UPDATE efactorproduct SET high_voltage_cutoff='$high_voltage_cutoff' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated high_voltage_cuttoff';
} 
if (!is_null($low_voltage_cutoff)) {
    $db->query("UPDATE efactorproduct SET low_voltage_cutoff='$low_voltage_cutoff' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated low_voltage_cutoff';
} 
if (!is_null($ultra_high_voltage_cutoff)) {
    $db->query("UPDATE efactorproduct SET ultra_high_voltage_cutoff='$ultra_high_voltage_cutoff' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated ultra_high_voltage_cutoff';
} 
if (!is_null($ultra_high_current_cutoff)) {
    $db->query("UPDATE efactorproduct SET ultra_high_current_cutoff='$ultra_high_current_cutoff' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated ultra_high_current_cutoff';
} 
if (!is_null($USER_SET_MAX_CURRENT_CUTOFF_LIMIT)) {
    $db->query("UPDATE efactorproduct SET USER_SET_MAX_CURRENT_CUTOFF_LIMIT='$USER_SET_MAX_CURRENT_CUTOFF_LIMIT' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated fan_speed6';
} 
if (!is_null($USER_AND_DEVICE_MAX_CURRENT_LIMIT)) {
    $db->query("UPDATE efactorproduct SET USER_AND_DEVICE_MAX_CURRENT_LIMIT='$USER_AND_DEVICE_MAX_CURRENT_LIMIT' WHERE node_id='$deviceid'");
 //   $response['status'] = 'successfully updated USER_AND_DEVICE_MAX_CURRENT_LIMIT';
} 
if (!is_null($LOCK_ENABLE_DISABLE)) {
    $db->query("UPDATE efactorproduct SET LOCK_ENABLE_DISABLE='$LOCK_ENABLE_DISABLE' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated LOCK_ENABLE_DISABLE';
} 
else {
        // Handle unrecognized parameters or provide an error message
     //   $response['status'] = 'error: unrecognized parameters';
    }
} else {
    // Handle invalid input or provide an error message
  //  $response['status'] = 'error: invalid input';
}

/*****************************mqtt***************************************/

/****************************mqtt***************************************/
if($addgateway != 'NULL')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';
$topic=$efactor."/".$things."/".$addgateway."/".$command;
//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;

if($payload == 'A1E70A')
{

//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;
      


   $mqtt->disconnect();
exit(json_encode($response));

       }

}

else{


//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
}


$response['payload']="";

$mqtt->disconnect();

exit(json_encode($response));


}
}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$deviceid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;


if($payload == 'A1E70A')
{


$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;
      


   $mqtt->disconnect();

exit(json_encode($response));


       }

}


else{



$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
}

$response['payload']="";


$mqtt->disconnect();

exit(json_encode($response));


}

}
/**********************************************************************/

/********************************mqtt_end*****************************************/
}



/**********************swpcp-end**************/
/*******************exhaust hood start*************/
else if($device_type == 'exhaust_hood')

{



$json_data = json_decode($req, true);


if (is_array($json_data)) {
    $deviceid = $json_data['device_id'];

     $power = isset($json_data['power']) ? $json_data['power'] : null;
     $name = isset($json_data['name']) ? $json_data['name'] : null;
    $mac = isset($json_data['mac']) ? $json_data['mac'] : null;
    $module_type = isset($json_data['module_type']) ? $json_data['module_type'] : null;
    $baudrate = isset($json_data['Baud_Rate']) ? $json_data['Baud_Rate'] : null;
    $errorcode = isset($json_data['Error_Code']) ? $json_data['Error_Code'] : null;
    $comm_mode = isset($json_data['Communication_Mode']) ? $json_data['Communication_Mode'] : null;
    $uartmsgid = isset($json_data['Uart_Msg_Id']) ? $json_data['Uart_Msg_Id'] : null;
    $time = isset($json_data['Time']) ? $json_data['Time'] : null;
    $payload = isset($json_data['payload']) ? $json_data['payload'] : null;
    $latitude = isset($json_data['latitude']) ? $json_data['latitude'] : null;
     $longitude = isset($json_data['longitude']) ? $json_data['longitude'] : null;
    $device_name = isset($json_data['device_name']) ? $json_data['device_name'] : null;
     $autoclean = isset($json_data['autoclean']) ? $json_data['autoclean'] : null;
    $airflow = isset($json_data['airflow']) ? $json_data['airflow'] : null;
    $lamp = isset($json_data['lamp']) ? $json_data['lamp'] : null;
     $power = isset($json_data['power']) && $json_data['power'] == "1" ? "true" : "false";

   




    // Update database based on conditions
    if (!is_null($power)) {
        $db->query("UPDATE efactorproduct SET power='$power' WHERE node_id='$deviceid'");
        //$response['status'] = 'successfully updated power';
    } 
   
    if (!is_null($latitude)) {
        $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
        //$response['status'] = 'successfully updated name';
    } 

    if (!is_null($longitude)) {
        $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
        //$response['status'] = 'successfully updated name';
    } 

    if (!is_null($name)) {
        $db->query("UPDATE efactorproduct SET name='$name' WHERE node_id='$deviceid'");
        //$response['status'] = 'successfully updated name';
    } 
    if (!is_null($lamp)) {
        $db->query("UPDATE efactorproduct SET lamp='$lamp' WHERE node_id='$deviceid'");
        //$response['status'] = 'successfully updated lamp';
    } 

    if (!is_null($baudrate)) {
        $db->query("UPDATE efactorproduct SET baudrate='$baudrate' WHERE node_id='$deviceid'");
        //$response['status'] = 'successfully updated baudrate';
    } 
    if (!is_null($errorcode)) {
        $db->query("UPDATE efactorproduct SET errorcode='$errorcode' WHERE node_id='$deviceid'");
        //$response['status'] = 'successfully updated errorcode';
    } 
    if (!is_null($comm_mode)) {
        $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");
        //$response['status'] = 'successfully updated comm_mode';
    } 
    if (!is_null($uartmsgid)) {
        $db->query("UPDATE efactorproduct SET uartmsgid='$uartmsgid' WHERE node_id='$deviceid'");
        //$response['status'] = 'successfully updated uartmsgid';
    } 
    if (!is_null($time)) {
        $db->query("UPDATE efactorproduct SET time='$time' WHERE node_id='$deviceid'");
        //$response['status'] = 'successfully updated time';
    } 

    if (!is_null($airflow)) {
        $db->query("UPDATE efactorproduct SET airflow='$airflow' WHERE node_id='$deviceid'");
        //$response['status'] = 'successfully updated airflow';
    } 
   

if (!is_null($mac)) {
    $db->query("UPDATE efactorproduct SET mac='$mac' WHERE node_id='$deviceid'");
    //$response['status'] = 'successfully updated mac';
} 
if (!is_null($module_type)) {
    $db->query("UPDATE efactorproduct SET module_type='$module_type' WHERE node_id='$deviceid'");
    //$response['status'] = 'successfully updated module_type';
} 
if (!is_null($device_name)) {
        $db->query("UPDATE efactorproduct SET device_name='$device_name' WHERE node_id='$deviceid'");
       // $response['status'] = 'successfully updated device_name';
    } 

    if (!is_null($autoclean)) {
        $db->query("UPDATE efactorproduct SET autoclean='$autoclean' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated autoclean';
    } 
   

if (!is_null($latitude)) {
    $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated latitude';
} 
if (!is_null($longitude)) {
    $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
  //  $response['status'] = 'successfully updated longitude';
} 
else {
        // Handle unrecognized parameters or provide an error message
    //    $response['status'] = 'error: unrecognized parameters';
    }
} else {
    // Handle invalid input or provide an error message
   // $response['status'] = 'error: invalid input';
}

/**************************mqtt*******************************/

/****************************mqtt***************************************/
if($addgateway != 'NULL')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';
$topic=$efactor."/".$things."/".$addgateway."/".$command;
//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;

if($payload == 'A1E70A')
{

//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;
      


   $mqtt->disconnect();
exit(json_encode($response));

       }

}

else{


//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
}


$response['payload']="";

$mqtt->disconnect();

exit(json_encode($response));


}
}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$deviceid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;


if($payload == 'A1E70A')
{


$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;
      


   $mqtt->disconnect();

exit(json_encode($response));


       }

}


else{



$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
}

$response['payload']="";


$mqtt->disconnect();

exit(json_encode($response));


}

}
/**********************************************************************/

/*************************************************************/









}




/**************************************************/
else if($device_type == 'Gateway')

{



$query = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$doctors = $db->get_results($query); 
//$num_dev = $db->num_rows($query);
$row = $db->get_row($query); 
$payload_scan=$row['payload'];


$json_data = json_decode($req, true);


if (is_array($json_data)) {
    $deviceid = $json_data['device_id'];

    // $power = isset($json_data['power']) ? $json_data['power'] : null;
     $name = isset($json_data['name']) ? $json_data['name'] : null;
    $baudrate = isset($json_data['Baud_Rate']) ? $json_data['Baud_Rate'] : null;
    $errorcode = isset($json_data['Error_Code']) ? $json_data['Error_Code'] : null;
    $comm_mode = isset($json_data['Communication_Mode']) ? $json_data['Communication_Mode'] : null;
    $uartmsgid = isset($json_data['Uart_Msg_Id']) ? $json_data['Uart_Msg_Id'] : null;
    $time = isset($json_data['Time']) ? $json_data['Time'] : null;
    $payload = isset($json_data['payload']) ? $json_data['payload'] : null;
    $power = isset($json_data['power']) && $json_data['power'] == "1" ? "true" : "false";
    $scan = isset($json_data['SCAN']) ? $json_data['SCAN'] : null;
    

    // Update database based on conditions
    if (!is_null($power)) {
        $db->query("UPDATE efactorproduct SET power='$power' WHERE node_id='$deviceid'");
       // $response['status'] = 'successfully updated power';
    } 
    if (!is_null($payload)) {
      //  $db->query("UPDATE efactorproduct SET payload='$payload' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated payload';
    } 

    if (!is_null($latitude)) {
        $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 

    if (!is_null($longitude)) {
        $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 

    if (!is_null($name)) {
        $db->query("UPDATE efactorproduct SET name='$name' WHERE node_id='$deviceid'");
      //  $response['status'] = 'successfully updated name';
    } 

    if (!is_null($baudrate)) {
        $db->query("UPDATE efactorproduct SET baudrate='$baudrate' WHERE node_id='$deviceid'");
       // $response['status'] = 'successfully updated baudrate';
    } 
    if (!is_null($errorcode)) {
        $db->query("UPDATE efactorproduct SET errorcode='$errorcode' WHERE node_id='$deviceid'");
       // $response['status'] = 'successfully updated errorcode';
    } 
    if (!is_null($comm_mode)) {
        $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");
       // $response['status'] = 'successfully updated comm_mode';
    } 

if (!is_null($scan)) {
       // $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");

         if($payload_scan != 'stop')
           {

              $response['payload'] = 'wait';

            }
        else{
        $response['payload'] = $payload_scan;
       }

      //    $response['status'] = 'wait';

    } 


    if (!is_null($uartmsgid)) {
        $db->query("UPDATE efactorproduct SET uartmsgid='$uartmsgid' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated uartmsgid';
    } 
    if (!is_null($time)) {
        $db->query("UPDATE efactorproduct SET time='$time' WHERE node_id='$deviceid'");
     //   $response['status'] = 'successfully updated time';
    } 

    



} 

echo json_encode($response);
/*****************************mqtt***************************************/

if($addgateway != 'NULL')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
$command='command';
$topic=$efactor."/".$things."/".$addgateway."/".$command;

if($payload == 'A1E70A')
{

$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);

$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];

  $response['payload']=$payload;
  
   $mqtt->disconnect();

       }

}

else{

$response['payload']="";

}
}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$deviceid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;


if($payload == 'A1E70A')
{


//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
//$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            $mqtt->publish($topic, $mqtt_json_1);
usleep(200000);
//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;
      


   $mqtt->disconnect();

       }

}


else{

$response['payload']="";

}

}

/********************************mqtt_end*****************************************/
}

else

{

//echo "no device found";

header('HTTP/1.1 400');
        $response['status']='failure';
        $response['error_code']='101112';
        $response['description']='no device found';


}


}




}
/**************************************************************************************/



if (isset($_GET['action']) && $_GET['action']=='device_current_page') {

//$nodeid =$_GET['node_id'];
//$deviceid=$_GET['device_id'];

$header = apache_request_headers();  
foreach ($header as $key => $val) {


if (is_array($json_data) && isset($json_data['device_id'])) {
    $deviceid = $json_data['device_id'];
   
}


if (is_array($json_data) && isset($json_data['node_id'])) {
    $nodeid = $json_data['node_id'];
   
}



if (is_array($json_data) && isset($json_data['page_name'])) {
    $pagename = $json_data['page_name'];
   
}





$db->query("update efactorproduct set page_name='$pagename' where node_id='$nodeid'");




$query_result = "SELECT * FROM efactorproduct WHERE node_id='$nodeid'";  
$result = $db->get_results($query_result); 
$row_result = $db->get_row($query_result); // Use correct query variable

if ($row_result) {
    $device_type = $row_result['device_type'];
    $model_name = $row_result['model_name'];
   // echo $device_type;

    // Make sure $device_type is valid and then update
    if ($model_name) {
        $db->query("UPDATE `$model_name` SET page_names='$pagename' WHERE node_id='$nodeid'");
    } else {
        $db->query("UPDATE efactorproduct SET page_name='$pagename' WHERE node_id='$nodeid'");
    }
} else {
    echo "No data found for node_id: $nodeid";
}


$response['status']='success';



}


}


/**************************************************************************************/
/*--------------------------------auto_get_nodes_params-------------------------------*/

if (isset($_GET['action']) && $_GET['action']=='user_get_nodes_params_test') {

$get_nodes='{"Fan":{"Direction":0,"led_colour":6,"output":true,"power":true,"Speed":3,"led":true}}';

echo $get_nodes;





}






if (isset($_GET['action']) && $_GET['action']=='auto_get_nodes_params_prev') {

// $nodeid ="FFFFFFFFFFFFFFFF8200000000004200";
// $deviceid="FFFFFFFFFFFFFFFF8200000000004200";

$nodeid =$_GET['node_id'];
$deviceid=$_GET['device_id'];

$header = apache_request_headers();  
foreach ($header as $key => $val) {

/****************************************************************/

$query_1 = "SELECT * FROM efactorproduct where access_token='$val' and node_id='$deviceid'";
$doctors = $db->get_results($query_1); 
$row = $db->get_row($query_1); 
$device_type=$row['device_type']; 
$model_name=$row['model_number'];
$direction=$row['direction'];
$brightness=$row['brightness'];
$light_led=$row['light_led'];


$hue=$row['Hue'];
 $power=filter_var($row['power'], FILTER_VALIDATE_BOOLEAN);
 $led = filter_var($row['led_indication'], FILTER_VALIDATE_BOOLEAN);
 
//$power=$row['power'];
 $speed=$row['speed'];
$name_dev=$row['name'];
$curr_devicetype=$row['current_devicetype'];
$recipe=$row['recipe'];
$cookermode=$row['cookermode'];
$current=$row['current'];
$bus_voltage=$row['bus_voltage'];
$voltage=$row['ac_float'];
$rpm=$row['rpm'];
$watt=$row['power_watt'];
$rem_time=$row['rem_time'];
$hood_autoclean=$row['hood_autoclean'];
//$hood_autoclean=filter_var($row['hood_autoclean'], FILTER_VALIDATE_BOOLEAN);
//$hood_lamp=filter_var($row['hood_lamp'], FILTER_VALIDATE_BOOLEAN);
$hood_lamp=$row['hood_lamp'];
//$hood_lamp=$row['hood_lamp'];
$hood_speed=$row['hood_speed'];

$cooktime=$row['cook_time'];
$cook_run_temp=$row['cook_run_temp'];
$timer=$row['timer'];
$fan_buzzer=$row['fan_buzzer'];
$payload=$row['payload'];
//$payload="A130000000000A";
 //$payload="DEE7000000002700B40D005A006A013C280005600000000000000A";
$pagename=$row['page_name'];
$access_token=$row['access_token'];

$nurse=filter_var($row['nurse'], FILTER_VALIDATE_BOOLEAN);
$personal_assistant=filter_var($row['personal_assistant'], FILTER_VALIDATE_BOOLEAN);
$doctormanage=filter_var($row['doctor'], FILTER_VALIDATE_BOOLEAN);
$nurse_resp=$row['nurse_resp'];      
$personal_assistant_resp=$row['personal_assistant_resp'];                    
$doctormanage_resp=$row['doctor_resp'];    

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
$fan_speed2 = $row['fan_speed2'];  
$fan_speed3 = $row['fan_speed3'];  
$fan_speed4 = $row['fan_speed4'];  
$fan_speed5 = $row['fan_speed5'];  
$fan_speed6 = $row['fan_speed6'];  
$fan_speed7 = $row['fan_speed7'];  
$fan_button = $row['fan_button'];  

                                            
if($device_type == 'cooker')
{


$individualBytes = str_split($payload, 2);

if ($individualBytes[0] == 'DE' && $individualBytes[1] == 'E7') {
    if ($individualBytes[2] == '00') {
    $power = false;
} elseif ($individualBytes[2] == '01') {
    $power = true;
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
    
    $cooker_timer_1 = $individualBytes[4];
    $cooker_timer = hexdec($cooker_timer_1);

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
    
       if ($individualBytes[10] == '00') {
        $pre_timer_wait_temperature_constant_mode =false;
    } elseif($individualBytes[10] == '01') {
        $pre_timer_wait_temperature_constant_mode =true;
    }
    
    $Pre_temperature_1 = $individualBytes[11];
    $Pre_temperature = hexdec($Pre_temperature_1);
    
     $Cooker_constant_mode_1 = $individualBytes[12];
    $Cooker_constant_mode = hexdec($Cooker_constant_mode_1);
    
     $cooking_temperature_1 = $individualBytes[13];
    $cooking_temperature = hexdec($cooking_temperature_1);
    
    

if ($individualBytes[14] == '00') {
        $warm_constant_cut_off = false;
    } elseif($individualBytes[14] == '01') {
        $warm_constant_cut_off = true;
    }


if ($individualBytes[20] == '00') {
        $cook_wait_temp_constant_mode = false;
    } elseif($individualBytes[20] == '01') {
        $cook_wait_temp_constant_mode = true;
    }
    
     $Keep_Warm_timer_1 = $individualBytes[15];
    $Keep_Warm_timer = hexdec($Keep_Warm_timer_1);
    
     $Keep_warm_1 = $individualBytes[16];
    $Keep_warm = hexdec($Keep_warm_1);
    
    

   if ($individualBytes[17] == '00') {
    $pre_timer_wait_temperature_constant_mode = false;
} elseif($individualBytes[17] == '01') {
    $pre_timer_wait_temperature_constant_mode = true;
}



    
     $pre_timer_waiting_time_1 = $individualBytes[18];
    $pre_timer_waiting_time = hexdec($pre_timer_waiting_time_1);
    
     $pre_timer_Waiting_temperature_1 = $individualBytes[19];
    $pre_timer_Waiting_temperature = hexdec($pre_timer_Waiting_temperature_1);
    
       
     $cook_waiting_time_1 = $individualBytes[21];
    $cook_waiting_time = hexdec($cook_waiting_time_1);
    
     $cook_Waiting_temperature_1 = $individualBytes[22];
    $cook_Waiting_temperature = hexdec($cook_Waiting_temperature_1);
    
  

if ($individualBytes[23] == '00') {
        $warm_wait_temp_constant_mode = false;
    } elseif($individualBytes[23] == '01') {
        $warm_wait_temp_constant_mode = true;
    }



    
     $warm_waiting_time_1 = $individualBytes[24];
    $warm_waiting_time = hexdec($warm_waiting_time_1);
    
     $warm_Waiting_temperature_1 = $individualBytes[25];
    $warm_Waiting_temperature = hexdec($warm_Waiting_temperature_1);



}

}


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
$LOCK_ENABLE_DISABLE = $row['LOCK_ENABLE_DISABLE'];  


/*************************************************************/

//$query = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.1'";
 $query = "SELECT * FROM product_api WHERE model_name = '$model_name'";
$result = $db->get_results($query);
$num = count($result); // Count the number of rows in the results
$row = $result; // Get the first row of the results

/*------------------------------------------------------------*/

$frames = array();

        foreach ($result as $row) {
            $frames[] = array(
                "name" => $row["cmd_fieldDescription"],
            );
        }


$additionalData = array(
    "url" => "action=auto_get_nodes_params",
    "token" => $access_token,
    "node_id"=>$nodeid,
    "device_id"=>$deviceid,
     "payload"=>$payload
);

       
//$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFACT V1.1' AND page_names= 'Page3'"; 
 $query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name' AND page_names= '$pagename'";
        $User_nodes_doc = $db->get_results($query_1); 
        $num_11 = $db->num_rows($query_1);
        $row = $db->get_row($query_1); 
        $parameter_name = $row['parameter_name']; 
         
 
$param1=$param['parameter_name'];

 

$device = [];


foreach ($User_nodes_doc as $param) {
    $param2=$param['parameter_name'];
 //  echo $param2." ";
 $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param2'";
    //  $query_param = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.1' AND parameter_name='$param2'";
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

            $resp_dataType = explode(',', $row_param['resp_dataType']);
            $resp_framesize = $row_param['resp_framesize'];
            $resp_fieldDescription = explode(',', $row_param['resp_fieldDescription']);
            $resp_uiType = explode(',', $row_param['resp_uiType']);
            $resp_defaultvalue = explode(',', $row_param['resp_defaultvalue']);
            $resp_asperui = explode(',', $row_param['resp_asperui']);
            $resp_asperui_values = explode(',', $row_param['resp_asperui_values']);

            $error_dataType = explode(',', $row_param['error_dataType']);
            $error_uiType = explode(',', $row_param['error_uiType']);
            $error_defaultvalue = explode(',', $row_param['error_defaultvalue']);
            $error_asperui = explode(',', $row_param['error_asperui']);
            $error_asperui_values = explode(',', $row_param['error_asperui_values']);
 

        $selected = $row_param['selected'];

       
        for ($i = 0; $i < $cmd_framesize; $i++) {
         if($cmd_fielddescription[$i] == 'Data'){
         
          $default_name = $cmd_defaultvalue[$i];


      if ($parameter_name =='Speed') {
    $as_per_ui_value =  (int)$speed;
}

 elseif ($parameter_name =='power') {
      $as_per_ui_value = (bool)$power;
}  



elseif ($parameter_name =='BAUDRATE') {
    $as_per_ui_value =  "115200";
}   elseif ($parameter_name =='Direction') {
    $as_per_ui_value =  $direction;
 //     $as_per_ui_value =  '8';

}   elseif ($parameter_name =='Led') {
    $as_per_ui_value = (bool)$led;
}
elseif ($parameter_name =='Led Color') {
    $as_per_ui_value = (int)$hue;
}


 elseif ($parameter_name =='name') {

     if($name_dev == 'NULL')
      {
          $curr_name=$curr_devicetype;
      }
     else{
          $curr_name=$name_dev;
      }
     $as_per_ui_value =  $curr_name;   
  //  $as_per_ui_value =  $name_dev;
} 

elseif ($parameter_name =='Display') {
  /*  $as_per_ui_value_1 = $value_pairs; 

//$as_per_ui_value = json_encode($value_pairs);
    $as_per_ui_value = trim($as_per_ui_value_1, '{}'); // Elimina las llaves*/


$as_per_ui_value = '';

//print_r($value_pairs);



$as_per_ui_value_1 = $value_pairs;
$as_per_ui_value = json_encode($as_per_ui_value_1);
$as_per_ui_value = trim($as_per_ui_value, '{}');
$as_per_ui_value = str_replace('"', '', $as_per_ui_value);
$as_per_ui_value = str_replace(',', ', ', $as_per_ui_value);
$as_per_ui_value = str_replace('\\/','/', $as_per_ui_value);
$as_per_ui_value = str_replace('\\','', $as_per_ui_value);

 
   
}


elseif ($parameter_name =='Current') {
    $as_per_ui_value =  (float)$current;
} elseif ($parameter_name =='Voltage') {
    $as_per_ui_value =  $bus_voltage;
  //  $as_per_ui_value = (float)$voltage;
} elseif ($parameter_name =='rpm') {
    $as_per_ui_value = (int)$rpm;
} elseif ($parameter_name =='Watt') {
    $as_per_ui_value =  $Watt;
} 
elseif ($parameter_name =='Skipmode') {
    $as_per_ui_value =  true;
}

elseif ($parameter_name =='Cooker mode') {
    $as_per_ui_value =  $cookermode;
}
elseif ($parameter_name =='Recipee') {
    $as_per_ui_value =  $recipe;
}

elseif ($parameter_name =='remaining time') {
    $as_per_ui_value =  (int)$cooktime;
}
elseif ($parameter_name =='running temperature') {
    $as_per_ui_value =  (int)$cook_run_temp;
}
elseif ($parameter_name =='Nurse') {
    $as_per_ui_value = $nurse;
}
elseif ($parameter_name =='Nurse_2') {
    $as_per_ui_value = $nurse_resp;
}
elseif ($parameter_name =='Doctor') {
    $as_per_ui_value = $doctormanage;
}


elseif ($parameter_name =='Doctor_2') {
    $as_per_ui_value = $doctormanage_resp;
}
elseif ($parameter_name =='PA_2') {
    $as_per_ui_value = $personal_assistant_resp;
}
elseif ($parameter_name =='PA') {
    $as_per_ui_value = $personal_assistant;
}
elseif ($parameter_name =='Auto Clean') {


    $as_per_ui_value = $hood_autoclean;


}


elseif ($parameter_name =='Air Flow') {

    $as_per_ui_value = (int)$hood_speed;
}

elseif ($parameter_name =='Lamp') {

    $as_per_ui_value = $hood_lamp;

}

elseif ($parameter_name == 'Display Log') {
  
 // $as_per_ui_value =  $payload;
 $as_per_ui_value =  true;
      
}
else {
   $as_per_ui_value =''; 
}


if ($cmd_defaultvalue[$i] =='resistance') {
    $as_per_ui_value =  (float)$fan_resistance;
} elseif ($cmd_defaultvalue[$i] =='lmin') {
      $as_per_ui_value = (float)$fan_lmin;
} elseif ($cmd_defaultvalue[$i] =='lmax') {
      $as_per_ui_value = (float)$fan_lmax;
} elseif ($cmd_defaultvalue[$i] =='frequency') {
      $as_per_ui_value = (int)$fan_frequency;
} elseif ($cmd_defaultvalue[$i] =='pole pairs') {
      $as_per_ui_value = (int)$fan_pairs;
} elseif ($cmd_defaultvalue[$i] =='Startup Current') {
      $as_per_ui_value = (float)$fan_startup_current;
} elseif ($cmd_defaultvalue[$i] =='max current') {
      $as_per_ui_value = (int)$fan_max_current;
} elseif ($cmd_defaultvalue[$i] =='min rpm') {
      $as_per_ui_value = (int)$fan_min_rpm;
} elseif ($cmd_defaultvalue[$i] =='max rpm') {
      $as_per_ui_value = (int)$fan_max_rpm;
} elseif ($cmd_defaultvalue[$i] =='current bw') {
      $as_per_ui_value = (int)$fan_current_bw;
} elseif ($cmd_defaultvalue[$i] =='Bemf freq') {
      $as_per_ui_value = (int)$fan_bemf_freq;
} elseif ($cmd_defaultvalue[$i] =='Bemf Volt') {
      $as_per_ui_value = (int)$fan_bemf_volt;
} elseif ($cmd_defaultvalue[$i] =='Speed L1') {
      $as_per_ui_value = (int)$fan_spped_l1;
} elseif ($cmd_defaultvalue[$i] =='Speed L2') {
      $as_per_ui_value = (int)$fan_spped_l2;
} elseif ($cmd_defaultvalue[$i] =='Speed L3') {
      $as_per_ui_value = (int)$fan_spped_l3;
} elseif ($cmd_defaultvalue[$i] =='Speed L4') {
      $as_per_ui_value = (int)$fan_spped_l4;

}

else if ($cmd_defaultvalue[$i] =='Recipee') {
    $as_per_ui_value =  $recipe;
}



} 
  
}
 
  for ($i = 0; $i < $resp_framesize; $i++) {
        if ($resp_fieldDescription[$i] == 'Data') {
        $default_name2 = $resp_defaultvalue[$i];
          $as_per_ui_value2 = ''; 
  

            if ($resp_defaultvalue[$i] == 'Power') {
                $as_per_ui_value2 = (bool)$power;
            } elseif ($resp_defaultvalue[$i] == 'Speed') {
                $as_per_ui_value2 = $speed;
            } elseif ($resp_defaultvalue[$i] == 'Direction') {
                $as_per_ui_value2 = $direction;
            } elseif ($resp_defaultvalue[$i] == 'Led Color') {
                $as_per_ui_value2 = (int)$hue;
            } elseif ($resp_defaultvalue[$i] == 'Lamp') {
                $as_per_ui_value2 = (bool)$hood_lamp;
            } elseif ($resp_defaultvalue[$i] == 'Timer') {
                $as_per_ui_value2 = (int)$cooktime;
            } elseif ($resp_defaultvalue[$i] == 'Dimming Level') {
                $as_per_ui_value2 = $speed;
            } elseif ($resp_defaultvalue[$i] == 'fan enable/disable') {
                $as_per_ui_value2 = $speed;
            } elseif ($resp_defaultvalue[$i] == 'remote enable/disable') {
                $as_per_ui_value2 = $speed;
            } elseif ($resp_defaultvalue[$i] == 'rpm') {
                $as_per_ui_value2 = (int)$rpm;
            } elseif ($resp_defaultvalue[$i] == 'watt') {
                $as_per_ui_value2 = $Watt;
            } elseif ($resp_defaultvalue[$i] == 'current') {
                $as_per_ui_value2 = (float)$current;
            } elseif ($resp_defaultvalue[$i] == 'voltage') {
                $as_per_ui_value2 = (float)$voltage;
            } elseif ($resp_defaultvalue[$i] == 'vacrms') {
                $as_per_ui_value2 = $speed;
            } 



            elseif ($resp_defaultvalue[$i] == 'power') {
                $as_per_ui_value2 = (bool)$power;
            } elseif ($resp_defaultvalue[$i] == 'skipmode') {
                $as_per_ui_value2 = $cookermode;
            }   elseif ($resp_defaultvalue[$i] == 'Recipee') {
                $as_per_ui_value2 = $recipe;
            } elseif ($resp_defaultvalue[$i] == 'remaing time') {
                $as_per_ui_value2 = (int)$cooker_timer;
            } elseif ($resp_defaultvalue[$i] == 'Running temp(current)') {
                $as_per_ui_value2 = (int)$cook_run_temp;
            } elseif ($resp_defaultvalue[$i] == 'cook temp constant mode') {
                $as_per_ui_value2 = $Cooker_constant_mode;
            } elseif ($resp_defaultvalue[$i] == 'Dish cooking Temperature') {
                $as_per_ui_value2 = (int)$cooking_temperature;
            } elseif ($resp_defaultvalue[$i] == 'pre-timer constant-cutoff mode') {
                $as_per_ui_value2 = (bool)$Pre_timer_constant_mode;
            } elseif ($resp_defaultvalue[$i] == 'Dish pre timer temperature') {
                $as_per_ui_value2 = (int)$Pre_temperature;
            } elseif ($resp_defaultvalue[$i] == 'warm constant mode') {
                $as_per_ui_value2 = (bool)$warm_constant_cut_off;
            } elseif ($resp_defaultvalue[$i] == 'keep warm timer') {
                $as_per_ui_value2 = (int)$Keep_Warm_timer;
            } elseif ($resp_defaultvalue[$i] == 'keep warm temprature') {
                $as_per_ui_value2 = (int)$Keep_warm;
            } elseif ($resp_defaultvalue[$i] == 'max temperature') {
                $as_per_ui_value2 = (int)$cook_max_temp;
            } elseif ($resp_defaultvalue[$i] == 'pre-wait temp constantmode') {
                $as_per_ui_value2 = (bool)$pre_timer_wait_temperature_constant_mode;
            } elseif ($resp_defaultvalue[$i] == 'pre-waiting time') {
                $as_per_ui_value2 = (int)$pre_timer_waiting_time;
            } elseif ($resp_defaultvalue[$i] == 'pre-waiting temperature') {
                $as_per_ui_value2 = (int)$pre_timer_Waiting_temperature;
            } elseif ($resp_defaultvalue[$i] == 'cook wait temp constantmode') {
                $as_per_ui_value2 = (int)$cook_wait_temp_constant_mode;
            } elseif ($resp_defaultvalue[$i] == 'cook waiting time') {
                $as_per_ui_value2 = (int)$cook_waiting_time;
            } elseif ($resp_defaultvalue[$i] == 'cook waiting temperature') {
                $as_per_ui_value2 = (int)$cook_Waiting_temperature;
            } elseif ($resp_defaultvalue[$i] == 'warm wait temp constantmode') {
                $as_per_ui_value2 = (bool)$warm_wait_temp_constant_mode;
            }  elseif ($resp_defaultvalue[$i] == 'keepwarm waiting') {

              $as_per_ui_value2 = $warm_Waiting_temperature;
               //   $as_per_ui_value2 = '0';

            }elseif ($resp_defaultvalue[$i] == 'keepwarm waiting time') {
                $as_per_ui_value2 = (int)$warm_waiting_time;
            }
            
            
            elseif ($resp_defaultvalue[$i] == 'power') {
                $as_per_ui_value2 = (bool)$power;
            } elseif ($resp_defaultvalue[$i] == 'Auto Clean') {
                $as_per_ui_value2 = (bool)$hood_autoclean;
            } elseif ($resp_defaultvalue[$i] == 'Air Flow') {
                $as_per_ui_value2 =(int)$hood_speed;
            } elseif ($resp_defaultvalue[$i] == 'Lamp') {
                $as_per_ui_value2 = (bool)$hood_lamp;
            } elseif ($resp_defaultvalue[$i] == 'enable') {
                $as_per_ui_value2 = (int)$starter_relay_off_timer;
            } elseif ($resp_defaultvalue[$i] == 'Voltage') {
                $as_per_ui_value2 = (float)$voltage;
            } elseif ($resp_defaultvalue[$i] == 'Current') {
                $as_per_ui_value2 =  (float)$current;
            } elseif ($resp_defaultvalue[$i] == 'Watt') {
                $as_per_ui_value2 = $Watt;
            } elseif ($resp_defaultvalue[$i] == 'Temperature') {
                $as_per_ui_value2 = (int)$starter_relay_off_timer;
            } 
            
            
            elseif ($resp_defaultvalue[$i] == 'Power') {
                $as_per_ui_value2 = (bool)$power;
            }elseif ($resp_defaultvalue[$i] == 'alarm') {
                $as_per_ui_value2 = (bool)$power;
            }elseif ($resp_defaultvalue[$i] == 'Device lock') {
                $as_per_ui_value2 = (bool)$power;
            }elseif ($resp_defaultvalue[$i] == 'avg powerper 1 Hr') {
                $as_per_ui_value2 = (bool)$power;
            }elseif ($resp_defaultvalue[$i] == 'Enable/Disable') {
                $as_per_ui_value2 = (bool)$power;
            }elseif ($resp_defaultvalue[$i] == 'power factor') {
                $as_per_ui_value2 = (bool)$power;
            }elseif ($resp_defaultvalue[$i] == 'units') {
                $as_per_ui_value2 = (bool)$power;
            }elseif ($resp_defaultvalue[$i] == 'Power') {
                $as_per_ui_value2 = (bool)$power;
            }elseif ($resp_defaultvalue[$i] == 'voltage') {
                $as_per_ui_value2 = (float)$voltage;
            }elseif ($resp_defaultvalue[$i] == 'current') {
                $as_per_ui_value2 = (float)$current;
            }elseif ($resp_defaultvalue[$i] == 'wattage') {
                $as_per_ui_value2 = $Watt;
            }  elseif ($resp_defaultvalue[$i] == 'starter relay off timer') {
                $as_per_ui_value2 = (int)$starter_relay_off_timer;
            } elseif ($resp_defaultvalue[$i] == 'start high current cutoff') {
                $as_per_ui_value2 = (int)$start_high_current_cutoff;
            } elseif ($resp_defaultvalue[$i] == 'start low current cutoff') {
                $as_per_ui_value2 = (int)$start_low_current_cutoff;
            } elseif ($resp_defaultvalue[$i] == 'LOCK ENABLE/DISABLE') {
                $as_per_ui_value2 = (bool)$LOCK_ENABLE_DISABLE;
            } elseif ($resp_defaultvalue[$i] == 'Run high current cutoff') {
                $as_per_ui_value2 = (int)$Run_high_current_cutoff;
            } elseif ($resp_defaultvalue[$i] == 'high voltage cutoff') {
                $as_per_ui_value2 = (int)$high_voltage_cutoff;
            } elseif ($resp_defaultvalue[$i] == 'USER SET MAX CURRENT CUTOFF LIMIT') {
                $as_per_ui_value2 = (int)$USER_SET_MAX_CURRENT_CUTOFF_LIMIT;
            } elseif ($resp_defaultvalue[$i] == 'low voltage cutoff') {
                $as_per_ui_value2 = (int)$low_voltage_cutoff;
            } elseif ($resp_defaultvalue[$i] == 'ultra high voltage cutoff') {
                $as_per_ui_value2 = (int)$ultra_high_voltage_cutoff;
            } elseif ($resp_defaultvalue[$i] == 'ultra high current cutoff') {
                $as_per_ui_value2 = (int)$ultra_high_current_cutoff;
            } elseif ($resp_defaultvalue[$i] == 'USER & DEVICE MAX CURRENT LIMIT') {
                $as_per_ui_value2 = (int)$USER_AND_DEVICE_MAX_CURRENT_LIMIT;
            } elseif ($resp_defaultvalue[$i] == 'Run low current cutoff') {
                $as_per_ui_value2 = (int)$Run_low_current_cutoff;
                }

                 elseif ($resp_defaultvalue[$i] == 'Power') {
                $as_per_ui_value2 = (bool)$power;
            } elseif ($resp_defaultvalue[$i] == 'color') {
                $as_per_ui_value2 = $speed;
            } elseif ($resp_defaultvalue[$i] == 'lock mode') {
                $as_per_ui_value2 = (bool)$power;
            } elseif ($resp_defaultvalue[$i] == 'device enable or disable') {
                $as_per_ui_value2 = (bool)$power;
            } elseif ($resp_defaultvalue[$i] == 'color status') {
                $as_per_ui_value2 = (bool)$power;
            } elseif ($resp_defaultvalue[$i] == 'dimming status') {
                $as_per_ui_value2 = (bool)$power;
            } elseif ($resp_defaultvalue[$i] == 'bus voltage') {
                $as_per_ui_value2 = (bool)$power;
            } elseif ($resp_defaultvalue[$i] == 'bus current') {
                $as_per_ui_value2 = (bool)$power;
            } elseif ($resp_defaultvalue[$i] == 'led current') {
                $as_per_ui_value2 = (bool)$power;
            } elseif ($resp_defaultvalue[$i] == 'error') {
                $as_per_ui_value2 = (bool)$power;
            }
                
            else{
            $as_per_ui_value2 = '';
            }

           // $resp_defaultvalue[$i] = 'power';
          //  $as_per_ui_value2 = '02'; 
            
            $value_pairs[$resp_defaultvalue[$i]] = $as_per_ui_value2;


            
        } 
    }

//print_r($value_pairs); 


 if (!isset($device[$curr_devicetype])) {
        $device[$curr_devicetype] = [];
    }

    if (!isset($device[$curr_devicetype][$pagename])) {
        $device[$curr_devicetype][$pagename] = [];
    }
    
    if (!empty($default_name)) {
            $device[$curr_devicetype][$pagename][$default_name] = $as_per_ui_value;
        }
        
        
}


$allData = array_merge($additionalData, $device);
echo json_encode($allData);

//echo json_encode($device);
exit;


/**************************************************************/
}

}



/**************************************************************************************/

if (isset($_GET['action']) && $_GET['action']=='auto_get_nodes_params') {
    
    
 
$nodeid =$_GET['node_id'];
$deviceid=$_GET['device_id'];

$header = apache_request_headers();  
foreach ($header as $key => $val) {

$query_efact = "SELECT * FROM efactorproduct where node_id='$deviceid'";
$doctors_efact = $db->get_results($query_efact); 
$dbcker_efact = $db->get_row($query_efact); 
$devicetype=$dbcker_efact['device_type'];
$model_name=$dbcker_efact['model_name'];

if($model_name == 'EFDEFLT V1.0'){


    $query_1 = "SELECT * FROM efactorproduct where node_id='$deviceid'";
}else{


    $query_1 = "SELECT * FROM `$model_name` where node_id='$deviceid'";
}

//$query_1 = "SELECT * FROM $devicetype where node_id='$deviceid'";
$doctors2 = $db->get_results($query_1); 
$dbcker = $db->get_row($query_1); 
$payload=$dbcker['payload'];

 
$model_name=$dbcker['model_name'];

if($model_name == 'EFDEFLT V1.0'){
$pagename=$dbcker['page_name'];

}else{
$pagename=$dbcker['page_names'];
}
$access_token=$dbcker['access_token'];
$curr_devicetype=$dbcker['current_devicetype'];







 //$query = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.1'";
  $query = "SELECT * FROM product_api WHERE model_name = '$model_name'";

 $result = $db->get_results($query);
$num = count($result); // Count the number of rows in the results
$row = $result; // Get the first row of the results
 
$frames = array();

foreach ($result as $row) {
   $frames[] = array(
       "name" => $row["cmd_fieldDescription"],
        );
    }


$additionalData = array(
    "url" => "action=auto_get_nodes_params",
    "token" => $access_token,
    "node_id"=>$nodeid,
    "device_id"=>$deviceid,
     "payload"=>$payload
);

 
     // $query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFACT V1.1' AND page_names= 'Page3'"; 
      $query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name' AND page_names= '$pagename'";
     $query_2 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFDEFLT V1.1' AND page_names= '$pagename'";
   // $query_2 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFDEFLT V1.0' AND page_names= 'Page3'"; 
 
    $result_1 = $db->get_results($query_1); 
   $result_2 = $db->get_results($query_2); 
   // print_r($result_1);
   $result_3 = $db->num_rows($query_1);
   $result_4 = $db->num_rows($query_2);
    
   $result_5 = $db->get_row($query_1); 
   $result_6 = $db->get_row($query_2); 
   
   $User_nodes_doc = array_merge($result_1, $result_2);
   $num_11 = $result_3 + $result_4;
   $row = array_merge($result_1, $result_2);
   $parameter_name = $row['parameter_name']; 
   // print_r($User_nodes_doc);
 /*  
 //$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFACT V1.1' AND page_names= 'Page1'"; 
 $query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name' AND page_names= '$pagename'";
        $User_nodes_doc = $db->get_results($query_1); 
        $num_11 = $db->num_rows($query_1);
        $row = $db->get_row($query_1); 
        $parameter_name = $row['parameter_name']; 
   */
 
$param1=$param['parameter_name'];

 

$device = [];


foreach ($User_nodes_doc as $param) {
    $param2=$param['parameter_name'];
 //  echo $param2." ";
 
    
    $query_param1 = "SELECT * FROM product_api WHERE model_name = 'EFDEFLT V1.1' AND parameter_name='$param2'";
      $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param2'";
    //  $query_param = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.1' AND parameter_name='$param2'";
   
    $result_11 = $db->get_results($query_param1); 
   $result_22 = $db->get_results($query_param); 
  // print_r($result_1);
   $result_33 = $db->num_rows($query_param1);
   $result_44 = $db->num_rows($query_param);
   
   $row_param11 = $db->get_row($query_param1); 
   $row_param22 = $db->get_row($query_param); 
   
   $params = array_merge($result_11, $result_22);
   $num_param = $result_33 + $result_44;
   $row_param = array_merge((array)$row_param11, (array)$row_param22);
  //print_r($row_param);
  /*
 $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param2'";
 // $query_param = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.1' AND parameter_name='$param2'";
        $params = $db->get_results($query_param); 
        $num_param = $db->num_rows($query_param);
        $row_param = $db->get_row($query_param); 
  */
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
            $cmd_sno_asperui = explode(',', $row_param['cmd_sno_asperui']);
            $cmd_asperui_values = explode(',', $row_param['cmd_asperui_values']);
            $cmd_valueinhexmin = explode(',', $row_param['cmd_valueinhexmin']);
            $cmd_numberOfbyte = explode(',', $row_param['cmd_numberOfbytes']);

            $resp_dataType = explode(',', $row_param['resp_dataType']);
            $resp_framesize = $row_param['resp_framesize'];
            $resp_fieldDescription = explode(',', $row_param['resp_fieldDescription']);
            $resp_uiType = explode(',', $row_param['resp_uiType']);
            $resp_defaultvalue = explode(',', $row_param['resp_defaultvalue']);
            $resp_asperui = explode(',', $row_param['resp_asperui']);
            $resp_sno_asperui = explode(',', $row_param['resp_sno_asperui']);
            $resp_asperui_values = explode(',', $row_param['resp_asperui_values']);
            $resp_numberOfBytes = explode(',', $row_param['resp_numberOfBytes']);

            $error_dataType = explode(',', $row_param['error_dataType']);
            $error_uiType = explode(',', $row_param['error_uiType']);
            $error_defaultvalue = explode(',', $row_param['error_defaultvalue']);
            $error_asperui = explode(',', $row_param['error_asperui']);
            $error_asperui_values = explode(',', $row_param['error_asperui_values']);
 

            $selected = $row_param['selected'];
            $cker = array();
    
            foreach ($dbcker as $key => $value) {
                $cker[] = array(
                    'name' => $key,
                    'value' => $value
                );
            }
          // $default_name = [];
         // echo  $cmd_framesize; 
          for ($i = 0; $i < $cmd_framesize; $i++) {
            if ($cmd_fielddescription[$i] == 'Data') {
                $default_name[] = [
                    "name" => $cmd_defaultvalue[$i],
                    "ui" => $cmd_uitype[$i],
                    "asperui" => $cmd_asperui[$i],
                    "sno_asperui" => $cmd_sno_asperui[$i],
                    "asperui_values" => $cmd_asperui_values[$i],
                    "type" => $cmd_datatype[$i]  
                ];
     
                $result = [];
        
                foreach ($default_name as $stag) {
                    $stag_name = $stag['name'];
                    $stag_ui = $stag['ui'];
                    $stag_asperui = $stag['asperui'];
                    $stag_sno_asperui = $stag['sno_asperui'];
                    $stag_asperui_values = $stag['asperui_values'];
                    $stag_type = $stag['type'];
        
                    foreach ($cker as $entry) {
                        if ($stag_name === $entry['name']) {
                            $value = $entry['value'];
        
                            /*
                            // Check the data type
                            if ($stag_type === 'String') {
    
                               // echo $model_name;
                                if($model_name === 'EFDEFLT V1.0'){
                                    if($stag_name == 'Display'){
                                        $result[$stag_name] = $payload;
                                        }elseif(!empty($value)){
                                            $result[$stag_name] = $value;  
                                        }else{ 
                                            $result[$stag_name] = "null";  
                                        }
                                }else{
                                    if(!empty($value)){
                                        $result[$stag_name] = $value;  
                                    }else{ 
                                        $result[$stag_name] = "null";  
                                    }
                            }
                            
                            } elseif($stag_type === 'Float'){
                               
                                if(!empty($value)){
                                    $result[$stag_name] = hexToFloat($value);   
                                }else{ 
                                    $result[$stag_name] = "null";  
                                }
    
                            } elseif($stag_type === 'UnsignedInt'){ 
                                
    if($stag_ui === 'Toggle Switch'){
       // print_r($stag_asperui);
        //  print_r($stag_asperui_values);
        if($stag_name === 'Led'){
            if($value !== '07'){
                $result[$stag_name] = true;
            }else{
                $result[$stag_name] = false;
            }
                                        }else{
                                       // $result[$stag_name] = (bool)$value;  
                                       if($value === '00'){
                                        $result[$stag_name] = false;
                                    }else{
                                        $result[$stag_name] = true;
                                    }
                                        }
    }else{
        if(!empty($value)){
            $result[$stag_name] = hexdec($value);    
        }else{ 
            $result[$stag_name] = "null";  
        }
    }
                            
                            }else{
                                if(!empty($value)){
                                    $result[$stag_name] = $value;    
                                }else{ 
                                    $result[$stag_name] = "null";  
                                }
                            }
                            break;  
                            */
                         
                            if ($stag_ui === 'Toggle Switch' || $stag_ui === 'Notification') {
                               // $ui_toggle = explode('][', $stag_asperui);
                               //$ui_toggle_value = explode('][', $stag_asperui_values);
                                $ui_toggle = explode('][', trim($stag_asperui, '[]'));
                                $ui_toggle_value = explode('][', trim($stag_asperui_values, '[]'));
                                $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);
                                //  print_r($value);   
                                // print_r($dropdown_map);   
                                $result[$stag_name] = isset($dropdown_map[$value]) ? $dropdown_map[$value] : '';   
                               // echo  $result[$stag_name] ;          
                            }elseif ($stag_ui === 'Slider' || $stag_ui === 'Hue Slider'  || $stag_ui === 'Hue Slider Custom'  || $stag_ui === 'Brightness Slider'  || $stag_ui === 'Airflow Slider') {
                                //echo $stag_name;
                                if ($stag_type === 'UnsignedInt') {
                                    if(!empty($value)){
                                        $result[$stag_name] = hexdec($value);    
                                    }else{ 
                                        $result[$stag_name] = "null";  
                                    }
                                } elseif ($stag_type === 'Float') {
                                    if(!empty($value)){
                                        $cmdfloat_value = hexToFloat($value); 
                                        $result[$stag_name] = (float)sprintf("%.2f", $cmdfloat_value);
                                    }else{ 
                                        $result[$stag_name] = "null";  
                                    }
                                }else{
                                    if(!empty($value)){
                                        $result[$stag_name] = $value;   
                                    }else{ 
                                        $result[$stag_name] = "null";  
                                    }
        
                                }
                            }elseif ($stag_ui === 'Drop Down') {
                              //  echo $stag_name;
                                if ($stag_type === 'UnsignedInt') {
                                    if(!empty($value)){
                                        //$result[$stag_name] = hexdec($value);

                                       if( $stag_name == 'Recipee')
{  
                                         $ui_toggle = explode('][', trim($stag_asperui, '[]'));
                                $ui_toggle_value = explode('][', trim($stag_sno_asperui, '[]'));
                                $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);
                               //   print_r($value);   
                               // print_r($dropdown_map);   
                                $result[$stag_name] = isset($dropdown_map[$value]) ? $dropdown_map[$value] : '';  
}
                              

else{
                         /*    $ui_toggle = explode('][', trim($stag_asperui, '[]'));
                                $ui_toggle_value = explode('][', trim($stag_asperui_values, '[]'));
                                $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);
                                 
                                $result[$stag_name] = isset($dropdown_map[$value]) ? $dropdown_map[$value] : '';*/


//echo 'dropdown else';



                             $ui_toggle = explode('][', trim($stag_asperui, '[]'));
                           //     $ui_toggle_value = explode('][', trim($stag_asperui_values, '[]'));
                               $ui_toggle_value = explode('][', trim($stag_sno_asperui, '[]'));
                                $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);

                                 
                                $result[$stag_name] = isset($dropdown_map[$value]) ? $dropdown_map[$value] : '';

//print_r($result);



}
                               






  
                                    }else{ 
                                    //    $result[$stag_name] = "null";  
                                    }
                                } elseif ($stag_type === 'Float') {
                                    if(!empty($value)){
                                        $cmdfloat_value = hexToFloat($value); 
                                        $result[$stag_name] = (float)sprintf("%.2f", $cmdfloat_value);
                                    }else{ 
                                        $result[$stag_name] = "null";  
                                    }
                                }else{
                                    if(!empty($value)){

                                
                                    //    $result[$stag_name] = $value; 

                                          
                                    }else{ 
                                        $result[$stag_name] = "null";  
                                    }
        
                                }
                            }elseif ($stag_ui === 'Text') {
                                if ($stag_type === 'String') {
                                   
                                     if($model_name === 'EFDEFLT V1.0'){
                                         if($stag_name == 'Display'){
                                             $result[$stag_name] = $payload;
                                             }elseif(!empty($value)){
                                                    $result[$stag_name] = $value;  
                                                }else{
                                                    $result[$stag_name] = "null";  
                                                }
                                             
                                     }else{
                                        if(!empty($value)){

                                          //  $result[$stag_name] = $value;  

                                        if(!empty($stag_asperui))
                                         {
                                             
                                            if($stag_name == 'name')
                                           {
                                                // echo 'if....';
                                                // echo $stag_name."\n";
                                                // echo $value."\n";         

                                               $result[$stag_name] = $value;
                                           }

                                         else{
                                                                                        
                                          $ui_toggle = explode('][', trim($stag_asperui, '[]'));
                                          $ui_toggle_value = explode('][', trim($stag_sno_asperui, '[]'));
                                $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);
                                //  print_r($value);   
                                // print_r($dropdown_map);   
                                $result[$stag_name] = isset($dropdown_map[$value]) ? $dropdown_map[$value] : '';
                                         }

                                        }else{

                                              $result[$stag_name] = $value;
                                        }




                                        }else{
                                            $result[$stag_name] = "null";  
                                    }  
                                 }
                                 }elseif ($stag_type === 'UnsignedInt') {
                                    if(!empty($value)){
                                        $result[$stag_name] = hexdec($value);    
                                    }else{ 
                                        $result[$stag_name] = "null";  
                                    }
                                 }elseif ($stag_type === 'Float') {
                                    if(!empty($value)){
                                        $cmdfloat_value = hexToFloat($value); 
                                        $result[$stag_name] = (float)sprintf("%.2f", $cmdfloat_value);  
                                    }else{ 
                                        $result[$stag_name] = "null";  
                                    }
                                 }else{
                                    if(!empty($value)){
                                        $result[$stag_name] = $value;  
                                    }else{
                                        $result[$stag_name] = "null";  
                                    }
                                 }
                                 }else{
                                     if(!empty($value)){
                                        $result[$stag_name] = $value;  
                                    }else{ 
                                        $result[$stag_name] = "null";  
                                    }
                                 } 
                        } 
                    }
               //  print_r($result);  
                }
            }
        } 

        for ($i = 0; $i < $resp_framesize; $i++) {
            $default_name2 = $resp_defaultvalue[$i];
              //echo $payload;
           //     print_r($resp_defaultvalue);
               //  print_r($resp_sno_asperui);
            $start = 0;
           
             
            foreach ($resp_numberOfBytes as $index => $bytes) { 
                $chunk = substr($payload, $start, $bytes * 2); 

             // print_r($chunk);

             //  print_r($resp_uiType); 
                if ($resp_uiType[$index] === 'Toggle Switch' || $resp_uiType[$index] === 'Notification') {

                             //   echo 'Toggle Switch';

                             //   echo $stag_name;
                                if ($resp_dataType[$index] === 'UnsignedInt') {

                                    //   echo 'UnsignedInt';
                                    if(!empty($chunk)){
                                        $convertedValue = hexdec($chunk);   
                                       /*  $ui_toggle = explode('][', trim($resp_asperui[$index], '[]'));
                                $ui_toggle_value = explode('][', trim($resp_sno_asperui[$index], '[]'));
                                $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);
                                //  print_r($chunk);   
                                //print_r($dropdown_map);   
                                $convertedValue = isset($dropdown_map[$chunk]) ? $dropdown_map[$chunk] : ''; 
                                //$convertedValue = (bool) $convertedValue;  
                                //echo  $convertedValue;*/
                                    }else{ 
                                      // echo 'power';
                                        $convertedValue = "null";  
                                    }
                                } elseif ($resp_dataType[$index] === 'Float') {

                                   // echo 'Float';
                                    if(!empty($chunk)){
                                        $cmdfloat_value = hexToFloat($chunk); 
                                        $convertedValue = (float)sprintf("%.2f", $cmdfloat_value);
                                    }else{ 
                                        $convertedValue = "null";  
                                    }
                                }else{
                                     // echo 'else';
                                    if(!empty($chunk)){
                                        $convertedValue = $chunk;   
                                    }else{ 
                                        $convertedValue = "null";  
                                    }
        
                                }
                            }elseif ($resp_uiType[$index] === 'Drop Down') {
                              //  echo $stag_name;
                                if ($resp_dataType[$index] === 'UnsignedInt') {
                                    if(!empty($chunk)){
                                        //$convertedValue = hexdec($chunk);   
                                         $ui_toggle = explode('][', trim($resp_asperui[$index], '[]'));
                                $ui_toggle_value = explode('][', trim($resp_sno_asperui[$index], '[]'));
                                $dropdown_map = array_combine($ui_toggle_value,$ui_toggle);
                                //  print_r($chunk);   
                                //print_r($dropdown_map);   
                                $convertedValue = isset($dropdown_map[$chunk]) ? $dropdown_map[$chunk] : '';   
                                //echo  $convertedValue;
                                    }else{ 
                                        $convertedValue = "null";  
                                    }
                                } elseif ($resp_dataType[$index] === 'Float') {
                                    if(!empty($chunk)){
                                        $cmdfloat_value = hexToFloat($chunk); 
                                        $convertedValue = (float)sprintf("%.2f", $cmdfloat_value);
                                    }else{ 
                                        $convertedValue = "null";  
                                    }
                                }else{
                                    if(!empty($chunk)){
                                        $convertedValue = $chunk;   
                                    }else{ 
                                        $convertedValue = "null";  
                                    }
        
                                }
                            } else{  
                if ($resp_dataType[$index] === 'UnsignedInt') { 
                    $convertedValue = hexdec($chunk); 
                } elseif ($resp_dataType[$index] === 'Float') { 
                    $cmdfloat_value = hexToFloat($chunk); 
               $convertedValue= (float)sprintf("%.2f", $cmdfloat_value);   
                } else { 
                    $convertedValue = $chunk; 
                } 
                }
                $key = $resp_defaultvalue[$index];
                $result_payload[$key] = $convertedValue; 
                $start += $bytes * 2;  
            }
            $result_payload = array_filter($result_payload, function($key) {
                return !empty($key);  
            }, ARRAY_FILTER_USE_KEY);
            
            // Output the result
           // print_r($result_payload);
            $defaultValues[] = [
                "name" => $default_name2,
            ];

             
        }

//$copied_payload = array_slice($result_payload, 0);
}

//print_r($copied_payload);

 // print_r($copied_payload);

 // print_r($defaultValues);

 // print_r($dbcker);

//print_r($doctors2);
 
//print_r($cker);

$result2 = [];
 
foreach ($defaultValues as $stag) {
    $stag_name = $stag['name'];

    foreach ($cker as $entry) {
        if ($stag_name === $entry['name']) {
        $result2[$stag_name] = $entry['value'];
         //   echo "Matched value for $stag_name: " . $entry['value'] . "\n";
            break;  
        }
    }
}
 
 // print_r($result2);
 $defaultValues2 = array();

foreach ($result2 as $key => $value) {
    $defaultValues2[] = array(
        'name' => $key,
        'value' => $value
    );
}
// print_r($defaultValues2);
 
$formattedEntries = [];

foreach ($defaultValues2 as $entry) {
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
 
   //print_r($resultString);
 
 $formatted = [];
foreach ($result_payload as $key => $value) {
    $formatted[] = "$key: $value";
}
 
$output_string = implode(",", $formatted);
 
//echo $output_string;
 
   //print_r($default_name);
 
 if (!isset($device[$curr_devicetype])) {
        $device[$curr_devicetype] = [];
    }

    if (!isset($device[$curr_devicetype][$pagename])) {
        $device[$curr_devicetype][$pagename] = [];
    }
    
        if (!empty($default_name)) {
    foreach ($default_name as $stag) {
        $name = $stag['name'];
        if (isset($result[$name])) {
              // echo $model_name;
            if($model_name === 'EFDEFLT V1.0'){
             //   $device[$curr_devicetype][$pagename][$name] = $result[$name];
                 if($name === 'Display'){
                $device[$curr_devicetype][$pagename][$name] = $payload;
               }
               else if($name === 'DisplayLog'){
                $device[$curr_devicetype][$pagename][$name] = $payload;
               }
               else{
                  // $device[$curr_devicetype][$pagename][$name] = "null";
                      $device[$curr_devicetype][$pagename] = $result;

               }

       }else{
         if($name === 'Display'){
         // echo 'Display';
         // $device[$curr_devicetype][$pagename][$name] = $resultString;
         $device[$curr_devicetype][$pagename][$name] = $output_string;

          if($model_name === 'EFDEFLT V1.0'){
                $device[$curr_devicetype][$pagename][$name] = $payload;
       }



          }else{
        //   echo 'else';
            $device[$curr_devicetype][$pagename][$name] = $result[$name];
            }
        }  
    }  
    }
} else {
    //echo "default_name array is empty.\n";
}
 
 

$allData = array_merge($additionalData, $device);
echo json_encode($allData);
exit;

/**************************************************************/
}

}



/***********************************************************************************/

/**************************************************************************************/

if (isset($_GET['action']) && $_GET['action']=='extract_payload') {
    
    
 
$nodeid =$_GET['node_id'];
$deviceid=$_GET['device_id'];

$header = apache_request_headers();  
foreach ($header as $key => $val) {

$query_efact = "SELECT * FROM efactorproduct where node_id='$deviceid'";
$doctors_efact = $db->get_results($query_efact); 
$dbcker_efact = $db->get_row($query_efact); 
$devicetype=$dbcker_efact['device_type'];
$model_name=$dbcker_efact['model_name'];
echo $model_name;
if($model_name == 'EFDEFLT V1.0'){
    $query_1 = "SELECT * FROM efactorproduct where node_id='$deviceid'";
}else{
    $query_1 = "SELECT * FROM `$model_name` where node_id='$deviceid'";
}

//$query_1 = "SELECT * FROM $devicetype where node_id='$deviceid'";
$doctors2 = $db->get_results($query_1); 
$dbcker = $db->get_row($query_1); 
$payload=$dbcker['payload'];

 
$model_name=$dbcker['model_name'];

if($model_name == 'EFDEFLT V1.0'){
$pagename=$dbcker['page_name'];

}else{
$pagename=$dbcker['page_names'];
}
$access_token=$dbcker['access_token'];
$curr_devicetype=$dbcker['current_devicetype'];







 //$query = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.1'";
  $query = "SELECT * FROM product_api WHERE model_name = '$model_name'";

 $result = $db->get_results($query);
$num = count($result); // Count the number of rows in the results
$row = $result; // Get the first row of the results
 
$frames = array();

foreach ($result as $row) {
   $frames[] = array(
       "name" => $row["cmd_fieldDescription"],
        );
    }


$additionalData = array(
    "url" => "action=auto_get_nodes_params",
    "token" => $access_token,
    "node_id"=>$nodeid,
    "device_id"=>$deviceid,
     "payload"=>$payload
);

 
     // $query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFACT V1.1' AND page_names= 'Page3'"; 
      $query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name' AND page_names= '$pagename'";
     $query_2 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFDEFLT V1.1' AND page_names= '$pagename'";
   // $query_2 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFDEFLT V1.0' AND page_names= 'Page3'"; 
 
    $result_1 = $db->get_results($query_1); 
   $result_2 = $db->get_results($query_2); 
   // print_r($result_1);
   $result_3 = $db->num_rows($query_1);
   $result_4 = $db->num_rows($query_2);
    
   $result_5 = $db->get_row($query_1); 
   $result_6 = $db->get_row($query_2); 
   
   $User_nodes_doc = array_merge($result_1, $result_2);
   $num_11 = $result_3 + $result_4;
   $row = array_merge($result_1, $result_2);
   $parameter_name = $row['parameter_name']; 
   // print_r($User_nodes_doc);
 /*  
 //$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = 'EFACT V1.1' AND page_names= 'Page1'"; 
 $query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name' AND page_names= '$pagename'";
        $User_nodes_doc = $db->get_results($query_1); 
        $num_11 = $db->num_rows($query_1);
        $row = $db->get_row($query_1); 
        $parameter_name = $row['parameter_name']; 
   */
 
$param1=$param['parameter_name'];

 

$device = [];


foreach ($User_nodes_doc as $param) {
    $param2=$param['parameter_name'];
 //  echo $param2." ";
 
    
    $query_param1 = "SELECT * FROM product_api WHERE model_name = 'EFDEFLT V1.1' AND parameter_name='$param2'";
      $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param2'";
    //  $query_param = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.1' AND parameter_name='$param2'";
   
    $result_11 = $db->get_results($query_param1); 
   $result_22 = $db->get_results($query_param); 
  // print_r($result_1);
   $result_33 = $db->num_rows($query_param1);
   $result_44 = $db->num_rows($query_param);
   
   $row_param11 = $db->get_row($query_param1); 
   $row_param22 = $db->get_row($query_param); 
   
   $params = array_merge($result_11, $result_22);
   $num_param = $result_33 + $result_44;
   $row_param = array_merge((array)$row_param11, (array)$row_param22);
  //print_r($row_param);
  /*
 $query_param = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='$param2'";
 // $query_param = "SELECT * FROM product_api WHERE model_name = 'EFACT V1.1' AND parameter_name='$param2'";
        $params = $db->get_results($query_param); 
        $num_param = $db->num_rows($query_param);
        $row_param = $db->get_row($query_param); 
  */
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
            $resp_numberOfBytes = explode(',', $row_param['resp_numberOfBytes']);

            $error_dataType = explode(',', $row_param['error_dataType']);
            $error_uiType = explode(',', $row_param['error_uiType']);
            $error_defaultvalue = explode(',', $row_param['error_defaultvalue']);
            $error_asperui = explode(',', $row_param['error_asperui']);
            $error_asperui_values = explode(',', $row_param['error_asperui_values']);
 

            $selected = $row_param['selected'];
            $cker = array();
    
            foreach ($dbcker as $key => $value) {
                $cker[] = array(
                    'name' => $key,
                    'value' => $value
                );
            }
         

        for ($i = 0; $i < $resp_framesize; $i++) {
            $default_name2 = $resp_defaultvalue[$i];
              //echo $payload;
               print_r($resp_defaultvalue);
               // print_r($resp_numberOfBytes);
            $start = 0;
           
             
            foreach ($resp_numberOfBytes as $index => $bytes) { 
                $chunk = substr($payload, $start, $bytes * 2);   
                $key = $resp_defaultvalue[$index];
                $result_payload[$key] = $chunk; 
                $start += $bytes * 2;  
            }
            $result_payload = array_filter($result_payload, function($key) {
                return !empty($key);  
            }, ARRAY_FILTER_USE_KEY);
            
            // Output the result
           // print_r($result_payload); 
        } 
}
 
        
    print_r($result_payload); 
 

$allData = array_merge($additionalData, $device);
echo json_encode($allData);
exit;

/**************************************************************/
}

}



/***********************************************************************************/







/***********************************************************************************/

if (isset($_GET['action']) && $_GET['action']=='auto_get_nodes_params_page') {

$nodeid =$_GET['node_id'];
$deviceid=$_GET['device_id'];

//$header = apache_request_headers();  
//foreach ($header as $key => $val) {

/****************************************************************/

$query_1 = "SELECT * FROM efactorproduct where node_id='$deviceid'";
$doctors = $db->get_results($query_1); 
$row = $db->get_row($query_1); 
$device_type=$row['device_type']; 
$model_name=$row['model_number'];
$direction=$row['direction'];
$brightness=$row['brightness'];
$light_led=$row['light_led'];


$hue=$row['Hue'];
$power=filter_var($row['power'], FILTER_VALIDATE_BOOLEAN);
$led = filter_var($row['led_indication'], FILTER_VALIDATE_BOOLEAN);




//$power=$row['power'];
$speed=$row['speed'];
$name_dev=$row['name'];
$curr_devicetype=$row['current_devicetype'];
$recipe=$row['recipe'];
$cookermode=$row['cookermode'];
$current=$row['current'];
$bus_voltage=$row['bus_voltage'];
$voltage=$row['ac_float'];
$rpm=$row['rpm'];
$watt=$row['power_watt'];
$rem_time=$row['rem_time'];
$hood_autoclean=$row['hood_autoclean'];
//$hood_autoclean=filter_var($row['hood_autoclean'], FILTER_VALIDATE_BOOLEAN);
//$hood_lamp=filter_var($row['hood_lamp'], FILTER_VALIDATE_BOOLEAN);
$hood_lamp=$row['hood_lamp'];
//$hood_lamp=$row['hood_lamp'];
$hood_speed=$row['hood_speed'];


 $cooktime=$row['cook_time'];
 $cook_run_temp=$row['cook_run_temp'];
$timer=$row['timer'];
$fan_buzzer=$row['fan_buzzer'];
$payload=$row['payload'];
$pagename=$row['page_name'];
$access_token=$row['access_token'];



$nurse=filter_var($row['nurse'], FILTER_VALIDATE_BOOLEAN);
$personal_assistant=filter_var($row['personal_assistant'], FILTER_VALIDATE_BOOLEAN);
$doctormanage=filter_var($row['doctor'], FILTER_VALIDATE_BOOLEAN);
$nurse_resp=$row['nurse_resp'];      
$personal_assistant_resp=$row['personal_assistant_resp'];                    
$doctormanage_resp=$row['doctor_resp'];    




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
$fan_speed2 = $row['fan_speed2'];  
$fan_speed3 = $row['fan_speed3'];  
$fan_speed4 = $row['fan_speed4'];  
$fan_speed5 = $row['fan_speed5'];  
$fan_speed6 = $row['fan_speed6'];  
$fan_speed7 = $row['fan_speed7'];  
$fan_button = $row['fan_button'];  



//cooker
                                           $cooker_timer = $row['cooker_timer_1'];  
                                            $cooking_temperature = $row['cooker_max_temperature_1'];  
                                            $Keep_warm= $row['Keep_warm_1'];  
                                            $Pre_timer_constant_mode = $row['Pre_timer_constant_mode'];  
                                            $Pre_timer = $row['Pre_timer'];  
                                            $Pre_temperature = $row['Pre_temperature'];  
                                           // $Coooker_max_temperature = (int)$row['Coooker_max_temperature'];  
                                              $Coooker_max_temperature = (int)$row['cook_max_temp'];
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
$LOCK_ENABLE_DISABLE = $row['LOCK_ENABLE_DISABLE'];  















/*************************************************************/


$query = "SELECT * FROM product_api WHERE model_name = '$model_name'";
$result = $db->get_results($query);
$num = count($result); // Count the number of rows in the results
$row = $result; // Get the first row of the results

/*------------------------------------------------------------*/

$frames = array();

        foreach ($result as $row) {
            $frames[] = array(
                "name" => $row["cmd_fieldDescription"],
            );
        }




$additionalData = array(
    "url" => "action=auto_get_nodes_params",
    "token" => $access_token,
    "node_id"=>$nodeid,
    "device_id"=>$deviceid,
     "payload"=>$payload
);

        



 
$query_1 = "SELECT parameter_name FROM product_api WHERE model_name = '$model_name' AND page_names= '$pagename'";
        $User_nodes_doc = $db->get_results($query_1); 
        $num_11 = $db->num_rows($query_1);
        $row = $db->get_row($query_1); 
        $parameter_name = $row['parameter_name']; 
         
 
$param1=$param['parameter_name'];

 

$device = [];


foreach ($User_nodes_doc as $param) {
    $param2=$param['parameter_name'];
 //  echo $param2." ";
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

            $resp_dataType = explode(',', $row_param['resp_dataType']);
            $resp_uiType = explode(',', $row_param['resp_uiType']);
            $resp_defaultvalue = explode(',', $row_param['resp_defaultvalue']);
            $resp_asperui = explode(',', $row_param['resp_asperui']);
            $resp_asperui_values = explode(',', $row_param['resp_asperui_values']);

            $error_dataType = explode(',', $row_param['error_dataType']);
            $error_uiType = explode(',', $row_param['error_uiType']);
            $error_defaultvalue = explode(',', $row_param['error_defaultvalue']);
            $error_asperui = explode(',', $row_param['error_asperui']);
            $error_asperui_values = explode(',', $row_param['error_asperui_values']);


        $selected = $row_param['selected'];


        
        for ($i = 0; $i < $cmd_framesize; $i++) {
         if($cmd_fielddescription[$i] == 'Data'){
         
          $default_name = $cmd_defaultvalue[$i];


     /*     
   if($cmd_uitype[$i] == "Toggle Switch"){
   $value = "Toogle";
   }elseif($cmd_uitype[$i] == "Slider"){
   $value = "Slider";
   }elseif($cmd_uitype[$i] == "Text"){
   $value = "Text";
   }elseif($cmd_uitype[$i] == "Drop Down"){
   $value = "Drop Down";
   }else{
   $value = " ";
   }*/
 if ($parameter_name =='Speed') {
    $as_per_ui_value =  (int)$speed;
}

 elseif ($parameter_name =='power') {
      $as_per_ui_value = (bool)$power;
}  



elseif ($parameter_name =='BAUDRATE') {
    $as_per_ui_value =  "115200";
}   elseif ($parameter_name =='Direction') {
    $as_per_ui_value =  $direction;
 //     $as_per_ui_value =  '8';

}   elseif ($parameter_name =='Led') {
    $as_per_ui_value = (bool)$led;
}
elseif ($parameter_name =='Led Color') {
    $as_per_ui_value = (int)$hue;
}
 elseif ($parameter_name =='name') {


 if($name_dev == 'NULL')
{
$curr_name=$curr_devicetype;
}
else{
$curr_name=$name_dev;
}

$as_per_ui_value =  $curr_name;   
  //  $as_per_ui_value =  $name_dev;
} elseif ($parameter_name =='Current') {
    $as_per_ui_value =  (float)$current;
} elseif ($parameter_name =='Voltage') {
    $as_per_ui_value =  $bus_voltage;
  //  $as_per_ui_value = (float)$voltage;
} elseif ($parameter_name =='rpm') {
    $as_per_ui_value = (int)$rpm;
} elseif ($parameter_name =='Watt') {
    $as_per_ui_value =  $Watt;
} 
elseif ($parameter_name =='Skipmode') {
    $as_per_ui_value =  true;
}

elseif ($parameter_name =='Cooker mode') {
    $as_per_ui_value =  $cookermode;
}
elseif ($parameter_name =='Recipee') {
    $as_per_ui_value =  $recipe;
}

elseif ($parameter_name =='remaining time') {
    $as_per_ui_value =  $cooktime;
}
elseif ($parameter_name =='running temperature') {
    $as_per_ui_value =  $cook_run_temp;
}
elseif ($parameter_name =='Nurse') {
    $as_per_ui_value = $nurse;
}
elseif ($parameter_name =='Nurse_2') {
    $as_per_ui_value = $nurse_resp;
}
elseif ($parameter_name =='Doctor') {
    $as_per_ui_value = $doctormanage;
}


elseif ($parameter_name =='Doctor_2') {
    $as_per_ui_value = $doctormanage_resp;
}
elseif ($parameter_name =='PA_2') {
    $as_per_ui_value = $personal_assistant_resp;
}
elseif ($parameter_name =='PA') {
    $as_per_ui_value = $personal_assistant;
}
elseif ($parameter_name =='Auto Clean') {

    $as_per_ui_value = $hood_autoclean;
}


elseif ($parameter_name =='Air Flow') {

    $as_per_ui_value = $hood_speed;
}

elseif ($parameter_name =='Lamp') {

    $as_per_ui_value = $hood_lamp;

}

else {
   $as_per_ui_value =''; 
}


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

else if ($cmd_defaultvalue[$i] =='Recipee') {
    $as_per_ui_value =  $recipe;
}




 if (!isset($device[$curr_devicetype])) {
        $device[$curr_devicetype] = [];
    }

    if (!isset($device[$curr_devicetype][$pagename])) {
        $device[$curr_devicetype][$pagename] = [];
    }
    
    if (!empty($default_name)) {
            $device[$curr_devicetype][$pagename][$default_name] = $as_per_ui_value;
        }

} 
  
}
}


$allData = array_merge($additionalData, $device);
echo json_encode($allData);





//echo json_encode($device);





exit;






 




/**************************************************************/
//}

}

/***********************************************************************************/


/********************user_update_ble_nodes_params************************************/

if (isset($_GET['action']) && $_GET['action']=='user_update_ble_nodes_params') {

$nodeid =$_GET['node_id'];
$deviceid =$_POST['device_id'];

$header = apache_request_headers();  
foreach ($header as $key => $val) {


$query = "SELECT * FROM efactorproduct where access_token='$val' AND node_id='$nodeid'";  
$doctors = $db->get_results($query); 
//$num_dev = $db->num_rows($query);
$row = $db->get_row($query); 
$device_type=$row['device_type'];
$addgateway=$row['add_gateway'];
$to=$row['mob_device_token'];
$name=$row['name'];
$user_id=$row['user_id'];
//date_default_timezone_set('Asia/Kolkata');
//$timestamp = time();
$timestamp=round(microtime(true) * 1000);
//$dt=date("Y-m-d H:i:s");
/***********************************************/

/***********************************************/


if($device_type == 'fan')
{

/*$speed=$_POST['Speed'];
$name=$_POST['name'];
//$device_name=$_POST['device_name'];
 
$baudrate=$_POST['Baud_Rate'];
$errorcode=$_POST['Error_Code'];
$comm_mode=$_POST['Communication_Mode'];
$uartmsgid=$_POST['Uart_Msg_Id'];
$time=$_POST['Time'];
$services=$_POST['services'];*/


$payload=$_POST['payload'];

/***************************************/

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
       case '06':
            $speed = '6';
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
        default:
            $hue = '';
            break;
    }

    if ($individualBytes[6] == '00') {
        $led_indication = 'true';
    } else if ($individualBytes[6] == '01') {
        $led_indication = 'false';
    }

   /* $rpm = $individualBytes[11] . $individualBytes[12];
    $power_wat = $individualBytes[13] . $individualBytes[14] . $individualBytes[15] . $individualBytes[16];
    $power_float = hexToFloat($power_wat);
    $current = $individualBytes[17] . $individualBytes[18] . $individualBytes[19] . $individualBytes[20];
    $current_float = hexToFloat($current);
    $bus_voltage = $individualBytes[21] . $individualBytes[22] . '00' . '00';
    $bus_voltage_float = hexToFloat($bus_voltage);
    $ac = $individualBytes[23] . $individualBytes[24];
    $ac_float = hexToFloat($ac);*/


    $db->query("update efactorproduct set power_watt='$power', current='$current_float', bus_voltage='$bus_voltage_float', ac_volt='$ac_float', power='$power', speed='$speed', Hue='$hue', led_indication='$led_indication' where node_id='$nodeid'");
$response['status']='success';
}

/*************************************/



 else if($payload == "A1C2010A")
   {
   
       $db->query("update efactorproduct set power='true' where node_id='$nodeid'");  
       
        $response['status']='success';
   }

   else if($payload == "A1C2000A")
   {
    
     $db->query("update efactorproduct set power='false' where node_id='$nodeid'");
    $response['status']='success';

   }

   else if($payload == "A1C0010A")
   {
    
     $db->query("update efactorproduct set speed='1' where node_id='$nodeid'");
     
      $response['status']='success';

   }

   else if($payload == "A1C0020A")
   {
    
     $db->query("update efactorproduct set speed='2' where node_id='$nodeid'");

     $response['status']='success';

      
   }

   else if($payload == "A1C0030A")
   {
    
     $db->query("update efactorproduct set speed='3' where node_id='$nodeid'");

    $response['status']='success';
 
      
   }


   else if($payload == "A1C0040A")
   {
    
     $db->query("update efactorproduct set speed='4' where node_id='$nodeid'");

     $response['status']='success';

      
   }

   else if($payload == "A1C0050A")
   {
    
     $db->query("update efactorproduct set speed='5' where node_id='$nodeid'");

      $response['status']='success';

   }

 else if($payload == "A1C3000A")
   {
    
     $db->query("update efactorproduct set Hue='0' where node_id='$nodeid'");
     $db->query("update efactorproduct set led_indication='true' where node_id='$nodeid'");
     $response['status']='success';

      
   }

  else if($payload == "A1C3010A")
   {
    
     $db->query("update efactorproduct set Hue='1' where node_id='$nodeid'");
     $db->query("update efactorproduct set led_indication='true' where node_id='$nodeid'");
     $response['status']='success';

      
   }

  else if($payload == "A1C3020A")
   {
    
     $db->query("update efactorproduct set Hue='2' where node_id='$nodeid'");
     $db->query("update efactorproduct set led_indication='true' where node_id='$nodeid'");
    $response['status']='success';

      
   }
   else if($payload == "A1C3030A")
   {
    
     $db->query("update efactorproduct set Hue='3' where node_id='$nodeid'");
     $db->query("update efactorproduct set led_indication='true' where node_id='$nodeid'");
     
     $response['status']='success';
 
   }

  else if($payload == "A1C3040A")
   {
    
     $db->query("update efactorproduct set Hue='4' where node_id='$nodeid'");
     $db->query("update efactorproduct set led_indication='true' where node_id='$nodeid'");
    
     $response['status']='success';
 
   }
   else if($payload == "A1C3050A")
   {
    
    $db->query("update efactorproduct set Hue='5' where node_id='$nodeid'");
    $db->query("update efactorproduct set led_indication='true' where node_id='$nodeid'");
    $response['status']='success';

   }

   else if($payload == "A1C3060A")
   {
    
     $db->query("update efactorproduct set Hue='6' where node_id='$nodeid'");
     $db->query("update efactorproduct set led_indication='true' where node_id='$nodeid'");
     
     $response['status']='success';
 
   }


 else if($payload == "A1C3070A")
   {
    
     $db->query("update efactorproduct set led_indication='false' where node_id='$nodeid'");
     
     $response['status']='success';

   }


else if($payload == "A1E70A")
   {
    
    // $db->query("update efactorproduct set led_indication='false' where node_id='$nodeid'");
     
     $response['status']='success';

   }


else if($payload == "A1CB010A")
   {
   
       $db->query("update efactorproduct set power='true' where node_id='$nodeid'");  
       $param='power'; 
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
        'event_data_payload' => '{"event_version":"v1","id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $name . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     


$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

     


   }

   else if($payload == "A1CB000A")
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
        'event_data_payload' => '{"event_version":"v1","id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $name . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;
    





   }


else if($payload == "A1CB010A")
   {
    
     $db->query("update efactorproduct set speed='1' where node_id='$nodeid'");
     $db->query("update efactorproduct set power='true' where node_id='$nodeid'");
     $param='power,Speed';
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
        'event_data_payload' => '{"event_version":"v1","id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $name . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     

$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
//$to=null;
         
   }
  

 else if($payload == "A1CB020A")
   {
    
     $db->query("update efactorproduct set speed='2' where node_id='$nodeid'");
     $db->query("update efactorproduct set power='true' where node_id='$nodeid'");
     $param='power,Speed';
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
        'event_data_payload' => '{"event_version":"v1","id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $name . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
//$to=null;

      
   }



else if($payload == "A1CB030A")
   {
    
     $db->query("update efactorproduct set speed='3' where node_id='$nodeid'");
     $db->query("update efactorproduct set power='true' where node_id='$nodeid'");
     $param='power,Speed';
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
        'event_data_payload' => '{"event_version":"v1","id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $name . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     

$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
//$to=null;

   }




else if($payload == "A1CB040A")
   {
    
     $db->query("update efactorproduct set speed='4' where node_id='$nodeid'");
     $db->query("update efactorproduct set power='true' where node_id='$nodeid'");
     $param='power, Speed';
     $value='4';

     
     $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":"v1","id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $name . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     


$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
//$to=null;

}



 else if($payload == "A1CB050A")
   {
    
     $db->query("update efactorproduct set speed='5' where node_id='$nodeid'");
      $db->query("update efactorproduct set power='true' where node_id='$nodeid'");
     $param='power,Speed';
     $value='5';

     $url = 'https://fcm.googleapis.com/fcm/send';
$update ='updated';
$space=' ';
$description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  $description,
        'event_data_payload' => '{"event_version":"v1","id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $name . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     


$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
//$to=null;
$payload=null;

}



else if($payload == "A1CC070A")
   {
    
     $db->query("update efactorproduct set led_indication='false' where node_id='$nodeid'");
     $param='led';
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
        'event_data_payload' => '{"event_version":"v1","id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $name . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
$to=null;

}




else if($payload == "A1CC000A" || $payload == "A1CC010A" || $payload == "A1CC020A" || $payload == "A1CC030A" || $payload == "A1CC040A" || $payload == "A1CC050A" || $payload == "A1CC060A")
   {
    
     $db->query("update efactorproduct set led_indication='false' where node_id='$nodeid'");
      $param='led';
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
        'event_data_payload' => '{"event_version":"v1","id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $name . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
//$to=null;

    
   }




else if($payload == "A1C3070A" )
   {
    
     $db->query("update efactorproduct set led_indication='true' where node_id='$nodeid'");
      $param='led';
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
        'event_data_payload' => '{"event_version":"v1","id":"1","event_type":"efactor.event.node_params_changed","timestamp":' . $timestamp . ',"description":"' . $description . '","event_data":{"user_id":"' . $user_id . '","node_id":"' . $nodeid . '","payload":"{\\"' . $name . '\\":{\\"' . $param . '\\":' . $value . '}}"}}'
    )
);
     
$name=null;
$nodeid=null;
$param=null;
$value=null;
//$data=null;
$payload=null;
//$to=null;
     
   }


else {

echo 'payload not matched';

$name=null;
$nodeid=null;
$param=null;
$value=null;
$data=null;
$payload=null;
$to=null;
$url=null;
}



}



/*
else
{

        header('HTTP/1.1 400');
        $response['status']='failure';
        $response['error_code']='101112';
        $response['description']='failed to update';

}

*/
print_r($data);

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

// execute the curl session
$response = curl_exec($curl);

// check for errors
if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
    // handle the error
     }

// close the curl session
curl_close($curl);



    }



}




/************************************************************************************/

/*-------------------------------------getdevices--------------------------------------*/

  if (isset($_GET['action']) && $_GET['action']=='getdevices') {


$header = apache_request_headers();  
foreach ($header as $key => $val) {

$query = "SELECT * FROM efactorproduct where access_token='$val'";  
$doctors = $db->get_results($query); 
$num_dev = $db->num_rows($query_dev);
$arr=array();

foreach ($doctors as $key => $doctor) 
  {
    
    array_push($arr,array("id"=>$doctor['id'],"node_id"=>$doctor['node_id']));
    //array_push($arr,array("node_id"=>$doctor['node_id']));


  }

  if(!empty($arr))
  {


$startid=FFFFFFFFFFFFFFFF8200000000000700;


for ($pos = 0; $pos <= 2; $pos++) 
{
//echo $node_id;

if($arr[$pos]['node_id']==$startid)
{
echo 'array_success';
echo $pos;
}

}


   //$response['status']='success';
   // $response['devices']=$arr;

  }
 
//headers_remove("$Autherization");
 }

  }

/*****************************************************************************/

/*******************user_push_notification_mobile_platform_endpoint**************/


if (isset($_GET['action']) && $_GET['action']=='user_push_notification_mobile_platform_endpoint') 

{

$req = file_get_contents( 'php://input' ); 
$json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['platform']) && isset($json_data['mobile_device_token'])) {
    $platform = $json_data['platform'];
    $devicetoken = $json_data['mobile_device_token'];
}

/*$platform =$_POST['platform'];
$devicetoken =$_POST['mobile_device_token'];

echo $devicetoken;*/


$header = apache_request_headers();  
	foreach ($header as $key => $val) {


$db->query("update efactorproduct set mob_device_token='$devicetoken' where access_token='$val'");
$db->query("update users set mob_device_token='$devicetoken' where access_token='$val'");
$response['status']='successfully updated payload';

//$resp = '{"platform_endpoint_arn":"arn:aws:sns:us-east-1:315787942180:endpoint/GCM/GCM/7466ef02-21f4-3293-9e0a-40e94882a1f6","status":"success"}';
//echo $resp;

}

}






/*****************************user_node_status******************************************/


if (isset($_GET['action']) && $_GET['action']=='user_node_status') 

{

$nodeid = $_GET['node_id'];

    $req = file_get_contents('php://input');
    
    $json_data = json_decode($req, true);

   
    if (is_array($json_data) && isset($json_data['latitude'])) {
        $latitude = $json_data['latitude'];
    } else {
        $latitude = '';
    }


    if (is_array($json_data) && isset($json_data['longitude'])) {
        $longitude = $json_data['longitude'];
    } else {
        $longitude = '';
    }



 $query = "SELECT * FROM efactorproduct where node_id='".$_GET['node_id']."'";
 
$num = $db->num_rows($query); 
$row = $db->get_row($query);
$time_stamp=$row['timestamp']; 
$moduletype=$row['module_type'];
$net_conn=$row['internet'];
$timestamp_1=strtotime("now");


 if($num>0)
{

/*$connectivity =array("connected"=>true,
                     "timestamp"=>$timestamp_1);
$connectivity_1[] = json_decode($connectivity, true);
$status_1 = array("connectivity" => $connectivity);
$user_node_status = json_encode(["status"=>$status_1]);
echo $user_node_status;*/

$db->query("update efactorproduct set node_latitude='$latitude' where node_id='$nodeid'");
        $db->query("update efactorproduct set node_longitude='$longitude' where node_id='$nodeid'");


/**********************************************************/


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$nodeid."/".$command;




if($moduletype == 'Gateway')
{
//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"address"=>$nodeid,
"device_id"=>$nodeid,
"payload"=>'A1E70A');
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
}

else if($moduletype == 'WIFI')
{
//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"address"=>$nodeid,
"device_id"=>$nodeid,
"payload"=>'A1E70A');
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
}




$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           // exit($mqtt->publish($topic,$mqtt_json_1));
$mqtt->publish($topic,$mqtt_json_1);
       }




/************************************************************************/


$connectivity = array("connected" => ($net_conn === "true") ? true : false, "timestamp" => $timestamp_1);
$connectivity_1 = $connectivity; 
$status_1 = json_encode(["connectivity" => $connectivity]); 
//$user_node_status = json_encode(["status" => $status_1]);
//exit($user_node_status);

echo $status_1;

exit;


}
else 
{

        header('HTTP/1.1 400');
         $response['status']='failure';
         $response['error_code']='103008';
         $response['description']='user node node not exist';
  
exit;
}



}
/****************************remove_node*******************************/



if (isset($_GET['action']) && $_GET['action']=='remove_node') {


     $header = apache_request_headers();  
     foreach ($header as $key => $val) {
     
     $req = file_get_contents( 'php://input' );
    $json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['node_id'])) {
    $nodeid = $json_data['node_id'];
   
}


    // $nodeid=$_POST['node_id'];
     //echo $nodeid;
     $query = "SELECT * FROM efactorproduct where node_id='$nodeid'";
     $num = $db->num_rows($query); 
     $row = $db->get_row($query);
     $node_id_1=$row['node_id'];
     $moduletype=$row['module_type'];
     $model_number=$row['model_number'];
     $addgateway=$row['add_gateway'];


//echo $model_number;

if($num>0)
       { 


if($moduletype == 'Gateway' || $moduletype == 'WIFI')

{

if($model_number == 'EFDEFLT V1.0')
{
         $db->query("update efactorproduct set secret_key='NULL'where node_id='$nodeid'");
         $db->query("update efactorproduct set request_id='NULL' where node_id='$nodeid'");
         $db->query("update efactorproduct set access_token='NULL' where node_id='$nodeid'");
         $db->query("update efactorproduct set req_status='NULL' where node_id='$nodeid'");
         $db->query("update efactorproduct set mob_device_token='NULL' where node_id='$nodeid'");
         $db->query("update efactorproduct set secret_key='NULL' where node_id='$nodeid'");
         $db->query("update efactorproduct set name='NULL' where node_id='$nodeid'");
         $db->query("update efactorproduct set user_id='NULL' where node_id='$nodeid'");
         $db->query("update efactorproduct set add_gateway='NULL' where node_id='$nodeid'");
         $db->query("update efactorproduct set is_added='NULL' where node_id='$nodeid'");

}

else{


         $db->query("update efactorproduct set secret_key='NULL'where node_id='$nodeid'");
         $db->query("update efactorproduct set request_id='NULL' where node_id='$nodeid'");
         $db->query("update efactorproduct set access_token='NULL' where node_id='$nodeid'");
         $db->query("update efactorproduct set req_status='NULL' where node_id='$nodeid'");
         $db->query("update efactorproduct set mob_device_token='NULL' where node_id='$nodeid'");
         $db->query("update efactorproduct set secret_key='NULL' where node_id='$nodeid'");
         $db->query("update efactorproduct set name='NULL' where node_id='$nodeid'");
         $db->query("update efactorproduct set user_id='NULL' where node_id='$nodeid'");
         $db->query("update efactorproduct set add_gateway='NULL' where node_id='$nodeid'");
         $db->query("update efactorproduct set is_added='NULL' where node_id='$nodeid'");


         $db->query("update `$model_number` set secret_key='NULL'where node_id='$nodeid'");
         $db->query("update `$model_number` set request_id='NULL' where node_id='$nodeid'");
         $db->query("update `$model_number` set access_token='NULL' where node_id='$nodeid'");
         $db->query("update `$model_number` set req_status='NULL' where node_id='$nodeid'");
         $db->query("update `$model_number` set mob_device_token='NULL' where node_id='$nodeid'");
         $db->query("update `$model_number` set secret_key='NULL' where node_id='$nodeid'");
         $db->query("update `$model_number` set name='NULL' where node_id='$nodeid'");
         $db->query("update `$model_number` set user_id='NULL' where node_id='$nodeid'");
         $db->query("update `$model_number` set add_gateway='NULL' where node_id='$nodeid'");
         $db->query("update `$model_number` set is_added='NULL' where node_id='$nodeid'");

}

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$nodeid."/".$command;


if($moduletype == 'Gateway')
{
//echo $topic;
$mqtt_json=array("command_type"=> "SET_DEVICE_STATUS", 
"address"=>$nodeid,
"device_id"=>$nodeid,
"payload"=>'F0F1');
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
}

else if($moduletype == 'WIFI')
{
//echo $topic;
$mqtt_json=array("command_type"=> "SET_DEVICE_STATUS", 
"address"=>$nodeid,
"device_id"=>$nodeid,
"payload"=>'F0');
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
}




$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }



      

       //   $response['status']='Successfully deleted node from your account';
 


}

else
{



$db->query("update efactorproduct set secret_key='NULL'where node_id='$nodeid'");
$db->query("update efactorproduct set request_id='NULL' where node_id='$nodeid'");
$db->query("update efactorproduct set access_token='NULL' where node_id='$nodeid'");
$db->query("update efactorproduct set req_status='NULL' where node_id='$nodeid'");
$db->query("update efactorproduct set mob_device_token='NULL' where node_id='$nodeid'");
$db->query("update efactorproduct set add_gateway='NULL' where node_id='$nodeid'");
$db->query("update efactorproduct set name='NULL' where node_id='$nodeid'");
$db->query("update efactorproduct set add_gateway='NULL' where node_id='$nodeid'");
$db->query("update efactorproduct set is_added='NULL' where node_id='$nodeid'");

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$addgateway."/".$command;



//echo $topic;
$mqtt_json=array("command_type"=> "SET_DEVICE_STATUS", 
"address"=>$addgateway,
"device_id"=>$nodeid,
"payload"=>'F0');
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }



      

       //   $response['status']='Successfully deleted node from your account';
 




}










     


     
         

      }
    else
   {

     header('HTTP/1.1 400');
     $response['status']='failure';
     $response['error_code']='100010'; 
     $response['description']='Node does not belong to user';
   }

}


}

/*******************************set_hours******************************/

if (isset($_GET['action']) && $_GET['action']=='set_hours') {


     $header = apache_request_headers();  
     foreach ($header as $key => $val) {
     
     $nodeid=$_POST['node_id'];
     $query = "SELECT * FROM efactorproduct where node_id='".$_POST['node_id']."'";
     $num = $db->num_rows($query); 
     $row = $db->get_row($query);
     $node_id_1=$row['node_id'];
     $moduletype=$row['module_type'];
     $addgateway=$row['add_gateway'];
     $payload_hours=$_POST['payload'];



//$hex = dechex($payload_hours);
$hex = sprintf('%04x', $payload_hours);


//echo $payload_hours;
//echo $hex;

$payload_cmd = 'A1A6' . $hex . '0A';

//echo $payload_cmd;

if($num>0)
       { 


if($moduletype == 'Gateway' || $moduletype == 'WIFI')

{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$nodeid."/".$command;



//echo $topic;
$mqtt_json=array("command_type"=> "SET_DEVICE_STATUS", 
"address"=>$nodeid,
"device_id"=>$nodeid,
"payload"=>$payload_cmd);
$mqtt_json_1=json_encode($mqtt_json);
echo json_encode($mqtt_json)."\n";

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }



      

       //   $response['status']='Successfully deleted node from your account';
 


}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$addgateway."/".$command;



echo $topic;
$mqtt_json=array("command_type"=> "SET_DEVICE_STATUS", 
"address"=>$addgateway,
"device_id"=>$nodeid,
"payload"=>$payload_cmd);
$mqtt_json_1=json_encode($mqtt_json);
echo json_encode($mqtt_json)."\n";

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }



      

       //   $response['status']='Successfully deleted node from your account';
 




}










     


     
         

      }
    else
   {

     header('HTTP/1.1 400');
     $response['status']='failure';
     $response['error_code']='100010'; 
     $response['description']='Node does not belong to user';
   }

}


}






/*****************************add_node********************************/



if (isset($_GET['action']) && $_GET['action']=='add_node') {




    $req = file_get_contents( 'php://input' ); 
    $json_data = json_decode($req, true);




   if (is_array($json_data) && isset($json_data['secret_key']) && isset($json_data['node_id']) && isset($json_data['operation'])) {
    $secret_key = $json_data['secret_key'];

if(empty($secret_key))
{
header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    
    $response = [
        'status' => 'failure',
        'error_code' => '100010',
        'description' => 'empty secret_key'
    ];
    
    echo json_encode($response);
    exit;


}
    $nodeid = $json_data['node_id'];
    $operation = $json_data['operation'];

if(empty($nodeid))
{


header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    
    $response = [
        'status' => 'failure',
        'error_code' => '100010',
        'description' => 'empty node_id'
    ];
    
    echo json_encode($response);
    exit;



}

if(empty($operation))
{

header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    
    $response = [
        'status' => 'failure',
        'error_code' => '100010',
        'description' => 'empty operation'
    ];
    
    echo json_encode($response);
    exit;





}



   } else {



header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json');
    
    $response = [
        'status' => 'failure',
        'error_code' => '100010',
        'description' => 'Required keys are missing in the JSON data'
    ];
    
    echo json_encode($response);
    exit;



}





     $header = apache_request_headers();  
     foreach ($header as $key => $val) {


     //$secret_key=$_POST['secret_key'];
     //$nodeid=$_POST['node_id'];
     $query = "SELECT * FROM efactorproduct where node_id='$nodeid'";
     $num = $db->num_rows($query); 
     $row = $db->get_row($query);
     $node_id_1=$row['node_id'];
     $row = $db->get_row($query);
     //$secretkey=$row['secret_key'];


     $query_usr = "SELECT * FROM users where access_token='$val'";
     $num_usr = $db->num_rows($query_usr); 
     $row_usr = $db->get_row($query_usr);
     $userid=$row_usr['id'];
     $mobiledevicetoken=$row_usr['mob_device_token'];
     $phone=$row_usr['phone'];
     $email=$row_usr['email'];




      $query_device = "SELECT * FROM efactorproduct where node_id='$nodeid'";
      $doctors = $db->get_results($query_device);
      $row_device = $db->get_row($query_device);
      $devicename=$row_device['device_type'];
      $devicetype=$row_device['device_type']; 
      $nodeid=$row_device['node_id'];
      $secretkey=$row_device['secret_key'];
      $request_id=$row_device['request_id'];
      $model_number=$row_device['model_number'];

     if($num>0)
       { 

      
      
if ($secretkey == $secret_key) {



         
if ($request_id !='NULL') {




 if($model_number === 'EFDEFLT V1.0' || $model_number === 'Gateway')
{



      $db->query("update efactorproduct set secret_key='$secret_key' where node_id='$nodeid'");
       $db->query("update efactorproduct set request_id='$requestid' where node_id='$nodeid'");
       $db->query("update efactorproduct set access_token='$val' where node_id='$nodeid'");
       $db->query("update efactorproduct set user_id='$userid' where node_id='$nodeid'");
       $db->query("update efactorproduct set mob_device_token='$mobiledevicetoken' where node_id='$nodeid'");
       $db->query("update efactorproduct set name='$devicename' where node_id='$nodeid'");
       $db->query("update efactorproduct set time='$dt' where node_id='$nodeid'");
       $db->query("update efactorproduct set current_devicetype='$deviceType' where node_id='$nodeid'");
       $db->query("update efactorproduct set phone='$phone' where node_id='$nodeid'");
       $db->query("update efactorproduct set email='$email' where node_id='$nodeid'");


    /*   $db->query("update efactorproduct set SUID='$nodeid' where node_id='$nodeid'");
       $db->query("update efactorproduct set DUID='0000000000000000' where node_id='$nodeid'");
       $db->query("update efactorproduct set UDP_CMD='DA' where node_id='$nodeid'");
       $db->query("update efactorproduct set UDP_ACK='01' where node_id='$nodeid'");
       $db->query("update efactorproduct set MEDIA='02' where node_id='$nodeid'");
       $db->query("update efactorproduct set Frame='00' where node_id='$nodeid'");
       $db->query("update efactorproduct set FID='00' where node_id='$nodeid'");
       $db->query("update efactorproduct set UserID='$userid' where node_id='$nodeid'");
       $db->query("update efactorproduct set SDUID_Enable='D400' where node_id='$nodeid'");
       $db->query("update efactorproduct set UID_DATA='00' where node_id='$nodeid'");*/






}

else{



   $db->query("update `$model_number` set secret_key='$secret_key' where node_id='$nodeid'");
       $db->query("update `$model_number` set request_id='$requestid' where node_id='$nodeid'");
       $db->query("update `$model_number` set access_token='$val' where node_id='$nodeid'");
       $db->query("update `$model_number` set user_id='$userid' where node_id='$nodeid'");
       $db->query("update `$model_number` set mob_device_token='$mobiledevicetoken' where node_id='$nodeid'");
       $db->query("update `$model_number` set name='$deviceType' where node_id='$nodeid'");
       $db->query("update `$model_number` set time='$dt' where node_id='$nodeid'");
       $db->query("update `$model_number` set current_devicetype='$deviceType' where node_id='$nodeid'");
       $db->query("update `$model_number` set phone='$phone' where node_id='$nodeid'");
       $db->query("update `$model_number` set email='$email' where node_id='$nodeid'");



      $db->query("update efactorproduct set secret_key='$secret_key' where node_id='$nodeid'");
       $db->query("update efactorproduct set request_id='$requestid' where node_id='$nodeid'");
       $db->query("update efactorproduct set access_token='$val' where node_id='$nodeid'");
       $db->query("update efactorproduct set user_id='$userid' where node_id='$nodeid'");
       $db->query("update efactorproduct set mob_device_token='$mobiledevicetoken' where node_id='$nodeid'");
       $db->query("update efactorproduct set name='$devicename' where node_id='$nodeid'");
       $db->query("update efactorproduct set time='$dt' where node_id='$nodeid'");
       $db->query("update efactorproduct set current_devicetype='$deviceType' where node_id='$nodeid'");
       $db->query("update efactorproduct set phone='$phone' where node_id='$nodeid'");
       $db->query("update efactorproduct set email='$email' where node_id='$nodeid'");  //17-05-25


    /*   $db->query("update efactorproduct set SUID='$nodeid' where node_id='$nodeid'");
       $db->query("update efactorproduct set DUID='0000000000000000' where node_id='$nodeid'");
       $db->query("update efactorproduct set UDP_CMD='DA' where node_id='$nodeid'");
       $db->query("update efactorproduct set UDP_ACK='01' where node_id='$nodeid'");
       $db->query("update efactorproduct set MEDIA='02' where node_id='$nodeid'");
       $db->query("update efactorproduct set Frame='00' where node_id='$nodeid'");
       $db->query("update efactorproduct set FID='00' where node_id='$nodeid'");
       $db->query("update efactorproduct set UserID='$userid' where node_id='$nodeid'");
       $db->query("update efactorproduct set SDUID_Enable='D400' where node_id='$nodeid'");
       $db->query("update efactorproduct set UID_DATA='00' where node_id='$nodeid'");*/












}

   

  //$response['request_id']=$requestid;

    $response['request_id'] = $request_id;
    echo json_encode($response);


exit;



}
}
          else
           {

            
//$timestamp=round(microtime(true) * 1000);
$dt=date("Y-m-d H:i:s");

$segment9 = substr($nodeid, 16, 2);
$segment13 = substr($nodeid, 24, 2);
$segment14 = substr($nodeid, 26, 2);
$segment15 = substr($nodeid, 28, 2);

$underscore='_';
    

$deviceType = $devicetype.$underscore. $segment9 . $segment13 . $segment14 . $segment15;

     
       $requestid = rand(100000,999999); 



      if($model_number === 'EFDEFLT V1.0' || $model_number === 'Gateway')
{



      $db->query("update efactorproduct set secret_key='$secret_key' where node_id='$nodeid'");
       $db->query("update efactorproduct set request_id='$requestid' where node_id='$nodeid'");
       $db->query("update efactorproduct set access_token='$val' where node_id='$nodeid'");
       $db->query("update efactorproduct set user_id='$userid' where node_id='$nodeid'");
       $db->query("update efactorproduct set mob_device_token='$mobiledevicetoken' where node_id='$nodeid'");
       $db->query("update efactorproduct set name='$devicename' where node_id='$nodeid'");
       $db->query("update efactorproduct set time='$dt' where node_id='$nodeid'");
       $db->query("update efactorproduct set current_devicetype='$deviceType' where node_id='$nodeid'");
       $db->query("update efactorproduct set phone='$phone' where node_id='$nodeid'");
       $db->query("update efactorproduct set email='$email' where node_id='$nodeid'");


       $db->query("update efactorproduct set SUID='$nodeid' where node_id='$nodeid'");
       $db->query("update efactorproduct set DUID='0000000000000000' where node_id='$nodeid'");
       $db->query("update efactorproduct set UDP_CMD='DA' where node_id='$nodeid'");
       $db->query("update efactorproduct set UDP_ACK='01' where node_id='$nodeid'");
       $db->query("update efactorproduct set MEDIA='02' where node_id='$nodeid'");
       $db->query("update efactorproduct set Frame='00' where node_id='$nodeid'");
       $db->query("update efactorproduct set FID='00' where node_id='$nodeid'");
       $db->query("update efactorproduct set UserID='$userid' where node_id='$nodeid'");
       $db->query("update efactorproduct set SDUID_Enable='D400' where node_id='$nodeid'");
       $db->query("update efactorproduct set UID_DATA='00' where node_id='$nodeid'");



/**********************************/
/*
 $db->query("
    UPDATE efactorproduct 
    SET 
        secret_key = '$secret_key',
        request_id = '$requestid',
        access_token = '$val',
        user_id = '$userid',
        mob_device_token = '$mobiledevicetoken',
        name = '$deviceType',
        time = '$dt',
        current_devicetype = '$deviceType',
        phone = '$phone',
        email = '$email',
        SUID = '$nodeid',
        DUID = '0000000000000000',
        UDP_CMD = 'DA',
        UDP_ACK = '01',
        MEDIA = '02',
        Frame = '00',
        FID = '00',
        UserID = '$userid',
        SDUID_Enable = 'D400',
        UID_DATA = '00'
    WHERE node_id = '$nodeid'
");



*/





/**********************/

}



else


{



     
/************************/



  $db->query("update efactorproduct set secret_key='$secret_key' where node_id='$nodeid'");
       $db->query("update efactorproduct set request_id='$requestid' where node_id='$nodeid'");
       $db->query("update efactorproduct set access_token='$val' where node_id='$nodeid'");
      $db->query("update efactorproduct set user_id='$userid' where node_id='$nodeid'");
       $db->query("update efactorproduct set mob_device_token='$mobiledevicetoken' where node_id='$nodeid'");
       $db->query("update efactorproduct set name='$devicename' where node_id='$nodeid'");
       $db->query("update efactorproduct set time='$dt' where node_id='$nodeid'");
       $db->query("update efactorproduct set current_devicetype='$deviceType' where node_id='$nodeid'");
       $db->query("update efactorproduct set phone='$phone' where node_id='$nodeid'");
       $db->query("update efactorproduct set email='$email' where node_id='$nodeid'");


    /*   $db->query("update efactorproduct set SUID='$nodeid' where node_id='$nodeid'");
       $db->query("update efactorproduct set DUID='0000000000000000' where node_id='$nodeid'");
       $db->query("update efactorproduct set UDP_CMD='DA' where node_id='$nodeid'");
       $db->query("update efactorproduct set UDP_ACK='01' where node_id='$nodeid'");
       $db->query("update efactorproduct set MEDIA='02' where node_id='$nodeid'");
       $db->query("update efactorproduct set Frame='00' where node_id='$nodeid'");
       $db->query("update efactorproduct set FID='00' where node_id='$nodeid'");
       $db->query("update efactorproduct set UserID='$userid' where node_id='$nodeid'");
       $db->query("update efactorproduct set SDUID_Enable='D400' where node_id='$nodeid'");
       $db->query("update efactorproduct set UID_DATA='00' where node_id='$nodeid'");
*/







       $db->query("update `$model_number` set secret_key='$secret_key' where node_id='$nodeid'");
       $db->query("update `$model_number` set request_id='$requestid' where node_id='$nodeid'");
      $db->query("update `$model_number` set access_token='$val' where node_id='$nodeid'");
      $db->query("update `$model_number` set user_id='$userid' where node_id='$nodeid'");
       $db->query("update `$model_number` set mob_device_token='$mobiledevicetoken' where node_id='$nodeid'");
       $db->query("update `$model_number` set name='$deviceType' where node_id='$nodeid'");
       $db->query("update `$model_number` set time='$dt' where node_id='$nodeid'");
       $db->query("update `$model_number` set current_devicetype='$deviceType' where node_id='$nodeid'");
       $db->query("update `$model_number` set phone='$phone' where node_id='$nodeid'");
       $db->query("update `$model_number` set email='$email' where node_id='$nodeid'");   //17-05-25



   

/**********************/

}



       
       $response['request_id']=$requestid;

    //$response['request_id'] = $requestid;
    echo json_encode($response);

  exit;        
          }


 

     }
    else
   {

     header('HTTP/1.1 400');
     $response['status']='failure';
     $response['error_code']='100010'; 
     $response['description']='Node does not belong to user';
exit;
   }


}


}

















/*



if (isset($_GET['action']) && $_GET['action'] === 'add_node') {
    
    $req = file_get_contents('php://input');
    $json_data = json_decode($req, true);

        if (!is_array($json_data) || !isset($json_data['secret_key']) || !isset($json_data['node_id'])) {
        header('HTTP/1.1 400');
        echo json_encode(['status' => 'failure', 'error_code' => '100001', 'description' => 'Invalid input']);
        exit;
    }

    $secret_key = $json_data['secret_key'];
    $node_id = $json_data['node_id'];

   
    $log_message = "[" . date("Y-m-d H:i:s") . "] Received request for node_id: $node_id.\n";
    file_put_contents('/var/www/html/device_add_status.log', $log_message, FILE_APPEND);

    
    $headers = apache_request_headers();
    if (!isset($headers['access_token'])) {
        header('HTTP/1.1 401');
        echo json_encode(['status' => 'failure', 'error_code' => '100002', 'description' => 'Access token missing']);
        exit;
    }
    $access_token = $headers['access_token'];

    // Check if node_id exists
    $query = "SELECT * FROM efactorproduct WHERE node_id = '$node_id'";
    $device = $db->get_row($query);

    if (!$device) {
        header('HTTP/1.1 400');
        echo json_encode(['status' => 'failure', 'error_code' => '100010', 'description' => 'Node does not belong to user']);
        exit;
    }

    
    $request_id = $device['request_id'];
    $stored_secret_key = $device['secret_key'];

    
    if ($secret_key === $stored_secret_key && $request_id !== null) {
        echo json_encode(['request_id' => $request_id]);
        exit;
    }

    
    $new_request_id = rand(100000, 999999);

    // Extract user details using access_token
    $user_query = "SELECT * FROM users WHERE access_token = '$access_token'";
    $user = $db->get_row($user_query);

    if (!$user) {
        header('HTTP/1.1 401');
        echo json_encode(['status' => 'failure', 'error_code' => '100011', 'description' => 'Invalid access token']);
        exit;
    }

    $user_id = $user['id'];
    $mobile_device_token = $user['mob_device_token'];
    $phone = $user['phone'];
    $email = $user['email'];

    // Update database with the new request_id and other details
    $dt = date("Y-m-d H:i:s");
    $segment9 = substr($node_id, 16, 2);
    $segment13 = substr($node_id, 24, 2);
    $segment14 = substr($node_id, 26, 2);
    $segment15 = substr($node_id, 28, 2);
    $device_type = $device['device_type'] . "_" . $segment9 . $segment13 . $segment14 . $segment15;

    $update_queries = [
        "UPDATE efactorproduct SET secret_key = '$secret_key', request_id = '$new_request_id', access_token = '$access_token', user_id = '$user_id', mob_device_token = '$mobile_device_token', name = '{$device['device_type']}', time = '$dt', current_devicetype = '$device_type', phone = '$phone', email = '$email' WHERE node_id = '$node_id'",
        "UPDATE efactorproduct SET SUID = '$node_id', DUID = '0000000000000000', UDP_CMD = 'DA', UDP_ACK = '01', MEDIA = '02', Frame = '00', FID = '00', UserID = '$user_id', SDUID_Enable = 'D400', UID_DATA = '00' WHERE node_id = '$node_id'"
    ];

    foreach ($update_queries as $query) {
        $db->query($query);
    }

    // Log success
    $log_message = "[" . date("Y-m-d H:i:s") . "] Generated new request_id: $new_request_id for node_id: $node_id.\n";
    file_put_contents('/var/www/html/device_add_status.log', $log_message, FILE_APPEND);

    // Return the new request_id
    echo json_encode(['request_id' => $new_request_id]);
    exit;
}


*/






/**************************add_node_req_status************************************/
if (isset($_GET['action']) && $_GET['action'] == 'add_node_req_status') {

    // Sanitize inputs to prevent SQL injection
    $request_id = isset($_GET['request_id']) ? trim($_GET['request_id']) : null;
    $node_id = isset($_GET['node_id']) ? trim($_GET['node_id']) : null;



    // Check for missing required parameters
    if (!$request_id || !$node_id) {
        header('HTTP/1.1 400');
        $response['status'] = 'failure';
        $response['error_code'] = '100012';
        $response['description'] = 'Missing required parameters';
        echo json_encode($response);
        exit;
    }

    $query_status = "SELECT * FROM efactorproduct WHERE node_id = '$node_id'";
    $num_status = $db->num_rows($query_status);

    // Check if the query returned any results
    if ($num_status > 0) {

        $row_status = $db->get_row($query_status);
        $model_number = $row_status['model_number'];

        if ($model_number == 'EFDEFLT V1.0') {
            $response['request_status'] = 'confirmed';
            $db->query("UPDATE efactorproduct SET req_status = 'confirmed' WHERE node_id = '$node_id'");
        } else {
            $response['request_status'] = 'confirmed';
            $db->query("UPDATE `$model_number` SET req_status = 'confirmed' WHERE node_id = '$node_id'");
            $db->query("UPDATE efactorproduct SET req_status = 'confirmed' WHERE node_id = '$node_id'");
        }


    } else {
        // No matching rows in efactorproduct
        header('HTTP/1.1 400');
        $response['status'] = 'failure';
        $response['error_code'] = '100011';
        $response['description'] = 'Node not authorized';


    }
    // Output the response
    echo json_encode($response);

exit;

} 



/**********************************get_default_cred*************************************************/

if (isset($_GET['action']) && $_GET['action'] === 'get_default_cred_21-06-25') {
    header('Content-Type: application/json'); 
    $headers = getallheaders();
    $req = file_get_contents('php://input');
    $json_data = json_decode($req, true);

    $nodeid = $_GET['node_id'];
    
    $accessToken = $headers['Authorization'] ?? null;

    
    



   
    $query = "SELECT * FROM users WHERE access_token ='$accessToken'";
    $num = $db->num_rows($query);



    if ($num <= 0) {
        http_response_code(401);
        echo json_encode([
            "status" => "failure",
            "error_code" => "101002",
            "description" => "Invalid access token"
        ]);
        exit;
    }

    // Check if the device exists
    $query = "SELECT * FROM efactorproduct WHERE node_id ='$nodeid'";
    $row = $db->get_row($query);
    $current_devicetype=$row['current_devicetype'];
    

    if (!$row) {
        http_response_code(404);
        echo json_encode([
            "status" => "failure",
            "error_code" => "101100",
            "description" => "Device Not Found"
        ]);
        exit;
    }




$segment9 = substr($nodeid, 16, 2);
$segment13 = substr($nodeid, 24, 2);
$segment14 = substr($nodeid, 26, 2);
$segment15 = substr($nodeid, 28, 2);

$underscore='_';

$wifitype='EWIFI';
    

$wifissid = $wifitype.$underscore. $segment9 . $segment13 . $segment14 . $segment15;




if (!empty($row['access_token']) || $row['access_token'] === 'NULL') {
    $response = [
        "ssid" => $wifissid,
        "password" => "IOT_PASS"
    ];
    http_response_code(200);
} else {
    $response = [
        "status" => "failure",
        "error_code" => "101005",
        "description" => "Do not have permission to do that"
    ];
    http_response_code(403);
}


   
header('Content-Type: application/json');
echo json_encode($response);

exit;




   }




if (isset($_GET['action']) && $_GET['action'] === 'get_default_cred') {
    header('Content-Type: application/json');

    $headers = getallheaders();
    $req = file_get_contents('php://input');
    $json_data = json_decode($req, true);

    $input_nodeid = $_GET['node_id'] ?? '';

    // Validate access token
    $accessToken = $headers['Authorization'] ?? null;
    $query = "SELECT * FROM users WHERE access_token = '$accessToken'";
    $num = $db->num_rows($query);

    if ($num <= 0) {
        http_response_code(401);
        echo json_encode([
            "status" => "failure",
            "error_code" => "101002",
            "description" => "Invalid access token"
        ]);
        exit;
    }

    // Handle 16-byte or 8-byte node_id
    if (strlen($input_nodeid) === 32) {
        $nodeid = substr($input_nodeid, -16); // extract last 8 bytes from 16-byte ID
    } elseif (strlen($input_nodeid) === 16) {
        $nodeid = $input_nodeid;
    } else {
        http_response_code(400);
        echo json_encode([
            "status" => "failure",
            "error_code" => "101001",
            "description" => "Invalid node_id length"
        ]);
        exit;
    }



    // Fetch device info using the processed nodeid
    $query = "SELECT * FROM efactorproduct WHERE node_id = '$input_nodeid'";
    $row = $db->get_row($query);

    if (!$row) {
        http_response_code(404);
        echo json_encode([
            "status" => "failure",
            "error_code" => "101100",
            "description" => "Device Not Found"
        ]);
        exit;
    }

    $current_devicetype = $row['current_devicetype'];

    // Extract segments for SSID
    $segment9  = substr($nodeid, 0, 2);  // previously 16
    $segment13 = substr($nodeid, 8, 2);  // previously 24
    $segment14 = substr($nodeid, 10, 2); // previously 26
    $segment15 = substr($nodeid, 12, 2); // previously 28
    $wifitype  = 'EWIFI';
    $wifissid  = $wifitype . '_' . $segment9 . $segment13 . $segment14 . $segment15;

    if (!empty($row['access_token']) || $row['access_token'] === 'NULL') {
        $response = [
            "ssid" => $wifissid,
            "password" => "IOT_PASS"
        ];
        http_response_code(200);
    } else {
        $response = [
            "status" => "failure",
            "error_code" => "101005",
            "description" => "Do not have permission to do that"
        ];
        http_response_code(403);
    }

    echo json_encode($response);
    exit;
}



/***************************************************************************/

if (isset($_GET['action']) && $_GET['action']=='scan_info') {


 $header = apache_request_headers();  
	foreach ($header as $key => $val) {

$req = file_get_contents( 'php://input' );
    $json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['node_id'])) {
    $nodeid = $json_data['node_id'];
   
}



    $query = "SELECT * FROM efactorproduct where node_id='$nodeid'";
    $num = $db->num_rows($query); 
    $row_1 = $db->get_row($query); 
         $category=$row_1['prod_category'];
         $accesstoken=$row_1['access_token'];
         $model_name=$row_1['model_number'];
         $Brand_name=$row_1['vendor_name'];



    if($num==0)
       {

      
       $connectivity_1 =json_encode(["status"=>'failure',
                 "error_code"=>'101100',
              "description"=>'Device Not Found']);

                  header('HTTP/1.1 400');
               exit($response['status']=$connectivity_1);

      }
     else
     {  
         $query_model = "SELECT * FROM `$model_name` where node_id='$nodeid'";
         $num_model = $db->num_rows($query_model); 
         $row_model = $db->get_row($query_model); 
        //$model_name=$row_1['model_name'];
         $configure_node=$row_model['configure_node'];


  if($configure_node == 'OK')
   {
     $configure_node = 'Done';
   }
  else if($configure_node == 'FAIL')
   {
     $configure_node = 'Fail';
   }
  else{

       $configure_node = 'Not yet Done';

}



           echo json_encode([
                    'node_id' => $nodeid,
                    'brand_name' => $Brand_name,
                    'model_name' => $model_name,
                    'device_configure' => $configure_node,
                    'test1' => 'Not yet Done',
                    'test2' => 'Not yet Done',
                    'test3' => 'Not yet Done'

                ]);

exit;


     

                
     }
}
}
/******************************id_assigned to Brand**************************************************/


if (isset($_GET['action']) && $_GET['action']=='id_assigned_to_brand') {


 $header = apache_request_headers();  
	foreach ($header as $key => $val) {

$req = file_get_contents( 'php://input' );
    $json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['node_id'])) {
    $nodeid = $json_data['node_id'];
   
}

if (is_array($json_data) && isset($json_data['brad_name'])) {
    $brad_name = $json_data['brad_name'];
   
}




    $query = "SELECT * FROM efactorproduct where node_id='$nodeid'";
    $num = $db->num_rows($query); 
    $row_1 = $db->get_row($query); 
         $category=$row_1['prod_category'];
         $accesstoken=$row_1['access_token'];
         $model_name=$row_1['model_number'];
         $Brand_name=$row_1['vendor_name'];



  /*  if($Brand_name != 'NULL')
       {

      
       $connectivity_1 =json_encode(["status"=>'failure',
                 "error_code"=>'101100',
              "description"=>'Brad name already assigned']);

                  header('HTTP/1.1 400');
               exit($response['status']=$connectivity_1);

      }
     else
     {  

  
        */


$db->query("update efactorproduct set vendor_name='$brad_name' where node_id='$nodeid'");


$connectivity_1 =json_encode(['status' => 'success',
              "description"=>'Brad name updated successfully']);

           exit($response['status']=$connectivity_1);
                
   //  }
}
}

/***********************************qr_duplicate******************************************/

/*
if (isset($_GET['action']) && $_GET['action'] == 'qr_duplicate') {
    $req = file_get_contents('php://input');
    $json_data = json_decode($req, true);

    if (is_array($json_data) && isset($json_data['data'])) {
        $data = $json_data['data'];
        
        // Include QR code library
       // include('phpqrcode/qrlib.php');
        
        // Output directory for QR codes
        $outputDir = 'qrcodes/';
        if (!file_exists($outputDir)) {
            mkdir($outputDir);
        }
        
        // Generate QR codes in three sizes
        $sizes = [
            'small' => 3,
            'medium' => 6,
            'large' => 10
        ];
        
        // Generate unique filename based on data hash
        $fileHash = md5($data);
        $qrFiles = [];
        
        foreach ($sizes as $sizeName => $size) {
            $filename = $outputDir . 'qr_' . $sizeName . '_' . $fileHash . '.png';
            QRcode::png($data, $filename, QR_ECLEVEL_L, $size, 2);
            $qrFiles[$sizeName] = $filename;
        }
        
        // Return the QR code images
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'QR codes generated successfully',
            'qr_codes' => $qrFiles
        ]);
        exit;
    }
}


*/






if (isset($_GET['action']) && $_GET['action'] == 'qr_duplicate') {
    // Get the raw input data
    $req = file_get_contents('php://input');
    
    // Attempt to decode JSON
    $json_data = json_decode($req, true);
    
    // Check if JSON is valid and contains 'data'
    if ($json_data !== null && isset($json_data['data'])) {
        // Clear any previous output
        if (ob_get_level() > 0) ob_clean();
        
        // Set proper header and return JSON
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'QR code generated successfully'
        ]);
    } else {
        // Clear any previous output
        if (ob_get_level() > 0) ob_clean();
        
        // Return error if no valid data
        header('Content-Type: application/json', true, 400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid or missing data parameter'
        ]);
    }
    exit;
}

/*************************************check_device******************************************/


 if (isset($_GET['action']) && $_GET['action']=='check_device') {


 $header = apache_request_headers();  
	foreach ($header as $key => $val) {

$req = file_get_contents( 'php://input' );
    $json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['node_id'])) {
    $nodeid = $json_data['node_id'];
   
}

if (is_array($json_data) && isset($json_data['latitude'])) {
    $latitude = $json_data['latitude'];
   
}
if (is_array($json_data) && isset($json_data['longitude'])) {
    $longitude = $json_data['longitude'];
   
}


    $query = "SELECT * FROM efactorproduct where node_id='$nodeid'";
    $num = $db->num_rows($query); 
    if($num==0)
       {

      
       $connectivity_1 =json_encode(["status"=>'failure',
                 "error_code"=>'101100',
              "description"=>'Device Not Found']);

                  header('HTTP/1.1 400');
               exit($response['status']=$connectivity_1);

      }
     else
     {  
         $query = "SELECT * FROM efactorproduct where access_token ='$val' and node_id='$nodeid'";
         $num = $db->num_rows($query); 
         $row = $db->get_row($query); 
       
     

         if ($num>0)
        {
          

        $connectivity_1 =json_encode(["status"=>'failure',
                 "error_code"=>'101005',
              "description"=>'Already Added']);

            header('HTTP/1.1 400');
           exit($response['status']=$connectivity_1);


         }
          else {

                 $query = "SELECT * FROM efactorproduct where node_id='$nodeid'";
                 $num_1 = $db->num_rows($query); 
                 $row_1 = $db->get_row($query); 
         $category=$row_1['prod_category'];
         $accesstoken=$row_1['access_token'];
         





                 if($accesstoken == 'NULL')
          {
           // echo'hi';


           $db->query("update efactorproduct set check_latitude='$latitude' where node_id='$nodeid'");
           $db->query("update efactorproduct set check_longitude='$longitude' where node_id='$nodeid'");

            $response['prod_category']=$category;
           //  exit($response['prod_category']=$category);



     

          }
        

          else 
          {
           


              $connectivity_1 =json_encode(["status"=>'failure',
                 "error_code"=>'101005',
              "description"=>'Do not have permission to do that']);

               header('HTTP/1.1 400');
                 exit($response['status']=$connectivity_1);

           
           }
         } 
     }
}
}
/***************************************************************************/



/*************************************check_device******************************************/


 if (isset($_GET['action']) && $_GET['action']=='check_device_1') {


    $query = "SELECT * FROM efactorproduct where node_id='".$_POST['node_id']."'";
    $num = $db->num_rows($query); 
    if($num==0)
       {

        header('HTTP/1.1 400');
        $response['status']='failure';
        $response['error_code']='101100';
        $response['description']='Device Not Found';
      }
     else
     {  
         $query = "SELECT * FROM efactorproduct where node_id='".$_POST['node_id']."'";
         $num = $db->num_rows($query); 
         $row = $db->get_row($query); 
         $category=$row['prod_category'];
         $accesstoken=$row['access_token'];
         //echo $accesstoken;
         if($accesstoken== 'NULL')
          {
            $response['prod_category']=$category;
          }
         else
          {
            header('HTTP/1.1 400');
            $response['status']='failure';
            $response['error_code']='101101';
            $response['description']='Device Already added to another user';
           
           }
          
     }
}



/*************************user_gateway_nodes_mapping**********************/

if (isset($_GET['action']) && $_GET['action']=='user_gateway_nodes_mapping_1') {


$req = file_get_contents( 'php://input' );
$json_data = json_decode($req, true);

//echo $json_data;


/*$json_data = $json_data(1, 2, 3, 4, 5, 6);
$stringValue = implode(', ', $json_data); // This will give you "1, 2, 3"
echo $stringValue;*/

$myArray = $json_data(1, 2, 3, 4, 5, 6);
$jsonString = json_encode($myArray); // This will give you "[1, 2, 3]"
echo $jsonString;

if (is_array($json_data) && isset($json_data['Gateway']) && isset($json_data['node_id'])) {
    $Gate_way = $json_data['Gateway'];
    $nodeid = $json_data['node_id'];
}


//$Gate_way=$_POST['Gateway'];
//$nodeid=$_POST['node_id'];

$header = apache_request_headers();  
foreach ($header as $key => $val) {

$query = "SELECT * FROM efactorproduct where node_id='$nodeid'";
$num = $db->num_rows($query); 
$row = $db->get_row($query); 
$module_type=$row['module_type'];
$addgateway=$row['add_gateway'];


if ($module_type == 'RF')
{

    if($addgateway == 'NULL')
    {
    
    $db->query("update efactorproduct set add_gateway='$Gate_way' where node_id='$nodeid'");
    $db->query("update efactorproduct set add_gateway='$Gate_way' where node_id='$Gate_way'");
    $db->query("update efactorproduct set is_added='1' where node_id='$Gate_way'");
    $response['status']='successfully added device to gateway';

    $response_2 =json_encode(["status"=>'successfully added device to gateway']);

    exit($response_2);
       

    }

    else
        {

            header('HTTP/1.1 400');
            
            $response_3 =json_encode(["status"=>'failure',
                                      "error_code"=>'101200',
                                      "description"=>'device already added to gateway']);

           
            exit($response_3);
    
      }

}

else
{

           header('HTTP/1.1 400');
           $response_4 =json_encode(["status"=>'failure',
                                      "error_code"=>'101111',
                                      "description"=>'node_id is not RF module']);  
            exit($response_4);
          

}
         

//headers_remove("$Autherization");
      }
}

/******************************************************************************/
if (isset($_GET['action']) && $_GET['action']=='user_gateway_nodes_mapping') {



//$nodeid=$_POST['node_id'];
//$payload=$_POST['payload'];

$req = file_get_contents( 'php://input' );
$json_data = json_decode($req, true);

/*echo $json_data->status;  // Output: John Doe
echo $json_data->payload;   // Output: 30
echo $json_data->internet;
echo $json_data->gateway_id;
echo $json_data->command_type;
echo $json_data->node_id;*/



/*$json_data = $json_data(1, 2, 3, 4, 5, 6);
$stringValue = implode(', ', $json_data); // This will give you "1, 2, 3"
echo $stringValue;*/

if (is_array($json_data) && isset($json_data['node_id']) && isset($json_data['payload'])) {
    $nodeid = $json_data['node_id'];
    $payload = $json_data['payload'];
}


if (is_array($json_data) && isset($json_data['gateway_id'])) {
    $gatewayid = $json_data['gateway_id'];
   
}



$first_four_digits = substr($payload, 0, 4);

$parts = explode($first_four_digits, $payload);
$new_payload = $parts[1];



$header = apache_request_headers();  
foreach ($header as $key => $val) {

$query = "SELECT * FROM efactorproduct where node_id='$nodeid'";
$num = $db->num_rows($query); 
$row = $db->get_row($query); 
$module_type=$row['module_type'];
$addgateway=$row['add_gateway'];
//echo $nodeid;
//echo $module_type;

if ($module_type == 'Gateway')
{


$number_of_devices = substr($payload, 2, 2);

$ids = [];
for ($i = 0; $i < $number_of_devices; $i++) {

$id = substr($new_payload, $i * 32, 32);

    $query_id = "SELECT * FROM efactorproduct where node_id='$id'";
    $num_id = $db->num_rows($query_id); 
    $row_id = $db->get_row($query_id); 
    $module_type_id=$row_id['module_type'];




    if($module_type_id == 'RF')
      {
          $db->query("update efactorproduct set add_gateway='$nodeid' where node_id='$id'");
          $db->query("update efactorproduct set add_gateway='$nodeid' where node_id='$nodeid'");  

    $db->query("update efactorproduct set is_added='1' where node_id='$nodeid'");
     

      }

    
      
}








 $response_2 =json_encode(["status"=>'successfully added device to gateway']);
          exit($response_2); 
   


}
else
{
      header('HTTP/1.1 400');
           $response_4 =json_encode(["status"=>'failure',
                                      "error_code"=>'101111',
                                      "description"=>'node_id is not GATEWAY']);  
            exit($response_4);



}



//headers_remove("$Autherization");
     }
}



















/********************user_remove_node_gateway_mapping**********************/

if (isset($_GET['action']) && $_GET['action']=='user_remove_node_gateway_mapping') {


$Gate_way=$_GET['Gateway'];
$nodeid=$_GET['node_id'];


$header = apache_request_headers();  
foreach ($header as $key => $val) {

$query = "SELECT * FROM efactorproduct where node_id='$nodeid'";
$num = $db->num_rows($query); 
$row = $db->get_row($query); 
$module_type=$row['module_type'];

echo $module_type;


//echo $module_type;
if ($module_type == 'RF')
{


$query_gate = "SELECT * FROM efactorproduct where add_gateway='$Gate_way'";
$num_gate = $db->num_rows($query_gate); 
$row_gate = $db->get_row($query_gate); 
//$module_type=$row['module_type'];
//echo $num_gate;
if($num_gate>2)
{
$db->query("update efactorproduct set add_gateway='NULL' where node_id='$nodeid'");
//echo 'success';

}
else{
//$db->query("update efactorproduct set add_gateway='NULL'where node_id='$Gate_way'");

$db->query("update efactorproduct set add_gateway='NULL' where node_id='$nodeid'");


//$db->query("update efactorproduct set is_added='NULL' where node_id='$Gate_way'");
//echo $Gate_way;
//echo $nodeid;

}

$response['status']='successfully unlinked device to gateway';


/***********************************************/

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$Gate_way."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF7EC2000000010800."/".$command;


echo $topic;
$mqtt_json=array("command_type"=> "SET_DEVICE_STATUS",
 "address"=>$Gate_way,
"device_id"=>$nodeid,
"payload"=>'13');
$mqtt_json_1=json_encode($mqtt_json);
echo json_encode($mqtt_json)."\n";


$efactor='Efactor';
$things='things';
$command='command';

    


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
   
       }







/************************************************/
}

else
{
            header('HTTP/1.1 400');
            $response['status']='failure';
            $response['error_code']='101112';
            $response['description']='node_id is not RF module';


}
    


  
 

      }
}

/**************************************************************/
/********************new_node_to_database**********************/
/**************************************************************/

if (isset($_GET['action']) && $_GET['action']=='new_node_to_database') {

$req = file_get_contents( 'php://input' );
$json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['node_id'])) {
    $nodeid = $json_data['node_id'];
    
}
if (is_array($json_data) && isset($json_data['module_type'])) {
    $moduletype = $json_data['module_type'];
    
}


$header = apache_request_headers();  
foreach ($header as $key => $val) {

// echo $val;
$scan_val='eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJwaG9uZV92ZXJpZmllZCI6dHJ1ZSwiY3VzdG9tOnVzZXJfaWQiOiIxOTAiLCJ0b2tlbl91c2UiOiJpZCIsImF1dGhfdGltZSI6MTY2NzAyMjI0MiwiZXhwIjoxNjY3MDI1ODQyLCJpYXQiOjE2NjcwMjIyNDIsInBob25lIjoiNjMwNTk4MzI0NiJ9.MkWVlrNPAvlnuw8NRbqJup50t6vP_E8cLmF161p9uio';


//$nodeid=$_POST['node_id'];
//$moduletype=$_POST['module_type'];




if($moduletype == 'WIFI')
{
 $prodcategory= 'Efactor Module A';
  $mac='NULL';
}
else if($moduletype == 'RF')
{
 $prodcategory= 'Efactor Module B';

  $mac = substr($nodeid, 18, 12);
$mac = implode(':', str_split($mac, 2));
//echo "MAC address: " . $mac; 
}
else if($moduletype == 'PLC')
{
 $prodcategory= 'Efactor Module C';
   $mac='NULL';
}
else if($moduletype == 'Gateway')
{
 $prodcategory= 'Efactor Gateway';

    $mac='NULL';
}




$query = "SELECT * FROM efactorproduct where access_token='$val' AND node_id='FFFFFFFFFFFFFFFF7EC2000000000200'";
$num = $db->num_rows($query); 
$row = $db->get_row($query); 
$accesstoken=$row['access_token'];


if($accesstoken != $scan_val){

            // $response['status']='failed';
            header('HTTP/1.1 400');
            $response['status']='failure';
            $response['error_code']='101112';
            $response['description']='not autherized device';
  
}


else{


$query = "SELECT * FROM efactorproduct where node_id='$nodeid'";
$num_1 = $db->num_rows($query); 
$row_1 = $db->get_row($query); 



if($num_1>0)
{

            header('HTTP/1.1 400');
            $response['status']='failure';
            $response['error_code']='101112';
            $response['description']='failed to add device';

}

else

{ 
   


$db->query("INSERT INTO efactorproduct (node_id, module_type, device_type, prod_category, vendor_name, vendor_id, end_device, serial_number, model_number, user_id, user_name, user_status, access_token, request_id, start_id, mac, payload, add_gateway, is_added, secret_key, req_status) VALUES ('$nodeid', '$moduletype', '$devicetype','$prodcategory', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', '$mac', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL')");


$db->query("INSERT INTO items (title, description, unit_type, rate, files, show_in_client_portal, category_id, sort, deleted, teststatus, testremarks, testdate, item_firmversion, item_modelversion, node_id, serialno, vendorname, purchased_date, mappedstatus, module_type, prod_category, access_token, user_id, vendor_id) VALUES ('NULL', 'NULL', 'NULL','NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', '$nodeid', '$serialno', 'NULL', 'NULL', 'NULL', '$moduletype', '$prodcategory', 'NULL', 'NULL', 'NULL')");


$resp='{"status": "success"}';
 
      exit($resp);



}



}


 }



}




/**************************************************************/
/********************user_device_status**********************/
/**************************************************************/

if (isset($_GET['action']) && $_GET['action']=='user_device_status_1') {


//$nodeid=$_POST['get_end_device_status'];
$nodeid=$_GET['node_id'];
/*$mac=$_POST['mac'];
$ipaddress=$_POST['ipaddress'];
$internet=$_POST['internet'];
$payload=$_POST['get_end_device_status'];*/


$req = file_get_contents( 'php://input' );
//echo $req; 
$json_data = json_decode($req, true);



if (is_array($json_data) && isset($json_data['mac'])) {
    $mac = $json_data['mac'];   
} else {
    $mac = '';   
}

if (is_array($json_data) && isset($json_data['ipaddress'])) {
    $ipaddress = $json_data['ipaddress'];   
}else {
    $ipaddress = '';   
}
if (is_array($json_data) && isset($json_data['internet'])) {
    $internet = $json_data['internet'];   
}else {
    $internet = '';   
}

if (is_array($json_data) && isset($json_data['get_end_device_status'])) {
    $payload = $json_data['get_end_device_status'];   
}else {
    $payload = '';   
}





$query = "SELECT * FROM efactorproduct where node_id='$nodeid'";
$num = $db->num_rows($query); 
$row = $db->get_row($query); 
//$module_type=$row['module_type'];
$device_type=$row['device_type'];

//echo $nodeid;
if($num>0){



       $db->query("update efactorproduct set ipaddress='$ipaddress' where node_id='$nodeid'");
       $db->query("update efactorproduct set internet='$internet' where node_id='$nodeid'");
       $db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'");


if($device_type == 'fan')

{
  $db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'"); 


$individualBytes = str_split($payload, 2);
   // Print each byte.
   foreach ($individualBytes as $byte) {
    //echo $byte. "\n";
   }


if($individualBytes[0] == 'DE' && $individualBytes[1] == 'E7')
{



echo 'success';

if($individualBytes[2] == '00')
{
 $db->query("update efactorproduct set power='false' where node_id='$nodeid'");
}

else if($individualBytes[2] == '01')
{
  $db->query("update efactorproduct set power='true' where node_id='$nodeid'");  
}
else if($individualBytes[3] == '01')
{
   $db->query("update efactorproduct set speed='1' where node_id='$nodeid'");
}
else if($individualBytes[3] == '02')
{
   $db->query("update efactorproduct set speed='2' where node_id='$nodeid'");
}
else if($individualBytes[3] == '03')
{
   $db->query("update efactorproduct set speed='3' where node_id='$nodeid'");
}
else if($individualBytes[3] == '04')
{
   $db->query("update efactorproduct set speed='4' where node_id='$nodeid'");
}
else if($individualBytes[3] == '05')
{
   $db->query("update efactorproduct set speed='5' where node_id='$nodeid'");
}
else if($individualBytes[5] == '00')
{
   $db->query("update efactorproduct set Hue='0' where node_id='$nodeid'");
}
else if($individualBytes[5] == '01')
{
   $db->query("update efactorproduct set Hue='1' where node_id='$nodeid'");
}
else if($individualBytes[5] == '02')
{
   $db->query("update efactorproduct set Hue='2' where node_id='$nodeid'");
}
else if($individualBytes[5] == '03')
{
   $db->query("update efactorproduct set Hue='3' where node_id='$nodeid'");
}
else if($individualBytes[5] == '04')
{
   $db->query("update efactorproduct set Hue='4' where node_id='$nodeid'");
}
else if($individualBytes[5] == '05')
{
   $db->query("update efactorproduct set Hue='5' where node_id='$nodeid'");
}

else if($individualBytes[6] == '00')
{
	$led_indication="true";
   $db->query("update efactorproduct set led_indication='$led_indication' where node_id='$nodeid'");
}

else if($individualBytes[6] == '01')
{
	$led_indication="false";
   $db->query("update efactorproduct set led_indication='$led_indication' where node_id='$nodeid'");
}

$rpm = $individualBytes[11].$individualBytes[12];
$db->query("update efactorproduct set rpm='$rpm' where node_id='$nodeid'");

$power = $individualBytes[13].$individualBytes[14].$individualBytes[15].$individualBytes[16];


$power_float = hexToFloat($power);
//echo $power;
//echo $power_float; // Output: 1.0
$db->query("update efactorproduct set power_watt='$power_float' where node_id='$nodeid'");




$current = $individualBytes[17].$individualBytes[18].$individualBytes[19].$individualBytes[20];
$current_float = hexToFloat($current);
//echo $current;
//echo $current_float; 
$db->query("update efactorproduct set current='$current_float' where node_id='$nodeid'");

$bus_voltage = $individualBytes[21].$individualBytes[22].'00'.'00';
$bus_voltage_float = hexToFloat(41920000);
//echo $bus_voltage;
echo $bus_voltage_float; 
$db->query("update efactorproduct set bus_voltage='$bus_voltage_float' where node_id='$nodeid'");


$ac = $individualBytes[23].$individualBytes[24];
$ac_float = hexToFloat($ac);
//echo $ac;
//echo $ac_float; 


$db->query("update efactorproduct set ac_volt='$ac_float' where node_id='$nodeid'");









}


}
       
       $resp='{"status": "success"}';
 
      exit($resp);

}


else{

$response['status']='failure';

}

/*else
{
  $response['status']='failed';
}*/


}

/**************************************************************/
/********************user_device_status**********************/
/**************************************************************/



if (isset($_GET['action']) && $_GET['action'] == 'user_device_status') {

    //$nodeid=$_POST['get_end_device_status'];
    $nodeid = $_GET['node_id'];

    $req = file_get_contents('php://input');
    //echo $req; 
    $json_data = json_decode($req, true);

    if (is_array($json_data) && isset($json_data['mac'])) {
        $mac = $json_data['mac'];
    } else {
        $mac = '';
    }

    if (is_array($json_data) && isset($json_data['ipaddress'])) {
        $ipaddress = $json_data['ipaddress'];
    } else {
        $ipaddress = '';
    }

    if (is_array($json_data) && isset($json_data['internet'])) {
        $internet = $json_data['internet'];
    } else {
        $internet = '';
    }

    if (is_array($json_data) && isset($json_data['get_end_device_status'])) {
        $payload = $json_data['get_end_device_status'];
    } else {
        $payload = '';
    }

    if (is_array($json_data) && isset($json_data['latitude'])) {
        $latitude = $json_data['latitude'];
    } else {
        $latitude = '';
    }


    if (is_array($json_data) && isset($json_data['longitude'])) {
        $longitude = $json_data['longitude'];
    } else {
        $longitude = '';
    }



    $query = "SELECT * FROM efactorproduct where node_id='$nodeid'";
    $num = $db->num_rows($query);
    $row = $db->get_row($query);
    $modulenumber = $row['module_number'];
    $device_type = $row['device_type'];

    //echo $nodeid;
    if ($num > 0) {

        $db->query("update efactorproduct set ipaddress='$ipaddress' where node_id='$nodeid'");
        $db->query("update efactorproduct set internet='$internet' where node_id='$nodeid'");
        $db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'");
        $db->query("update efactorproduct set node_latitude='$latitude' where node_id='$nodeid'");
        $db->query("update efactorproduct set node_longitude='$longitude' where node_id='$nodeid'");

        if ($device_type == 'fan') {

            if ($modulenumber == 'EFACT V1.1') {
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
                        case '06':
                            $speed = '6';
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
                    $rpm = $individualBytes[11] . $individualBytes[12];
                    $power_wat = $individualBytes[13] . $individualBytes[14] . $individualBytes[15] . $individualBytes[16];
                    $power_float = hexToFloat($power_wat);
                    $current = $individualBytes[17] . $individualBytes[18] . $individualBytes[19] . $individualBytes[20];
                    $current_float = hexToFloat($current);
                    $bus_voltage = $individualBytes[21] . $individualBytes[22] . '00' . '00';
                    $bus_voltage_float = hexToFloat(41920000);
                    $ac = $individualBytes[23] . $individualBytes[24];
                    $ac_float = hexToFloat($ac);

                    $db->query("update efactorproduct set power_watt='$power', current='$current_float', bus_voltage='$bus_voltage_float', ac_volt='$ac_float', power='$power',                            speed='$speed', Hue='$hue', led_indication='$led_indication', direction='$direction' where node_id='$nodeid'");
                }
            } else {
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

                    $db->query("update efactorproduct set power='$power', speed='$speed', Hue='$hue', direction='$direction' where node_id='$nodeid'");
                }
            }
        }


else if(device_type == 'cooker')
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

       case '00':
            $cookermode = 'standby';
            break;
        case '01':
            $cookermode = 'pre-timer';
            break;
        case '02':
            $cookermode = 'wait-timer';
            break;
        case '03':
            $cookermode = 'cooking';
            break;
        case '04':
            $cookermode = 'keepwarm';
            break;
        case '05':
            $cookermode = 'soaking';
            break;

        default:
            $cookermode = '';
            break;
    }


   

    
    $cook_time = $individualBytes[4];
    $cook_run_temp = $individualBytes[5];
    $cook_max_temp = $individualBytes[6];


switch ($individualBytes[7]) {
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
            $recipe = 'DIY';
            break;


        default:
            $recipe = '';
            break;
    }
    


 
    $db->query("update efactorproduct set cook_time='$cook_time', cookermode='$cookermode', cook_run_temp='$cook_run_temp', power='$power', cook_max_temp='$cook_max_temp', recipe='$recipe' where node_id='$nodeid'");





}








}


else if(device_type == 'cooker')
{

   $db->query("update efactorproduct set payload='$payload' where node_id='$nodeid'"); 
$individualBytes = str_split($payload, 2);

if ($individualBytes[0] == 'DE' && $individualBytes[1] == 'E7') {
    if ($individualBytes[2] == '00') {
        $power = 'false';
    } else if ($individualBytes[2] == '01') {
        $power = 'true';
    }


 $db->query("update efactorproduct power='$power' where node_id='$nodeid'");

}

}


else{
   echo 'Require Device_type';
}






         /*-------------------ping---------------------------------*/

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$nodeid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF7EC2000000010800."/".$command;

echo $nodeid;
echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$nodeid,
"payload"=>'A1E70A');
$mqtt_json_1=json_encode($mqtt_json);
echo json_encode($mqtt_json)."\n";


$efactor='Efactor';
$things='things';
$command='command';

    $db->query("update efactorproduct set ping='set' where node_id='$nodeid'");


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
   
       }


$query = "SELECT * FROM efactorproduct where node_id='$nodeid'";  
$doctors = $db->get_results($query); 
//$num_dev = $db->num_rows($query);
$row = $db->get_row($query); 
$ping=$row['ping'];


/*if($ping =='set')
{

if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
    for ($i = 0; $i < 2; $i++) {
        $mqtt->publish($topic, $mqtt_json_1);
        usleep(550000); // Wait for 550 milliseconds
    }
}
}

else
{

exit;
}*/


  /*-------------------------------------------------------*/
        $resp = '{"status": "success"}';
        exit($resp);
    } else {
        $response['status'] = 'failure';
    }
}




/******************************************************************/









/******************************************************************/


if (isset($_GET['action']) && $_GET['action']=='wifi_reset'){

 // echo 'ok reset'; 

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

/*$efactor='Efactor';
$things='things';
$thingid='FFFFFFFFFFFFFFFF8200000000001700';
$command='command';

$topic=$efactor."/".$things."/".$thingid."/".$command;

echo $topic;*/

//$mqtt->publish('Efactor/things/FFFFFFFFFFFFFFFF8200000000001700/command', '{"Hellooo"}'); 


// $mqtt->publish('Efactor/things/FFFFFFFFFFFFFFFFC4C3C2C1D4D35500/command', '{"command_type": "GET_DEVICE_STATUS",     "address": "FFFFFFFFFFFFFFFFC4C3C2C1D4D35500", "payload": //"A1C0040A"} ');


  if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) { 

     $mqtt->publish('Efactor/things/FFFFFFFFFFFFFFFF8200000000002000/command', '{"command_type": "GET_DEVICE_STATUS",     "address": "FFFFFFFFFFFFFFFF8200000000002000", "payload": "F0"} ');

echo 'hi'; 
    }




}

/***********************************************************************/
/***********************************************************************/
/*************************add_wifinode**********************************/


if (isset($_GET['action']) && $_GET['action']=='add_wifi_node') {


       $header = apache_request_headers();  
	foreach ($header as $key => $val) {
	$query = "SELECT * FROM efactorproduct where node_id='".$_GET['node_id']."'";
	$doctors = $db->get_results($query); 
	$num = $db->num_rows($query);
   
$nodeid=$_GET['node_id'];


 if($num>0)
  {

      $query = "SELECT * FROM users where access_token='$val'";
	$doctors = $db->get_results($query); 
	$num = $db->num_rows($query);
      $row = $db->get_row($query);
	$userid=$row['id'];
      $mobiledevicetoken=$row['mob_device_token'];
      $phone=$row['phone'];
     $email=$row['email'];


      $query_device = "SELECT * FROM efactorproduct where node_id='".$_GET['node_id']."'";
      $doctors = $db->get_results($query_device);
      $row_device = $db->get_row($query_device);
      $devicename=$row_device['device_type'];
      $devicetype=$row_device['device_type'];
      $nodeid=$row_device['node_id'];
      $model_number=$row_device['model_number'];

//$timestamp=round(microtime(true) * 1000);
$dt=date("Y-m-d H:i:s");

$segment9 = substr($nodeid, 16, 2);
$segment13 = substr($nodeid, 24, 2);
$segment14 = substr($nodeid, 26, 2);
$segment15 = substr($nodeid, 28, 2);

$underscore='_';
    

$deviceType = $devicetype.$underscore. $segment9 . $segment13 . $segment14 . $segment15;


     

     if($model_number === 'EFDEFLT V1.0')
{



       $db->query("update efactorproduct set secret_key='$secret_key' where node_id='$nodeid'");
       $db->query("update efactorproduct set request_id='$requestid' where node_id='$nodeid'");
       $db->query("update efactorproduct set access_token='$val' where node_id='$nodeid'");
       $db->query("update efactorproduct set user_id='$userid' where node_id='$nodeid'");
       $db->query("update efactorproduct set mob_device_token='$mobiledevicetoken' where node_id='$nodeid'");
       $db->query("update efactorproduct set name='$deviceType' where node_id='$nodeid'");
       $db->query("update efactorproduct set time='$dt' where node_id='$nodeid'");
       $db->query("update efactorproduct set current_devicetype='$deviceType' where node_id='$nodeid'");
       $db->query("update efactorproduct set phone='$phone' where node_id='$nodeid'");
       $db->query("update efactorproduct set email='$email' where node_id='$nodeid'");


     /*  $db->query("update efactorproduct set SUID='$nodeid' where node_id='$nodeid'");
       $db->query("update efactorproduct set DUID='0000000000000000' where node_id='$nodeid'");
       $db->query("update efactorproduct set UDP_CMD='DA' where node_id='$nodeid'");
       $db->query("update efactorproduct set UDP_ACK='01' where node_id='$nodeid'");
       $db->query("update efactorproduct set MEDIA='02' where node_id='$nodeid'");
       $db->query("update efactorproduct set Frame='00' where node_id='$nodeid'");
       $db->query("update efactorproduct set FID='00' where node_id='$nodeid'");
       $db->query("update efactorproduct set UserID='$userid' where node_id='$nodeid'");
       $db->query("update efactorproduct set SDUID_Enable='D400' where node_id='$nodeid'");
       $db->query("update efactorproduct set UID_DATA='00' where node_id='$nodeid'");*/   //later








/**********************/

}



else


{

     


      $db->query("update efactorproduct set secret_key='$secret_key' where node_id='$nodeid'");
       $db->query("update efactorproduct set request_id='$requestid' where node_id='$nodeid'");
       $db->query("update efactorproduct set access_token='$val' where node_id='$nodeid'");
       $db->query("update efactorproduct set user_id='$userid' where node_id='$nodeid'");
       $db->query("update efactorproduct set mob_device_token='$mobiledevicetoken' where node_id='$nodeid'");
       $db->query("update efactorproduct set name='$deviceType' where node_id='$nodeid'");
       $db->query("update efactorproduct set time='$dt' where node_id='$nodeid'");
       $db->query("update efactorproduct set current_devicetype='$deviceType' where node_id='$nodeid'");
       $db->query("update efactorproduct set phone='$phone' where node_id='$nodeid'");
       $db->query("update efactorproduct set email='$email' where node_id='$nodeid'");

       $db->query("update `$model_number` set secret_key='$secret_key' where node_id='$nodeid'");
       $db->query("update `$model_number` set request_id='$requestid' where node_id='$nodeid'");
       $db->query("update `$model_number` set access_token='$val' where node_id='$nodeid'");
       $db->query("update `$model_number` set user_id='$userid' where node_id='$nodeid'");
       $db->query("update `$model_number` set mob_device_token='$mobiledevicetoken' where node_id='$nodeid'");
       $db->query("update `$model_number` set name='$deviceType' where node_id='$nodeid'");
       $db->query("update `$model_number` set time='$dt' where node_id='$nodeid'");
       $db->query("update `$model_number` set current_devicetype='$deviceType' where node_id='$nodeid'");
       $db->query("update `$model_number` set phone='$phone' where node_id='$nodeid'");
       $db->query("update `$model_number` set email='$email' where node_id='$nodeid'");     //17-05-25

 

/**********************/

}




/*---------------------------------------------------------------*/

/*   $query_node="SELECT * FROM efactorproduct where node_id='$nodeid'";
$single_node_dynamic = $db->get_results($query_node);
$row_node = $db->get_row($query_node);
$model_name=$row_node['model_number'];
$current_devicetype=$row_node['current_devicetype'];
$device_type=$row_node['device_type'];
$nett_conn=$row_node['internet'];



$param_power = "SELECT * FROM product_api WHERE model_name = '$model_name' AND parameter_name='power'";
$param_power_dynamic = $db->get_results($param_power);
$row_power = $db->get_row($param_power);
$pagename=$row_power['page_names'];



      $device_power = "SELECT * FROM `model_name` WHERE node_id='$nodeid'";
        $row__device_power = $db->get_row($device_power);
        //$current_devicetype=$row__device_power['current_devicetype'];
        $power_1=$row__device_power['power'];
        //$nett_conn=$row__device_power['internet'];



        $power = ($power_1 == "01") ? true : false;
       // $nett_conn=$row__device_power? true : false;


        $local_control = "enable";
        $ui_description = "Read/Write";
        $primary = "power";
        $efactor = "efactor";
        $device = "device";
        $type = $efactor . "." . $device . "." . $device_type;
        $brand_logo = "https://efactor.tech/files/brand_logo/efactor.jpg";

        
        $devices = [
            [
                "id" => $nodeid,
                "device_name" => $current_devicetype,
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

        
        $nodeData = [
            "config" => [
                "node_id" => $nodeid,
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

        
        $all_nodes[] = $nodeData;



$response = [
        
        "all_nodes" => $all_nodes
    ];

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);





*/


/*-----------------------------------------------------------------*/
      
     $resp='{"status": "success"}';
       exit($resp);

  }
else 

{
    $response['error']='access_token not valid';

exit;


}



}

}
/******************************ws_add_node*************************************/




if (isset($_GET['action']) && $_GET['action'] == 'add') {


$nodeid =$_GET['node_id'];
$deviceid=$_GET['device_id'];


    $header = apache_request_headers();  
foreach ($header as $key => $val) {


$additionalData = array(
    "url" => "action=add",
    "token" => $val,
    "node_id"=>$nodeid,
    "device_id"=>$deviceid,
     );



//$allData = array_merge($additionalData, $device);
echo json_encode($additionalData);
exit;


}
}






if (isset($_GET['action']) && $_GET['action'] == 'remove') {


$nodeid =$_GET['node_id'];
$deviceid=$_GET['device_id'];


    $header = apache_request_headers();  
foreach ($header as $key => $val) {


$additionalData = array(
    "url" => "action=remove",
    "token" => $val,
    "node_id"=>$nodeid,
    "device_id"=>$deviceid,
     );



//$allData = array_merge($additionalData, $device);
echo json_encode($additionalData);
exit;

/*

      

    // Final JSON response
    $response = [
        "url" => "action=remove",
        "token" => $access_token,
        "node_id" => $nodeid,
        "device_id" => $deviceid
    ];

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
exit;*/

}
}




/*************************user_nodes_sharing**********************/

if (isset($_GET['action']) && $_GET['action']=='user_nodes_sharing') {

$req = file_get_contents( 'php://input' ); 
$json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['nodes'])) {
    $nodes = $json_data['nodes'];

//echo "Nodes:" . PHP_EOL;
    foreach ($nodes as $node) {
         $node . PHP_EOL;
    }
    
}
if (is_array($json_data) && isset($json_data['phone'])) {
    $phone = $json_data['phone'];
  
}



$header = apache_request_headers();  
foreach ($header as $key => $val) {


        $query = "SELECT * FROM efactorproduct where node_id='$node'";
	$doctors = $db->get_results($query); 
	$num = $db->num_rows($query);
        $row = $db->get_row($query);
        $devicetype=$row['device_type'];
        $name=$row['name'];
        $prime_phone=$row['phone'];

if($num>0)

    {

       $query_phone = "SELECT * FROM users where phone='$phone'";
	$doctors_phone = $db->get_results($query_phone); 
	$num_phone = $db->num_rows($query_phone);
        $row_phone = $db->get_row($query_phone);
	$userid=$row_phone['id'];
        $to=$row_phone['mob_device_token'];
        $email=$row_phone['email'];
        $phone_phone=$row_phone['phone'];
        $timestamp=round(microtime(true) * 1000);
        $dt=date("Y-m-d H:i:s"); 






            $query_share = "SELECT * FROM efactorproduct where share_user='$phone_phone' AND (node_id='$node' AND share_req_status='accept')";
	    $doctors_share = $db->get_results($query_share); 
	    $num_share = $db->num_rows($query_share);
            $row_share = $db->get_row($query_share);    
        

       if($num_share>0)

            {


   
                 header('HTTP/1.1 400');
                 $response['status']='failure';
                 $response['error_code']='101122';
                 $response['description']='Already node shared';

            }


             else{

                 
                   $request_id_val = rand(100000,999999); 
                   $ef_req_id='EF'.$request_id_val;
                
                   if(share_user == 'NULL')
                     {
                            $db->query("update efactorproduct set share_user='$phone' where node_id='$node'");
                            $db->query("update efactorproduct set user_priority='secondary' where node_id='$node'");
                            $db->query("update efactorproduct set share_req_id='$ef_req_id' where node_id='$node'");
                            $db->query("update efactorproduct set share_req_status='pending' where node_id='$node'");
                            $db->query("update efactorproduct set share_time='$timestamp' where node_id='$node'");
                   }
                   else
                     {
                       

                        /*    $db->query("update efactorproduct set share_user='$phone' where node_id='$node'");
                            $db->query("update efactorproduct set user_priority='secondary' where node_id='$node'");
                            $db->query("update efactorproduct set share_req_id='$ef_req_id' where node_id='$node'");
                            $db->query("update efactorproduct set share_req_status='pending' where node_id='$node'");
                            $db->query("update efactorproduct set share_time='$timestamp' where node_id='$node'");*/

                       $db->query("UPDATE efactorproduct SET share_user=CONCAT(share_user, ', $phone'), share_req_id=CONCAT(share_req_id, ', $ef_req_id'), share_req_status=CONCAT(share_req_status, ', pending') WHERE node_id='$node'");


                     }

                  
                   $response = array(
    'status' => 'success',
    'description' => 'Success description',
    'request_id' => $ef_req_id
);


$response_json = json_encode($response);
header('Content-Type: application/json');
echo $response_json;

             

 $url = 'https://fcm.googleapis.com/fcm/send';
     $update ='updated';
     $space=' ';
     $description=$name.$space.$param.$space.$update;
     $type1='efactor';
       $type2='device';       

      $type =  $type1 . '.' . $type2 . '.' . $devicetype;

$data = array(
    'to' => $to,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  'New node sharing request received. Please accept or decline',
        'event_data_payload' => '{"event_version":"v5","id":"1","event_type":"efactor.event.user_node_sharing_add","timestamp":' . $timestamp . ',"description":"New node sharing request received. Please accept or decline","event_data":{"user_id":' . $userid . ',"nodes":[' . $node . '],"primary_user_name":' . $prime_phone . ',"request_id":' . $ef_req_id . ',"metadata":{"devices":[{"name":' . $name . ',"type":' . $type . '}]}}}'
    )
);




//print_r($data);

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

// execute the curl session
$response_1 = curl_exec($curl);

// check for errors
if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
    // handle the error
     }

// close the curl session
curl_close($curl);
     
exit();


}

             
            }
      











else 
{


  header('HTTP/1.1 400');
            $response['status']='failure';
            $response['error_code']='101112';
            $response['description']='device not exists';


}


 

 


 
       

    
         

//headers_remove("$Autherization");
      }
}






/*************************user_nodes_sharing_requests**********************/

if (isset($_GET['action']) && $_GET['action']=='user_nodes_sharing_requests') {

$request_id=$_GET['start_request_id'];
$primary_user=$_GET['primary_user'];
$start_user_name=$_GET['start_user_name'];

//$timestamp=round(microtime(true) * 1000);

$header = apache_request_headers();  
foreach ($header as $key => $val) {


$query = "SELECT * FROM efactorproduct where access_token='$val' and share_req_id='$request_id'";
	$doctors = $db->get_results($query); 
	$num_user = $db->num_rows($query);
        $row = $db->get_row($query);
        $shareuser=$row['share_user'];
        $share_req_status=$row['share_req_status'];
        


//if($share_req_status == 'pending'){

if($shareuser != 'NULL')
{



if($primary_user == 'true')
{

if(empty($_GET['start_request_id']))
{



        $query = "SELECT * FROM efactorproduct where access_token='$val' and share_req_status='pending'";
	$doctors = $db->get_results($query); 
	$num = $db->num_rows($query);
        $row = $db->get_row($query);
        $nodeid=$row['node_id'];
  	$shareuser=$row['share_user'];
        $phone=$row['phone'];
        $devicetype= $row['device_type'];
        $name= $row['name'];
        $share_req_status= $row['share_req_status'];
        $share_req_id= $row['share_req_id'];
         $timestamp=$row['share_time'];  
       $type1='efactor';
       $type2='device';       
       $type =  $type1 . '.' . $type2 . '.' . $devicetype;

if ($row) {
    
 $arr = array();





foreach ($doctors as $key => $doctor) {
        array_push($arr, array(
            "id" => $doctor['id'],
            "node_id" => $doctor['node_id'],
             "phone" => $doctor['share_user'],
            "share_user" => $doctor['share_user'],
            "share_req_id" => $doctor['share_req_id']
        ));
    }


/*foreach ($arr as $doctor) {
    echo "ID: " . $doctor['id'] . "<br>";
    echo "Node ID: " . $doctor['node_id'] . "<br>";
    echo "Share User: " . $doctor['share_user'] . "<br>";
    echo "Share Request ID: " . $doctor['share_req_id'] . "<br>";
    echo "------------------------<br>";
}*/





								 
//  $device_id=$arr[0]['share_req_id'];
$next_share_req_id =$arr[1]['share_req_id'];
 $nextid = $next_share_req_id;
$next_share_phone =$arr[1]['phone'];
 $nextphone = $next_share_phone;
						      
}






if($share_req_status == 'pending'){


$responseData = array(
    "sharing_requests" => array(
        array(
            "request_id" => "$share_req_id",
            "request_status" => "$share_req_status",
            "request_timestamp" => "$timestamp",
            "node_ids" => array("$nodeid"),
            "phone" => "$shareuser",
            "primary_user_name" => "$phone",
            "metadata" => array(
                "devices" => array(
                    array(
                        "name" => "$name",
                        "type" => "$type"
                    )
                )
            )
        )
    ),
            "next_request_id" => "$next_share_req_id",
            "next_user_name" => "$nextphone"
);

}

else{


       $response_2 =json_encode(["sharing_requests"=>'']);
        exit($response_2);

}



}


else
{



        $query = "SELECT * FROM efactorproduct where share_req_id='$request_id' and share_user ='$shareuser'";
	$doctors = $db->get_results($query); 
	$num = $db->num_rows($query);
        $row = $db->get_row($query);
        $nodeid=$row['node_id'];
  	$shareuser=$row['share_user'];
        $phone=$row['phone'];
        $devicetype= $row['device_type'];
        $name= $row['name'];
        $share_req_status= $row['share_req_status'];
           $timestamp=$row['share_time'];
       $type1='efactor';
       $type2='device';       
       $type =  $type1 . '.' . $type2 . '.' . $devicetype;


        $query_share = "SELECT * FROM efactorproduct where access_token='$val' and share_user ='$shareuser'";
	$doctors_share = $db->get_results($query_share); 
	$num_share = $db->num_rows($query_share);
        $row_share = $db->get_row($query_share);

if ($row) {
    $arr = array();


    foreach ($doctors_share as $key => $doctor) {
        array_push($arr, array(
            "id" => $doctor['id'],
            "phone" => $doctor['share_user'],
            "share_user" => $doctor['share_user'],
            "share_req_id" => $doctor['share_req_id']
        ));
    }


/*foreach ($arr as $doctor) {
    echo "ID: " . $doctor['id'] . "<br>";
    echo "Node ID: " . $doctor['node_id'] . "<br>";
    echo "Share User: " . $doctor['share_user'] . "<br>";
    echo "Share Request ID: " . $doctor['share_req_id'] . "<br>";
    echo "------------------------<br>";
}*/



    

   
   for ($pos = 0; $pos < $num_share; $pos++) {
    if ($arr[$pos]['share_req_id'] == $request_id) {
        if ($pos + 1 < $num_share) {  
            $next_share_req_id = $arr[$pos + 1]['share_req_id'];
            $nextphone=$arr[$pos + 1]['phone'];
            
        } else {
            $next_share_req_id = null;  // No next element
            $nextphone = null;   
        }
        break;  // Exit the loop once the condition is met
    }
}



}



if($share_req_status == 'pending'){

$responseData = array(
    "sharing_requests" => array(
        array(
            "request_id" => "$request_id",
            "request_status" => "$share_req_status",
            "request_timestamp" => "$timestamp",
            "node_ids" => array("$nodeid"),
            "phone" => "$shareuser",
            "primary_user_name" => "$phone",
            "metadata" => array(
                "devices" => array(
                    array(
                        "name" => "$name",
                        "type" => "$type"
                    )
                )
            )
        )
    ),
            "next_request_id" => "$next_share_req_id",
            "next_user_name" => "$nextphone"
);


}
else{



$response_2 =json_encode(["sharing_requests"=>'']);
        exit($response_2);


}





}
$response = json_encode($responseData, JSON_PRETTY_PRINT);

//header('Content-Type: application/json');
echo $response;


    exit;
}





else
{


        $query = "SELECT * FROM users where access_token='$val'";
	$doctors = $db->get_results($query); 
	$num = $db->num_rows($query);
        $row = $db->get_row($query);
        $phone=$row['phone'];


   //echo $phone;

        $query = "SELECT * FROM efactorproduct where share_user='$phone'";
	$doctors = $db->get_results($query); 
	$num_true = $db->num_rows($query);
        $row = $db->get_row($query);
       // $phone=$row['phone'];

        if($num_true>0)
         {

if(empty($_GET['start_request_id']))
{

    

       // $query = "SELECT * FROM efactorproduct where access_token='$val' and share_req_status='pending'";
        $query = "SELECT * FROM efactorproduct where share_user='$phone'";
	$doctors = $db->get_results($query); 
	$num_start = $db->num_rows($query);
        $row = $db->get_row($query);
        $nodeid=$row['node_id'];
  	$shareuser=$row['share_user'];
        $phone=$row['phone'];
        $devicetype= $row['device_type'];
        $name= $row['name'];
        $share_req_status= $row['share_req_status'];
        $share_req_id= $row['share_req_id'];
         $timestamp=$row['share_time'];  

       $type1='efactor';
       $type2='device';       
       $type =  $type1 . '.' . $type2 . '.' . $devicetype;

if ($row) {
    
 $arr = array();


   


foreach ($doctors as $key => $doctor) {
        array_push($arr, array(
            "id" => $doctor['id'],
            "node_id" => $doctor['node_id'],
             "phone" => $doctor['share_user'],
            "share_user" => $doctor['share_user'],
            "share_req_id" => $doctor['share_req_id']
        ));
    }


/*foreach ($arr as $doctor) {
    echo "ID: " . $doctor['id'] . "<br>";
    echo "Node ID: " . $doctor['node_id'] . "<br>";
    echo "phone: " . $doctor['phone'] . "<br>";
    echo "Share User: " . $doctor['share_user'] . "<br>";
    echo "Share Request ID: " . $doctor['share_req_id'] . "<br>";
    echo "------------------------<br>";
}*/





								 
//  $device_id=$arr[0]['share_req_id'];
$next_share_req_id =$arr[1]['share_req_id'];
 $nextid = $next_share_req_id;
$next_share_phone =$arr[1]['phone'];
 $nextphone = $next_share_phone;
						      
}



if($share_req_status == 'pending')
{

$responseData = array(
    "sharing_requests" => array(
        array(
            "request_id" => "$share_req_id",
            "request_status" => "$share_req_status",
            "request_timestamp" => "$timestamp",
            "node_ids" => array("$nodeid"),
            "phone" => "$shareuser",
            "primary_user_name" => "$phone",
            "metadata" => array(
                "devices" => array(
                    array(
                        "name" => "$name",
                        "type" => "$type"
                    )
                )
            )
        )
    ),
            "next_request_id" => "$next_share_req_id",
            "next_user_name" => "$nextphone"
);

}

else{

$response_2 =json_encode(["sharing_requests"=>'']);
        exit($response_2);

}



}


else
{

  


        $query = "SELECT * FROM efactorproduct where share_req_id='$request_id' and share_user ='$phone'";
	$doctors = $db->get_results($query); 
	$num = $db->num_rows($query);
        $row = $db->get_row($query);
        $nodeid=$row['node_id'];
  	$shareuser=$row['share_user'];
        $phone=$row['phone'];
        $devicetype= $row['device_type'];
        $name= $row['name'];
        $share_req_status= $row['share_req_status'];
       $timestamp=$row['share_time'];
           
       $type1='efactor';
       $type2='device';       
       $type =  $type1 . '.' . $type2 . '.' . $devicetype;


        $query_share = "SELECT * FROM efactorproduct where access_token='$val' and share_user ='$shareuser'";
	$doctors_share = $db->get_results($query_share); 
	$num_share = $db->num_rows($query_share);
        $row_share = $db->get_row($query_share);

if ($row) {
    $arr = array();


    foreach ($doctors_share as $key => $doctor) {
        array_push($arr, array(
            "id" => $doctor['id'],
            "phone" => $doctor['share_user'],
            "share_user" => $doctor['share_user'],
            "share_req_id" => $doctor['share_req_id']
        ));
    }


/*foreach ($arr as $doctor) {
    echo "ID: " . $doctor['id'] . "<br>";
    echo "Node ID: " . $doctor['node_id'] . "<br>";
    echo "Share User: " . $doctor['share_user'] . "<br>";
    echo "Share Request ID: " . $doctor['share_req_id'] . "<br>";
    echo "------------------------<br>";
}*/



    

   
   for ($pos = 0; $pos < $num_share; $pos++) {
    if ($arr[$pos]['share_req_id'] == $request_id) {
        if ($pos + 1 < $num_share) {  
            $next_share_req_id = $arr[$pos + 1]['share_req_id'];
            $nextphone=$arr[$pos + 1]['phone'];
            
        } else {
            $next_share_req_id = null;  // No next element
            $nextphone = null;   
        }
        break;  // Exit the loop once the condition is met
    }
}



}



if($share_req_status == 'pending'){

$responseData = array(
    "sharing_requests" => array(
        array(
            "request_id" => "$request_id",
            "request_status" => "$share_req_status",
            "request_timestamp" => "$timestamp",
            "node_ids" => array("$nodeid"),
            "phone" => "$shareuser",
            "primary_user_name" => "$phone",
            "metadata" => array(
                "devices" => array(
                    array(
                        "name" => "$name",
                        "type" => "$type"
                    )
                )
            )
        )
    ),
            "next_request_id" => "$next_share_req_id",
            "next_user_name" => "$nextphone"
);

}

else{

$response_2 =json_encode(["sharing_requests"=>'']);
        exit($response_2);


}




}
$response = json_encode($responseData, JSON_PRETTY_PRINT);

//header('Content-Type: application/json');
echo $response;


    exit;







         }
         else
         {

            $response_2 =json_encode(["sharing_requests"=>'']);

            exit($response_2);

         }
       


}
       
}


else
{


$response_2 =json_encode(["sharing_requests"=>'']);

    exit($response_2);
       

}

/*}


else{

$response_2 =json_encode(["sharing_requests"=>'']);

    exit($response_2);

}*/
    
         

//headers_remove("$Autherization");
      }
}




/*******************************user_nodes_get_sharing***********************************/


if (isset($_GET['action']) && $_GET['action']=='user_nodes_get_sharing') {




$node_id=$_GET['node_id'];



$header = apache_request_headers();  
foreach ($header as $key => $val) {


$query = "SELECT * FROM efactorproduct where node_id='$node_id'";
	$doctors = $db->get_results($query); 
	$num = $db->num_rows($query);
        $row = $db->get_row($query);
        $userpriority=$row['user_priority'];
        $primery_phone=$row['phone'];
        $secondary_phone=$row['share_user'];
        $accesstoken=$row['access_token'];
       $share_req_status=$row['share_req_status'];
     


if($num>= 0)
{

if($userpriority != 'NULL')
{


if($share_req_status == 'accept')
    {

   if($accesstoken == $val)

   {
$data = array(
    "node_sharing" => array(
        array(
            "node_id" => "$node_id",
            "users" => array(
                "primary" => array($primery_phone),
                "secondary" => array($secondary_phone)
            )
        )
    )
);

$jsonString = json_encode($data, JSON_PRETTY_PRINT);
echo $jsonString;


}else{






$data = array(
    "node_sharing" => array(
        array(
            "node_id" => "$node_id",
            "users" => array(
                "primary" => array($primery_phone),
                "secondary" => array($secondary_phone)
            ),
            "sources" => array(
                "NODE",
                "<group_id>"
            )
        )
    )
);

$jsonString = json_encode($data, JSON_PRETTY_PRINT);
echo $jsonString;


}



}

    else
      {

          $response = array();
          $response['node_sharing'] = 'null';
          echo json_encode($response);
      }

}


else
{
$response = array();
$response['node_sharing'] = 'null';

echo json_encode($response);
}

exit(0);
}


else{
header('HTTP/1.1 400');
            $response['status']='failure';
            $response['error_code']='101112';
            $response['description']='node_id not exists';

}


//break;




//headers_remove("$Autherization");
      }
}





if (isset($_GET['action']) && $_GET['action']=='user_nodes_get_sharing') {




$node_id=$_GET['node_id'];



$header = apache_request_headers();  
foreach ($header as $key => $val) {


$query = "SELECT * FROM efactorproduct where node_id='$node_id'";
	$doctors = $db->get_results($query); 
	$num = $db->num_rows($query);
        $row = $db->get_row($query);
        $userpriority=$row['user_priority'];
        $primery_phone=$row['phone'];
        $secondary_phone=$row['share_user'];
        $accesstoken=$row['access_token'];
       $share_req_status=$row['share_req_status'];
     


if($num>= 0)
{

if($userpriority != 'NULL')
{


if($share_req_status == 'accept')
    {

   if($accesstoken == $val)

   {
$data = array(
    "node_sharing" => array(
        array(
            "node_id" => "$node_id",
            "users" => array(
                "primary" => array($primery_phone),
                "secondary" => array($secondary_phone)
            )
        )
    )
);

$jsonString = json_encode($data, JSON_PRETTY_PRINT);
echo $jsonString;


}else{






$data = array(
    "node_sharing" => array(
        array(
            "node_id" => "$node_id",
            "users" => array(
                "primary" => array($primery_phone),
                "secondary" => array($secondary_phone)
            ),
            "sources" => array(
                "NODE",
                "<group_id>"
            )
        )
    )
);

$jsonString = json_encode($data, JSON_PRETTY_PRINT);
echo $jsonString;


}



}

    else
      {

          $response = array();
          $response['node_sharing'] = 'null';
          echo json_encode($response);
      }

}


else
{
$response = array();
$response['node_sharing'] = 'null';

echo json_encode($response);
}

exit(0);
}


else{
header('HTTP/1.1 400');
            $response['status']='failure';
            $response['error_code']='101112';
            $response['description']='node_id not exists';

}


//break;




//headers_remove("$Autherization");
      }
}




/*******************************************************************************************/

if (isset($_GET['action']) && $_GET['action']=='user_nodes_update_sharing_requests') {

$req = file_get_contents( 'php://input' );
$json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['accept']) && isset($json_data['request_id'])) {
    $accept = $json_data['accept'];
    $share_req_id = $json_data['request_id'];
}



$header = apache_request_headers();  
foreach ($header as $key => $val) {


        $query = "SELECT * FROM users where access_token='$val'";
	$doctors = $db->get_results($query); 
	$num = $db->num_rows($query);
        $row = $db->get_row($query);
       // $devicetype=$row['device_type'];
       // $name=$row['name'];


if($num>0)

    {

       $query_usr1 = "SELECT * FROM users where access_token='$val'";  
$doctors_usr1 = $db->get_results($query_usr1); 
$num_dev_usr1 = $db->num_rows($query_usr1);
$row_usr1 = $db->get_row($query_usr1); 
$phone_usr1=$row_usr1['phone'];
$mob_device_token_usr1=$row_usr1['mob_device_token'];
//$name_usr1=$row_usr1['name'];
$user_id_usr1=$row_usr1['id'];

//$to_usr1=$row_usr1['mob_device_token'];
$email_usr1=$row_usr1['email'];



$query_usr2 = "SELECT * FROM efactorproduct where share_user='$phone_usr1'";  
$doctors_usr2 = $db->get_results($query_usr2); 
//$num_dev = $db->num_rows($query);
$row_usr2 = $db->get_row($query_usr2); 
$phone_usr2=$row_usr2['phone'];
$mob_device_token_usr2=$row_usr2['mob_device_token'];
//$name_usr2=$row_usr2['name'];
$user_id_usr2=$row_usr2['user_id'];
//$timestamp=round(microtime(true) * 1000);
//$to_usr2=$row_usr2['mob_device_token'];
$nodeid=$row_usr2['node_id'];
$email_usr2=$row_usr2['email'];


$timestamp=round(microtime(true) * 1000);


if($accept =='true')

{


$db->query("update efactorproduct set share_req_status='accept' where node_id='$nodeid'");


$url = 'https://fcm.googleapis.com/fcm/send';
     $update ='updated';
     $space=' ';
     $description=$name.$space.$param.$space.$update;


/*$data1 = array(
    'to' => $mob_device_token_usr1,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  'Secondary User has accepted sharing request',
        'event_data_payload' => '{"event_version":"v5","id":"1","event_type":"efactor.event.user_node_sharing_add","timestamp":' . $timestamp . ',"description":"Secondary User has accepted sharing request","event_data":{"user_id":' . $user_id_usr1 . ',"nodes":[' . $nodeid . '],"secondary_user_name":' . $phone_usr1 . ',"request_id":' . $share_req_id . ',"accept":"true"}}'
    )
);


$data2 = array(
    'to' => $mob_device_token_usr2,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  'New Node(s) added',
        'event_data_payload' => '{"event_version":"v5","id":"1","event_type":"efactor.event.user_node_added","timestamp":' . $timestamp . ',"description":"New Node(s)added","event_data":{"user_id":"' . $user_id_usr2 . '","user_role":"primary","nodes":[' . $nodeid . ']}}'
    )
);
*/





$data1 = array(
    'to' => $mob_device_token_usr2,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  'Secondary User has accepted sharing request',
        'event_data_payload' => '{"event_version":"v5","id":"1","event_type":"efactor.event.user_node_sharing_add","timestamp":' . $timestamp . ',"description":"Secondary User has accepted sharing request","event_data":{"user_id":' . $user_id_usr2 . ',"nodes":[' . $nodeid . '],"secondary_user_name":' . $phone_usr1 . ',"request_id":' . $share_req_id . ',"accept":"true"}}'
    )
);


$data2 = array(
    'to' => $mob_device_token_usr1,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  'New Node(s) added',
        'event_data_payload' => '{"event_version":"v5","id":"1","event_type":"efactor.event.user_node_added","timestamp":' . $timestamp . ',"description":"New Node(s)added","event_data":{"user_id":"' . $user_id_usr1 . '","user_role":"primary","nodes":[' . $nodeid . ']}}'
    )
);


}

else
{



$db->query("update efactorproduct set share_req_status='decline' where node_id='$nodeid'");

$url = 'https://fcm.googleapis.com/fcm/send';
     $update ='updated';
     $space=' ';
     $description=$name.$space.$param.$space.$update;



/*$data1 = array(
    'to' => $mob_device_token_usr1,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  'Secondary User has declined sharing request',
        'event_data_payload' => '{"event_version":"v5","id":"1","event_type":"efactor.event.user_node_sharing_add","timestamp":' . $timestamp . ',"description":"Secondary User has declined sharing request","event_data":{"user_id":"' . $user_id_usr1 . '","nodes":["' . $nodeid . '"],"secondary_user_name":' . $phone_usr1 . ',"request_id":' . $share_req_id . ',"accept":"false"}}'
    )
);




$data2 = array(
    'to' => $mob_device_token_usr2,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  'Node(s) removed',
        'event_data_payload' => '{"event_version":"v5","id":"1","event_type":"efactor.event.user_node_removed","timestamp":' . $timestamp . ',"description":"Node(s) removed by primary user","event_data":{"user_id":"' . $user_id_usr2 . '","user_role":"primary","nodes":["' . $nodeid . '"]}}'
    )
);
*/


$data1 = array(
    'to' => $mob_device_token_usr2,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  'Secondary User has declined sharing request',
        'event_data_payload' => '{"event_version":"v5","id":"1","event_type":"efactor.event.user_node_sharing_add","timestamp":' . $timestamp . ',"description":"Secondary User has declined sharing request","event_data":{"user_id":"' . $user_id_usr2 . '","nodes":["' . $nodeid . '"],"secondary_user_name":' . $phone_usr1 . ',"request_id":' . $share_req_id . ',"accept":"false"}}'
    )
);




$data2 = array(
    'to' => $mob_device_token_usr1,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  'Node(s) removed',
        'event_data_payload' => '{"event_version":"v5","id":"1","event_type":"efactor.event.user_node_removed","timestamp":' . $timestamp . ',"description":"Node(s) removed by primary user","event_data":{"user_id":"' . $user_id_usr1 . '","user_role":"primary","nodes":["' . $nodeid . '"]}}'
    )
);






}
//print_r($data);
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
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data1));

// execute the curl session
$response = curl_exec($curl);

// check for errors
if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
    // handle the error
     }

// close the curl session
curl_close($curl);





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
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data2));

// execute the curl session
$response = curl_exec($curl);

// check for errors
if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
    // handle the error
     }

// close the curl session
curl_close($curl);

$response['status']='success';
$response['description']='Success description';
$response['request_id']=$ef_req_id;



//break;
exit($response);


        



}

else 
{


  header('HTTP/1.1 400');
            $response['status']='failure';
            $response['error_code']='101112';
            $response['description']='node_id not exists';


}


   
       

    
         

//headers_remove("$Autherization");
      }
}

/******************************************************************************************/

if (isset($_GET['action']) && $_GET['action']=='user_nodes_remove_sharing_requests') {

$req = file_get_contents( 'php://input' ); 
$json_data = json_decode($req, true);



$request_id=$_GET['request_id'];


$header = apache_request_headers();  
foreach ($header as $key => $val) {


$query = "SELECT * FROM efactorproduct where access_token='$val' AND share_req_id='$request_id'";  
$doctors= $db->get_results($query); 
$num_dev = $db->num_rows($query);
$row = $db->get_row($query);
$node=$row['node_id'];
//$phone=$row['phone'];
//$shareuser=$row['share_user'];
//echo $num_dev;

if($num_dev>0)

{

                   $db->query("update efactorproduct set share_user='NULL' where node_id='$node'");
                   $db->query("update efactorproduct set user_priority='NULL' where node_id='$node'");
                   $db->query("update efactorproduct set share_req_id='NULL' where node_id='$node'");
                   $db->query("update efactorproduct set share_req_status='NULL' where node_id='$node'");

$response = array(
    'status' => 'success',
    'description' => 'user share node request removed'
  
);


$response_json = json_encode($response);
header('Content-Type: application/json');
exit($response_json);

                  /* $response['status']='success';
                   $response['description']='user share node request removed';

                    exit($response);*/


}

else
{
            header('HTTP/1.1 400');
            $response['status']='failure';
            $response['error_code']='101112';
            $response['description']='request_id not exists';
          //  exit($response);

}





//headers_remove("$Autherization");
      }
}

/*******************************************************************************************/
if (isset($_GET['action']) && $_GET['action']=='user_nodes_remove_sharing') {

$req = file_get_contents( 'php://input' ); 
$json_data = json_decode($req, true);



$node=$_GET['nodes'];
$phone=$_GET['phone'];

/*if (is_array($json_data) && isset($json_data['nodes'])) {
    $nodes = $json_data['nodes'];

//echo "Nodes:" . PHP_EOL;
    foreach ($nodes as $node) {
         $node . PHP_EOL;
    }
    
}
if (is_array($json_data) && isset($json_data['phone'])) {
    $phone = $json_data['phone'];
  
}*/






$header = apache_request_headers();  
foreach ($header as $key => $val) {



/*$query = "SELECT * FROM efactorproduct where access_token='$val' and node_id='$node'";  
$doctors= $db->get_results($query); 
$num_dev = $db->num_rows($query);
$row = $db->get_row($query); 
$phone=$row['phone'];
$shareuser=$row['share_user'];*/



 $query = "SELECT * FROM efactorproduct where node_id='$node'";
	$doctors = $db->get_results($query); 
	$num = $db->num_rows($query);
        $row = $db->get_row($query);
        //$devicetype=$row['device_type'];
        //$name=$row['name'];
      //  $prime_phone=$row['phone'];





$phone_usr1=$row['phone'];
$mob_device_token_usr1=$row['mob_device_token'];
//$name_usr1=$row_usr1['name'];
$user_id_usr1=$row['user_id'];

//$to_usr1=$row_usr1['mob_device_token'];
$email_usr1=$row['email'];
   
$shareuser=$row['share_user'];



$query_usr2 = "SELECT * FROM users where phone='$shareuser'";  
$doctors_usr2 = $db->get_results($query_usr2); 
$num_dev_usr2 = $db->num_rows($query_usr2);
$row_usr2 = $db->get_row($query_usr2); 
$phone_usr2=$row_usr2['phone'];
$mob_device_token_usr2=$row_usr2['mob_device_token'];
$user_id_usr2=$row_usr2['id'];
$email_usr2=$row_usr2['email'];


$timestamp=round(microtime(true) * 1000);

if($num>0)

{

   if($shareuser == $phone)

    {
                   $db->query("update efactorproduct set share_user='NULL' where node_id='$node'");
                   $db->query("update efactorproduct set user_priority='NULL' where node_id='$node'");
                   $db->query("update efactorproduct set share_req_id='NULL' where node_id='$node'");
                   $db->query("update efactorproduct set share_req_status='NULL' where node_id='$node'");





$url = 'https://fcm.googleapis.com/fcm/send';
     $update ='updated';
     $space=' ';
     $description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $mob_device_token_usr2,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  'Node(s) removed',
        'event_data_payload' => '{"event_version":"v5","id":"1","event_type":"efactor.event.user_node_removed","timestamp":' . $timestamp . ',"description":"Node(s) removed by primary user","event_data":{"user_id":"' . $user_id_usr2 . '","user_role":"primary","nodes":["' . $node . '"]}}'
    )
);



//print_r($data);
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

// execute the curl session
$response = curl_exec($curl);

// check for errors
if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
    // handle the error
     }

// close the curl session
curl_close($curl);






                  $response['status']='success';
                   $response['description']='user share node removed';

                    exit($response);
                  
     }

   else 
{

header('HTTP/1.1 400');
            $response['status']='failure';
            $response['error_code']='101112';
            $response['description']='phone number not matched';
            exit($response);

}



}


else
{


header('HTTP/1.1 400');
            $response['status']='failure';
            $response['error_code']='101112';
            $response['description']='Device does not belong to user';

             exit($response);


}


}


}



/*************************user_nodes_update_sharing_requests**********************/

if (isset($_GET['action']) && $_GET['action']=='user_nodes_update_sharing_requests_2') {

$req = file_get_contents( 'php://input' );
$json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['accept']) && isset($json_data['request_id'])) {
    $accept = $json_data['accept'];
    $share_req_id = $json_data['request_id'];
}


$header = apache_request_headers();  
foreach ($header as $key => $val) {


$query_usr1 = "SELECT * FROM users where access_token='$val'";  
$doctors_usr1 = $db->get_results($query_usr1); 
$num_dev_usr1 = $db->num_rows($query_usr1);
$row_usr1 = $db->get_row($query_usr1); 
$phone_usr1=$row_usr1['phone'];
$mob_device_token_usr1=$row_usr1['mob_device_token'];
//$name_usr1=$row_usr1['name'];
$user_id_usr1=$row_usr1['id'];

//$to_usr1=$row_usr1['mob_device_token'];
$email_usr1=$row_usr1['email'];



$query_usr2 = "SELECT * FROM efactorproduct where share_user='$phone_usr1'";  
$doctors_usr2 = $db->get_results($query_usr2); 
//$num_dev = $db->num_rows($query);
$row_usr2 = $db->get_row($query_usr2); 
$phone_usr2=$row_usr2['phone'];
$mob_device_token_usr2=$row_usr2['mob_device_token'];
//$name_usr2=$row_usr2['name'];
$user_id_usr2=$row_usr2['user_id'];
//$timestamp=round(microtime(true) * 1000);
//$to_usr2=$row_usr2['mob_device_token'];
$nodeid=$row_usr2['node_id'];
$email_usr2=$row_usr2['email'];


$timestamp=round(microtime(true) * 1000);



$url = 'https://fcm.googleapis.com/fcm/send';
     $update ='updated';
     $space=' ';
     $description=$name.$space.$param.$space.$update;


$data = array(
    'to' => $mob_device_token_usr1,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  'Secondary User has accepted sharing request',
        'event_data_payload' => '{"event_version":"v5","id":"1","event_type":"efactor.event.user_node_sharing_add","timestamp":' . $timestamp . ',"description":"Secondary User has accepted sharing request","event_data":{"user_id":' . $user_id_usr1 . ',"nodes":[' . $nodeid . '],"secondary_user_name":' . $phone_usr1 . ',"request_id":' . $share_req_id . ',"accept":"true"}}'
    )
);




/*$data[] = array(
    'to' => $mob_device_token_usr2,
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body' =>  'New Node(s) added',
        'event_data_payload' => '{"event_version":"v5","id":"1","event_type":"efactor.event.user_node_added","timestamp":' . $timestamp . ',"description":"New Node(s)added","event_data":{"user_id":"' . $user_id_usr2 . '","user_role":"primary","nodes":[' . $nodeid . ']}}'
    )
);*/




//print_r($data);
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

// execute the curl session
$response = curl_exec($curl);

// check for errors
if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
    // handle the error
     }

// close the curl session
curl_close($curl);

$response['status']='success';
$response['description']='Success description';
$response['request_id']=$ef_req_id;



//break;
exit($response);


//headers_remove("$Autherization");
      }
}








/****************************grops_sharing************************************/

/**************************create-group*******************************/

    

if (isset($_GET['action']) && $_GET['action']=='node_create_group') {
$req = file_get_contents( 'php://input' ); 
$json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['nodes'])) {
    $nodes = $json_data['nodes'];


    foreach ($nodes as $node) {
         $node . PHP_EOL;
    }
    
}
if (is_array($json_data) && isset($json_data['group_name'])) {
    $groupname = $json_data['group_name'];
  
}



$header = apache_request_headers();  
foreach ($header as $key => $val) {


        $query = "SELECT * FROM efactorproduct where access_token='$val'";
	$doctors = $db->get_results($query); 
	$num = $db->num_rows($query);
       /* $row = $db->get_row($query);
        $devicetype=$row['device_type'];
        $name=$row['name'];
        $prime_phone=$row['phone'];*/

if($num>0)

    {


        

                   $request_id_val = rand(100000,999999); 
                   $grp_req_id='GRP'.$request_id_val;

foreach ($nodes as $key => $node) {

                 
                   
                   $db->query("update efactorproduct set group_name='$groupname' where node_id='$node'");
                   $db->query("update efactorproduct set group_req_id='$grp_req_id' where node_id='$node'");

                   $query_account = "SELECT * FROM users WHERE access_token='$val'";
                   $doctors = $db->get_results($query); 
	
                   $row = $db->get_row($query_account);
                   $current_group_name=$row['group_name'];
                   $current_group_req_id=$row['group_req_id'];


               
                   


               if ($current_group_name == 'NULL') {

             
    
    $db->query("UPDATE users SET group_name='$groupname', group_req_id='$grp_req_id' WHERE access_token='$val'");
} else {
   
    $db->query("UPDATE users SET group_name=CONCAT(group_name, ', $groupname'), group_req_id=CONCAT(group_req_id, ', $grp_req_id') WHERE access_token='$val'");
}

                
                  

}
                  
    $response = array(
    'status' => 'success',
    'description' => 'Success description',
    'request_id' => $grp_req_id
);


$response_json = json_encode($response);
header('Content-Type: application/json');
exit($response_json);

             


}

             
            
      




else 
{


  header('HTTP/1.1 400');
            $response['status']='failure';
            $response['error_code']='101112';
            $response['description']='device not exists';


}


 



//headers_remove("$Autherization");
      }


}

/****************************node_get_group***************************/

if (isset($_GET['action']) && $_GET['action']=='node_get_group') {

$startid=$_GET['start_id'];

$header = apache_request_headers();  
foreach ($header as $key => $val) {


        $query = "SELECT * FROM users where access_token='$val'";
	$doctors = $db->get_results($query); 
	$num = $db->num_rows($query);
        $row = $db->get_row($query);
        


if ($doctors) {
    $groupname = array();
    $group_req_id = array();

    foreach ($doctors as $row) {

        $groupNameParts = explode(' ', $row['group_name']); 
        $firstName = $groupNameParts[0]; 
        $groupname[] = $firstName; 




       // $groupname[] = $row['group_name'];

       $groupids = explode(', ', $row['group_req_id']); 
        $firstId = $groupids[0]; 
        $groupId[] = $firstId;

       // $group_req_id[] = $row['group_req_id'];
    }

    
  if (!empty($groupname)) {
        
        $resultname = $groupname[0] ;
       // echo $resultname;
    }

if (!empty($groupId)) {
        
        $resultid = $groupId[0] ;
      //  echo $resultid;
    }
} else {
    //echo "No results found.";
}


$individualValues = explode(',', $resultname);
$group_ref_id=explode(',', $resultid);

if($num>= 0)
{



if(empty($_GET['start_id'])){



       $query = "SELECT * FROM efactorproduct where access_token='$val' AND group_name='$individualValues[0]'";
	$doctors_group = $db->get_results($query); 
	$num_grp = $db->num_rows($query);
        $row_grp = $db->get_row($query);
       $groupname_grp=$row_grp['group_name'];
       $group_req_id_grp=$row_grp['group_req_id'];
       $current_id=$row_grp['id'];

$arr = array();


    foreach ($doctors_group as $key => $doctor) {
        array_push($arr, array(
            "id" => $doctor['id'],
             "node_id" => $doctor['node_id'],  
            "group_name" => $doctor['group_name'],
            "group_req_id" => $doctor['group_req_id'],
        ));
    }



/*----------------------------------------------*/
//$query_nxtgrp = "SELECT * FROM efactorproduct WHERE access_token='$val' AND id > $current_id ORDER BY id ASC LIMIT 1";
$query_nxtgrp = "SELECT * FROM efactorproduct WHERE access_token = '$val' AND id > $current_id AND (group_name IS NOT NULL AND group_name <> 'NULL' AND group_name <> '') ORDER BY id ASC";
	$num_nxtgrp = $db->num_rows($query_nxtgrp);
        $row_nxtgrp = $db->get_row($query_nxtgrp);
$nxt_groupname=$row_nxtgrp['group_name'];
       $nxt_group_req_id=$row_nxtgrp['group_req_id'];
/*---------------------------------------------*/    

   $nodes_info = array(); // Initialize an empty array to store node_ids



for ($pos = 0; $pos < $num_grp; $pos++) {
    if ($arr[$pos]['group_name'] == $groupname_grp) {
        $nodes_info[] = $arr[$pos]['node_id'];
    }
}




$response = array(
    'groups' => array(
        array(
            'group_name' =>"$groupname_grp",
            'group_id' => "$group_req_id_grp",
            'primary' => true,
            'type' => 'type_metadata',
            'mutually_exclusive' => true,
           'nodes' => $nodes_info,
            'total' => "$num_grp",
            'description' => 'description'
        )
    ),
    'next_id' => "$groupids[1]"
);


$json_response = json_encode($response);


header('Content-Type: application/json');


exit($json_response);

}

else{


$query_start = "SELECT * FROM efactorproduct where access_token='$val' AND group_req_id='$startid'";
	$doctors_start = $db->get_results($query_start); 
	$num_start = $db->num_rows($query_start);
        $row_start = $db->get_row($query_start);
        $groupname_start=$row_start['group_name'];
       $group_req_id_start=$row_start['group_req_id'];
        $current_id=$row_start['id'];


$arr = array();


    foreach ($doctors_start as $key => $doctor) {
        array_push($arr, array(
            "id" => $doctor['id'],
             "node_id" => $doctor['node_id'],  
            "group_name" => $doctor['group_name'],
            "group_req_id" => $doctor['group_req_id'],
        ));
    }



/*----------------------------------------------*/
//$query_nxtgrp = "SELECT * FROM efactorproduct WHERE access_token='$val' AND id > $current_id ORDER BY id ASC LIMIT 1";
/*$query_nxtgrp = "SELECT * FROM efactorproduct WHERE access_token = '$val' AND id > $current_id AND (group_name IS NOT NULL AND group_name <> 'NULL' AND group_name <> '') ORDER BY id ASC";
	$num_nxtgrp = $db->num_rows($query_nxtgrp);
        $row_nxtgrp = $db->get_row($query_nxtgrp);
$nxt_groupname_start=$row_nxtgrp['group_name'];
       $nxt_group_req_id_start=$row_nxtgrp['group_req_id'];*/







 $query_nxtgrp = "SELECT group_req_id FROM users WHERE group_req_id > '$group_req_id_start' ORDER BY group_req_id ASC LIMIT 1";

$result = $db->query($query_nxtgrp);
//echo $result;
if ($result) {
    $row_nxtgrp = $db->get_row($query_nxtgrp);
    $next_group_req_id = $row_nxtgrp['group_req_id'];
   // echo "Next group_req_id after GRP110097 is: $next_group_req_id";

$group_req_ids = explode(', ', $next_group_req_id);
$position = array_search($group_req_id_start, $group_req_ids);



/*if ($position !== false) {
    echo "Position of $search_value is: $position";
} else {
    echo "$search_value not found in the array.";
}*/




} else {
    echo "No next group_req_id found after GRP110097.";
}







//echo $result;

//echo $groupids[2];

//echo $groupids[$result+1];

/*---------------------------------------------*/  



/*
$position = array_search($startid, $group_ref_id);


if ($position !== false && isset($group_ref_id[$position + 1])) {
  
    $nextId = $group_ref_id[$position + 1];
   
} else {
   
}
*/


  /*------------------------------------------------*/

/*$nodes_info = array(); // Initialize an empty array to store node_ids



for ($pos = 0; $pos < $num_grp; $pos++) {
    if ($arr[$pos]['group_name'] == $groupname_grp) {
        $nodes_info[] = $arr[$pos]['node_id'];
    }
}
*/








/*------------------------------------------------*/
   $nodes_info_start = array(); // Initialize an empty array to store node_ids

   for ($pos = 0; $pos < $num_start; $pos++) {
    if ($arr[$pos]['group_name'] == $groupname_start) {

      $nodes_info_start[] = $arr[$pos]['node_id'];


      
        
    }
}





$response = array(
    'groups' => array(
        array(
            'group_name' =>"$groupname_start",
            'group_id' => "$group_req_id_start",
            'primary' => true,
            'type' => 'type_metadata',
            'mutually_exclusive' => true,
           'nodes' => $nodes_info_start,
            'total' => "$num_start",
            'description' => 'description'
        )
    ),
    'next_id' => isset($group_req_ids[$position + 1]) ? $group_req_ids[$position + 1] : ''
);


$json_response = json_encode($response);


header('Content-Type: application/json');


exit($json_response);







}


}




else{
header('HTTP/1.1 400');
            $response['status']='failure';
            $response['error_code']='101112';
            $response['description']='node_id not exists';

}







//break;




//headers_remove("$Autherization");
      }
}

/******************************remove_group*****************************/


if (isset($_GET['action']) && $_GET['action']=='node_remove_group') {

$req = file_get_contents( 'php://input' ); 
$json_data = json_decode($req, true);



$groupid=$_GET['group_id'];


$header = apache_request_headers();  
foreach ($header as $key => $val) {




 $query = "SELECT * FROM efactorproduct where access_token='$val' AND group_req_id='$groupid'";
	$doctors = $db->get_results($query); 
	$num = $db->num_rows($query);
        $row = $db->get_row($query);
        $groupname=$row['group_name'];



if($num>0)

{


$arr = array();


    foreach ($doctors as $key => $doctor) {
        array_push($arr, array(
            "id" => $doctor['id'],
             "node_id" => $doctor['node_id'],  
            "group_name" => $doctor['group_name'],
            "group_req_id" => $doctor['group_req_id'],
      
 ));
 $db->query("update efactorproduct set group_name='NULL' where group_req_id='$groupid'");
           $db->query("update efactorproduct set group_req_id='NULL' where group_req_id='$groupid'");





$db->query("UPDATE users SET group_name = REPLACE(group_name, ', $groupname', ''), group_req_id = REPLACE(group_req_id, ', $groupid', '') WHERE access_token='$val'");

    }

   
                  
                 

                 $response = array(
    'status' => 'success',
    'description' => 'Success description'
    
);


$response_json = json_encode($response);
header('Content-Type: application/json');
exit($response_json);
                  
     }

   





else
{


header('HTTP/1.1 400');
            $response['status']='failure';
            $response['error_code']='101112';
            $response['description']='Device does not belong to user';

             exit($response);


}


}


}



/**************************node_update_group*********************/



if (isset($_GET['action']) && $_GET['action']=='node_update_group') {

$req = file_get_contents( 'php://input' ); 
$json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['nodes'])) {
    $nodes = $json_data['nodes'];


    foreach ($nodes as $node) {
         $node . PHP_EOL;
    }
    
}
if (is_array($json_data) && isset($json_data['group_name'])) {
    $group_name = $json_data['group_name'];
  
}
if (is_array($json_data) && isset($json_data['operation'])) {
    $operation = $json_data['operation'];
  
}



$groupid=$_GET['group_id'];


$header = apache_request_headers();  
foreach ($header as $key => $val) {


/********************/
/*$query = "SELECT * FROM users where access_token='$val'";
$doctors = $db->get_results($query);

if ($doctors) {
    $groupname = array();
    $groupId = array();

    foreach ($doctors as $row) {
        $groupNameParts = explode(' ', $row->group_name);
        $firstName = $groupNameParts[0];
        $groupname[] = $firstName;

        $groupids = explode(' ', $row->group_req_id);
        $firstId = $groupids[0];
        $groupId[] = $firstId;
    }

    if (!empty($groupname)) {
        $resultname = implode(',', $groupname);
     //   echo "Result Name: $resultname<br>";
    }

    if (!empty($groupId)) {
        $resultid = implode(',', $groupId);
     //   echo "Result ID: $resultid<br>";
    }
} else {
    echo "No results found.";
}

echo $resultname;*/


/********************/



 $query = "SELECT * FROM efactorproduct where group_req_id='$groupid'";

	$doctors = $db->get_results($query); 
	$num = $db->num_rows($query);
        $row = $db->get_row($query);
        $groupname=$row['group_name'];

if($num>0)

{


if(empty($operation))


   {


    if(!empty($group_name))
{

      $db->query("update efactorproduct set group_name='$group_name' where group_req_id='$groupid'");




$db->query("UPDATE users SET group_name = REPLACE(group_name, '$groupname', '$group_name') WHERE access_token='$val'");

/*---------------------------------*/


/*$query_nxtgrp = "SELECT group_name FROM users WHERE group_name > '$groupname' ORDER BY group_name ASC LIMIT 1";


$result = $db->query($query_nxtgrp);

    

if ($result) {
    
$row_nxtgrp = $db->get_row($query_nxtgrp);
$next_group_name = $row_nxtgrp['group_name'];

$group_req_name = explode(', ', $next_group_name);

   
  $position = array_search($groupname, $group_req_name);








//$gp_name = isset($groupNameParts[$position]) ? $groupNameParts[$position] : null;

echo $groupname;
echo $group_name;


}
*/

}



/*-------------------------------*/

    

 $response = array(
    'status' => 'success',
    'description' => 'Success description'
    
);


$response_json = json_encode($response);
header('Content-Type: application/json');
exit($response_json);


}



else{





   if($operation == 'add')

   {


     foreach ($nodes as $key => $node) {

     
            
                   $db->query("update efactorproduct set group_name='$groupname' where node_id='$node'");
                   $db->query("update efactorproduct set group_req_id='$groupid' where node_id='$node'");
                  

        }


 $response = array(
    'status' => 'success',
    'description' => 'Success description'
    
);


$response_json = json_encode($response);
header('Content-Type: application/json');
exit($response_json);


}

else

{


         

             foreach ($nodes as $key => $node) {
            
                   $db->query("update efactorproduct set group_name='NULL' where node_id='$node'");
                   $db->query("update efactorproduct set group_req_id='NULL' where node_id='$node'");
                  

        }


 $response = array(
    'status' => 'success',
    'description' => 'Success description'
    
);


$response_json = json_encode($response);
header('Content-Type: application/json');
exit($response_json);



}




}






                  
                 

            
                  
     }

   





else
{


            header('HTTP/1.1 400');
            $response['status']='failure';
            $response['error_code']='101112';
            $response['description']='Device does not belong to user';

             exit($response);


}


}


}

/************************************************************************************/


if (isset($_GET['action']) && $_GET['action']=='user_signupLocation') {

$req = file_get_contents( 'php://input' ); 
$json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['latitude'])) {
    $latitude = $json_data['latitude'];
    
}
if (is_array($json_data) && isset($json_data['longitude'])) {
    $longitude = $json_data['longitude'];
    
}

if (is_array($json_data) && isset($json_data['app_version'])) {
    $app_version = $json_data['app_version'];
    
}

if (is_array($json_data) && isset($json_data['email'])) {
    $email = $json_data['email'];
    
}


if (is_array($json_data) && isset($json_data['mobile_number'])) {
    $mobile_number = $json_data['mobile_number'];
    
}






//$header = apache_request_headers();  
//foreach ($header as $key => $val) {


    $query = "SELECT * FROM users where email='$email' OR phone='$mobile_number'";
	$doctors = $db->get_results($query); 
	$num = $db->num_rows($query);
      $row = $db->get_row($query);
	$userid=$row['id'];



if($num>0)
{
 //if($latitude !== 'NULL' && $longitude !== 'NULL')
     {
        /*  $db->query("update efactorproduct set signin_latitude='$latitude' where access_token='$val'");
          $db->query("update efactorproduct set signin_longitude='$longitude' where access_token='$val'");
          $db->query("update efactorproduct set fw_version='$app_version' where access_token='$val'");*/


          $db->query("update users set signup_latitude='$latitude' where id='$userid'");
          $db->query("update users set signup_longitude='$longitude' where id='$userid'");
          $db->query("update users set fw_version='$app_version' where id='$userid'");
          

      }
$resp = '{"status": "success"}';
        exit($resp);
}

else
{
$response['error']='access_token not valid';
}
         

//headers_remove("$Autherization");
//}

}
/******************************************************************/









/*****************************************************************/
/***********************CurrentLocation***************************/

if (isset($_GET['action']) && $_GET['action']=='user_currentLocation') {

$req = file_get_contents( 'php://input' ); 
$json_data = json_decode($req, true);

if (is_array($json_data) && isset($json_data['latitude'])) {
    $latitude = $json_data['latitude'];
    
}
if (is_array($json_data) && isset($json_data['longitude'])) {
    $longitude = $json_data['longitude'];
    
}

if (is_array($json_data) && isset($json_data['app_version'])) {
    $app_version = $json_data['app_version'];
    
}


$header = apache_request_headers();  
foreach ($header as $key => $val) {


    $query = "SELECT * FROM users where access_token='$val'";
	$doctors = $db->get_results($query); 
	$num = $db->num_rows($query);
      $row = $db->get_row($query);
	$userid=$row['id'];

if($num>0)
{
// if($latitude !== 'NULL' && $longitude !== 'NULL')
     {
          $db->query("update efactorproduct set signin_latitude='$latitude' where access_token='$val'");
          $db->query("update efactorproduct set signin_longitude='$longitude' where access_token='$val'");
          $db->query("update efactorproduct set fw_version='$app_version' where access_token='$val'");

          $db->query("update users set signin_latitude='$latitude' where access_token='$val'");
          $db->query("update users set signin_longitude='$longitude' where access_token='$val'");
          $db->query("update users set fw_version='$app_version' where access_token='$val'");

      }
$resp = '{"status": "success"}';
        exit($resp);
}

else
{
$response['error']='access_token not valid';
}
         

//headers_remove("$Autherization");
}

}
/******************************************************************/

/**************************************user_get_nodes_config_reload_default****************************************/

if (isset($_GET['action']) && $_GET['action'] == 'user_get_nodes_config_reload_default') {

    $nodeid = $_GET['node_id'];
    $deviceid = $_GET['device_id'];

    $header = apache_request_headers();  
    foreach ($header as $key => $val) {

        $query_1 = "SELECT * FROM efactorproduct where access_token='$val' and node_id='$nodeid'";
        $doctors = $db->get_results($query_1); 
        $row = $db->get_row($query_1); 
        $device_type = $row['device_type']; 
        $curr_devicetype = $row['current_devicetype'];
        $recipe = $row['recipe'];


        // Define default settings for different recipes
       $default_settings = array(
  "RICE" => array("Dishes"=>"RICE","Cooker Timer"=>60,"Cooking temperature"=>140,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>160,"Coooker max-temperature"=>180,"Cooker temp constant mode"=>false,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),

    "BROWNRICE" => array("Dishes"=>"BROWNRICE","Cooker Timer"=>60,"Cooking temperature"=>140,"Keep warm temperature"=>40,"Pre-timer  temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>160,"Coooker max-temperature"=>180,"Cooker temp constant mode"=>false,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),

    "FRY" => array("Dishes"=>"FRY","Cooker Timer"=>80,"Cooking temperature"=>180,"Keep warm temperature"=>40,"Pre-timer  temp constant mode"=>false,"Pre-timer"=>15,"Pre – temperature"=>180,"Coooker max-temperature"=>280,"Cooker temp constant mode"=>true,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),

    "MILK" => array("Dishes"=>"MILK","Cooker Timer"=>30,"Cooking temperature"=>100,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>110,"Coooker max-temperature"=>150,"Cooker temp constant mode"=>true,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "SOUP" => array("Dishes"=>"SOUP","Cooker Timer"=>30,"Cooking temperature"=>150,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>180,"Coooker max-temperature"=>180,"Cooker temp constant mode"=>false,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "BIRIYANI" => array("Dishes"=>"BIRIYANI","Cooker Timer"=>45,"Cooking temperature"=>240,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>true,"Pre-timer"=>0,"Pre – temperature"=>180,"Coooker max-temperature"=>180,"Cooker temp constant mode"=>false,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>0,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "IDLY" => array("Dishes"=>"IDLY","Cooker Timer"=>40,"Cooking temperature"=>150,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>160,"Coooker max-temperature"=>160,"Cooker temp constant mode"=>false,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "DHAL" => array("Dishes"=>"DHAL","Cooker Timer"=>45,"Cooking temperature"=>150,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>160,"Coooker max-temperature"=>150,"Cooker temp constant mode"=>false,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "HARDVEG" => array("Dishes"=>"HARDVEG","Cooker Timer"=>40,"Cooking temperature"=>200,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>240,"Coooker max-temperature"=>240,"Cooker temp constant mode"=>true,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "WATER" => array("Dishes"=>"WATER","Cooker Timer"=>30,"Cooking temperature"=>150,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>160,"Coooker max-temperature"=>160,"Cooker temp constant mode"=>false,"Keep Warm timer"=>0,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "DEEPFRY" => array("Dishes"=>"DEEPFRY","Cooker Timer"=>80,"Cooking temperature"=>220,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>5,"Pre – temperature"=>180,"Coooker max-temperature"=>290,"Cooker temp constant mode"=>true,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "POPCORN" => array("Dishes"=>"POPCORN","Cooker Timer"=>5,"Cooking temperature"=>220,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>3,"Pre – temperature"=>160,"Coooker max-temperature"=>290,"Cooker temp constant mode"=>true,"Keep Warm timer"=>0,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>false,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "CAKE" => array("Dishes"=>"CAKE","Cooker Timer"=>45,"Cooking temperature"=>220,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>5,"Pre – temperature"=>180,"Coooker max-temperature"=>290,"Cooker temp constant mode"=>true,"Keep Warm timer"=>0,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>false,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),

 "MAGGI" => array("Dishes"=>"CAKE","Cooker Timer"=>5,"Cooking temperature"=>110,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>5,"Pre – temperature"=>90,"Coooker max-temperature"=>180,"Cooker temp constant mode"=>false,"Keep Warm timer"=>0,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>false,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),

 "NOODLES" => array("Dishes"=>"CAKE","Cooker Timer"=>10,"Cooking temperature"=>220,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>8,"Pre – temperature"=>180,"Coooker max-temperature"=>180,"Cooker temp constant mode"=>false,"Keep Warm timer"=>0,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>false,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),

"DIY" => array("Dishes"=>"DIY","Cooker Timer"=>80,"Cooking temperature"=>180,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>true,"Pre-timer"=>5,"Pre – temperature"=>180,"Coooker max-temperature"=>255,"Cooker temp constant mode"=>true,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0)
   
);


 /*"" => array("Dishes"=>"RICE","Cooker Timer"=>45,"Cooking temperature"=>150,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>160,"Coooker max-temperature"=>180,"Cooker temp constant mode"=>false,"Keep Warm timer"=>30,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0)*/



        // Assign the default settings based on the recipe value
        $cooker = $default_settings[$recipe] ?? array(); // Use the recipe value to get the corresponding settings

        if($device_type == 'cooker') {
            if($curr_devicetype != 'NULL') {
                $devices_cooker = json_encode([$curr_devicetype => $cooker]);
            } else {
                $devices_cooker = json_encode([$device_type => $cooker]);
            }



       $update_query = "UPDATE efactorproduct SET 
                cooker_timer_1 = '{$cooker["Cooker Timer"]}',
                cooker_max_temperature_1 = '{$cooker["Cooking temperature"]}',
                Keep_warm_1 = '{$cooker["Keep warm temperature"]}',
                Pre_timer_constant_mode = '{$cooker["Pre-timer temp constant mode"]}',
                Pre_timer = '{$cooker["Pre-timer"]}',
                Pre_temperature = '{$cooker["Pre – temperature"]}',
                Coooker_max_temperature = '{$cooker["Coooker max-temperature"]}',
                Cooker_constant_mode = '{$cooker["Cooker constant mode"]}',
                Keep_Warm_timer_1 = '{$cooker["Keep Warm timer"]}',
                pre_timer_wait_temperature_constant_mode = '{$cooker["pre-timer wait temperature constant mode"]}',
                pre_timer_waiting_time = '{$cooker["pre-timer waiting time"]}',
                pre_timer_Waiting_temperature = '{$cooker["pre-timer Waiting temperature"]}',
                warm_constant_cut_off = '{$cooker["keep warm temp constant mode"]}',
                cook_wait_temp_constant_mode = '{$cooker["cook wait temp constant mode"]}',
                cook_waiting_time = '{$cooker["cook waiting time"]}',
                cook_Waiting_temperature = '{$cooker["cook Waiting- temperature"]}',
                warm_wait_temp_constant_mode = '{$cooker["warm-wait temp constant mode"]}',
                warm_waiting_time = '{$cooker["warm waiting time"]}',
                warm_Waiting_temperature = '{$cooker["warm Waiting temperature"]}',
                cooker_ok_button = '{$cooker["OK"]}',
                reload_default = '{$cooker["Reload Default"]}'
                WHERE access_token='$val' AND node_id='$nodeid'";
            
            $db->query($update_query);

            echo $devices_cooker;
        }
    }
}







/*********************************************************************************/





















//Output
echo json_encode($response);
//echo json_data;
exit;



?>
-temperature"=>150,"Cooker temp constant mode"=>true,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "SOUP" => array("Dishes"=>"SOUP","Cooker Timer"=>30,"Cooking temperature"=>150,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>180,"Coooker max-temperature"=>180,"Cooker temp constant mode"=>false,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "BIRIYANI" => array("Dishes"=>"BIRIYANI","Cooker Timer"=>45,"Cooking temperature"=>240,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>true,"Pre-timer"=>0,"Pre – temperature"=>180,"Coooker max-temperature"=>180,"Cooker temp constant mode"=>false,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>0,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "IDLY" => array("Dishes"=>"IDLY","Cooker Timer"=>40,"Cooking temperature"=>150,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>160,"Coooker max-temperature"=>160,"Cooker temp constant mode"=>false,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "DHAL" => array("Dishes"=>"DHAL","Cooker Timer"=>45,"Cooking temperature"=>150,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>160,"Coooker max-temperature"=>150,"Cooker temp constant mode"=>false,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "HARDVEG" => array("Dishes"=>"HARDVEG","Cooker Timer"=>40,"Cooking temperature"=>200,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>240,"Coooker max-temperature"=>240,"Cooker temp constant mode"=>true,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "WATER" => array("Dishes"=>"WATER","Cooker Timer"=>30,"Cooking temperature"=>150,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>160,"Coooker max-temperature"=>160,"Cooker temp constant mode"=>false,"Keep Warm timer"=>0,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "DEEPFRY" => array("Dishes"=>"DEEPFRY","Cooker Timer"=>80,"Cooking temperature"=>220,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>5,"Pre – temperature"=>180,"Coooker max-temperature"=>290,"Cooker temp constant mode"=>true,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "POPCORN" => array("Dishes"=>"POPCORN","Cooker Timer"=>5,"Cooking temperature"=>220,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>3,"Pre – temperature"=>160,"Coooker max-temperature"=>290,"Cooker temp constant mode"=>true,"Keep Warm timer"=>0,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>false,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "CAKE" => array("Dishes"=>"CAKE","Cooker Timer"=>45,"Cooking temperature"=>220,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>5,"Pre – temperature"=>180,"Coooker max-temperature"=>290,"Cooker temp constant mode"=>true,"Keep Warm timer"=>0,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>false,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),

 "MAGGI" => array("Dishes"=>"CAKE","Cooker Timer"=>5,"Cooking temperature"=>110,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>5,"Pre – temperature"=>90,"Coooker max-temperature"=>180,"Cooker temp constant mode"=>false,"Keep Warm timer"=>0,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>false,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),

 "NOODLES" => array("Dishes"=>"CAKE","Cooker Timer"=>10,"Cooking temperature"=>220,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>8,"Pre – temperature"=>180,"Coooker max-temperature"=>180,"Cooker temp constant mode"=>false,"Keep Warm timer"=>0,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>false,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),

"DIY" => array("Dishes"=>"DIY","Cooker Timer"=>80,"Cooking temperature"=>180,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>true,"Pre-timer"=>5,"Pre – temperature"=>180,"Coooker max-temperature"=>255,"Cooker temp constant mode"=>true,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0)
   
);


 /*"" => array("Dishes"=>"RICE","Cooker Timer"=>45,"Cooking temperature"=>150,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>160,"Coooker max-temperature"=>180,"Cooker temp constant mode"=>false,"Keep Warm timer"=>30,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0)*/



        // Assign the default settings based on the recipe value
        $cooker = $default_settings[$recipe] ?? array(); // Use the recipe value to get the corresponding settings

        if($device_type == 'cooker') {
            if($curr_devicetype != 'NULL') {
                $devices_cooker = json_encode([$curr_devicetype => $cooker]);
            } else {
                $devices_cooker = json_encode([$device_type => $cooker]);
            }



       $update_query = "UPDATE efactorproduct SET 
                cooker_timer_1 = '{$cooker["Cooker Timer"]}',
                cooker_max_temperature_1 = '{$cooker["Cooking temperature"]}',
                Keep_warm_1 = '{$cooker["Keep warm temperature"]}',
                Pre_timer_constant_mode = '{$cooker["Pre-timer temp constant mode"]}',
                Pre_timer = '{$cooker["Pre-timer"]}',
                Pre_temperature = '{$cooker["Pre – temperature"]}',
                Coooker_max_temperature = '{$cooker["Coooker max-temperature"]}',
                Cooker_constant_mode = '{$cooker["Cooker constant mode"]}',
                Keep_Warm_timer_1 = '{$cooker["Keep Warm timer"]}',
                pre_timer_wait_temperature_constant_mode = '{$cooker["pre-timer wait temperature constant mode"]}',
                pre_timer_waiting_time = '{$cooker["pre-timer waiting time"]}',
                pre_timer_Waiting_temperature = '{$cooker["pre-timer Waiting temperature"]}',
                warm_constant_cut_off = '{$cooker["keep warm temp constant mode"]}',
                cook_wait_temp_constant_mode = '{$cooker["cook wait temp constant mode"]}',
                cook_waiting_time = '{$cooker["cook waiting time"]}',
                cook_Waiting_temperature = '{$cooker["cook Waiting- temperature"]}',
                warm_wait_temp_constant_mode = '{$cooker["warm-wait temp constant mode"]}',
                warm_waiting_time = '{$cooker["warm waiting time"]}',
                warm_Waiting_temperature = '{$cooker["warm Waiting temperature"]}',
                cooker_ok_button = '{$cooker["OK"]}',
                reload_default = '{$cooker["Reload Default"]}'
                WHERE access_token='$val' AND node_id='$nodeid'";
            
            $db->query($update_query);

            echo $devices_cooker;
        }
    }
}







/*********************************************************************************/





















//Output
echo json_encode($response);
//echo json_data;
exit;



?>
******************/





















//Output
echo json_encode($response);
//echo json_data;
exit;



?>
-temperature"=>150,"Cooker temp constant mode"=>true,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "SOUP" => array("Dishes"=>"SOUP","Cooker Timer"=>30,"Cooking temperature"=>150,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>180,"Coooker max-temperature"=>180,"Cooker temp constant mode"=>false,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "BIRIYANI" => array("Dishes"=>"BIRIYANI","Cooker Timer"=>45,"Cooking temperature"=>240,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>true,"Pre-timer"=>0,"Pre – temperature"=>180,"Coooker max-temperature"=>180,"Cooker temp constant mode"=>false,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>0,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "IDLY" => array("Dishes"=>"IDLY","Cooker Timer"=>40,"Cooking temperature"=>150,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>160,"Coooker max-temperature"=>160,"Cooker temp constant mode"=>false,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "DHAL" => array("Dishes"=>"DHAL","Cooker Timer"=>45,"Cooking temperature"=>150,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>160,"Coooker max-temperature"=>150,"Cooker temp constant mode"=>false,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "HARDVEG" => array("Dishes"=>"HARDVEG","Cooker Timer"=>40,"Cooking temperature"=>200,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>240,"Coooker max-temperature"=>240,"Cooker temp constant mode"=>true,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "WATER" => array("Dishes"=>"WATER","Cooker Timer"=>30,"Cooking temperature"=>150,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>160,"Coooker max-temperature"=>160,"Cooker temp constant mode"=>false,"Keep Warm timer"=>0,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "DEEPFRY" => array("Dishes"=>"DEEPFRY","Cooker Timer"=>80,"Cooking temperature"=>220,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>5,"Pre – temperature"=>180,"Coooker max-temperature"=>290,"Cooker temp constant mode"=>true,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "POPCORN" => array("Dishes"=>"POPCORN","Cooker Timer"=>5,"Cooking temperature"=>220,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>3,"Pre – temperature"=>160,"Coooker max-temperature"=>290,"Cooker temp constant mode"=>true,"Keep Warm timer"=>0,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>false,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),


    "CAKE" => array("Dishes"=>"CAKE","Cooker Timer"=>45,"Cooking temperature"=>220,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>5,"Pre – temperature"=>180,"Coooker max-temperature"=>290,"Cooker temp constant mode"=>true,"Keep Warm timer"=>0,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>false,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),

 "MAGGI" => array("Dishes"=>"CAKE","Cooker Timer"=>5,"Cooking temperature"=>110,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>5,"Pre – temperature"=>90,"Coooker max-temperature"=>180,"Cooker temp constant mode"=>false,"Keep Warm timer"=>0,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>false,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),

 "NOODLES" => array("Dishes"=>"CAKE","Cooker Timer"=>10,"Cooking temperature"=>220,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>8,"Pre – temperature"=>180,"Coooker max-temperature"=>180,"Cooker temp constant mode"=>false,"Keep Warm timer"=>0,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>false,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0),

"DIY" => array("Dishes"=>"DIY","Cooker Timer"=>80,"Cooking temperature"=>180,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>true,"Pre-timer"=>5,"Pre – temperature"=>180,"Coooker max-temperature"=>255,"Cooker temp constant mode"=>true,"Keep Warm timer"=>60,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0)
   
);


 /*"" => array("Dishes"=>"RICE","Cooker Timer"=>45,"Cooking temperature"=>150,"Keep warm temperature"=>40,"Pre-timer temp constant mode"=>false,"Pre-timer"=>0,"Pre – temperature"=>160,"Coooker max-temperature"=>180,"Cooker temp constant mode"=>false,"Keep Warm timer"=>30,"pre-timer wait temperature constant mode"=>false,"pre-timer waiting time"=>0,"pre-timer Waiting temperature"=>0,"keep warm temp constant mode"=>true,"cook wait temp constant mode"=>false,"cook waiting time"=>0,"cook Waiting temperature"=>0,"warm-wait temp constant mode"=>false,"warm waiting time"=>0,"warm Waiting temperature"=>0,"OK"=>0,"Reload Default"=>0)*/



        // Assign the default settings based on the recipe value
        $cooker = $default_settings[$recipe] ?? array(); // Use the recipe value to get the corresponding settings

        if($device_type == 'cooker') {
            if($curr_devicetype != 'NULL') {
                $devices_cooker = json_encode([$curr_devicetype => $cooker]);
            } else {
                $devices_cooker = json_encode([$device_type => $cooker]);
            }



       $update_query = "UPDATE efactorproduct SET 
                cooker_timer_1 = '{$cooker["Cooker Timer"]}',
                cooker_max_temperature_1 = '{$cooker["Cooking temperature"]}',
                Keep_warm_1 = '{$cooker["Keep warm temperature"]}',
                Pre_timer_constant_mode = '{$cooker["Pre-timer temp constant mode"]}',
                Pre_timer = '{$cooker["Pre-timer"]}',
                Pre_temperature = '{$cooker["Pre – temperature"]}',
                Coooker_max_temperature = '{$cooker["Coooker max-temperature"]}',
                Cooker_constant_mode = '{$cooker["Cooker constant mode"]}',
                Keep_Warm_timer_1 = '{$cooker["Keep Warm timer"]}',
                pre_timer_wait_temperature_constant_mode = '{$cooker["pre-timer wait temperature constant mode"]}',
                pre_timer_waiting_time = '{$cooker["pre-timer waiting time"]}',
                pre_timer_Waiting_temperature = '{$cooker["pre-timer Waiting temperature"]}',
                warm_constant_cut_off = '{$cooker["keep warm temp constant mode"]}',
                cook_wait_temp_constant_mode = '{$cooker["cook wait temp constant mode"]}',
                cook_waiting_time = '{$cooker["cook waiting time"]}',
                cook_Waiting_temperature = '{$cooker["cook Waiting- temperature"]}',
                warm_wait_temp_constant_mode = '{$cooker["warm-wait temp constant mode"]}',
                warm_waiting_time = '{$cooker["warm waiting time"]}',
                warm_Waiting_temperature = '{$cooker["warm Waiting temperature"]}',
                cooker_ok_button = '{$cooker["OK"]}',
                reload_default = '{$cooker["Reload Default"]}'
                WHERE access_token='$val' AND node_id='$nodeid'";
            
            $db->query($update_query);

            echo $devices_cooker;
        }
    }
}


















/*********************************************************************************/



//Output
echo json_encode($response);
//echo json_data;
exit;



?>
