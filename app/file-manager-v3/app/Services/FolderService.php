<?php
namespace App\Services;

use App\Repositories\FolderRepository;
use App\Repositories\FileRepository;
use App\Repositories\ClusterRepository;
use App\Repositories\ShareRepository;
use App\Repositories\StarredRepository;
use App\Repositories\UserRepository;
use App\Models\Folder;
use App\Helpers\FileHelper;

class FolderService
{
    private FolderRepository $folderRepository;
    private FileRepository $fileRepository;
    private ClusterRepository $clusterRepository;
    private ShareRepository $shareRepository;
    private StarredRepository $starredRepository;
    private UserRepository $userRepository;
    private StorageService $storageService;
    private PermissionService $permissionService;

    public function __construct(
        FolderRepository $folderRepository,
        FileRepository $fileRepository,
        ClusterRepository $clusterRepository,
        ShareRepository $shareRepository,
        StarredRepository $starredRepository,
        UserRepository $userRepository,
        StorageService $storageService,
        PermissionService $permissionService
    ) {
        $this->folderRepository = $folderRepository;
        $this->fileRepository = $fileRepository;
        $this->clusterRepository = $clusterRepository;
        $this->shareRepository = $shareRepository;
        $this->starredRepository = $starredRepository;
        $this->userRepository = $userRepository;
        $this->storageService = $storageService;
        $this->permissionService = $permissionService;
    }

    /**
     * Get folders
     */
    public function getFolders(int $userId, ?int $parentId = null, ?int $folderId = null): array
{
    if ($folderId !== null) {
        $folder = $this->folderRepository->find($folderId);
        if ($folder) {
            if ($folder['deleted_at'] !== null) {
                throw new \Exception('Cannot open folder in trash', 403);
            }
            return ['success' => true, 'folder' => $folder];
        }
        throw new \Exception('Folder not found', 404);
    }

    // Pass null to get root folders
    $folders = $this->folderRepository->getSubfolders($parentId, $userId);
    return ['success' => true, 'folders' => $folders];
}

    /**
     * Create folder
     */
    public function createFolder(int $userId, string $name, ?int $parentId = null, ?string $clusterSlug = null): array
{
    // Auto-detect cluster
    if (!$clusterSlug || $clusterSlug === 'home' || $clusterSlug === 'my-drive' || $clusterSlug === 'drive') {
        $clusterSlug = 'projects';
    }

    // Get cluster
    $cluster = $this->clusterRepository->getBySlug($userId, $clusterSlug);
    if (!$cluster) {
        $cluster = $this->clusterRepository->getDefault($userId);
    }

    if (!$cluster) {
        $this->clusterRepository->createDefaultClusters($userId);
        $cluster = $this->clusterRepository->getDefault($userId);
    }

    $clusterId = $cluster['id'];

    // If parent is provided, use parent's cluster
    if ($parentId !== null) {
        $parent = $this->folderRepository->find($parentId);
        if ($parent && $parent['user_id'] == $userId) {
            $clusterId = $parent['cluster_id'];
            $cluster = $this->clusterRepository->find($clusterId);
            if ($cluster) {
                $clusterSlug = $cluster['slug'];
            }
        }
    }

    // Check duplicate
    $existing = $this->folderRepository->findDuplicate($userId, $clusterId, $parentId, $name);
    if ($existing) {
        throw new \Exception('A folder with this name already exists', 400);
    }

    // Create folder - pass array directly
    $folderId = $this->folderRepository->create([
        'user_id' => $userId,
        'cluster_id' => $clusterId,
        'parent_id' => $parentId,
        'name' => $name
    ]);

    // Create physical folder
    $clusterDir = $this->storageService->createClusterDirectory($userId, $clusterSlug);
    
    // Build path
    if ($parentId === null) {
        $folderPath = $clusterDir . $name . '/';
    } else {
        $parent = $this->folderRepository->find($parentId);
        $parentPath = $parent ? $parent['name'] : '';
        $folderPath = $clusterDir . $parentPath . '/' . $name . '/';
    }

    if (!is_dir($folderPath)) {
        mkdir($folderPath, 0755, true);
    }

    return [
        'success' => true,
        'message' => 'Folder created successfully in "' . $clusterSlug . '" cluster',
        'folder_id' => $folderId,
        'cluster_id' => $clusterId,
        'cluster_slug' => $clusterSlug
    ];
}

