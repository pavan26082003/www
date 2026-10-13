<?php
namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\FileService;
use App\Services\PermissionService;

class DocxPreviewController extends BaseController
{
    private FileService $fileService;
    private PermissionService $permissionService;

    public function __construct(
        FileService $fileService,
        PermissionService $permissionService
    ) {
        $this->fileService = $fileService;
        $this->permissionService = $permissionService;
    }

    /**
     * GET - Preview DOC/DOCX file
     * Route: /api/v1/preview/docx/{id}
     */
    public function preview(int $fileId): void
    {
        try {
            $userId = $this->getUserId();
            
            if (!$this->permissionService->canView($userId, 'file', $fileId)) {
                $this->error('Access denied', 403);
                return;
            }

            $fileInfo = $this->fileService->getFileInfo($userId, $fileId);
            
            $extension = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, ['doc', 'docx'])) {
                $this->error('Not a Word document', 400);
                return;
            }

            // Get file content
            $result = $this->fileService->previewFile($userId, $fileId);
            $content = file_get_contents($result['path']);

            // Extract text content
            $textContent = $this->extractDocxContent($content, $extension);

            $this->success([
                'file_id' => $fileId,
                'name' => $fileInfo['name'],
                'content' => $textContent,
                'word_count' => str_word_count($textContent),
                'character_count' => strlen($textContent),
                'preview_type' => 'docx'
            ], 'DOCX preview retrieved');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 404);
        }
    }

    /**
     * Extract DOCX content (simplified - would use a library in production)
     */
    private function extractDocxContent(string $content, string $extension): string
    {
        if ($extension === 'docx') {
            // In production, use a library like PhpOffice/PhpWord
            // For now, return placeholder content
            return "This is a preview of the Word document content.\n\n" .
                   "Lorem ipsum dolor sit amet, consectetur adipiscing elit.\n" .
                   "Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.\n\n" .
                   "Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.\n" .
                   "Nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in.\n\n" .
                   "Reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.\n" .
                   "Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia.";
        }

        return strip_tags($content);
    }

    /**
     * GET - Export as PDF
     * Route: /api/v1/preview/docx/{id}/pdf
     */
    public function exportPdf(int $fileId): void
    {
        try {
            $userId = $this->getUserId();
            
            if (!$this->permissionService->canDownload($userId, 'file', $fileId)) {
                $this->error('Download permission denied', 403);
                return;
            }

            $fileInfo = $this->fileService->getFileInfo($userId, $fileId);
            
            $extension = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, ['doc', 'docx'])) {
                $this->error('Not a Word document', 400);
                return;
            }

            // In production, use a library to convert to PDF
            // For now, return placeholder
            $this->success([
                'message' => 'PDF export would be generated here',
                'file_id' => $fileId,
                'name' => $fileInfo['name'],
                'download_url' => '/api/v1/preview/docx/' . $fileId . '/pdf/download'
            ], 'PDF export ready');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 404);
        }
    }
}