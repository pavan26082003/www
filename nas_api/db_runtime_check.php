<?php

declare(strict_types=1);

require_once __DIR__ . '/Config/config.php';
require_once __DIR__ . '/Core/ApiException.php';
require_once __DIR__ . '/Core/Database.php';

use NasApi\Core\Database;

header('Content-Type: application/json');

try {

    $config = $GLOBALS['NAS_API_CONFIG']['efactor_db'];

    $pdo = Database::connect($config);

    $database = $pdo->query(
        'SELECT DATABASE()'
    )->fetchColumn();

    $server = $pdo->query(
        'SELECT @@hostname'
    )->fetchColumn();

    $stmt = $pdo->prepare(
        'SELECT
            id,
            email,
            phone,
            status,
            deleted,
            disable_login
         FROM users
         WHERE id IN (177, 704)
         ORDER BY id'
    );

    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,

        'nas_api_environment' =>
            defined('NAS_API_ENV')
                ? NAS_API_ENV
                : null,

        'configured_host' =>
            $config['host'],

        'configured_port' =>
            $config['port'],

        'configured_database' =>
            $config['name'],

        'actual_database' =>
            $database,

        'mysql_server' =>
            $server,

        'users' =>
            $users

    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}