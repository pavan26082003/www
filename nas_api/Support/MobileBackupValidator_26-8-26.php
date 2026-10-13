<?php

declare(strict_types=1);

namespace NasApi\Support;

use DateTimeImmutable;
use Exception;
use NasApi\Core\ApiException;

final class MobileBackupValidator
{
    /**
     * @param array<string,mixed> $input
     * @return array{device_id:string,device_name:string,platform:string,manufacturer:?string,model:?string,os_version:?string,app_version:?string}
     */
    public static function device(array $input): array
    {
        $deviceId = self::deviceId($input);
        $deviceName = self::requiredString($input, 'device_name', 255);
        $platform = strtolower(self::requiredString($input, 'platform', 32));

        if (!in_array($platform, ['android', 'ios'], true)) {
            throw new ApiException(
                'platform must be android or ios.',
                422,
                'invalid_platform'
            );
        }

        return [
            'device_id' => $deviceId,
            'device_name' => $deviceName,
            'platform' => $platform,
            'manufacturer' => self::optionalString($input, 'manufacturer', 100),
            'model' => self::optionalString($input, 'model', 100),
            'os_version' => self::optionalString($input, 'os_version', 64),
            'app_version' => self::optionalString($input, 'app_version', 64),
        ];
    }

    /**
     * @param array<string,mixed> $input
     * @return array{device_id:string}
     */
    public static function backupInit(array $input): array
    {
        return ['device_id' => self::deviceId($input)];
    }

    /**
     * @param array<string,mixed> $input
     * @return array{device_id:string}
     */
    public static function backupStatus(array $input): array
    {
        return self::backupInit($input);
    }

    /**
     * @param array<string,mixed> $input
     * @return array{device_id:string,run_id:string,folders:array<int,array<string,string>>,files:array<int,array<string,mixed>>}
     */
    public static function backupPlan(array $input): array
    {
        $deviceId = self::deviceId($input);
        $runId = self::requiredString($input, 'run_id', 64);

        if (!preg_match('/^mb_[A-Za-z0-9_\-]+$/', $runId)) {
            throw new ApiException(
                'run_id is invalid.',
                422,
                'invalid_run_id'
            );
        }

        $foldersRaw = $input['folders'] ?? [];
        $filesRaw = $input['files'] ?? [];

        if (!is_array($foldersRaw)) {
            throw new ApiException(
                'folders must be an array.',
                422,
                'invalid_folders'
            );
        }

        if (!is_array($filesRaw)) {
            throw new ApiException(
                'files must be an array.',
                422,
                'invalid_files'
            );
        }

        if (count($foldersRaw) > 1000 || count($filesRaw) > 1000) {
            throw new ApiException(
                'A backup plan request may contain at most 1000 folders and 1000 files.',
                422,
                'backup_plan_batch_too_large'
            );
        }

        $folders = [];
        foreach ($foldersRaw as $index => $folder) {
            if (!is_array($folder)) {
                throw new ApiException(
                    'Each folders entry must be an object.',
                    422,
                    'invalid_folder_entry'
                );
            }

            $folders[] = self::sourceLocation($folder, 'folders[' . $index . ']');
        }

        $files = [];
        foreach ($filesRaw as $index => $file) {
            if (!is_array($file)) {
                throw new ApiException(
                    'Each files entry must be an object.',
                    422,
                    'invalid_file_entry'
                );
            }

            $location = self::sourceLocation($file, 'files[' . $index . ']');
            $sourceSize = self::nonNegativeInteger($file, 'source_size', $index);
            $sourceHash = self::optionalSha256($file, 'source_hash', $index);
            $modifiedAt = self::optionalDateTime($file, 'source_modified_at', $index);
            $mimeType = self::optionalString($file, 'mime_type', 191);

            $files[] = [
                'source_root' => $location['source_root'],
                'source_relative_path' => $location['source_relative_path'],
                'source_size' => $sourceSize,
                'source_hash' => $sourceHash,
                'source_modified_at' => $modifiedAt,
                'mime_type' => $mimeType,
            ];
        }

        return [
            'device_id' => $deviceId,
            'run_id' => $runId,
            'folders' => $folders,
            'files' => $files,
        ];
    }


