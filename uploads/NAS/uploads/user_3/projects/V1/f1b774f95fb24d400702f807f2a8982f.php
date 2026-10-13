<?php
namespace App\Controllers\NAS\Api\V1;

use App\Controllers\NAS\BaseController;
use App\Services\NAS\OfficeConversionService;
use App\Services\NAS\FileService;
use App\Services\NAS\PermissionService;
use App\Services\NAS\StorageService;
use App\Repositories\NAS\FileRepository;
use App\Repositories\NAS\FolderRepository;
use App\Repositories\NAS\ClusterRepository;
use App\Repositories\NAS\ShareRepository;
use App\Repositories\NAS\StarredRepository;
use App\Repositories\NAS\UserRepository;
use App\Repositories\NAS\SyncRepository;

class PreviewController extends BaseController
{
    private OfficeConversionService $conversionService;  // ✅ Property declared
    private FileService $fileService;
    private PermissionService $permissionService;
    private StorageService $storageService;

    public function __construct()
    {
        // ✅ Create all required dependencies
        $fileRepository = new FileRepository();
        $folderRepository = new FolderRepository();
        $clusterRepository = new ClusterRepository();
        $shareRepository = new ShareRepository();
        $starredRepository = new StarredRepository();
        $userRepository = new UserRepository();
        $syncRepository = new SyncRepository();
        $this->storageService = new StorageService();
        
        $this->permissionService = new PermissionService(
            $fileRepository,
            $folderRepository,
            $shareRepository
        );
        
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
        
        // ✅ CREATE THE CONVERSION SERVICE
        $this->conversionService = new OfficeConversionService(
            $this->storageService,
            $this->fileService
        );
    }

    public function convert(): void
    {
        try {
            
            $userId = $this->getUserId();
            $fileId = isset($_GET['file_id']) ? (int)$_GET['file_id'] : 0;


            if (!$fileId) {
                $this->error('File ID required', 400);
                return;
            }

            // ✅ Check permission and get permission level
            $canView = $this->permissionService->canView($userId, 'file', $fileId);
            $canDownload = $this->permissionService->canDownload($userId, 'file', $fileId);
            

            if (!$canView) {
                $this->error('Access denied', 403);
                return;
            }

            // Get file info
            $fileInfo = $this->fileService->getFileInfo($userId, $fileId);
            
            $extension = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));

            // ✅ For PDF files
            if ($extension === 'pdf') {
                $result = $this->fileService->previewFile($userId, $fileId);
                
                // ✅ Check if view-only - disable toolbar
                if (!$canDownload) {
                    $this->sendPdfViewOnly($result);
                } else {
                    $this->sendPdfWithToolbar($result);
                }
                return;
            }

            // ✅ For Office files (PPT, DOC, etc.)
            if (in_array($extension, ['ppt', 'pptx', 'doc', 'docx'])) {
                $result = $this->conversionService->convertToPdf($fileId, $userId);
                
                if ($result['success']) {
                    // ✅ Check if view-only - disable toolbar
                    if (!$canDownload) {
                        $this->sendPdfViewOnly($result);
                    } else {
                        $this->sendPdfWithToolbar($result);
                    }
                    return;
                } else {
                    // Fallback: Show error
                    $this->error('Failed to convert file for preview', 500);
                    return;
                }
            }
            $result = $this->fileService->previewFile($userId, $fileId);
            
            if (!$canDownload) {
                $this->sendPreviewViewOnly($result);
            } else {
                $this->sendPreviewWithToolbar($result);
            }

        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Send PDF with toolbar disabled (view-only mode)
     * Disables download, print, save buttons in PDF viewer
     */
    private function sendPdfViewOnly(array $result): void
    {
        $path = $result['path'] ?? $result['pdf_path'] ?? null;
        $name = $result['name'] ?? 'preview.pdf';

        if (!$path || !file_exists($path)) {
            $this->error('File not found', 404);
            return;
        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        if (!headers_sent()) {
            // ✅ Force inline display
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $name . '"');
            
            // ✅ Disable caching
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // ✅ PDF viewer parameters to disable toolbar
            // #toolbar=0 hides the toolbar
            // #navpanes=0 hides navigation panes
            // #scrollbar=0 hides scrollbar
            header('Content-Disposition: inline; filename="' . $name . '#toolbar=0&navpanes=0&scrollbar=0"');
            
            header('Content-Length: ' . filesize($path));
            header('X-Content-Type-Options: nosniff');
        }
        
        readfile($path);
        exit;
    }

     /**
     * Send PDF with toolbar enabled (full access)
     */
    private function sendPdfWithToolbar(array $result): void
    {
        $path = $result['path'] ?? $result['pdf_path'] ?? null;
        $name = $result['name'] ?? 'preview.pdf';

        if (!$path || !file_exists($path)) {
            $this->error('File not found', 404);
            return;
        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        if (!headers_sent()) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $name . '"');
            header('Cache-Control: public, max-age=3600');
            header('Content-Length: ' . filesize($path));
            header('X-Content-Type-Options: nosniff');
        }
        
        readfile($path);
        exit;
    }

    /**
     * Send other previews with toolbar disabled (view-only mode)
     */
    private function sendPreviewViewOnly(array $result): void
    {
        $mimeType = $result['mime_type'] ?? 'text/plain';
        $name = $result['name'] ?? 'preview';
        $path = $result['path'];

        if (!file_exists($path)) {
            throw new \Exception('File not found', 404);
        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        if (!headers_sent()) {
            header('Content-Type: ' . $mimeType);
            header('Content-Disposition: inline; filename="' . $name . '"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('X-Content-Type-Options: nosniff');
            header('Access-Control-Allow-Origin: *');
        }
        
        readfile($path);
        exit;
    }

    /**
     * Send other previews with toolbar enabled (full access)
     */
    private function sendPreviewWithToolbar(array $result): void
    {
        $mimeType = $result['mime_type'] ?? 'text/plain';
        $name = $result['name'] ?? 'preview';
        $path = $result['path'];

        if (!file_exists($path)) {
            throw new \Exception('File not found', 404);
        }

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
     * Send PDF file
     */
    private function sendPdf(string $path, string $name): void
    {
        if (!file_exists($path)) {
            $this->error('File not found', 404);
            return;
        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        if (!headers_sent()) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $name . '"');
            header('Cache-Control: public, max-age=3600');
            header('Content-Length: ' . filesize($path));
        }
        
        readfile($path);
        exit;
    }

    /**
     * Send preview (image/video/etc)
     */
    private function sendPreview(array $result): void
    {
        $mimeType = $result['mime_type'] ?? 'text/plain';
        $name = $result['name'] ?? 'preview';
        $path = $result['path'];

        if (!file_exists($path)) {
            throw new \Exception('File not found', 404);
        }

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
     * Get preview info
     * Route: /NAS/api/v1/preview/info
     */
    public function info(): void
    {
        try {
            $userId = $this->getUserId();
            $fileId = isset($_GET['file_id']) ? (int)$_GET['file_id'] : 0;

            if (!$fileId) {
                $this->error('File ID required', 400);
                return;
            }

            if (!$this->permissionService->canView($userId, 'file', $fileId)) {
                $this->error('Access denied', 403);
                return;
            }

            // ✅ Now $this->conversionService exists
            $info = $this->conversionService->getPreviewInfo($fileId, $userId);
            $this->success($info, 'Preview info retrieved');

        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}