<?php

declare(strict_types=1);

namespace NasApi\Controllers;

use NasApi\Auth\AuthenticatedNasContext;
use NasApi\Core\ApiException;
use NasApi\Core\JsonResponse;
use NasApi\Integration\SharedNasFactory;
use NasApi\Repositories\MobileBackupDeviceRepository;
use NasApi\Repositories\MobileBackupFolderRepository;
use NasApi\Repositories\MobileBackupItemRepository;
use NasApi\Repositories\MobileBackupRunRepository;
use NasApi\Services\MobileBackupService;
use NasApi\Support\MobileBackupValidator;

final class BackupController
{
    /** POST /v1/backup/devices/register */
    public function registerDevice(): void
    {
        $this->run(function (int $userId): void {
            $input = $this->jsonObject();
            $device = MobileBackupValidator::device($input);

            $result = $this->service()->registerDevice($userId, $device);
            $created = !empty($result['created']);
            $row = is_array($result['device'] ?? null) ? $result['device'] : [];

            JsonResponse::send([
                'success' => true,
                'message' => $created
                    ? 'Backup device registered'
                    : 'Backup device updated',
                'created' => $created,
                'device' => $this->publicDevice($row),
            ], $created ? 201 : 200);
        });
    }

    /** GET /v1/backup/devices */
    public function devices(): void
    {
        $this->run(function (int $userId): void {
            $rows = $this->service()->getDevices($userId);
            $devices = [];

            foreach ($rows as $row) {
                if (is_array($row)) {
                    $devices[] = $this->publicDevice($row);
                }
            }

            JsonResponse::send([
                'success' => true,
                'message' => 'Backup devices retrieved',
                'devices' => $devices,
            ]);
        });
    }

    /** POST /v1/backup/init */
    public function init(): void
    {
        $this->run(function (int $userId): void {
            $input = $this->jsonObject();
            $validated = MobileBackupValidator::backupInit($input);

            $result = $this->service()->initBackup(
                $userId,
                (string) $validated['device_id']
            );

            $device = is_array($result['device'] ?? null)
                ? $result['device']
                : [];
            $run = is_array($result['run'] ?? null)
                ? $result['run']
                : [];

            JsonResponse::send([
                'success' => true,
                'message' => 'Mobile backup initialized',
                'device' => $this->publicDevice($device),
                'run' => $this->publicRun($run),
            ], 201);
        });
    }

    /** GET /v1/backup/status?device_id=... */
    public function status(): void
    {
        $this->run(function (int $userId): void {
            $validated = MobileBackupValidator::backupStatus($_GET);

            $result = $this->service()->getStatus(
                $userId,
                (string) $validated['device_id']
            );

            $device = is_array($result['device'] ?? null)
                ? $result['device']
                : [];
            $run = is_array($result['run'] ?? null)
                ? $result['run']
                : null;

            JsonResponse::send([
                'success' => true,
                'message' => 'Mobile backup status retrieved',
                'device' => $this->publicDevice($device),
                'run' => $run === null ? null : $this->publicRun($run),
            ]);
        });
    }

    /** POST /v1/backup/plan */
    public function plan(): void
    {
        $this->run(function (int $userId): void {
            $input = $this->jsonObject();
            $validated = MobileBackupValidator::backupPlan($input);

            $result = $this->service()->planBackup(
                $userId,
                (string) $validated['device_id'],
                (string) $validated['run_id'],
                $validated['folders'],
                $validated['files']
            );

            $run = is_array($result['run'] ?? null) ? $result['run'] : [];

            JsonResponse::send([
                'success' => true,
                'message' => 'Mobile backup plan created',
                'run' => $this->publicRun($run),
                'summary' => $result['summary'] ?? [],
                'actions' => $result['actions'] ?? [],
            ]);
        });
    }


    /** POST /v1/backup/folders/ensure */
    public function ensureFolders(): void
    {
        $this->run(function (int $userId): void {
            $input = $this->jsonObject();
            $validated = MobileBackupValidator::backupFoldersEnsure($input);

            $result = $this->service()->ensureBackupFolders(
                $userId,
                (string) $validated['device_id'],
                (string) $validated['run_id'],
                $validated['folders']
            );

            JsonResponse::send([
                'success' => true,
                'message' => 'Mobile backup folders ensured',
                'cluster' => $result['cluster'] ?? [],
                'device_root' => $result['device_root'] ?? [],
                'summary' => $result['summary'] ?? [],
                'folders' => $result['folders'] ?? [],
            ]);
        });
    }

