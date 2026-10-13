<?php
namespace App\Controllers\NAS\Api\V1;

use App\Controllers\NAS\BaseController;
use App\Services\NAS\FileService;
use App\Services\NAS\StorageService;
use App\Services\NAS\PermissionService;
use App\Repositories\NAS\FileRepository;
use App\Repositories\NAS\FolderRepository;
use App\Repositories\NAS\ClusterRepository;
use App\Repositories\NAS\ShareRepository;
use App\Repositories\NAS\StarredRepository;
use App\Repositories\NAS\UserRepository;
use App\Repositories\NAS\SyncRepository;

class DocxPreviewController extends BaseController
{
    private FileService $fileService;
    private PermissionService $permissionService;

    public function __construct()
    {
        $fileRepository = new FileRepository();
        $folderRepository = new FolderRepository();
        $clusterRepository = new ClusterRepository();
        $shareRepository = new ShareRepository();
        $starredRepository = new StarredRepository();
        $userRepository = new UserRepository();
        $syncRepository = new SyncRepository();
        $storageService = new StorageService();
        
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
            $storageService,
            $this->permissionService
        );
    }

    /**
     * GET - Preview DOC/DOCX file
     * Route: /NAS/api/v1/preview/docx/{id}
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

        } catch (\Throwable $e) {
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
     * Route: /NAS/api/v1/preview/docx/{id}/pdf
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
                'download_url' => site_url('NAS/api/v1/preview/docx/' . $fileId . '/pdf/download')
            ], 'PDF export ready');

        } catch (\Throwable $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 404);
        }
    }
}