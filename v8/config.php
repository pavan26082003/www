<?php
//define("SITE_URL","https://efactor.myoffice-365.com/");
define("SITE_URL","https://efactor.tech/");
//define("BASE_PATH","/var/www/html/demo/api");
define("BASE_PATH","/var/www/html");
define("SITE_NAME","Factor");
define("DB_HOST","localhost" );
define("DB_USER","root" );
define("DB_PASS","mysql" );
define("DB_NAME","factor_db2" );
define("SEND_ERRORS_TO","factorenovations23@gmail.com" );
define("DISPLAY_DEBUG", false );
error_reporting(0);
//ini_set('display_errors', 1);
date_default_timezone_set("Asia/Kolkata");
//Database
require_once('class.db.php');
$db = new DB();

$server = 'efactor.tech';     // change if necessary
$port = 1883;                     
$username = 'efactorpw';       // set your username
$password = '123456';         // set your password
$client_id = 'phpMQTT-publisher'; 
?>
