<?php
// Valid PHP Version?
$minPHPVersion = '7.3';
if (version_compare(PHP_VERSION, $minPHPVersion, '<')) {
    die("Your PHP version must be {$minPHPVersion} or higher to run CodeIgniter. Current version: " . PHP_VERSION);
}
unset($minPHPVersion);

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

/*
 * ---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 * ---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// Ensure the current directory is pointing to the front controller's directory
chdir(__DIR__);

// Load our paths config file
// This is the line that might need to be changed, depending on your folder structure.
// Provide a clearer error message if the file cannot be found or read (helps local debugging).
$pathsCandidate = FCPATH . 'app/Config/Paths.php';
$pathsFile = realpath($pathsCandidate) ?: $pathsCandidate;
if (!is_file($pathsFile) || !is_readable($pathsFile)) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "Required file not found or not readable: {$pathsFile}\n";
    echo "Please ensure the `app/Config/Paths.php` file exists and is readable by the web server.\n";
    error_log("[bootstrap] Paths file missing or unreadable: {$pathsFile}");
    exit(1);
}
require $pathsFile;
// ^^^ Change this if you move your application folder

$paths = new Config\Paths();

// Location of the framework bootstrap file.
$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
$app = require realpath($bootstrap) ?: $bootstrap;

//set the variable to 'installed' after installation
$app_state = "installed";

// we don't want to access the main project before installation. redirect to installation page
if ($app_state === 'pre_installation') {
    $domain = $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

    $domain = preg_replace('/index.php.*/', '', $domain); //remove everything after index.php
    if (!empty($_SERVER['HTTPS'])) {
        $domain = 'https://' . $domain;
    } else {
        $domain = 'http://' . $domain;
    }

    header("Location: $domain./install/index.php");
  // header("Location: https://alexa.amazon.co.jp/api/skill/link/M33GEKSHKE2FYM");


    exit;
}

/*
 * ---------------------------------------------------------------
 * LAUNCH THE APPLICATION
 * ---------------------------------------------------------------
 * Now that everything is setup, it's time to actually fire
 * up the engines and make this app do its thang.
 */
$app->run();
