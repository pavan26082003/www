<?php

namespace App\Repositories\NAS;

use PDO;

abstract class Repository
{
    protected PDO $db;

    public function __construct(?PDO $pdo = null)
    {
        /*
         * 1. Explicit PDO connection
         */
        if ($pdo !== null) {
            $this->db = $pdo;
            return;
        }

        /*
         * 2. Existing global PDO connection
         */
        if (
            isset($GLOBALS['pdo']) &&
            $GLOBALS['pdo'] instanceof PDO
        ) {
            $this->db = $GLOBALS['pdo'];
            return;
        }

        /*
         * 3. NAS database connection
         *
         * NAS is integrated into the existing application,
         * but it uses its own database:
         *
         * app/Config/NAS/database.php
         */
        $dbConfigFile = dirname(__DIR__, 2)
            . DIRECTORY_SEPARATOR . 'Config'
            . DIRECTORY_SEPARATOR . 'NAS'
            . DIRECTORY_SEPARATOR . 'database.php';

        if (!file_exists($dbConfigFile)) {
            throw new \RuntimeException(
                'NAS database configuration not found: ' . $dbConfigFile
            );
        }

        $dbConfig = require $dbConfigFile;

        $this->db = new PDO(
            "mysql:host={$dbConfig['host']};" .
            "dbname={$dbConfig['database']};" .
            "charset={$dbConfig['charset']}",
            $dbConfig['username'],
            $dbConfig['password'],
            $dbConfig['options']
        );
    }

    public function getPdo(): PDO
    {
        return $this->db;
    }
}