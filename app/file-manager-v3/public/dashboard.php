<?php
// public/dashboard.php - Dashboard Entry Point

require_once __DIR__ . '/../bootstrap/autoload.php';

use App\Controllers\Web\AuthController;

$authController = new AuthController();
$authController->showDashboard();