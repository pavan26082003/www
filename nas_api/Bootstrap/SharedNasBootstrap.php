<?php

declare(strict_types=1);

namespace NasApi\Bootstrap;

use NasApi\Core\ApiException;

/**
 * Makes the existing Efactor/NAS App\ classes available to the standalone
 * nas_api process without running the CodeIgniter router or changing the web
 * application's session flow.
 *
 * Expected default layout:
 *   <project-root>/app/
 *   <project-root>/nas_api/
 *
 * Override the project root with EFACTOR_PROJECT_ROOT if nas_api is deployed
 * elsewhere.
 */
final class SharedNasBootstrap
{
    private static bool $booted = false;
    private static ?string $projectRoot = null;

    public static function boot(): void
    {
        if (self::$booted) {
            return;
        }

        $config = $GLOBALS['NAS_API_CONFIG']['shared_app'] ?? [];
        $configuredRoot = trim((string) ($config['project_root'] ?? ''));
        $fallbackRoot = dirname(__DIR__, 2);
        $rootCandidate = $configuredRoot !== '' ? $configuredRoot : $fallbackRoot;

        $realRoot = realpath($rootCandidate);
        $projectRoot = $realRoot !== false ? $realRoot : rtrim($rootCandidate, "\\/");
        $appPath = $projectRoot . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR;

        if (!is_dir($appPath)) {
            throw new ApiException(
                'Shared Efactor app directory was not found. Expected: ' . $appPath,
                500,
                'shared_app_not_found'
            );
        }

        self::$projectRoot = $projectRoot;

        // The existing NAS Repository layer uses APPPATH to load
        // app/Config/NAS/database.php. Define only when the host process has not
        // already booted CodeIgniter.
        if (!defined('ROOTPATH')) {
            define('ROOTPATH', $projectRoot . DIRECTORY_SEPARATOR);
        }

        if (!defined('APPPATH')) {
            define('APPPATH', $appPath);
        }

        if (!defined('WRITEPATH')) {
            define('WRITEPATH', $projectRoot . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR);
        }

        if (!defined('FCPATH')) {
            define('FCPATH', $projectRoot . DIRECTORY_SEPARATOR);
        }

        $constantsFile = $appPath . 'Config' . DIRECTORY_SEPARATOR . 'Constants.php';
        if (is_file($constantsFile)) {
            require_once $constantsFile;
        }

        // Reuse the real application classes. This is deliberately a lightweight
        // class-loader bridge: it does not execute app/Config/Routes.php and does
        // not start or alter the CodeIgniter web session.
        spl_autoload_register(static function (string $class) use ($appPath): void {
            $prefix = 'App\\';
            if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
                return;
            }

            $relative = substr($class, strlen($prefix));
            $file = $appPath . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

            if (is_file($file)) {
                require_once $file;
            }
        }, true, true);

        // Some existing application configuration classes use the normal
        // CodeIgniter Config\ namespace. Register it too, without booting routes.
        spl_autoload_register(static function (string $class) use ($appPath): void {
            $prefix = 'Config\\';
            if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
                return;
            }

            $relative = substr($class, strlen($prefix));
            $file = $appPath . 'Config' . DIRECTORY_SEPARATOR
                . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

            if (is_file($file)) {
                require_once $file;
            }
        }, true, false);

        self::$booted = true;
    }

    public static function projectRoot(): string
    {
        self::boot();
        return (string) self::$projectRoot;
    }

    /**
     * Validate shared Phase-2/Phase-3 dependencies without touching web routes.
     *
     * @return array<string,bool>
     */
    public static function sharedDependencyStatus(): array
    {
        self::boot();

        $classes = [
            'App\Services\NAS\DashboardService',
            'App\Repositories\NAS\Repository',
            'App\Repositories\NAS\UserRepository',
            'App\Repositories\NAS\FileRepository',
            'App\Repositories\NAS\FolderRepository',
            'App\Repositories\NAS\ShareRepository',
            'App\Helpers\NAS\FileHelper',
            'App\Services\NAS\FileService',
            'App\Services\NAS\FolderService',
            'App\Services\NAS\ShareService',
            'App\Services\NAS\PermissionService',
            'App\Services\NAS\StorageService',
            'App\Services\NAS\OfficeConversionService',
            'App\Services\NAS\ClusterService',
            'App\Services\NAS\ClientSyncService',
            'App\Services\NAS\SyncService',
            'App\Repositories\NAS\ClusterRepository',
            'App\Repositories\NAS\StarredRepository',
            'App\Repositories\NAS\SyncRepository',
        ];

        $status = [];
        foreach ($classes as $class) {
            $status[$class] = class_exists($class);
        }

        return $status;
    }
}
