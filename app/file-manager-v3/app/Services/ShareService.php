<?php
namespace App\Services;

use App\Repositories\ShareRepository;
use App\Repositories\UserRepository;
use App\Repositories\FileRepository;
use App\Repositories\FolderRepository;

class ShareService
{
    private ShareRepository $shareRepository;
    private UserRepository $userRepository;
    private FileRepository $fileRepository;
    private FolderRepository $folderRepository;
    private PermissionService $permissionService;

    public function __construct(
        ShareRepository $shareRepository,
        UserRepository $userRepository,
        FileRepository $fileRepository,
        FolderRepository $folderRepository,
        PermissionService $permissionService
    ) {
        $this->shareRepository = $shareRepository;
        $this->userRepository = $userRepository;
        $this->fileRepository = $fileRepository;
        $this->folderRepository = $folderRepository;
        $this->permissionService = $permissionService;
    }

    /**
 * Create share - supports recursive folder sharing
 */
public function createShare(
    int $userId,
    string $itemType,
    int $itemId,
    int $sharedWithUserId,
    string $permissions = 'view',
    ?string $expiresAt = null
): array {
    // Check if user exists
    $user = $this->userRepository->find($sharedWithUserId);
    if (!$user) {
        throw new \Exception('User not found', 404);
    }

    // Check if user is trying to share with themselves
    if ($userId === $sharedWithUserId) {
        throw new \Exception('You cannot share with yourself', 400);
    }

    // ✅ Check if user has permission to share
    // If user is not the owner, check if they have 'share' or 'full' permission
    $isOwner = $this->permissionService->isOwner($userId, $itemType, $itemId);
    
    if (!$isOwner) {
        // Check if user has 'share' or 'full' permission on this item
        $userShare = $this->shareRepository->findForUserAndItem($userId, $itemType, $itemId);
        if (!$userShare || !in_array($userShare['permissions'], ['share', 'full'])) {
            throw new \Exception('You do not have permission to share this item', 403);
        }
        
        // ✅ Get the original owner of the item
        $originalOwner = $this->getItemOwner($itemType, $itemId);
        if (!$originalOwner) {
            throw new \Exception('Original owner not found', 404);
        }
        
        // ✅ Use the original owner's ID for the share (not the current user)
        // This ensures the share is tracked under the original owner
        $shareUserId = $originalOwner['user_id'];
    } else {
        $shareUserId = $userId;
    }

    // Check if item exists
    $item = $this->getItem($itemType, $itemId);
    if (!$item) {
        throw new \Exception('Item not found', 404);
    }

    // Check if already shared with this user
    $existing = $this->shareRepository->findForUserAndItem($sharedWithUserId, $itemType, $itemId);
    if ($existing) {
        throw new \Exception('Item already shared with this user', 400);
    }

    // Get item name
    $itemName = $this->getItemName($itemType, $item);

    // ✅ Create share with the original owner as the sharer
    $shareId = $this->shareRepository->create([
        'user_id' => $shareUserId, // ✅ Original owner, not the current user
        'item_type' => $itemType,
        'item_id' => $itemId,
        'shared_with_user_id' => $sharedWithUserId,
        'permissions' => $permissions,
        'expires_at' => $expiresAt
    ]);

    // If it's a folder, recursively share all subfolders and files
    if ($itemType === 'folder') {
        $this->shareFolderRecursive($shareUserId, $itemId, $sharedWithUserId, $permissions, $expiresAt);
    }

    // Get the user who shared (original owner)
    $shareUser = $this->userRepository->find($shareUserId);

    return [
        'share_id' => $shareId,
        'item_type' => $itemType,
        'item_id' => $itemId,
        'item_name' => $itemName,
        'shared_with_user_id' => $sharedWithUserId,
        'shared_with_username' => $user['username'],
        'shared_by_user_id' => $shareUserId,
        'shared_by_username' => $shareUser['username'] ?? 'Unknown',
        'permissions' => $permissions,
        'expires_at' => $expiresAt,
        'is_reshare' => !$isOwner
    ];
}

/**
 * Get the owner of an item
 */
private function getItemOwner(string $itemType, int $itemId): ?array
{
    if ($itemType === 'file') {
        $item = $this->fileRepository->find($itemId);
        if ($item) {
            return ['user_id' => $item['user_id']];
        }
    }
    if ($itemType === 'folder') {
        $item = $this->folderRepository->find($itemId);
        if ($item) {
            return ['user_id' => $item['user_id']];
        }
    }
    return null;
}

    /**
     * Recursively share all subfolders and files inside a folder
     */
    private function shareFolderRecursive(
        int $userId,
        int $folderId,
        int $sharedWithUserId,
        string $permissions,
        ?string $expiresAt
    ): void {
        // Get all subfolders
        $subfolders = $this->folderRepository->getSubfolders($folderId, $userId);
        
        // Share each subfolder
        foreach ($subfolders as $subfolder) {
            // Check if already shared
            $existing = $this->shareRepository->findForUserAndItem($sharedWithUserId, 'folder', $subfolder['id']);
            if (!$existing) {
                $this->shareRepository->create([
                    'user_id' => $userId,
                    'item_type' => 'folder',
                    'item_id' => $subfolder['id'],
                    'shared_with_user_id' => $sharedWithUserId,
                    'permissions' => $permissions,
                    'expires_at' => $expiresAt
                ]);
            }
            
            // Recursively share subfolders inside this folder
            $this->shareFolderRecursive($userId, $subfolder['id'], $sharedWithUserId, $permissions, $expiresAt);
        }

        // Get all files in this folder
        $files = $this->fileRepository->getFilesByFolderId($folderId, $userId);
        
        // Share each file
        foreach ($files as $file) {
            // Check if already shared
            $existing = $this->shareRepository->findForUserAndItem($sharedWithUserId, 'file', $file['id']);
            if (!$existing) {
                $this->shareRepository->create([
                    'user_id' => $userId,
                    'item_type' => 'file',
                    'item_id' => $file['id'],
                    'shared_with_user_id' => $sharedWithUserId,
                    'permissions' => $permissions,
                    'expires_at' => $expiresAt
                ]);
            }
        }
    }

