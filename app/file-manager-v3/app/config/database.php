<?php
// config/Database.php
return [
    'host' => 'localhost',
    'database' => 'file_manager_v4',
    'username' => 'root',
    'password' => 'mysql',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]
];