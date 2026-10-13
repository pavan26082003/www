<?php
namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\FolderService;
use App\Services\StorageService;
use App\Services\PermissionService;
use App\Repositories\FileRepository;
use App\Repositories\FolderRepository;
use App\Repositories\ClusterRepository;
use App\Repositories\ShareRepository;
use App\Repositories\StarredRepository;
use App\Repositories\UserRepository;

class FolderController extends BaseController
{
     private FolderService $folderService;
    private PermissionService $permissionService;
    private FolderRepository $folderRepository;
    private FileRepository $fileRepository;

    public function __construct()
    {
        // Create dependencies
        $fileRepository = new FileRepository();
        $folderRepository = new FolderRepository();
        $clusterRepository = new ClusterRepository();
        $shareRepository = new ShareRepository();
        $starredRepository = new StarredRepository();
        $userRepository = new UserRepository();
        $storageService = new StorageService();

        $this->fileRepository = $fileRepository;
        $this->folderRepository = $folderRepository;
        $this->permissionService = new PermissionService(
            $fileRepository,
            $folderRepository,
            $shareRepository
        );
        
        $this->folderService = new FolderService(
            $folderRepository,
            $fileRepository,
            $clusterRepository,
            $shareRepository,
            $starredRepository,
            $userRepository,
            $storageService,
            $this->permissionService
        );
    }

    /**
     * GET - List Folders
     * Route: /api/v1/folders
     */
    public function index(): void
    {
        try {
            $userId = $this->getUserId();
            $parentId = isset($_GET['parent_id']) && $_GET['parent_id'] !== '' ? (int)$_GET['parent_id'] : null;
            $folderId = isset($_GET['id']) && $_GET['id'] !== '' ? (int)$_GET['id'] : null;

            $result = $this->folderService->getFolders($userId, $parentId, $folderId);
            $this->success($result, 'Folders retrieved');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * POST - Create Folder
     * Route: /api/v1/folders
     */
    public function store(): void
    {
        try {
            $userId = $this->getUserId();
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                $this->error('Invalid JSON data', 400);
                return;
            }

            $name = trim($input['name'] ?? '');
            $parentId = isset($input['parent_id']) && $input['parent_id'] !== '' && $input['parent_id'] !== 'null' 
                ? (int)$input['parent_id'] 
                : null;
            $clusterSlug = isset($input['cluster']) ? $input['cluster'] : null;

            if (empty($name)) {
                $this->error('Folder name required', 400);
                return;
            }

            $result = $this->folderService->createFolder($userId, $name, $parentId, $clusterSlug);
            $this->success($result, $result['message']);

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * PUT - Update Folder
     * Route: /api/v1/folders/{id}
     */
    public function update(int $folderId): void
    {
        try {
            $userId = $this->getUserId();
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                $this->error('Invalid JSON data', 400);
                return;
            }

            $action = isset($input['action']) ? $input['action'] : 'rename';

            $result = $this->folderService->updateFolder($userId, $folderId, $action, $input);
            $this->success($result, $result['message']);

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    
    /**
     * DELETE - Delete Folder
     * Route: /api/v1/folders/{id}
     */
    public function destroy(int $folderId): void
    {
        try {
            $userId = $this->getUserId();
            
            $input = json_decode(file_get_contents('php://input'), true);
            $permanent = false;
            
            // Check both JSON payload and GET parameter
            if ($input && isset($input['permanent'])) {
                $permanent = (bool)$input['permanent'];
            } elseif (isset($_GET['permanent'])) {
                $permanent = $_GET['permanent'] === 'true' || $_GET['permanent'] === '1';
            }
            

            // ✅ If permanent delete, delete all contents first
            if ($permanent) {
                $result = $this->folderService->deleteFolderPermanently($userId, $folderId);
            } else {
                $result = $this->folderService->deleteFolder($userId, $folderId);
            }
            
            $this->success($result, $result['message']);

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    /**
     * PUT - Restore Folder
     * Route: /api/v1/folders/{id}/restore
     */
    public function restore(int $folderId): void
    {
        try {
            $userId = $this->getUserId();
            
            // ✅ Find the folder including trash
            $folder = $this->folderRepository->findWithTrash($folderId);
            
            if (!$folder) {
                $this->error('Folder not found', 404);
                return;
            }
            
            // Check if user is the owner
            if ($folder['user_id'] != $userId) {
                $this->error('Only the owner can restore this folder', 403);
                return;
            }
            
            // Check if folder is actually in trash
            if ($folder['deleted_at'] === null) {
                $this->error('Folder is not in trash', 400);
                return;
            }
            
            // Get all subfolders
            $allFolderIds = $this->folderRepository->getAllSubfolderIds($folderId, $userId, true);
            
            // Restore folders
            $foldersRestored = $this->folderRepository->bulkRestore($allFolderIds, $userId);

            $fileIds = $this->fileRepository->getFileIdsFromFolders($allFolderIds, $userId, true);
            
            
            $filesRestored = 0;
            if (!empty($fileIds)) {
                $filesRestored = $this->fileRepository->restoreFiles($fileIds, $userId);
            }
            
           $this->success([
            'folders_restored' => $foldersRestored,
            'files_restored' => $filesRestored,
            'total_folders' => count($allFolderIds),
            'total_files' => count($fileIds)
            ], "Folder restored with {$foldersRestored} folders and {$filesRestored} files");
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * GET - Folder Info
     * Route: /api/v1/folders/{id}/info
     */
    public function info(int $folderId): void
    {
        try {
            $userId = $this->getUserId();
            $info = $this->folderService->getFolderInfo($userId, $folderId);
            $this->success(['folder' => $info], 'Folder info retrieved');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 404);
        }
    }
}