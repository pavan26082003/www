<?php
// file-manager/bootstrap.php - Frontend bootstrap

session_start();

// Define paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEWS_PATH', ROOT_PATH . '/app/Views');

// Load helpers
require_once APP_PATH . '/Helpers/helpers.php';

// Simple autoloader for frontend controllers
spl_autoload_register(function ($className) {
    $prefix = 'App\\';
    $baseDir = APP_PATH . '/Controllers/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $className, $len) !== 0) {
        return;
    }
    
    $relativeClass = substr($className, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Also load Services
spl_autoload_register(function ($className) {
    $prefix = 'App\\Services\\';
    $baseDir = APP_PATH . '/Services/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $className, $len) !== 0) {
        return;
    }
    
    $relativeClass = substr($className, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// API Base URL
define('API_BASE', '/api/v1');