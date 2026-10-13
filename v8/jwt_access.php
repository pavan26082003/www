<?php

include('JWT.php');
//include('key.php');

//$key = 'example_key';


$header = json_encode(["kid" =>"abC123!",
                       'typ' => 'JWT', 
                       'alg' => 'HS256']);


//$payload = json_encode(['user_id' => 123]);


$payload = json_encode([
     "sub" => "1",
     "device_key"=> "",
      "aud"=> "",
      "email_verified"=> true,
      "token_use"=>"access",
      "auth_time"=>"",
      "username"=> "sudha",
      "exp"=>"",
      "iat"=>"",
      "email"=> "",
      "phone"=> "7893325350",
      "jti"=> "",
      "origin_jti"=> ""
]);



$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

//$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);

$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'Efactor', true);

$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;


echo $jwt;

//echo $base64UrlPayload;

//$response['id_token']=$jwt;



?>