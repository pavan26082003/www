<?php
namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\FileService;
use App\Services\StorageService;
use App\Services\PermissionService;
use App\Services\ClusterService;
use App\Repositories\FileRepository;
use App\Repositories\FolderRepository;
use App\Repositories\ClusterRepository;
use App\Repositories\ShareRepository;
use App\Repositories\StarredRepository;
use App\Repositories\SyncRepository;
use App\Repositories\UserRepository;

class FileController extends BaseController
{
    private FileService $fileService;
    private StorageService $storageService;
    private PermissionService $permissionService;
    private ClusterService $clusterService;
    private FolderRepository $folderRepository;
    private FileRepository $fileRepository;
    private StarredRepository $starredRepository;

    public function __construct()
    {
        // Create dependencies
        $userRepository = new UserRepository();
        $fileRepository = new FileRepository();
        $folderRepository = new FolderRepository();
        $clusterRepository = new ClusterRepository();
        $shareRepository = new ShareRepository();
        $starredRepository = new StarredRepository();
        $syncRepository = new SyncRepository();
        
        $this->fileRepository = $fileRepository;
        $this->folderRepository = $folderRepository;
        $this->storageService = new StorageService();
        $this->permissionService = new PermissionService(
            $fileRepository,
            $folderRepository,
            $shareRepository
        );
        $this->clusterService = new ClusterService($clusterRepository);
        
        $this->fileService = new FileService(
            $fileRepository,
            $folderRepository,
            $clusterRepository,
            $shareRepository,
            $starredRepository,
            $userRepository,
            $syncRepository,
            $this->storageService,
            $this->permissionService
        );
    }

