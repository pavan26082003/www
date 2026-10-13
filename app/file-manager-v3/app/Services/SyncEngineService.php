<?php
namespace App\Services;

use App\Repositories\FileRepository;
use App\Repositories\FolderRepository;
use App\Repositories\SyncRepository;
use App\Repositories\UserRepository;
use App\Helpers\FileHelper;

class SyncEngineService
{
    private FileRepository $fileRepository;
    private FolderRepository $folderRepository;
    private SyncRepository $syncRepository;
    private UserRepository $userRepository;
    private StorageService $storageService;
    private PermissionService $permissionService;
    private array $changes = [];
    private array $conflicts = [];

    public function __construct(
        FileRepository $fileRepository,
        FolderRepository $folderRepository,
        SyncRepository $syncRepository,
        UserRepository $userRepository,
        StorageService $storageService,
        PermissionService $permissionService
    ) {
        $this->fileRepository = $fileRepository;
        $this->folderRepository = $folderRepository;
        $this->syncRepository = $syncRepository;
        $this->userRepository = $userRepository;
        $this->storageService = $storageService;
        $this->permissionService = $permissionService;
    }

    /**
     * Run full sync for a user
     */
    public function runSync(int $userId, string $sessionId): array
    {
        $this->changes = [];
        $this->conflicts = [];

        try {
            // Step 1: Detect changes
            $this->detectChanges($userId);
            
            // Step 2: Resolve conflicts
            $this->resolveConflicts($userId);
            
            // Step 3: Apply changes
            $this->applyChanges($userId);
            
            // Step 4: Update sync metadata
            $this->updateSyncMetadata($userId, $sessionId);
            
            return [
                'success' => true,
                'changes' => $this->changes,
                'conflicts' => $this->conflicts,
                'total_files_synced' => count($this->changes)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'changes' => $this->changes,
                'conflicts' => $this->conflicts
            ];
        }
    }

    /**
     * Detect changes in files and folders
     */
    private function detectChanges(int $userId): void
    {
        $pdo = $this->userRepository->getPdo();
        
        // Get all files modified in the last 24 hours
        $stmt = $pdo->prepare("
            SELECT id, original_name, file_path, file_size, updated_at, deleted_at
            FROM files 
            WHERE user_id = ? 
            AND (updated_at > DATE_SUB(NOW(), INTERVAL 24 HOUR) 
                 OR deleted_at IS NOT NULL)
        ");
        $stmt->execute([$userId]);
        $files = $stmt->fetchAll();

        foreach ($files as $file) {
            $change = $this->determineFileChange($userId, $file);
            if ($change) {
                $this->changes[] = $change;
            }
        }

        // Get all folders modified in the last 24 hours
        $stmt = $pdo->prepare("
            SELECT id, name, parent_id, updated_at, deleted_at
            FROM folders 
            WHERE user_id = ? 
            AND (updated_at > DATE_SUB(NOW(), INTERVAL 24 HOUR) 
                 OR deleted_at IS NOT NULL)
        ");
        $stmt->execute([$userId]);
        $folders = $stmt->fetchAll();

        foreach ($folders as $folder) {
            $change = $this->determineFolderChange($userId, $folder);
            if ($change) {
                $this->changes[] = $change;
            }
        }
    }

    /**
     * Determine file change type
     */
    private function determineFileChange(int $userId, array $file): ?array
    {
        if ($file['deleted_at'] !== null) {
            return [
                'type' => 'file',
                'id' => $file['id'],
                'action' => 'delete',
                'name' => $file['original_name']
            ];
        }

        // Check if file has been synced before
        $pdo = $this->userRepository->getPdo();
        $stmt = $pdo->prepare("
            SELECT id FROM sync_queue 
            WHERE user_id = ? AND item_type = 'file' AND item_id = ? 
            AND status = 'completed'
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$userId, $file['id']]);
        $lastSync = $stmt->fetch();

        if (!$lastSync) {
            return [
                'type' => 'file',
                'id' => $file['id'],
                'action' => 'create',
                'name' => $file['original_name'],
                'path' => $file['file_path'],
                'size' => $file['file_size']
            ];
        }

        // Check if file was modified after last sync
        $stmt = $pdo->prepare("
            SELECT created_at FROM sync_queue 
            WHERE user_id = ? AND item_type = 'file' AND item_id = ? 
            AND status = 'completed'
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$userId, $file['id']]);
        $lastSyncTime = $stmt->fetch();

        if ($lastSyncTime && strtotime($file['updated_at']) > strtotime($lastSyncTime['created_at'])) {
            return [
                'type' => 'file',
                'id' => $file['id'],
                'action' => 'update',
                'name' => $file['original_name'],
                'path' => $file['file_path'],
                'size' => $file['file_size']
            ];
        }

        return null;
    }

