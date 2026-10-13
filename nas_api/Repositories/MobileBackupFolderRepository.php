<?php

declare(strict_types=1);

namespace NasApi\Repositories;

use PDO;

/**
 * Mobile-backup folder lookup helper.
 *
 * Creation remains owned by the existing App\Services\NAS\FolderService.
 * This repository only resolves the Mobile Backup cluster mapping and checks
 * whether a folder node already exists so /backup/folders/ensure is idempotent.
 */
final class MobileBackupFolderRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** @return array<string,mixed>|null */
    public function findClusterForUser(int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT sc.id, sc.name, sc.slug '
            . 'FROM user_clusters uc '
            . 'INNER JOIN storage_clusters sc ON sc.id = uc.cluster_id '
            . 'WHERE uc.user_id = :user_id '
            . 'AND uc.is_active = 1 '
            . "AND sc.slug = 'mobile-backup' "
            . 'LIMIT 1'
        );
        $stmt->execute(['user_id' => $userId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }

    /** @return array<string,mixed>|null */
    public function findActiveFolder(
        int $userId,
        int $clusterId,
        ?int $parentId,
        string $name
    ): ?array {
        if ($parentId === null) {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM folders '
                . 'WHERE user_id = :user_id '
                . 'AND cluster_id = :cluster_id '
                . 'AND parent_id IS NULL '
                . 'AND name = :name '
                . 'AND deleted_at IS NULL '
                . 'LIMIT 1'
            );
            $stmt->execute([
                'user_id' => $userId,
                'cluster_id' => $clusterId,
                'name' => $name,
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM folders '
                . 'WHERE user_id = :user_id '
                . 'AND cluster_id = :cluster_id '
                . 'AND parent_id = :parent_id '
                . 'AND name = :name '
                . 'AND deleted_at IS NULL '
                . 'LIMIT 1'
            );
            $stmt->execute([
                'user_id' => $userId,
                'cluster_id' => $clusterId,
                'parent_id' => $parentId,
                'name' => $name,
            ]);
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }
}
