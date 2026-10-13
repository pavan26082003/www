<?php
namespace App\Services\NAS;

use App\Repositories\NAS\FileRepository;
use App\Repositories\NAS\FolderRepository;
use App\Repositories\NAS\ClusterRepository;
use App\Repositories\NAS\ShareRepository;
use App\Repositories\NAS\StarredRepository;
use App\Repositories\NAS\UserRepository;
use App\Repositories\NAS\SyncRepository;
use App\Models\NAS\File;
use App\Helpers\NAS\FileHelper;
use App\Config\NAS\Application as NASApplication;

class FileService
{
    private FileRepository $fileRepository;
    private FolderRepository $folderRepository;
    private ClusterRepository $clusterRepository;
    private ShareRepository $shareRepository;
    private StarredRepository $starredRepository;
    private UserRepository $userRepository;
    private SyncRepository $syncRepository;
    private StorageService $storageService;
    private PermissionService $permissionService;
    private string $uploadPath;

    /** @var string[] */
    private array $allowedExtensions = [];
    private int $maxFileSize = 104857600; // fallback: 100MB


    public function __construct(
        FileRepository $fileRepository,
        FolderRepository $folderRepository,
        ClusterRepository $clusterRepository,
        ShareRepository $shareRepository,
        StarredRepository $starredRepository,
        UserRepository $userRepository,
        SyncRepository $syncRepository,
        StorageService $storageService,
        PermissionService $permissionService
    ) {
        $this->fileRepository = $fileRepository;
        $this->folderRepository = $folderRepository;
        $this->clusterRepository = $clusterRepository;
        $this->shareRepository = $shareRepository;
        $this->starredRepository = $starredRepository;
        $this->userRepository = $userRepository;
        $this->syncRepository = $syncRepository;
        $this->storageService = $storageService;
        $this->permissionService = $permissionService;
        $this->uploadPath = $this->storageService->getUploadRoot();

        // Read upload rules from app/Config/NAS/Application.php.
        // This avoids having a second hardcoded extension list in FileService.
        $configuredExtensions = NASApplication::get('allowed_extensions', []);
        if (!is_array($configuredExtensions)) {
            $configuredExtensions = [];
        }

        $this->allowedExtensions = array_values(array_unique(array_filter(array_map(
            static function ($extension): string {
                return strtolower(ltrim(trim((string) $extension), '.'));
            },
            $configuredExtensions
        ))));

        $configuredMaxFileSize = (int) NASApplication::get('max_file_size', 104857600);
        $this->maxFileSize = $configuredMaxFileSize > 0
            ? $configuredMaxFileSize
            : 104857600;
    }

   
    /**
     * Get files by view type
     */
    public function getFilesByView(int $userId, string $view, ?int $folderId = null, ?string $search = null): array
    {
        /*
        |--------------------------------------------------------------------------
        | Folder navigation
        |--------------------------------------------------------------------------
        |
        | Keep the originating view while opening a folder.
        |
        | - Shared: load the actual folder owner's children and apply the
        |   recipient's inherited/shared permission.
        | - Starred: keep the current user's star state while browsing.
        | - Other views: use the normal folder-content loader.
        |
        */
        if ($folderId !== null) {
            if ($view === 'shared') {
                return $this->getSharedFolderContents($userId, $folderId);
            }

            $items = $this->getFolderContents($userId, $folderId);

            if ($view === 'starred') {
                foreach ($items as &$item) {
                    $item['is_starred'] = $this->starredRepository->isStarred(
                        $userId,
                        $item['type'],
                        $item['id']
                    );
                }
                unset($item);
            }

            return $items;
        }

        // No folder_id: load the selected root/special view.
        switch ($view) {
            case 'trash':
                return $this->getTrashItems($userId, $search);

            case 'shared':
                return $this->getSharedItems($userId, $search);

            case 'starred':
                return $this->getStarredItems($userId, $search);

            case 'computers':
                return $this->getComputersItems($userId, $search);

            case 'my-drive':
            case 'drive':
            case 'home':
                return $this->getMyDriveItems($userId, null, $search);

            default:
                return $this->getClusterItems($userId, $view, null, $search);
        }
    }
    /**
     * Get Computers cluster items - virtual view showing ALL files
     */
    private function getComputersItems(int $userId, ?string $search = null): array
    {
        /*
         * Computers is a virtual view of the user's owned drive tree.
         * We intentionally reuse the same folder/file IDs instead of duplicating
         * database rows. Therefore an item is visible in both My Drive and
         * Computers, and opening a folder preserves the real hierarchy.
         */
        $folders = $this->folderRepository->getMyDriveRootFolders($userId);
        $files = $this->fileRepository->getMyDriveRootFiles($userId, null);
        $items = array_merge($folders, $files);

        foreach ($items as &$item) {
            $item['is_computers_view'] = true;
            $item['is_owner'] = 1;
        }
        unset($item);

        if ($search !== null && $search !== '') {
            $searchLower = strtolower($search);
            $items = array_values(array_filter(
                $items,
                static function (array $item) use ($searchLower): bool {
                    $name = (string) ($item['display_name'] ?? $item['name'] ?? '');
                    return strpos(strtolower($name), $searchLower) !== false;
                }
            ));
        }

        return FileHelper::formatItems($items);
    }

