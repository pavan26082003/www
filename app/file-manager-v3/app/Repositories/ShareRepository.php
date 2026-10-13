<?php
namespace App\Repositories;

class ShareRepository extends Repository
{
    public function findForUserAndItem(int $userId, string $itemType, int $itemId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM shared_items 
            WHERE item_type = ? AND item_id = ? AND shared_with_user_id = ?
            AND (expires_at IS NULL OR expires_at > NOW())
        ");
        $stmt->execute([$itemType, $itemId, $userId]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        // Ensure we have the required fields
        if (!isset($data['user_id']) || !isset($data['item_type']) || !isset($data['item_id']) || !isset($data['shared_with_user_id'])) {
            throw new \Exception('Missing required fields for share creation');
        }
        
        $stmt = $this->db->prepare("
            INSERT INTO shared_items (user_id, item_type, item_id, shared_with_user_id, permissions, expires_at) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['user_id'],
            $data['item_type'],
            $data['item_id'],
            $data['shared_with_user_id'],
            $data['permissions'] ?? 'view',
            $data['expires_at'] ?? null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $shareId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM shared_items WHERE id = ?");
        return $stmt->execute([$shareId]);
    }

    public function deleteForItem(int $itemId, string $itemType, int $sharedWithUserId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM shared_items 
            WHERE item_type = ? AND item_id = ? AND shared_with_user_id = ?
        ");
        return $stmt->execute([$itemType, $itemId, $sharedWithUserId]);
    }

    /**
     * Get shares by item with user information
     */
    public function getByItem(int $itemId, string $itemType): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                si.*,
                u.id as shared_with_user_id,
                u.username as shared_with_username,
                u.email as shared_with_email,
                u2.id as owner_user_id,
                u2.username as owner_username
            FROM shared_items si
            LEFT JOIN users u ON si.shared_with_user_id = u.id
            LEFT JOIN users u2 ON si.user_id = u2.id
            WHERE si.item_type = ? AND si.item_id = ?
            AND (si.expires_at IS NULL OR si.expires_at > NOW())
        ");
        $stmt->execute([$itemType, $itemId]);
        $results = $stmt->fetchAll();
        
        // Format the results
        return array_map(function($row) {
            return [
                'share_id' => (int)$row['id'],
                'item_type' => $row['item_type'],
                'item_id' => (int)$row['item_id'],
                'permissions' => $row['permissions'],
                'expires_at' => $row['expires_at'],
                'created_at' => $row['created_at'],
                'shared_with' => [
                    'id' => (int)$row['shared_with_user_id'],
                    'username' => $row['shared_with_username'] ?? 'Unknown',
                    'email' => $row['shared_with_email'] ?? ''
                ],
                'owner' => [
                    'id' => (int)$row['owner_user_id'],
                    'username' => $row['owner_username'] ?? 'Unknown'
                ]
            ];
        }, $results);
    }

    /**
     * Get shares for a user (items shared with them)
     */
    public function getSharedWithMe(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                si.*,
                u.id as owner_user_id,
                u.username as owner_username,
                u.email as owner_email,
                CASE 
                    WHEN si.item_type = 'folder' THEN f.name 
                    ELSE fl.original_name 
                END as item_name,
                f.user_id as folder_owner_id,
                fl.user_id as file_owner_id
            FROM shared_items si
            LEFT JOIN users u ON si.user_id = u.id
            LEFT JOIN folders f ON si.item_type = 'folder' AND si.item_id = f.id AND f.deleted_at IS NULL
            LEFT JOIN files fl ON si.item_type = 'file' AND si.item_id = fl.id AND fl.deleted_at IS NULL
            WHERE si.shared_with_user_id = ?
            AND (si.expires_at IS NULL OR si.expires_at > NOW())
            ORDER BY si.created_at DESC
        ");
        $stmt->execute([$userId]);
        $results = $stmt->fetchAll();
        
        return array_map(function($row) {
            return [
                'share_id' => (int)$row['id'],
                'item_type' => $row['item_type'],
                'item_id' => (int)$row['item_id'],
                'item_name' => $row['item_name'] ?? 'Unknown',
                'permissions' => $row['permissions'],
                'expires_at' => $row['expires_at'],
                'created_at' => $row['created_at'],
                'owner' => [
                    'id' => (int)$row['user_id'],
                    'username' => $row['owner_username'] ?? 'Unknown',
                    'email' => $row['owner_email'] ?? ''
                ],
                'can_download' => in_array($row['permissions'], ['download', 'edit', 'share', 'full']),
                'can_edit' => in_array($row['permissions'], ['edit', 'full']),
                'can_share' => in_array($row['permissions'], ['share', 'full'])
            ];
        }, $results);
    }
    /**
     * Find share by ID
     */
    public function find(int $shareId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM shared_items WHERE id = ?");
        $stmt->execute([$shareId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get PDO instance
     */
    public function getPdo(): \PDO
    {
        return $this->db;
    }
}