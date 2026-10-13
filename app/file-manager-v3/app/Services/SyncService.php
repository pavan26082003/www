<?php
namespace App\Services;

use App\Repositories\SyncRepository;
use App\Repositories\FileRepository;
use App\Repositories\FolderRepository;
use App\Repositories\UserRepository;

class SyncService
{
    private SyncRepository $syncRepository;
    private FileRepository $fileRepository;
    private FolderRepository $folderRepository;
    private UserRepository $userRepository;
    private StorageService $storageService;
    private PermissionService $permissionService;
    private string $syncPath;

    public function __construct(
        SyncRepository $syncRepository,
        FileRepository $fileRepository,
        FolderRepository $folderRepository,
        UserRepository $userRepository,
        StorageService $storageService,
        PermissionService $permissionService
    ) {
        $this->syncRepository = $syncRepository;
        $this->fileRepository = $fileRepository;
        $this->folderRepository = $folderRepository;
        $this->userRepository = $userRepository;
        $this->storageService = $storageService;
        $this->permissionService = $permissionService;
        $this->syncPath = $this->getSyncPath();
    }

    /**
     * Get or create sync folder path for user
     */
    private function getSyncPath(): string
    {
        $home = getenv('USERPROFILE') ?: getenv('HOME');
        $path = $home . DIRECTORY_SEPARATOR . 'eFactor' . DIRECTORY_SEPARATOR . 'my-drive';
        
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        
        return $path;
    }

    /**
     * Get user-specific sync folder path
     */
    private function getUserSyncPath(int $userId): string
    {
        // Get username
        $user = $this->userRepository->find($userId);
        $username = $user ? $user['username'] : 'user_' . $userId;
        
        // Sanitize username for folder name
        $username = preg_replace('/[^a-zA-Z0-9._-]/', '', $username);
        
        return $this->syncPath . DIRECTORY_SEPARATOR . $username;
    }