    /** POST /v1/backup/upload (multipart/form-data) */
    public function upload(): void
    {
        $this->run(function (int $userId): void {
            $validated = MobileBackupValidator::backupUpload($_POST);

            if (!isset($_FILES['file']) || !is_array($_FILES['file'])) {
                throw new ApiException(
                    'multipart field "file" is required.',
                    400,
                    'backup_file_required'
                );
            }

            $result = $this->service()->uploadFile(
                $userId,
                (string) $validated['device_id'],
                (string) $validated['run_id'],
                (string) $validated['source_root'],
                (string) $validated['source_relative_path'],
                $validated['source_hash'],
                $validated['source_modified_at'],
                $_FILES['file']
            );

            $run = is_array($result['run'] ?? null) ? $result['run'] : [];
            $item = is_array($result['backup_item'] ?? null)
                ? $result['backup_item']
                : [];

            JsonResponse::send([
                'success' => true,
                'message' => !empty($result['skipped'])
                    ? 'Mobile backup file already uploaded'
                    : 'Mobile backup file uploaded',
                'skipped' => !empty($result['skipped']),
                'reason' => (string) ($result['reason'] ?? ''),
                'file' => [
                    'file_id' => isset($result['file_id']) ? (int) $result['file_id'] : null,
                    'folder_id' => isset($result['folder_id']) ? (int) $result['folder_id'] : ($item['folder_id'] ?? null),
                    'original_name' => $result['original_name'] ?? null,
                    'file_size' => (int) ($result['file_size'] ?? 0),
                    'file_hash' => $result['file_hash'] ?? null,
                    'mime_type' => $result['mime_type'] ?? ($item['mime_type'] ?? null),
                    'stored_path' => $result['stored_path'] ?? null,
                ],
                'backup_item' => [
                    'id' => isset($item['id']) ? (int) $item['id'] : null,
                    'source_root' => $item['source_root'] ?? $validated['source_root'],
                    'source_relative_path' => $item['source_relative_path'] ?? $validated['source_relative_path'],
                    'source_path_hash' => $result['source_path_hash'] ?? ($item['source_path_hash'] ?? null),
                    'backup_status' => $item['backup_status'] ?? null,
                    'cloud_deleted' => isset($item['cloud_deleted'])
                        ? (int) $item['cloud_deleted'] === 1
                        : false,
                ],
                'run' => $this->publicRun($run),
            ], !empty($result['skipped']) ? 200 : 201);
        });
    }

    private function service(): MobileBackupService
    {
        // Reuse the existing NAS repository connection. No duplicate NAS DB
        // configuration is introduced in nas_api.
        $pdo = SharedNasFactory::userRepository()->getPdo();

        return new MobileBackupService(
            new MobileBackupDeviceRepository($pdo),
            new MobileBackupRunRepository($pdo),
            new MobileBackupItemRepository($pdo),
            new MobileBackupFolderRepository($pdo),
            SharedNasFactory::folderService(),
            SharedNasFactory::fileRepository(),
            SharedNasFactory::userRepository(),
            SharedNasFactory::storageService()
        );
    }

    /** @return array<string,mixed> */
    private function jsonObject(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || trim($raw) === '') {
            throw new ApiException(
                'JSON request body is required.',
                400,
                'json_body_required'
            );
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new ApiException(
                'Request body must be a valid JSON object.',
                400,
                'invalid_json'
            );
        }

        return $decoded;
    }

    /**
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    private function publicDevice(array $row): array
    {
        return [
            'id' => (int) ($row['id'] ?? 0),
            'device_id' => (string) ($row['device_id'] ?? ''),
            'device_name' => (string) ($row['device_name'] ?? ''),
            'platform' => (string) ($row['platform'] ?? ''),
            'manufacturer' => $row['manufacturer'] ?? null,
            'model' => $row['model'] ?? null,
            'os_version' => $row['os_version'] ?? null,
            'app_version' => $row['app_version'] ?? null,
            'backup_enabled' => (int) ($row['backup_enabled'] ?? 0) === 1,
            'last_seen_at' => $row['last_seen_at'] ?? null,
            'last_backup_at' => $row['last_backup_at'] ?? null,
            'created_at' => $row['created_at'] ?? null,
            'updated_at' => $row['updated_at'] ?? null,
        ];
    }

    /**
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    private function publicRun(array $row): array
    {
        return [
            'id' => (int) ($row['id'] ?? 0),
            'run_id' => (string) ($row['run_id'] ?? ''),
            'backup_device_id' => (int) ($row['backup_device_id'] ?? 0),
            'status' => (string) ($row['status'] ?? ''),
            'files_scanned' => (int) ($row['files_scanned'] ?? 0),
            'folders_scanned' => (int) ($row['folders_scanned'] ?? 0),
            'files_uploaded' => (int) ($row['files_uploaded'] ?? 0),
            'files_skipped' => (int) ($row['files_skipped'] ?? 0),
            'files_suppressed' => (int) ($row['files_suppressed'] ?? 0),
            'bytes_uploaded' => (int) ($row['bytes_uploaded'] ?? 0),
            'error_count' => (int) ($row['error_count'] ?? 0),
            'started_at' => $row['started_at'] ?? null,
            'completed_at' => $row['completed_at'] ?? null,
            'created_at' => $row['created_at'] ?? null,
            'updated_at' => $row['updated_at'] ?? null,
        ];
    }

    /** @param callable(int):void $handler */
    private function run(callable $handler): void
    {
        try {
            $context = AuthenticatedNasContext::resolve();
            $handler((int) $context['nas_user_id']);
        } catch (ApiException $e) {
            JsonResponse::send([
                'success' => false,
                'error' => $e->errorCode(),
                'message' => $e->getMessage(),
            ], $e->statusCode());
        } catch (\Throwable $e) {
            error_log(
                '[nas_api][backup] ' . get_class($e) . ': ' . $e->getMessage()
            );

            JsonResponse::send([
                'success' => false,
                'error' => 'backup_service_error',
                'message' => NAS_API_DEBUG
                    ? $e->getMessage()
                    : 'Unable to process the backup request.',
            ], 500);
        }
    }
}