    /**
     * Update folder
     */
    public function updateFolder(int $userId, int $folderId, string $action, array $data): array
    {
        $folder = $this->folderRepository->findWithTrash($folderId);
        
        if (!$folder) {
            throw new \Exception('Folder not found', 404);
        }

        $isOwner = ($folder['user_id'] == $userId);
        
        if (!$isOwner) {
            $share = $this->shareRepository->findForUserAndItem($userId, 'folder', $folderId);
            if (!$share) {
                throw new \Exception('Access denied', 403);
            }
        }

        switch ($action) {
            case 'rename':
                return $this->renameFolder($userId, $folder, $data['new_name'] ?? '', $isOwner);
            
            case 'star':
                return $this->toggleStar($userId, $folder, $isOwner);
            
            case 'restore':
                return $this->restoreFolder($userId, $folder);
            
            case 'open':
                return $this->markOpened($folder);
            
            default:
                throw new \Exception('Invalid action', 400);
        }
    }

    /**
     * Rename folder
     */
    private function renameFolder(int $userId, array $folder, string $newName, bool $isOwner): array
    {
        if (!$isOwner) {
            throw new \Exception('Only the owner can rename this folder', 403);
        }

        if (empty($newName)) {
            throw new \Exception('New name required', 400);
        }

        $existing = $this->folderRepository->findDuplicate(
            $userId, 
            $folder['cluster_id'], 
            $folder['parent_id'], 
            $newName
        );
        
        if ($existing && $existing['id'] != $folder['id']) {
            throw new \Exception('A folder with this name already exists', 400);
        }

        $this->folderRepository->update($folder['id'], ['name' => $newName]);
        return ['success' => true, 'message' => 'Folder renamed successfully'];
    }

    /**
     * Toggle star
     */
    private function toggleStar(int $userId, array $folder, bool $isOwner): array
    {
        if ($isOwner) {
            $newStatus = $folder['is_starred'] ? 0 : 1;
            $this->folderRepository->update($folder['id'], ['is_starred' => $newStatus]);
            return [
                'success' => true,
                'message' => $newStatus ? 'Folder starred' : 'Folder unstarred',
                'is_starred' => (bool)$newStatus
            ];
        } else {
            $isStarred = $this->starredRepository->isStarred($userId, 'folder', $folder['id']);
            
            if ($isStarred) {
                $this->starredRepository->unstar($userId, 'folder', $folder['id']);
                return [
                    'success' => true,
                    'message' => 'Folder unstarred',
                    'is_starred' => false
                ];
            } else {
                $this->starredRepository->star($userId, 'folder', $folder['id']);
                return [
                    'success' => true,
                    'message' => 'Folder starred',
                    'is_starred' => true
                ];
            }
        }
    }

