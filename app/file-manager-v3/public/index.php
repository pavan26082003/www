<?php
// public/index.php - API Entry Point

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
ini_set('log_errors', 1);

// Create logs directory if it doesn't exist
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
ini_set('error_log', $logDir . '/php_errors.log');

// ✅ Add exception handler to catch ALL errors and return JSON
set_exception_handler(function($e) {
    
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    exit;
});

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => "$errstr in $errfile:$errline"
    ]);
    exit;
});

// ✅ Load bootstrap
require_once __DIR__ . '/../bootstrap/autoload.php';

use App\Routes\Api;

// Register routes
Api::register();

// Get request method and URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];


// Dispatch the request
Api::dispatch($method, $uri);