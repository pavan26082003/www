<?php
namespace App\Services;

use App\Repositories\FileRepository;
use App\Repositories\FolderRepository;
use App\Repositories\ShareRepository;

class PermissionService
{
    private FileRepository $fileRepository;
    private FolderRepository $folderRepository;
    private ShareRepository $shareRepository;

    public function __construct(
        FileRepository $fileRepository,
        FolderRepository $folderRepository,
        ShareRepository $shareRepository
    ) {
        $this->fileRepository = $fileRepository;
        $this->folderRepository = $folderRepository;
        $this->shareRepository = $shareRepository;
    }

    /**
     * Check if user is owner of the item
     */
    public function isOwner(int $userId, string $type, int $itemId): bool
    {
        if ($type === 'file') {
            $file = $this->fileRepository->find($itemId);
            return $file && $file['user_id'] == $userId;
        }

        if ($type === 'folder') {
            $folder = $this->folderRepository->find($itemId);
            return $folder && $folder['user_id'] == $userId;
        }

        return false;
    }

    /**
     * Check if user can view item
     */
    public function canView(int $userId, string $type, int $itemId): bool
    {
        if ($this->isOwner($userId, $type, $itemId)) {
            return true;
        }

        return $this->hasPermission($userId, $type, $itemId, 'view');
    }

    /**
     * Check if user can download item
     */
    public function canDownload(int $userId, string $type, int $itemId): bool
    {
        if ($this->isOwner($userId, $type, $itemId)) {
            return true;
        }

        return $this->hasPermission($userId, $type, $itemId, 'download');
    }

    /**
     * Check if user can modify item
     */
    public function canModify(int $userId, string $type, int $itemId): bool
    {
        if ($this->isOwner($userId, $type, $itemId)) {
            return true;
        }

        return $this->hasPermission($userId, $type, $itemId, 'edit');
    }

    /**
     * Check if user can share item
     */
    public function canShare(int $userId, string $type, int $itemId): bool
    {
        if ($this->isOwner($userId, $type, $itemId)) {
            return true;
        }

        return $this->hasPermission($userId, $type, $itemId, 'share');
    }

    /**
     * Check if user can reshare an item
     */
    public function canReshare(int $userId, string $type, int $itemId): bool
    {
        // If user is owner, they can reshare
        if ($this->isOwner($userId, $type, $itemId)) {
            return true;
        }
        
        // Check if user has 'share' or 'full' permission
        $share = $this->shareRepository->findForUserAndItem($userId, $type, $itemId);
        if ($share && in_array($share['permissions'], ['share', 'full'])) {
            return true;
        }
        
        return false;
    }

    /**
     * Get user's permission level - includes inherited permissions from parent folders
     */
    public function getPermission(int $userId, string $type, int $itemId): string
    {
        // ✅ If user is owner, return 'full'
        if ($this->isOwner($userId, $type, $itemId)) {
            return 'full';
        }

        // ✅ Check direct share
        $share = $this->shareRepository->findForUserAndItem($userId, $type, $itemId);
        if ($share) {
            return $share['permissions'];
        }

        // ✅ For files, check parent folder permissions (inheritance)
        if ($type === 'file') {
            $file = $this->fileRepository->find($itemId);
            if ($file && $file['folder_id']) {
                $folderPermission = $this->getPermission($userId, 'folder', $file['folder_id']);
                if ($folderPermission !== 'none') {
                    return $folderPermission;
                }
            }
            return 'none';
        }

        // ✅ For folders, check parent folder permissions (inheritance)
        if ($type === 'folder') {
            $folder = $this->folderRepository->find($itemId);
            if ($folder && $folder['parent_id']) {
                $parentPermission = $this->getPermission($userId, 'folder', $folder['parent_id']);
                if ($parentPermission !== 'none') {
                    return $parentPermission;
                }
            }
            return 'none';
        }

        return 'none';
    }

    /**
     * Check if user has specific permission
     */
    private function hasPermission(int $userId, string $type, int $itemId, string $required): bool
    {
        // Check direct share
        $share = $this->shareRepository->findForUserAndItem($userId, $type, $itemId);
        if ($share && $this->permissionLevel($share['permissions']) >= $this->permissionLevel($required)) {
            return true;
        }

        // If file, check parent folder shares
        if ($type === 'file') {
            $file = $this->fileRepository->find($itemId);
            if ($file && $file['folder_id']) {
                return $this->hasPermission($userId, 'folder', $file['folder_id'], $required);
            }
        }

        // If folder, check parent folders recursively
        if ($type === 'folder') {
            $folder = $this->folderRepository->find($itemId);
            if ($folder && $folder['parent_id']) {
                return $this->hasPermission($userId, 'folder', $folder['parent_id'], $required);
            }
        }

        return false;
    }

    /**
     * Permission level hierarchy
     */
    private function permissionLevel(string $permission): int
    {
        $levels = [
            'view' => 1,
            'download' => 2,
            'edit' => 3,
            'share' => 4,
            'full' => 5
        ];

        return $levels[$permission] ?? 0;
    }

    /**
     * Check if user has access to folder path
     */
    public function checkFolderAccess(int $userId, int $folderId): bool
    {
        $folder = $this->folderRepository->find($folderId);
        if (!$folder) {
            return false;
        }

        if ($folder['user_id'] == $userId) {
            return true;
        }

        // Check if folder is shared with user
        return $this->hasPermission($userId, 'folder', $folderId, 'view');
    }

    /**
     * Get users who have access to an item
     */
    public function getUsersWithAccess(int $itemId, string $type): array
    {
        $shares = $this->shareRepository->getByItem($itemId, $type);
        $users = [];

        foreach ($shares as $share) {
            $users[] = [
                'user_id' => $share['shared_with_user_id'],
                'username' => $share['shared_with_name'],
                'email' => $share['shared_with_email'],
                'permissions' => $share['permissions'],
                'expires_at' => $share['expires_at']
            ];
        }

        return $users;
    }
}