    /**
     * Restore folder
     */
    public function restoreFolder(int $userId, array $folder): array
    {
        if ($folder['user_id'] != $userId) {
            throw new \Exception('Only the owner can restore this folder', 403);
        }

        // Get all subfolders
        $allFolderIds = $this->folderRepository->getAllSubfolderIds($folder['id'], $userId, true);
        
        // Restore folders
        $foldersRestored = $this->folderRepository->bulkRestore($allFolderIds, $userId);
        
        // Restore files in these folders
        $filesInFolders = $this->fileRepository->getFilesInFolderIds($allFolderIds, $userId);
        $fileIds = array_column($filesInFolders, 'id');
        
        $filesRestored = 0;
        if (!empty($fileIds)) {
            $placeholders = implode(',', array_fill(0, count($fileIds), '?'));
            $params = array_merge($fileIds, [$userId]);
            $stmt = $this->fileRepository->getPdo()->prepare("
                UPDATE files 
                SET deleted_at = NULL 
                WHERE id IN ($placeholders) AND user_id = ?
            ");
            $stmt->execute($params);
            $filesRestored = $stmt->rowCount();
        }

        return [
            'success' => true,
            'message' => "Folder restored with {$foldersRestored} folders and {$filesRestored} files",
            'folders_restored' => $foldersRestored,
            'files_restored' => $filesRestored
        ];
    }

    /**
     * Mark folder as opened
     */
    private function markOpened(array $folder): array
    {
        $this->folderRepository->update($folder['id'], ['last_opened' => date('Y-m-d H:i:s')]);
        return ['success' => true];
    }

    /**
     * Soft delete a folder and all its contents (move to trash)
     */
    public function deleteFolder(int $userId, int $folderId): array
    {
        $folder = $this->folderRepository->find($folderId);
        if (!$folder) {
            throw new \Exception('Folder not found', 404);
        }
        
        if ($folder['user_id'] != $userId) {
            throw new \Exception('You do not have permission to delete this folder', 403);
        }
        
        // ✅ Get all subfolder IDs
        $allFolderIds = $this->folderRepository->getAllSubfolderIds($folderId, $userId, false);
        $allFolderIds[] = $folderId;
        
        // ✅ Get all files in all subfolders
        $allFileIds = [];
        if (!empty($allFolderIds)) {
            $allFileIds = $this->fileRepository->getFileIdsFromFolders($allFolderIds, $userId, false);
        }
        
        // ✅ Soft delete all files
        foreach ($allFileIds as $fileId) {
            $this->fileRepository->softDelete($fileId);
        }
        
        // ✅ Soft delete all subfolders (reverse order so deepest first)
        $allFolderIds = array_reverse($allFolderIds);
        foreach ($allFolderIds as $subFolderId) {
            $this->folderRepository->softDelete($subFolderId);
        }
        
        return [
            'success' => true,
            'message' => 'Folder moved to trash with all contents'
        ];
    }

    /**
     * Permanently delete a folder and all its contents
     */
    public function deleteFolderPermanently(int $userId, int $folderId): array
    {
        $folder = $this->folderRepository->findWithTrash($folderId);
        if (!$folder) {
            throw new \Exception('Folder not found', 404);
        }
        
        // Check ownership
        if ($folder['user_id'] != $userId) {
            throw new \Exception('You do not have permission to delete this folder', 403);
        }
        
        // ✅ Step 1: Get all subfolder IDs recursively
        $allFolderIds = $this->folderRepository->getAllSubfolderIds($folderId, $userId, true);
        
        // ✅ Step 2: Get all files in all subfolders
        $allFileIds = [];
        if (!empty($allFolderIds)) {
            $allFileIds = $this->fileRepository->getFileIdsFromFolders($allFolderIds, $userId, true);
        }
        
        // ✅ Step 3: Also get files directly in the root folder
        $directFiles = $this->fileRepository->getFilesByFolderId($folderId, $userId);
        foreach ($directFiles as $file) {
            $allFileIds[] = $file['id'];
        }
        
        
        // ✅ Step 4: Delete all files permanently
        $deletedFiles = 0;
        foreach ($allFileIds as $fileId) {
            try {
                $file = $this->fileRepository->findWithTrash($fileId);
                if ($file) {
                    // Delete physical file
                    if (!empty($file['file_path'])) {
                        $this->storageService->deleteFile($file['file_path']);
                    }
                    // Force delete from database
                    $this->fileRepository->forceDelete($fileId);
                    $deletedFiles++;
                }
            } catch (\Exception $e) {
                error_log("  ❌ Error deleting file ID $fileId: " . $e->getMessage());
            }
        }
        
        // ✅ Step 5: Delete all subfolders
        $deletedFolders = 0;
        // Sort in reverse order (delete deepest first)
        $allFolderIds = array_reverse($allFolderIds);
        foreach ($allFolderIds as $subFolderId) {
            try {
                $this->folderRepository->forceDelete($subFolderId);
                $deletedFolders++;
            } catch (\Exception $e) {
                error_log("  ❌ Error deleting subfolder ID $subFolderId: " . $e->getMessage());
            }
        }
        
        // ✅ Step 6: Delete the root folder itself
        // Delete physical folder if exists
        $folderPath = $folder['path'] ?? '';
        if (!empty($folderPath)) {
            $fullPath = $this->storageService->getFullPath($folderPath);
            if (file_exists($fullPath) && is_dir($fullPath)) {
                $this->storageService->deleteDirectory($folderPath);
            }
        }
        
        $this->folderRepository->forceDelete($folderId);
        
        // ✅ Step 7: Update storage usage
        $totalSize = $this->calculateFolderSize($userId, $folderId);
        if ($totalSize > 0) {
            $this->userRepository->updateStorageUsed($userId, $totalSize, 'subtract');
        }
        
        return [
            'success' => true,
            'message' => "Folder permanently deleted with $deletedFiles files and $deletedFolders subfolders",
            'deleted_files' => $deletedFiles,
            'deleted_folders' => $deletedFolders
        ];
    }

    /**
     * Calculate total size of all files in a folder
     */
    private function calculateFolderSize(int $userId, int $folderId): int
    {
        $totalSize = 0;
        
        // Get files in this folder
        $files = $this->fileRepository->getFilesByFolderId($folderId, $userId);
        foreach ($files as $file) {
            $totalSize += (int)($file['file_size'] ?? 0);
        }
        
        // Get subfolders and their files
        $subfolders = $this->folderRepository->getSubfolders($folderId, $userId);
        foreach ($subfolders as $subfolder) {
            $totalSize += $this->calculateFolderSize($userId, $subfolder['id']);
        }
        
        return $totalSize;
    }

    /**
     * Soft delete folder
     */
    private function softDeleteFolder(int $userId, array $folder): array
    {
        $allFolderIds = $this->folderRepository->getAllSubfolderIds($folder['id'], $userId, false);
        $allFolderIds[] = $folder['id'];
        
        // Mark folders as deleted
        $this->folderRepository->update($folder['id'], ['deleted_at' => date('Y-m-d H:i:s')]);
        foreach ($allFolderIds as $subId) {
            $this->folderRepository->update($subId, ['deleted_at' => date('Y-m-d H:i:s')]);
        }

        // Mark files in these folders as deleted
        $filesInFolders = $this->fileRepository->getFilesInFolderIds($allFolderIds, $userId);
        $fileIds = array_column($filesInFolders, 'id');
        
        if (!empty($fileIds)) {
            $placeholders = implode(',', array_fill(0, count($fileIds), '?'));
            $params = array_merge($fileIds, [$userId]);
            $stmt = $this->fileRepository->getPdo()->prepare("
                UPDATE files 
                SET deleted_at = NOW() 
                WHERE id IN ($placeholders) AND user_id = ? AND deleted_at IS NULL
            ");
            $stmt->execute($params);
        }

        return [
            'success' => true,
            'message' => 'Folder and all contents moved to trash',
            'folders_moved' => count($allFolderIds)
        ];
    }

    /**
     * Permanent delete folder
     */
    private function permanentDeleteFolder(int $userId, array $folder): array
    {
        $allFolderIds = $this->folderRepository->getAllSubfolderIds($folder['id'], $userId, true);
        $allFolderIds[] = $folder['id'];
        
        // Get all files and delete from disk
        $totalSize = 0;
        $filesInFolders = $this->fileRepository->getFilesInFolderIds($allFolderIds, $userId);
        
        foreach ($filesInFolders as $file) {
            $this->storageService->deleteFile($file['file_path']);
            $totalSize += $file['file_size'];
        }

        // Delete files from database
        if (!empty($filesInFolders)) {
            $fileIds = array_column($filesInFolders, 'id');
            $placeholders = implode(',', array_fill(0, count($fileIds), '?'));
            $params = array_merge($fileIds, [$userId]);
            $stmt = $this->fileRepository->getPdo()->prepare("
                DELETE FROM files 
                WHERE id IN ($placeholders) AND user_id = ?
            ");
            $stmt->execute($params);
        }

        // Delete folders from database
        $this->folderRepository->bulkDelete($allFolderIds, $userId);

        // Update storage
        if ($totalSize > 0) {
            $this->userRepository->updateStorageUsed($userId, $totalSize, 'subtract');
        }

        return ['success' => true, 'message' => 'Folder and all contents permanently deleted'];
    }

    /**
     * Get folder info
     */
    public function getFolderInfo(int $userId, int $folderId): array
    {
        $folder = $this->folderRepository->find($folderId);
        
        if (!$folder) {
            throw new \Exception('Folder not found', 404);
        }

        if (!$this->permissionService->canView($userId, 'folder', $folderId)) {
            throw new \Exception('Access denied', 403);
        }

        $isOwner = ($folder['user_id'] == $userId);
        $permission = $this->permissionService->getPermission($userId, 'folder', $folderId);
        
        $share = $this->shareRepository->findForUserAndItem($userId, 'folder', $folderId);
        $sharedBy = $share ? $share['user_id'] : null;

        // Get folder size and file count
        $filesInFolder = $this->fileRepository->getFilesInFolderIds([$folderId], $folder['user_id']);
        $folderSize = array_sum(array_column($filesInFolder, 'file_size'));
        $fileCount = count($filesInFolder);

        return [
            'id' => (int)$folder['id'],
            'name' => $folder['name'],
            'owner_id' => (int)$folder['user_id'],
            'parent_id' => $folder['parent_id'] ? (int)$folder['parent_id'] : null,
            'created_at' => $folder['created_at'],
            'is_starred' => (bool)$folder['is_starred'],
            'is_owner' => $isOwner,
            'can_download' => $isOwner || in_array($permission, ['download', 'edit', 'share', 'full']),
            'permission' => $permission,
            'shared_by' => $sharedBy,
            'folder_size' => $folderSize,
            'folder_size_formatted' => FileHelper::formatSize($folderSize),
            'file_count' => $fileCount
        ];
    }

    /**
     * Get folder for restore
     */
    public function getFolderForRestore(int $userId, int $folderId): array
    {
        $folder = $this->folderRepository->findWithTrash($folderId);
        
        if (!$folder) {
            throw new \Exception('Folder not found', 404);
        }

        if ($folder['user_id'] != $userId) {
            throw new \Exception('Only the owner can restore this folder', 403);
        }

        return $folder;
    }
}