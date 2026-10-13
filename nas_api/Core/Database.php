<?php

declare(strict_types=1);

namespace NasApi\Core;

use PDO;
use PDOException;

final class Database
{
    public static function connect(array $config): PDO
    {
        foreach (['host', 'port', 'name', 'user', 'charset'] as $required) {
            if (!array_key_exists($required, $config)) {
                throw new ApiException(
                    'Database configuration is incomplete.',
                    500,
                    'db_config_error'
                );
            }
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            (int) $config['port'],
            $config['name'],
            $config['charset']
        );

        try {
            return new PDO(
                $dsn,
                (string) $config['user'],
                (string) ($config['pass'] ?? ''),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new ApiException(
                NAS_API_DEBUG
                    ? 'Database connection failed: ' . $e->getMessage()
                    : 'Database connection failed.',
                500,
                'db_connection_failed'
            );
        }
    }
}
