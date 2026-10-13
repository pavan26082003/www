<?php

declare(strict_types=1);

namespace NasApi\Repositories;

use PDO;

/**
 * Mobile backup item metadata used by planning and folder mapping.
 *
 * File upload metadata is still not written in this step. Folder ensure stores
 * only the logical phone-folder -> canonical NAS folder_id relationship.
 */
final class MobileBackupItemRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param array<int,string> $pathHashes
     * @return array<string,array<string,mixed>> keyed by source_path_hash
     */
    public function findByPathHashes(
        int $userId,
        int $backupDeviceId,
        array $pathHashes
    ): array {
        $pathHashes = array_values(array_unique(array_filter(
            $pathHashes,
            static function ($value): bool {
                return is_string($value) && $value !== '';
            }
        )));

        if ($pathHashes === []) {
            return [];
        }

        $placeholders = [];
        $params = [
            'user_id' => $userId,
            'backup_device_id' => $backupDeviceId,
        ];

        foreach ($pathHashes as $index => $hash) {
            $key = 'hash_' . $index;
            $placeholders[] = ':' . $key;
            $params[$key] = $hash;
        }

        $sql = 'SELECT id, user_id, backup_device_id, item_type, file_id, folder_id, '
            . 'source_root, source_relative_path, source_path_hash, source_size, '
            . 'source_hash, source_modified_at, mime_type, backup_status, '
            . 'cloud_deleted, cloud_deleted_at, backed_up_at, last_seen_at, '
            . 'created_at, updated_at '
            . 'FROM nas_backup_items '
            . 'WHERE user_id = :user_id '
            . 'AND backup_device_id = :backup_device_id '
            . 'AND source_path_hash IN (' . implode(', ', $placeholders) . ')';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!is_array($row)) {
                continue;
            }

            $hash = (string) ($row['source_path_hash'] ?? '');
            if ($hash !== '') {
                $result[$hash] = $row;
            }
        }

        return $result;
    }

    /**
     * Persist the logical phone-folder -> NAS folder mapping.
     *
     * If a future tombstone already exists (cloud_deleted=1), this method does
     * not clear it. Tombstone lifecycle enforcement is handled in the later
     * deletion/suppression phase.
     *
     * @return array<string,mixed>|null
     */
    public function upsertFolderMapping(
        int $userId,
        int $backupDeviceId,
        string $sourceRoot,
        string $sourceRelativePath,
        string $sourcePathHash,
        int $folderId
    ): ?array {
        $sql = 'INSERT INTO nas_backup_items ('
            . 'user_id, backup_device_id, item_type, file_id, folder_id, '
            . 'source_root, source_relative_path, source_path_hash, source_size, '
            . 'source_hash, source_modified_at, mime_type, backup_status, '
            . 'cloud_deleted, cloud_deleted_at, backed_up_at, last_seen_at'
            . ') VALUES ('
            . ':user_id, :backup_device_id, \'folder\', NULL, :folder_id, '
            . ':source_root, :source_relative_path, :source_path_hash, 0, '
            . 'NULL, NULL, NULL, \'uploaded\', 0, NULL, NOW(), NOW()'
            . ') ON DUPLICATE KEY UPDATE '
            . 'item_type = \'folder\', '
            . 'file_id = NULL, '
            . 'folder_id = VALUES(folder_id), '
            . 'source_root = VALUES(source_root), '
            . 'source_relative_path = VALUES(source_relative_path), '
            . 'source_size = 0, '
            . 'source_hash = NULL, '
            . 'source_modified_at = NULL, '
            . 'mime_type = NULL, '
            . 'backup_status = IF(cloud_deleted = 1, backup_status, \'uploaded\'), '
            . 'backed_up_at = IF(cloud_deleted = 1, backed_up_at, NOW()), '
            . 'last_seen_at = NOW()';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'backup_device_id' => $backupDeviceId,
            'folder_id' => $folderId,
            'source_root' => $sourceRoot,
            'source_relative_path' => $sourceRelativePath,
            'source_path_hash' => $sourcePathHash,
        ]);

        $rows = $this->findByPathHashes(
            $userId,
            $backupDeviceId,
            [$sourcePathHash]
        );

        return $rows[$sourcePathHash] ?? null;
    }


    /**
     * Update the current cloud file mapped to one logical phone source path.
     *
     * Used only after an explicit changed-source decision (replace/keep_both).
     * The mapping row id remains stable while file_id/hash/size metadata moves
     * to the selected current version.
     *
     * @return array<string,mixed>|null
     */
    public function updateFileMapping(
        int $userId,
        int $backupDeviceId,
        int $backupItemId,
        int $fileId,
        int $folderId,
        string $sourceRoot,
        string $sourceRelativePath,
        string $sourcePathHash,
        int $sourceSize,
        string $sourceHash,
        ?string $sourceModifiedAt,
        string $mimeType
    ): ?array {
        $stmt = $this->pdo->prepare(
            'UPDATE nas_backup_items SET '
            . "item_type = 'file', "
            . 'file_id = :file_id, '
            . 'folder_id = :folder_id, '
            . 'source_root = :source_root, '
            . 'source_relative_path = :source_relative_path, '
            . 'source_size = :source_size, '
            . 'source_hash = :source_hash, '
            . 'source_modified_at = :source_modified_at, '
            . 'mime_type = :mime_type, '
            . "backup_status = 'uploaded', "
            . 'cloud_deleted = 0, '
            . 'cloud_deleted_at = NULL, '
            . 'backed_up_at = NOW(), '
            . 'last_seen_at = NOW() '
            . 'WHERE id = :id '
            . 'AND user_id = :user_id '
            . 'AND backup_device_id = :backup_device_id '
            . 'AND source_path_hash = :source_path_hash '
            . 'AND cloud_deleted = 0'
        );

        $stmt->execute([
            'file_id' => $fileId,
            'folder_id' => $folderId,
            'source_root' => $sourceRoot,
            'source_relative_path' => $sourceRelativePath,
            'source_size' => $sourceSize,
            'source_hash' => $sourceHash,
            'source_modified_at' => $sourceModifiedAt,
            'mime_type' => $mimeType,
            'id' => $backupItemId,
            'user_id' => $userId,
            'backup_device_id' => $backupDeviceId,
            'source_path_hash' => $sourcePathHash,
        ]);

        $rows = $this->findByPathHashes($userId, $backupDeviceId, [$sourcePathHash]);
        return $rows[$sourcePathHash] ?? null;
    }

    /**
     * Mark every backup mapping attached to one canonical NAS file as deleted
     * from the cloud/Web side.
     *
     * Returns the number of mapping rows that exist for this file.
     */
    public function markCloudDeletedByFileId(int $userId, int $fileId): int
    {
        $countStmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM nas_backup_items '
            . 'WHERE user_id = :user_id AND file_id = :file_id'
        );
        $countStmt->execute([
            'user_id' => $userId,
            'file_id' => $fileId,
        ]);

        $count = (int) $countStmt->fetchColumn();
        if ($count === 0) {
            return 0;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE nas_backup_items SET '
            . "backup_status = 'suppressed', "
            . 'cloud_deleted = 1, '
            . 'cloud_deleted_at = NOW(), '
            . 'last_seen_at = NOW() '
            . 'WHERE user_id = :user_id AND file_id = :file_id'
        );
        $stmt->execute([
            'user_id' => $userId,
            'file_id' => $fileId,
        ]);

        return $count;
    }

    /**
     * Suppress every Mobile Backup mapping contained by one canonical NAS
     * folder tree before that tree is deleted from the Web app.
     *
     * Backup folder rows store their own folder_id and backup file rows store
     * their parent folder_id, so matching the full subtree covers both.
     *
     * @param array<int,int> $folderIds
     * @param array<int,int> $fileIds
     */
    public function markCloudDeletedByFolderTree(
        int $userId,
        array $folderIds,
        array $fileIds = []
    ): int {
        $folderIds = $this->normalizeIds($folderIds);
        $fileIds = $this->normalizeIds($fileIds);

        if ($folderIds === [] && $fileIds === []) {
            return 0;
        }

        $conditions = [];
        $params = [$userId];

        if ($folderIds !== []) {
            $conditions[] = 'folder_id IN ('
                . implode(',', array_fill(0, count($folderIds), '?'))
                . ')';

            foreach ($folderIds as $id) {
                $params[] = $id;
            }
        }

        if ($fileIds !== []) {
            $conditions[] = 'file_id IN ('
                . implode(',', array_fill(0, count($fileIds), '?'))
                . ')';

            foreach ($fileIds as $id) {
                $params[] = $id;
            }
        }

        $where = '(' . implode(' OR ', $conditions) . ')';

        $countStmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM nas_backup_items '
            . 'WHERE user_id = ? AND ' . $where
        );
        $countStmt->execute($params);

        $count = (int) $countStmt->fetchColumn();
        if ($count === 0) {
            return 0;
        }

        $updateStmt = $this->pdo->prepare(
            'UPDATE nas_backup_items SET '
            . "backup_status = 'suppressed', "
            . 'cloud_deleted = 1, '
            . 'cloud_deleted_at = NOW(), '
            . 'last_seen_at = NOW() '
            . 'WHERE user_id = ? AND ' . $where
        );
        $updateStmt->execute($params);

        return $count;
    }

    /**
     * @param array<int,mixed> $ids
     * @return array<int,int>
     */
    private function normalizeIds(array $ids): array
    {
        $normalized = [];

        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $normalized[$id] = $id;
            }
        }

        return array_values($normalized);
    }


    /** @return array<string,mixed>|null */
    public function markCloudDeleted(
        int $userId,
        int $backupDeviceId,
        int $backupItemId,
        string $sourcePathHash
    ): ?array {
        $stmt = $this->pdo->prepare(
            'UPDATE nas_backup_items SET '
            . "backup_status = 'suppressed', "
            . 'cloud_deleted = 1, '
            . 'cloud_deleted_at = NOW(), '
            . 'last_seen_at = NOW() '
            . 'WHERE id = :id '
            . 'AND user_id = :user_id '
            . 'AND backup_device_id = :backup_device_id '
            . 'AND source_path_hash = :source_path_hash'
        );
        $stmt->execute([
            'id' => $backupItemId,
            'user_id' => $userId,
            'backup_device_id' => $backupDeviceId,
            'source_path_hash' => $sourcePathHash,
        ]);

        $rows = $this->findByPathHashes($userId, $backupDeviceId, [$sourcePathHash]);
        return $rows[$sourcePathHash] ?? null;
    }

    /**
     * Re-enable one explicitly suppressed phone source for backup.
     *
     * The old NAS file may still exist in Trash. Detach file_id so planning
     * and upload do not immediately interpret that Trash row as another cloud
     * deletion. The logical nas_backup_items row id is intentionally retained.
     *
     * @return array<string,mixed>|null
     */
    public function enableBackupAgain(
        int $userId,
        int $backupDeviceId,
        int $backupItemId,
        string $sourcePathHash
    ): ?array {
        $stmt = $this->pdo->prepare(
            'UPDATE nas_backup_items SET '
            . 'file_id = NULL, '
            . "backup_status = 'pending', "
            . 'cloud_deleted = 0, '
            . 'cloud_deleted_at = NULL, '
            . 'backed_up_at = NULL, '
            . 'last_seen_at = NOW() '
            . 'WHERE id = :id '
            . 'AND user_id = :user_id '
            . 'AND backup_device_id = :backup_device_id '
            . 'AND source_path_hash = :source_path_hash '
            . "AND item_type = 'file'"
        );
        $stmt->execute([
            'id' => $backupItemId,
            'user_id' => $userId,
            'backup_device_id' => $backupDeviceId,
            'source_path_hash' => $sourcePathHash,
        ]);

        $rows = $this->findByPathHashes($userId, $backupDeviceId, [$sourcePathHash]);
        return $rows[$sourcePathHash] ?? null;
    }

    /** @return array<string,mixed>|null */
    public function createFileMapping(
        int $userId,
        int $backupDeviceId,
        int $fileId,
        int $folderId,
        string $sourceRoot,
        string $sourceRelativePath,
        string $sourcePathHash,
        int $sourceSize,
        string $sourceHash,
        ?string $sourceModifiedAt,
        string $mimeType
    ): ?array {
        $stmt = $this->pdo->prepare(
            'INSERT INTO nas_backup_items ('
            . 'user_id, backup_device_id, item_type, file_id, folder_id, '
            . 'source_root, source_relative_path, source_path_hash, source_size, '
            . 'source_hash, source_modified_at, mime_type, backup_status, '
            . 'cloud_deleted, cloud_deleted_at, backed_up_at, last_seen_at'
            . ') VALUES ('
            . ':user_id, :backup_device_id, \'file\', :file_id, :folder_id, '
            . ':source_root, :source_relative_path, :source_path_hash, :source_size, '
            . ':source_hash, :source_modified_at, :mime_type, \'uploaded\', '
            . '0, NULL, NOW(), NOW()'
            . ')'
        );
        $stmt->execute([
            'user_id' => $userId,
            'backup_device_id' => $backupDeviceId,
            'file_id' => $fileId,
            'folder_id' => $folderId,
            'source_root' => $sourceRoot,
            'source_relative_path' => $sourceRelativePath,
            'source_path_hash' => $sourcePathHash,
            'source_size' => $sourceSize,
            'source_hash' => $sourceHash,
            'source_modified_at' => $sourceModifiedAt,
            'mime_type' => $mimeType,
        ]);

        $rows = $this->findByPathHashes($userId, $backupDeviceId, [$sourcePathHash]);
        return $rows[$sourcePathHash] ?? null;
    }

}
