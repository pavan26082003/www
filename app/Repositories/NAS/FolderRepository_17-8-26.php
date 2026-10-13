<?php
namespace App\Repositories\NAS;

use App\Models\NAS\Folder;

class FolderRepository extends Repository
{
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM folders 
            WHERE id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findWithTrash(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM folders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get every active folder owned by a user, including its cluster slug.
     * Used by cloud -> local sync so empty folders can be materialized locally.
     */
    public function getAllByUserWithCluster(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT f.*, c.slug AS cluster_slug
            FROM folders f
            LEFT JOIN storage_clusters c ON c.id = f.cluster_id
            WHERE f.user_id = ?
              AND f.deleted_at IS NULL
            ORDER BY f.id ASC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getRootFolders(int $userId, int $clusterId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, name as display_name, 'folder' as type, created_at, 
                   is_starred, cluster_id, NULL as file_size, NULL as file_type
            FROM folders 
            WHERE user_id = ? AND cluster_id = ? AND parent_id IS NULL AND deleted_at IS NULL 
            ORDER BY name ASC
        ");
        $stmt->execute([$userId, $clusterId]);
        return $stmt->fetchAll();
    }

    public function getMyDriveRootFolders(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, name as display_name, 'folder' as type, created_at, 
                   is_starred, cluster_id, NULL as file_size, NULL as file_type
            FROM folders 
            WHERE user_id = ? AND parent_id IS NULL AND deleted_at IS NULL 
            ORDER BY name ASC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getSubfolders(?int $folderId, int $userId): array
{
    if ($folderId === null) {
        // Get root folders (parent_id IS NULL)
        $stmt = $this->db->prepare("
            SELECT id, name as display_name, 'folder' as type, created_at, 
                   is_starred, cluster_id, NULL as file_size, NULL as file_type
            FROM folders 
            WHERE user_id = ? AND parent_id IS NULL AND deleted_at IS NULL
            ORDER BY name ASC
        ");
        $stmt->execute([$userId]);
    } else {
        // Get subfolders of specific folder
        $stmt = $this->db->prepare("
            SELECT id, name as display_name, 'folder' as type, created_at, 
                   is_starred, cluster_id, NULL as file_size, NULL as file_type
            FROM folders 
            WHERE user_id = ? AND parent_id = ? AND deleted_at IS NULL
            ORDER BY name ASC
        ");
        $stmt->execute([$userId, $folderId]);
    }
    
    return $stmt->fetchAll();
}

    public function getStarredFolders(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, name as display_name, 'folder' as type, created_at, 
                   is_starred, cluster_id, NULL as file_size, NULL as file_type
            FROM folders 
            WHERE user_id = ? AND is_starred = 1 AND deleted_at IS NULL 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getTrashFolders(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, name as display_name, 'folder' as type, created_at, 
                   is_starred, deleted_at, cluster_id, NULL as file_size, NULL as file_type, NULL as folder_id
            FROM folders f
            WHERE user_id = ? AND deleted_at IS NOT NULL 
            AND (
                parent_id IS NULL 
                OR NOT EXISTS (
                    SELECT 1 FROM folders parent 
                    WHERE parent.id = f.parent_id 
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
 * Create a new folder
 * Accepts array data directly
 */
public function create(array $data): int
{
    // Ensure we have the required fields
    if (!isset($data['user_id']) || !isset($data['cluster_id']) || !isset($data['name'])) {
        throw new \Exception('Missing required fields for folder creation');
    }
    
    $sql = "INSERT INTO folders (user_id, cluster_id, name";
    $params = [$data['user_id'], $data['cluster_id'], $data['name']];
    
    if (isset($data['parent_id']) && $data['parent_id'] !== null) {
        $sql .= ", parent_id";
        $params[] = $data['parent_id'];
    }
    
    $sql .= ") VALUES (" . implode(',', array_fill(0, count($params), '?')) . ")";
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return (int)$this->db->lastInsertId();
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
        $sql = "UPDATE folders SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE folders SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function restore(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE folders SET deleted_at = NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function forceDelete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM folders WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getSubfolderIdsRecursive(int $parentId, int $userId, bool $includeTrash = false): array
    {
        $result = [];
        
        $sql = "SELECT id FROM folders WHERE parent_id = ? AND user_id = ?";
        if (!$includeTrash) {
            $sql .= " AND deleted_at IS NULL";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$parentId, $userId]);
        $subfolders = $stmt->fetchAll();
        
        foreach ($subfolders as $sub) {
            $result[] = (int)$sub['id'];
            $result = array_merge($result, $this->getSubfolderIdsRecursive($sub['id'], $userId, $includeTrash));
        }
        
        return $result;
    }

    

    public function getAllSubfolderIds(int $folderId, int $userId, bool $includeTrash = false): array
    {
        $allFolderIds = $this->getSubfolderIdsRecursive($folderId, $userId, $includeTrash);
        $allFolderIds[] = $folderId;
        return $allFolderIds;
    }

    public function bulkRestore(array $folderIds, int $userId): int
    {
        if (empty($folderIds)) {
            return 0;
        }
        
        $placeholders = implode(',', array_fill(0, count($folderIds), '?'));
        $params = array_merge($folderIds, [$userId]);
        
        $stmt = $this->db->prepare("
            UPDATE folders 
            SET deleted_at = NULL 
            WHERE id IN ($placeholders) AND user_id = ?
        ");
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function bulkDelete(array $folderIds, int $userId): int
    {
        if (empty($folderIds)) {
            return 0;
        }
        
        $placeholders = implode(',', array_fill(0, count($folderIds), '?'));
        $params = array_merge($folderIds, [$userId]);
        
        $stmt = $this->db->prepare("
            DELETE FROM folders 
            WHERE id IN ($placeholders) AND user_id = ?
        ");
        $stmt->execute($params);
        return $stmt->rowCount();
    }


    /**
     * Get folders shared with a user
     */
    public function getSharedFolders(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                f.id, 
                f.name as display_name, 
                'folder' as type, 
                f.created_at, 
                f.is_starred, 
                f.cluster_id, 
                f.parent_id,
                NULL as file_size, 
                NULL as file_type,
                0 as is_owner, 
                u.username as shared_by,
                si.permissions
            FROM shared_items si
            INNER JOIN folders f ON si.item_id = f.id AND si.item_type = 'folder'
            INNER JOIN users u ON si.user_id = u.id
            WHERE si.shared_with_user_id = ? 
                AND f.deleted_at IS NULL
                AND (si.expires_at IS NULL OR si.expires_at > NOW())
        ");
        $stmt->execute([$userId]);
        $results = $stmt->fetchAll();
        
        // Map to include parent_id
        return array_map(function($row) {
            return [
                'id' => (int)$row['id'],
                'display_name' => $row['display_name'],
                'type' => $row['type'],
                'created_at' => $row['created_at'],
                'is_starred' => (bool)$row['is_starred'],
                'cluster_id' => (int)$row['cluster_id'],
                'parent_id' => $row['parent_id'] ? (int)$row['parent_id'] : null,
                'file_size' => null,
                'file_type' => null,
                'is_owner' => false,
                'shared_by' => $row['shared_by'],
                'permissions' => $row['permissions'],
                'depth' => 0,
                'children' => []
            ];
        }, $results);
    }

    public function getStarredSharedFolders(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT f.id, f.name as display_name, 'folder' as type, f.created_at, 
                   f.is_starred, f.cluster_id, NULL as file_size, NULL as file_type,
                   0 as is_owner, u.username as shared_by, 'shared' as star_type
            FROM user_starred_items usi
            INNER JOIN folders f ON usi.item_id = f.id AND usi.item_type = 'folder'
            INNER JOIN shared_items si ON si.item_id = f.id AND si.item_type = 'folder' 
                AND si.shared_with_user_id = usi.user_id
            INNER JOIN users u ON si.user_id = u.id
            WHERE usi.user_id = ? 
                AND f.deleted_at IS NULL
                AND (si.expires_at IS NULL OR si.expires_at > NOW())
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

        /**
     * Find duplicate folder
     */
    public function findDuplicate(int $userId, int $clusterId, ?int $parentId, string $name): ?array
    {
        if ($parentId === null) {
            $stmt = $this->db->prepare("
                SELECT id FROM folders 
                WHERE user_id = ? AND cluster_id = ? AND parent_id IS NULL AND name = ? AND deleted_at IS NULL
            ");
            $stmt->execute([$userId, $clusterId, $name]);
        } else {
            $stmt = $this->db->prepare("
                SELECT id FROM folders 
                WHERE user_id = ? AND cluster_id = ? AND parent_id = ? AND name = ? AND deleted_at IS NULL
            ");
            $stmt->execute([$userId, $clusterId, $parentId, $name]);
        }
        
        return $stmt->fetch() ?: null;
    }

}