    /**
     * Determine folder change type
     */
    private function determineFolderChange(int $userId, array $folder): ?array
    {
        if ($folder['deleted_at'] !== null) {
            return [
                'type' => 'folder',
                'id' => $folder['id'],
                'action' => 'delete',
                'name' => $folder['name']
            ];
        }

        $pdo = $this->userRepository->getPdo();
        $stmt = $pdo->prepare("
            SELECT id FROM sync_queue 
            WHERE user_id = ? AND item_type = 'folder' AND item_id = ? 
            AND status = 'completed'
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$userId, $folder['id']]);
        $lastSync = $stmt->fetch();

        if (!$lastSync) {
            return [
                'type' => 'folder',
                'id' => $folder['id'],
                'action' => 'create',
                'name' => $folder['name'],
                'parent_id' => $folder['parent_id']
            ];
        }

        $stmt = $pdo->prepare("
            SELECT created_at FROM sync_queue 
            WHERE user_id = ? AND item_type = 'folder' AND item_id = ? 
            AND status = 'completed'
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$userId, $folder['id']]);
        $lastSyncTime = $stmt->fetch();

        if ($lastSyncTime && strtotime($folder['updated_at']) > strtotime($lastSyncTime['created_at'])) {
            return [
                'type' => 'folder',
                'id' => $folder['id'],
                'action' => 'update',
                'name' => $folder['name'],
                'parent_id' => $folder['parent_id']
            ];
        }

        return null;
    }

    /**
     * Resolve conflicts
     */
    private function resolveConflicts(int $userId): void
    {
        $pdo = $this->userRepository->getPdo();
        
        // Check for file conflicts (same name in same folder)
        $stmt = $pdo->prepare("
            SELECT f1.id as file1_id, f1.original_name, f1.folder_id,
                   f2.id as file2_id, f2.original_name as conflict_name
            FROM files f1
            JOIN files f2 ON f1.folder_id = f2.folder_id 
                AND f1.original_name = f2.original_name 
                AND f1.id != f2.id
            WHERE f1.user_id = ? AND f1.deleted_at IS NULL 
                AND f2.deleted_at IS NULL
        ");
        $stmt->execute([$userId]);
        $conflicts = $stmt->fetchAll();

        foreach ($conflicts as $conflict) {
            $this->conflicts[] = [
                'type' => 'file',
                'file_id' => $conflict['file1_id'],
                'conflict_with' => $conflict['file2_id'],
                'name' => $conflict['original_name'],
                'folder_id' => $conflict['folder_id'],
                'resolution' => 'rename' // Default resolution
            ];
        }

        // Check for folder conflicts
        $stmt = $pdo->prepare("
            SELECT f1.id as folder1_id, f1.name, f1.parent_id,
                   f2.id as folder2_id, f2.name as conflict_name
            FROM folders f1
            JOIN folders f2 ON f1.parent_id = f2.parent_id 
                AND f1.name = f2.name 
                AND f1.id != f2.id
            WHERE f1.user_id = ? AND f1.deleted_at IS NULL 
                AND f2.deleted_at IS NULL
        ");
        $stmt->execute([$userId]);
        $conflicts = $stmt->fetchAll();

        foreach ($conflicts as $conflict) {
            $this->conflicts[] = [
                'type' => 'folder',
                'folder_id' => $conflict['folder1_id'],
                'conflict_with' => $conflict['folder2_id'],
                'name' => $conflict['name'],
                'parent_id' => $conflict['parent_id'],
                'resolution' => 'rename'
            ];
        }
    }

    /**
     * Apply changes
     */
    private function applyChanges(int $userId): void
    {
        foreach ($this->changes as $change) {
            try {
                if ($change['type'] === 'file') {
                    $this->applyFileChange($userId, $change);
                } else {
                    $this->applyFolderChange($userId, $change);
                }
            } catch (\Exception $e) {
                error_log("Sync error: " . $e->getMessage());
            }
        }
    }

    /**
     * Apply file change
     */
    private function applyFileChange(int $userId, array $change): void
    {
        switch ($change['action']) {
            case 'create':
                // Ensure file exists on disk
                $fullPath = $this->storageService->getFullPath($change['path']);
                if (!file_exists($fullPath)) {
                    throw new \Exception("File not found on disk: " . $change['name']);
                }
                break;

            case 'update':
                // Update file metadata
                $this->fileRepository->update($change['id'], [
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                break;

            case 'delete':
                // Delete from disk
                $file = $this->fileRepository->find($change['id']);
                if ($file) {
                    $this->storageService->deleteFile($file['file_path']);
                    $this->fileRepository->forceDelete($change['id']);
                }
                break;
        }
    }

    /**
     * Apply folder change
     */
    private function applyFolderChange(int $userId, array $change): void
    {
        switch ($change['action']) {
            case 'create':
                // Ensure folder exists on disk
                $cluster = $this->clusterRepository->getDefault($userId);
                if ($cluster) {
                    $clusterDir = $this->storageService->createClusterDirectory($userId, $cluster['slug']);
                    
                    // Build path
                    $path = '';
                    if ($change['parent_id']) {
                        $parent = $this->folderRepository->find($change['parent_id']);
                        if ($parent) {
                            $path = $this->getFolderPath($parent) . '/';
                        }
                    }
                    
                    $fullPath = $clusterDir . $path . $change['name'];
                    if (!is_dir($fullPath)) {
                        mkdir($fullPath, 0755, true);
                    }
                }
                break;

            case 'update':
                // Update folder metadata
                $this->folderRepository->update($change['id'], [
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                break;

            case 'delete':
                // Delete folder and its contents
                $folder = $this->folderRepository->find($change['id']);
                if ($folder) {
                    // Get all files in folder
                    $filesInFolder = $this->fileRepository->getFilesInFolderIds([$change['id']], $userId);
                    foreach ($filesInFolder as $file) {
                        $this->storageService->deleteFile($file['file_path']);
                    }
                    $this->folderRepository->forceDelete($change['id']);
                }
                break;
        }
    }

    /**
     * Get folder path recursively
     */
    private function getFolderPath(array $folder): string
    {
        if (!$folder['parent_id']) {
            return $folder['name'];
        }
        
        $parent = $this->folderRepository->find($folder['parent_id']);
        if ($parent) {
            return $this->getFolderPath($parent) . '/' . $folder['name'];
        }
        
        return $folder['name'];
    }

    /**
     * Update sync metadata
     */
    private function updateSyncMetadata(int $userId, string $sessionId): void
    {
        $pdo = $this->userRepository->getPdo();
        
        // Update session
        $stmt = $pdo->prepare("
            UPDATE sync_sessions 
            SET files_synced = ?, last_sync_at = NOW(), completed_at = NOW(), status = 'completed'
            WHERE session_id = ? AND user_id = ?
        ");
        $stmt->execute([count($this->changes), $sessionId, $userId]);

        // Update user last sync
        $stmt = $pdo->prepare("
            UPDATE users SET last_sync_at = NOW() WHERE id = ?
        ");
        $stmt->execute([$userId]);
    }
}