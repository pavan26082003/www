<?php

namespace App\Controllers\NAS\Api\V1;

use App\Controllers\NAS\BaseController;
use App\Services\NAS\ClientSyncService;
use App\Services\NAS\SyncService;

class SyncController extends BaseController
{
    private SyncService $legacySyncService;
    private ClientSyncService $clientSyncService;

    public function __construct()
    {
        $fileRepository = new \App\Repositories\NAS\FileRepository();
        $folderRepository = new \App\Repositories\NAS\FolderRepository();
        $shareRepository = new \App\Repositories\NAS\ShareRepository();
        $userRepository = new \App\Repositories\NAS\UserRepository();
        $syncRepository = new \App\Repositories\NAS\SyncRepository();
        $storageService = new \App\Services\NAS\StorageService();

        $permissionService = new \App\Services\NAS\PermissionService(
            $fileRepository,
            $folderRepository,
            $shareRepository
        );

        // Keep the old service available only for history/log/repair compatibility.
        // Normal Init/Sync/Download no longer touches a server-side "local" folder.
        $this->legacySyncService = new SyncService(
            $syncRepository,
            $fileRepository,
            $folderRepository,
            $userRepository,
            $storageService,
            $permissionService
        );

        $this->clientSyncService = new ClientSyncService(
            $syncRepository,
            $fileRepository,
            $folderRepository,
            $userRepository,
            $storageService
        );
    }

