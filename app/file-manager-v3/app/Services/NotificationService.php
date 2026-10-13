<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\NotificationRepository;

class NotificationService
{
    private UserRepository $userRepository;
    private NotificationRepository $notificationRepository;
    private WebSocketService $webSocketService;

    public function __construct(
        UserRepository $userRepository,
        NotificationRepository $notificationRepository,
        WebSocketService $webSocketService
    ) {
        $this->userRepository = $userRepository;
        $this->notificationRepository = $notificationRepository;
        $this->webSocketService = $webSocketService;
    }

    /**
     * Send notification to user
     */
    public function sendNotification(int $userId, string $type, array $data): void
    {
        // Save to database
        $notificationId = $this->notificationRepository->create([
            'user_id' => $userId,
            'type' => $type,
            'data' => json_encode($data),
            'read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Send via WebSocket
        $this->webSocketService->notifyUser($userId, [
            'notification_id' => $notificationId,
            'type' => $type,
            'data' => $data
        ]);
    }

    /**
     * Notify users when a file is shared with them
     */
    public function notifyFileShared(int $fileId, int $sharedByUserId, int $sharedWithUserId): void
    {
        $sharedBy = $this->userRepository->find($sharedByUserId);
        $file = $this->fileService->getFileInfo($sharedByUserId, $fileId);

        $this->sendNotification($sharedWithUserId, 'file_shared', [
            'file_id' => $fileId,
            'file_name' => $file['name'] ?? 'Unknown file',
            'shared_by' => $sharedBy['username'] ?? 'Unknown user',
            'message' => "A file has been shared with you"
        ]);
    }

    /**
     * Notify users when a folder is shared with them
     */
    public function notifyFolderShared(int $folderId, int $sharedByUserId, int $sharedWithUserId): void
    {
        $sharedBy = $this->userRepository->find($sharedByUserId);
        $folder = $this->folderService->getFolderInfo($sharedByUserId, $folderId);

        $this->sendNotification($sharedWithUserId, 'folder_shared', [
            'folder_id' => $folderId,
            'folder_name' => $folder['name'] ?? 'Unknown folder',
            'shared_by' => $sharedBy['username'] ?? 'Unknown user',
            'message' => "A folder has been shared with you"
        ]);
    }

    /**
     * Notify users when a file is updated
     */
    public function notifyFileUpdated(int $fileId, int $updatedByUserId): void
    {
        $updatedBy = $this->userRepository->find($updatedByUserId);
        $file = $this->fileService->getFileInfo($updatedByUserId, $fileId);

        // Get all users who have access to this file
        $users = $this->permissionService->getUsersWithAccess($fileId, 'file');

        foreach ($users as $user) {
            if ($user['user_id'] != $updatedByUserId) {
                $this->sendNotification($user['user_id'], 'file_updated', [
                    'file_id' => $fileId,
                    'file_name' => $file['name'] ?? 'Unknown file',
                    'updated_by' => $updatedBy['username'] ?? 'Unknown user',
                    'message' => "A file has been updated"
                ]);
            }
        }
    }
}