    /**
     * @param array<string,mixed> $input
     * @return array{device_id:string,run_id:string,folders:array<int,array<string,string>>}
     */
    public static function backupFoldersEnsure(array $input): array
    {
        $deviceId = self::deviceId($input);
        $runId = self::requiredString($input, 'run_id', 64);

        if (!preg_match('/^mb_[A-Za-z0-9_\-]+$/', $runId)) {
            throw new ApiException(
                'run_id is invalid.',
                422,
                'invalid_run_id'
            );
        }

        $foldersRaw = $input['folders'] ?? null;
        if (!is_array($foldersRaw)) {
            throw new ApiException(
                'folders must be an array.',
                422,
                'invalid_folders'
            );
        }

        if (count($foldersRaw) > 1000) {
            throw new ApiException(
                'A folder ensure request may contain at most 1000 folders.',
                422,
                'backup_folder_batch_too_large'
            );
        }

        $folders = [];
        foreach ($foldersRaw as $index => $folder) {
            if (!is_array($folder)) {
                throw new ApiException(
                    'Each folders entry must be an object.',
                    422,
                    'invalid_folder_entry'
                );
            }
            $folders[] = self::sourceLocation($folder, 'folders[' . $index . ']');
        }

        return [
            'device_id' => $deviceId,
            'run_id' => $runId,
            'folders' => $folders,
        ];
    }

    /**
     * Validate multipart fields for POST /v1/backup/upload.
     *
     * @param array<string,mixed> $input
     * @return array{device_id:string,run_id:string,source_root:string,source_relative_path:string,source_hash:?string,source_modified_at:?string}
     */
    public static function backupUpload(array $input): array
    {
        $deviceId = self::deviceId($input);
        $runId = self::requiredString($input, 'run_id', 64);
        if (!preg_match('/^mb_[A-Za-z0-9_\-]+$/', $runId)) {
            throw new ApiException('run_id is invalid.', 422, 'invalid_run_id');
        }

        $location = self::sourceLocation($input, 'upload');
        $sourceHash = self::optionalSha256($input, 'source_hash', 0);
        $modifiedAt = self::optionalDateTime($input, 'source_modified_at', 0);

        return [
            'device_id' => $deviceId,
            'run_id' => $runId,
            'source_root' => $location['source_root'],
            'source_relative_path' => $location['source_relative_path'],
            'source_hash' => $sourceHash,
            'source_modified_at' => $modifiedAt,
        ];
    }

    /** @param array<string,mixed> $input */
    private static function deviceId(array $input): string
    {
        $deviceId = self::requiredString($input, 'device_id', 128);

        if (
            $deviceId === '.' || $deviceId === '..'
            || preg_match('/[\x00-\x1F\x7F]/', $deviceId)
            || strpos($deviceId, '/') !== false
            || strpos($deviceId, '\\') !== false
        ) {
            throw new ApiException(
                'device_id contains unsupported characters.',
                422,
                'invalid_device_id'
            );
        }

        return $deviceId;
    }