    /** GET /NAS/api/v1/sync/status */
    public function status(): void
    {
        try {
            $userId = $this->getUserId();
            $status = $this->clientSyncService->getStatus($userId);
            $this->success(['status' => $status], 'Sync status retrieved');
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $this->statusCode($e));
        }
    }

    /**
     * POST /NAS/api/v1/sync/init
     * Register browser sync and return the cloud manifest.
     * No mkdir/copy is performed on the web server.
     */
    public function init(): void
    {
        try {
            $userId = $this->getUserId();
            $result = $this->clientSyncService->initialize($userId);
            $this->success($result, 'Browser sync initialized');
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $this->statusCode($e));
        }
    }

    /** GET /NAS/api/v1/sync/manifest */
    public function manifest(): void
    {
        try {
            $userId = $this->getUserId();
            $manifest = $this->clientSyncService->getManifest($userId);
            $this->success(['manifest' => $manifest], 'Sync manifest retrieved');
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $this->statusCode($e));
        }
    }

    /**
     * POST /NAS/api/v1/sync/start
     * Starts a client-driven session. The browser performs the local scan.
     */
    public function start(): void
    {
        try {
            $userId = $this->getUserId();
            $result = $this->clientSyncService->begin($userId);
            $this->success($result, 'Client sync started');
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $this->statusCode($e));
        }
    }

    /**
     * POST /NAS/api/v1/sync/folders/ensure
     * JSON: {"paths":["Projects/A","Projects/A/Docs"]}
     */
    public function ensureFolders(): void
    {
        try {
            $userId = $this->getUserId();
            $input = json_decode((string) file_get_contents('php://input'), true);
            $paths = is_array($input) && isset($input['paths']) && is_array($input['paths'])
                ? $input['paths']
                : [];

            $result = $this->clientSyncService->ensureFolders($userId, $paths);
            $this->success($result, 'Sync folders processed');
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $this->statusCode($e));
        }
    }

    /**
     * POST /NAS/api/v1/sync/upload
     * multipart/form-data:
     *   file=<binary>
     *   relative_path=Projects/A/test.php
     */
    public function upload(): void
    {
        try {
            $userId = $this->getUserId();
            $relativePath = isset($_POST['relative_path'])
                ? trim((string) $_POST['relative_path'])
                : '';

            if ($relativePath === '') {
                $this->error('relative_path is required', 400);
                return;
            }
            if (!isset($_FILES['file'])) {
                $this->error('file is required', 400);
                return;
            }

            $result = $this->clientSyncService->uploadFile(
                $userId,
                $relativePath,
                $_FILES['file']
            );

            $this->success($result, !empty($result['skipped'])
                ? 'File already exists'
                : 'File synchronized successfully');
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $this->statusCode($e));
        }
    }

    /** POST /NAS/api/v1/sync/complete */
    public function complete(): void
    {
        try {
            $userId = $this->getUserId();
            $input = json_decode((string) file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $input = [];
            }

            $result = $this->clientSyncService->complete($userId, $input);
            $this->success($result, $result['message'] ?? 'Sync completed');
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $this->statusCode($e));
        }
    }

    /**
     * POST /NAS/api/v1/sync/download
     * Kept for compatibility. It now returns a manifest; the browser writes it
     * to the user's selected local folder.
     */
    public function download(): void
    {
        try {
            $userId = $this->getUserId();
            $manifest = $this->clientSyncService->getManifest($userId);
            $this->success(
                ['manifest' => $manifest, 'client_mode' => 'browser'],
                'Download manifest prepared'
            );
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $this->statusCode($e));
        }
    }

    /**
     * POST /NAS/api/v1/sync/uploaded
     * A normal web upload should not be copied into a directory on the server.
     * Mark it pending for a later client pull instead.
     */
    public function uploaded(): void
    {
        try {
            $userId = $this->getUserId();
            $input = json_decode((string) file_get_contents('php://input'), true);
            if (!is_array($input) || empty($input['file_id'])) {
                $this->error('File ID required', 400);
                return;
            }

            $result = $this->clientSyncService->markCloudFilePending(
                $userId,
                (int) $input['file_id']
            );
            $this->success($result, 'Cloud file queued for local synchronization');
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $this->statusCode($e));
        }
    }

    /** GET /NAS/api/v1/sync/history */
    public function history(): void
    {
        try {
            $userId = $this->getUserId();
            $history = $this->legacySyncService->getHistory($userId);
            $this->success(['history' => $history], 'Sync history retrieved');
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $this->statusCode($e));
        }
    }

    /** POST /NAS/api/v1/sync/cancel */
    public function cancel(): void
    {
        try {
            $userId = $this->getUserId();
            $result = $this->clientSyncService->cancel($userId);
            $this->success($result, 'Sync cancelled');
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $this->statusCode($e));
        }
    }

    /** GET /NAS/api/v1/sync/folders */
    public function folders(): void
    {
        try {
            $userId = $this->getUserId();
            $folders = $this->clientSyncService->getFolders($userId);
            $this->success(['folders' => $folders], 'Sync folders retrieved');
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $this->statusCode($e));
        }
    }

    /** GET /NAS/api/v1/sync/unsynced */
    public function unsynced(): void
    {
        try {
            $userId = $this->getUserId();
            $files = $this->legacySyncService->getUnsyncedFiles($userId);
            $this->success(['files' => $files], 'Unsynced files retrieved');
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $this->statusCode($e));
        }
    }

    /** GET /NAS/api/v1/sync/logs */
    public function logs(): void
    {
        try {
            $userId = $this->getUserId();
            $limit = isset($_GET['limit']) ? max(1, min(200, (int) $_GET['limit'])) : 50;
            $logs = $this->legacySyncService->getLogs($userId, $limit);
            $this->success(['logs' => $logs], 'Sync logs retrieved');
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $this->statusCode($e));
        }
    }

    /** POST /NAS/api/v1/sync/pause */
    public function pause(): void
    {
        try {
            $userId = $this->getUserId();
            $result = $this->clientSyncService->pause($userId);
            $this->success($result, 'Sync paused');
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $this->statusCode($e));
        }
    }

    /** POST /NAS/api/v1/sync/resume */
    public function resume(): void
    {
        try {
            $userId = $this->getUserId();
            $result = $this->clientSyncService->resume($userId);
            $this->success($result, 'Sync resumed');
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $this->statusCode($e));
        }
    }

    /** POST /NAS/api/v1/sync/repair */
    public function repair(): void
    {
        try {
            $userId = $this->getUserId();
            $result = $this->legacySyncService->repairClusters($userId);
            $this->success($result, 'Clusters repaired');
        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $this->statusCode($e));
        }
    }

    private function statusCode(\Throwable $e): int
    {
        $code = (int) $e->getCode();
        return $code >= 400 && $code <= 599 ? $code : 500;
    }
}
