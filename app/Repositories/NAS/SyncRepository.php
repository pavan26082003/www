<?php
namespace App\Repositories\NAS;

use App\Models\NAS\SyncSession;
use App\Models\NAS\SyncQueue;

class SyncRepository extends Repository
{
    /**
     * Create a new sync session
     */
    public function createSession(int $userId): int
    {
        $sessionId = uniqid('sync_', true);
        
        $stmt = $this->db->prepare("
            INSERT INTO sync_sessions (user_id, session_id, status, started_at) 
            VALUES (?, ?, 'running', NOW())
        ");
        $stmt->execute([$userId, $sessionId]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Get active session for user
     */
    public function getActiveSession(int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM sync_sessions 
            WHERE user_id = ? AND status IN ('running', 'idle')
            ORDER BY started_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get session by ID
     */
    public function getSession(int $sessionId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM sync_sessions WHERE id = ?");
        $stmt->execute([$sessionId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get session by session_id
     */
    public function getSessionBySessionId(string $sessionId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM sync_sessions WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Update session status
     */
    public function updateSessionStatus(int $sessionId, string $status, ?string $errorMessage = null): bool
    {
        $validStatuses = ['running', 'completed', 'failed', 'idle'];
        $finalStatus = in_array($status, $validStatuses, true) ? $status : 'idle';

        $sql = "UPDATE sync_sessions SET status = ?";
        $params = [$finalStatus];

        // Only terminal states should stamp completed_at. Returning to running/idle
        // must not make the session look completed.
        if (in_array($finalStatus, ['completed', 'failed'], true)) {
            $sql .= ", completed_at = NOW()";
        } else {
            $sql .= ", completed_at = NULL";
        }

        if ($errorMessage !== null) {
            $sql .= ", error_message = ?";
            $params[] = $errorMessage;
        } elseif ($finalStatus !== 'failed') {
            $sql .= ", error_message = NULL";
        }

        $sql .= " WHERE id = ?";
        $params[] = $sessionId;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Update sync progress
     */
    public function updateProgress(int $sessionId, int $filesSynced, int $totalFiles): bool
    {
        $stmt = $this->db->prepare("
            UPDATE sync_sessions 
            SET files_synced = ?, total_files = ?, last_sync_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$filesSynced, $totalFiles, $sessionId]);
    }

    /**
     * Add item to sync queue
     */
    public function addToQueue(int $userId, string $sessionId, string $itemType, int $itemId, string $action): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO sync_queue (user_id, session_id, item_type, item_id, action, status, created_at) 
            VALUES (?, ?, ?, ?, ?, 'pending', NOW())
        ");
        $stmt->execute([$userId, $sessionId, $itemType, $itemId, $action]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Get pending queue items
     */
    public function getPendingQueueItems(string $sessionId, int $limit = 100): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM sync_queue 
            WHERE session_id = ? AND status = 'pending'
            ORDER BY created_at ASC
            LIMIT ?
        ");
        $stmt->execute([$sessionId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Update queue item status
     */
    public function updateQueueItemStatus(int $queueId, string $status, ?string $errorMessage = null): bool
    {
        $validStatuses = ['pending', 'processing', 'completed', 'failed'];
        $finalStatus = in_array($status, $validStatuses, true) ? $status : 'pending';

        $sql = "UPDATE sync_queue SET status = ?";
        $params = [$finalStatus];

        if (in_array($finalStatus, ['completed', 'failed'], true)) {
            $sql .= ", processed_at = NOW()";
        } else {
            $sql .= ", processed_at = NULL";
        }

        if ($errorMessage !== null) {
            $sql .= ", error_message = ?";
            $params[] = $errorMessage;
        } elseif ($finalStatus !== 'failed') {
            $sql .= ", error_message = NULL";
        }

        $sql .= " WHERE id = ?";
        $params[] = $queueId;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Get sync history for user
     */
    public function getHistory(int $userId, int $limit = 20): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM sync_sessions 
            WHERE user_id = ? 
            ORDER BY started_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }


    

    /**
     * Clear completed sessions older than 7 days
     */
    public function clearOldSessions(): int
    {
        $stmt = $this->db->prepare("
            DELETE FROM sync_sessions 
            WHERE status IN ('completed', 'failed') 
            AND completed_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $stmt->execute();
        return $stmt->rowCount();
    }

    

    /**
     * Get sync folder for user
     */
    public function getSyncFolder(int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM sync_folders 
            WHERE user_id = ? 
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Create sync folder
     */
    public function createSyncFolder(int $userId, string $localPath, string $folderName, bool $isAuto = false): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO sync_folders (user_id, local_path, folder_name, is_auto, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $localPath, $folderName, $isAuto ? 1 : 0]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Update sync folder last sync time
     */
    public function updateSyncFolderLastSync(int $folderId): bool
    {
        $stmt = $this->db->prepare("UPDATE sync_folders SET last_sync = NOW() WHERE id = ?");
        return $stmt->execute([$folderId]);
    }

    /**
     * Count synced files for user from sync_status table
     */
    public function countSyncedFiles(int $userId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM sync_status
            WHERE user_id = ?
              AND file_id IS NOT NULL
              AND sync_status = 'synced'
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }

    /**
     * Get queue statistics for session
     */
    public function getQueueStats(string $sessionId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
            FROM sync_queue 
            WHERE session_id = ?
        ");
        $stmt->execute([$sessionId]);
        $result = $stmt->fetch();
        return $result ?: ['total' => 0, 'pending' => 0, 'processing' => 0, 'completed' => 0, 'failed' => 0];
    }

    /**
     * Get sync status for user
     */
    public function getSyncStatus(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM sync_status
            WHERE user_id = ? AND file_id IS NULL
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: [];
    }

    /**
     * Update sync status
     */
    public function updateSyncStatus(int $userId, string $status, ?string $errorMessage = null): bool
    {
        $validStatuses = [
            'pending', 'synced', 'uploading', 'downloading', 'error',
            'idle', 'running', 'completed', 'failed'
        ];
        $finalStatus = in_array($status, $validStatuses, true) ? $status : 'pending';

        $stmt = $this->db->prepare("
            SELECT id FROM sync_status
            WHERE user_id = ? AND file_id IS NULL
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE sync_status
                SET sync_status = ?, error_message = ?, updated_at = NOW()
                WHERE id = ?
            ");
            return $stmt->execute([
                $finalStatus,
                $errorMessage,
                (int)$existing['id']
            ]);
        }

        $defaultHash = hash('sha256', 'sync_' . $userId . '_' . time());
        $stmt = $this->db->prepare("
            INSERT INTO sync_status (
                user_id, file_id, sync_status, error_message,
                local_path, file_hash, file_size,
                modified_time, created_at
            )
            VALUES (?, NULL, ?, ?, ?, ?, 0, NOW(), NOW())
        ");

        return $stmt->execute([
            $userId,
            $finalStatus,
            $errorMessage,
            '/',
            $defaultHash
        ]);
    }

    /**
     * Add sync log entry
     */
    public function addLog(int $userId, string $syncType, string $filePath, ?string $fileHash, ?string $message = null): int
    {
        // sync_logs.sync_type is ENUM('upload','download','skip','error').
        // Normalize and guard here so no caller can make the whole sync fail
        // with MySQL error 1265 by passing a synthetic/legacy value.
        $normalizedType = strtolower(trim($syncType));
        $validTypes = ['upload', 'download', 'skip', 'error'];

        if (!in_array($normalizedType, $validTypes, true)) {
            $invalidType = $normalizedType !== '' ? $normalizedType : '(empty)';
            $normalizedType = 'error';
            $message = trim(
                'Invalid sync log type [' . $invalidType . '] normalized to error. ' .
                (string) $message
            );
        }

        $stmt = $this->db->prepare("
            INSERT INTO sync_logs (user_id, sync_type, file_path, file_hash, message, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $normalizedType, $filePath, $fileHash, $message]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Find files that exist in cloud but not locally
     */
    public function findDeletedFiles(int $userId, array $localFiles): array
    {
        if (empty($localFiles)) {
            // Get all synced files for user
            $stmt = $this->db->prepare("
                SELECT s.file_id 
                FROM sync_status s
                WHERE s.user_id = ? AND s.file_id IS NOT NULL AND s.sync_status = 'synced'
            ");
            $stmt->execute([$userId]);
            $results = $stmt->fetchAll();
            return array_column($results, 'file_id');
        }

        // Get local file paths
        $localPaths = array_column($localFiles, 'relative_path');
        $placeholders = implode(',', array_fill(0, count($localPaths), '?'));

        // ✅ Fix: Use local_path from sync_status table
        $stmt = $this->db->prepare("
            SELECT s.file_id 
            FROM sync_status s
            WHERE s.user_id = ? 
            AND s.file_id IS NOT NULL
            AND s.sync_status = 'synced'
            AND s.local_path NOT IN ($placeholders)
        ");
        $stmt->execute(array_merge([$userId], $localPaths));
        $results = $stmt->fetchAll();
        return array_column($results, 'file_id');
    }
    
    /**
     * Add file to sync status
     */
    public function addSyncStatus(int $userId, int $fileId, string $localPath, ?string $fileHash, int $fileSize, string $status = 'pending'): int
    {
        $validStatuses = ['pending', 'synced', 'uploading', 'downloading', 'error'];
        $finalStatus = in_array($status, $validStatuses) ? $status : 'pending';
        
        // ✅ Handle null hash - generate a default one
        if (empty($fileHash)) {
            $fileHash = hash('sha256', $localPath . $fileId . time());
        }
        
        // Check if file already exists
        $stmt = $this->db->prepare("
            SELECT id FROM sync_status 
            WHERE user_id = ? AND file_id = ?
        ");
        $stmt->execute([$userId, $fileId]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Update existing
            $stmt = $this->db->prepare("
                UPDATE sync_status 
                SET local_path = ?, file_hash = ?, file_size = ?, 
                    sync_status = ?, modified_time = NOW(), updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$localPath, $fileHash, $fileSize, $finalStatus, $existing['id']]);
            return (int)$existing['id'];
        } else {
            // Insert new - with all required fields
            $stmt = $this->db->prepare("
                INSERT INTO sync_status (
                    user_id, file_id, local_path, file_hash, file_size, 
                    sync_status, modified_time, created_at
                ) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([$userId, $fileId, $localPath, $fileHash, $fileSize, $finalStatus]);
            return (int)$this->db->lastInsertId();
        }
    }

    /**
     * Get unsynced files for user
     */
    public function getUnsyncedFiles(int $userId, int $limit = 100): array
    {
        $stmt = $this->db->prepare("
            SELECT
                s.id,
                s.file_id,
                s.local_path as path,
                s.file_hash,
                s.file_size as size,
                s.sync_status as status,
                s.error_message,
                f.original_name as name,
                f.file_path as cloud_path
            FROM sync_status s
            INNER JOIN files f ON s.file_id = f.id
            WHERE s.user_id = ?
              AND s.file_id IS NOT NULL
              AND s.sync_status IN ('pending', 'error')
              AND f.deleted_at IS NULL
            ORDER BY s.created_at ASC
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        $results = $stmt->fetchAll();
        return $results ?: [];
    }

    /**
     * Get all sync folders for user
     */
    public function getSyncFolders(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM sync_folders 
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get sync logs for user
     */
    public function getLogs(int $userId, int $limit = 50): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM sync_logs 
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Update sync status for a file
     */
    public function updateFileSyncStatus(int $syncStatusId, string $status, ?string $errorMessage = null): bool
    {
        $validStatuses = ['pending', 'synced', 'uploading', 'downloading', 'error'];
        $finalStatus = in_array($status, $validStatuses) ? $status : 'pending';
        
        $sql = "UPDATE sync_status SET sync_status = ?, updated_at = NOW()";
        $params = [$finalStatus];
        
        if ($errorMessage !== null) {
            $sql .= ", error_message = ?";
            $params[] = $errorMessage;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $syncStatusId;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete sync status for a file
     */
    public function deleteSyncStatus(int $fileId, int $userId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM sync_status 
            WHERE file_id = ? AND user_id = ?
        ");
        return $stmt->execute([$fileId, $userId]);
    }

    /**
     * Get sync status for a specific file
     */
    public function getFileSyncStatus(int $fileId, int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM sync_status 
            WHERE file_id = ? AND user_id = ?
        ");
        $stmt->execute([$fileId, $userId]);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * Get all synced files for a user
     */
    public function getSyncedFiles(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, f.original_name, f.file_path 
            FROM sync_status s
            INNER JOIN files f ON s.file_id = f.id
            WHERE s.user_id = ? 
            AND s.file_id IS NOT NULL
            AND s.sync_status = 'synced'
            AND f.deleted_at IS NULL
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