    /**
     * @param array<string,mixed> $input
     * @return array{source_root:string,source_relative_path:string}
     */
    private static function sourceLocation(array $input, string $label): array
    {
        $root = trim((string) ($input['source_root'] ?? ''));
        if ($root === '' || strlen($root) > 100) {
            throw new ApiException(
                $label . '.source_root is required and must be at most 100 characters.',
                422,
                'invalid_source_root'
            );
        }

        if (
            $root === '.' || $root === '..'
            || strpos($root, '/') !== false
            || strpos($root, '\\') !== false
            || preg_match('/[\x00-\x1F\x7F]/', $root)
        ) {
            throw new ApiException(
                $label . '.source_root is invalid.',
                422,
                'invalid_source_root'
            );
        }

        $relative = str_replace('\\', '/', trim((string) ($input['source_relative_path'] ?? '')));
        $relative = preg_replace('#/+#', '/', $relative) ?? $relative;
        $relative = trim($relative, '/');

        if ($relative === '' || strlen($relative) > 1000) {
            throw new ApiException(
                $label . '.source_relative_path is required and must be at most 1000 characters.',
                422,
                'invalid_source_relative_path'
            );
        }

        if (preg_match('/[\x00-\x1F\x7F]/', $relative)) {
            throw new ApiException(
                $label . '.source_relative_path is invalid.',
                422,
                'invalid_source_relative_path'
            );
        }

        foreach (explode('/', $relative) as $segment) {
            if (
                $segment === '' || $segment === '.' || $segment === '..'
                || strlen($segment) > 255
            ) {
                throw new ApiException(
                    $label . '.source_relative_path contains an invalid path segment.',
                    422,
                    'invalid_source_relative_path'
                );
            }
        }

        return [
            'source_root' => $root,
            'source_relative_path' => $relative,
        ];
    }

    /** @param array<string,mixed> $input */
    private static function nonNegativeInteger(array $input, string $key, int $index): int
    {
        if (!array_key_exists($key, $input)) {
            throw new ApiException(
                'files[' . $index . '].' . $key . ' is required.',
                422,
                'source_size_required'
            );
        }

        $value = $input[$key];
        if (is_int($value)) {
            $intValue = $value;
        } elseif (is_string($value) && preg_match('/^\d+$/', $value)) {
            $intValue = (int) $value;
        } else {
            throw new ApiException(
                'files[' . $index . '].' . $key . ' must be a non-negative integer.',
                422,
                'invalid_source_size'
            );
        }

        if ($intValue < 0) {
            throw new ApiException(
                'files[' . $index . '].' . $key . ' must be non-negative.',
                422,
                'invalid_source_size'
            );
        }

        return $intValue;
    }

    /** @param array<string,mixed> $input */
    private static function optionalSha256(array $input, string $key, int $index): ?string
    {
        if (!array_key_exists($key, $input) || $input[$key] === null || trim((string) $input[$key]) === '') {
            return null;
        }

        $hash = strtolower(trim((string) $input[$key]));
        if (!preg_match('/^[a-f0-9]{64}$/', $hash)) {
            throw new ApiException(
                'files[' . $index . '].' . $key . ' must be a SHA-256 hex string.',
                422,
                'invalid_source_hash'
            );
        }

        return $hash;
    }

    /** @param array<string,mixed> $input */
    private static function optionalDateTime(array $input, string $key, int $index): ?string
    {
        if (!array_key_exists($key, $input) || $input[$key] === null || trim((string) $input[$key]) === '') {
            return null;
        }

        try {
            $date = new DateTimeImmutable((string) $input[$key]);
        } catch (Exception $e) {
            throw new ApiException(
                'files[' . $index . '].' . $key . ' is not a valid date/time.',
                422,
                'invalid_source_modified_at'
            );
        }

        return $date->format('Y-m-d H:i:s');
    }

    /** @param array<string,mixed> $input */
    private static function requiredString(array $input, string $key, int $maxLength): string
    {
        $value = trim((string) ($input[$key] ?? ''));
        if ($value === '') {
            throw new ApiException(
                $key . ' is required.',
                400,
                $key . '_required'
            );
        }

        if (strlen($value) > $maxLength) {
            throw new ApiException(
                $key . ' is too long.',
                422,
                $key . '_too_long'
            );
        }

        return $value;
    }

    /** @param array<string,mixed> $input */
    private static function optionalString(array $input, string $key, int $maxLength): ?string
    {
        if (!array_key_exists($key, $input) || $input[$key] === null) {
            return null;
        }

        $value = trim((string) $input[$key]);
        if ($value === '') {
            return null;
        }

        if (strlen($value) > $maxLength) {
            throw new ApiException(
                $key . ' is too long.',
                422,
                $key . '_too_long'
            );
        }

        return $value;
    }
}
