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
use NasApi\Repositories\MobileBackupSettingsRepository;
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



    /** POST /v1/backup/pause */
    public function pause(): void
    {
        $this->run(function (int $userId): void {
            $validated = MobileBackupValidator::backupRunAction($this->jsonObject());
            $result = $this->service()->pauseBackup(
                $userId,
                (string) $validated['device_id'],
                (string) $validated['run_id']
            );

            JsonResponse::send([
                'success' => true,
                'message' => !empty($result['changed'])
                    ? 'Mobile backup paused'
                    : 'Mobile backup is already paused',
                'changed' => !empty($result['changed']),
                'reason' => (string) ($result['reason'] ?? ''),
                'run' => $this->publicRun(
                    is_array($result['run'] ?? null) ? $result['run'] : []
                ),
            ]);
        });
    }

    /** POST /v1/backup/resume */
    public function resume(): void
    {
        $this->run(function (int $userId): void {
            $validated = MobileBackupValidator::backupRunAction($this->jsonObject());
            $result = $this->service()->resumeBackup(
                $userId,
                (string) $validated['device_id'],
                (string) $validated['run_id']
            );

            JsonResponse::send([
                'success' => true,
                'message' => !empty($result['changed'])
                    ? 'Mobile backup resumed'
                    : 'Mobile backup is already running',
                'changed' => !empty($result['changed']),
                'reason' => (string) ($result['reason'] ?? ''),
                'run' => $this->publicRun(
                    is_array($result['run'] ?? null) ? $result['run'] : []
                ),
            ]);
        });
    }

    /** POST /v1/backup/cancel */
    public function cancel(): void
    {
        $this->run(function (int $userId): void {
            $validated = MobileBackupValidator::backupRunAction($this->jsonObject());
            $result = $this->service()->cancelBackup(
                $userId,
                (string) $validated['device_id'],
                (string) $validated['run_id']
            );

            JsonResponse::send([
                'success' => true,
                'message' => !empty($result['changed'])
                    ? 'Mobile backup canceled'
                    : 'Mobile backup is already canceled',
                'changed' => !empty($result['changed']),
                'reason' => (string) ($result['reason'] ?? ''),
                'run' => $this->publicRun(
                    is_array($result['run'] ?? null) ? $result['run'] : []
                ),
            ]);
        });
    }

    /** POST /v1/backup/complete */
    public function complete(): void
    {
        $this->run(function (int $userId): void {
            $validated = MobileBackupValidator::backupRunAction($this->jsonObject());
            $result = $this->service()->completeBackup(
                $userId,
                (string) $validated['device_id'],
                (string) $validated['run_id']
            );

            JsonResponse::send([
                'success' => true,
                'message' => !empty($result['changed'])
                    ? 'Mobile backup completed'
                    : 'Mobile backup is already completed',
                'changed' => !empty($result['changed']),
                'reason' => (string) ($result['reason'] ?? ''),
                'device' => $this->publicDevice(
                    is_array($result['device'] ?? null) ? $result['device'] : []
                ),
                'run' => $this->publicRun(
                    is_array($result['run'] ?? null) ? $result['run'] : []
                ),
            ]);
        });
    }

    /** GET /v1/backup/history?device_id=...&limit=... */
    public function history(): void
    {
        $this->run(function (int $userId): void {
            $validated = MobileBackupValidator::backupHistory($_GET);
            $result = $this->service()->getBackupHistory(
                $userId,
                (string) $validated['device_id'],
                (int) $validated['limit']
            );

            $history = [];
            foreach (($result['history'] ?? []) as $row) {
                if (is_array($row)) {
                    $history[] = $this->publicRun($row);
                }
            }

            JsonResponse::send([
                'success' => true,
                'message' => 'Mobile backup history retrieved',
                'device_id' => (string) $validated['device_id'],
                'count' => count($history),
                'history' => $history,
            ]);
        });
    }


    /** GET /v1/backup/settings?device_id=... */
    public function settings(): void
    {
        $this->run(function (int $userId): void {
            $validated = MobileBackupValidator::backupSettingsQuery($_GET);
            $result = $this->service()->getBackupSettings(
                $userId,
                (string) $validated['device_id']
            );

            JsonResponse::send([
                'success' => true,
                'message' => 'Mobile backup settings retrieved',
                'device_id' => (string) $validated['device_id'],
                'persisted' => !empty($result['persisted']),
                'settings' => $result['settings'] ?? [],
            ]);
        });
    }

    /** PUT /v1/backup/settings */
    public function updateSettings(): void
    {
        $this->run(function (int $userId): void {
            $input = $this->jsonObject();
            $validated = MobileBackupValidator::backupSettingsUpdate($input);

            $result = $this->service()->updateBackupSettings(
                $userId,
                (string) $validated['device_id'],
                $validated
            );

            JsonResponse::send([
                'success' => true,
                'message' => 'Mobile backup settings updated',
                'device_id' => (string) $validated['device_id'],
                'persisted' => true,
                'settings' => $result['settings'] ?? [],
            ]);
        });
    }

    /** POST /v1/backup/items/backup-again */
    public function backupAgain(): void
    {
        $this->run(function (int $userId): void {
            $input = $this->jsonObject();
            $validated = MobileBackupValidator::backupAgain($input);

            $result = $this->service()->backupAgain(
                $userId,
                (string) $validated['device_id'],
                (string) $validated['source_root'],
                (string) $validated['source_relative_path']
            );

            $item = is_array($result['backup_item'] ?? null)
                ? $result['backup_item']
                : [];

            JsonResponse::send([
                'success' => true,
                'message' => !empty($result['changed'])
                    ? 'Source enabled for backup again'
                    : 'Source is already enabled for backup',
                'backup_again' => true,
                'changed' => !empty($result['changed']),
                'reason' => (string) ($result['reason'] ?? ''),
                'source' => [
                    'source_root' => $item['source_root'] ?? $validated['source_root'],
                    'source_relative_path' => $item['source_relative_path'] ?? $validated['source_relative_path'],
                    'source_path_hash' => $result['source_path_hash'] ?? ($item['source_path_hash'] ?? null),
                ],
                'backup_item' => [
                    'id' => isset($item['id']) ? (int) $item['id'] : null,
                    'file_id' => isset($item['file_id']) && $item['file_id'] !== null
                        ? (int) $item['file_id']
                        : null,
                    'folder_id' => isset($item['folder_id']) && $item['folder_id'] !== null
                        ? (int) $item['folder_id']
                        : null,
                    'backup_status' => $item['backup_status'] ?? null,
                    'cloud_deleted' => isset($item['cloud_deleted'])
                        ? (int) $item['cloud_deleted'] === 1
                        : false,
                    'cloud_deleted_at' => $item['cloud_deleted_at'] ?? null,
                ],
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
                $validated['conflict_action'],
                $_FILES['file']
            );

            $run = is_array($result['run'] ?? null) ? $result['run'] : [];
            $item = is_array($result['backup_item'] ?? null)
                ? $result['backup_item']
                : [];

            if (!empty($result['conflict'])) {
                JsonResponse::send([
                    'success' => false,
                    'conflict' => true,
                    'error' => (string) ($result['error'] ?? 'backup_source_changed'),
                    'message' => (string) ($result['message'] ?? 'A different file is already backed up from the same source path.'),
                    'conflict_type' => (string) ($result['conflict_type'] ?? 'same_path_changed_content'),
                    'source' => $result['source'] ?? [],
                    'existing_file' => $result['existing_file'] ?? [],
                    'incoming_file' => $result['incoming_file'] ?? [],
                    'backup_item' => [
                        'id' => isset($item['id']) ? (int) $item['id'] : null,
                        'source_root' => $item['source_root'] ?? $validated['source_root'],
                        'source_relative_path' => $item['source_relative_path'] ?? $validated['source_relative_path'],
                        'source_path_hash' => $item['source_path_hash'] ?? null,
                        'backup_status' => $item['backup_status'] ?? null,
                        'cloud_deleted' => isset($item['cloud_deleted'])
                            ? (int) $item['cloud_deleted'] === 1
                            : false,
                    ],
                    'allowed_actions' => $result['allowed_actions'] ?? ['replace', 'keep_both', 'cancel'],
                    'run' => $this->publicRun($run),
                ], 409);
                return;
            }

            $reason = (string) ($result['reason'] ?? '');
            $message = 'Mobile backup file uploaded';
            if ($reason === 'already_uploaded') {
                $message = 'Mobile backup file already uploaded';
            } elseif ($reason === 'conflict_canceled') {
                $message = 'Mobile backup conflict canceled';
            } elseif ($reason === 'source_changed_replaced') {
                $message = 'Mobile backup file replaced';
            } elseif ($reason === 'source_changed_kept_both') {
                $message = 'Mobile backup kept both file versions';
            }

            $response = [
                'success' => true,
                'message' => $message,
                'skipped' => !empty($result['skipped']),
                'canceled' => !empty($result['canceled']),
                'reason' => $reason,
                'conflict_action' => $result['conflict_action'] ?? null,
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
                    'file_id' => isset($item['file_id']) ? (int) $item['file_id'] : null,
                    'folder_id' => isset($item['folder_id']) ? (int) $item['folder_id'] : null,
                    'source_root' => $item['source_root'] ?? $validated['source_root'],
                    'source_relative_path' => $item['source_relative_path'] ?? $validated['source_relative_path'],
                    'source_path_hash' => $result['source_path_hash'] ?? ($item['source_path_hash'] ?? null),
                    'backup_status' => $item['backup_status'] ?? null,
                    'cloud_deleted' => isset($item['cloud_deleted'])
                        ? (int) $item['cloud_deleted'] === 1
                        : false,
                ],
                'run' => $this->publicRun($run),
            ];

            if (isset($result['retained_file']) && is_array($result['retained_file'])) {
                $response['retained_file'] = $result['retained_file'];
            }

            $status = 201;
            if (!empty($result['skipped']) || !empty($result['canceled']) || $reason === 'source_changed_replaced') {
                $status = 200;
            }

            JsonResponse::send($response, $status);
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
            new MobileBackupSettingsRepository($pdo),
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
