<?php

/*-----------------------------------------------------------*/

echo 'hi';

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

// Collect log data
$logData = [
    'timestamp'   => date('Y-m-d H:i:s'),
    'ip_address'  => $_SERVER['REMOTE_ADDR'],
    'method'      => $_SERVER['REQUEST_METHOD'],
    'uri'         => $_SERVER['REQUEST_URI'],
    'headers'     => get_all_headers(),
    'body'        => file_get_contents('php://input')
];

// Format log as JSON
$logEntry = json_encode($logData, JSON_PRETTY_PRINT);

// Append to log file
file_put_contents($logFile, $logEntry . "\n\n", FILE_APPEND);




/*----------------------------------------------------------*/
?>
