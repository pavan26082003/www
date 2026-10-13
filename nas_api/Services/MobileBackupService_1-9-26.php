<?php

declare(strict_types=1);

namespace NasApi\Services;

use App\Services\NAS\FolderService;
use App\Services\NAS\StorageService;
use App\Repositories\NAS\FileRepository;
use App\Repositories\NAS\UserRepository;
use App\Helpers\NAS\FileHelper;
use NasApi\Core\ApiException;
use NasApi\Repositories\MobileBackupDeviceRepository;
use NasApi\Repositories\MobileBackupFolderRepository;
use NasApi\Repositories\MobileBackupItemRepository;
use NasApi\Repositories\MobileBackupRunRepository;
use NasApi\Repositories\MobileBackupSettingsRepository;

final class MobileBackupService
{
    private MobileBackupDeviceRepository $devices;
    private MobileBackupRunRepository $runs;
    private MobileBackupItemRepository $items;
    private MobileBackupFolderRepository $folders;
    private MobileBackupSettingsRepository $settings;
    private FolderService $folderService;
    private FileRepository $fileRepository;
    private UserRepository $userRepository;
    private StorageService $storageService;

    public function __construct(
        MobileBackupDeviceRepository $devices,
        MobileBackupRunRepository $runs,
        MobileBackupItemRepository $items,
        MobileBackupFolderRepository $folders,
        MobileBackupSettingsRepository $settings,
        FolderService $folderService,
        FileRepository $fileRepository,
        UserRepository $userRepository,
        StorageService $storageService
    ) {
        $this->devices = $devices;
        $this->runs = $runs;
        $this->items = $items;
        $this->folders = $folders;
        $this->settings = $settings;
        $this->folderService = $folderService;
        $this->fileRepository = $fileRepository;
        $this->userRepository = $userRepository;
        $this->storageService = $storageService;
    }

    /**
     * @param array<string,mixed> $device
     * @return array<string,mixed>
     */
    public function registerDevice(int $userId, array $device): array
    {
        return $this->devices->upsert($userId, $device);
    }

    /** @return array<int,array<string,mixed>> */
    public function getDevices(int $userId): array
    {
        return $this->devices->getByUser($userId);
    }


    /** @return array<string,mixed> */
    public function getBackupSettings(int $userId, string $deviceId): array
    {
        $device = $this->devices->findByDeviceId($userId, $deviceId);
        if (!$device) {
            throw new ApiException(
                'Backup device is not registered for this user.',
                404,
                'backup_device_not_found'
            );
        }

        $row = $this->settings->findByDevice($userId, (int) $device['id']);
        if (!is_array($row)) {
            return [
                'device' => $device,
                'persisted' => false,
                'settings' => $this->defaultBackupSettings(),
            ];
        }

        return [
            'device' => $device,
            'persisted' => true,
            'settings' => $this->normalizeBackupSettingsRow($row),
        ];
    }

    /**
     * @param array<string,mixed> $settings
     * @return array<string,mixed>
     */
    public function updateBackupSettings(int $userId, string $deviceId, array $settings): array
    {
        $device = $this->devices->findByDeviceId($userId, $deviceId);
        if (!$device) {
            throw new ApiException(
                'Backup device is not registered for this user.',
                404,
                'backup_device_not_found'
            );
        }

        $row = $this->settings->upsert(
            $userId,
            (int) $device['id'],
            $settings
        );

        if (!is_array($row)) {
            throw new ApiException(
                'Unable to save mobile backup settings.',
                500,
                'backup_settings_save_failed'
            );
        }

        return [
            'device' => $device,
            'persisted' => true,
            'settings' => $this->normalizeBackupSettingsRow($row),
        ];
    }

    /** @return array<string,mixed> */
    public function initBackup(int $userId, string $deviceId): array
    {
        $device = $this->requireEnabledDevice($userId, $deviceId);

        $runId = 'mb_' . gmdate('YmdHis') . '_' . bin2hex(random_bytes(8));
        $run = $this->runs->create(
            $userId,
            (int) $device['id'],
            $runId
        );

        if (!$run) {
            throw new ApiException(
                'Unable to initialize mobile backup.',
                500,
                'backup_init_failed'
            );
        }

        return [
            'device' => $device,
            'run' => $run,
        ];
    }

    /** @return array<string,mixed> */
    public function getStatus(int $userId, string $deviceId): array
    {
        $device = $this->devices->findByDeviceId($userId, $deviceId);
        if (!$device) {
            throw new ApiException(
                'Backup device is not registered for this user.',
                404,
                'backup_device_not_found'
            );
        }

        $run = $this->runs->findLatestForDevice(
            $userId,
            (int) $device['id']
        );

        return [
            'device' => $device,
            'run' => $run,
        ];
    }


