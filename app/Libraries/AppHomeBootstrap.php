<?php

declare(strict_types=1);

namespace App\Libraries;

/**
 * Minimal CodeIgniter bootstrap for standalone scripts (e.g. v8/api_creation.php).
 */
class AppHomeBootstrap
{
    public static function appHomePageModel(): ?\App\Models\AppHomePageModel
    {
        try {
            self::bootIfNeeded();
            return new \App\Models\AppHomePageModel();
        } catch (\Throwable $e) {
            log_message('error', 'AppHomeBootstrap: ' . $e->getMessage());
            return null;
        }
    }

    public static function bootIfNeeded(): void
    {
        if (defined('APP_HOME_CI_BOOTED')) {
            return;
        }

        if (defined('APPPATH') && function_exists('db_connect')) {
            define('APP_HOME_CI_BOOTED', true);
            return;
        }

        // Avoid Config\App fatals when HTTP_HOST is absent (CLI / some API front-controllers).
        if (empty($_SERVER['HTTP_HOST'])) {
            $_SERVER['HTTP_HOST'] = !empty($_SERVER['SERVER_NAME'])
                ? (string) $_SERVER['SERVER_NAME']
                : 'localhost';
        }
        if (empty($_SERVER['SCRIPT_NAME'])) {
            $_SERVER['SCRIPT_NAME'] = '/index.php';
        }
        if (!isset($_SERVER['REQUEST_URI'])) {
            $_SERVER['REQUEST_URI'] = '/';
        }

        $roots = [];
        $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
        if ($docRoot !== '') {
            $roots[] = $docRoot;
        }
        $roots[] = dirname(__DIR__, 2);

        foreach (array_unique($roots) as $root) {
            $pathsFile = rtrim(str_replace('\\', '/', $root), '/') . '/app/Config/Paths.php';
            if (!is_file($pathsFile)) {
                continue;
            }

            if (!defined('FCPATH')) {
                define('FCPATH', rtrim($root, '/\\') . DIRECTORY_SEPARATOR);
            }

            require_once $pathsFile;
            $paths = new \Config\Paths();
            $bootstrap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
            if (!is_file($bootstrap)) {
                continue;
            }

            require_once $bootstrap;
            define('APP_HOME_CI_BOOTED', true);
            return;
        }

        throw new \RuntimeException('Unable to locate CodeIgniter bootstrap paths.');
    }
}
