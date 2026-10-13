<?php

declare(strict_types=1);

namespace NasApi\Repositories;

use PDO;

/**
 * Data access for nas_backup_runs only.
 *
 * This repository stores mobile-backup run/session metadata. It does not
 * create folders, upload files, or modify the existing NAS sync tables.
 */
final class MobileBackupRunRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** @return array<string,mixed> */
    public function create(int $userId, int $backupDeviceId, string $runId): array
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO nas_backup_runs '
            . '(user_id, backup_device_id, run_id, status, started_at) '
            . "VALUES (:user_id, :backup_device_id, :run_id, 'running', NOW())"
        );

        $stmt->execute([
            'user_id' => $userId,
            'backup_device_id' => $backupDeviceId,
            'run_id' => $runId,
        ]);

        $row = $this->findByRunId($userId, $runId);
        return $row ?: [];
    }

    /** @return array<string,mixed>|null */
    public function findByRunId(int $userId, string $runId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, backup_device_id, run_id, status, '
            . 'files_scanned, folders_scanned, files_uploaded, files_skipped, '
            . 'files_suppressed, bytes_uploaded, error_count, errors_json, '
            . 'started_at, completed_at, created_at, updated_at '
            . 'FROM nas_backup_runs '
            . 'WHERE user_id = :user_id AND run_id = :run_id '
            . 'LIMIT 1'
        );
        $stmt->execute([
            'user_id' => $userId,
            'run_id' => $runId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }

    /** @return array<string,mixed>|null */
    public function findLatestForDevice(
        int $userId,
        int $backupDeviceId
    ): ?array {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, backup_device_id, run_id, status, '
            . 'files_scanned, folders_scanned, files_uploaded, files_skipped, '
            . 'files_suppressed, bytes_uploaded, error_count, errors_json, '
            . 'started_at, completed_at, created_at, updated_at '
            . 'FROM nas_backup_runs '
            . 'WHERE user_id = :user_id AND backup_device_id = :backup_device_id '
            . 'ORDER BY id DESC LIMIT 1'
        );
        $stmt->execute([
            'user_id' => $userId,
            'backup_device_id' => $backupDeviceId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }

    /** @return array<string,mixed>|null */
    public function updatePlanCounts(
        int $userId,
        string $runId,
        int $filesScanned,
        int $foldersScanned,
        int $filesSkipped,
        int $filesSuppressed
    ): ?array {
        $stmt = $this->pdo->prepare(
            'UPDATE nas_backup_runs SET '
            . 'files_scanned = :files_scanned, '
            . 'folders_scanned = :folders_scanned, '
            . 'files_skipped = :files_skipped, '
            . 'files_suppressed = :files_suppressed '
            . 'WHERE user_id = :user_id AND run_id = :run_id '
            . "AND status = 'running'"
        );

        $stmt->execute([
            'files_scanned' => $filesScanned,
            'folders_scanned' => $foldersScanned,
            'files_skipped' => $filesSkipped,
            'files_suppressed' => $filesSuppressed,
            'user_id' => $userId,
            'run_id' => $runId,
        ]);

        return $this->findByRunId($userId, $runId);
    }


    /** @return array<string,mixed>|null */
    public function addUploadedFile(
        int $userId,
        string $runId,
        int $bytesUploaded
    ): ?array {
        $stmt = $this->pdo->prepare(
            'UPDATE nas_backup_runs SET '
            . 'files_uploaded = files_uploaded + 1, '
            . 'bytes_uploaded = bytes_uploaded + :bytes_uploaded '
            . 'WHERE user_id = :user_id AND run_id = :run_id '
            . "AND status = 'running'"
        );
        $stmt->execute([
            'bytes_uploaded' => $bytesUploaded,
            'user_id' => $userId,
            'run_id' => $runId,
        ]);
        return $this->findByRunId($userId, $runId);
    }


    /** @return array<string,mixed>|null */
    public function setStatus(
        int $userId,
        int $backupDeviceId,
        string $runId,
        string $fromStatus,
        string $toStatus,
        bool $terminal
    ): ?array {
        $sql = 'UPDATE nas_backup_runs SET status = :to_status';
        if ($terminal) {
            $sql .= ', completed_at = COALESCE(completed_at, NOW())';
        } else {
            $sql .= ', completed_at = NULL';
        }
        $sql .= ' WHERE user_id = :user_id '
            . 'AND backup_device_id = :backup_device_id '
            . 'AND run_id = :run_id '
            . 'AND status = :from_status';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'to_status' => $toStatus,
            'user_id' => $userId,
            'backup_device_id' => $backupDeviceId,
            'run_id' => $runId,
            'from_status' => $fromStatus,
        ]);

        $changed = $stmt->rowCount() > 0;
        $run = $this->findByRunId($userId, $runId);
        if (!is_array($run)) {
            return null;
        }

        // Internal compare-and-set result. The service uses this to distinguish
        // a real transition from an idempotent/concurrent request that found
        // the run already in the requested target state.
        $run['_transition_changed'] = $changed;
        return $run;
    }

    /** @return array<string,mixed>|null */
    public function cancelFromActive(
        int $userId,
        int $backupDeviceId,
        string $runId
    ): ?array {
        $stmt = $this->pdo->prepare(
            'UPDATE nas_backup_runs SET '
            . "status = 'canceled', "
            . 'completed_at = COALESCE(completed_at, NOW()) '
            . 'WHERE user_id = :user_id '
            . 'AND backup_device_id = :backup_device_id '
            . 'AND run_id = :run_id '
            . "AND status IN ('running', 'paused')"
        );
        $stmt->execute([
            'user_id' => $userId,
            'backup_device_id' => $backupDeviceId,
            'run_id' => $runId,
        ]);

        $changed = $stmt->rowCount() > 0;
        $run = $this->findByRunId($userId, $runId);
        if (!is_array($run)) {
            return null;
        }

        $run['_transition_changed'] = $changed;
        return $run;
    }

    /** @return array<int,array<string,mixed>> */
    public function getHistoryForDevice(
        int $userId,
        int $backupDeviceId,
        int $limit = 50
    ): array {
        $limit = max(1, min(100, $limit));

        $stmt = $this->pdo->prepare(
            'SELECT id, user_id, backup_device_id, run_id, status, '
            . 'files_scanned, folders_scanned, files_uploaded, files_skipped, '
            . 'files_suppressed, bytes_uploaded, error_count, errors_json, '
            . 'started_at, completed_at, created_at, updated_at '
            . 'FROM nas_backup_runs '
            . 'WHERE user_id = :user_id AND backup_device_id = :backup_device_id '
            . 'ORDER BY id DESC LIMIT ' . $limit
        );
        $stmt->execute([
            'user_id' => $userId,
            'backup_device_id' => $backupDeviceId,
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return is_array($rows) ? $rows : [];
    }


}
