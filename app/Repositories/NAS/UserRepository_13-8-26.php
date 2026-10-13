<?php

namespace App\Repositories\NAS;

use App\Models\NAS\User;

class UserRepository extends Repository
{
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }


    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE username = ?
            LIMIT 1
        ");

        $stmt->execute([$username]);

        return $stmt->fetch() ?: null;
    }


    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        return $stmt->fetch() ?: null;
    }


    /**
     * Find the NAS user mapped to an Efactor user.
     */
    public function findByEfactorUserId(int $efactorUserId): ?array
    {
        if ($efactorUserId <= 0) {
            return null;
        }

        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE efactor_user_id = ?
            LIMIT 1
        ");

        $stmt->execute([$efactorUserId]);

        return $stmt->fetch() ?: null;
    }


    /**
     * Link an existing NAS account to an Efactor user.
     *
     * This does NOT change the NAS user ID.
     * Therefore existing files/folders/shares remain attached
     * to the same NAS account.
     */
    public function linkEfactorUser(
        int $nasUserId,
        int $efactorUserId
    ): bool {
        if ($nasUserId <= 0 || $efactorUserId <= 0) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE users
            SET efactor_user_id = ?
            WHERE id = ?
              AND (
                    efactor_user_id IS NULL
                    OR efactor_user_id = ?
                  )
        ");

        $stmt->execute([
            $efactorUserId,
            $nasUserId,
            $efactorUserId,
        ]);

        /*
         * rowCount() can be zero if the value was already the same.
         * Verify the mapping instead.
         */
        $user = $this->find($nasUserId);

        return $user
            && isset($user['efactor_user_id'])
            && (int) $user['efactor_user_id'] === $efactorUserId;
    }


    public function updateLastLogin(int $userId): bool
    {
        if ($userId <= 0) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE users
            SET last_login = CURRENT_TIMESTAMP
            WHERE id = ?
        ");

        return $stmt->execute([$userId]);
    }


    public function getStorageInfo(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT storage_used, storage_limit
            FROM users
            WHERE id = ?
        ");

        $stmt->execute([$userId]);

        $result = $stmt->fetch();

        if (!$result) {
            return [
                'storage_used' => 0,
                'storage_limit' => 10737418240,
            ];
        }

        return $result;
    }


    public function updateStorageUsed(
        int $userId,
        int $size,
        string $operation = 'add'
    ): bool {
        $sql = $operation === 'add'
            ? "
                UPDATE users
                SET storage_used = storage_used + ?
                WHERE id = ?
              "
            : "
                UPDATE users
                SET storage_used =
                    GREATEST(storage_used - ?, 0)
                WHERE id = ?
              ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $size,
            $userId,
        ]);
    }


    public function search(
        string $query,
        int $excludeUserId
    ): array {
        $stmt = $this->db->prepare("
            SELECT
                id,
                username,
                email
            FROM users
            WHERE (
                    username LIKE ?
                    OR email LIKE ?
                  )
              AND id != ?
            LIMIT 10
        ");

        $searchTerm = '%' . $query . '%';

        $stmt->execute([
            $searchTerm,
            $searchTerm,
            $excludeUserId,
        ]);

        return $stmt->fetchAll();
    }


    /**
     * Create NAS user.
     *
     * Works for:
     * - normal NAS registration
     * - automatically provisioned Efactor users
     */
    public function create(array $data): int
    {
        $passwordField =
            isset($data['password_hash'])
                ? 'password_hash'
                : 'password';

        $fields = [
            'username',
            'email',
            $passwordField,
        ];

        $values = [
            $data['username'],
            $data['email'],
            $data['password_hash']
                ?? $data['password']
                ?? '',
        ];

        /*
         * Only include Efactor mapping when supplied.
         *
         * Normal NAS registration remains unaffected.
         */
        if (
            array_key_exists(
                'efactor_user_id',
                $data
            )
            && $data['efactor_user_id'] !== null
        ) {
            $fields[] = 'efactor_user_id';

            $values[] =
                (int) $data['efactor_user_id'];
        }

        $placeholders =
            array_fill(
                0,
                count($fields),
                '?'
            );

        $sql =
            "INSERT INTO users (" .
            implode(', ', $fields) .
            ") VALUES (" .
            implode(', ', $placeholders) .
            ")";

        $stmt = $this->db->prepare($sql);

        $stmt->execute($values);

        return (int) $this->db->lastInsertId();
    }
}