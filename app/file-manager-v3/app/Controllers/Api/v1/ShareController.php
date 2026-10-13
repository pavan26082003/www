<?php
namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\ShareService;
use App\Services\PermissionService;
use App\Services\NotificationService;
use App\Repositories\ShareRepository;
use App\Repositories\UserRepository;
use App\Repositories\FileRepository;
use App\Repositories\FolderRepository;

class ShareController extends BaseController
{
    private ShareService $shareService;
    private PermissionService $permissionService;

    public function __construct()
    {
        $fileRepository = new FileRepository();
        $folderRepository = new FolderRepository();
        $shareRepository = new ShareRepository();
        $userRepository = new UserRepository();
        
        $this->permissionService = new PermissionService(
            $fileRepository,
            $folderRepository,
            $shareRepository
        );
        
        $this->shareService = new ShareService(
            $shareRepository,
            $userRepository,
            $fileRepository,
            $folderRepository,
            $this->permissionService
        );
    }

    /**
     * POST - Share Item
     * Route: /api/v1/share
     */
    public function store(): void
    {
        try {
            $userId = $this->getUserId();
            $input = json_decode(file_get_contents('php://input'), true);


            if (!$input) {
                $this->error('Invalid JSON data', 400);
                return;
            }

            // Get parameters with proper validation
            $itemType = isset($input['item_type']) ? $input['item_type'] : null;
            $itemId = isset($input['item_id']) ? (int)$input['item_id'] : null;
            $sharedWithUserId = isset($input['share_with_user_id']) ? (int)$input['share_with_user_id'] : null;
            $permission = isset($input['permission']) ? $input['permission'] : 'view';
            $expiresAt = isset($input['expires_at']) && !empty($input['expires_at']) ? $input['expires_at'] : null;

            // Validate required fields
            if (!$itemType || !$itemId || !$sharedWithUserId) {
                $this->error('Missing required fields: item_type, item_id, share_with_user_id are required', 400);
                return;
            }

            // Validate item type
            if (!in_array($itemType, ['file', 'folder'])) {
                $this->error('Invalid item_type. Must be "file" or "folder"', 400);
                return;
            }

            // Validate permission
            $validPermissions = ['view', 'download', 'edit', 'share', 'full'];
            if (!in_array($permission, $validPermissions)) {
                $this->error('Invalid permission. Must be one of: ' . implode(', ', $validPermissions), 400);
                return;
            }

            // Check if user is trying to share with themselves
            if ($sharedWithUserId === $userId) {
                $this->error('You cannot share with yourself', 400);
                return;
            }

            // Check if user has permission to share
            if (!$this->permissionService->canShare($userId, $itemType, $itemId)) {
                $this->error('You do not have permission to share this item', 403);
                return;
            }

            // Create the share
            $result = $this->shareService->createShare(
                $userId,
                $itemType,
                $itemId,
                $sharedWithUserId,
                $permission,
                $expiresAt
            );

            $this->success(['share' => $result], 'Item shared successfully');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * GET - List Shares
     * Route: /api/v1/share
     */
    public function index(): void
    {
        try {
            $userId = $this->getUserId();
            $itemType = isset($_GET['item_type']) ? $_GET['item_type'] : null;
            $itemId = isset($_GET['item_id']) ? (int)$_GET['item_id'] : null;

            if ($itemType && $itemId) {
                // Check if user owns this item or has share permission
                if (!$this->permissionService->isOwner($userId, $itemType, $itemId)) {
                    // Check if user has share permission on this item
                    $share = $this->shareService->getShareForUser($userId, $itemType, $itemId);
                    if (!$share || !in_array($share['permissions'], ['share', 'full'])) {
                        $this->error('Access denied', 403);
                        return;
                    }
                }
                $shares = $this->shareService->getSharesByItem($itemType, $itemId);
            } else {
                $shares = $this->shareService->getSharesForUser($userId);
            }
            
            $this->success(['shares' => $shares], 'Shares retrieved');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * PUT - Update Share
     * Route: /api/v1/share/{id}
     */
    public function update(int $shareId): void
    {
        try {
            $userId = $this->getUserId();
            $input = json_decode(file_get_contents('php://input'), true);


            if (!$input) {
                $this->error('Invalid JSON data', 400);
                return;
            }

            $permission = isset($input['permission']) ? $input['permission'] : null;
            $expiresAt = isset($input['expires_at']) ? $input['expires_at'] : null;

            if (!$permission) {
                $this->error('Permission required', 400);
                return;
            }

            $validPermissions = ['view', 'download', 'edit', 'share', 'full'];
            if (!in_array($permission, $validPermissions)) {
                $this->error('Invalid permission. Must be one of: ' . implode(', ', $validPermissions), 400);
                return;
            }

            $result = $this->shareService->updateShare($shareId, $userId, $permission, $expiresAt);
            $this->success(['share' => $result], 'Share updated');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * DELETE - Revoke Share
     * Route: /api/v1/share/{id}
     */
    public function destroy(int $shareId): void
    {
        try {
            $userId = $this->getUserId();
            
            $result = $this->shareService->revokeShare($shareId, $userId);
            $this->success(null, 'Share revoked successfully');

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}