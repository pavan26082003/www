<?php

declare(strict_types=1);

use NasApi\Bootstrap\SharedNasBootstrap;
use NasApi\Controllers\AuthController;
use NasApi\Controllers\BackupController;
use NasApi\Controllers\ClusterController;
use NasApi\Controllers\DashboardController;
use NasApi\Controllers\FileController;
use NasApi\Controllers\FolderController;
use NasApi\Controllers\PreviewController;
use NasApi\Controllers\ShareController;
use NasApi\Controllers\SyncController;
use NasApi\Controllers\UserController;
use NasApi\Core\JsonResponse;
use NasApi\Core\Router;

require __DIR__ . '/Config/config.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'NasApi\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $router = new Router();

    $router->get('/v1/health', static function (): void {
        JsonResponse::send([
            'success' => true,
            'service' => 'Efactor NAS Mobile API',
            'version' => 'v1',
            'phase' => 10,
        ]);
    });

    // Diagnostic only. Does not reveal local/server filesystem paths.
    $router->get('/v1/health/shared', static function (): void {
        $status = SharedNasBootstrap::sharedDependencyStatus();
        $ok = !in_array(false, $status, true);

        JsonResponse::send([
            'success' => $ok,
            'message' => $ok
                ? 'Shared NAS classes are available.'
                : 'One or more shared NAS classes are unavailable.',
            'dependencies' => $status,
        ], $ok ? 200 : 500);
    });

    // Phase 1 - Bearer authentication + Efactor -> NAS identity bridge.
    $router->get('/v1/auth/me', static function (): void {
        (new AuthController())->me();
    });

    // Phase 2 - Dashboard backed by existing App\Services\NAS.
    $router->get('/v1/dashboard', static function (): void {
        (new DashboardController())->index();
    });

    $router->get('/v1/dashboard/recent', static function (): void {
        (new DashboardController())->recent();
    });

    $router->get('/v1/dashboard/stats', static function (): void {
        (new DashboardController())->stats();
    });

    // Phase 3 - Read-only My Drive / folders. Business logic stays in app/*.
    $router->get('/v1/files', static function (): void {
        (new FileController())->index();
    });

    // Phase 10 - Complete drive views + recursive folder ZIP export.
    $router->get('/v1/files/download-folder', static function (): void {
        (new FileController())->downloadFolder();
    });


    // Phase 5 - Write operations. Transport lives here; all NAS business
    // behavior remains in the existing App\Services\NAS service layer.
    $router->post('/v1/files', static function (): void {
        (new FileController())->store();
    });

    // Explicit upload alias for Flutter/Postman clients.
    $router->post('/v1/files/upload', static function (): void {
        (new FileController())->store();
    });

    $router->get('/v1/files/{id}/info', static function (array $params): void {
        (new FileController())->info((int) ($params['id'] ?? 0));
    });


    // Phase 6 - File management. Request transport lives in nas_api; the
    // existing FileService owns rename/star/trash/restore/permanent-delete rules.
    $router->put('/v1/files/{id}/restore', static function (array $params): void {
        (new FileController())->restore((int) ($params['id'] ?? 0));
    });

    $router->put('/v1/files/{id}', static function (array $params): void {
        (new FileController())->update((int) ($params['id'] ?? 0));
    });

    $router->delete('/v1/files/{id}', static function (array $params): void {
        (new FileController())->destroy((int) ($params['id'] ?? 0));
    });


    // Phase 4 - Secure file retrieval. Existing App\Services\NAS performs
    // file permission/storage resolution; nas_api owns Bearer transport/streaming.
    $router->get('/v1/files/{id}/download', static function (array $params): void {
        (new FileController())->download((int) ($params['id'] ?? 0));
    });

    $router->get('/v1/files/{id}/preview', static function (array $params): void {
        (new FileController())->preview((int) ($params['id'] ?? 0));
    });

    $router->get('/v1/files/{id}/content', static function (array $params): void {
        (new FileController())->content((int) ($params['id'] ?? 0));
    });

    $router->get('/v1/folders', static function (): void {
        (new FolderController())->index();
    });


    $router->post('/v1/folders', static function (): void {
        (new FolderController())->store();
    });

    $router->get('/v1/folders/{id}/info', static function (array $params): void {
        (new FolderController())->info((int) ($params['id'] ?? 0));
    });


    // Phase 6 - Folder management via the existing FolderService.
    $router->put('/v1/folders/{id}/restore', static function (array $params): void {
        (new FolderController())->restore((int) ($params['id'] ?? 0));
    });

    $router->put('/v1/folders/{id}', static function (array $params): void {
        (new FolderController())->update((int) ($params['id'] ?? 0));
    });

    $router->delete('/v1/folders/{id}', static function (array $params): void {
        (new FolderController())->destroy((int) ($params['id'] ?? 0));
    });



    // Phase 7 - Sharing. nas_api owns Bearer/request transport while the
    // existing ShareService + PermissionService remain authoritative.
    $router->get('/v1/users/search', static function (): void {
        (new UserController())->search();
    });

    $router->get('/v1/share', static function (): void {
        (new ShareController())->index();
    });

    $router->post('/v1/share', static function (): void {
        (new ShareController())->store();
    });

    $router->put('/v1/share/{id}', static function (array $params): void {
        (new ShareController())->update((int) ($params['id'] ?? 0));
    });

    $router->delete('/v1/share/{id}', static function (array $params): void {
        (new ShareController())->destroy((int) ($params['id'] ?? 0));
    });



    // Mobile Backup Phase 2 - device registration/discovery only.
    // No device filesystem paths or backup files are written in this step.
    $router->post('/v1/backup/devices/register', static function (): void {
        (new BackupController())->registerDevice();
    });

    $router->get('/v1/backup/devices', static function (): void {
        (new BackupController())->devices();
    });

    // Mobile Backup Phase 2 - initialize a backup run/session only.
    // No NAS files/folders or physical backup files are created here.
    $router->post('/v1/backup/init', static function (): void {
        (new BackupController())->init();
    });

    // Mobile Backup Phase 2 - read current/latest backup status only.
    $router->get('/v1/backup/status', static function (): void {
        (new BackupController())->status();
    });


    // Mobile Backup Phase 5 - backup run lifecycle and history.
    $router->post('/v1/backup/pause', static function (): void {
        (new BackupController())->pause();
    });

    $router->post('/v1/backup/resume', static function (): void {
        (new BackupController())->resume();
    });

    $router->post('/v1/backup/cancel', static function (): void {
        (new BackupController())->cancel();
    });

    $router->post('/v1/backup/complete', static function (): void {
        (new BackupController())->complete();
    });

    $router->get('/v1/backup/history', static function (): void {
        (new BackupController())->history();
    });


    // Mobile Backup Phase 3 - plan phone inventory only.
    // This endpoint returns upload/skip/suppressed actions and updates only
    // backup-run scan counters. It does not create NAS folders or upload data.
    $router->post('/v1/backup/plan', static function (): void {
        (new BackupController())->plan();
    });

    // Mobile Backup Phase 3 - ensure canonical cloud folder hierarchy.
    // Uses the existing NAS FolderService for DB + physical folder creation.
    $router->post('/v1/backup/folders/ensure', static function (): void {
        (new BackupController())->ensureFolders();
    });

    // Mobile Backup Phase 4 - upload one phone file into an already ensured
    // Mobile Backup folder. Uses multipart/form-data and canonical NAS storage.
    $router->post('/v1/backup/upload', static function (): void {
        (new BackupController())->upload();
    });


    // Mobile Backup Phase 4.2 - per-device automatic-backup preferences.
    // The server stores policy only; Android/iOS performs the background trigger.
    $router->get('/v1/backup/settings', static function (): void {
        (new BackupController())->settings();
    });

    $router->put('/v1/backup/settings', static function (): void {
        (new BackupController())->updateSettings();
    });

    // Mobile Backup Phase 4.1 - explicitly re-enable one source that the user
    // previously deleted from cloud backup. This does not restore Trash data.
    $router->post('/v1/backup/items/backup-again', static function (): void {
        (new BackupController())->backupAgain();
    });


    // Phase 8 - Storage clusters + native/client synchronization.
    // Normal sync is delegated to the existing ClientSyncService, which exposes
    // cloud manifests and accepts client uploads without creating a local-PC
    // sync directory on the PHP server.
    $router->get('/v1/clusters', static function (): void {
        (new ClusterController())->index();
    });

    $router->get('/v1/sync/status', static function (): void {
        (new SyncController())->status();
    });

    $router->post('/v1/sync/init', static function (): void {
        (new SyncController())->init();
    });

    $router->post('/v1/sync/start', static function (): void {
        (new SyncController())->start();
    });

    $router->get('/v1/sync/manifest', static function (): void {
        (new SyncController())->manifest();
    });

    $router->post('/v1/sync/folders/ensure', static function (): void {
        (new SyncController())->ensureFolders();
    });

    $router->post('/v1/sync/upload', static function (): void {
        (new SyncController())->upload();
    });

    $router->post('/v1/sync/complete', static function (): void {
        (new SyncController())->complete();
    });

    $router->post('/v1/sync/download', static function (): void {
        (new SyncController())->download();
    });

    $router->post('/v1/sync/uploaded', static function (): void {
        (new SyncController())->uploaded();
    });

    $router->get('/v1/sync/history', static function (): void {
        (new SyncController())->history();
    });

    $router->post('/v1/sync/cancel', static function (): void {
        (new SyncController())->cancel();
    });

    $router->get('/v1/sync/folders', static function (): void {
        (new SyncController())->folders();
    });

    $router->get('/v1/sync/unsynced', static function (): void {
        (new SyncController())->unsynced();
    });

    $router->get('/v1/sync/logs', static function (): void {
        (new SyncController())->logs();
    });

    $router->post('/v1/sync/pause', static function (): void {
        (new SyncController())->pause();
    });

    $router->post('/v1/sync/resume', static function (): void {
        (new SyncController())->resume();
    });

    // Legacy maintenance compatibility. Flutter normal sync should not call it.
    $router->post('/v1/sync/repair', static function (): void {
        (new SyncController())->repair();
    });

    // Phase 9 - Preview discovery + Office-to-PDF conversion.
    // XLS/XLSX/CSV deliberately stay on the secure raw preview endpoint so
    // clients can render them with the XLSX-compatible spreadsheet library.
    $router->get('/v1/preview/info', static function (): void {
        (new PreviewController())->info();
    });

    $router->get('/v1/preview/convert', static function (): void {
        (new PreviewController())->convert();
    });

    $router->dispatch();
} catch (Throwable $e) {
    error_log('[nas_api] ' . get_class($e) . ': ' . $e->getMessage());

    JsonResponse::send([
        'success' => false,
        'message' => NAS_API_DEBUG ? $e->getMessage() : 'Internal server error.',
    ], 500);
}
