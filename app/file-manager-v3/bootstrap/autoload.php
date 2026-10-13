<?php
// bootstrap/autoload.php

// Define root paths
define('ROOT_PATH', realpath(dirname(__DIR__)));
define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'app');
define('PUBLIC_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'public');
define('CONFIG_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'app/config');
define('UPLOAD_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'uploads');
define('VIEWS_PATH', ROOT_PATH . '/app/Views');
define('ASSETS_PATH', '/assets');



// Load configuration
$configFile = CONFIG_PATH . DIRECTORY_SEPARATOR . 'Application.php';
if (!file_exists($configFile)) {
    $configFile = CONFIG_PATH . DIRECTORY_SEPARATOR . 'Application.php';
}
if (!file_exists($configFile)) {
    die("Config file not found");
}
$config = require_once $configFile;

// Set timezone
date_default_timezone_set($config['timezone'] ?? 'Asia/Kolkata');

// ✅ ENSURE SESSION IS STARTED
if (session_status() === PHP_SESSION_NONE) {
    // Set session cookie parameters
    session_set_cookie_params([
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

if (!defined('ALLOWED_EXTENSIONS')) {
    define('ALLOWED_EXTENSIONS', $config['allowed_extensions'] ?? []);
}
if (!defined('MAX_FILE_SIZE')) {
    define('MAX_FILE_SIZE', $config['max_file_size'] ?? 10485760);
}

// Load database config
$dbConfigFile = CONFIG_PATH . DIRECTORY_SEPARATOR . 'database.php';
if (!file_exists($dbConfigFile)) {
    $dbConfigFile = CONFIG_PATH . DIRECTORY_SEPARATOR . 'Database.php';
}
if (!file_exists($dbConfigFile)) {
    die("Database config file not found");
}
$dbConfig = require_once $dbConfigFile;

try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}",
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['options']
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$GLOBALS['pdo'] = $pdo;

// Autoloader
spl_autoload_register(function ($className) {
    $prefix = 'App\\';
    $baseDir = APP_PATH . DIRECTORY_SEPARATOR;
    
    $len = strlen($prefix);
    if (strncmp($prefix, $className, $len) !== 0) {
        return;
    }
    
    $relativeClass = substr($className, $len);
    $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

