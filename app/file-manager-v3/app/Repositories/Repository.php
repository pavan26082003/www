<?php
namespace App\Repositories;

use PDO;

abstract class Repository
{
    protected PDO $db;

    public function __construct(?PDO $pdo = null)
    {
        if ($pdo !== null) {
            $this->db = $pdo;
        } elseif (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) {
            $this->db = $GLOBALS['pdo'];
        } else {
            // Fallback to config
            $dbConfig = require_once __DIR__ . '/../../app/config/database.php';
            $this->db = new PDO(
                "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}",
                $dbConfig['username'],
                $dbConfig['password'],
                $dbConfig['options']
            );
        }
    }

    // Make this public so services can access it
    public function getPdo(): PDO
    {
        return $this->db;
    }
}