<?php
// public/login.php - Login Page Entry Point

require_once __DIR__ . '/../bootstrap/autoload.php';

use App\Controllers\Web\AuthController;

$authController = new AuthController();
$authController->showLogin();