<?php
namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\SyncService;

class SyncController extends BaseController
{
    private SyncService $syncService;

    public function __construct()
    {
        // Initialize sync service with all dependencies
        $fileRepository = new \App\Repositories\FileRepository();
        $folderRepository = new \App\Repositories\FolderRepository();
        $shareRepository = new \App\Repositories\ShareRepository();
        $userRepository = new \App\Repositories\UserRepository();
        $syncRepository = new \App\Repositories\SyncRepository();
        $storageService = new \App\Services\StorageService();
        
        $permissionService = new \App\Services\PermissionService(
            $fileRepository,
            $folderRepository,
            $shareRepository
        );
        
        $this->syncService = new SyncService(
            $syncRepository,
            $fileRepository,
            $folderRepository,
            $userRepository,
            $storageService,
            $permissionService
        );
    }

    /**
     * GET - Sync Status
     * Route: /api/v1/sync/status
     */
    public function status(): void
    {
        try {
            $userId = $this->getUserId();
            $status = $this->syncService->getStatus($userId);
            $this->success(['status' => $status], 'Sync status retrieved');
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $statusCode);
        }
    }

    /**
     * POST - Initialize Sync (Create local folder and download all files)
     * Route: /api/v1/sync/init
     */
    public function init(): void
    {
        try {
            
            $userId = $this->getUserId();
            
            $result = $this->syncService->initializeSync($userId);
            
            $this->success($result, 'Sync initialized successfully');
            
        } catch (\Exception $e) {
            
            // ✅ Return proper JSON error
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            exit;
        }
    }

    /**
     * POST - Start Sync (Upload local changes to cloud)
     * Route: /api/v1/sync/start
     */
    public function start(): void
    {
        try {
            $userId = $this->getUserId();
            
            $result = $this->syncService->uploadAllFiles($userId);
            
            $this->success($result, $result['message'] ?? 'Sync completed');
            
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $statusCode);
        }
    }

    /**
     * POST - Download from Cloud to Local
     * Route: /api/v1/sync/download
     */
    public function download(): void
    {
        try {
            $userId = $this->getUserId();
            $result = $this->syncService->downloadAllFiles($userId);
            $this->success($result, 'Download completed');
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $statusCode);
        }
    }

    /**
     * POST - Sync Uploaded File (When file uploaded via web)
     * Route: /api/v1/sync/uploaded
     */
    public function uploaded(): void
    {
        try {
            $userId = $this->getUserId();
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['file_id'])) {
                $this->error('File ID required', 400);
                return;
            }

            $result = $this->syncService->syncUploadedFile($userId, (int)$input['file_id']);
            $this->success($result, 'File synced to local folder');
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $statusCode);
        }
    }

    /**
     * GET - Sync History
     * Route: /api/v1/sync/history
     */
    public function history(): void
    {
        try {
            $userId = $this->getUserId();
            $history = $this->syncService->getHistory($userId);
            $this->success(['history' => $history], 'Sync history retrieved');
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $statusCode);
        }
    }

    /**
     * POST - Cancel Sync
     * Route: /api/v1/sync/cancel
     */
    public function cancel(): void
    {
        try {
            $userId = $this->getUserId();
            $result = $this->syncService->cancelSync($userId);
            $this->success($result, 'Sync cancelled');
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $statusCode);
        }
    }

    /**
     * GET - Get sync folders
     * Route: /api/v1/sync/folders
     */
    public function folders(): void
    {
        try {
            $userId = $this->getUserId();
            $folders = $this->syncService->getSyncFolders($userId);
            $this->success(['folders' => $folders], 'Sync folders retrieved');
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $statusCode);
        }
    }

    /**
     * GET - Get unsynced files
     * Route: /api/v1/sync/unsynced
     */
    public function unsynced(): void
    {
        try {
            $userId = $this->getUserId();
            $files = $this->syncService->getUnsyncedFiles($userId);
            $this->success(['files' => $files], 'Unsynced files retrieved');
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $statusCode);
        }
    }

    /**
     * GET - Get sync logs
     * Route: /api/v1/sync/logs
     */
    public function logs(): void
    {
        try {
            $userId = $this->getUserId();
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
            $logs = $this->syncService->getLogs($userId, $limit);
            $this->success(['logs' => $logs], 'Sync logs retrieved');
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $statusCode);
        }
    }

    /**
     * POST - Pause sync
     * Route: /api/v1/sync/pause
     */
    public function pause(): void
    {
        try {
            $userId = $this->getUserId();
            $result = $this->syncService->pauseSync($userId);
            $this->success($result, 'Sync paused');
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $statusCode);
        }
    }

    /**
     * POST - Resume sync
     * Route: /api/v1/sync/resume
     */
    public function resume(): void
    {
        try {
            $userId = $this->getUserId();
            $result = $this->syncService->resumeSync($userId);
            $this->success($result, 'Sync resumed');
        } catch (\Exception $e) {
            $statusCode = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $statusCode);
        }
    }

    /**
     * POST - Repair file clusters
     * Route: /api/v1/sync/repair
     */
    public function repair(): void
    {
        try {
            $userId = $this->getUserId();
            $result = $this->syncService->repairClusters($userId);
            $this->success($result, 'Clusters repaired');
        } catch (\Exception $e) {
            $code = is_int($e->getCode()) ? $e->getCode() : 500;
            $this->error($e->getMessage(), $code);
        }
    }
}