    /**
     * Initialize sync for a user - creates local folder
     */
    public function initializeSync(int $userId): array
    {
        $user = $this->userRepository->find($userId);
        
        if (!$user) {
            throw new \Exception('User not found');
        }

        // Get username for folder
        $username = preg_replace('/[^a-zA-Z0-9._-]/', '', $user['username']);
        
        // Create user-specific sync folder with username
        $userSyncPath = $this->syncPath . DIRECTORY_SEPARATOR . $username;
        if (!is_dir($userSyncPath)) {
            mkdir($userSyncPath, 0755, true);
        }

        // Create cluster folders
        $clusters = ['Documents', 'Images', 'Videos', 'Projects', 'Archives', 'My Drive', 'Computers'];
        foreach ($clusters as $cluster) {
            $path = $userSyncPath . DIRECTORY_SEPARATOR . $cluster;
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        // Create sync folder record in database
        $existingFolder = $this->syncRepository->getSyncFolder($userId);
        if (!$existingFolder) {
            $this->syncRepository->createSyncFolder(
                $userId,
                $userSyncPath,
                $username,
                false
            );
        }

        // Create sync status record if not exists
        $this->syncRepository->updateSyncStatus($userId, 'idle');

        // ✅ Check if files are already synced
        $syncedFiles = $this->syncRepository->getSyncedFiles($userId);
        $totalSynced = count($syncedFiles);
        $totalCloudFiles = $this->fileRepository->countByUser($userId);
        
        error_log("📊 Sync status: " . $totalSynced . " files synced, " . $totalCloudFiles . " files in cloud");

        // ✅ Only download if there are files that need syncing
        if ($totalSynced < $totalCloudFiles) {
            error_log("📥 Downloading missing files...");
            $result = $this->downloadAllFiles($userId);
        } else {
            $result = [
                'total' => $totalCloudFiles,
                'downloaded' => 0,
                'skipped' => $totalCloudFiles,
                'errors' => []
            ];
        }

        return [
            'success' => true,
            'message' => 'Sync initialized successfully',
            'folder_path' => $userSyncPath,
            'total_files' => $result['total'],
            'downloaded' => $result['downloaded'],
            'skipped' => $result['skipped'],
            'errors' => $result['errors']
        ];
    }

    /**
     * Download all files from cloud to local folder
     */
    public function downloadAllFiles(int $userId): array
    {
        $result = [
            'total' => 0,
            'downloaded' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        // Get all files for user
        $files = $this->fileRepository->getAllByUser($userId);
        $result['total'] = count($files);
        error_log("📊 Download: Found " . $result['total'] . " files in cloud");

        // Get user sync path with username
        $userSyncPath = $this->getUserSyncPath($userId);

        // Create cluster folders
        $clusters = ['Documents', 'Images', 'Videos', 'Projects', 'Archives', 'My Drive', 'Computers'];
        foreach ($clusters as $cluster) {
            $path = $userSyncPath . DIRECTORY_SEPARATOR . $cluster;
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        // Get existing sync status for files already downloaded
        $syncedFiles = $this->syncRepository->getSyncedFiles($userId);
        $syncedByFileId = [];
        foreach ($syncedFiles as $syncedFile) {
            $syncedByFileId[$syncedFile['file_id']] = $syncedFile;
        }

        // Download each file
        foreach ($files as $file) {
            try {
                $fileId = $file['id'];
                
                // ✅ Skip if file has no hash
                if (empty($file['file_hash'])) {
                    error_log("⚠️ File has no hash, skipping: " . ($file['original_name'] ?? 'unknown'));
                    $result['skipped']++;
                    continue;
                }
                
                // Check if this file is already in sync_status
                if (isset($syncedByFileId[$fileId])) {
                    $syncedInfo = $syncedByFileId[$fileId];
                    $localPath = $syncedInfo['local_path'] ?? '';
                    
                    // Check if the local file still exists
                    if (!empty($localPath)) {
                        $fullLocalPath = $this->storageService->getFullPath($localPath);
                        if (file_exists($fullLocalPath)) {
                            // Verify hash matches
                            $existingHash = hash_file('sha256', $fullLocalPath);
                            if ($existingHash === $file['file_hash']) {
                                $result['skipped']++;
                                error_log("⏭️ File already synced and exists locally: " . $file['original_name']);
                                continue;
                            }
                        }
                    }
                }

                // Determine target directory
                $clusterMap = [
                    'images' => 'Images',
                    'videos' => 'Videos',
                    'documents' => 'Documents',
                    'projects' => 'Projects',
                    'archives' => 'Archives',
                    'my-drive' => 'My Drive',
                    'computers' => 'Computers'
                ];
                
                $clusterName = $clusterMap[$file['cluster_slug'] ?? 'my-drive'] ?? 'My Drive';
                $targetDir = $userSyncPath . DIRECTORY_SEPARATOR . $clusterName;
                
                // Add folder structure if file is in a folder
                if ($file['folder_id']) {
                    $folderPath = $this->getFolderPath($file['folder_id']);
                    if ($folderPath) {
                        $targetDir .= DIRECTORY_SEPARATOR . $folderPath;
                    }
                }
                
                // Create directory if needed
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                
                // Target file path
                $targetPath = $targetDir . DIRECTORY_SEPARATOR . $file['original_name'];
                $relativePath = str_replace($userSyncPath . DIRECTORY_SEPARATOR, '', $targetPath);
                
                // Check if file already exists locally
                if (file_exists($targetPath)) {
                    $existingHash = hash_file('sha256', $targetPath);
                    if ($existingHash === $file['file_hash']) {
                        // File exists and hash matches - update sync status and skip
                        $this->syncRepository->addSyncStatus(
                            $userId,
                            $fileId,
                            $relativePath,
                            $file['file_hash'],  // ✅ Now it's not null
                            $file['file_size'],
                            'synced'
                        );
                        $result['skipped']++;
                        error_log("⏭️ File already exists (hash match): " . $file['original_name']);
                        continue;
                    }
                    // Hash doesn't match - rename duplicate
                    $info = pathinfo($file['original_name']);
                    $counter = 1;
                    while (file_exists($targetDir . DIRECTORY_SEPARATOR . $info['filename'] . ' (' . $counter . ').' . $info['extension'])) {
                        $counter++;
                    }
                    $targetPath = $targetDir . DIRECTORY_SEPARATOR . $info['filename'] . ' (' . $counter . ').' . $info['extension'];
                    $relativePath = str_replace($userSyncPath . DIRECTORY_SEPARATOR, '', $targetPath);
                    error_log("📝 Renaming duplicate: " . $file['original_name'] . " → " . $info['filename'] . ' (' . $counter . ').' . $info['extension']);
                }
                
                // Copy file from uploads to local
                $sourcePath = $this->storageService->getFullPath($file['file_path']);
                if (file_exists($sourcePath)) {
                    if (copy($sourcePath, $targetPath)) {
                        $result['downloaded']++;
                        error_log("✅ Downloaded: " . $file['original_name']);
                        
                        // ✅ Add to sync status
                        $this->syncRepository->addSyncStatus(
                            $userId,
                            $fileId,
                            $relativePath,
                            $file['file_hash'],
                            $file['file_size'],
                            'synced'
                        );
                        
                        $this->syncRepository->addLog(
                            $userId,
                            'download',
                            $file['original_name'],
                            $file['file_hash'],
                            'Downloaded successfully'
                        );
                    } else {
                        $result['errors'][] = 'Failed to copy: ' . $file['original_name'];
                    }
                } else {
                    $result['errors'][] = 'Source file not found: ' . $file['original_name'];
                }
                
            } catch (\Exception $e) {
                $result['errors'][] = $e->getMessage();
                error_log("❌ Error downloading file: " . $e->getMessage());
            }
        }

        error_log("📊 Download summary: Total={$result['total']}, Downloaded={$result['downloaded']}, Skipped={$result['skipped']}, Errors=" . count($result['errors']));
        
        return $result;
    }

    /**
     * Get folder path
     */
    private function getFolderPath(int $folderId): ?string
    {
        $folder = $this->folderRepository->find($folderId);
        if (!$folder) {
            return null;
        }
        
        $path = $folder['name'];
        if ($folder['parent_id']) {
            $parentPath = $this->getFolderPath($folder['parent_id']);
            if ($parentPath) {
                $path = $parentPath . DIRECTORY_SEPARATOR . $path;
            }
        }
        
        return $path;
    }

    /**
     * Get sync folders for user
     */
    public function getSyncFolders(int $userId): array
    {
        return $this->syncRepository->getSyncFolders($userId);
    }

    /**
     * Get unsynced files for user
     */
    public function getUnsyncedFiles(int $userId): array
    {
        return $this->syncRepository->getUnsyncedFiles($userId);
    }

    /**
     * Get sync logs
     */
    public function getLogs(int $userId, int $limit = 50): array
    {
        return $this->syncRepository->getLogs($userId, $limit);
    }

    /**
     * Pause sync
     */
    public function pauseSync(int $userId): array
    {
        $activeSession = $this->syncRepository->getActiveSession($userId);
        if ($activeSession) {
            $this->syncRepository->updateSessionStatus($activeSession['id'], 'idle');
            $this->syncRepository->updateSyncStatus($userId, 'idle');
        }
        
        // Update sync folder status
        $syncFolder = $this->syncRepository->getSyncFolder($userId);
        if ($syncFolder) {
            $stmt = $this->userRepository->getPdo()->prepare("
                UPDATE sync_folders SET sync_enabled = 0 WHERE id = ?
            ");
            $stmt->execute([$syncFolder['id']]);
        }
        
        return ['success' => true, 'message' => 'Sync paused'];
    }

    public function resumeSync(int $userId): array
    {
        // Update sync folder status
        $syncFolder = $this->syncRepository->getSyncFolder($userId);
        if ($syncFolder) {
            $stmt = $this->userRepository->getPdo()->prepare("
                UPDATE sync_folders SET sync_enabled = 1 WHERE id = ?
            ");
            $stmt->execute([$syncFolder['id']]);
        }
        
        $this->syncRepository->updateSyncStatus($userId, 'running');
        
        // Create new session
        $sessionId = $this->syncRepository->createSession($userId);
        
        // Resume syncing
        return $this->uploadAllFiles($userId);
    }

    /**
     * Get sync status
     */
    public function getStatus(int $userId): array
    {
        $activeSession = $this->syncRepository->getActiveSession($userId);
        $totalFiles = $this->fileRepository->countByUser($userId);
        $syncedFiles = $this->syncRepository->countSyncedFiles($userId);
        
        // Get user sync path with username
        $userSyncPath = $this->getUserSyncPath($userId);

        $queueStats = ['pending' => 0, 'completed' => 0, 'failed' => 0];
        if ($activeSession) {
            $queueStats = $this->syncRepository->getQueueStats($activeSession['session_id']);
        }
        
        // Get sync folder
        $syncFolder = $this->syncRepository->getSyncFolder($userId);
        
        return [
            'status' => $activeSession ? $activeSession['status'] : 'idle',
            'session_id' => $activeSession ? $activeSession['session_id'] : null,
            'started_at' => $activeSession ? $activeSession['started_at'] : null,
            'total_files' => $totalFiles,
            'files_synced' => $syncedFiles,
            'sync_folder' => $userSyncPath,
            'last_sync' => $activeSession ? $activeSession['last_sync_at'] : null,
            'queue_stats' => $queueStats,
            'sync_enabled' => $syncFolder ? (bool)$syncFolder['sync_enabled'] : false
        ];
    }

    public function uploadAllFiles(int $userId): array
    {
        $result = [
            'success' => true,
            'message' => 'Sync completed',
            'total' => 0,
            'uploaded' => 0,
            'updated' => 0,
            'deleted' => 0,
            'skipped' => 0,
            'errors' => [],
            'files' => []
        ];

        $userSyncPath = $this->getUserSyncPath($userId);
        
        if (!is_dir($userSyncPath)) {
            throw new \Exception('Sync folder not found for user', 404);
        }

        // ✅ Define cluster folders (system folders)
        $clusterFolderNames = ['Documents', 'Images', 'Videos', 'Projects', 'Archives', 'My Drive', 'Computers'];
        
        // ✅ STEP 1: Scan all files
        $localFiles = $this->scanLocalFolder($userSyncPath);
        $result['total'] = count($localFiles);
        error_log("📊 Found " . $result['total'] . " files locally");

        // ✅ STEP 2: Get existing files from database
        $dbFiles = $this->fileRepository->getAllByUser($userId);
        error_log("📊 Found " . count($dbFiles) . " files in database");
        
        // Build lookup maps
        $dbByPath = [];
        $dbByFolderAndName = [];
        
        foreach ($dbFiles as $dbFile) {
            if (!empty($dbFile['file_path'])) {
                $dbByPath[$this->normalizePath($dbFile['file_path'])] = $dbFile;
            }
            $key = ($dbFile['folder_id'] ?? 'root') . '|' . ($dbFile['original_name'] ?? '');
            $dbByFolderAndName[$key] = $dbFile;
        }

        // ✅ STEP 3: Collect folder paths (skip cluster folders at root level)
        $folderPaths = [];
        foreach ($localFiles as $localFile) {
            $normalizedPath = $this->normalizePath($localFile['relative_path']);
            $folderPath = dirname($normalizedPath);
            
            if ($folderPath === '.' || empty($folderPath)) {
                continue;
            }
            
            $parts = explode('/', $folderPath);
            $firstPart = $parts[0] ?? '';
            
            // ✅ Skip if it's just a cluster folder (root level)
            if (count($parts) == 1 && in_array($firstPart, $clusterFolderNames)) {
                error_log("⏭️ Skipping cluster folder: " . $folderPath);
                continue;
            }
            
            // ✅ If first part is a cluster, store the subfolder path
            if (in_array($firstPart, $clusterFolderNames) && count($parts) > 1) {
                // Example: "My Drive/New folder" → store "New folder"
                array_shift($parts);
                $actualFolderPath = implode('/', $parts);
                if (!empty($actualFolderPath) && !in_array($actualFolderPath, $folderPaths)) {
                    $folderPaths[] = $actualFolderPath;
                }
            } else if (!in_array($folderPath, $folderPaths)) {
                // Regular folder (not in a cluster)
                $folderPaths[] = $folderPath;
            }
        }
        
        // Sort by depth
        usort($folderPaths, function($a, $b) {
            return substr_count($a, '/') - substr_count($b, '/');
        });
        
        error_log("📁 Creating " . count($folderPaths) . " folder structures");
        $folderIdMap = [];
        foreach ($folderPaths as $folderPath) {
            $folderId = $this->createFolderStructureByPath($userId, $folderPath);
            $folderIdMap[$folderPath] = $folderId;
            error_log("📁 Folder: " . $folderPath . " → ID: " . ($folderId ?: 'null'));
        }

        // ✅ STEP 4: Process each local file
        foreach ($localFiles as $localFile) {
            try {
                $normalizedPath = $this->normalizePath($localFile['relative_path']);
                error_log("📝 Processing: " . $normalizedPath);
                
                // ✅ Determine cluster and folder
                $parts = explode('/', $normalizedPath);
                $firstPart = $parts[0] ?? '';
                $clusterSlug = null;
                $folderId = null;
                
                // ✅ Check if file is in a cluster
                if (in_array($firstPart, $clusterFolderNames)) {
                    // File is in a cluster folder
                    $clusterSlug = $this->detectClusterFromPath($firstPart);
                    error_log("📁 File in cluster: " . $firstPart . " (slug: " . $clusterSlug . ")");
                    
                    // If there are more parts, it's in a subfolder
                    if (count($parts) > 2) {
                        // Example: "My Drive/New folder/file.txt" → folder path is "New folder"
                        array_shift($parts); // Remove cluster name
                        array_pop($parts);   // Remove filename
                        $folderPath = implode('/', $parts);
                        
                        if (!empty($folderPath)) {
                            $folderId = $folderIdMap[$folderPath] ?? null;
                            if (!$folderId) {
                                $folderId = $this->getFolderIdByPath($userId, $folderPath);
                                if (!$folderId) {
                                    $folderId = $this->createFolderStructureByPath($userId, $folderPath);
                                    $folderIdMap[$folderPath] = $folderId;
                                }
                            }
                            error_log("📁 Subfolder path: " . $folderPath . " → ID: " . ($folderId ?: 'null'));
                        }
                    } else {
                        // File is directly in cluster root (e.g., "My Drive/file.txt")
                        error_log("📁 File in cluster root: " . $firstPart);
                        $folderId = null; // Root of cluster
                    }
                } else {
                    // ✅ File is NOT in a cluster folder
                    // Example: "New folder/file.txt" or "s1.PNG"
                    $clusterSlug = 'my-drive'; // Default cluster
                    
                    if (count($parts) > 1) {
                        // File is in a subfolder
                        array_pop($parts); // Remove filename
                        $folderPath = implode('/', $parts);
                        
                        if (!empty($folderPath) && $folderPath !== $firstPart) {
                            $folderId = $folderIdMap[$folderPath] ?? null;
                            if (!$folderId) {
                                $folderId = $this->getFolderIdByPath($userId, $folderPath);
                                if (!$folderId) {
                                    $folderId = $this->createFolderStructureByPath($userId, $folderPath);
                                    $folderIdMap[$folderPath] = $folderId;
                                }
                            }
                        } else {
                            // File is directly in root (e.g., "s1.PNG")
                            $folderId = null;
                        }
                    } else {
                        // File in root (single part)
                        $folderId = null;
                    }
                }
                
                // ✅ Get cluster ID
                $clusterId = $this->getClusterIdBySlug($userId, $clusterSlug);
                if (!$clusterId) {
                    $clusterId = $this->getDefaultClusterId($userId);
                    error_log("⚠️ Cluster not found, using default: " . $clusterId);
                }
                
                error_log("📁 File: " . $localFile['name'] . " → Cluster: " . $clusterSlug . " → Folder ID: " . ($folderId ?: 'root'));
                
                // ✅ Check if file exists in same location
                $existsInSameLocation = false;
                
                // Check by path
                if (isset($dbByPath[$normalizedPath])) {
                    $existingFile = $dbByPath[$normalizedPath];
                    if ($existingFile['file_size'] == $localFile['size']) {
                        $existsInSameLocation = true;
                        error_log("⏭️ File exists by path: " . $localFile['name']);
                        
                        $this->syncRepository->addSyncStatus(
                            $userId,
                            $existingFile['id'],
                            $normalizedPath,
                            $localFile['hash'],
                            $localFile['size'],
                            'synced'
                        );
                        $result['skipped']++;
                        continue;
                    }
                }
                
                // ✅ Check if file exists in same folder by name
                if ($folderId !== null) {
                    $key = ($folderId ?? 'root') . '|' . $localFile['name'];
                    if (isset($dbByFolderAndName[$key])) {
                        $existingFile = $dbByFolderAndName[$key];
                        if ($existingFile['file_size'] == $localFile['size']) {
                            $existsInSameLocation = true;
                            error_log("⏭️ File exists in same folder: " . $localFile['name']);
                            
                            $this->syncRepository->addSyncStatus(
                                $userId,
                                $existingFile['id'],
                                $normalizedPath,
                                $localFile['hash'],
                                $localFile['size'],
                                'synced'
                            );
                            $result['skipped']++;
                            continue;
                        }
                    }
                }
                
                // ✅ If not exists, upload it
                if (!$existsInSameLocation) {
                    error_log("📤 Uploading new file: " . $localFile['name']);
                    $uploadResult = $this->uploadSingleFile($userId, $localFile);
                    if ($uploadResult['success']) {
                        $result['uploaded']++;
                        $result['files'][] = [
                            'name' => $localFile['name'],
                            'action' => 'uploaded',
                            'path' => $normalizedPath,
                            'file_id' => $uploadResult['file_id']
                        ];
                        error_log("✅ Uploaded: " . $localFile['name']);
                    } else {
                        $result['errors'][] = $uploadResult['error'];
                        error_log("❌ Failed to upload: " . $localFile['name']);
                    }
                }
                
            } catch (\Exception $e) {
                $result['errors'][] = $e->getMessage();
                error_log("❌ Error: " . $e->getMessage());
            }
        }

        // ✅ STEP 5: Update sync status
        $syncFolder = $this->syncRepository->getSyncFolder($userId);
        if ($syncFolder) {
            $this->syncRepository->updateSyncFolderLastSync($syncFolder['id']);
        }
        
        $this->syncRepository->updateSyncStatus($userId, 'completed');

        $this->syncRepository->addLog(
            $userId,
            'upload',
            'sync_complete',
            null,
            sprintf(
                "Sync: %d uploaded, %d updated, %d skipped",
                $result['uploaded'],
                $result['updated'],
                $result['skipped']
            )
        );

        $result['message'] = sprintf(
            "Sync: %d uploaded, %d updated, %d skipped",
            $result['uploaded'],
            $result['updated'],
            $result['skipped']
        );

        return $result;
    }

    /**
     * Upload a single file with a known folder ID
     */
    private function uploadSingleFileWithFolderId(int $userId, array $localFile, ?int $folderId): array
    {
        try {
            $sourcePath = $localFile['path'];
            
            // ✅ Remove global hash check - we already checked in uploadAllFiles()
            // Just upload the file
            
            // ✅ Determine cluster
            $relativePath = $localFile['relative_path'];
            $clusterSlug = $this->detectClusterFromPath($relativePath);
            
            if ($clusterSlug === 'my-drive' || $clusterSlug === 'computers') {
                $fileCategory = $this->getFileCategory($localFile['name']);
                $clusterMap = [
                    'image' => 'images',
                    'video' => 'videos',
                    'audio' => 'audio',
                    'document' => 'documents',
                    'archive' => 'archives',
                    'code' => 'documents',
                    'other' => 'my-drive'
                ];
                $clusterSlug = $clusterMap[$fileCategory] ?? 'my-drive';
            }
            
            $clusterId = $this->getClusterIdBySlug($userId, $clusterSlug);
            if (!$clusterId) {
                $clusterId = $this->getDefaultClusterId($userId);
            }

            // Generate unique filename
            $extension = strtolower(pathinfo($localFile['name'], PATHINFO_EXTENSION));
            $filename = uniqid() . '.' . $extension;
            $uploadPath = 'user_' . $userId . '/' . $clusterSlug . '/' . $filename;
            
            // Save file
            $destPath = UPLOAD_PATH . '/' . $uploadPath;
            $destDir = dirname($destPath);
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            if (!copy($sourcePath, $destPath)) {
                return ['success' => false, 'error' => 'Failed to copy file to uploads'];
            }

            $fileHash = hash_file('sha256', $destPath);
            $fileSize = filesize($sourcePath);
            $mimeType = mime_content_type($sourcePath) ?: 'application/octet-stream';
            $fileCategory = $this->getFileCategory($localFile['name']);

            // ✅ Create file record with folder_id
            $fileData = [
                'user_id' => $userId,
                'cluster_id' => $clusterId,
                'folder_id' => $folderId,
                'filename' => $filename,
                'original_name' => $localFile['name'],
                'file_path' => $uploadPath,
                'file_size' => $fileSize,
                'file_type' => $mimeType,
                'file_hash' => $fileHash,
                'file_category' => $fileCategory
            ];

            $fileId = $this->fileRepository->create($fileData);
            error_log("✅ File created: " . $localFile['name'] . " (ID: " . $fileId . ", Folder ID: " . ($folderId ?: 'root') . ")");
            
            // Update storage usage
            $this->userRepository->updateStorageUsed($userId, $fileSize, 'add');

            // Add to sync status
            $this->syncRepository->addSyncStatus(
                $userId,
                $fileId,
                $localFile['relative_path'],
                $fileHash,
                $fileSize,
                'synced'
            );

            return ['success' => true, 'file_id' => $fileId];

        } catch (\Exception $e) {
            error_log("❌ uploadSingleFileWithFolderId error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get folder ID by path - Skips cluster folders
     */
    private function getFolderIdByPath(int $userId, string $folderPath): ?int
    {
        if (empty($folderPath) || $folderPath === '.' || $folderPath === '/') {
            return null;
        }
        
        $folderPath = trim($folderPath, '/');
        $parts = explode('/', $folderPath);
        
        // ✅ Define cluster folders
        $clusterFolderNames = ['Documents', 'Images', 'Videos', 'Projects', 'Archives', 'My Drive', 'Computers'];
        
        // ✅ If it's just a cluster folder, return null (it's not a user folder)
        if (count($parts) == 1 && in_array($parts[0], $clusterFolderNames)) {
            return null;
        }
        
        // ✅ Determine cluster
        $firstPart = $parts[0] ?? '';
        $startIndex = 0;
        $clusterId = null;
        
        if (in_array($firstPart, $clusterFolderNames)) {
            $clusterSlug = $this->detectClusterFromPath($folderPath);
            $clusterId = $this->getClusterIdBySlug($userId, $clusterSlug);
            if ($clusterId) {
                $startIndex = 1;
            }
        }
        
        if (!$clusterId) {
            $clusterId = $this->getDefaultClusterId($userId);
            $startIndex = 0;
        }
        
        // ✅ Traverse the path (only subfolders inside clusters)
        $currentParentId = null;
        $lastFolderId = null;
        
        for ($i = $startIndex; $i < count($parts); $i++) {
            $folderName = $parts[$i];
            if (empty($folderName)) continue;
            
            $existing = $this->folderRepository->findDuplicate($userId, $clusterId, $currentParentId, $folderName);
            if ($existing) {
                $currentParentId = $existing['id'];
                $lastFolderId = $currentParentId;
            } else {
                return null;
            }
        }
        
        return $lastFolderId;
    }

    /**
     * Repair file clusters - move files to correct clusters
     */
    public function repairClusters(int $userId): array
    {
        $result = [
            'total' => 0,
            'fixed' => 0,
            'errors' => []
        ];
        
        // Get all files for this user
        $files = $this->fileRepository->getAllByUser($userId);
        $result['total'] = count($files);
        
        foreach ($files as $file) {
            try {
                // Determine correct cluster from file path
                $correctCluster = $this->detectClusterFromPath($file['file_path']);
                $currentCluster = $file['cluster_slug'] ?? 'my-drive';
                
                if ($correctCluster !== $currentCluster) {
                    error_log("🔄 Fixing cluster for file: " . $file['original_name']);
                    error_log("  Current: " . $currentCluster . " → Correct: " . $correctCluster);
                    
                    $correctClusterId = $this->getClusterIdBySlug($userId, $correctCluster);
                    if ($correctClusterId) {
                        $this->fileRepository->update($file['id'], [
                            'cluster_id' => $correctClusterId
                        ]);
                        $result['fixed']++;
                        error_log("✅ Fixed: " . $file['original_name']);
                    } else {
                        $result['errors'][] = "Cluster not found: " . $correctCluster;
                    }
                }
            } catch (\Exception $e) {
                $result['errors'][] = $e->getMessage();
            }
        }
        
        return $result;
    }

    /**
     * Debug: Log directory structure
     */
    private function logDirectoryStructure(string $path, string $prefix = ''): void
    {
        if (!is_dir($path)) {
            return;
        }
        
        $items = scandir($path);
        if ($items === false) {
            return;
        }
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..' || $item === '.efactor' || $item === '.DS_Store') {
                continue;
            }
            
            $fullPath = $path . DIRECTORY_SEPARATOR . $item;
            $relative = $prefix ? $prefix . '/' . $item : $item;
            
            if (is_dir($fullPath)) {
                error_log("  📁 " . $relative . "/");
                $this->logDirectoryStructure($fullPath, $relative);
            } else {
                error_log("  📄 " . $relative);
            }
        }
    }

    /**
     * Update an existing file in cloud
     */
    private function updateExistingFile(int $userId, int $fileId, array $localFile): array
    {
        try {
            $existingFile = $this->fileRepository->find($fileId);
            if (!$existingFile) {
                return ['success' => false, 'error' => 'File not found in database'];
            }

            // Generate new filename
            $extension = strtolower(pathinfo($localFile['name'], PATHINFO_EXTENSION));
            $filename = uniqid() . '.' . $extension;
            
            // Determine cluster
            $cluster = $this->detectClusterFromPath($localFile['relative_path']);
            $clusterId = $this->getClusterIdBySlug($userId, $cluster);
            if (!$clusterId) {
                $clusterId = $this->getDefaultClusterId($userId);
            }

            // Upload path
            $uploadPath = 'user_' . $userId . '/' . $cluster . '/' . $filename;
            
            // Save file to uploads directory
            $destPath = UPLOAD_PATH . '/' . $uploadPath;
            $destDir = dirname($destPath);
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            // Copy file to uploads
            if (!copy($localFile['path'], $destPath)) {
                return ['success' => false, 'error' => 'Failed to copy file to uploads'];
            }

            $fileHash = hash_file('sha256', $destPath);

            // Update file in database
            $updateData = [
                'filename' => $filename,
                'file_path' => $uploadPath,
                'file_size' => $localFile['size'],
                'file_hash' => $fileHash,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->fileRepository->update($fileId, $updateData);
            
            // Update sync status
            $syncStatus = $this->syncRepository->getFileSyncStatus($fileId, $userId);
            if ($syncStatus) {
                $this->syncRepository->updateFileSyncStatus($syncStatus['id'], 'synced');
            }

            return ['success' => true, 'file_id' => $fileId];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    

    /**
     * Find files that exist in cloud but not locally
     * Only mark for deletion if they were previously synced AND no longer exist locally
     */
    private function findDeletedFiles(int $userId, array $localFiles): array
    {
        // Get all synced files for this user
        $syncedFiles = $this->syncRepository->getSyncedFiles($userId);
        
        if (empty($syncedFiles)) {
            return [];
        }
        
        // Build local file paths map for quick lookup
        $localPaths = [];
        foreach ($localFiles as $file) {
            $normalizedPath = $this->normalizePath($file['relative_path']);
            $localPaths[$normalizedPath] = true;
        }
        
        $deletedFileIds = [];
        
        foreach ($syncedFiles as $syncedFile) {
            $syncPath = $this->normalizePath($syncedFile['local_path'] ?? '');
            
            // Skip if local_path is empty or is the default '/'
            if (empty($syncPath) || $syncPath === '/') {
                continue;
            }
            
            // Check if the file still exists locally
            if (!isset($localPaths[$syncPath])) {
                // File exists in sync status but not locally - mark for deletion
                $file = $this->fileRepository->find($syncedFile['file_id']);
                if ($file && $file['deleted_at'] === null) {
                    $deletedFileIds[] = $syncedFile['file_id'];
                    error_log("🗑️ File no longer exists locally, marking for deletion: " . $syncPath);
                }
            }
        }
        
        return $deletedFileIds;
    }

    /**
     * Get relative path for comparison
     */
    private function getRelativePathForComparison(array $file): string
    {
        // Build path from cluster and folder structure
        $clusterMap = [
            'images' => 'Images',
            'videos' => 'Videos',
            'documents' => 'Documents',
            'projects' => 'Projects',
            'archives' => 'Archives',
            'my-drive' => 'My Drive',
            'computers' => 'Computers'
        ];
        
        $clusterName = $clusterMap[$file['cluster_slug'] ?? 'my-drive'] ?? 'My Drive';

        $pathParts = [$clusterName];
    
        // Add folder structure if exists
        if ($file['folder_id']) {
            $folderPath = $this->getFolderPath($file['folder_id']);
            if ($folderPath) {
                $pathParts[] = $folderPath;
            }
        }

        $pathParts[] = $file['original_name'] ?? 'unknown';
    
        return implode('/', $pathParts);
    }

    /**
     * Normalize path for comparison (remove extra slashes, trim)
     */
    private function normalizePath(string $path): string
    {
        // Replace backslashes with forward slashes
        $path = str_replace('\\', '/', $path);
        // Remove leading/trailing slashes
        $path = trim($path, '/');
        // Remove duplicate slashes
        $path = preg_replace('#/+#', '/', $path);
        return $path;
    }

    /**
     * Get sync status ID for a file
     */
    private function getSyncStatusId(int $userId, int $fileId): int
    {
        $syncStatus = $this->syncRepository->getFileSyncStatus($fileId, $userId);
        return $syncStatus ? (int)$syncStatus['id'] : 0;
    }

    /**
     * Scan local folder for files - Recursively scan ALL subfolders
     */
    private function scanLocalFolder(string $path, string $relativePath = ''): array
    {
        $results = [];
        
        if (!is_dir($path)) {
            error_log("⚠️ Directory not found: " . $path);
            return [];
        }
        
        error_log("📁 SCANNING: " . $path . " (relative: " . ($relativePath ?: 'root') . ")");
        
        $items = scandir($path);
        if ($items === false) {
            error_log("⚠️ Cannot scan directory: " . $path);
            return [];
        }
        
        error_log("📁 Found " . count($items) . " items in " . ($relativePath ?: 'root'));
        
        foreach ($items as $item) {
            // Skip hidden/system files
            if ($item === '.' || $item === '..' || $item === '.efactor' || $item === '.DS_Store') {
                continue;
            }
            
            $fullPath = $path . DIRECTORY_SEPARATOR . $item;
            $relative = $relativePath ? $relativePath . '/' . $item : $item;
            
            if (is_file($fullPath)) {
                $results[] = [
                    'name' => $item,
                    'path' => $fullPath,
                    'relative_path' => $relative,
                    'size' => filesize($fullPath),
                    'hash' => hash_file('sha256', $fullPath),
                    'modified' => filemtime($fullPath)
                ];
                error_log("  📄 Found file: " . $relative);
            } elseif (is_dir($fullPath)) {
                error_log("  📁 Found subfolder: " . $relative);
                // ✅ Recursively scan ALL subdirectories
                $subResults = $this->scanLocalFolder($fullPath, $relative);
                error_log("  📁 Subfolder " . $relative . " returned " . count($subResults) . " files");
                $results = array_merge($results, $subResults);
            }
        }
        
        error_log("📁 SCAN COMPLETE for " . ($relativePath ?: 'root') . ": " . count($results) . " total files");
        return $results;
    }

    private function uploadSingleFile(int $userId, array $localFile): array
    {
        try {
            $sourcePath = $localFile['path'];
            
            // ✅ Check if file already exists by hash
            $existingFile = $this->fileRepository->findByHash($localFile['hash']);
            if ($existingFile) {
                error_log("⏭️ Already exists (hash): " . $localFile['name']);
                $this->syncRepository->addSyncStatus(
                    $userId,
                    $existingFile['id'],
                    $localFile['relative_path'],
                    $localFile['hash'],
                    $localFile['size'],
                    'synced'
                );
                return ['success' => true, 'file_id' => $existingFile['id'], 'skipped' => true];
            }
            
            // ✅ Determine cluster
            $relativePath = $localFile['relative_path'];
            $clusterSlug = $this->detectClusterFromPath($relativePath);
            
            if ($clusterSlug === 'my-drive' || $clusterSlug === 'computers') {
                $fileCategory = $this->getFileCategory($localFile['name']);
                $clusterMap = [
                    'image' => 'images',
                    'video' => 'videos',
                    'audio' => 'audio',
                    'document' => 'documents',
                    'archive' => 'archives',
                    'code' => 'documents',
                    'other' => 'my-drive'
                ];
                $clusterSlug = $clusterMap[$fileCategory] ?? 'my-drive';
                error_log("📁 File category detected: " . $fileCategory . " → cluster: " . $clusterSlug);
            } else {
                error_log("📁 Path detected cluster: " . $clusterSlug);
            }
            
            $clusterId = $this->getClusterIdBySlug($userId, $clusterSlug);
            if (!$clusterId) {
                $clusterId = $this->getDefaultClusterId($userId);
                error_log("⚠️ Cluster not found, using default");
            }

            // ✅ Extract folder structure from path
            $parts = explode('/', $relativePath);
            $clusterFolderNames = ['Documents', 'Images', 'Videos', 'Projects', 'Archives', 'My Drive', 'Computers'];
            
            $folderParts = [];
            if (count($parts) > 1) {
                // Check if first part is a cluster folder
                if (in_array($parts[0], $clusterFolderNames)) {
                    array_shift($parts);
                }
                // Remove filename (last part)
                array_pop($parts);
                $folderParts = $parts;
            }
            
            error_log("📁 File: " . $localFile['name']);
            error_log("📁 Relative path: " . $relativePath);
            error_log("📁 Folder parts: " . (empty($folderParts) ? 'root' : implode(' → ', $folderParts)));
            
            // ✅ Get folder ID - CRITICAL FIX
            $folderId = null;
            if (!empty($folderParts)) {
                // Try to find the folder in database
                $folderPath = implode('/', $folderParts);
                $folderId = $this->getFolderIdByPath($userId, $folderPath);
                error_log("📁 Folder ID from path '" . $folderPath . "': " . ($folderId ?: 'not found'));
                
                // If folder not found, create it
                if (!$folderId) {
                    error_log("📁 Folder not found, creating: " . $folderPath);
                    $folderId = $this->createFolderStructureByPath($userId, $folderPath);
                    error_log("📁 Created folder ID: " . ($folderId ?: 'failed'));
                }
            }

            // Generate unique filename
            $extension = strtolower(pathinfo($localFile['name'], PATHINFO_EXTENSION));
            $filename = uniqid() . '.' . $extension;
            $uploadPath = 'user_' . $userId . '/' . $clusterSlug . '/' . $filename;
            
            // Save file
            $destPath = UPLOAD_PATH . '/' . $uploadPath;
            $destDir = dirname($destPath);
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            if (!copy($sourcePath, $destPath)) {
                return ['success' => false, 'error' => 'Failed to copy file to uploads'];
            }

            $fileHash = hash_file('sha256', $destPath);
            $fileSize = filesize($sourcePath);
            $mimeType = mime_content_type($sourcePath) ?: 'application/octet-stream';
            $fileCategory = $this->getFileCategory($localFile['name']);

            // ✅ Create file record
            $fileData = [
                'user_id' => $userId,
                'cluster_id' => $clusterId,
                'folder_id' => $folderId,
                'filename' => $filename,
                'original_name' => $localFile['name'],
                'file_path' => $uploadPath,
                'file_size' => $fileSize,
                'file_type' => $mimeType,
                'file_hash' => $fileHash,
                'file_category' => $fileCategory
            ];

            $fileId = $this->fileRepository->create($fileData);
            error_log("✅ File created: " . $localFile['name'] . " in cluster: " . $clusterSlug . " (ID: " . $fileId . ")");
            
            // Update storage usage
            $this->userRepository->updateStorageUsed($userId, $fileSize, 'add');

            // Add to sync status
            $this->syncRepository->addSyncStatus(
                $userId,
                $fileId,
                $localFile['relative_path'],
                $fileHash,
                $fileSize,
                'synced'
            );

            return ['success' => true, 'file_id' => $fileId];

        } catch (\Exception $e) {
            error_log("❌ uploadSingleFile error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get Computers cluster ID (with proper creation)
     */
    private function getComputersClusterId(int $userId): ?int
    {
        $pdo = $this->userRepository->getPdo();
        
        // First check if user already has Computers cluster
        $stmt = $pdo->prepare("
            SELECT c.id FROM storage_clusters c
            INNER JOIN user_clusters uc ON c.id = uc.cluster_id
            WHERE uc.user_id = ? AND c.slug = 'computers'
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        if ($result) {
            return (int)$result['id'];
        }
        
        // Check if global Computers cluster exists (user_id = 0)
        $stmt = $pdo->prepare("SELECT id FROM storage_clusters WHERE user_id = 0 AND slug = 'computers'");
        $stmt->execute([]);
        $global = $stmt->fetch();
        
        if ($global) {
            // Add the global cluster to user_clusters
            $stmt = $pdo->prepare("INSERT IGNORE INTO user_clusters (user_id, cluster_id) VALUES (?, ?)");
            $stmt->execute([$userId, $global['id']]);
            return (int)$global['id'];
        }
        
        // Check if user has a Computers cluster (user_id = userId)
        $stmt = $pdo->prepare("SELECT id FROM storage_clusters WHERE user_id = ? AND slug = 'computers'");
        $stmt->execute([$userId]);
        $userCluster = $stmt->fetch();
        
        if ($userCluster) {
            // Add to user_clusters
            $stmt = $pdo->prepare("INSERT IGNORE INTO user_clusters (user_id, cluster_id) VALUES (?, ?)");
            $stmt->execute([$userId, $userCluster['id']]);
            return (int)$userCluster['id'];
        }
        
        // Create Computers cluster for user
        try {
            $stmt = $pdo->prepare("
                INSERT INTO storage_clusters (user_id, name, slug, is_default, sort_order, icon) 
                VALUES (?, 'Computers', 'computers', 0, 20, 'fa-desktop')
            ");
            $stmt->execute([$userId]);
            $clusterId = (int)$pdo->lastInsertId();
            
            // Add to user_clusters
            $stmt = $pdo->prepare("INSERT IGNORE INTO user_clusters (user_id, cluster_id) VALUES (?, ?)");
            $stmt->execute([$userId, $clusterId]);
            
            return $clusterId;
        } catch (\PDOException $e) {
            // If duplicate, try to get the existing cluster
            $stmt = $pdo->prepare("SELECT id FROM storage_clusters WHERE user_id = ? AND slug = 'computers'");
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            if ($result) {
                return (int)$result['id'];
            }
            return null;
        }
    }

    /**
     * Create Computers cluster for user if it doesn't exist
     */
    private function createComputersCluster(int $userId): int
    {
        $pdo = $this->userRepository->getPdo();
        
        // Check if Computers cluster already exists
        $stmt = $pdo->prepare("SELECT id FROM storage_clusters WHERE user_id = ? AND slug = 'computers'");
        $stmt->execute([$userId]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            return (int)$existing['id'];
        }
        
        // Check if Computers cluster exists as a global cluster (user_id = 0)
        $stmt = $pdo->prepare("SELECT id FROM storage_clusters WHERE user_id = 0 AND slug = 'computers'");
        $stmt->execute([]);
        $global = $stmt->fetch();
        
        if ($global) {
            // Add the global cluster to user_clusters
            $stmt = $pdo->prepare("INSERT IGNORE INTO user_clusters (user_id, cluster_id) VALUES (?, ?)");
            $stmt->execute([$userId, $global['id']]);
            return (int)$global['id'];
        }
        
        // Create Computers cluster
        $stmt = $pdo->prepare("
            INSERT INTO storage_clusters (user_id, name, slug, is_default, sort_order, icon) 
            VALUES (?, 'Computers', 'computers', 0, 20, 'fa-desktop')
        ");
        $stmt->execute([$userId]);
        $clusterId = (int)$pdo->lastInsertId();
        
        // Also add to user_clusters
        $stmt = $pdo->prepare("INSERT IGNORE INTO user_clusters (user_id, cluster_id) VALUES (?, ?)");
        $stmt->execute([$userId, $clusterId]);
        
        return $clusterId;
    }

    /**
     * Add is_reference column to files table if not exists
     */
    private function addReferenceColumn(): void
    {
        $pdo = $this->userRepository->getPdo();
        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM files LIKE 'is_reference'");
            if ($stmt->rowCount() === 0) {
                $pdo->exec("ALTER TABLE files ADD COLUMN is_reference TINYINT(1) DEFAULT 0 AFTER file_hash");
            }
        } catch (\PDOException $e) {
            // Column might already exist
        }
    }

    /**
     * Detect cluster from file path
     */
    private function detectClusterFromPath(string $path): string
    {
        $parts = explode('/', $path);
        $firstPart = $parts[0] ?? '';
        
        // ✅ Map folder names to cluster slugs
        $clusterMap = [
            'Documents' => 'documents',
            'Images' => 'images',
            'Videos' => 'videos',
            'Projects' => 'projects',
            'Archives' => 'archives',
            'My Drive' => 'my-drive',
            'Computers' => 'computers'
        ];
        
        // ✅ Check if first part is a known cluster folder
        if (isset($clusterMap[$firstPart])) {
            return $clusterMap[$firstPart];
        }
        
        // ✅ If not, try to detect from file extension/type
        if (count($parts) > 0) {
            $filename = end($parts);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            // Check if extension matches a cluster
            $extensionMap = [
                'jpg' => 'images', 'jpeg' => 'images', 'png' => 'images', 'gif' => 'images',
                'webp' => 'images', 'svg' => 'images', 'bmp' => 'images', 'ico' => 'images',
                'mp4' => 'videos', 'webm' => 'videos', 'avi' => 'videos', 'mkv' => 'videos',
                'mov' => 'videos', 'wmv' => 'videos', 'flv' => 'videos',
                'mp3' => 'videos', 'wav' => 'videos', 'flac' => 'videos',
                'pdf' => 'documents', 'doc' => 'documents', 'docx' => 'documents',
                'xls' => 'documents', 'xlsx' => 'documents', 'ppt' => 'documents',
                'pptx' => 'documents', 'txt' => 'documents', 'rtf' => 'documents',
                'zip' => 'archives', 'rar' => 'archives', '7z' => 'archives',
                'tar' => 'archives', 'gz' => 'archives', 'bz2' => 'archives'
            ];
            
            if (isset($extensionMap[$extension])) {
                return $extensionMap[$extension];
            }
        }
        
        // ✅ Default to my-drive
        return 'my-drive';
    }

    /**
     * Create folder structure in database
     */
    private function createFolderStructure(int $userId, int $clusterId, string $path): ?int
    {
        $parts = explode('/', $path);
        // Remove first part (cluster name)
        array_shift($parts);
        
        if (empty($parts)) {
            return null;
        }
        
        // Remove last part (filename)
        array_pop($parts);
        
        if (empty($parts)) {
            return null;
        }
        
        $parentId = null;
        foreach ($parts as $folderName) {
            // Check if folder exists
            $existing = $this->folderRepository->findDuplicate($userId, $clusterId, $parentId, $folderName);
            if ($existing) {
                $parentId = $existing['id'];
            } else {
                $folderId = $this->folderRepository->create([
                    'user_id' => $userId,
                    'cluster_id' => $clusterId,
                    'parent_id' => $parentId,
                    'name' => $folderName
                ]);
                $parentId = $folderId;
            }
        }
        
        return $parentId;
    }

    /**
     * Create folder structure by path - Skips cluster folders
     */
    private function createFolderStructureByPath(int $userId, string $folderPath): ?int
    {
        if (empty($folderPath) || $folderPath === '.' || $folderPath === '/') {
            return null;
        }
        
        $folderPath = trim($folderPath, '/');
        $parts = explode('/', $folderPath);
        
        // ✅ Define cluster folders (these are system folders, not user folders)
        $clusterFolderNames = ['Documents', 'Images', 'Videos', 'Projects', 'Archives', 'My Drive', 'Computers'];
        
        // ✅ If it's just a cluster folder (e.g., "Projects"), skip creation
        if (count($parts) == 1 && in_array($parts[0], $clusterFolderNames)) {
            error_log("⏭️ Skipping cluster folder (system): " . $folderPath);
            return null;
        }
        
        // ✅ Determine cluster from the path
        $firstPart = $parts[0] ?? '';
        $startIndex = 0;
        $clusterId = null;
        $clusterSlug = null;
        
        // ✅ Check if first part is a cluster folder
        if (in_array($firstPart, $clusterFolderNames)) {
            // Map folder name to cluster slug
            $clusterSlug = $this->detectClusterFromPath($folderPath);
            $clusterId = $this->getClusterIdBySlug($userId, $clusterSlug);
            if ($clusterId) {
                $startIndex = 1; // Skip the cluster folder name
                error_log("📁 Using existing cluster: " . $clusterSlug . " (ID: " . $clusterId . ")");
            }
        }
        
        // ✅ If no cluster detected, use default (My Drive)
        if (!$clusterId) {
            $clusterId = $this->getDefaultClusterId($userId);
            $startIndex = 0;
            error_log("📁 Using default cluster: " . $clusterId);
        }
        
        // ✅ Create folder structure (only for subfolders inside clusters)
        $currentParentId = null;
        $lastFolderId = null;
        
        for ($i = $startIndex; $i < count($parts); $i++) {
            $folderName = $parts[$i];
            if (empty($folderName)) continue;
            
            // ✅ Check if folder exists
            $existingFolder = $this->folderRepository->findDuplicate($userId, $clusterId, $currentParentId, $folderName);
            if ($existingFolder) {
                $currentParentId = $existingFolder['id'];
                $lastFolderId = $currentParentId;
                error_log("📁 Folder exists: " . $folderName . " (ID: " . $currentParentId . ")");
            } else {
                $newFolderId = $this->folderRepository->create([
                    'user_id' => $userId,
                    'cluster_id' => $clusterId,
                    'parent_id' => $currentParentId,
                    'name' => $folderName
                ]);
                $currentParentId = $newFolderId;
                $lastFolderId = $newFolderId;
                error_log("📁 Created folder: " . $folderName . " (ID: " . $currentParentId . ")");
            }
        }
        
        return $lastFolderId;
    }

    /**
     * Get cluster ID by slug
     */
    private function getClusterIdBySlug(int $userId, string $slug): ?int
    {
        $pdo = $this->userRepository->getPdo();
        $stmt = $pdo->prepare("
            SELECT id FROM storage_clusters 
            WHERE user_id = ? AND slug = ?
        ");
        $stmt->execute([$userId, $slug]);
        $result = $stmt->fetch();
        return $result ? (int)$result['id'] : null;
    }

    /**
     * Get default cluster ID
     */
    private function getDefaultClusterId(int $userId): int
    {
        $pdo = $this->userRepository->getPdo();
        $stmt = $pdo->prepare("
            SELECT id FROM storage_clusters 
            WHERE user_id = ? AND is_default = 1
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result ? (int)$result['id'] : 1;
    }

    /**
     * Get file category
     */
    private function getFileCategory(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];
        $videoExtensions = ['mp4', 'webm', 'avi', 'mkv', 'mov', 'wmv', 'flv'];
        $audioExtensions = ['mp3', 'wav', 'flac', 'aac', 'ogg', 'wma'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf', 'odt', 'ods', 'odp'];
        $archiveExtensions = ['zip', 'rar', '7z', 'tar', 'gz', 'bz2'];
        $codeExtensions = ['php', 'js', 'css', 'html', 'htm', 'json', 'xml', 'sql', 'py', 'java', 'cpp', 'c', 'h', 'sh', 'bash'];
        
        if (in_array($extension, $imageExtensions)) return 'image';
        if (in_array($extension, $videoExtensions)) return 'video';
        if (in_array($extension, $audioExtensions)) return 'audio';
        if (in_array($extension, $documentExtensions)) return 'document';
        if (in_array($extension, $archiveExtensions)) return 'archive';
        if (in_array($extension, $codeExtensions)) return 'code';
        return 'other';
    }

    /**
     * Sync uploaded file to local folder
     */
    public function syncUploadedFile(int $userId, int $fileId): array
    {
        $file = $this->fileRepository->find($fileId);
        if (!$file) {
            throw new \Exception('File not found');
        }

        // Get user sync path with username
        $userSyncPath = $this->getUserSyncPath($userId);
        
        // Determine cluster
        $clusterMap = [
            'images' => 'Images',
            'videos' => 'Videos',
            'documents' => 'Documents',
            'projects' => 'Projects',
            'archives' => 'Archives',
            'my-drive' => 'My Drive'
        ];
        
        $pdo = $this->userRepository->getPdo();
        $stmt = $pdo->prepare("SELECT slug FROM storage_clusters WHERE id = ?");
        $stmt->execute([$file['cluster_id']]);
        $cluster = $stmt->fetch();
        
        $clusterName = $cluster ? $clusterMap[$cluster['slug']] ?? 'My Drive' : 'My Drive';
        $targetDir = $userSyncPath . DIRECTORY_SEPARATOR . $clusterName;
        
        // Add folder structure
        if ($file['folder_id']) {
            $folderPath = $this->getFolderPath($file['folder_id']);
            if ($folderPath) {
                $targetDir .= DIRECTORY_SEPARATOR . $folderPath;
            }
        }
        
        // Create directory if needed
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        // Target file path
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $file['original_name'];
        
        // Check if file already exists
        if (file_exists($targetPath)) {
            $info = pathinfo($file['original_name']);
            $counter = 1;
            while (file_exists($targetDir . DIRECTORY_SEPARATOR . $info['filename'] . ' (' . $counter . ').' . $info['extension'])) {
                $counter++;
            }
            $targetPath = $targetDir . DIRECTORY_SEPARATOR . $info['filename'] . ' (' . $counter . ').' . $info['extension'];
        }
        
        // Copy file from uploads to local
        $sourcePath = $this->storageService->getFullPath($file['file_path']);
        if (file_exists($sourcePath)) {
            if (copy($sourcePath, $targetPath)) {
                return [
                    'success' => true,
                    'message' => 'File synced to local folder',
                    'local_path' => $targetPath
                ];
            } else {
                throw new \Exception('Failed to copy file to local folder');
            }
        } else {
            throw new \Exception('Source file not found');
        }
    }

    /**
     * Get sync history
     */
    public function getHistory(int $userId): array
    {
        return $this->syncRepository->getHistory($userId);
    }

    /**
     * Cancel sync
     */
    public function cancelSync(int $userId): array
    {
        $activeSession = $this->syncRepository->getActiveSession($userId);
        
        if (!$activeSession) {
            throw new \Exception('No active sync session found', 404);
        }

        $this->syncRepository->updateSessionStatus($activeSession['id'], 'failed', 'Cancelled by user');
        
        return ['success' => true, 'message' => 'Sync cancelled'];
    }

    /**
     * Debug helper - log file lists
     */
    private function debugFileLists(array $localFiles, array $syncedFiles): void
    {
        error_log("=== DEBUG: File Lists ===");
        error_log("Local files (" . count($localFiles) . "):");
        foreach ($localFiles as $file) {
            error_log("  - " . $file['relative_path'] . " (" . $file['hash'] . ")");
        }
        error_log("Synced files (" . count($syncedFiles) . "):");
        foreach ($syncedFiles as $file) {
            $path = $file['local_path'] ?? 'unknown';
            error_log("  - " . $path . " (" . $file['file_hash'] . ")");
        }
    }
}