    /**
     * Get item
     */
    private function getItem(string $type, int $id): ?array
    {
        if ($type === 'file') {
            return $this->fileRepository->find($id);
        }
        if ($type === 'folder') {
            return $this->folderRepository->find($id);
        }
        return null;
    }

    /**
     * Get item name
     */
    private function getItemName(string $type, array $item): string
    {
        if ($type === 'file') {
            return $item['original_name'] ?? 'File';
        }
        if ($type === 'folder') {
            return $item['name'] ?? 'Folder';
        }
        return 'Item';
    }

    /**
     * Get shares by item
     */
    public function getSharesByItem(string $itemType, int $itemId): array
    {
        return $this->shareRepository->getByItem($itemId, $itemType);
    }

    /**
     * Get shares for user
     */
    public function getSharesForUser(int $userId): array
    {
        return $this->shareRepository->getSharedWithMe($userId);
    }

    /**
     * Revoke share - also revokes all sub-items if folder
     */
    public function revokeShare(int $shareId, int $userId): bool
    {
        $share = $this->shareRepository->find($shareId);
        if (!$share) {
            throw new \Exception('Share not found', 404);
        }

        // Check if user owns this share
        if ($share['user_id'] != $userId) {
            throw new \Exception('You do not have permission to revoke this share', 403);
        }

        // ✅ If it's a folder, revoke all sub-items too
        if ($share['item_type'] === 'folder') {
            $this->revokeFolderSharesRecursive($share['item_id'], $share['shared_with_user_id'], $userId);
        }

        return $this->shareRepository->delete($shareId);
    }

    /**
     * Recursively revoke shares for all subfolders and files
     */
    private function revokeFolderSharesRecursive(int $folderId, int $sharedWithUserId, int $userId): void
    {
        // Get all subfolders
        $subfolders = $this->folderRepository->getSubfolders($folderId, $userId);
        
        // Revoke each subfolder share
        foreach ($subfolders as $subfolder) {
            $this->shareRepository->deleteForItem($subfolder['id'], 'folder', $sharedWithUserId);
            // Recursively revoke subfolders inside this folder
            $this->revokeFolderSharesRecursive($subfolder['id'], $sharedWithUserId, $userId);
        }

        // Get all files in this folder
        $files = $this->fileRepository->getFilesByFolderId($folderId, $userId);
        
        // Revoke each file share
        foreach ($files as $file) {
            $this->shareRepository->deleteForItem($file['id'], 'file', $sharedWithUserId);
        }
    }

    /**
     * Update share
     */
    public function updateShare(int $shareId, int $userId, string $permission, ?string $expiresAt): array
    {
        $share = $this->shareRepository->find($shareId);
        if (!$share) {
            throw new \Exception('Share not found', 404);
        }

        // Check if user owns this share
        if ($share['user_id'] != $userId) {
            throw new \Exception('You do not have permission to update this share', 403);
        }

        // ✅ If it's a folder, update all sub-items too
        if ($share['item_type'] === 'folder') {
            $this->updateFolderSharesRecursive($share['item_id'], $share['shared_with_user_id'], $permission, $expiresAt);
        }

        // Update the main share
        $pdo = $this->shareRepository->getPdo();
        $sql = "UPDATE shared_items SET permissions = ?, expires_at = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$permission, $expiresAt, $shareId]);

        return [
            'share_id' => $shareId,
            'permissions' => $permission,
            'expires_at' => $expiresAt
        ];
    }

    /**
     * Recursively update shares for all subfolders and files
     */
    private function updateFolderSharesRecursive(int $folderId, int $sharedWithUserId, string $permission, ?string $expiresAt): void
    {
        // Get all subfolders
        $subfolders = $this->folderRepository->getSubfolders($folderId, $sharedWithUserId);
        
        // Update each subfolder share
        foreach ($subfolders as $subfolder) {
            $share = $this->shareRepository->findForUserAndItem($sharedWithUserId, 'folder', $subfolder['id']);
            if ($share) {
                $pdo = $this->shareRepository->getPdo();
                $sql = "UPDATE shared_items SET permissions = ?, expires_at = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$permission, $expiresAt, $share['id']]);
            }
            // Recursively update subfolders inside this folder
            $this->updateFolderSharesRecursive($subfolder['id'], $sharedWithUserId, $permission, $expiresAt);
        }

        // Get all files in this folder
        $files = $this->fileRepository->getFilesByFolderId($folderId, $sharedWithUserId);
        
        // Update each file share
        foreach ($files as $file) {
            $share = $this->shareRepository->findForUserAndItem($sharedWithUserId, 'file', $file['id']);
            if ($share) {
                $pdo = $this->shareRepository->getPdo();
                $sql = "UPDATE shared_items SET permissions = ?, expires_at = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$permission, $expiresAt, $share['id']]);
            }
        }
    }

    /**
     * Get share for a specific user and item
     */
    public function getShareForUser(int $userId, string $itemType, int $itemId): ?array
    {
        return $this->shareRepository->findForUserAndItem($userId, $itemType, $itemId);
    }
}