    /**
     * GET - List Files
     * Handles: My Drive, Clusters, Trash, Shared, Starred
     * Route: /api/v1/files
     */
    public function index(): void
    {
        try {
            $userId = $this->getUserId();
            $view = isset($_GET['view']) ? $_GET['view'] : 'my-drive';
            $folderId = isset($_GET['folder_id']) && $_GET['folder_id'] !== '' ? (int)$_GET['folder_id'] : null;
            $search = isset($_GET['search']) ? trim($_GET['search']) : null;

            if ($folderId !== null) {
                // Get folder contents (this handles subfolders and files)
                $items = $this->fileService->getFolderContents($userId, $folderId);
                
                // Add starred status for items if in starred view
                if ($view === 'starred') {
                    foreach ($items as &$item) {
                        $isStarred = $this->starredRepository->isStarred(
                            $userId,
                            $item['type'],
                            $item['id']
                        );
                        $item['is_starred'] = $isStarred;
                    }
                }
            } else {
                // No folder_id, get items based on view
                $items = $this->fileService->getFilesByView($userId, $view, $folderId, $search);
            }

            

            $userStorage = $this->getUserStorageInfo($userId);
            $items = $this->fileService->getFilesByView($userId, $view, $folderId, $search);
            
            $response = [
                'success' => true,
                'cluster_id' => null,
                'cluster_slug' => $view,
                'items' => $items,
                'storage' => $userStorage
            ];

            $this->jsonResponse($response);

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * POST - Upload Files
     * Route: /api/v1/files
     */
    public function store(): void
    {
        try {
            $userId = $this->getUserId();
            
            // Check if files were uploaded
            if (!isset($_FILES) || empty($_FILES)) {
                $this->error('No files uploaded', 400);
                return;
            }

            // Get parameters
            $clusterSlug = isset($_POST['cluster']) ? $_POST['cluster'] : null;
            $folderId = isset($_POST['folder_id']) && $_POST['folder_id'] !== '' && $_POST['folder_id'] !== 'null' 
                ? (int)$_POST['folder_id'] 
                : null;
            $paths = isset($_POST['paths']) ? $_POST['paths'] : [];
            $rootFolder = isset($_POST['root_folder']) ? $_POST['root_folder'] : null;
            $isFolderUpload = !empty($rootFolder);

            // Process uploaded files
            $files = $this->processUploadedFiles($_FILES);
            
            if (empty($files)) {
                $this->error('No valid files to upload', 400);
                return;
            }

            // Auto-detect cluster if not specified or is 'home'/'my-drive'
            if (!$clusterSlug || $clusterSlug === 'home' || $clusterSlug === 'my-drive' || $clusterSlug === 'drive') {
                if ($isFolderUpload) {
                    $clusterSlug = 'projects';
                } else {
                    $file = $files[0];
                    $clusterSlug = $this->fileService->detectCluster($file['type'], $file['name']);
                }
            }

            $result = $this->fileService->uploadFiles(
                $userId,
                $files,
                $clusterSlug,
                $folderId,
                $isFolderUpload,
                $paths,
                $rootFolder
            );

            if (count($result['uploaded']) > 0) {
                $message = count($result['uploaded']) . ' file(s) uploaded successfully to "' . 
                           $result['cluster_slug'] . '" cluster';
                if (count($result['folders_created']) > 0) {
                    $message .= ', ' . count($result['folders_created']) . ' folder(s) created';
                }

                $this->success([
                    'uploaded' => $result['uploaded'],
                    'uploaded_files' => $result['file_ids'],
                    'folders_created' => $result['folders_created'],
                    'root_folder_id' => $result['root_folder_id'],
                    'cluster_id' => $result['cluster_id'],
                    'cluster_slug' => $result['cluster_slug'],
                    'total_files' => $result['total_files'],
                    'total_folders' => $result['total_folders'],
                    'errors' => $result['errors']
                ], $message);
            } else {
                $this->error('No files were uploaded successfully', 400, ['errors' => $result['errors']]);
            }

        } catch (\Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }

     /**
     * Helper: Process uploaded files from $_FILES
     */
    private function processUploadedFiles($files): array
    {
        $processed = [];
        
        if (isset($files['files'])) {
            if (is_array($files['files']['name'])) {
                // Multiple files
                for ($i = 0; $i < count($files['files']['name']); $i++) {
                    if ($files['files']['error'][$i] === UPLOAD_ERR_OK) {
                        $processed[] = [
                            'name' => $files['files']['name'][$i],
                            'type' => $files['files']['type'][$i],
                            'tmp_name' => $files['files']['tmp_name'][$i],
                            'error' => $files['files']['error'][$i],
                            'size' => $files['files']['size'][$i],
                            'path' => isset($files['files']['path'][$i]) ? $files['files']['path'][$i] : ''
                        ];
                    }
                }
            } else {
                // Single file
                if ($files['files']['error'] === UPLOAD_ERR_OK) {
                    $processed[] = [
                        'name' => $files['files']['name'],
                        'type' => $files['files']['type'],
                        'tmp_name' => $files['files']['tmp_name'],
                        'error' => $files['files']['error'],
                        'size' => $files['files']['size'],
                        'path' => ''
                    ];
                }
            }
        } elseif (isset($files['file'])) {
            if ($files['file']['error'] === UPLOAD_ERR_OK) {
                $processed[] = [
                    'name' => $files['file']['name'],
                    'type' => $files['file']['type'],
                    'tmp_name' => $files['file']['tmp_name'],
                    'error' => $files['file']['error'],
                    'size' => $files['file']['size'],
                    'path' => ''
                ];
            }
        }

        return $processed;
    }

    /**
     * PUT - Update File
     * Route: /api/v1/files/{id}
     */
    public function update(int $fileId): void
    {
        try {
            $userId = $this->getUserId();
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                $this->error('Invalid JSON data', 400);
                return;
            }

            $action = isset($input['action']) ? $input['action'] : 'rename';
            $newName = trim($input['new_name'] ?? '');

            $result = $this->fileService->updateFile($userId, $fileId, $action, $input);
            $this->success($result, $result['message']);

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    
    /**
     * DELETE - Delete File
     * Route: /api/v1/files/{id}
     */
    public function destroy(int $fileId): void
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


            $result = $this->fileService->deleteFile($userId, $fileId, $permanent);
            $this->success($result, $result['message']);

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }


    /**
     * GET - Download File
     * Route: /api/v1/files/{id}/download
     * Route: /api/v1/files/{id}/preview
     */
    public function download(int $fileId): void
    {
        try {
            $userId = $this->getUserId();
            
            // Check if it's a preview request
            $isPreview = isset($_GET['preview']) && $_GET['preview'] == 1;
            
            // Also check the URI for /preview
            $uri = $_SERVER['REQUEST_URI'] ?? '';
            if (strpos($uri, '/preview') !== false) {
                $isPreview = true;
            }

            if ($isPreview) {
                $result = $this->fileService->previewFile($userId, $fileId);
                $this->sendPreview($result);
            } else {
                $result = $this->fileService->downloadFile($userId, $fileId);
                $this->sendDownload($result);
            }

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 404);
        }
    }

    /**
     * Send file as preview (inline)
     */
    protected function sendPreview(array $result): void
    {
        $mimeType = $result['mime_type'] ?? 'text/plain';
        $name = $result['name'] ?? 'preview';
        $path = $result['path'];

        if (!file_exists($path)) {
            throw new \Exception('File not found', 404);
        }

        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }

        if (!headers_sent()) {
            header('Content-Type: ' . $mimeType);
            header('Content-Disposition: inline; filename="' . $name . '"');
            header('Cache-Control: public, max-age=3600');
            header('X-Content-Type-Options: nosniff');
            header('Access-Control-Allow-Origin: *');
        }
        
        readfile($path);
        exit;
    }

    /**
     * Send file as download (attachment)
     */
    protected function sendDownload(array $result): void
    {
        $mimeType = $result['mime_type'] ?? 'application/octet-stream';
        $name = $result['name'] ?? 'download';
        $path = $result['path'];
        $size = $result['size'] ?? 0;

        if (!file_exists($path)) {
            throw new \Exception('File not found', 404);
        }

        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }

        if (!headers_sent()) {
            header('Content-Type: ' . $mimeType);
            header('Content-Disposition: attachment; filename="' . $name . '"');
            header('Content-Length: ' . filesize($path));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
        }
        
        // Clean up temp file if it's a ZIP
        if (isset($result['is_temp']) && $result['is_temp'] && file_exists($path)) {
            readfile($path);
            unlink($path);
        } else {
            readfile($path);
        }
        exit;
    }

