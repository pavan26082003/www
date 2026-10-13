<?php
// public/home.php - Home Entry Point

require_once __DIR__ . '/../bootstrap/autoload.php';

use App\Controllers\Web\HomeController;

$homeController = new HomeController();
$homeController->showHome();