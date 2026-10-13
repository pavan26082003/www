<?php
namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\FileService;
use App\Services\StorageService;
use App\Services\PermissionService;
use App\Repositories\FileRepository;
use App\Repositories\FolderRepository;
use App\Repositories\ClusterRepository;
use App\Repositories\ShareRepository;
use App\Repositories\StarredRepository;
use App\Repositories\UserRepository;
use App\Repositories\SyncRepository;

class PptPreviewController extends BaseController
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
        
        $permissionService = new PermissionService(
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
            $permissionService
        );
    }
    /**
     * GET - Preview PPT/PPTX file
     * Route: /api/v1/preview/ppt/{id}
     */
    public function preview(int $fileId): void
    {
        try {
            $userId = $this->getUserId();
            
            // Check permission
            if (!$this->permissionService->canView($userId, 'file', $fileId)) {
                $this->error('Access denied', 403);
                return;
            }

            // Get file info
            $fileInfo = $this->fileService->getFileInfo($userId, $fileId);
            
            // Check if it's a PPT/PPTX file
            $extension = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, ['ppt', 'pptx'])) {
                $this->error('Not a PowerPoint file', 400);
                return;
            }

            // Get file content
            $result = $this->fileService->previewFile($userId, $fileId);
            $content = file_get_contents($result['path']);

            // Try to extract preview info
            $preview = $this->extractPptPreview($content, $extension);

            $this->success([
                'file_id' => $fileId,
                'name' => $fileInfo['name'],
                'slides' => $preview['slides'] ?? [],
                'slide_count' => $preview['slide_count'] ?? 0,
                'preview_type' => 'ppt'
            ], 'PPT preview retrieved');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 404);
        }
    }

    /**
     * Extract PPT preview (simplified - would use a library in production)
     */
    private function extractPptPreview(string $content, string $extension): array
    {
        // In production, use a library like PhpOffice/PhpPresentation
        // For now, return placeholder data
        return [
            'slides' => [
                ['title' => 'Slide 1', 'content' => 'Content of slide 1'],
                ['title' => 'Slide 2', 'content' => 'Content of slide 2'],
                ['title' => 'Slide 3', 'content' => 'Content of slide 3']
            ],
            'slide_count' => 3
        ];
    }

    /**
     * GET - Get slide thumbnails
     * Route: /api/v1/preview/ppt/{id}/slides
     */
    public function slides(int $fileId): void
    {
        try {
            $userId = $this->getUserId();
            
            if (!$this->permissionService->canView($userId, 'file', $fileId)) {
                $this->error('Access denied', 403);
                return;
            }

            $fileInfo = $this->fileService->getFileInfo($userId, $fileId);
            
            $extension = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, ['ppt', 'pptx'])) {
                $this->error('Not a PowerPoint file', 400);
                return;
            }

            // In production, generate thumbnail for each slide
            $slides = [];
            for ($i = 1; $i <= 10; $i++) {
                $slides[] = [
                    'number' => $i,
                    'thumbnail' => null, // Would be base64 image
                    'title' => "Slide $i"
                ];
            }

            $this->success([
                'file_id' => $fileId,
                'total_slides' => count($slides),
                'slides' => $slides
            ], 'Slides retrieved');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 404);
        }
    }
}