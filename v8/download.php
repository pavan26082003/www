<?php


$secret_key = "1a2b3c4d5e6f7g8h9i0j1k2l3m4n5o6p7q8r9s0t1u2v3w4x5y6z7a8b9c0d";


$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', trim($path, '/'));

$file_name = urldecode($parts[4]); // 'efactorprotov2-1.3.aar'
$token = $parts[2]; // '1a2b3c4d5e6f7g8h9i0j1k2l3m4n5o6p7q8r9s0t1u2v3w4x5y6z7a8b9c0d'
$expires = $parts[3]; // '1697049600'


$file_path = "https://efactor.tech/androidfiles/v8/" . basename($file_name);

// Validate the token and expiration
$expected_token = md5($secret_key . "https://efactor.tech/androidfiles/v8/" . $file_name . $expires);

if (empty($token) || empty($expires) || empty($file_name)) {
    http_response_code(400);
    die("Invalid request.");
}

if (time() > $expires) {
    http_response_code(403);
    die("Link has expired.");
}

if ($token === $expected_token && time() < $expires) {
    // Fetch and stream the remote file
    $remote_file_url = $file_path;
    $file_name = basename($file_path);

    // Set headers for download
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $file_name . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    // Use cURL to fetch and output the remote file
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remote_file_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // Stream directly to output
    curl_setopt($ch, CURLOPT_HEADER, false); // Exclude headers from output
    curl_exec($ch);

    if (curl_errno($ch)) {
        http_response_code(500);
        die("Failed to fetch the file.");
    }

    curl_close($ch);
    exit;
} else {
    http_response_code(403);
    die("Access denied.");
}


?>