    /**
     * GET - File Info
     * Route: /api/v1/files/{id}/info
     */
    public function info(int $fileId): void
    {
        try {
            $userId = $this->getUserId();
            $info = $this->fileService->getFileInfo($userId, $fileId);
            $this->success(['file' => $info], 'File info retrieved');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 404);
        }
    }

    /**
     * GET - Content Preview (NO AUTH)
     * Route: /api/v1/files/{id}/content
     */
    public function contentPreview(int $fileId): void
    {
        try {
            $result = $this->fileService->getContentPreview($fileId);
            
            header('Content-Type: text/plain');
            header('Content-Disposition: inline; filename="' . $result['name'] . '"');
            header('Cache-Control: public, max-age=3600');
            header('X-Content-Type-Options: nosniff');
            header('Access-Control-Allow-Origin: *');
            
            echo $result['content'];
            exit;

        } catch (\Exception $e) {
            http_response_code(404);
            echo 'Error: ' . $e->getMessage();
            exit;
        }
    }

    /**
     * PUT - Restore File
     * Route: /api/v1/files/{id}/restore
     */
    public function restore(int $fileId): void
    {
        try {
            $userId = $this->getUserId();
            
            // ✅ Find the file including trash
            $file = $this->fileRepository->findWithTrash($fileId);
            
            if (!$file) {
                $this->error('File not found', 404);
                return;
            }
            
            // Check if user is the owner
            if ($file['user_id'] != $userId) {
                $this->error('Only the owner can restore this file', 403);
                return;
            }
            
            // Check if file is actually in trash
            if ($file['deleted_at'] === null) {
                $this->error('File is not in trash', 400);
                return;
            }
            
            // Check if parent folder exists and is not in trash
            if ($file['folder_id']) {
                $folder = $this->folderRepository->find($file['folder_id']);
                if (!$folder) {
                    $this->error('Parent folder is in trash. Please restore the folder first.', 400);
                    return;
                }
            }
            
            $this->fileRepository->restore($fileId);
            $this->success(null, 'File restored successfully');
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * GET - Download Folder as ZIP
     * Route: /api/v1/files/download-folder
     */
    public function downloadFolder(): void
    {
        try {
            $userId = $this->getUserId();
            $folderId = isset($_GET['folder_id']) ? (int)$_GET['folder_id'] : 0;

            if (!$folderId) {
                $this->error('Folder ID required', 400);
                return;
            }

            $folder = $this->folderRepository->find($folderId);
            if (!$folder) {
                // Check if it's a file
                $file = $this->fileRepository->find($folderId);
                if ($file) {
                    // It's a file, redirect to file download
                    $this->error('This is a file, not a folder. Please use the file download.', 400);
                    return;
                }
                $this->error('Folder not found', 404);
                return;
            }

            if ($folder['user_id'] != $userId) {
                if (!$this->permissionService->canDownload($userId, 'folder', $folderId)) {
                    $this->error('Download permission denied', 403);
                    return;
                }
            }
            $result = $this->fileService->downloadFolderAsZip($userId, $folderId);
            $this->sendDownload($result);

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 404);
        }
    }
}