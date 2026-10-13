<?php

declare(strict_types=1);

namespace NasApi\Repositories;

use PDO;

final class MobileBackupSettingsRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** @return array<string,mixed>|null */
    public function findByDevice(int $userId, int $backupDeviceId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM nas_backup_settings '
            . 'WHERE user_id = :user_id AND backup_device_id = :backup_device_id '
            . 'LIMIT 1'
        );
        $stmt->execute([
            ':user_id' => $userId,
            ':backup_device_id' => $backupDeviceId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }

    /**
     * @param array<string,mixed> $settings
     * @return array<string,mixed>|null
     */
    public function upsert(int $userId, int $backupDeviceId, array $settings): ?array
    {
        $sql = 'INSERT INTO nas_backup_settings ('
            . 'user_id, backup_device_id, auto_backup_enabled, backup_frequency, '
            . 'wifi_only, charging_only, preferred_weekday, preferred_time, timezone, '
            . 'created_at, updated_at'
            . ') VALUES ('
            . ':user_id, :backup_device_id, :auto_backup_enabled, :backup_frequency, '
            . ':wifi_only, :charging_only, :preferred_weekday, :preferred_time, :timezone, '
            . 'NOW(), NOW()'
            . ') ON DUPLICATE KEY UPDATE '
            . 'auto_backup_enabled = VALUES(auto_backup_enabled), '
            . 'backup_frequency = VALUES(backup_frequency), '
            . 'wifi_only = VALUES(wifi_only), '
            . 'charging_only = VALUES(charging_only), '
            . 'preferred_weekday = VALUES(preferred_weekday), '
            . 'preferred_time = VALUES(preferred_time), '
            . 'timezone = VALUES(timezone), '
            . 'updated_at = NOW()';

        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute([
            ':user_id' => $userId,
            ':backup_device_id' => $backupDeviceId,
            ':auto_backup_enabled' => !empty($settings['auto_backup_enabled']) ? 1 : 0,
            ':backup_frequency' => (string) $settings['backup_frequency'],
            ':wifi_only' => !empty($settings['wifi_only']) ? 1 : 0,
            ':charging_only' => !empty($settings['charging_only']) ? 1 : 0,
            ':preferred_weekday' => (int) $settings['preferred_weekday'],
            ':preferred_time' => (string) $settings['preferred_time'] . ':00',
            ':timezone' => (string) $settings['timezone'],
        ]);

        if (!$ok) {
            return null;
        }

        return $this->findByDevice($userId, $backupDeviceId);
    }
}
