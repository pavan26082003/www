<?php
namespace App\Services;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WebSocketService implements MessageComponentInterface
{
    private array $clients = [];
    private array $users = [];
    private FileService $fileService;
    private FolderService $folderService;
    private NotificationService $notificationService;

    public function __construct(
        FileService $fileService,
        FolderService $folderService,
        NotificationService $notificationService
    ) {
        $this->fileService = $fileService;
        $this->folderService = $folderService;
        $this->notificationService = $notificationService;
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        $this->clients[$conn->resourceId] = $conn;
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        $data = json_decode($msg, true);
        
        if (!$data) {
            return;
        }

        switch ($data['type'] ?? '') {
            case 'auth':
                $this->authenticate($from, $data['user_id'] ?? null);
                break;

            case 'sync':
                $this->handleSync($from, $data);
                break;

            case 'file_update':
                $this->handleFileUpdate($from, $data);
                break;

            case 'folder_update':
                $this->handleFolderUpdate($from, $data);
                break;

            case 'share_update':
                $this->handleShareUpdate($from, $data);
                break;

            case 'ping':
                $from->send(json_encode(['type' => 'pong', 'timestamp' => time()]));
                break;
        }
    }

    private function authenticate(ConnectionInterface $conn, ?int $userId): void
    {
        if ($userId && isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
            $this->users[$conn->resourceId] = $userId;
            $conn->send(json_encode([
                'type' => 'auth_success',
                'user_id' => $userId,
                'message' => 'Authenticated successfully'
            ]));
            
            // Send initial data
            $this->sendInitialData($conn, $userId);
        } else {
            $conn->send(json_encode([
                'type' => 'auth_failed',
                'message' => 'Authentication failed'
            ]));
        }
    }

    private function sendInitialData(ConnectionInterface $conn, int $userId): void
    {
        // Send recent changes
        $changes = $this->getRecentChanges($userId);
        $conn->send(json_encode([
            'type' => 'initial_data',
            'recent_changes' => $changes
        ]));
    }

    private function getRecentChanges(int $userId): array
    {
        // Get recent file changes
        $files = $this->fileService->getFilesByView($userId, 'recent');
        return array_slice($files, 0, 10);
    }

    private function handleSync(ConnectionInterface $conn, array $data): void
    {
        $userId = $this->users[$conn->resourceId] ?? null;
        if (!$userId) {
            return;
        }

        // Start sync process
        $conn->send(json_encode([
            'type' => 'sync_started',
            'message' => 'Sync process started'
        ]));

        // Simulate sync progress
        for ($i = 0; $i <= 100; $i += 10) {
            $conn->send(json_encode([
                'type' => 'sync_progress',
                'progress' => $i,
                'message' => "Syncing... $i%"
            ]));
            usleep(100000); // 0.1 second delay
        }

        $conn->send(json_encode([
            'type' => 'sync_completed',
            'message' => 'Sync completed successfully'
        ]));
    }

    private function handleFileUpdate(ConnectionInterface $conn, array $data): void
    {
        $userId = $this->users[$conn->resourceId] ?? null;
        if (!$userId) {
            return;
        }

        // Broadcast to all connected clients
        foreach ($this->clients as $client) {
            if ($client !== $conn) {
                $client->send(json_encode([
                    'type' => 'file_update',
                    'user_id' => $userId,
                    'file_id' => $data['file_id'] ?? null,
                    'action' => $data['action'] ?? null,
                    'data' => $data['data'] ?? []
                ]));
            }
        }
    }

    private function handleFolderUpdate(ConnectionInterface $conn, array $data): void
    {
        $userId = $this->users[$conn->resourceId] ?? null;
        if (!$userId) {
            return;
        }

        foreach ($this->clients as $client) {
            if ($client !== $conn) {
                $client->send(json_encode([
                    'type' => 'folder_update',
                    'user_id' => $userId,
                    'folder_id' => $data['folder_id'] ?? null,
                    'action' => $data['action'] ?? null,
                    'data' => $data['data'] ?? []
                ]));
            }
        }
    }

    private function handleShareUpdate(ConnectionInterface $conn, array $data): void
    {
        $userId = $this->users[$conn->resourceId] ?? null;
        if (!$userId) {
            return;
        }

        foreach ($this->clients as $client) {
            if ($client !== $conn) {
                $client->send(json_encode([
                    'type' => 'share_update',
                    'user_id' => $userId,
                    'share_id' => $data['share_id'] ?? null,
                    'action' => $data['action'] ?? null,
                    'data' => $data['data'] ?? []
                ]));
            }
        }
    }

    public function onClose(ConnectionInterface $conn): void
    {
        unset($this->clients[$conn->resourceId]);
        unset($this->users[$conn->resourceId]);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    /**
     * Broadcast notification to specific user
     */
    public function notifyUser(int $userId, array $data): void
    {
        foreach ($this->users as $resourceId => $clientUserId) {
            if ($clientUserId === $userId && isset($this->clients[$resourceId])) {
                $this->clients[$resourceId]->send(json_encode(array_merge(
                    ['type' => 'notification'],
                    $data
                )));
            }
        }
    }
}