    /**
     * Get trash items
     */
    private function getTrashItems(int $userId, ?string $search = null): array
    {
        $folders = $this->folderRepository->getTrashFolders($userId);
        $files = $this->fileRepository->getTrashFiles($userId);
        
        $items = array_merge($folders, $files);

        if ($search) {
            $searchLower = strtolower($search);
            $items = array_filter($items, function($item) use ($searchLower) {
                return strpos(strtolower($item['display_name']), $searchLower) !== false;
            });
            $items = array_values($items);
        }

        return FileHelper::formatItems($items);
    }

    /**
     * Get shared items - Hierarchical view
     */
    private function getSharedItems(int $userId, ?string $search = null): array
    {
        // Check if viewing a specific shared folder
        $folderId = isset($_GET['folder_id']) ? (int)$_GET['folder_id'] : null;
        
        if ($folderId) {
            return $this->getSharedFolderContents($userId, $folderId);
        }

        // Get all shared items
        $sharedFolders = $this->folderRepository->getSharedFolders($userId);
        $sharedFiles = $this->fileRepository->getSharedFiles($userId);
        
        // Build hierarchical structure
        $items = [];
        $folderMap = [];
        
        // First, organize folders by ID
        foreach ($sharedFolders as $folder) {
            $folder['is_folder'] = true;
            $folder['children'] = [];
            $folder['is_reshare'] = false;
            $folder['original_owner'] = $folder['shared_by'] ?? 'Unknown';
            $folderMap[$folder['id']] = $folder;
        }
        
        // Build tree: only show root folders (parent_id is null or parent not shared)
        foreach ($folderMap as $id => $folder) {
            $parentId = $folder['parent_id'] ?? null;
            
            // Check if parent is also shared with user
            if ($parentId && isset($folderMap[$parentId])) {
                // Parent is shared, add as child
                $folderMap[$parentId]['children'][] = $folder;
            } else {
                // Root level folder
                $items[] = $folder;
            }
        }
        
        // Add shared files that are not inside shared folders
        foreach ($sharedFiles as $file) {
            $folderId = $file['folder_id'] ?? null;
            
            // Check if file is inside a shared folder
            $isInsideSharedFolder = false;
            if ($folderId && isset($folderMap[$folderId])) {
                $isInsideSharedFolder = true;
                // Add file as child of the folder
                $folderMap[$folderId]['children'][] = $file;
            }
            
            // If not inside a shared folder, add as root item
            if (!$isInsideSharedFolder) {
                $items[] = $file;
            }
        }
        
        // Flatten the tree for rendering
        $flattenedItems = [];
        $this->flattenSharedTree($items, $flattenedItems, 0);
        
        // Check starred status and set permissions
        foreach ($flattenedItems as &$item) {
            $isStarred = $this->starredRepository->isStarred(
                $userId, 
                $item['type'], 
                $item['id']
            );
            $item['is_starred'] = $isStarred;
            $item['is_shared'] = true;
            $item['is_owner'] = false;
            
            // ✅ Always show the original owner as the sharer
            // The original owner is stored in 'shared_by' field from the repository
            // This ensures even reshared items show the original owner
        }

        // Apply search filter
        if ($search) {
            $searchLower = strtolower($search);
            $flattenedItems = array_filter($flattenedItems, function($item) use ($searchLower) {
                return strpos(strtolower($item['display_name']), $searchLower) !== false;
            });
            $flattenedItems = array_values($flattenedItems);
        }

        return FileHelper::formatItems($flattenedItems);
    }

    /**
     * Flatten shared tree for display
     */
    private function flattenSharedTree(array $items, array &$result, int $depth = 0): void
    {
        foreach ($items as $item) {
            $item['depth'] = $depth;
            $result[] = $item;
            
            // If folder has children, flatten them with increased depth
            if (isset($item['children']) && !empty($item['children'])) {
                $this->flattenSharedTree($item['children'], $result, $depth + 1);
            }
        }
    }

    /**
     * Get shared folder contents
     */
    private function getSharedFolderContents(int $userId, int $folderId): array
    {
        $folder = $this->folderRepository->find($folderId);
        if (!$folder) {
            return [];
        }

        if (!$this->permissionService->canView($userId, 'folder', $folderId)) {
            return [];
        }

        $ownerId = $folder['user_id'];
        $permission = $this->permissionService->getPermission($userId, 'folder', $folderId);

        $subfolders = $this->folderRepository->getSubfolders($folderId, $ownerId);
        $files = $this->fileRepository->getFilesByFolderId($folderId, $ownerId);

        $items = array_merge($subfolders, $files);

        foreach ($items as &$item) {
            $item['permission'] = $permission;
            $item['can_download'] = in_array(
                $permission,
                ['download', 'edit', 'share', 'full'],
                true
            );
            $item['is_shared'] = true;
            $item['is_owner'] = false;
            $item['is_starred'] = $this->starredRepository->isStarred(
                $userId,
                $item['type'],
                $item['id']
            );
        }
        unset($item);

        return FileHelper::formatItems($items);
    }

