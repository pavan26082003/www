<?php
namespace App\Repositories;

use App\Models\File;
use App\Repositories\UserRepository;

class FileRepository extends Repository
{
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM files 
            WHERE id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Find file by hash
     */
    public function findByHash(string $hash): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM files 
            WHERE file_hash = ? AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([$hash]);
        return $stmt->fetch() ?: null;
    }

    public function findByPath(int $userId, string $path): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM files 
            WHERE user_id = ? AND file_path = ? AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([$userId, $path]);
        return $stmt->fetch() ?: null;
    }

    public function findWithTrash(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM files WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getMyDriveRootFiles(int $userId, ?string $search = null): array
    {
        $sql = "
            SELECT id, original_name as display_name, 'file' as type, 
                   created_at, is_starred, cluster_id, file_size, file_type
            FROM files 
            WHERE user_id = ? AND folder_id IS NULL AND deleted_at IS NULL
            ORDER BY original_name ASC
        ";

        $params = [$userId];

        if ($search) {
            $sql = str_replace("ORDER BY", "AND original_name LIKE ? ORDER BY", $sql);
            $params[] = '%' . $search . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get PDO instance
     */
    public function getPdo(): \PDO
    {
        return $this->db;
    }

    public function getFilesByFolderId(int $folderId, int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, original_name as display_name, 'file' as type, 
                   created_at, is_starred, cluster_id, file_size, file_type
            FROM files 
            WHERE folder_id = ? AND user_id = ? AND deleted_at IS NULL
            ORDER BY original_name ASC
        ");
        $stmt->execute([$folderId, $userId]);
        return $stmt->fetchAll();
    }

    public function getStarredFiles(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, original_name as display_name, 'file' as type, 
                   created_at, is_starred, cluster_id, file_size, file_type
            FROM files 
            WHERE user_id = ? AND is_starred = 1 AND deleted_at IS NULL 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getTrashFiles(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, original_name as display_name, 'file' as type, 
                   created_at, file_size, file_type, is_starred, deleted_at, cluster_id, folder_id
            FROM files 
            WHERE user_id = ? AND deleted_at IS NOT NULL 
            AND (
                folder_id IS NULL 
                OR NOT EXISTS (
                    SELECT 1 FROM folders parent 
                    WHERE parent.id = files.folder_id 
                    AND parent.user_id = ? 
                    AND parent.deleted_at IS NOT NULL
                )
            )
            ORDER BY deleted_at DESC
        ");
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Create a new file
     */
    public function create($data): int
    {
        
        if ($data instanceof File) {
            $data = $data->toArray();
        }
        
        if (!isset($data['user_id']) || !isset($data['cluster_id']) || !isset($data['filename'])) {
            throw new \Exception('Missing required fields for file creation');
        }
        
        try {
            $fields = ['user_id', 'cluster_id', 'folder_id', 'filename', 'original_name', 
                    'file_path', 'file_size', 'file_type', 'file_category'];
            $values = [
                $data['user_id'],
                $data['cluster_id'],
                $data['folder_id'] ?? null,
                $data['filename'],
                $data['original_name'] ?? $data['filename'],
                $data['file_path'] ?? '',
                $data['file_size'] ?? 0,
                $data['file_type'] ?? null,
                $data['file_category'] ?? null
            ];
            
            // ✅ Add file_hash
            if (!empty($data['file_hash'])) {
                $fields[] = 'file_hash';
                $values[] = $data['file_hash'];
            }
            
            // ✅ Add is_reference
            if (isset($data['is_reference'])) {
                $fields[] = 'is_reference';
                $values[] = $data['is_reference'];
            }
            
            $placeholders = implode(',', array_fill(0, count($fields), '?'));
            $sql = "INSERT INTO files (" . implode(', ', $fields) . ") VALUES (" . $placeholders . ")";
            
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            $fileId = (int)$this->db->lastInsertId();
            
            
            return $fileId;
            
        } catch (\PDOException $e) {
            throw $e;
        }
    }

    /**
     * Get files for Computers cluster (show all files, including references)
     */
    public function getComputersFiles(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT f.*, c.slug as cluster_slug 
            FROM files f
            LEFT JOIN storage_clusters c ON f.cluster_id = c.id
            WHERE f.user_id = ? 
            AND f.deleted_at IS NULL
            AND c.slug = 'computers'
            ORDER BY f.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        
        $params[] = $id;
        $sql = "UPDATE files SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE files SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function restore(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE files SET deleted_at = NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function forceDelete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM files WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateLastOpened(int $fileId): bool
    {
        $stmt = $this->db->prepare("UPDATE files SET last_opened = NOW() WHERE id = ?");
        return $stmt->execute([$fileId]);
    }

    public function getFilesInFolderIds(array $folderIds, int $userId): array
    {
        if (empty($folderIds)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($folderIds), '?'));
        $params = array_merge($folderIds, [$userId]);
        
        $stmt = $this->db->prepare("
            SELECT id, file_path, file_size 
            FROM files 
            WHERE folder_id IN ($placeholders) AND user_id = ? AND deleted_at IS NULL
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get all files from multiple folder IDs (including subfolders)
     */
    public function getFilesFromFolders(array $folderIds, int $userId, bool $includeTrash = true): array
    {
        if (empty($folderIds)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($folderIds), '?'));
        $params = array_merge($folderIds, [$userId]);
        
        $sql = "
            SELECT id, folder_id, file_path, file_size 
            FROM files 
            WHERE folder_id IN ($placeholders) AND user_id = ?
        ";
        
        if (!$includeTrash) {
            $sql .= " AND deleted_at IS NULL";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get all file IDs from multiple folders
     */
    public function getFileIdsFromFolders(array $folderIds, int $userId, bool $includeTrash = true): array
    {
        if (empty($folderIds)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($folderIds), '?'));
        $params = array_merge($folderIds, [$userId]);
        
        $sql = "
            SELECT id 
            FROM files 
            WHERE folder_id IN ($placeholders) AND user_id = ?
        ";
        
        if (!$includeTrash) {
            $sql .= " AND deleted_at IS NULL";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Restore files by IDs
     */
    public function restoreFiles(array $fileIds, int $userId): int
    {
        if (empty($fileIds)) {
            return 0;
        }
        
        $placeholders = implode(',', array_fill(0, count($fileIds), '?'));
        $params = array_merge($fileIds, [$userId]);
        
        $stmt = $this->db->prepare("
            UPDATE files 
            SET deleted_at = NULL 
            WHERE id IN ($placeholders) AND user_id = ?
        ");
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Get files shared with a user
     */
    public function getSharedFiles(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                fl.id, 
                fl.original_name as display_name, 
                'file' as type, 
                fl.created_at, 
                fl.is_starred, 
                fl.cluster_id, 
                fl.file_size, 
                fl.file_type,
                fl.folder_id,
                0 as is_owner, 
                u.username as shared_by,
                si.permissions
            FROM shared_items si
            INNER JOIN files fl ON si.item_id = fl.id AND si.item_type = 'file'
            INNER JOIN users u ON si.user_id = u.id
            WHERE si.shared_with_user_id = ? 
                AND fl.deleted_at IS NULL
                AND (si.expires_at IS NULL OR si.expires_at > NOW())
        ");
        $stmt->execute([$userId]);
        $results = $stmt->fetchAll();
        
        // Map to include folder_id
        return array_map(function($row) {
            return [
                'id' => (int)$row['id'],
                'display_name' => $row['display_name'],
                'type' => $row['type'],
                'created_at' => $row['created_at'],
                'is_starred' => (bool)$row['is_starred'],
                'cluster_id' => (int)$row['cluster_id'],
                'file_size' => (int)$row['file_size'],
                'file_type' => $row['file_type'],
                'folder_id' => $row['folder_id'] ? (int)$row['folder_id'] : null,
                'is_owner' => false,
                'shared_by' => $row['shared_by'],
                'permissions' => $row['permissions']
            ];
        }, $results);
    }

    public function getStarredSharedFiles(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT fl.id, fl.original_name as display_name, 'file' as type, 
                   fl.created_at, fl.is_starred, fl.cluster_id, fl.file_size, fl.file_type,
                   0 as is_owner, u.username as shared_by, 'shared' as star_type
            FROM user_starred_items usi
            INNER JOIN files fl ON usi.item_id = fl.id AND usi.item_type = 'file'
            INNER JOIN shared_items si ON si.item_id = fl.id AND si.item_type = 'file' 
                AND si.shared_with_user_id = usi.user_id
            INNER JOIN users u ON si.user_id = u.id
            WHERE usi.user_id = ? 
                AND fl.deleted_at IS NULL
                AND (si.expires_at IS NULL OR si.expires_at > NOW())
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getFilesByClusterAndFolder(
        int $userId, 
        int $clusterId, 
        ?int $folderId, 
        ?string $search, 
        ?string $fileCondition = null
    ): array {
        $sql = "
            SELECT id, original_name as display_name, 'file' as type, 
                   created_at, is_starred, cluster_id, file_size, file_type
            FROM files 
            WHERE user_id = ? AND cluster_id = ? AND folder_id " . ($folderId ? "= ?" : "IS NULL") . " 
            AND deleted_at IS NULL
        ";

        $params = [$userId, $clusterId];
        if ($folderId !== null) {
            $params[] = $folderId;
        }

        if ($fileCondition) {
            $sql .= " $fileCondition";
        }

        if ($search) {
            $sql .= " AND original_name LIKE ?";
            $params[] = '%' . $search . '%';
        }

        $sql .= " ORDER BY original_name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Count files by user
     */
    public function countByUser(int $userId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM files 
            WHERE user_id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }

    /**
     * Get all files by user with cluster info
     * For Computers cluster, get ALL files (virtual view)
     */
    public function getAllByUser(int $userId, ?string $clusterSlug = null): array
    {
        $sql = "
            SELECT f.*, c.slug as cluster_slug 
            FROM files f
            LEFT JOIN storage_clusters c ON f.cluster_id = c.id
            WHERE f.user_id = ? 
            AND f.deleted_at IS NULL
        ";
        
        $params = [$userId];
        
        // ✅ If viewing Computers cluster, get all files
        if ($clusterSlug === 'computers') {
            // No additional filters - show all files
        } elseif ($clusterSlug !== null) {
            // Filter by specific cluster
            $sql .= " AND c.slug = ?";
            $params[] = $clusterSlug;
        }
        
        $sql .= " ORDER BY f.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }



    /**
     * Add is_reference column to files table if not exists
     */
    private function addReferenceColumn(): void
    {
        $pdo = $this->userRepository->getPdo();
        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM files LIKE 'is_reference'");
            if ($stmt->rowCount() === 0) {
                $pdo->exec("ALTER TABLE files ADD COLUMN is_reference TINYINT(1) DEFAULT 0 AFTER file_hash");
            }
        } catch (\PDOException $e) {
            // Column might already exist
        }
    }


    /**
     * Get all files by user including references (for Computers cluster view)
     */
    public function getAllFilesIncludingReferences(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT f.*, c.slug as cluster_slug 
            FROM files f
            LEFT JOIN storage_clusters c ON f.cluster_id = c.id
            WHERE f.user_id = ? AND f.deleted_at IS NULL
            ORDER BY f.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
}

