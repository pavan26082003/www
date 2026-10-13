<?php

declare(strict_types=1);

namespace NasApi\Repositories;

use PDO;

/**
 * Data access for nas_backup_devices only.
 *
 * This repository intentionally owns only the new mobile-backup metadata table.
 * Existing NAS files/folders/storage logic remains in app/Repositories/NAS and
 * app/Services/NAS.
 */
final class MobileBackupDeviceRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param array<string,mixed> $device
     * @return array<string,mixed>
     */
    public function upsert(int $userId, array $device): array
    {
        $existing = $this->findByDeviceId($userId, (string) $device['device_id']);

        if ($existing) {
            $stmt = $this->pdo->prepare(
                'UPDATE nas_backup_devices SET '
                . 'device_name = :device_name, '
                . 'platform = :platform, '
                . 'manufacturer = :manufacturer, '
                . 'model = :model, '
                . 'os_version = :os_version, '
                . 'app_version = :app_version, '
                . 'backup_enabled = 1, '
                . 'last_seen_at = NOW() '
                . 'WHERE id = :id AND user_id = :user_id'
            );

            $stmt->execute([
                'device_name' => $device['device_name'],
                'platform' => $device['platform'],
                'manufacturer' => $device['manufacturer'],
                'model' => $device['model'],
                'os_version' => $device['os_version'],
                'app_version' => $device['app_version'],
                'id' => (int) $existing['id'],
                'user_id' => $userId,
            ]);

            $row = $this->findByDeviceId($userId, (string) $device['device_id']);
            return [
                'created' => false,
                'device' => $row ?: $existing,
            ];
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO nas_backup_devices '
            . '(user_id, device_id, device_name, platform, manufacturer, model, '
            . 'os_version, app_version, backup_enabled, last_seen_at) '
            . 'VALUES '
            . '(:user_id, :device_id, :device_name, :platform, :manufacturer, :model, '
            . ':os_version, :app_version, 1, NOW())'
        );

        $stmt->execute([
            'user_id' => $userId,
            'device_id' => $device['device_id'],
            'device_name' => $device['device_name'],
            'platform' => $device['platform'],
            'manufacturer' => $device['manufacturer'],
            'model' => $device['model'],
            'os_version' => $device['os_version'],
            'app_version' => $device['app_version'],
        ]);

        $row = $this->findByDeviceId($userId, (string) $device['device_id']);

        return [
            'created' => true,
            'device' => $row ?: [],
        ];
    }

    /** @return array<string,mixed>|null */
    public function findByDeviceId(int $userId, string $deviceId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, device_id, device_name, platform, manufacturer, '
            . 'model, os_version, app_version, backup_enabled, last_seen_at, '
            . 'last_backup_at, created_at, updated_at '
            . 'FROM nas_backup_devices '
            . 'WHERE user_id = :user_id AND device_id = :device_id '
            . 'LIMIT 1'
        );
        $stmt->execute([
            'user_id' => $userId,
            'device_id' => $deviceId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }

    /** @return array<int,array<string,mixed>> */
    public function getByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, device_id, device_name, platform, manufacturer, '
            . 'model, os_version, app_version, backup_enabled, last_seen_at, '
            . 'last_backup_at, created_at, updated_at '
            . 'FROM nas_backup_devices '
            . 'WHERE user_id = :user_id '
            . 'ORDER BY COALESCE(last_seen_at, created_at) DESC, id DESC'
        );
        $stmt->execute(['user_id' => $userId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return is_array($rows) ? $rows : [];
    }

    /** @return array<string,mixed>|null */
    public function markBackupCompleted(
        int $userId,
        int $backupDeviceId
    ): ?array {
        $stmt = $this->pdo->prepare(
            'UPDATE nas_backup_devices SET '
            . 'last_backup_at = NOW(), '
            . 'last_seen_at = NOW() '
            . 'WHERE id = :id AND user_id = :user_id'
        );
        $stmt->execute([
            'id' => $backupDeviceId,
            'user_id' => $userId,
        ]);

        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, device_id, device_name, platform, manufacturer, '
            . 'model, os_version, app_version, backup_enabled, last_seen_at, '
            . 'last_backup_at, created_at, updated_at '
            . 'FROM nas_backup_devices '
            . 'WHERE id = :id AND user_id = :user_id '
            . 'LIMIT 1'
        );
        $stmt->execute([
            'id' => $backupDeviceId,
            'user_id' => $userId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }


}