    /**
     * Get starred items
     */
    private function getStarredItems(int $userId, ?string $search = null): array
    {
        $items = [];

        // User's own starred folders
        $starredFolders = $this->folderRepository->getStarredFolders($userId);
        foreach ($starredFolders as $folder) {
            $folder['is_owner'] = 1;
            $folder['star_type'] = 'owner';
            $items[] = $folder;
        }

        // User's own starred files
        $starredFiles = $this->fileRepository->getStarredFiles($userId);
        foreach ($starredFiles as $file) {
            $file['is_owner'] = 1;
            $file['star_type'] = 'owner';
            $items[] = $file;
        }

        // Shared folders starred by this user
        $sharedStarredFolders = $this->folderRepository->getStarredSharedFolders($userId);
        foreach ($sharedStarredFolders as $folder) {
            $exists = false;
            foreach ($items as $item) {
                if ($item['id'] == $folder['id'] && $item['type'] === 'folder') {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $items[] = $folder;
            }
        }

        // Shared files starred by this user
        $sharedStarredFiles = $this->fileRepository->getStarredSharedFiles($userId);
        foreach ($sharedStarredFiles as $file) {
            $exists = false;
            foreach ($items as $item) {
                if ($item['id'] == $file['id'] && $item['type'] === 'file') {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $items[] = $file;
            }
        }

        if ($search) {
            $searchLower = strtolower($search);
            $items = array_filter($items, function($item) use ($searchLower) {
                return strpos(strtolower($item['display_name'] ?? $item['name'] ?? ''), $searchLower) !== false;
            });
            $items = array_values($items);
        }

        return FileHelper::formatItems($items);
    }

    

    /**
     * Get My Drive items
     */
    private function getMyDriveItems(int $userId, ?int $folderId = null, ?string $search = null): array
    {
        if ($folderId) {
            return $this->getFolderContents($userId, $folderId);
        }

        $folders = $this->folderRepository->getMyDriveRootFolders($userId);
        $files = $this->fileRepository->getMyDriveRootFiles($userId, $search);
        
        $items = array_merge($folders, $files);

        if ($search) {
            $searchLower = strtolower($search);
            $items = array_filter($items, function($item) use ($searchLower) {
                return strpos(strtolower($item['display_name']), $searchLower) !== false;
            });
            $items = array_values($items);
        }

        return FileHelper::formatItems($items);
    }

    /**
     * Get cluster items - regular clusters (not Computers)
     */
    private function getClusterItems(int $userId, string $clusterSlug, ?int $folderId = null, ?string $search = null): array
{
    $cluster = $this->clusterRepository->getBySlug($userId, $clusterSlug);
    if (!$cluster) {
        $cluster = $this->clusterRepository->getDefault($userId);
    }

    if (!$cluster) {
        return [];
    }

    $clusterId = $cluster['id'];

    if ($folderId) {
        return $this->getFolderContents($userId, $folderId);
    }

    $folders = $this->folderRepository->getRootFolders($userId, $clusterId);
    $fileCondition = $this->getFileConditionForCluster($clusterSlug);
    $files = $this->fileRepository->getFilesByClusterAndFolder($userId, $clusterId, null, $search, $fileCondition);

    $items = array_merge($folders, $files);

    if ($search) {
        $searchLower = strtolower($search);
        $items = array_filter($items, function($item) use ($searchLower) {
            return strpos(strtolower($item['display_name']), $searchLower) !== false;
        });
        $items = array_values($items);
    }

    return FileHelper::formatItems($items);
}

    /**
     * Get file condition for cluster filtering
     */
    private function getFileConditionForCluster(string $clusterSlug): ?string
    {
        switch ($clusterSlug) {
            case 'projects':
                return "AND (file_type LIKE '%zip%' OR file_type LIKE '%rar%' OR file_type LIKE '%7z%' OR file_type LIKE '%tar%' OR file_type LIKE '%gz%')";
            case 'images':
                return "AND (file_type LIKE '%image%' OR file_type LIKE 'image/%')";
            case 'videos':
                return "AND (file_type LIKE '%video%' OR file_type LIKE 'video/%' OR file_type LIKE '%audio%' OR file_type LIKE 'audio/%')";
            case 'documents':
                return "AND (
                    file_type LIKE '%pdf%' OR 
                    file_type LIKE '%msword%' OR 
                    file_type LIKE '%document%' OR 
                    file_type LIKE '%text%' OR 
                    file_type LIKE '%spreadsheet%' OR 
                    file_type LIKE '%presentation%' OR
                    file_type LIKE '%vnd.openxmlformats%' OR
                    file_type LIKE '%javascript%' OR
                    file_type LIKE '%css%' OR
                    file_type LIKE '%html%' OR
                    file_type LIKE '%json%' OR
                    file_type LIKE '%xml%' OR
                    file_type LIKE '%php%' OR
                    file_type LIKE '%sql%' OR
                    file_type LIKE '%x-php%' OR
                    file_type LIKE '%octet-stream%'
                )";
            case 'archives':
                return "AND (file_type LIKE '%zip%' OR file_type LIKE '%rar%' OR file_type LIKE '%7z%' OR file_type LIKE '%tar%' OR file_type LIKE '%gz%' OR file_type LIKE 'application/x-%')";
            default:
                return null;
        }
    }

    /**
     * Get folder contents
     */
    public function getFolderContents(int $userId, int $folderId): array
    {
        $folder = $this->folderRepository->find($folderId);

        if (!$folder) {
            return [];
        }

        $ownerId = (int) $folder['user_id'];
        $isOwner = ($ownerId === $userId);

        /*
        |--------------------------------------------------------------------------
        | Access check
        |--------------------------------------------------------------------------
        |
        | A recipient can browse another user's folder only when the folder
        | share/inherited share grants view access.
        |
        */
        if (
            !$isOwner
            && !$this->permissionService->canView($userId, 'folder', $folderId)
        ) {
            return [];
        }

        /*
        |--------------------------------------------------------------------------
        | Load children by the real owner
        |--------------------------------------------------------------------------
        |
        | This is the critical shared-folder fix. Files and child folders keep
        | the owner's user_id in the database, so querying by the recipient's
        | user_id returns an empty result.
        |
        */
        $subfolders = $this->folderRepository->getSubfolders(
            $folderId,
            $ownerId
        );

        $files = $this->fileRepository->getFilesByFolderId(
            $folderId,
            $ownerId
        );

        $items = array_merge($subfolders, $files);

        /*
        |--------------------------------------------------------------------------
        | Decorate shared children with the recipient permission
        |--------------------------------------------------------------------------
        */
        if (!$isOwner) {
            $permission = $this->permissionService->getPermission(
                $userId,
                'folder',
                $folderId
            );

            foreach ($items as &$item) {
                $item['is_shared'] = true;
                $item['is_owner'] = false;
                $item['permission'] = $permission;
                $item['can_download'] = in_array(
                    $permission,
                    ['download', 'edit', 'share', 'full'],
                    true
                );
            }
            unset($item);
        }

        return FileHelper::formatItems($items);
    }
    /**
     * Upload files
     */

    public function uploadFiles(
        int $userId,
        array $files,
        ?string $clusterSlug,
        ?int $folderId = null,
        bool $isFolderUpload = false,
        array $paths = [],
        ?string $rootFolder = null
    ): array {
        $result = [
            'uploaded' => [],
            'file_ids' => [],
            'folders_created' => [],
            'errors' => [],
            'root_folder_id' => $folderId,
            'cluster_slug' => $clusterSlug,
            'cluster_id' => null
        ];

        if ($userId <= 0) {
            throw new \InvalidArgumentException('Invalid user ID');
        }

        if ($files === []) {
            throw new \InvalidArgumentException('No files supplied for upload');
        }

        if ($rootFolder !== null && $rootFolder !== '') {
            $rootFolder = $this->validateFolderName($rootFolder);
        }

        // Validate all browser-supplied relative paths before creating any
        // physical directory or database folder. This prevents a rejected
        // traversal request from leaving partial folder state behind.
        foreach ($files as $uploadEntry) {
            if (!empty($uploadEntry['path'])) {
                $this->normalizeRelativeFolderPath(
                    (string) $uploadEntry['path']
                );
            }
        }

        // Auto-detect cluster if needed
        if ($isFolderUpload) {
            if (!$clusterSlug || $clusterSlug === 'home' || $clusterSlug === 'my-drive' || $clusterSlug === 'drive') {
                $clusterSlug = 'projects';
            }
        } else {
            if (!$clusterSlug || $clusterSlug === 'home' || $clusterSlug === 'my-drive' || $clusterSlug === 'drive') {
                $file = $files[0];
                $clusterSlug = $this->detectCluster($file['type'], $file['name']);
            }
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

        if (!$cluster) {
            throw new \Exception('No cluster found for user');
        }

        $clusterId = $cluster['id'];
        $result['cluster_id'] = $clusterId;
        $result['cluster_slug'] = $cluster['slug'];

        // A target folder must belong to the logged-in user. The current
        // physical storage layout is rooted under user_<id>, so attaching an
        // upload to another user's folder would create inconsistent ownership.
        if ($folderId !== null) {
            $folder = $this->folderRepository->find($folderId);

            if (!$folder) {
                throw new \InvalidArgumentException('Target folder not found');
            }

            if ((int) $folder['user_id'] !== $userId) {
                throw new \RuntimeException(
                    'Uploading directly into another user\'s folder is not allowed'
                );
            }

            $clusterId = (int) $folder['cluster_id'];
            $folderCluster = $this->clusterRepository->find($clusterId);

            if (!$folderCluster) {
                throw new \RuntimeException('Target folder cluster not found');
            }

            $cluster = $folderCluster;
            $result['cluster_id'] = $clusterId;
            $result['cluster_slug'] = (string) $cluster['slug'];
        }

        if (
            in_array(
                (string) $cluster['slug'],
                ['shared', 'starred', 'trash', 'computers'],
                true
            )
        ) {
            throw new \InvalidArgumentException(
                'Uploads are not allowed in this virtual view'
            );
        }

        // Check storage
        $totalSize = array_sum(array_column($files, 'size'));
        $storageInfo = $this->userRepository->getStorageInfo($userId);
        $used = (int)$storageInfo['storage_used'];
        $limit = (int)$storageInfo['storage_limit'];
        $available = $limit - $used;

        if ($totalSize > $available) {
            throw new \Exception('Not enough storage available. Need ' . 
                FileHelper::formatSize($totalSize) . ', available: ' . 
                FileHelper::formatSize($available));
        }

        try {
            $clusterDir = $this->storageService->createClusterDirectory($userId, $cluster['slug']);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create directory: ' . $e->getMessage());
        }

        // Create root folder for folder upload
        $rootFolderId = $folderId;
        if ($isFolderUpload && $rootFolder) {
            // Validate/create the physical directory before mutating DB state.
            // If the server is not writable, the upload fails cleanly without
            // leaving a folder row that has no matching directory.
            $rootPath =
                $clusterDir
                . $rootFolder
                . DIRECTORY_SEPARATOR;
            $this->storageService->ensureDirectory($rootPath);

            $existing = $this->folderRepository->findDuplicate(
                $userId,
                $clusterId,
                $folderId,
                $rootFolder
            );
            
            if ($existing) {
                $rootFolderId = $existing['id'];
            } else {
                $rootFolderId = $this->folderRepository->create([
                    'user_id' => $userId,
                    'cluster_id' => $clusterId,
                    'parent_id' => $folderId,
                    'name' => $rootFolder
                ]);
                $result['folders_created'][] = $rootFolder;
            }
            $result['root_folder_id'] = $rootFolderId;
        }

        
        foreach ($files as $file) {
            try {
                if (!isset($file['name'], $file['tmp_name'], $file['size'])) {
                    throw new \InvalidArgumentException('Malformed upload entry');
                }

                $originalName = $this->validateOriginalFileName(
                    (string) $file['name']
                );
                $extension = strtolower(
                    (string) pathinfo($originalName, PATHINFO_EXTENSION)
                );

                if (
                    $extension === ''
                    || !in_array($extension, $this->allowedExtensions, true)
                ) {
                    $result['errors'][] =
                        'File "' . $originalName . '" type not allowed';
                    continue;
                }

                $fileSize = (int) $file['size'];
                if ($fileSize < 0 || $fileSize > $this->maxFileSize) {
                    $result['errors'][] =
                        'File "' . $originalName
                        . '" exceeds maximum allowed size';
                    continue;
                }

                $filename = $this->storageService
                    ->generateUniqueFilename($originalName);
                $targetFolderId = $rootFolderId ?? $folderId;
                $relativePath = isset($file['path'])
                    ? (string) $file['path']
                    : '';

                // Determine file path
                if ($isFolderUpload && $rootFolder) {
                    $filePath = $clusterDir . $rootFolder . DIRECTORY_SEPARATOR;
                } else {
                    $filePath = $clusterDir;
                }

                // Handle relative path (subfolders in folder upload)
                if (!empty($relativePath)) {
                    $pathParts =
                        $this->normalizeRelativeFolderPath($relativePath);
                    $currentParent = $rootFolderId ?? $folderId;
                    $currentPhysicalPath = $isFolderUpload
                        ? $clusterDir
                            . $rootFolder
                            . DIRECTORY_SEPARATOR
                        : $clusterDir;

                    foreach ($pathParts as $folderName) {
                        $currentPhysicalPath .=
                            $folderName
                            . DIRECTORY_SEPARATOR;
                        $this->storageService->ensureDirectory($currentPhysicalPath);

                        $existing = $this->folderRepository->findDuplicate($userId, $clusterId, $currentParent, $folderName);
                        
                        if ($existing) {
                            $currentParent = $existing['id'];
                        } else {
                            $currentParent = $this->folderRepository->create([
                                'user_id' => $userId,
                                'cluster_id' => $clusterId,
                                'parent_id' => $currentParent,
                                'name' => $folderName
                            ]);
                            $result['folders_created'][] = $folderName;
                        }
                    }

                    $targetFolderId = $currentParent;
                    $filePath = $currentPhysicalPath;
                }

                $filePath .= $filename;
                $category = FileHelper::getFileCategory($file['type']);

                // Save file
                if ($this->storageService->saveUploadedFile($file['tmp_name'], $filePath)) {
                    // Get relative path for database
                    $relativePath = str_replace($this->uploadPath, '', $filePath);
                    $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');

                    $fileHash = hash_file('sha256', $filePath);

                    // Create file model
                    $fileModel = new File([
                        'user_id' => $userId,
                        'cluster_id' => $clusterId,
                        'folder_id' => $targetFolderId,
                        'filename' => $filename,
                        'original_name' => $originalName,
                        'file_path' => $relativePath,
                        'file_size' => $fileSize,
                        'file_type' => $file['type'],
                        'file_category' => $category,
                        'file_hash' => $fileHash
                    ]);

                    $fileId = $this->fileRepository->create($fileModel);

                    $this->syncRepository->addSyncStatus(
                        $userId,
                        $fileId,
                        $relativePath,
                        $fileHash,
                        $fileSize,
                        'pending'
                    );
                    
                    $result['uploaded'][] = $originalName;
                    $result['file_ids'][] = $fileId;

                    // Update storage
                    $this->userRepository
                        ->updateStorageUsed($userId, $fileSize, 'add');
                } else {
                    $result['errors'][] = 'Failed to move file "' . $originalName . '"';
                }

            } catch (\Exception $e) {
                $result['errors'][] = 'Error uploading "' . $file['name'] . '": ' . $e->getMessage();
            }
        }

        $result['total_files'] = count($result['uploaded']);
        $result['total_folders'] = count($result['folders_created']);

        return $result;
    }

    /**
     * Validate a single folder-name segment supplied by the browser.
     */
    private function validateFolderName(string $name): string
    {
        $name = trim($name);

        if (
            $name === ''
            || $name === '.'
            || $name === '..'
            || strpos($name, "\0") !== false
            || strpos($name, '/') !== false
            || strpos($name, '\\') !== false
            || preg_match('/[\x00-\x1F\x7F]/u', $name)
        ) {
            throw new \InvalidArgumentException('Invalid folder name');
        }

        if (strlen($name) > 255) {
            throw new \InvalidArgumentException('Folder name is too long');
        }

        return $name;
    }

    /**
     * Convert a browser relative folder path into validated path segments.
     */
    private function normalizeRelativeFolderPath(string $relativePath): array
    {
        $relativePath = str_replace('\\', '/', trim($relativePath));

        if ($relativePath === '') {
            return [];
        }

        if (
            $relativePath[0] === '/'
            || preg_match('/^[A-Za-z]:\//', $relativePath)
        ) {
            throw new \InvalidArgumentException(
                'Absolute upload paths are not allowed'
            );
        }

        $segments = [];
        foreach (explode('/', trim($relativePath, '/')) as $segment) {
            if ($segment === '') {
                continue;
            }

            $segments[] = $this->validateFolderName($segment);
        }

        return $segments;
    }

    /**
     * Browser/API supplied filenames are display names only.
     */
    private function validateOriginalFileName(string $name): string
    {
        $name = trim(str_replace('\\', '/', $name));
        $name = basename($name);

        if (
            $name === ''
            || $name === '.'
            || $name === '..'
            || strpos($name, "\0") !== false
            || preg_match('/[\x00-\x1F\x7F]/u', $name)
        ) {
            throw new \InvalidArgumentException('Invalid file name');
        }

        if (strlen($name) > 255) {
            throw new \InvalidArgumentException('File name is too long');
        }

        return $name;
    }

    /**
     * Detect cluster from file
     */
    public function detectCluster(string $mimeType, string $fileName): string
    {
        $fileName = strtolower($fileName);
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Video
        $videoTypes = ['video/mp4', 'video/webm', 'video/avi', 'video/mkv', 'video/mov', 'video/wmv', 'video/flv'];
        $videoExtensions = ['mp4', 'webm', 'avi', 'mkv', 'mov', 'wmv', 'flv', 'm4v'];
        if (in_array($mimeType, $videoTypes) || in_array($extension, $videoExtensions)) {
            return 'videos';
        }

        // Audio
        $audioTypes = ['audio/mpeg', 'audio/wav', 'audio/flac', 'audio/aac', 'audio/ogg'];
        $audioExtensions = ['mp3', 'wav', 'flac', 'aac', 'ogg', 'wma'];
        if (in_array($mimeType, $audioTypes) || in_array($extension, $audioExtensions)) {
            return 'audio';
        }

        // Images
        $imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        if (in_array($mimeType, $imageTypes) || in_array($extension, $imageExtensions)) {
            return 'images';
        }

        // Archives
        $archiveTypes = ['application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed'];
        $archiveExtensions = ['zip', 'rar', '7z', 'tar', 'gz'];
        if (in_array($mimeType, $archiveTypes) || in_array($extension, $archiveExtensions)) {
            return 'archives';
        }

        // Code files
        $codeExtensions = ['php', 'js', 'css', 'html', 'htm', 'json', 'xml', 'py', 'java', 'cpp', 'c', 'h', 'sql', 'sh'];
        if (in_array($extension, $codeExtensions)) {
            return 'documents';
        }

        // Documents
        $docTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $docExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
        if (in_array($mimeType, $docTypes) || in_array($extension, $docExtensions)) {
            return 'documents';
        }

        return 'my-drive';
    }

    /**
     * Update file
     */
    public function updateFile(int $userId, int $fileId, string $action, array $data): array
    {
        $file = $this->fileRepository->findWithTrash($fileId);
        
        if (!$file) {
            throw new \Exception('File not found', 404);
        }

        $isOwner = ($file['user_id'] == $userId);
        $isShared = false;
        
        if (!$isOwner) {
            $share = $this->shareRepository->findForUserAndItem($userId, 'file', $fileId);
            if ($share) {
                $isShared = true;
            } else {
                throw new \Exception('Access denied', 403);
            }
        }

        switch ($action) {
            case 'rename':
                return $this->renameFile($userId, $file, $data['new_name'] ?? '', $isOwner);
            
            case 'star':
                return $this->toggleStar($userId, $file, $isOwner);
            
            case 'restore':
                return $this->restoreFile($userId, $file);
            
            case 'open':
                return $this->markOpened($file);
            
            default:
                throw new \Exception('Invalid action', 400);
        }
    }

    /**
     * Rename file
     */
    private function renameFile(int $userId, array $file, string $newName, bool $isOwner): array
    {
        if (!$isOwner) {
            throw new \Exception('Only the owner can rename this file', 403);
        }

        if (empty($newName)) {
            throw new \Exception('New name required', 400);
        }

        $this->fileRepository->update($file['id'], ['original_name' => $newName]);
        return ['success' => true, 'message' => 'File renamed successfully'];
    }

    /**
     * Toggle star
     */
    private function toggleStar(int $userId, array $file, bool $isOwner): array
    {
        if ($isOwner) {
            $newStatus = $file['is_starred'] ? 0 : 1;
            $this->fileRepository->update($file['id'], ['is_starred' => $newStatus]);
            return [
                'success' => true,
                'message' => $newStatus ? 'File starred' : 'File unstarred',
                'is_starred' => (bool)$newStatus
            ];
        } else {
            $isStarred = $this->starredRepository->isStarred($userId, 'file', $file['id']);
            
            if ($isStarred) {
                $this->starredRepository->unstar($userId, 'file', $file['id']);
                return [
                    'success' => true,
                    'message' => 'File unstarred',
                    'is_starred' => false
                ];
            } else {
                $this->starredRepository->star($userId, 'file', $file['id']);
                return [
                    'success' => true,
                    'message' => 'File starred',
                    'is_starred' => true
                ];
            }
        }
    }

    /**
     * Restore file
     */
    public function restoreFile(int $userId, array $file): array
    {
        if ($file['user_id'] != $userId) {
            throw new \Exception('Only the owner can restore this file', 403);
        }

        if ($file['folder_id']) {
            $folder = $this->folderRepository->find($file['folder_id']);
            if (!$folder) {
                throw new \Exception('Parent folder is in trash. Please restore the folder first.', 400);
            }
        }

        $this->fileRepository->restore($file['id']);
        return ['success' => true, 'message' => 'File restored successfully'];
    }

    /**
     * Mark as opened
     */
    private function markOpened(array $file): array
    {
        $this->fileRepository->updateLastOpened($file['id']);
        return ['success' => true];
    }

    /**
     * Delete file
     */
    /**
     * Delete file
     */
    public function deleteFile(int $userId, int $fileId, bool $permanent = false): array
    {
        $file = $this->fileRepository->findWithTrash($fileId);
        
        if (!$file) {
            throw new \Exception('File not found', 404);
        }

        $isOwner = ($file['user_id'] == $userId);

        if ($isOwner) {
            // ✅ OWNER: Permanently delete or move to trash for everyone
            if ($permanent || $file['deleted_at'] !== null) {
                // Permanent delete - remove from database and disk
                $this->storageService->deleteFile($file['file_path']);
                $this->fileRepository->forceDelete($fileId);
                $this->userRepository->updateStorageUsed($userId, $file['file_size'], 'subtract');
                
                return ['success' => true, 'message' => 'File permanently deleted'];
            } else {
                // Owner moves to trash - this affects everyone
                $this->fileRepository->softDelete($fileId);
                return ['success' => true, 'message' => 'File moved to trash'];
            }
        } else {
            // ✅ SHARED USER: Only remove the share for this user
            // Check if user has access to this file
            $share = $this->shareRepository->findForUserAndItem($userId, 'file', $fileId);
            if (!$share) {
                throw new \Exception('You do not have access to this file', 403);
            }
            
            // Remove the share for this user only
            $this->shareRepository->deleteForItem($fileId, 'file', $userId);
            
            // Also remove from starred items for this user
            $this->starredRepository->deleteForItem($fileId, 'file', $userId);
            
            return ['success' => true, 'message' => 'File removed from your shared items'];
        }
    }

    /**
     * Download file
     */
    public function downloadFile(int $userId, int $fileId): array
    {
        $file = $this->fileRepository->find($fileId);
        
        if (!$file) {
            throw new \Exception('File not found', 404);
        }

        if (!$this->permissionService->canDownload($userId, 'file', $fileId)) {
            throw new \Exception('Download permission denied', 403);
        }

        $this->fileRepository->updateLastOpened($fileId);

        $fullPath = $this->storageService->getFullPath($file['file_path']);
        if (!file_exists($fullPath)) {
            throw new \Exception('File not found on server', 404);
        }

        return [
            'path' => $fullPath,
            'name' => $file['original_name'],
            'size' => $file['file_size'],
            'mime_type' => $this->storageService->getMimeType($file['file_path'])
        ];
    }

    /**
     * Preview file
     */
    public function previewFile(int $userId, int $fileId): array
    {
        $file = $this->fileRepository->find($fileId);
        
        if (!$file) {
            throw new \Exception('File not found', 404);
        }

        if (!$this->permissionService->canView($userId, 'file', $fileId)) {
            throw new \Exception('Access denied', 403);
        }

        $fullPath = $this->storageService->getFullPath($file['file_path']);
        if (!file_exists($fullPath)) {
            throw new \Exception('File not found on server', 404);
        }

        $extension = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
        $mimeType = $this->storageService->getMimeType($file['file_path']);

        // Force text/plain for code files
        $codeExtensions = ['php', 'js', 'css', 'html', 'htm', 'json', 'xml', 'txt', 'py', 'java', 'cpp', 'c', 'h', 'sql', 'sh'];
        if (in_array($extension, $codeExtensions)) {
            $mimeType = 'text/plain';
        }

        return [
            'path' => $fullPath,
            'name' => $file['original_name'],
            'mime_type' => $mimeType
        ];
    }

    /**
 * Get file info
 */
public function getFileInfo(int $userId, int $fileId): array
{
    $file = $this->fileRepository->find($fileId);
    
    if (!$file) {
        throw new \Exception('File not found', 404);
    }

    if (!$this->permissionService->canView($userId, 'file', $fileId)) {
        throw new \Exception('Access denied', 403);
    }

    $isOwner = ($file['user_id'] == $userId);
    $permission = $this->permissionService->getPermission($userId, 'file', $fileId);
    
    // ✅ If permission is 'none' but user can view, treat as 'view'
    if ($permission === 'none' && $this->permissionService->canView($userId, 'file', $fileId)) {
        $permission = 'view';
    }
    
    $share = $this->shareRepository->findForUserAndItem($userId, 'file', $fileId);
    $sharedBy = $share ? $share['user_id'] : null;
    $isDirectShare = (bool)$share;

    $folderName = null;
    if ($file['folder_id']) {
        $folder = $this->folderRepository->find($file['folder_id']);
        if ($folder) {
            $folderName = $folder['name'];
        }
    }

    list($canPreview, $previewType) = FileHelper::isPreviewable($file);

    return [
        'id' => (int)$file['id'],
        'name' => $file['original_name'],
        'original_name' => $file['original_name'],
        'filename' => $file['filename'],
        'size' => (int)$file['file_size'],
        'size_formatted' => FileHelper::formatSize($file['file_size']),
        'type' => $file['file_type'],
        'path' => $file['file_path'],
        'folder_id' => $file['folder_id'] ? (int)$file['folder_id'] : null,
        'folder_name' => $folderName,
        'is_starred' => (bool)$file['is_starred'],
        'created_at' => $file['created_at'],
        'can_preview' => $canPreview,
        'preview_type' => $previewType,
        'is_owner' => $isOwner,
        'is_direct_share' => $isDirectShare,
        'can_download' => $isOwner || in_array($permission, ['download', 'edit', 'share', 'full']),
        'permission' => $permission,
        'shared_by' => $sharedBy
    ];
}

    /**
     * Get content preview (NO AUTH)
     */
    public function getContentPreview(int $fileId): array
    {
        $file = $this->fileRepository->find($fileId);
        
        if (!$file) {
            throw new \Exception('File not found', 404);
        }

        $extension = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
        $codeExtensions = ['js', 'css', 'html', 'htm', 'json', 'xml', 'txt', 'py', 'java', 'cpp', 'c', 'h', 'sql', 'sh', 'php'];
        
        if (!in_array($extension, $codeExtensions)) {
            throw new \Exception('Content preview not available for this file type', 400);
        }

        $content = $this->storageService->getFileContent($file['file_path']);
        
        return [
            'content' => $content,
            'name' => $file['original_name']
        ];
    }

    /**
     * Download folder as zip
     */
    public function downloadFolderAsZip(int $userId, int $folderId): array
    {
        $folder = $this->folderRepository->find($folderId);
        if (!$folder) {
            throw new \Exception('Folder not found', 404);
        }

        if ($folder['user_id'] != $userId) {
            if (!$this->permissionService->canDownload($userId, 'folder', $folderId)) {
                throw new \Exception('Download permission denied', 403);
            }
        }

        $files = $this->getAllFilesInFolder($folderId, $folder['user_id']);
        
        if (empty($files)) {
            throw new \Exception('Folder is empty No files to zip.', 400);
        }

        $zipPath = $this->storageService->createZip($files, 'folder_' . $folderId);
        
        return [
            'path' => $zipPath,
            'name' => $folder['name'] . '.zip',
            'is_temp' => true,
            'mime_type' => 'application/zip',
            'size' => filesize($zipPath)
        ];
    }

    /**
     * Get all files in folder recursively
     */
    private function getAllFilesInFolder(int $folderId, int $userId, string $basePath = ''): array
    {
        $files = [];
        
        $folderFiles = $this->fileRepository->getFilesByFolderId($folderId, $userId);
        
        foreach ($folderFiles as $file) {
            $fileData = $this->fileRepository->find($file['id']);
            if ($fileData) {
                $files[] = [
                    'relative_path' => $fileData['file_path'],
                    'name' => ($basePath ? $basePath . '/' : '') . $fileData['original_name']
                ];
            }
        }
        
        $subfolders = $this->folderRepository->getSubfolders($folderId, $userId);
        
        foreach ($subfolders as $subfolder) {
            $subPath = ($basePath ? $basePath . '/' : '') . $subfolder['display_name'];
            $subFiles = $this->getAllFilesInFolder($subfolder['id'], $userId, $subPath);
            $files = array_merge($files, $subFiles);
        }
        
        return $files;
    }
}
