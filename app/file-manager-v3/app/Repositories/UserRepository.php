<?php
namespace App\Repositories;

use App\Models\User;

class UserRepository extends Repository
{
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function getStorageInfo(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT storage_used, storage_limit FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        if (!$result) {
            return ['storage_used' => 0, 'storage_limit' => 10737418240];
        }
        
        return $result;
    }

    public function updateStorageUsed(int $userId, int $size, string $operation = 'add'): bool
    {
        $sql = $operation === 'add' 
            ? "UPDATE users SET storage_used = storage_used + ? WHERE id = ?"
            : "UPDATE users SET storage_used = storage_used - ? WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$size, $userId]);
    }

    public function search(string $query, int $excludeUserId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, username, email 
            FROM users 
            WHERE (username LIKE ? OR email LIKE ?) AND id != ?
            LIMIT 10
        ");
        $searchTerm = '%' . $query . '%';
        $stmt->execute([$searchTerm, $searchTerm, $excludeUserId]);
        return $stmt->fetchAll();
    }

     public function create(array $data): int
    {
        // Check if we have password_hash or password field
        $passwordField = isset($data['password_hash']) ? 'password_hash' : 'password';
        
        // Build SQL dynamically
        $fields = ['username', 'email', $passwordField];
        $placeholders = array_fill(0, count($fields), '?');
        $values = [
            $data['username'],
            $data['email'],
            $data['password_hash'] ?? $data['password'] ?? ''
        ];
        
        $sql = "INSERT INTO users (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);
        return (int)$this->db->lastInsertId();
    }
}