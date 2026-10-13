<?php
// public/logout.php - Simple logout redirect

require_once __DIR__ . '/../bootstrap/autoload.php';

// Clear session
$_SESSION = [];
session_destroy();

// Redirect to login
header('Location: /app/file-manager-v3/login');
exit;