    /**
     * @return array<string,mixed>
     */
    public function pauseBackup(
        int $userId,
        string $deviceId,
        string $runId
    ): array {
        $device = $this->requireEnabledDevice($userId, $deviceId);
        $run = $this->requireRunForDevice($userId, (int) $device['id'], $runId);
        $status = (string) ($run['status'] ?? '');

        if ($status === 'paused') {
            return [
                'changed' => false,
                'reason' => 'already_paused',
                'device' => $device,
                'run' => $run,
            ];
        }

        if ($status !== 'running') {
            throw new ApiException(
                'Only a running backup can be paused.',
                409,
                'backup_run_not_running'
            );
        }

        $updated = $this->runs->setStatus(
            $userId,
            (int) $device['id'],
            $runId,
            'running',
            'paused',
            false
        );

        if (!is_array($updated) || (string) ($updated['status'] ?? '') !== 'paused') {
            throw new ApiException(
                'Unable to pause the backup run.',
                409,
                'backup_pause_failed'
            );
        }

        if (empty($updated['_transition_changed'])) {
            return [
                'changed' => false,
                'reason' => 'already_paused',
                'device' => $device,
                'run' => $updated,
            ];
        }

        return [
            'changed' => true,
            'reason' => 'backup_paused',
            'device' => $device,
            'run' => $updated,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public function resumeBackup(
        int $userId,
        string $deviceId,
        string $runId
    ): array {
        $device = $this->requireEnabledDevice($userId, $deviceId);
        $run = $this->requireRunForDevice($userId, (int) $device['id'], $runId);
        $status = (string) ($run['status'] ?? '');

        if ($status === 'running') {
            return [
                'changed' => false,
                'reason' => 'already_running',
                'device' => $device,
                'run' => $run,
            ];
        }

        if ($status !== 'paused') {
            throw new ApiException(
                'Only a paused backup can be resumed.',
                409,
                'backup_run_not_paused'
            );
        }

        $updated = $this->runs->setStatus(
            $userId,
            (int) $device['id'],
            $runId,
            'paused',
            'running',
            false
        );

        if (!is_array($updated) || (string) ($updated['status'] ?? '') !== 'running') {
            throw new ApiException(
                'Unable to resume the backup run.',
                409,
                'backup_resume_failed'
            );
        }

        if (empty($updated['_transition_changed'])) {
            return [
                'changed' => false,
                'reason' => 'already_running',
                'device' => $device,
                'run' => $updated,
            ];
        }

        return [
            'changed' => true,
            'reason' => 'backup_resumed',
            'device' => $device,
            'run' => $updated,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public function cancelBackup(
        int $userId,
        string $deviceId,
        string $runId
    ): array {
        $device = $this->devices->findByDeviceId($userId, $deviceId);
        if (!$device) {
            throw new ApiException(
                'Backup device is not registered for this user.',
                404,
                'backup_device_not_found'
            );
        }

        $run = $this->requireRunForDevice($userId, (int) $device['id'], $runId);
        $status = (string) ($run['status'] ?? '');

        if (in_array($status, ['canceled', 'cancelled'], true)) {
            return [
                'changed' => false,
                'reason' => 'already_canceled',
                'device' => $device,
                'run' => $run,
            ];
        }

        if ($status === 'completed') {
            throw new ApiException(
                'A completed backup run cannot be canceled.',
                409,
                'backup_run_already_completed'
            );
        }

        if (!in_array($status, ['running', 'paused'], true)) {
            throw new ApiException(
                'This backup run cannot be canceled from its current status.',
                409,
                'backup_run_not_cancelable'
            );
        }

        $updated = $this->runs->cancelFromActive(
            $userId,
            (int) $device['id'],
            $runId
        );

        if (!is_array($updated)
            || !in_array((string) ($updated['status'] ?? ''), ['canceled', 'cancelled'], true)) {
            throw new ApiException(
                'Unable to cancel the backup run.',
                409,
                'backup_cancel_failed'
            );
        }

        if (empty($updated['_transition_changed'])) {
            return [
                'changed' => false,
                'reason' => 'already_canceled',
                'device' => $device,
                'run' => $updated,
            ];
        }

        return [
            'changed' => true,
            'reason' => 'backup_canceled',
            'device' => $device,
            'run' => $updated,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public function completeBackup(
        int $userId,
        string $deviceId,
        string $runId
    ): array {
        $device = $this->requireEnabledDevice($userId, $deviceId);
        $run = $this->requireRunForDevice($userId, (int) $device['id'], $runId);
        $status = (string) ($run['status'] ?? '');

        if ($status === 'completed') {
            return [
                'changed' => false,
                'reason' => 'already_completed',
                'device' => $device,
                'run' => $run,
            ];
        }

        if ($status === 'paused') {
            throw new ApiException(
                'Resume the backup run before completing it.',
                409,
                'backup_run_paused'
            );
        }

        if (in_array($status, ['canceled', 'cancelled'], true)) {
            throw new ApiException(
                'A canceled backup run cannot be completed.',
                409,
                'backup_run_canceled'
            );
        }

        if ($status !== 'running') {
            throw new ApiException(
                'Only a running backup can be completed.',
                409,
                'backup_run_not_running'
            );
        }

        $updated = $this->runs->setStatus(
            $userId,
            (int) $device['id'],
            $runId,
            'running',
            'completed',
            true
        );

        if (!is_array($updated) || (string) ($updated['status'] ?? '') !== 'completed') {
            throw new ApiException(
                'Unable to complete the backup run.',
                409,
                'backup_complete_failed'
            );
        }

        if (empty($updated['_transition_changed'])) {
            return [
                'changed' => false,
                'reason' => 'already_completed',
                'device' => $device,
                'run' => $updated,
            ];
        }

        $updatedDevice = $this->devices->markBackupCompleted(
            $userId,
            (int) $device['id']
        );

        return [
            'changed' => true,
            'reason' => 'backup_completed',
            'device' => is_array($updatedDevice) ? $updatedDevice : $device,
            'run' => $updated,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public function getBackupHistory(
        int $userId,
        string $deviceId,
        int $limit = 50
    ): array {
        $device = $this->devices->findByDeviceId($userId, $deviceId);
        if (!$device) {
            throw new ApiException(
                'Backup device is not registered for this user.',
                404,
                'backup_device_not_found'
            );
        }

        return [
            'device' => $device,
            'history' => $this->runs->getHistoryForDevice(
                $userId,
                (int) $device['id'],
                $limit
            ),
        ];
    }


    /**
     * Explicitly allow a previously cloud-deleted phone source to be backed up
     * again. This never restores or reuses the old Trash object; the next plan
     * marks the phone source for upload and the next upload stores current phone
     * contents as a fresh active NAS file.
     *
     * Repeating the request is safe and does not detach an active backup.
     *
     * @return array<string,mixed>
     */
    public function backupAgain(
        int $userId,
        string $deviceId,
        string $sourceRoot,
        string $sourceRelativePath
    ): array {
        $device = $this->devices->findByDeviceId($userId, $deviceId);
        if (!$device) {
            throw new ApiException(
                'Backup device is not registered for this user.',
                404,
                'backup_device_not_found'
            );
        }

        $sourcePathHash = hash('sha256', $sourceRoot . "\n" . $sourceRelativePath);
        $rows = $this->items->findByPathHashes(
            $userId,
            (int) $device['id'],
            [$sourcePathHash]
        );
        $item = $rows[$sourcePathHash] ?? null;

        if (!is_array($item) || (string) ($item['item_type'] ?? '') !== 'file') {
            throw new ApiException(
                'Backup item was not found for this source path.',
                404,
                'backup_item_not_found'
            );
        }

        // Idempotency: if already re-enabled (or currently active), do not alter
        // the existing active NAS mapping. Only a cloud_deleted item is detached.
        if ((int) ($item['cloud_deleted'] ?? 0) !== 1) {
            return [
                'changed' => false,
                'reason' => 'already_enabled',
                'source_path_hash' => $sourcePathHash,
                'backup_item' => $item,
            ];
        }

        $updated = $this->items->enableBackupAgain(
            $userId,
            (int) $device['id'],
            (int) ($item['id'] ?? 0),
            $sourcePathHash
        );

        if (!is_array($updated)) {
            throw new ApiException(
                'Unable to enable this source for backup again.',
                500,
                'backup_again_failed'
            );
        }

        return [
            'changed' => true,
            'reason' => 'backup_again_requested',
            'source_path_hash' => $sourcePathHash,
            'backup_item' => $updated,
        ];
    }

    /**
     * @param array<int,array<string,string>> $folders
     * @param array<int,array<string,mixed>> $files
     * @return array<string,mixed>
     */
    public function planBackup(
        int $userId,
        string $deviceId,
        string $runId,
        array $folders,
        array $files
    ): array {
        $device = $this->requireEnabledDevice($userId, $deviceId);

        $run = $this->runs->findByRunId($userId, $runId);
        if (!$run) {
            throw new ApiException(
                'Backup run was not found.',
                404,
                'backup_run_not_found'
            );
        }

        if ((int) ($run['backup_device_id'] ?? 0) !== (int) $device['id']) {
            throw new ApiException(
                'Backup run does not belong to this device.',
                409,
                'backup_run_device_mismatch'
            );
        }

        if ((string) ($run['status'] ?? '') !== 'running') {
            throw new ApiException(
                'Backup run is not running.',
                409,
                'backup_run_not_running'
            );
        }

        $normalizedFiles = [];
        $hashes = [];
        foreach ($files as $file) {
            $root = (string) $file['source_root'];
            $relative = (string) $file['source_relative_path'];
            $pathHash = hash('sha256', $root . "\n" . $relative);

            $file['source_path_hash'] = $pathHash;
            $normalizedFiles[] = $file;
            $hashes[] = $pathHash;
        }

        $existing = $this->items->findByPathHashes(
            $userId,
            (int) $device['id'],
            $hashes
        );

        $actions = [];
        $uploadCount = 0;
        $conflictCount = 0;
        $skipCount = 0;
        $suppressedCount = 0;

        foreach ($normalizedFiles as $file) {
            $hash = (string) $file['source_path_hash'];
            $row = $existing[$hash] ?? null;

            $action = 'upload';
            $reason = 'not_backed_up';

            if (is_array($row)) {
                if ((int) ($row['cloud_deleted'] ?? 0) === 1) {
                    $action = 'suppressed';
                    $reason = 'cloud_deleted';
                } elseif (
                    (string) ($row['backup_status'] ?? '') === 'pending'
                    && ($row['file_id'] ?? null) === null
                ) {
                    // Explicit Back Up Again request. The old Trash object was
                    // intentionally detached; current phone contents must upload.
                    $action = 'upload';
                    $reason = 'backup_again_requested';
                } else {
                    $mappedFile = ($row['file_id'] ?? null) !== null
                        ? $this->fileRepository->find((int) $row['file_id'])
                        : null;

                    if (($row['file_id'] ?? null) !== null && !is_array($mappedFile)) {
                        $withTrash = $this->fileRepository->findWithTrash((int) $row['file_id']);
                        if (is_array($withTrash) && ($withTrash['deleted_at'] ?? null) !== null) {
                            $this->items->markCloudDeleted(
                                $userId,
                                (int) $device['id'],
                                (int) ($row['id'] ?? 0),
                                $hash
                            );
                            $action = 'suppressed';
                            $reason = 'cloud_deleted';
                        } else {
                            // A hard-missing NAS row is recoverable. Upload will
                            // create a new NAS file and rebind the same backup item.
                            $action = 'upload';
                            $reason = 'cloud_file_missing';
                        }
                    } elseif ($this->isUnchanged($file, $row)) {
                        $action = 'skip';
                        $reason = 'unchanged';
                    } else {
                        $action = 'conflict';
                        $reason = 'source_changed';
                    }
                }
            }

            if ($action === 'upload') {
                $uploadCount++;
            } elseif ($action === 'conflict') {
                $conflictCount++;
            } elseif ($action === 'skip') {
                $skipCount++;
            } else {
                $suppressedCount++;
            }

            $actions[] = [
                'source_root' => (string) $file['source_root'],
                'source_relative_path' => (string) $file['source_relative_path'],
                'source_path_hash' => $hash,
                'action' => $action,
                'reason' => $reason,
                'file_id' => $reason === 'cloud_file_missing'
                    ? null
                    : (is_array($row) && $row['file_id'] !== null
                        ? (int) $row['file_id']
                        : null),
            ];
        }

        $updatedRun = $this->runs->updatePlanCounts(
            $userId,
            $runId,
            count($files),
            count($folders),
            $skipCount,
            $suppressedCount
        );

        return [
            'device' => $device,
            'run' => $updatedRun ?: $run,
            'summary' => [
                'folders_scanned' => count($folders),
                'files_scanned' => count($files),
                'upload' => $uploadCount,
                'conflict' => $conflictCount,
                'skip' => $skipCount,
                'suppressed' => $suppressedCount,
            ],
            'actions' => $actions,
        ];
    }


    /**
     * Ensure canonical cloud folder hierarchy for the supplied phone folders.
     *
     * Physical/directory creation is delegated to the existing NAS
     * FolderService. nas_api only adds mobile-specific device/source hierarchy
     * orchestration and backup metadata mapping.
     *
     * @param array<int,array<string,string>> $folders
     * @return array<string,mixed>
     */
    public function ensureBackupFolders(
        int $userId,
        string $deviceId,
        string $runId,
        array $folders
    ): array {
        $device = $this->requireEnabledDevice($userId, $deviceId);

        $run = $this->runs->findByRunId($userId, $runId);
        if (!$run) {
            throw new ApiException(
                'Backup run was not found.',
                404,
                'backup_run_not_found'
            );
        }

        if ((int) ($run['backup_device_id'] ?? 0) !== (int) $device['id']) {
            throw new ApiException(
                'Backup run does not belong to this device.',
                409,
                'backup_run_device_mismatch'
            );
        }

        if ((string) ($run['status'] ?? '') !== 'running') {
            throw new ApiException(
                'Backup run is not running.',
                409,
                'backup_run_not_running'
            );
        }

        $cluster = $this->folders->findClusterForUser($userId);
        if (!$cluster) {
            throw new ApiException(
                'Mobile Backup cluster is not mapped to this NAS user.',
                409,
                'mobile_backup_cluster_not_mapped'
            );
        }

        $clusterId = (int) ($cluster['id'] ?? 0);
        if ($clusterId <= 0) {
            throw new ApiException(
                'Mobile Backup cluster mapping is invalid.',
                500,
                'mobile_backup_cluster_invalid'
            );
        }

        $createdNodes = 0;
        $existingNodes = 0;

        // The stable device_id is the physical/logical device-root folder.
        // device_name remains presentation metadata in nas_backup_devices.
        $deviceRoot = $this->ensureFolderNode(
            $userId,
            $clusterId,
            null,
            $deviceId
        );
        if ($deviceRoot['created']) {
            $createdNodes++;
        } else {
            $existingNodes++;
        }

        $deviceRootId = (int) $deviceRoot['folder_id'];
        $mapping = [];
        $seen = [];

        foreach ($folders as $folder) {
            $sourceRoot = (string) $folder['source_root'];
            $relative = (string) $folder['source_relative_path'];
            $logicalKey = $sourceRoot . '/' . $relative;

            if (isset($seen[$logicalKey])) {
                continue;
            }
            $seen[$logicalKey] = true;

            $parentId = $deviceRootId;
            $segments = [$sourceRoot];
            if ($relative !== '') {
                $segments = array_merge($segments, explode('/', $relative));
            }

            $relativeSegments = [];
            $item = null;
            $pathHash = '';

            foreach ($segments as $index => $segment) {
                $node = $this->ensureFolderNode(
                    $userId,
                    $clusterId,
                    $parentId,
                    $segment
                );

                $parentId = (int) $node['folder_id'];
                if ($node['created']) {
                    $createdNodes++;
                } else {
                    $existingNodes++;
                }

                // Persist a mapping for every usable parent folder, not only
                // the final requested folder. This means a nested request such
                // as DCIM/Camera also creates the logical mapping for DCIM
                // itself, allowing files directly under DCIM to be backed up.
                if ($index > 0) {
                    $relativeSegments[] = $segment;
                }
                $currentRelative = implode('/', $relativeSegments);
                $currentHash = hash(
                    'sha256',
                    $sourceRoot . "\n" . $currentRelative
                );

                $currentItem = $this->items->upsertFolderMapping(
                    $userId,
                    (int) $device['id'],
                    $sourceRoot,
                    $currentRelative,
                    $currentHash,
                    $parentId
                );

                if ($currentRelative === $relative) {
                    $item = $currentItem;
                    $pathHash = $currentHash;
                }
            }

            if ($pathHash === '') {
                $pathHash = hash('sha256', $sourceRoot . "\n" . $relative);
            }

            $mapping[] = [
                'source_root' => $sourceRoot,
                'source_relative_path' => $relative,
                'source_path_hash' => $pathHash,
                'folder_id' => $parentId,
                'backup_item_id' => is_array($item)
                    ? (int) ($item['id'] ?? 0)
                    : null,
            ];
        }

        return [
            'device' => $device,
            'run' => $run,
            'cluster' => [
                'id' => $clusterId,
                'slug' => (string) ($cluster['slug'] ?? 'mobile-backup'),
            ],
            'device_root' => [
                'folder_id' => $deviceRootId,
                'folder_name' => $deviceId,
            ],
            'summary' => [
                'requested_folders' => count($seen),
                'nodes_created' => $createdNodes,
                'nodes_existing' => $existingNodes,
            ],
            'folders' => $mapping,
        ];
    }

    /**
     * Store one phone file in the already-ensured Mobile Backup hierarchy.
     *
     * @param array<string,mixed> $upload PHP uploaded-file array
     * @return array<string,mixed>
     */
    public function uploadFile(
        int $userId,
        string $deviceId,
        string $runId,
        string $sourceRoot,
        string $sourceRelativePath,
        ?string $declaredSourceHash,
        ?string $sourceModifiedAt,
        ?string $conflictAction,
        array $upload
    ): array {
        $device = $this->requireEnabledDevice($userId, $deviceId);
        $run = $this->runs->findByRunId($userId, $runId);
        if (!$run) {
            throw new ApiException('Backup run was not found.', 404, 'backup_run_not_found');
        }
        if ((int) ($run['backup_device_id'] ?? 0) !== (int) $device['id']) {
            throw new ApiException('Backup run does not belong to this device.', 409, 'backup_run_device_mismatch');
        }
        if ((string) ($run['status'] ?? '') !== 'running') {
            throw new ApiException('Backup run is not running.', 409, 'backup_run_not_running');
        }

        if (!isset($upload['tmp_name'], $upload['name'], $upload['error'], $upload['size'])) {
            throw new ApiException('Malformed uploaded file.', 400, 'malformed_backup_upload');
        }
        if ((int) $upload['error'] !== UPLOAD_ERR_OK) {
            throw new ApiException('Uploaded file was not received correctly.', 400, 'backup_upload_failed');
        }
        $tmpPath = (string) $upload['tmp_name'];
        if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
            throw new ApiException('Invalid uploaded file.', 400, 'invalid_backup_upload');
        }

        $fileSize = (int) $upload['size'];
        if ($fileSize < 0) {
            throw new ApiException('Uploaded file size is invalid.', 422, 'invalid_backup_upload_size');
        }

        $originalName = $this->uploadedOriginalName($upload);
        $sourcePathHash = hash('sha256', $sourceRoot . "\n" . $sourceRelativePath);
        $existingMap = $this->items->findByPathHashes(
            $userId,
            (int) $device['id'],
            [$sourcePathHash]
        );
        $existing = $existingMap[$sourcePathHash] ?? null;

        $actualHash = hash_file('sha256', $tmpPath);
        if ($actualHash === false) {
            throw new ApiException('Unable to hash uploaded file.', 500, 'backup_hash_failed');
        }
        $actualHash = strtolower($actualHash);

        if ($declaredSourceHash !== null && !hash_equals($declaredSourceHash, $actualHash)) {
            throw new ApiException(
                'source_hash does not match the uploaded file.',
                422,
                'backup_source_hash_mismatch'
            );
        }

        $incomingMime = $this->detectMimeType($tmpPath);

        $orphanedExisting = null;

        if (is_array($existing)) {
            if ((int) ($existing['cloud_deleted'] ?? 0) === 1) {
                throw new ApiException(
                    'This source path is suppressed because its cloud backup was deleted.',
                    409,
                    'backup_source_suppressed'
                );
            }

            /*
             * Resolve the mapped NAS file before deciding that the upload is an
             * unchanged retry. FileRepository::find() hides Trash rows.
             */
            $existingFile = $existing['file_id'] !== null
                ? $this->fileRepository->find((int) $existing['file_id'])
                : null;

            if (!is_array($existingFile)) {
                $withTrash = $existing['file_id'] !== null
                    ? $this->fileRepository->findWithTrash((int) $existing['file_id'])
                    : null;

                if (is_array($withTrash) && ($withTrash['deleted_at'] ?? null) !== null) {
                    $this->items->markCloudDeleted(
                        $userId,
                        (int) $device['id'],
                        (int) ($existing['id'] ?? 0),
                        $sourcePathHash
                    );

                    throw new ApiException(
                        'This source path is suppressed because its cloud backup was deleted.',
                        409,
                        'backup_source_suppressed'
                    );
                }

                /*
                 * Hard-missing NAS row: keep the mobile source identity row and
                 * repair it through the fresh-upload path below. This avoids
                 * requiring manual deletion from nas_backup_items.
                 */
                $orphanedExisting = $existing;
                $existing = null;
            }
        }

        if (is_array($existing)) {
            $storedHash = (string) ($existing['source_hash'] ?? '');
            if (
                $storedHash !== ''
                && hash_equals($storedHash, $actualHash)
                && (int) ($existing['source_size'] ?? -1) === $fileSize
            ) {
                return [
                    'skipped' => true,
                    'reason' => 'already_uploaded',
                    'file_id' => (int) $existingFile['id'],
                    'folder_id' => (int) ($existingFile['folder_id'] ?? ($existing['folder_id'] ?? 0)),
                    'backup_item' => $existing,
                    'run' => $run,
                    'source_path_hash' => $sourcePathHash,
                    'file_hash' => $actualHash,
                    'file_size' => $fileSize,
                    'mime_type' => (string) ($existingFile['file_type'] ?? ($existing['mime_type'] ?? 'application/octet-stream')),
                    'original_name' => (string) ($existingFile['original_name'] ?? ''),
                    'stored_path' => (string) ($existingFile['file_path'] ?? ''),
                ];
            }

            // IMPORTANT: this conflict is based only on the logical phone
            // source identity (source_root + source_relative_path). A file with
            // the same visible name from a different phone path is independent.
            if ($conflictAction === null) {
                return $this->changedSourceConflict(
                    $sourceRoot,
                    $sourceRelativePath,
                    $sourcePathHash,
                    $existing,
                    $existingFile,
                    $originalName,
                    $fileSize,
                    $actualHash,
                    $incomingMime,
                    $sourceModifiedAt,
                    $run
                );
            }

            if ($conflictAction === 'cancel') {
                return [
                    'skipped' => true,
                    'canceled' => true,
                    'reason' => 'conflict_canceled',
                    'conflict_action' => 'cancel',
                    'file_id' => (int) $existingFile['id'],
                    'folder_id' => (int) ($existingFile['folder_id'] ?? ($existing['folder_id'] ?? 0)),
                    'backup_item' => $existing,
                    'run' => $run,
                    'source_path_hash' => $sourcePathHash,
                    'file_hash' => (string) ($existingFile['file_hash'] ?? ($existing['source_hash'] ?? '')),
                    'file_size' => (int) ($existingFile['file_size'] ?? ($existing['source_size'] ?? 0)),
                    'mime_type' => (string) ($existingFile['file_type'] ?? ($existing['mime_type'] ?? 'application/octet-stream')),
                    'original_name' => (string) ($existingFile['original_name'] ?? ''),
                    'stored_path' => (string) ($existingFile['file_path'] ?? ''),
                ];
            }

            if ($conflictAction === 'replace') {
                return $this->replaceChangedSource(
                    $userId,
                    $deviceId,
                    (int) $device['id'],
                    $runId,
                    $run,
                    $sourceRoot,
                    $sourceRelativePath,
                    $sourcePathHash,
                    $actualHash,
                    $fileSize,
                    $sourceModifiedAt,
                    $originalName,
                    $incomingMime,
                    $tmpPath,
                    $existing,
                    $existingFile
                );
            }

            if ($conflictAction === 'keep_both') {
                return $this->keepBothChangedSource(
                    $userId,
                    $deviceId,
                    (int) $device['id'],
                    $runId,
                    $run,
                    $sourceRoot,
                    $sourceRelativePath,
                    $sourcePathHash,
                    $actualHash,
                    $fileSize,
                    $sourceModifiedAt,
                    $originalName,
                    $incomingMime,
                    $tmpPath,
                    $existing,
                    $existingFile
                );
            }
        }

        $parent = $this->requireUploadParent(
            $userId,
            (int) $device['id'],
            $sourceRoot,
            $sourceRelativePath
        );
        $parentRelative = $parent['parent_relative'];
        $folderId = $parent['folder_id'];

        $cluster = $this->folders->findClusterForUser($userId);
        if (!$cluster) {
            throw new ApiException(
                'Mobile Backup cluster is not mapped to this NAS user.',
                409,
                'mobile_backup_cluster_not_mapped'
            );
        }
        $clusterId = (int) ($cluster['id'] ?? 0);
        if ($clusterId <= 0) {
            throw new ApiException('Mobile Backup cluster mapping is invalid.', 500, 'mobile_backup_cluster_invalid');
        }

        $this->requireAvailableStorage($userId, $fileSize);

        $stored = $this->storeInCanonicalBackupPath(
            $userId,
            $deviceId,
            $sourceRoot,
            $parentRelative,
            $originalName,
            $tmpPath
        );

        try {
            $fileId = $this->fileRepository->create([
                'user_id' => $userId,
                'cluster_id' => $clusterId,
                'folder_id' => $folderId,
                'filename' => $stored['stored_filename'],
                'original_name' => $originalName,
                'file_path' => $stored['stored_path'],
                'file_size' => $fileSize,
                'file_type' => $stored['mime_type'],
                'file_category' => FileHelper::getFileCategory($stored['mime_type']),
                'file_hash' => $actualHash,
            ]);
        } catch (\Throwable $e) {
            @unlink($stored['destination']);
            throw $e;
        }

        try {
            if (is_array($orphanedExisting)) {
                $backupItem = $this->items->updateFileMapping(
                    $userId,
                    (int) $device['id'],
                    (int) ($orphanedExisting['id'] ?? 0),
                    (int) $fileId,
                    $folderId,
                    $sourceRoot,
                    $sourceRelativePath,
                    $sourcePathHash,
                    $fileSize,
                    $actualHash,
                    $sourceModifiedAt,
                    $stored['mime_type']
                );
            } else {
                $backupItem = $this->items->createFileMapping(
                    $userId,
                    (int) $device['id'],
                    (int) $fileId,
                    $folderId,
                    $sourceRoot,
                    $sourceRelativePath,
                    $sourcePathHash,
                    $fileSize,
                    $actualHash,
                    $sourceModifiedAt,
                    $stored['mime_type']
                );
            }

            if (!is_array($backupItem)) {
                throw new \RuntimeException('Unable to persist mobile backup item mapping.');
            }
        } catch (\Throwable $e) {
            try {
                $this->fileRepository->forceDelete((int) $fileId);
            } catch (\Throwable $cleanupError) {
                error_log('[nas_api][backup] fresh-upload DB cleanup failed: ' . $cleanupError->getMessage());
            }
            @unlink($stored['destination']);
            throw $e;
        }

        $this->userRepository->updateStorageUsed($userId, $fileSize, 'add');
        $updatedRun = $this->runs->addUploadedFile($userId, $runId, $fileSize);

        return [
            'skipped' => false,
            'reason' => 'uploaded',
            'file_id' => (int) $fileId,
            'folder_id' => $folderId,
            'backup_item' => $backupItem,
            'run' => $updatedRun ?: $run,
            'source_path_hash' => $sourcePathHash,
            'file_hash' => $actualHash,
            'file_size' => $fileSize,
            'mime_type' => $stored['mime_type'],
            'original_name' => $originalName,
            'stored_path' => $stored['stored_path'],
        ];
    }

    /**
     * @param array<string,mixed> $existing
     * @param array<string,mixed> $existingFile
     * @param array<string,mixed> $run
     * @return array<string,mixed>
     */
    private function changedSourceConflict(
        string $sourceRoot,
        string $sourceRelativePath,
        string $sourcePathHash,
        array $existing,
        array $existingFile,
        string $incomingName,
        int $incomingSize,
        string $incomingHash,
        string $incomingMime,
        ?string $sourceModifiedAt,
        array $run
    ): array {
        return [
            'conflict' => true,
            'error' => 'backup_source_changed',
            'message' => 'A different file is already backed up from the same source path.',
            'conflict_type' => 'same_path_changed_content',
            'allowed_actions' => ['replace', 'keep_both', 'cancel'],
            'source' => [
                'source_root' => $sourceRoot,
                'source_relative_path' => $sourceRelativePath,
                'source_path_hash' => $sourcePathHash,
            ],
            'existing_file' => [
                'file_id' => (int) ($existingFile['id'] ?? ($existing['file_id'] ?? 0)),
                'folder_id' => (int) ($existingFile['folder_id'] ?? ($existing['folder_id'] ?? 0)),
                'original_name' => (string) ($existingFile['original_name'] ?? ''),
                'file_size' => (int) ($existingFile['file_size'] ?? ($existing['source_size'] ?? 0)),
                'file_hash' => (string) ($existingFile['file_hash'] ?? ($existing['source_hash'] ?? '')),
                'mime_type' => (string) ($existingFile['file_type'] ?? ($existing['mime_type'] ?? 'application/octet-stream')),
            ],
            'incoming_file' => [
                'original_name' => $incomingName,
                'file_size' => $incomingSize,
                'file_hash' => $incomingHash,
                'mime_type' => $incomingMime,
                'source_modified_at' => $sourceModifiedAt,
            ],
            'backup_item' => $existing,
            'run' => $run,
        ];
    }

    /**
     * Replace the canonical unsuffixed cloud object only after the caller
     * explicitly chose conflict_action=replace. Keep Both copies stay intact.
     *
     * @param array<string,mixed> $run
     * @param array<string,mixed> $existing
     * @param array<string,mixed> $existingFile
     * @return array<string,mixed>
     */
    private function replaceChangedSource(
        int $userId,
        string $deviceId,
        int $backupDeviceId,
        string $runId,
        array $run,
        string $sourceRoot,
        string $sourceRelativePath,
        string $sourcePathHash,
        string $actualHash,
        int $fileSize,
        ?string $sourceModifiedAt,
        string $originalName,
        string $incomingMime,
        string $tmpPath,
        array $existing,
        array $existingFile
    ): array {
        $backupItemId = (int) ($existing['id'] ?? 0);
        $folderId = (int) ($existingFile['folder_id'] ?? ($existing['folder_id'] ?? 0));
        if ($backupItemId <= 0 || $folderId <= 0) {
            throw new ApiException(
                'Existing backup metadata is incomplete and cannot be replaced safely.',
                409,
                'backup_existing_file_invalid'
            );
        }

        /*
         * Replace targets the canonical unsuffixed visible file, not whichever
         * Keep Both copy is currently mapped as the latest phone version.
         *
         * Example:
         *   arch.txt       <- canonical Replace target
         *   arch (1).txt   <- Keep Both copy (must remain untouched)
         */
        $canonicalFile = $this->findActiveFileByOriginalName(
            $userId,
            $folderId,
            $originalName
        );

        if (!is_array($canonicalFile)) {
            return $this->createCanonicalReplacement(
                $userId,
                $deviceId,
                $backupDeviceId,
                $runId,
                $run,
                $sourceRoot,
                $sourceRelativePath,
                $sourcePathHash,
                $actualHash,
                $fileSize,
                $sourceModifiedAt,
                $originalName,
                $incomingMime,
                $tmpPath,
                $existing,
                $existingFile
            );
        }

        $fileId = (int) ($canonicalFile['id'] ?? 0);
        $oldStoredPath = (string) ($canonicalFile['file_path'] ?? '');
        if ($fileId <= 0 || $oldStoredPath === '') {
            throw new ApiException(
                'Canonical backup file metadata is incomplete.',
                409,
                'backup_existing_file_invalid'
            );
        }

        $oldSize = (int) ($canonicalFile['file_size'] ?? 0);
        $sizeDelta = $fileSize - $oldSize;
        if ($sizeDelta > 0) {
            $this->requireAvailableStorage($userId, $sizeDelta);
        }

        $oldDirRelative = trim(str_replace('\\', '/', dirname($oldStoredPath)), '/');
        $destinationDir = dirname($this->storageService->getFullPath($oldStoredPath));
        if (!is_dir($destinationDir)) {
            throw new ApiException(
                'Existing backup destination folder is missing.',
                409,
                'backup_destination_missing'
            );
        }

        $storedFilename = $this->storageService->generateUniqueFilename($originalName);
        $destination = rtrim($destinationDir, '/\\') . DIRECTORY_SEPARATOR . $storedFilename;
        if (!$this->storageService->saveUploadedFile($tmpPath, $destination)) {
            throw new ApiException('Unable to store replacement file.', 500, 'backup_file_store_failed');
        }

        $mimeType = $this->detectMimeType($destination);
        if ($mimeType === 'application/octet-stream' && $incomingMime !== '') {
            $mimeType = $incomingMime;
        }
        $storedPath = ($oldDirRelative === '' ? '' : $oldDirRelative . '/') . $storedFilename;

        $oldFileUpdate = [
            'filename' => (string) ($canonicalFile['filename'] ?? ''),
            'original_name' => (string) ($canonicalFile['original_name'] ?? ''),
            'file_path' => $oldStoredPath,
            'file_size' => $oldSize,
            'file_type' => (string) ($canonicalFile['file_type'] ?? 'application/octet-stream'),
            'file_category' => (string) ($canonicalFile['file_category'] ?? FileHelper::getFileCategory((string) ($canonicalFile['file_type'] ?? 'application/octet-stream'))),
            'file_hash' => (string) ($canonicalFile['file_hash'] ?? ''),
        ];

        try {
            $this->fileRepository->update($fileId, [
                'filename' => $storedFilename,
                'original_name' => $originalName,
                'file_path' => $storedPath,
                'file_size' => $fileSize,
                'file_type' => $mimeType,
                'file_category' => FileHelper::getFileCategory($mimeType),
                'file_hash' => $actualHash,
            ]);

            $backupItem = $this->items->updateFileMapping(
                $userId,
                $backupDeviceId,
                $backupItemId,
                $fileId,
                $folderId,
                $sourceRoot,
                $sourceRelativePath,
                $sourcePathHash,
                $fileSize,
                $actualHash,
                $sourceModifiedAt,
                $mimeType
            );

            if (!is_array($backupItem)) {
                throw new \RuntimeException('Unable to update mobile backup item mapping.');
            }
        } catch (\Throwable $e) {
            try {
                $this->fileRepository->update($fileId, $oldFileUpdate);
                $this->restorePreviousBackupMapping(
                    $userId,
                    $backupDeviceId,
                    $backupItemId,
                    $sourcePathHash,
                    $existing,
                    $existingFile
                );
            } catch (\Throwable $rollbackError) {
                error_log('[nas_api][backup] replacement rollback failed: ' . $rollbackError->getMessage());
            }
            @unlink($destination);
            throw $e;
        }

        try {
            if ($sizeDelta > 0) {
                $this->userRepository->updateStorageUsed($userId, $sizeDelta, 'add');
            } elseif ($sizeDelta < 0) {
                $this->userRepository->updateStorageUsed($userId, abs($sizeDelta), 'subtract');
            }
            $updatedRun = $this->runs->addUploadedFile($userId, $runId, $fileSize);
        } catch (\Throwable $e) {
            try {
                $this->fileRepository->update($fileId, $oldFileUpdate);
                $this->restorePreviousBackupMapping(
                    $userId,
                    $backupDeviceId,
                    $backupItemId,
                    $sourcePathHash,
                    $existing,
                    $existingFile
                );
                if ($sizeDelta > 0) {
                    $this->userRepository->updateStorageUsed($userId, $sizeDelta, 'subtract');
                } elseif ($sizeDelta < 0) {
                    $this->userRepository->updateStorageUsed($userId, abs($sizeDelta), 'add');
                }
            } catch (\Throwable $rollbackError) {
                error_log('[nas_api][backup] replacement accounting rollback failed: ' . $rollbackError->getMessage());
            }
            @unlink($destination);
            throw $e;
        }

        if ($oldStoredPath !== $storedPath) {
            $this->storageService->deleteFile($oldStoredPath);
        }

        $result = [
            'skipped' => false,
            'reason' => 'source_changed_replaced',
            'conflict_action' => 'replace',
            'file_id' => $fileId,
            'folder_id' => $folderId,
            'backup_item' => $backupItem,
            'run' => $updatedRun ?: $run,
            'source_path_hash' => $sourcePathHash,
            'file_hash' => $actualHash,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
            'original_name' => $originalName,
            'stored_path' => $storedPath,
        ];

        $mappedFileId = (int) ($existingFile['id'] ?? 0);
        if ($mappedFileId > 0 && $mappedFileId !== $fileId) {
            $result['retained_file'] = [
                'file_id' => $mappedFileId,
                'folder_id' => (int) ($existingFile['folder_id'] ?? $folderId),
                'original_name' => (string) ($existingFile['original_name'] ?? ''),
                'file_size' => (int) ($existingFile['file_size'] ?? 0),
                'file_hash' => (string) ($existingFile['file_hash'] ?? ''),
                'mime_type' => (string) ($existingFile['file_type'] ?? 'application/octet-stream'),
            ];
        }

        return $result;
    }

    /**
     * Explicit Replace when the canonical unsuffixed visible file is absent.
     * A new canonical file is created and the Keep Both copy remains untouched.
     *
     * @param array<string,mixed> $run
     * @param array<string,mixed> $existing
     * @param array<string,mixed> $existingFile
     * @return array<string,mixed>
     */
    private function createCanonicalReplacement(
        int $userId,
        string $deviceId,
        int $backupDeviceId,
        string $runId,
        array $run,
        string $sourceRoot,
        string $sourceRelativePath,
        string $sourcePathHash,
        string $actualHash,
        int $fileSize,
        ?string $sourceModifiedAt,
        string $originalName,
        string $incomingMime,
        string $tmpPath,
        array $existing,
        array $existingFile
    ): array {
        $backupItemId = (int) ($existing['id'] ?? 0);
        if ($backupItemId <= 0) {
            throw new ApiException('Existing backup metadata is invalid.', 409, 'backup_existing_file_invalid');
        }

        $parent = $this->requireUploadParent(
            $userId,
            $backupDeviceId,
            $sourceRoot,
            $sourceRelativePath
        );
        $folderId = (int) $parent['folder_id'];

        $cluster = $this->folders->findClusterForUser($userId);
        if (!$cluster || (int) ($cluster['id'] ?? 0) <= 0) {
            throw new ApiException(
                'Mobile Backup cluster is not mapped to this NAS user.',
                409,
                'mobile_backup_cluster_not_mapped'
            );
        }
        $clusterId = (int) $cluster['id'];

        $this->requireAvailableStorage($userId, $fileSize);

        $stored = $this->storeInCanonicalBackupPath(
            $userId,
            $deviceId,
            $sourceRoot,
            $parent['parent_relative'],
            $originalName,
            $tmpPath
        );
        if ($stored['mime_type'] === 'application/octet-stream' && $incomingMime !== '') {
            $stored['mime_type'] = $incomingMime;
        }

        try {
            $fileId = $this->fileRepository->create([
                'user_id' => $userId,
                'cluster_id' => $clusterId,
                'folder_id' => $folderId,
                'filename' => $stored['stored_filename'],
                'original_name' => $originalName,
                'file_path' => $stored['stored_path'],
                'file_size' => $fileSize,
                'file_type' => $stored['mime_type'],
                'file_category' => FileHelper::getFileCategory($stored['mime_type']),
                'file_hash' => $actualHash,
            ]);
        } catch (\Throwable $e) {
            @unlink($stored['destination']);
            throw $e;
        }

        try {
            $backupItem = $this->items->updateFileMapping(
                $userId,
                $backupDeviceId,
                $backupItemId,
                (int) $fileId,
                $folderId,
                $sourceRoot,
                $sourceRelativePath,
                $sourcePathHash,
                $fileSize,
                $actualHash,
                $sourceModifiedAt,
                $stored['mime_type']
            );
            if (!is_array($backupItem)) {
                throw new \RuntimeException('Unable to update mobile backup item mapping.');
            }
        } catch (\Throwable $e) {
            try {
                $this->fileRepository->forceDelete((int) $fileId);
            } catch (\Throwable $cleanupError) {
                error_log('[nas_api][backup] canonical replacement DB cleanup failed: ' . $cleanupError->getMessage());
            }
            @unlink($stored['destination']);
            throw $e;
        }

        try {
            $this->userRepository->updateStorageUsed($userId, $fileSize, 'add');
            $updatedRun = $this->runs->addUploadedFile($userId, $runId, $fileSize);
        } catch (\Throwable $e) {
            try {
                $this->restorePreviousBackupMapping(
                    $userId,
                    $backupDeviceId,
                    $backupItemId,
                    $sourcePathHash,
                    $existing,
                    $existingFile
                );
                $this->fileRepository->forceDelete((int) $fileId);
                $this->userRepository->updateStorageUsed($userId, $fileSize, 'subtract');
            } catch (\Throwable $rollbackError) {
                error_log('[nas_api][backup] canonical replacement accounting rollback failed: ' . $rollbackError->getMessage());
            }
            @unlink($stored['destination']);
            throw $e;
        }

        return [
            'skipped' => false,
            'reason' => 'source_changed_replaced',
            'conflict_action' => 'replace',
            'file_id' => (int) $fileId,
            'folder_id' => $folderId,
            'backup_item' => $backupItem,
            'run' => $updatedRun ?: $run,
            'source_path_hash' => $sourcePathHash,
            'file_hash' => $actualHash,
            'file_size' => $fileSize,
            'mime_type' => $stored['mime_type'],
            'original_name' => $originalName,
            'stored_path' => $stored['stored_path'],
            'retained_file' => [
                'file_id' => (int) ($existingFile['id'] ?? 0),
                'folder_id' => (int) ($existingFile['folder_id'] ?? $folderId),
                'original_name' => (string) ($existingFile['original_name'] ?? ''),
                'file_size' => (int) ($existingFile['file_size'] ?? 0),
                'file_hash' => (string) ($existingFile['file_hash'] ?? ''),
                'mime_type' => (string) ($existingFile['file_type'] ?? 'application/octet-stream'),
            ],
        ];
    }

    /**
     * @param array<string,mixed> $existing
     * @param array<string,mixed> $existingFile
     */
    private function restorePreviousBackupMapping(
        int $userId,
        int $backupDeviceId,
        int $backupItemId,
        string $sourcePathHash,
        array $existing,
        array $existingFile
    ): void {
        $this->items->updateFileMapping(
            $userId,
            $backupDeviceId,
            $backupItemId,
            (int) ($existingFile['id'] ?? ($existing['file_id'] ?? 0)),
            (int) ($existingFile['folder_id'] ?? ($existing['folder_id'] ?? 0)),
            (string) ($existing['source_root'] ?? ''),
            (string) ($existing['source_relative_path'] ?? ''),
            $sourcePathHash,
            (int) ($existing['source_size'] ?? 0),
            (string) ($existing['source_hash'] ?? ''),
            $existing['source_modified_at'] ?? null,
            (string) ($existing['mime_type'] ?? ($existingFile['file_type'] ?? 'application/octet-stream'))
        );
    }

    /** @return array<string,mixed>|null */
    private function findActiveFileByOriginalName(
        int $userId,
        int $folderId,
        string $originalName
    ): ?array {
        $files = $this->fileRepository->getFilesByFolderId($folderId, $userId);
        foreach ($files as $file) {
            if (!is_array($file)) {
                continue;
            }
            $visibleName = (string) ($file['original_name'] ?? $file['display_name'] ?? '');
            if (strcasecmp($visibleName, $originalName) !== 0) {
                continue;
            }

            $full = $this->fileRepository->find((int) ($file['id'] ?? 0));
            if (is_array($full)) {
                return $full;
            }
        }

        return null;
    }

    /**
     * Preserve the existing cloud file and create a second NAS file only after
     * the caller explicitly chose conflict_action=keep_both. The backup item
     * is moved to the new/current phone version so later unchanged scans skip.
     *
     * @param array<string,mixed> $run
     * @param array<string,mixed> $existing
     * @param array<string,mixed> $existingFile
     * @return array<string,mixed>
     */
    private function keepBothChangedSource(
        int $userId,
        string $deviceId,
        int $backupDeviceId,
        string $runId,
        array $run,
        string $sourceRoot,
        string $sourceRelativePath,
        string $sourcePathHash,
        string $actualHash,
        int $fileSize,
        ?string $sourceModifiedAt,
        string $incomingName,
        string $incomingMime,
        string $tmpPath,
        array $existing,
        array $existingFile
    ): array {
        $backupItemId = (int) ($existing['id'] ?? 0);
        $oldFileId = (int) ($existingFile['id'] ?? 0);
        if ($backupItemId <= 0 || $oldFileId <= 0) {
            throw new ApiException('Existing backup metadata is invalid.', 409, 'backup_existing_file_invalid');
        }

        $parent = $this->requireUploadParent(
            $userId,
            $backupDeviceId,
            $sourceRoot,
            $sourceRelativePath
        );
        $parentRelative = $parent['parent_relative'];
        $folderId = $parent['folder_id'];

        $cluster = $this->folders->findClusterForUser($userId);
        if (!$cluster || (int) ($cluster['id'] ?? 0) <= 0) {
            throw new ApiException(
                'Mobile Backup cluster is not mapped to this NAS user.',
                409,
                'mobile_backup_cluster_not_mapped'
            );
        }
        $clusterId = (int) $cluster['id'];

        $this->requireAvailableStorage($userId, $fileSize);

        $visibleName = $this->nextAvailableOriginalName($userId, $folderId, $incomingName);
        $stored = $this->storeInCanonicalBackupPath(
            $userId,
            $deviceId,
            $sourceRoot,
            $parentRelative,
            $visibleName,
            $tmpPath
        );

        try {
            $newFileId = $this->fileRepository->create([
                'user_id' => $userId,
                'cluster_id' => $clusterId,
                'folder_id' => $folderId,
                'filename' => $stored['stored_filename'],
                'original_name' => $visibleName,
                'file_path' => $stored['stored_path'],
                'file_size' => $fileSize,
                'file_type' => $stored['mime_type'],
                'file_category' => FileHelper::getFileCategory($stored['mime_type']),
                'file_hash' => $actualHash,
            ]);
        } catch (\Throwable $e) {
            @unlink($stored['destination']);
            throw $e;
        }

        try {
            $backupItem = $this->items->updateFileMapping(
                $userId,
                $backupDeviceId,
                $backupItemId,
                (int) $newFileId,
                $folderId,
                $sourceRoot,
                $sourceRelativePath,
                $sourcePathHash,
                $fileSize,
                $actualHash,
                $sourceModifiedAt,
                $stored['mime_type']
            );

            if (!is_array($backupItem)) {
                throw new \RuntimeException('Unable to update mobile backup item mapping.');
            }
        } catch (\Throwable $e) {
            try {
                $this->fileRepository->forceDelete((int) $newFileId);
            } catch (\Throwable $cleanupError) {
                error_log('[nas_api][backup] keep-both DB cleanup failed: ' . $cleanupError->getMessage());
            }
            @unlink($stored['destination']);
            throw $e;
        }

        try {
            $this->userRepository->updateStorageUsed($userId, $fileSize, 'add');
            $updatedRun = $this->runs->addUploadedFile($userId, $runId, $fileSize);
        } catch (\Throwable $e) {
            try {
                $this->items->updateFileMapping(
                    $userId,
                    $backupDeviceId,
                    $backupItemId,
                    $oldFileId,
                    (int) ($existing['folder_id'] ?? ($existingFile['folder_id'] ?? $folderId)),
                    (string) ($existing['source_root'] ?? $sourceRoot),
                    (string) ($existing['source_relative_path'] ?? $sourceRelativePath),
                    $sourcePathHash,
                    (int) ($existing['source_size'] ?? 0),
                    (string) ($existing['source_hash'] ?? ''),
                    $existing['source_modified_at'] ?? null,
                    (string) ($existing['mime_type'] ?? ($existingFile['file_type'] ?? 'application/octet-stream'))
                );
                $this->fileRepository->forceDelete((int) $newFileId);
                $this->userRepository->updateStorageUsed($userId, $fileSize, 'subtract');
            } catch (\Throwable $rollbackError) {
                error_log('[nas_api][backup] keep-both accounting rollback failed: ' . $rollbackError->getMessage());
            }
            @unlink($stored['destination']);
            throw $e;
        }

        return [
            'skipped' => false,
            'reason' => 'source_changed_kept_both',
            'conflict_action' => 'keep_both',
            'file_id' => (int) $newFileId,
            'folder_id' => $folderId,
            'backup_item' => $backupItem,
            'run' => $updatedRun ?: $run,
            'source_path_hash' => $sourcePathHash,
            'file_hash' => $actualHash,
            'file_size' => $fileSize,
            'mime_type' => $stored['mime_type'],
            'original_name' => $visibleName,
            'stored_path' => $stored['stored_path'],
            'retained_file' => [
                'file_id' => $oldFileId,
                'folder_id' => (int) ($existingFile['folder_id'] ?? 0),
                'original_name' => (string) ($existingFile['original_name'] ?? ''),
                'file_size' => (int) ($existingFile['file_size'] ?? 0),
                'file_hash' => (string) ($existingFile['file_hash'] ?? ($existing['source_hash'] ?? '')),
                'mime_type' => (string) ($existingFile['file_type'] ?? ($existing['mime_type'] ?? 'application/octet-stream')),
            ],
        ];
    }

    /**
     * @return array{parent_relative:string,folder_id:int}
     */
    private function requireUploadParent(
        int $userId,
        int $backupDeviceId,
        string $sourceRoot,
        string $sourceRelativePath
    ): array {
        $parentRelative = dirname($sourceRelativePath);
        if ($parentRelative === '.') {
            // A file directly inside a source root is valid, for example
            // Download/app.apk or Documents/report.pdf. The source-root folder
            // mapping uses an empty source_relative_path.
            $parentRelative = '';
        }

        $folderHash = hash('sha256', $sourceRoot . "\n" . $parentRelative);
        $folderMaps = $this->items->findByPathHashes($userId, $backupDeviceId, [$folderHash]);
        $folderMap = $folderMaps[$folderHash] ?? null;
        if (!is_array($folderMap) || ($folderMap['folder_id'] ?? null) === null) {
            throw new ApiException(
                'The source folder has not been ensured for this device.',
                409,
                'backup_parent_folder_not_ensured'
            );
        }
        if ((int) ($folderMap['cloud_deleted'] ?? 0) === 1) {
            throw new ApiException(
                'The source folder is suppressed because its cloud backup was deleted.',
                409,
                'backup_parent_folder_suppressed'
            );
        }

        return [
            'parent_relative' => $parentRelative,
            'folder_id' => (int) $folderMap['folder_id'],
        ];
    }

    private function requireAvailableStorage(int $userId, int $requiredBytes): void
    {
        if ($requiredBytes <= 0) {
            return;
        }

        $storage = $this->userRepository->getStorageInfo($userId);
        $used = (int) ($storage['storage_used'] ?? 0);
        $limit = (int) ($storage['storage_limit'] ?? 0);
        if ($limit > 0 && $requiredBytes > max(0, $limit - $used)) {
            throw new ApiException('Not enough NAS storage available.', 413, 'backup_storage_limit_exceeded');
        }
    }

    /** @param array<string,mixed> $upload */
    private function uploadedOriginalName(array $upload): string
    {
        $originalName = basename(str_replace('\\', '/', (string) ($upload['name'] ?? '')));
        if ($originalName === '' || $originalName === '.' || $originalName === '..') {
            throw new ApiException('Uploaded filename is invalid.', 422, 'invalid_backup_filename');
        }
        return $originalName;
    }

    private function detectMimeType(string $path): string
    {
        $mimeType = 'application/octet-stream';
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo !== false) {
            $detected = finfo_file($finfo, $path);
            finfo_close($finfo);
            if (is_string($detected) && $detected !== '') {
                $mimeType = $detected;
            }
        }
        return $mimeType;
    }

    /**
     * @return array{stored_filename:string,destination:string,stored_path:string,mime_type:string}
     */
    private function storeInCanonicalBackupPath(
        int $userId,
        string $deviceId,
        string $sourceRoot,
        string $parentRelative,
        string $originalName,
        string $tmpPath
    ): array {
        $clusterDir = $this->storageService->createClusterDirectory($userId, 'mobile-backup');
        $logicalDir = $deviceId . '/' . $sourceRoot;
        if ($parentRelative !== '') {
            $logicalDir .= '/' . $parentRelative;
        }
        $destinationDir = rtrim($clusterDir, '/\\') . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $logicalDir);

        if (!is_dir($destinationDir)) {
            throw new ApiException(
                'Backup destination folder is missing. Call /backup/folders/ensure first.',
                409,
                'backup_destination_missing'
            );
        }

        $storedFilename = $this->storageService->generateUniqueFilename($originalName);
        $destination = rtrim($destinationDir, '/\\') . DIRECTORY_SEPARATOR . $storedFilename;
        if (!$this->storageService->saveUploadedFile($tmpPath, $destination)) {
            throw new ApiException('Unable to store uploaded file.', 500, 'backup_file_store_failed');
        }

        $mimeType = $this->detectMimeType($destination);
        $storedPath = 'user_' . $userId . '/mobile-backup/'
            . $deviceId . '/' . $sourceRoot;
        if ($parentRelative !== '') {
            $storedPath .= '/' . $parentRelative;
        }
        $storedPath .= '/' . $storedFilename;
        $storedPath = str_replace('\\', '/', $storedPath);

        return [
            'stored_filename' => $storedFilename,
            'destination' => $destination,
            'stored_path' => $storedPath,
            'mime_type' => $mimeType,
        ];
    }

    private function nextAvailableOriginalName(int $userId, int $folderId, string $requestedName): string
    {
        $files = $this->fileRepository->getFilesByFolderId($folderId, $userId);
        $used = [];
        foreach ($files as $file) {
            if (!is_array($file)) {
                continue;
            }
            $name = (string) ($file['original_name'] ?? $file['display_name'] ?? '');
            if ($name !== '') {
                $used[strtolower($name)] = true;
            }
        }

        if (!isset($used[strtolower($requestedName)])) {
            return $requestedName;
        }

        $extension = pathinfo($requestedName, PATHINFO_EXTENSION);
        $base = pathinfo($requestedName, PATHINFO_FILENAME);
        if ($base === '') {
            $base = $requestedName;
        }

        for ($index = 1; $index <= 10000; $index++) {
            $candidate = $base . ' (' . $index . ')' . ($extension !== '' ? '.' . $extension : '');
            if (!isset($used[strtolower($candidate)])) {
                return $candidate;
            }
        }

        throw new ApiException(
            'Unable to generate a Keep Both filename.',
            409,
            'backup_keep_both_name_exhausted'
        );
    }

    /**
     * @return array{folder_id:int,created:bool}
     */
    private function ensureFolderNode(
        int $userId,
        int $clusterId,
        ?int $parentId,
        string $name
    ): array {
        $existing = $this->folders->findActiveFolder(
            $userId,
            $clusterId,
            $parentId,
            $name
        );
        if ($existing) {
            return [
                'folder_id' => (int) $existing['id'],
                'created' => false,
            ];
        }

        try {
            $created = $this->folderService->createFolder(
                $userId,
                $name,
                $parentId,
                'mobile-backup'
            );
        } catch (\Throwable $e) {
            // A concurrent/idempotent request may have created the same node
            // after our first lookup. Re-read before surfacing the error.
            $existing = $this->folders->findActiveFolder(
                $userId,
                $clusterId,
                $parentId,
                $name
            );
            if ($existing) {
                return [
                    'folder_id' => (int) $existing['id'],
                    'created' => false,
                ];
            }
            throw $e;
        }

        $folderId = (int) ($created['folder_id'] ?? 0);
        if ($folderId <= 0) {
            $existing = $this->folders->findActiveFolder(
                $userId,
                $clusterId,
                $parentId,
                $name
            );
            $folderId = $existing ? (int) $existing['id'] : 0;
        }

        if ($folderId <= 0) {
            throw new ApiException(
                'NAS folder creation did not return a folder id.',
                500,
                'backup_folder_create_failed'
            );
        }

        return [
            'folder_id' => $folderId,
            'created' => true,
        ];
    }


    /** @return array<string,mixed> */
    private function defaultBackupSettings(): array
    {
        return [
            'auto_backup_enabled' => false,
            'backup_frequency' => 'weekly',
            'wifi_only' => true,
            'charging_only' => false,
            'preferred_weekday' => 7,
            'preferred_time' => '02:00',
            'timezone' => 'UTC',
        ];
    }

    /**
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    private function normalizeBackupSettingsRow(array $row): array
    {
        $time = (string) ($row['preferred_time'] ?? '02:00:00');
        if (strlen($time) >= 5) {
            $time = substr($time, 0, 5);
        }

        return [
            'auto_backup_enabled' => (int) ($row['auto_backup_enabled'] ?? 0) === 1,
            'backup_frequency' => (string) ($row['backup_frequency'] ?? 'weekly'),
            'wifi_only' => (int) ($row['wifi_only'] ?? 1) === 1,
            'charging_only' => (int) ($row['charging_only'] ?? 0) === 1,
            'preferred_weekday' => (int) ($row['preferred_weekday'] ?? 7),
            'preferred_time' => $time,
            'timezone' => (string) ($row['timezone'] ?? 'UTC'),
        ];
    }


    /** @return array<string,mixed> */
    private function requireRunForDevice(
        int $userId,
        int $backupDeviceId,
        string $runId
    ): array {
        $run = $this->runs->findByRunId($userId, $runId);
        if (!$run) {
            throw new ApiException(
                'Backup run was not found.',
                404,
                'backup_run_not_found'
            );
        }

        if ((int) ($run['backup_device_id'] ?? 0) !== $backupDeviceId) {
            throw new ApiException(
                'Backup run does not belong to this device.',
                409,
                'backup_run_device_mismatch'
            );
        }

        return $run;
    }

    /** @return array<string,mixed> */
    private function requireEnabledDevice(int $userId, string $deviceId): array
    {
        $device = $this->devices->findByDeviceId($userId, $deviceId);
        if (!$device) {
            throw new ApiException(
                'Backup device is not registered for this user.',
                404,
                'backup_device_not_found'
            );
        }

        if ((int) ($device['backup_enabled'] ?? 0) !== 1) {
            throw new ApiException(
                'Backup is disabled for this device.',
                409,
                'backup_device_disabled'
            );
        }

        return $device;
    }

    /**
     * @param array<string,mixed> $incoming
     * @param array<string,mixed> $stored
     */
    private function isUnchanged(array $incoming, array $stored): bool
    {
        if ($stored['file_id'] === null) {
            return false;
        }

        $status = (string) ($stored['backup_status'] ?? '');
        if (!in_array($status, ['uploaded', 'unchanged'], true)) {
            return false;
        }

        $incomingHash = $incoming['source_hash'] ?? null;
        $storedHash = $stored['source_hash'] ?? null;
        if ($incomingHash !== null && $storedHash !== null) {
            return hash_equals((string) $storedHash, (string) $incomingHash);
        }

        $incomingModified = $incoming['source_modified_at'] ?? null;
        $storedModified = $stored['source_modified_at'] ?? null;

        return (int) ($incoming['source_size'] ?? -1) === (int) ($stored['source_size'] ?? -2)
            && $incomingModified !== null
            && $storedModified !== null
            && (string) $incomingModified === (string) $storedModified;
    }
}
