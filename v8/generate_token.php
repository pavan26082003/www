<?php


$secret_key = "1a2b3c4d5e6f7g8h9i0j1k2l3m4n5o6p7q8r9s0t1u2v3w4x5y6z7a8b9c0d";
$file_path = "https://efactor.tech/androidfiles/v8/efactorprotov2-1.16.aar";
//$expires = time() + 3600;   //for 1 hour
$expires = time() + (10 * 86400);    //for 10 days
//$expires = time() + (1 * 86400)      //for 1 days


$file_name="efactorprotov2-1.16.aar";


$token = md5($secret_key . $file_path . $expires);
//$download_url = "https://efactor.tech/v8/download.php?file=" . urlencode(basename($file_path)) . "&token=$token&expires=$expires&passkey=";

$download_url = "https://efactor.tech/v8/download.php/$token/$expires/$file_name";



echo "Download URL: " . $download_url;
?>