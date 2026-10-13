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

/*include('alexa_control.php');
include('alexa_endpoint.php');
include('alexa_response.php');
include('alexa_report.php');
include('alexa_const_errors.php');*/
/*-----------------------------------------------------*/
$req = file_get_contents ( 'php://input' );
$rawdata = $HTTP_RAW_POST_DATA;
//$json_data = json_decode($req)
//echo $json_data;
header('Content-Type: application/json');
//header('Authorization: Bearer {token}');
header('Authorization: API Key');
//$not_found = false; // change it to true if you wish device not found response
//$user_check_faild = false; // change to true if you wish user credential error response

/*-----------------------MQTT------------------------------------*/



//include('jwt_utils.php');

$server = 'efactor.tech';             // change if necessary
$port = 1883;                         // change if necessary
$username = 'efactorpw';              // set your username
$password = '123456';                 // set your password
$client_id = 'phpMQTT-publisher';     // make sure this is unique for connecting to sever - you could use uniqid()



/******************************************************************/
/*-----------------------------forgotpwd--------------------------*/

if (isset($_GET['action']) && $_GET['action']=='fsm') {

// set the URL of the FCM endpoint
$url = 'https://fcm.googleapis.com/fcm/send';

// create the data payload for the request
$data = array(
    'to' => 'eBA6Rwo5SX-_4KjvEp1FVm:APA91bFJRz4uRBu4zhbvQfdyGeKrQiHIo0m3kcsYOU673_aLPR5VEvCK7u_lmL0X6z8lpDecwBhCHKVyqEEgCzRPz-K-8N1f-1ZLBtzZ8YY5aclJdu3-zBOqxnTc4U-T2YCkY3ufQQ5x',
    'data' => array(
        'title' => 'Efactor SmartThings',
        'body'  => 'Node(s) connected to cloud',
        'event_data_payload' => '{"event_version":"v1",
                                        "id":"1",
                                  "event_type":"efactor.event.node_connected",
                                  "timestamp":1648563678737,
                                 "description":"Node(s) connected to cloud",
                               "event_data":{"user_id":"163","node_id":"FFFFFFFFFFFFFFFF8000000000000400","connectivity":{"connected":true,"timestamp":1648563678517}}}'
    ));

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


/*************************************************************/
/*******************password_verify***************************/



if (isset($_GET['action']) && $_GET['action']=='resetpassword') {

 if($_POST['phone']!='' && $_POST['verification_code']!='')
   {
$query = "SELECT * FROM `otps` where phone='".$_POST['phone']."' and otp='".$_POST['verification_code']."'";
$doctors = $db->get_results($query); 
$row = $db->get_row($query); 
$password=$_POST['password'];
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


$db->query("update users set password='$password' where phone='".$_POST['phone']."'");


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



/****************************************************************************/
/****************************************************************************/
/****************************************************************************/


//Output
echo json_encode($response);
//echo json_data;
exit;



?>
