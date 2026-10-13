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

final class MobileBackupService
{
    private MobileBackupDeviceRepository $devices;
    private MobileBackupRunRepository $runs;
    private MobileBackupItemRepository $items;
    private MobileBackupFolderRepository $folders;
    private FolderService $folderService;
    private FileRepository $fileRepository;
    private UserRepository $userRepository;
    private StorageService $storageService;

    public function __construct(
        MobileBackupDeviceRepository $devices,
        MobileBackupRunRepository $runs,
        MobileBackupItemRepository $items,
        MobileBackupFolderRepository $folders,
        FolderService $folderService,
        FileRepository $fileRepository,
        UserRepository $userRepository,
        StorageService $storageService
    ) {
        $this->devices = $devices;
        $this->runs = $runs;
        $this->items = $items;
        $this->folders = $folders;
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
                } elseif ($this->isUnchanged($file, $row)) {
                    $action = 'skip';
                    $reason = 'unchanged';
                } else {
                    $action = 'upload';
                    $reason = 'source_changed';
                }
            }

            if ($action === 'upload') {
                $uploadCount++;
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
                'file_id' => is_array($row) && $row['file_id'] !== null
                    ? (int) $row['file_id']
                    : null,
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

        if (is_array($existing)) {
            if ((int) ($existing['cloud_deleted'] ?? 0) === 1) {
                throw new ApiException(
                    'This source path is suppressed because its cloud backup was deleted.',
                    409,
                    'backup_source_suppressed'
                );
            }

            $storedHash = (string) ($existing['source_hash'] ?? '');
            if (
                $existing['file_id'] !== null
                && $storedHash !== ''
                && hash_equals($storedHash, $actualHash)
                && (int) ($existing['source_size'] ?? -1) === $fileSize
            ) {
                // A retry of an already-backed-up file is idempotent: do not
                // create a second NAS file and do not increment run counters.
                // Read the canonical NAS file row so the response shape is the
                // same as a fresh upload (useful to Flutter/Postman callers).
                $existingFile = $this->fileRepository->find((int) $existing['file_id']);

                return [
                    'skipped' => true,
                    'reason' => 'already_uploaded',
                    'file_id' => (int) $existing['file_id'],
                    'folder_id' => is_array($existingFile)
                        ? (int) ($existingFile['folder_id'] ?? ($existing['folder_id'] ?? 0))
                        : (int) ($existing['folder_id'] ?? 0),
                    'backup_item' => $existing,
                    'run' => $run,
                    'source_path_hash' => $sourcePathHash,
                    'file_hash' => $actualHash,
                    'file_size' => $fileSize,
                    'mime_type' => is_array($existingFile)
                        ? (string) ($existingFile['file_type'] ?? ($existing['mime_type'] ?? 'application/octet-stream'))
                        : (string) ($existing['mime_type'] ?? 'application/octet-stream'),
                    'original_name' => is_array($existingFile)
                        ? (string) ($existingFile['original_name'] ?? '')
                        : '',
                    'stored_path' => is_array($existingFile)
                        ? (string) ($existingFile['file_path'] ?? '')
                        : '',
                ];
            }

            throw new ApiException(
                'A different version of this source path is already backed up. Replacement is handled in the next upload phase.',
                409,
                'backup_source_changed'
            );
        }

        $parentRelative = dirname($sourceRelativePath);
        if ($parentRelative === '.') {
            // A file directly inside a source root is valid, for example
            // Download/app.apk or Documents/report.pdf. The source-root folder
            // mapping uses an empty source_relative_path.
            $parentRelative = '';
        }

        $folderHash = hash('sha256', $sourceRoot . "\n" . $parentRelative);
        $folderMaps = $this->items->findByPathHashes(
            $userId,
            (int) $device['id'],
            [$folderHash]
        );
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
        $folderId = (int) $folderMap['folder_id'];

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

        $storage = $this->userRepository->getStorageInfo($userId);
        $used = (int) ($storage['storage_used'] ?? 0);
        $limit = (int) ($storage['storage_limit'] ?? 0);
        if ($limit > 0 && $fileSize > max(0, $limit - $used)) {
            throw new ApiException('Not enough NAS storage available.', 413, 'backup_storage_limit_exceeded');
        }

        // Keep the same naming semantics as the existing NAS FileService:
        // the user-visible file name comes from the actual multipart upload,
        // while source_relative_path remains mobile backup identity metadata.
        $originalName = basename(str_replace('\\', '/', (string) $upload['name']));
        if ($originalName === '' || $originalName === '.' || $originalName === '..') {
            throw new ApiException('Uploaded filename is invalid.', 422, 'invalid_backup_filename');
        }

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

        $mimeType = 'application/octet-stream';
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo !== false) {
            $detected = finfo_file($finfo, $destination);
            finfo_close($finfo);
            if (is_string($detected) && $detected !== '') {
                $mimeType = $detected;
            }
        }

        $storedPath = 'user_' . $userId . '/mobile-backup/'
            . $deviceId . '/' . $sourceRoot;
        if ($parentRelative !== '') {
            $storedPath .= '/' . $parentRelative;
        }
        $storedPath .= '/' . $storedFilename;
        $storedPath = str_replace('\\', '/', $storedPath);

        try {
            $fileId = $this->fileRepository->create([
                'user_id' => $userId,
                'cluster_id' => $clusterId,
                'folder_id' => $folderId,
                'filename' => $storedFilename,
                'original_name' => $originalName,
                'file_path' => $storedPath,
                'file_size' => $fileSize,
                'file_type' => $mimeType,
                'file_category' => FileHelper::getFileCategory($mimeType),
                'file_hash' => $actualHash,
            ]);
        } catch (\Throwable $e) {
            @unlink($destination);
            throw $e;
        }

        try {
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
                $mimeType
            );
        } catch (\Throwable $e) {
            // The NAS file row has already been created. Do not silently remove
            // it here because repository/business-layer deletion may carry
            // accounting semantics. Surface the failure for explicit repair.
            throw $e;
        }

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
            'mime_type' => $mimeType,
            'original_name' => $originalName,
            'stored_path' => $storedPath,
        ];
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
