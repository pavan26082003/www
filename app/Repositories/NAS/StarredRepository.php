<?php
namespace App\Repositories\NAS;

class StarredRepository extends Repository
{
    public function isStarred(int $userId, string $itemType, int $itemId): bool
    {
        $stmt = $this->db->prepare("
            SELECT id FROM user_starred_items 
            WHERE user_id = ? AND item_type = ? AND item_id = ?
        ");
        $stmt->execute([$userId, $itemType, $itemId]);
        return (bool) $stmt->fetch();
    }

    public function star(int $userId, string $itemType, int $itemId): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO user_starred_items (user_id, item_type, item_id) 
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$userId, $itemType, $itemId]);
    }

    public function unstar(int $userId, string $itemType, int $itemId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM user_starred_items 
            WHERE user_id = ? AND item_type = ? AND item_id = ?
        ");
        return $stmt->execute([$userId, $itemType, $itemId]);
    }

    public function deleteForItem(int $itemId, string $itemType, int $userId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM user_starred_items 
            WHERE user_id = ? AND item_type = ? AND item_id = ?
        ");
        return $stmt->execute([$userId, $itemType, $itemId]);
    }

    
}