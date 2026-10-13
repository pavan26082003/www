<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\FileRepository;
use App\Repositories\FolderRepository;
use App\Repositories\ShareRepository;
use App\Helpers\FileHelper;

class DashboardService
{
    private UserRepository $userRepository;
    private FileRepository $fileRepository;
    private FolderRepository $folderRepository;
    private ShareRepository $shareRepository;

    public function __construct(
        UserRepository $userRepository,
        FileRepository $fileRepository,
        FolderRepository $folderRepository,
        ShareRepository $shareRepository
    ) {
        $this->userRepository = $userRepository;
        $this->fileRepository = $fileRepository;
        $this->folderRepository = $folderRepository;
        $this->shareRepository = $shareRepository;
    }

    /**
     * Get complete dashboard data
     */
    public function getDashboardData(int $userId): array
    {
        $user = $this->userRepository->find($userId);
        
        if (!$user) {
            throw new \Exception('User not found', 404);
        }

        $storageUsed = (int)$user['storage_used'];
        $storageLimit = (int)$user['storage_limit'];
        $storagePercentage = $storageLimit > 0 ? ($storageUsed / $storageLimit) * 100 : 0;

        return [
            'user' => [
                'id' => $userId,
                'username' => $user['username'],
                'email' => $user['email'],
                'initial' => strtoupper(substr($user['username'], 0, 1))
            ],
            'storage' => [
                'used' => $storageUsed,
                'limit' => $storageLimit,
                'used_formatted' => FileHelper::formatSize($storageUsed),
                'limit_formatted' => FileHelper::formatSize($storageLimit),
                'percentage' => round($storagePercentage, 1)
            ],
            'stats' => $this->getQuickStats($userId),
            'recent_items' => $this->getRecentItems($userId)
        ];
    }

    /**
     * Get recent items
     */
    public function getRecentItems(int $userId): array
    {
        $pdo = $this->userRepository->getPdo();
        $stmt = $pdo->prepare("
            (SELECT 
                id, 
                original_name as name, 
                'file' as type, 
                created_at, 
                file_size, 
                file_type,
                NULL as parent_id
             FROM files 
             WHERE user_id = ? AND deleted_at IS NULL
             ORDER BY created_at DESC LIMIT 5)
            UNION ALL
            (SELECT 
                id, 
                name, 
                'folder' as type, 
                created_at, 
                NULL as file_size, 
                NULL as file_type,
                parent_id
             FROM folders 
             WHERE user_id = ? AND deleted_at IS NULL
             ORDER BY created_at DESC LIMIT 5)
            ORDER BY created_at DESC
            LIMIT 10
        ");
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get quick stats
     */
    public function getQuickStats(int $userId): array
    {
        $pdo = $this->userRepository->getPdo();
        $stats = [];

        // Total files
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM files WHERE user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$userId]);
        $stats['files'] = (int)$stmt->fetch()['total'];

        // Total folders
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM folders WHERE user_id = ? AND deleted_at IS NULL");
        $stmt->execute([$userId]);
        $stats['folders'] = (int)$stmt->fetch()['total'];

        // Shared items (received)
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total FROM shared_items 
            WHERE shared_with_user_id = ? AND (expires_at IS NULL OR expires_at > NOW())
        ");
        $stmt->execute([$userId]);
        $stats['shared'] = (int)$stmt->fetch()['total'];

        // Trash - Files
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM files WHERE user_id = ? AND deleted_at IS NOT NULL");
        $stmt->execute([$userId]);
        $stats['trash_files'] = (int)$stmt->fetch()['total'];

        // Trash - Folders
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM folders WHERE user_id = ? AND deleted_at IS NOT NULL");
        $stmt->execute([$userId]);
        $stats['trash_folders'] = (int)$stmt->fetch()['total'];
        $stats['trash_total'] = $stats['trash_files'] + $stats['trash_folders'];

        return $stats;
    }
}