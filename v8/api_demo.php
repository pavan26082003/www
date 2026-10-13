<?php

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





function handleResponse($topic, $message) {


echo 'hi';
    global $response_topic;

    if ($topic == 'Efactor/things/FFFFFFFFFFFFFFFF7EC2000000004200/event') {
        // Process the response message
        echo "Received response: " . $message . "\n";
        
        // Here, you can exit the loop or take further actions
        exit;
    }
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

/******************************************************************/
/*-----------------------------forgotpwd--------------------------*/

if (isset($_GET['action']) && $_GET['action']=='forgotpassword') {

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
echo $phone;

$arr=array();

/*-------------------------------------------------------*/

if($num_dev>0)
  { 

/***********************************/
  /* $otp = rand(100000,999999);
	   

         $password = '&password=12345678';
         $type = '&type=TEXT';
         $mobile= '&mobile=';
         $sender = '&sender=EFACTR';
         $message_1 = '&message=Dear%20customer%2C%20Your%20OTP%20for%20sms%20notification%20is%20';  //%7B
         $message_2 = '%20.%20Use%20this%20OTP%20to%20register%20%20EFACTR';    //%7D
         $PEID = '&PEID=1201159585221010777';
         $HeaderId = '&HeaderId=1205165538224202626';
         $templateId = '&templateId=1207165579677863503';

         $req="http://sms.softpal.com/sendsms/sendsms.php?username=SPEFACTR".$password.$type.$mobile.$phone.$sender.$message_1.$otp.$message_2.$PEID.$HeaderId.$templateId;


//working
        

	      $sms = file_get_contents($req);
	      $sms_status = json_decode($sms, true);

//echo $req; 
            $db->query("insert into otps set phone='".$_POST['phone']."',otp='".$otp."'");


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

      $response['status']='success';*/



         header('HTTP/1.1 400');
         $response['status']='failure';
         $response['error_code']='10100';
         $response['description']='I do not have the permission to delete this account.';
      
  }
  else
  {
  	// $response['status']='error';
  	 // $response['message']='Not a regitered user';
header('HTTP/1.1 400');
         $response['status']='failure';
         $response['error_code']='10100';
         $response['description']='I do not have the permission to delete this account.';
  }

   
/*------------------------------------------------------*/
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


//$arr=array();


if($num>0)
{
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

if (isset($_GET['action']) && $_GET['action']=='user') {

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
              $db->query("insert into otps set phone='$phone_1',otp='".$otp."',password='$password_1',email='$email_1',country_code='+86'");
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
              $db->query("insert into otps set phone='$phone_1',otp='".$otp."',password='password_1',email='$email_1',country_code='+86'");
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



/*******************************************************************************************/
/*******************************************userverify**************************************/
if (isset($_GET['action']) && $_GET['action']=='userverify') {

$req = file_get_contents( 'php://input' );
//echo $req; 
$json_data = json_decode($req, true);
$dt=date("Y-m-d H:i:s");

if (is_array($json_data) && isset($json_data['phone']) && isset($json_data['verification_code'])) {
    $phone = $json_data['phone'];
    $verificationcode = $json_data['verification_code'];
}



 if($phone!='' && $verificationcode!='')
   {





     $query = "SELECT * FROM otps where phone='$phone' and otp='$verificationcode'";
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
           $query_verify = "SELECT * FROM `otps` where phone='$phone' and otp='$verificationcode'";
           $doctors = $db->get_results($query_verify); 
           $row_verify = $db->get_row($query_verify); 
           $email=$row_verify['email'];

           // $db->query("insert into users set phone='$phone',email='$email',password='$password',user_type='staff',created_at='".$dt."'");
            $db->query("insert into users set phone='$phone',email='$email',password='$password',user_type='staff',created_at='".$dt."',app_version='v8'");
            
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

if (is_array($json_data) && isset($json_data['username']) && isset($json_data['password'])) {
    $username = $json_data['username'];
    $password_1 = $json_data['password'];
}


//$query = "SELECT * FROM `users` where phone='".$_POST['username']."' and password='".$_POST['password']."'";

$query = "SELECT * FROM `users` WHERE email = '$username' OR phone = '$username'";

/*$query = "SELECT * FROM `users` where phone='".$_POST['phone']."' and status='active' and deleted=0 and disable_login=0";*/

  $num = $db->num_rows($query); 
  if($num>0)
  { 
    $row = $db->get_row($query); 
    $password=$row['password']; 
    $phone = $row['phone']; 
    $userid = $row['id']; 
   



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


/*------------*/

/*---------------------------------------------------------------------------------------*/


/**********************************************************************************************/
/*---------------------------------------------------------------------------------------*/
/*****************************************************************************************/
/*-----------------------------------user-nodes-params---------------------------------*/

if (isset($_GET['action']) && $_GET['action']=='user_update_nodes_params') {

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



/*
    // Update database based on conditions
    if (!is_null($speed)) {
        $db->query("UPDATE efactorproduct SET speed='$speed' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated speed';
    } 
    if (!is_null($payload)) {
        $db->query("UPDATE efactorproduct SET payload='$payload' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated payload';
    } 
    if (!is_null($name)) {
        $db->query("UPDATE efactorproduct SET name='$name' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 
    if (!is_null($led)) {
        $db->query("UPDATE efactorproduct SET led_indication='$led' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 

    if (!is_null($latitude)) {
        $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 

    if (!is_null($longitude)) {
        $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 



    if (!is_null($baudrate)) {
        $db->query("UPDATE efactorproduct SET baudrate='$baudrate' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated baudrate';
    } 
    if (!is_null($errorcode)) {
        $db->query("UPDATE efactorproduct SET errorcode='$errorcode' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated errorcode';
    } 
    if (!is_null($comm_mode)) {
        $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated comm_mode';
    } 
    if (!is_null($uartmsgid)) {
        $db->query("UPDATE efactorproduct SET uartmsgid='$uartmsgid' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated uartmsgid';
    } 
    if (!is_null($time)) {
        $db->query("UPDATE efactorproduct SET time='$time' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated time';
    } 

    if (!is_null($services)) {
        // Handle services update if needed
    } 
    if (!is_null($Hue)) {


        $db->query("UPDATE efactorproduct SET Hue='$Hue' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated hue';
    } 
    if (!is_null($direction)) {
        $db->query("UPDATE efactorproduct SET direction='$direction' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated direction';
    } 
    if (!is_null($fan_buzzer)) {
        $fan_buzzer_value = $fan_buzzer == "1" ? 'true' : 'false';
        $db->query("UPDATE efactorproduct SET fan_buzzer='$fan_buzzer_value' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated fan_buzzer';
    } 

if (!is_null($fan_resistance)) {
    $db->query("UPDATE efactorproduct SET fan_resistance='$fan_resistance' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_resistance';
} 
if (!is_null($fan_lmin)) {
    $db->query("UPDATE efactorproduct SET fan_lmin='$fan_lmin' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_lmin';
} 
if (!is_null($fan_lmax)) {
    $db->query("UPDATE efactorproduct SET fan_lmax='$fan_lmax' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_lmax';
} 
if (!is_null($fan_frequency)) {
    $db->query("UPDATE efactorproduct SET fan_frequency='$fan_frequency' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_frequency';
} 
if (!is_null($fan_pole_pairs)) {
    $db->query("UPDATE efactorproduct SET fan_pairs='$fan_pole_pairs' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_pole_pairs';
} 
if (!is_null($fan_open_loop_current)) {
    $db->query("UPDATE efactorproduct SET fan_open_loop_current='$fan_open_loop_current' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_open_loop_current';
} 
if (!is_null($fan_power_limit)) {
    $db->query("UPDATE efactorproduct SET fan_power_limit='$fan_power_limit' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_power_limit';
} 
  if (!is_null($fan_torque_limit)) {
    $db->query("UPDATE efactorproduct SET fan_torque_limit='$fan_torque_limit' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_torque_limit';
} 
if (!is_null($fan_speed1)) {
    $db->query("UPDATE efactorproduct SET fan_speed1='$fan_speed1' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_speed1';
} 
if (!is_null($fan_speed2)) {
    $db->query("UPDATE efactorproduct SET fan_speed2='$fan_speed2' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_speed2';
} 
if (!is_null($fan_speed3)) {
    $db->query("UPDATE efactorproduct SET fan_speed3='$fan_speed3' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_speed3';
} 
if (!is_null($fan_speed4)) {
    $db->query("UPDATE efactorproduct SET fan_speed4='$fan_speed4' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_speed4';
} 
if (!is_null($fan_speed5)) {
    $db->query("UPDATE efactorproduct SET fan_speed5='$fan_speed5' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_speed5';
} 
if (!is_null($fan_speed6)) {
    $db->query("UPDATE efactorproduct SET fan_speed6='$fan_speed6' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_speed6';
} 
if (!is_null($fan_speed7)) {
    $db->query("UPDATE efactorproduct SET fan_speed7='$fan_speed7' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_speed7';
} 
if (!is_null($fan_button)) {
    $db->query("UPDATE efactorproduct SET fan_button='$fan_button' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_button';
} 
if (!is_null($fan_buzzer)) {
    $fan_buzzer_value = $fan_buzzer == "1" ? 'true' : 'false';
    $db->query("UPDATE efactorproduct SET fan_buzzer='$fan_buzzer_value' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_buzzer';
}

else {
        // Handle unrecognized parameters or provide an error message
        $response['status'] = 'error: unrecognized parameters';
    }*/
} 






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

       }

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
}


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
        $response['status'] = 'successfully updated power';
    } 
    if (!is_null($payload)) {
        $db->query("UPDATE efactorproduct SET payload='$payload' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated payload';
    } 



    if (!is_null($latitude)) {
        $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 

    if (!is_null($longitude)) {
        $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 



    if (!is_null($name)) {
        $db->query("UPDATE efactorproduct SET name='$name' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 
    if (!is_null($brightness)) {
        $db->query("UPDATE efactorproduct SET brightness='$brightness' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated brightness';
    } 

    if (!is_null($baudrate)) {
        $db->query("UPDATE efactorproduct SET baudrate='$baudrate' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated baudrate';
    } 
    if (!is_null($errorcode)) {
        $db->query("UPDATE efactorproduct SET errorcode='$errorcode' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated errorcode';
    } 
    if (!is_null($comm_mode)) {
        $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated comm_mode';
    } 
    if (!is_null($uartmsgid)) {
        $db->query("UPDATE efactorproduct SET uartmsgid='$uartmsgid' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated uartmsgid';
    } 
    if (!is_null($time)) {
        $db->query("UPDATE efactorproduct SET time='$time' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated time';
    } 

    if (!is_null($led_colour)) {
        $db->query("UPDATE efactorproduct SET led_colour='$led_colour' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated led_colour';
    } 
   

if (!is_null($latitude)) {
    $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated latitude';
} 
if (!is_null($longitude)) {
    $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated longitude';
} 
else {
        // Handle unrecognized parameters or provide an error message
        $response['status'] = 'error: unrecognized parameters';
    }
} else {
    // Handle invalid input or provide an error message
    $response['status'] = 'error: invalid input';
}











/********************************************/
if($addgateway != 'NULL')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$addgateway."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;

if($payload != 'null')
{



//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }


}



}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$nodeid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;



if($payload != 'null')
{


//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);

$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }


}
}










/*********************************************/
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
        $response['status'] = 'successfully updated power';
    } 
    if (!is_null($payload)) {
        $db->query("UPDATE efactorproduct SET payload='$payload' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated payload';
    } 

    if (!is_null($latitude)) {
        $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 

    if (!is_null($longitude)) {
        $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 


    if (!is_null($name)) {
        $db->query("UPDATE efactorproduct SET name='$name' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 
    if (!is_null($brightness)) {
        $db->query("UPDATE efactorproduct SET brightness='$brightness' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated brightness';
    } 

    if (!is_null($baudrate)) {
        $db->query("UPDATE efactorproduct SET baudrate='$baudrate' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated baudrate';
    } 
    if (!is_null($errorcode)) {
        $db->query("UPDATE efactorproduct SET errorcode='$errorcode' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated errorcode';
    } 
    if (!is_null($comm_mode)) {
        $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated comm_mode';
    } 
    if (!is_null($uartmsgid)) {
        $db->query("UPDATE efactorproduct SET uartmsgid='$uartmsgid' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated uartmsgid';
    } 
    if (!is_null($time)) {
        $db->query("UPDATE efactorproduct SET time='$time' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated time';
    } 

if (!is_null($latitude)) {
    $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated latitude';
} 
if (!is_null($longitude)) {
    $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated longitude';
} 
else {
        // Handle unrecognized parameters or provide an error message
        $response['status'] = 'error: unrecognized parameters';
    }
} else {
    // Handle invalid input or provide an error message
    $response['status'] = 'error: invalid input';
}

/************************************************************/

if($addgateway != 'NULL')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$addgateway."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;


if($payload != 'null')
{


//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }



}


}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$nodeid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;



if($payload != 'null')
{


//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }

}
}





/*************************************************************/



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
        $response['status'] = 'successfully updated power';
    }

    if (isset($json_data['name'])) {
        $name = $json_data['name'];
        $db->query("UPDATE efactorproduct SET name='$name' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    }

    if (isset($json_data['recipe'])) {
        $recipe = $json_data['recipe'];
        $db->query("UPDATE efactorproduct SET recipe='$recipe' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated recipe';
    }

    if (isset($json_data['cookermode'])) {
        $cookermode = $json_data['cookermode'];
        $db->query("UPDATE efactorproduct SET cookermode='$cookermode' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated cookermode';
    }

    if (isset($json_data['Baud_Rate'])) {
        $baudrate = $json_data['Baud_Rate'];
        $db->query("UPDATE efactorproduct SET baudrate='$baudrate' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated baudrate';
    }

    if (isset($json_data['Error_Code'])) {
        $errorcode = $json_data['Error_Code'];
        $db->query("UPDATE efactorproduct SET errorcode='$errorcode' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated errorcode';
    }

    if (isset($json_data['Communication_Mode'])) {
        $comm_mode = $json_data['Communication_Mode'];
        $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated comm_mode';
    }

    if (isset($json_data['Uart_Msg_Id'])) {
        $uartmsgid = $json_data['Uart_Msg_Id'];
        $db->query("UPDATE efactorproduct SET uartmsgid='$uartmsgid' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated uartmsgid';
    }

    if (isset($json_data['Time'])) {
        $time = $json_data['Time'];
        $db->query("UPDATE efactorproduct SET time='$time' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated time';
    }

    if (isset($json_data['payload'])) {
        $payload = $json_data['payload'];
        $db->query("UPDATE efactorproduct SET payload='$payload' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated payload';
    }

    if (isset($json_data['latitude'])) {
        $latitude = $json_data['latitude'];
        $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated latitude';
    }

    if (isset($json_data['longitude'])) {
        $longitude = $json_data['longitude'];
        $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated longitude';
    }

    // Configuration parameters
    if (isset($json_data['Cooker Timer'])) {
    $cooker_timer = $json_data['Cooker Timer'];
    $db->query("UPDATE efactorproduct SET cooker_timer_1='$cooker_timer' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated cooker_timer';
} else if (isset($json_data['Cooking temperature'])) {
    $Cooking_temperature = $json_data['Cooking temperature'];
    $db->query("UPDATE efactorproduct SET cooker_max_temperature_1='$Cooking_temperature' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated cooker_max_temperature';
} else if (isset($json_data['Keep warm temperature'])) {
    $Keep_warm_temperature = $json_data['Keep warm temperature'];
    $db->query("UPDATE efactorproduct SET Keep_warm_1='$Keep_warm_temperature' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated Keep_warm';
} else if (isset($json_data['Pre-timer temp constant mode'])) {
    $Pre_timer_constant_mode = $json_data['Pre-timer temp constant mode'];
    $db->query("UPDATE efactorproduct SET Pre_timer_constant_mode='$Pre_timer_constant_mode' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated Pre_timer_constant_mode';
} else if (isset($json_data['Pre-timer'])) {
    $Pre_timer = $json_data['Pre-timer'];
    $db->query("UPDATE efactorproduct SET Pre_timer='$Pre_timer' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated Pre_timer';
} else if (isset($json_data['Pre – temperature'])) {
    $Pre_temperature = $json_data['Pre – temperature'];
    $db->query("UPDATE efactorproduct SET Pre_temperature='$Pre_temperature' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated Pre_temperature';
} else if (isset($json_data['Coooker max-temperature'])) {
    $Coooker_max_temperature = $json_data['Coooker max-temperature'];
    $db->query("UPDATE efactorproduct SET Coooker_max_temperature='$Coooker_max_temperature' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated Coooker_max_temperature';
} else if (isset($json_data['Cooker temp constant mode'])) {
    $Cooker_constant_mode = $json_data['Cooker temp constant mode'];
    $db->query("UPDATE efactorproduct SET Cooker_constant_mode='$Cooker_constant_mode' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated Cooker_constant_mode';
} else if (isset($json_data['Keep Warm timer'])) {
    $Keep_Warm_timer = $json_data['Keep Warm timer'];
    $db->query("UPDATE efactorproduct SET Keep_Warm_timer_1='$Keep_Warm_timer' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated Keep_Warm_timer';
} else if (isset($json_data['Dishes'])) {
    $Dishes = $json_data['Dishes'];
    $db->query("UPDATE efactorproduct SET Dish='$Dishes' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated Dishes';
} else if (isset($json_data['pre-timer wait temperature constant mode'])) {
    $pre_timer_wait_temperature_constant_mode = $json_data['pre-timer wait temperature constant mode'];
    $db->query("UPDATE efactorproduct SET pre_timer_wait_temperature_constant_mode='$pre_timer_wait_temperature_constant_mode' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated pre_timer_wait_temperature_constant_mode';
} else if (isset($json_data['pre-timer waiting time'])) {
    $pre_timer_waiting_time = $json_data['pre-timer waiting time'];
    $db->query("UPDATE efactorproduct SET pre_timer_waiting_time='$pre_timer_waiting_time' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated pre_timer_waiting_time';
} else if (isset($json_data['pre-timer Waiting temperature'])) {
    $pre_timer_Waiting_temperature = $json_data['pre-timer Waiting temperature'];
    $db->query("UPDATE efactorproduct SET pre_timer_Waiting_temperature='$pre_timer_Waiting_temperature' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated pre_timer_Waiting_temperature';
} else if (isset($json_data['Keep warm temp constant mode'])) {
    $keep_warm_temp_constant_mode = $json_data['Keep warm temp constant mode'];
    $db->query("UPDATE efactorproduct SET keep_warm_temp_constant_mode='$keep_warm_temp_constant_mode' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated keep_warm_temp_constant_mode';
} else if (isset($json_data['cook wait temp constant mode'])) {
    $cook_wait_temp_constant_mode = $json_data['cook wait temp constant mode'];
    $db->query("UPDATE efactorproduct SET cook_wait_temp_constant_mode='$cook_wait_temp_constant_mode' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated cook_wait_temp_constant_mode';
} else if (isset($json_data['cook waiting time'])) {
    $cook_waiting_time = $json_data['cook waiting time'];
    $db->query("UPDATE efactorproduct SET cook_waiting_time='$cook_waiting_time' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated cook_waiting_time';
} else if (isset($json_data['cook Waiting- temperature'])) {
    $cook_Waiting_temperature = $json_data['cook Waiting- temperature'];
    $db->query("UPDATE efactorproduct SET cook_Waiting_temperature='$cook_Waiting_temperature' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated cook_Waiting_temperature';
} else if (isset($json_data['warm-wait temp constant mode'])) {
    $warm_wait_temp_constant_mode = $json_data['warm-wait temp constant mode'];
    $db->query("UPDATE efactorproduct SET warm_wait_temp_constant_mode='$warm_wait_temp_constant_mode' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated warm_wait_temp_constant_mode';
} else if (isset($json_data['warm waiting time'])) {
    $warm_waiting_time = $json_data['warm waiting time'];
    $db->query("UPDATE efactorproduct SET warm_waiting_time='$warm_waiting_time' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated warm_waiting_time';
} else if (isset($json_data['warm Waiting temperature'])) {
    $warm_Waiting_temperature = $json_data['warm Waiting temperature'];
    $db->query("UPDATE efactorproduct SET warm_Waiting_temperature='$warm_Waiting_temperature' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated warm_Waiting_temperature';
} else if (isset($json_data['OK'])) {
    $cooker_ok_button = $json_data['OK'];
    $db->query("UPDATE efactorproduct SET cooker_ok_button='$cooker_ok_button' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated cooker_ok_button';
} else if (isset($json_data['Reload Default'])) {
    $reload_default = $json_data['Reload Default'];
    $db->query("UPDATE efactorproduct SET reload_default='$reload_default' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated reload_default';
} else {
    $response['status'] = 'error: unrecognized parameters';
}

} else {
    $response['status'] = 'error: invalid input';
}


/************************************************************/

if($addgateway != 'NULL')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$addgateway."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;

echo $payload;

if($payload != 'null')
{



$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);

$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }



}


}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$nodeid."/".$command;



if($payload != 'null')
{



//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);

$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }

}
}




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
        $response['status'] = 'successfully updated power';
    } 
    if (!is_null($payload)) {
        $db->query("UPDATE efactorproduct SET payload='$payload' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated payload';
    } 

    if (!is_null($latitude)) {
        $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 

    if (!is_null($longitude)) {
        $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 

    if (!is_null($name)) {
        $db->query("UPDATE efactorproduct SET name='$name' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 

    if (!is_null($baudrate)) {
        $db->query("UPDATE efactorproduct SET baudrate='$baudrate' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated baudrate';
    } 
    if (!is_null($errorcode)) {
        $db->query("UPDATE efactorproduct SET errorcode='$errorcode' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated errorcode';
    } 
    if (!is_null($comm_mode)) {
        $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated comm_mode';
    } 
    if (!is_null($uartmsgid)) {
        $db->query("UPDATE efactorproduct SET uartmsgid='$uartmsgid' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated uartmsgid';
    } 
    if (!is_null($time)) {
        $db->query("UPDATE efactorproduct SET time='$time' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated time';
    } 

if (!is_null($latitude)) {
    $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated latitude';
} 
if (!is_null($longitude)) {
    $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated longitude';
} 
else {
        // Handle unrecognized parameters or provide an error message
        $response['status'] = 'error: unrecognized parameters';
    }
} else {
    // Handle invalid input or provide an error message
    $response['status'] = 'error: invalid input';
}










/******************************************************************/


if($addgateway != 'NULL')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$addgateway."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;


if($payload != 'null')
{


//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"address"=>$nodeid,
"device_id"=>$deviceid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }


}



}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$nodeid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;

if($payload != 'null')
{



//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"address"=>$nodeid,
"device_id"=>$deviceid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }

}
}



/*****************************************************************/
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
        $response['status'] = 'successfully updated power';
    } 
    if (!is_null($payload)) {
        $db->query("UPDATE efactorproduct SET payload='$payload' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated payload';
    } 


/*----------------------------------------------*/


if (!is_null($doctor)) {
        $db->query("UPDATE efactorproduct SET doctor='$doctor' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated doctor';
    } 
    if (!is_null($personal_assiatant)) {
        $db->query("UPDATE efactorproduct SET personal_assiatant='$personal_assiatant' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated personal_assiatant';
    } 
if (!is_null($nurse)) {
        $db->query("UPDATE efactorproduct SET nurse='$nurse' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated nurse';
    } 
    if (!is_null($doctor_response)) {
        $db->query("UPDATE efactorproduct SET doctor_response='$doctor_response' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated doctor_response';
    } 
if (!is_null($personal_assistant_resp)) {
        $db->query("UPDATE efactorproduct SET personal_assistant_resp='$personal_assistant_resp' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated personal_assistant_resp';
    } 
    if (!is_null($nurse_response)) {
        $db->query("UPDATE efactorproduct SET nurse_response='$nurse_response' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated nurse_response';
    } 


/*----------------------------------------------*/

    if (!is_null($latitude)) {
        $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 

    if (!is_null($longitude)) {
        $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 

    if (!is_null($name)) {
        $db->query("UPDATE efactorproduct SET name='$name' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 

    if (!is_null($baudrate)) {
        $db->query("UPDATE efactorproduct SET baudrate='$baudrate' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated baudrate';
    } 
    if (!is_null($errorcode)) {
        $db->query("UPDATE efactorproduct SET errorcode='$errorcode' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated errorcode';
    } 
    if (!is_null($comm_mode)) {
        $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated comm_mode';
    } 
    if (!is_null($uartmsgid)) {
        $db->query("UPDATE efactorproduct SET uartmsgid='$uartmsgid' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated uartmsgid';
    } 
    if (!is_null($time)) {
        $db->query("UPDATE efactorproduct SET time='$time' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated time';
    } 

if (!is_null($latitude)) {
    $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated latitude';
} 
if (!is_null($longitude)) {
    $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated longitude';
} 
else {
        // Handle unrecognized parameters or provide an error message
        $response['status'] = 'error: unrecognized parameters';
    }
} else {
    // Handle invalid input or provide an error message
    $response['status'] = 'error: invalid input';
}










/******************************************************************/


if($addgateway != 'NULL')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$addgateway."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;

if($payload != 'null')
{



//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"address"=>$nodeid,
"device_id"=>$deviceid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }



}


}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$nodeid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;

if($payload != 'null')
{



//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"address"=>$nodeid,
"device_id"=>$deviceid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }

}
}



/*****************************************************************/
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
        $response['status'] = 'successfully updated power';
    } 
    if (!is_null($payload)) {
        $db->query("UPDATE efactorproduct SET payload='$payload' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated payload';
    } 

   if (!is_null($latitude)) {
        $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 

    if (!is_null($longitude)) {
        $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 

    if (!is_null($name)) {
        $db->query("UPDATE efactorproduct SET name='$name' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 
 
    if (!is_null($baudrate)) {
        $db->query("UPDATE efactorproduct SET baudrate='$baudrate' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated baudrate';
    } 
    if (!is_null($errorcode)) {
        $db->query("UPDATE efactorproduct SET errorcode='$errorcode' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated errorcode';
    } 
    if (!is_null($comm_mode)) {
        $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated comm_mode';
    } 
    if (!is_null($uartmsgid)) {
        $db->query("UPDATE efactorproduct SET uartmsgid='$uartmsgid' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated uartmsgid';
    } 
    if (!is_null($time)) {
        $db->query("UPDATE efactorproduct SET time='$time' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated time';
    } 

    
    if (!is_null($mac)) {


        $db->query("UPDATE efactorproduct SET mac='$mac' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated mac';
    } 
    if (!is_null($module_type)) {
        $db->query("UPDATE efactorproduct SET module_type='$module_type' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated module_type';
    } 

if (!is_null($device_name)) {
    $db->query("UPDATE efactorproduct SET device_name='$device_name' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated device_name';
} 
if (!is_null($latitude)) {
    $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated latitude';
} 
if (!is_null($longitude)) {
    $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated longitude';
} 
if (!is_null($timer)) {
    $db->query("UPDATE efactorproduct SET timer='$timer' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated timer';
} 
if (!is_null($starter_relay_off_timer)) {
    $db->query("UPDATE efactorproduct SET starter_relay_off_timer='$starter_relay_off_timer' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated starter_relay_off_timer';
} 
if (!is_null($start_high_current_cutoff)) {
    $db->query("UPDATE efactorproduct SET start_high_current_cutoff='$start_high_current_cutoff' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated start_high_current_cutoff';
} 
if (!is_null($start_low_current_cutoff)) {
    $db->query("UPDATE efactorproduct SET start_low_current_cutoff='$start_low_current_cutoff' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated start_low_current_cutoff';
} 
  if (!is_null($Run_high_current_cutoff)) {
    $db->query("UPDATE efactorproduct SET Run_high_current_cutoff='$Run_high_current_cutoff' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated Run_high_current_cutoff';
} 
if (!is_null($Run_low_current_cutoff)) {
    $db->query("UPDATE efactorproduct SET Run_low_current_cutoff='$Run_low_current_cutoff' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated Run_low_current_cutoff';
} 
if (!is_null($high_voltage_cutoff)) {
    $db->query("UPDATE efactorproduct SET high_voltage_cutoff='$high_voltage_cutoff' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated high_voltage_cuttoff';
} 
if (!is_null($low_voltage_cutoff)) {
    $db->query("UPDATE efactorproduct SET low_voltage_cutoff='$low_voltage_cutoff' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated low_voltage_cutoff';
} 
if (!is_null($ultra_high_voltage_cutoff)) {
    $db->query("UPDATE efactorproduct SET ultra_high_voltage_cutoff='$ultra_high_voltage_cutoff' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated ultra_high_voltage_cutoff';
} 
if (!is_null($ultra_high_current_cutoff)) {
    $db->query("UPDATE efactorproduct SET ultra_high_current_cutoff='$ultra_high_current_cutoff' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated ultra_high_current_cutoff';
} 
if (!is_null($USER_SET_MAX_CURRENT_CUTOFF_LIMIT)) {
    $db->query("UPDATE efactorproduct SET USER_SET_MAX_CURRENT_CUTOFF_LIMIT='$USER_SET_MAX_CURRENT_CUTOFF_LIMIT' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated fan_speed6';
} 
if (!is_null($USER_AND_DEVICE_MAX_CURRENT_LIMIT)) {
    $db->query("UPDATE efactorproduct SET USER_AND_DEVICE_MAX_CURRENT_LIMIT='$USER_AND_DEVICE_MAX_CURRENT_LIMIT' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated USER_AND_DEVICE_MAX_CURRENT_LIMIT';
} 
if (!is_null($LOCK_ENABLE_DISABLE)) {
    $db->query("UPDATE efactorproduct SET LOCK_ENABLE_DISABLE='$LOCK_ENABLE_DISABLE' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated LOCK_ENABLE_DISABLE';
} 
else {
        // Handle unrecognized parameters or provide an error message
        $response['status'] = 'error: unrecognized parameters';
    }
} else {
    // Handle invalid input or provide an error message
    $response['status'] = 'error: invalid input';
}

/******************************************************************/


if($addgateway != 'NULL')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$addgateway."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;



if($payload != 'null')
{



//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"address"=>$nodeid,
"device_id"=>$deviceid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }


}



}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$nodeid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;


if($payload != 'null')
{



//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"address"=>$nodeid,
"device_id"=>$deviceid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }

}
}



/*****************************************************************/
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
        $response['status'] = 'successfully updated power';
    } 
    if (!is_null($payload)) {
        $db->query("UPDATE efactorproduct SET payload='$payload' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated payload';
    } 

    if (!is_null($latitude)) {
        $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 

    if (!is_null($longitude)) {
        $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 

    if (!is_null($name)) {
        $db->query("UPDATE efactorproduct SET name='$name' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated name';
    } 
    if (!is_null($lamp)) {
        $db->query("UPDATE efactorproduct SET lamp='$lamp' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated lamp';
    } 

    if (!is_null($baudrate)) {
        $db->query("UPDATE efactorproduct SET baudrate='$baudrate' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated baudrate';
    } 
    if (!is_null($errorcode)) {
        $db->query("UPDATE efactorproduct SET errorcode='$errorcode' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated errorcode';
    } 
    if (!is_null($comm_mode)) {
        $db->query("UPDATE efactorproduct SET comm_mode='$comm_mode' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated comm_mode';
    } 
    if (!is_null($uartmsgid)) {
        $db->query("UPDATE efactorproduct SET uartmsgid='$uartmsgid' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated uartmsgid';
    } 
    if (!is_null($time)) {
        $db->query("UPDATE efactorproduct SET time='$time' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated time';
    } 

    if (!is_null($airflow)) {
        $db->query("UPDATE efactorproduct SET airflow='$airflow' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated airflow';
    } 
   

if (!is_null($mac)) {
    $db->query("UPDATE efactorproduct SET mac='$mac' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated mac';
} 
if (!is_null($module_type)) {
    $db->query("UPDATE efactorproduct SET module_type='$module_type' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated module_type';
} 
if (!is_null($device_name)) {
        $db->query("UPDATE efactorproduct SET device_name='$device_name' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated device_name';
    } 

    if (!is_null($autoclean)) {
        $db->query("UPDATE efactorproduct SET autoclean='$autoclean' WHERE node_id='$deviceid'");
        $response['status'] = 'successfully updated autoclean';
    } 
   

if (!is_null($latitude)) {
    $db->query("UPDATE efactorproduct SET latitude='$latitude' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated latitude';
} 
if (!is_null($longitude)) {
    $db->query("UPDATE efactorproduct SET longitude='$longitude' WHERE node_id='$deviceid'");
    $response['status'] = 'successfully updated longitude';
} 
else {
        // Handle unrecognized parameters or provide an error message
        $response['status'] = 'error: unrecognized parameters';
    }
} else {
    // Handle invalid input or provide an error message
    $response['status'] = 'error: invalid input';
}

/**************************mqtt*******************************/

if($addgateway != 'NULL')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$addgateway."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;




if($payload != 'null')
{


echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);

$mqtt_json_1=json_encode($mqtt_json);
echo json_encode($mqtt_json)."\n";
$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }



}


}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$nodeid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;



if($payload != 'null')
{


echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);

$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
echo $mqtt_json_1;
$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }

}
}





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
/**************************mqtt*******************************/

if($addgateway != 'NULL')

{

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$addgateway."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;



if($payload != 'null')
{




if($payload == '2500')
{
$mqtt_json=array("command_type"=> "E_NETWORK_INFO", 
"address"=>$nodeid,
"device_id"=>$nodeid,
"payload"=>$payload);
}

else{




$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"address"=>$nodeid,
"device_id"=>$nodeid,
"payload"=>$payload);


}
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }

}
}

else
{


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$nodeid."/".$command;



if($payload != 'null')
{

//echo $topic;
$mqtt_json=array("command_type"=> "E_NETWORK_INFO", 
"address"=>$nodeid,
"device_id"=>$nodeid,
"payload"=>$payload);

$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';

//$topic=$efactor."/".$things."/".$device_id_1."/".$command;


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
            exit($mqtt->publish($topic,$mqtt_json_1));
       }

}
}




/*************************************************************/
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
/*--------------------------------user_get_nodes_params-------------------------------*/

if (isset($_GET['action']) && $_GET['action']=='user_get_nodes_params_test') {

$get_nodes='{"Fan":{"Direction":0,"led_colour":6,"output":true,"power":true,"Speed":3,"led":true}}';

echo $get_nodes;





}









if (isset($_GET['action']) && $_GET['action']=='user_get_nodes_params') {

$nodeid =$_GET['node_id'];
$deviceid=$_GET['device_id'];



$nodeid =$_GET['node_id'];
//$deviceid =$_POST['device_id'];

$header = apache_request_headers();  
foreach ($header as $key => $val) {

$req = file_get_contents( 'php://input' );
    $json_data = json_decode($req, true);



if (is_array($json_data) && isset($json_data['page_name'])) {
    $pagename = $json_data['page_name'];
   
}


if (is_array($json_data) && isset($json_data['payload'])) {
    $payload = $json_data['payload'];
    
}

/*
if (is_array($json_data) && isset($json_data['longitude'])) {
    $longitude = $json_data['longitude'];
    
}

*/





$query = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$doctors = $db->get_results($query); 
//$num_dev = $db->num_rows($query);
$row = $db->get_row($query); 
$device_type=$row['device_type'];
$addgateway=$row['add_gateway'];




/********************************/


if($addgateway != 'NULL')

{


echo 'hi1';

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$addgateway."/".$command;




//echo $topic;
$mqtt_json=array("command_type"=> "GET_DEVICE_STATUS", 
"device_id"=>$deviceid,
"address"=>$nodeid,
"payload"=>$payload);
$mqtt_json_1=json_encode($mqtt_json);
//echo json_encode($mqtt_json)."\n";
$response['status']='success';

$efactor='Efactor';
$things='things';
$command='command';


     if ($mqtt->connect(true, NULL, 'efactorpw', '123456')) {
           $mqtt->publish($topic, $mqtt_json_1);

//sleep(1);


$query_result = "SELECT * FROM efactorproduct where node_id='$deviceid'";  
$result = $db->get_results($query_result); 
//$num_dev = $db->num_rows($query_result);
$row_result = $db->get_row($query); 
$payload=$row_result['payload'];
//$addgateway=$row['add_gateway'];



  $response['payload']=$payload;




       }






}

else
{


//echo 'hi2';


$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$nodeid';
$command='command';

$topic=$efactor."/".$things."/".$deviceid."/".$command;

//$topic=$efactor."/".$things."/".FFFFFFFFFFFFFFFF8200000000001700."/".$command;




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
















/******************************/


}

}









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


$connectivity = array("connected" => $net_conn, "timestamp" => $timestamp_1);
$connectivity_1 = $connectivity; 
$status_1 = json_encode(["connectivity" => $connectivity]); 
//$user_node_status = json_encode(["status" => $status_1]);
//exit($user_node_status);

echo $status_1;


}
else 
{

        header('HTTP/1.1 400');
         $response['status']='failure';
         $response['error_code']='103008';
         $response['description']='user node node not exist';
  

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
     $addgateway=$row['add_gateway'];

if($num>0)
       { 


if($moduletype == 'Gateway' || $moduletype == 'WIFI')

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

$mqtt = new Bluerhinos\phpMQTT('efactor.tech', '1883', 'phpMQTT-publisher');

$efactor='Efactor';
$things='things';
//$thingid='$add_gateway';
$command='command';

$topic=$efactor."/".$things."/".$nodeid."/".$command;


/*echo $topic;
$mqtt_json=array("command_type"=> "SET_DEVICE_STATUS", 
"address"=>$nodeid,
"device_id"=>$nodeid,
"payload"=>'F0F1');
$mqtt_json_1=json_encode($mqtt_json);
echo json_encode($mqtt_json)."\n";*/



if($moduletype == 'Gateway')
{
//echo $topic;
$mqtt_json=array("command_type"=> "SET_DEVICE_STATUS", 
"address"=>$nodeid,
"device_id"=>$nodeid,
"payload"=>'F0F1');
$mqtt_json_1=json_encode($mqtt_json);
echo json_encode($mqtt_json)."\n";
}

else if($moduletype == 'WIFI')
{
//echo $topic;
$mqtt_json=array("command_type"=> "SET_DEVICE_STATUS", 
"address"=>$nodeid,
"device_id"=>$nodeid,
"payload"=>'F0');
$mqtt_json_1=json_encode($mqtt_json);
echo json_encode($mqtt_json)."\n";
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

   if (is_array($json_data) && isset($json_data['secret_key']) && isset($json_data['node_id'])) {
         $secret_key = $json_data['secret_key'];
         $nodeid = $json_data['node_id'];
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



     if($num>0)
       { 

      
         $row = $db->get_row($query);
        // $secretkey=$row['secret_key']; 
        


           if(empty($secret_key))          //if($secretkey != 'NULL')   6-2-23
            {
                   
                    $same_requestid = $row['request_id'];
                    $response = ['request_id' => $same_requestid];
                     exit(json_encode($response));           
               
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
       $response['request_id']=$requestid;

    $response['request_id'] = $requestid;
    exit(json_encode($response)); 

          
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


/**************************add_node_req_status************************************/

if (isset($_GET['action']) && $_GET['action']=='add_node_req_status') 
{

$query = "SELECT * FROM efactorproduct where request_id='".$_GET['request_id']."'";
$num = $db->num_rows($query);

 if($num>0)
  {    
    
    $response['request_status']='confirmed';
     $db->query("update efactorproduct set req_status='confirmed' where node_id='".$_GET['node_id']."'");

  }
else
   {
     header('HTTP/1.1 400');
     $response['status']='failure';
     $response['error_code']='100011'; 
     $response['description']='Node not authorized';
}

}

/**************************add_node_req_status************************************/

if (isset($_GET['action']) && $_GET['action']=='add_node_req_status') 
{

$query = "SELECT * FROM efactorproduct where request_id='".$_GET['request_id']."'";
$num = $db->num_rows($query);

 if($num>0)
  {    
    
    $response['request_status']='confirmed';
     $db->query("update efactorproduct set req_status='confirmed' where node_id='".$_GET['node_id']."'");

  }
else
   {
     header('HTTP/1.1 400');
     $response['status']='failure';
     $response['error_code']='100011'; 
     $response['description']='Node not authorized';
}

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

$query = "SELECT * FROM efactorproduct where node_id='".$_GET['node_id']."'";
$num = $db->num_rows($query); 
$row = $db->get_row($query); 
$module_type=$row['module_type'];


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

//$timestamp=round(microtime(true) * 1000);
$dt=date("Y-m-d H:i:s");

$segment9 = substr($nodeid, 16, 2);
$segment13 = substr($nodeid, 24, 2);
$segment14 = substr($nodeid, 26, 2);
$segment15 = substr($nodeid, 28, 2);

$underscore='_';
    

$deviceType = $devicetype.$underscore. $segment9 . $segment13 . $segment14 . $segment15;


     

       $db->query("update efactorproduct set req_status='confirmed' where node_id='".$_GET['node_id']."'");
       $db->query("update efactorproduct set access_token='$val' where node_id='".$_GET['node_id']."'");
        $db->query("update efactorproduct set user_id='$userid' where node_id='".$_GET['node_id']."'");
       $db->query("update efactorproduct set mob_device_token='$mobiledevicetoken' where node_id='".$_GET['node_id']."'");
       //$db->query("update efactorproduct set name='$devicename' where node_id='".$_GET['node_id']."'");
       $db->query("update efactorproduct set time='$dt' where node_id='".$_GET['node_id']."'");
       $db->query("update efactorproduct set current_devicetype='$deviceType' where node_id='".$_GET['node_id']."'");
       $db->query("update efactorproduct set phone='$phone' where node_id='".$_GET['node_id']."'");
       $db->query("update efactorproduct set email='$email' where node_id='".$_GET['node_id']."'");
      
       $resp='{"status": "success"}';
       exit($resp);

  }
else 

{
    $response['error']='access_token not valid';


}



}

}
/*******************************************************************/

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
 if($latitude !== 'NULL' && $longitude !== 'NULL')
     {
          $db->query("update efactorproduct set signin_latitude='$latitude' where access_token='$val'");
          $db->query("update efactorproduct set signin_longitude='$longitude' where access_token='$val'");
          $db->query("update efactorproduct set fw_version='$app_version' where access_token='$val'");
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
