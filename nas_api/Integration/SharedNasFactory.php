<?php

declare(strict_types=1);

namespace NasApi\Integration;

use App\Repositories\NAS\ClusterRepository;
use App\Repositories\NAS\FileRepository;
use App\Repositories\NAS\FolderRepository;
use App\Repositories\NAS\ShareRepository;
use App\Repositories\NAS\StarredRepository;
use App\Repositories\NAS\SyncRepository;
use App\Repositories\NAS\UserRepository;
use App\Services\NAS\ClientSyncService;
use App\Services\NAS\ClusterService;
use App\Services\NAS\DashboardService;
use App\Services\NAS\FileService;
use App\Services\NAS\FolderService;
use App\Services\NAS\OfficeConversionService;
use App\Services\NAS\PermissionService;
use App\Services\NAS\ShareService;
use App\Services\NAS\StorageService;
use App\Services\NAS\SyncService;
use NasApi\Bootstrap\SharedNasBootstrap;

/**
 * Composition only. No NAS business logic lives here.
 *
 * nas_api owns HTTP/Bearer transport. Existing App\Services\NAS and
 * App\Repositories\NAS remain the single source of truth for NAS behavior.
 */
final class SharedNasFactory
{
    public static function userRepository(): UserRepository
    {
        SharedNasBootstrap::boot();
        return new UserRepository();
    }

    public static function fileRepository(): FileRepository
    {
        SharedNasBootstrap::boot();
        return new FileRepository();
    }

    public static function storageService(): StorageService
    {
        SharedNasBootstrap::boot();
        return new StorageService();
    }

    public static function dashboardService(): DashboardService
    {
        SharedNasBootstrap::boot();

        return new DashboardService(
            new UserRepository(),
            new FileRepository(),
            new FolderRepository(),
            new ShareRepository()
        );
    }


    public static function clusterService(): ClusterService
    {
        SharedNasBootstrap::boot();
        return new ClusterService(new ClusterRepository());
    }

    public static function syncRepository(): SyncRepository
    {
        SharedNasBootstrap::boot();
        return new SyncRepository();
    }

    public static function clientSyncService(): ClientSyncService
    {
        SharedNasBootstrap::boot();

        return new ClientSyncService(
            new SyncRepository(),
            new FileRepository(),
            new FolderRepository(),
            new UserRepository(),
            new StorageService()
        );
    }

    /**
     * Legacy compatibility only. Do not use for normal mobile/client sync.
     * The older SyncService contains server-local sync behavior; the explicit
     * repair endpoint is the only Phase-8 caller.
     */
    public static function legacySyncService(): SyncService
    {
        SharedNasBootstrap::boot();

        $fileRepository = new FileRepository();
        $folderRepository = new FolderRepository();
        $shareRepository = new ShareRepository();
        $permissionService = new PermissionService(
            $fileRepository,
            $folderRepository,
            $shareRepository
        );

        return new SyncService(
            new SyncRepository(),
            $fileRepository,
            $folderRepository,
            new UserRepository(),
            new StorageService(),
            $permissionService
        );
    }

    public static function fileService(): FileService
    {
        SharedNasBootstrap::boot();
        $deps = self::fileFolderDependencies();

        return new FileService(
            $deps['fileRepository'],
            $deps['folderRepository'],
            $deps['clusterRepository'],
            $deps['shareRepository'],
            $deps['starredRepository'],
            $deps['userRepository'],
            $deps['syncRepository'],
            $deps['storageService'],
            $deps['permissionService']
        );
    }

    public static function officeConversionService(): OfficeConversionService
    {
        SharedNasBootstrap::boot();

        return new OfficeConversionService(
            new StorageService(),
            self::fileService()
        );
    }

    public static function permissionService(): PermissionService
    {
        SharedNasBootstrap::boot();

        return new PermissionService(
            new FileRepository(),
            new FolderRepository(),
            new ShareRepository()
        );
    }

    public static function shareService(): ShareService
    {
        SharedNasBootstrap::boot();

        $fileRepository = new FileRepository();
        $folderRepository = new FolderRepository();
        $shareRepository = new ShareRepository();
        $permissionService = new PermissionService(
            $fileRepository,
            $folderRepository,
            $shareRepository
        );

        return new ShareService(
            $shareRepository,
            new UserRepository(),
            $fileRepository,
            $folderRepository,
            $permissionService
        );
    }

    public static function folderService(): FolderService
    {
        SharedNasBootstrap::boot();
        $deps = self::fileFolderDependencies();

        return new FolderService(
            $deps['folderRepository'],
            $deps['fileRepository'],
            $deps['clusterRepository'],
            $deps['shareRepository'],
            $deps['starredRepository'],
            $deps['userRepository'],
            $deps['storageService'],
            $deps['permissionService']
        );
    }

    /**
     * @return array{
     *   userRepository:UserRepository,
     *   fileRepository:FileRepository,
     *   folderRepository:FolderRepository,
     *   clusterRepository:ClusterRepository,
     *   shareRepository:ShareRepository,
     *   starredRepository:StarredRepository,
     *   syncRepository:SyncRepository,
     *   storageService:StorageService,
     *   permissionService:PermissionService
     * }
     */
    private static function fileFolderDependencies(): array
    {
        $userRepository = new UserRepository();
        $fileRepository = new FileRepository();
        $folderRepository = new FolderRepository();
        $clusterRepository = new ClusterRepository();
        $shareRepository = new ShareRepository();
        $starredRepository = new StarredRepository();
        $syncRepository = new SyncRepository();
        $storageService = new StorageService();
        $permissionService = new PermissionService(
            $fileRepository,
            $folderRepository,
            $shareRepository
        );

        return compact(
            'userRepository',
            'fileRepository',
            'folderRepository',
            'clusterRepository',
            'shareRepository',
            'starredRepository',
            'syncRepository',
            'storageService',
            'permissionService'
        );
    }
}
