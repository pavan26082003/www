<?php
namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\OfficeConversionService;
use App\Services\FileService;
use App\Services\PermissionService;

class PreviewController extends BaseController
{
    private OfficeConversionService $conversionService;
    private FileService $fileService;
    private PermissionService $permissionService;

    public function __construct()
    {
        // Create dependencies
        $this->conversionService = new OfficeConversionService(
            new \App\Repositories\FileRepository(),
            new \App\Repositories\UserRepository(),
            new \App\Services\StorageService()
        );
        
        $this->fileService = new \App\Services\FileService(
            new \App\Repositories\FileRepository(),
            new \App\Repositories\FolderRepository(),
            new \App\Repositories\ClusterRepository(),
            new \App\Repositories\ShareRepository(),
            new \App\Repositories\StarredRepository(),
            new \App\Repositories\UserRepository(),
            new \App\Services\StorageService(),
            new \App\Services\PermissionService(
                new \App\Repositories\FileRepository(),
                new \App\Repositories\FolderRepository(),
                new \App\Repositories\ShareRepository()
            )
        );
        
        $this->permissionService = new PermissionService(
            new \App\Repositories\FileRepository(),
            new \App\Repositories\FolderRepository(),
            new \App\Repositories\ShareRepository()
        );
    }

    /**
     * Convert and preview file
     * Route: /api/v1/preview/convert
     */
    public function convert(): void
    {
        try {
            error_log("=== PREVIEW CONVERT STARTED ===");
            
            $userId = $this->getUserId();
            $fileId = isset($_GET['file_id']) ? (int)$_GET['file_id'] : 0;

            error_log("📤 User ID: $userId, File ID: $fileId");

            if (!$fileId) {
                $this->error('File ID required', 400);
                return;
            }

            // Check permission
            if (!$this->permissionService->canView($userId, 'file', $fileId)) {
                error_log("❌ Access denied for user $userId on file $fileId");
                $this->error('Access denied', 403);
                return;
            }

            // Get file info
            $fileInfo = $this->fileService->getFileInfo($userId, $fileId);
            error_log("📄 File info: " . print_r($fileInfo, true));
            
            $extension = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
            error_log("📄 Extension: $extension");

            // For PDF files, serve directly
            if ($extension === 'pdf') {
                error_log("📄 Serving PDF directly");
                $result = $this->fileService->previewFile($userId, $fileId);
                $this->sendPreview($result);
                return;
            }

            // For Office files, convert to PDF
            if (in_array($extension, ['ppt', 'pptx', 'doc', 'docx'])) {
                error_log("📄 Converting Office file to PDF");
                $result = $this->conversionService->convertToPdf($fileId, $userId);
                
                error_log("📄 Conversion result: " . print_r($result, true));
                
                if ($result['success']) {
                    error_log("✅ Conversion successful, serving PDF");
                    // Serve the PDF
                    $this->sendPdf($result['pdf_path'], $result['name']);
                    return;
                } else {
                    error_log("❌ Conversion failed: " . ($result['error'] ?? 'Unknown error'));
                    // Use Google Docs viewer as fallback
                    if (isset($result['fallback_url'])) {
                        error_log("📄 Redirecting to Google Docs: " . $result['fallback_url']);
                        header('Location: ' . $result['fallback_url']);
                        exit;
                    }
                    $this->error($result['error'] ?? 'Conversion failed', 500);
                    return;
                }
            }

            // For other files, use the regular preview
            error_log("📄 Using regular preview for extension: $extension");
            $result = $this->fileService->previewFile($userId, $fileId);
            $this->sendPreview($result);

        } catch (\Exception $e) {
            error_log("❌ Preview conversion error: " . $e->getMessage());
            error_log("❌ Stack trace: " . $e->getTraceAsString());
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
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
     * Route: /api/v1/preview/info
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

            $info = $this->conversionService->getPreviewInfo($fileId, $userId);
            $this->success($info, 'Preview info retrieved');

        } catch (\Exception $e) {
            error_log("❌ Preview info error: " . $e->getMessage());
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}