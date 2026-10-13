<?php
namespace App\Services\NAS;

class StorageService
{
    private string $uploadPath;

    public function __construct()
    {
        $this->uploadPath =
            rtrim(ROOTPATH, '/\\')
            . DIRECTORY_SEPARATOR
            . 'uploads'
            . DIRECTORY_SEPARATOR
            . 'NAS'
            . DIRECTORY_SEPARATOR
            . 'uploads'
            . DIRECTORY_SEPARATOR;

        // Intentionally do not touch the filesystem here. Read-only endpoints
        // also instantiate this service and must not fail because uploads are
        // temporarily unavailable.
    }

    /**
     * Ensure a directory exists, is inside the NAS upload root and is writable
     * by the PHP process. Parent directories are created recursively with 0775.
     */
    public function ensureDirectory(string $path): string
    {
        $path = rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
        $this->assertPathInsideUploadRoot($path);

        if (!is_dir($path)) {
            if (!@mkdir($path, 0775, true) && !is_dir($path)) {
                $error = error_get_last();
                throw new \RuntimeException(
                    'Unable to create storage directory: ' . $path
                    . ($error && isset($error['message']) ? ' (' . $error['message'] . ')' : '')
                );
            }
        }

        if (is_link(rtrim($path, '/\\'))) {
            throw new \RuntimeException('Storage directory must not be a symbolic link: ' . $path);
        }

        if (!is_writable($path)) {
            throw new \RuntimeException('Storage directory is not writable by PHP: ' . $path);
        }

        return $path;
    }

    public function getUploadRoot(): string
    {
        return $this->uploadPath;
    }

    public function createUserDirectory(int $userId): string
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('Invalid user ID');
        }

        return $this->ensureDirectory(
            $this->uploadPath . 'user_' . $userId . DIRECTORY_SEPARATOR
        );
    }

    public function createClusterDirectory(int $userId, string $clusterSlug): string
    {
        $clusterSlug = trim($clusterSlug);

        if ($clusterSlug === '' || !preg_match('/^[A-Za-z0-9_-]+$/', $clusterSlug)) {
            throw new \InvalidArgumentException('Invalid cluster slug');
        }

        $userDir = $this->createUserDirectory($userId);

        return $this->ensureDirectory(
            $userDir . $clusterSlug . DIRECTORY_SEPARATOR
        );
    }

    public function generateUniqueFilename(string $originalName): string
    {
        $extension = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
        $base = bin2hex(random_bytes(16));

        return $extension !== '' ? $base . '.' . $extension : $base;
    }

    /**
     * Resolve an application-controlled relative path under the upload root.
     * Absolute paths, NUL bytes and traversal segments are rejected.
     */
    public function getFullPath(string $relativePath): string
    {
        $relativePath = $this->normalizeRelativePath($relativePath);

        if ($relativePath === '') {
            return $this->uploadPath;
        }

        $fullPath = $this->uploadPath
            . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        $this->assertPathInsideUploadRoot($fullPath);
        return $fullPath;
    }

    public function saveUploadedFile(string $tmpPath, string $destinationPath): bool
    {
        $this->assertPathInsideUploadRoot($destinationPath);
        $this->ensureDirectory(dirname($destinationPath));

        if (!is_file($tmpPath)) {
            throw new \RuntimeException('Temporary upload file not found');
        }

        if (!is_uploaded_file($tmpPath)) {
            throw new \RuntimeException('Invalid PHP uploaded file');
        }

        if (is_dir($destinationPath)) {
            throw new \RuntimeException('Upload destination is a directory');
        }

        if (!@move_uploaded_file($tmpPath, $destinationPath)) {
            $error = error_get_last();
            throw new \RuntimeException(
                'Failed to save uploaded file to "' . $destinationPath . '"'
                . ($error && isset($error['message']) ? ': ' . $error['message'] : '')
            );
        }

        return true;
    }

    public function deleteFile(string $relativePath): bool
    {
        $fullPath = $this->getFullPath($relativePath);

        if (!is_file($fullPath) || is_link($fullPath)) {
            return false;
        }

        return unlink($fullPath);
    }

    public function deleteDirectory(string $relativePath): bool
    {
        $fullPath = rtrim($this->getFullPath($relativePath), '/\\');

        // Never allow the entire upload root to be removed through this method.
        if ($this->samePath($fullPath, rtrim($this->uploadPath, '/\\'))) {
            throw new \InvalidArgumentException('Refusing to delete NAS upload root');
        }

        if (!is_dir($fullPath) || is_link($fullPath)) {
            return false;
        }

        return $this->deleteDirectoryRecursive($fullPath);
    }

    public function getFileContent(string $relativePath): string
    {
        $fullPath = $this->getFullPath($relativePath);

        if (!is_file($fullPath)) {
            throw new \RuntimeException('File not found');
        }

        $content = file_get_contents($fullPath);
        if ($content === false) {
            throw new \RuntimeException('Unable to read file');
        }

        return $content;
    }

    public function getMimeType(string $relativePath): string
    {
        $fullPath = $this->getFullPath($relativePath);

        if (!is_file($fullPath)) {
            return 'application/octet-stream';
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            return 'application/octet-stream';
        }

        $mimeType = finfo_file($finfo, $fullPath);
        finfo_close($finfo);

        return is_string($mimeType) && $mimeType !== ''
            ? $mimeType
            : 'application/octet-stream';
    }

    public function getFileSize(string $relativePath): int
    {
        $fullPath = $this->getFullPath($relativePath);
        if (!is_file($fullPath)) {
            return 0;
        }

        $size = filesize($fullPath);
        return $size === false ? 0 : (int) $size;
    }

    public function fileExists(string $relativePath): bool
    {
        return is_file($this->getFullPath($relativePath));
    }

    public function getFilesRecursive(string $relativePath): array
    {
        $fullPath = $this->getFullPath($relativePath);

        if (!is_dir($fullPath)) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $fullPath,
                \RecursiveDirectoryIterator::SKIP_DOTS
            )
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->isLink()) {
                continue;
            }

            $pathName = $file->getPathname();
            $this->assertPathInsideUploadRoot($pathName);

            $relative = substr(
                str_replace('\\', '/', $pathName),
                strlen(str_replace('\\', '/', $this->uploadPath))
            );

            $files[] = [
                'path' => ltrim((string) $relative, '/'),
                'name' => $file->getFilename(),
                'size' => $file->getSize(),
            ];
        }

        return $files;
    }

    public function createZip(array $files, string $zipName): string
    {
        if ($files === []) {
            throw new \InvalidArgumentException('No files to zip');
        }

        $safeZipName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($zipName));
        $safeZipName = trim((string) $safeZipName, '._-');
        if ($safeZipName === '') {
            $safeZipName = 'download';
        }

        $zipPath = rtrim(sys_get_temp_dir(), '/\\')
            . DIRECTORY_SEPARATOR
            . $safeZipName
            . '_'
            . bin2hex(random_bytes(6))
            . '.zip';

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Could not create zip file');
        }

        foreach ($files as $file) {
            if (!isset($file['relative_path'], $file['name'])) {
                continue;
            }

            $fullPath = $this->getFullPath((string) $file['relative_path']);
            if (is_file($fullPath)) {
                $zip->addFile($fullPath, basename((string) $file['name']));
            }
        }

        $zip->close();
        return $zipPath;
    }

    public function downloadFile(string $relativePath, string $downloadName = null): void
    {
        $fullPath = $this->getFullPath($relativePath);
        if (!is_file($fullPath)) {
            throw new \RuntimeException('File not found');
        }

        $mimeType = $this->getMimeType($relativePath);
        $downloadName = $this->safeHeaderFilename(
            $downloadName ?? basename($relativePath)
        );

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Content-Length: ' . (string) filesize($fullPath));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        readfile($fullPath);
        exit;
    }

    public function previewFile(string $relativePath): void
    {
        $fullPath = $this->getFullPath($relativePath);
        if (!is_file($fullPath)) {
            throw new \RuntimeException('File not found');
        }

        $mimeType = $this->getMimeType($relativePath);
        $name = $this->safeHeaderFilename(basename($relativePath));

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . $name . '"');
        header('Cache-Control: public, max-age=3600');
        header('X-Content-Type-Options: nosniff');

        readfile($fullPath);
        exit;
    }

    private function normalizeRelativePath(string $relativePath): string
    {
        if (strpos($relativePath, "\0") !== false) {
            throw new \InvalidArgumentException('Invalid path');
        }

        $path = str_replace('\\', '/', trim($relativePath));

        if ($path === '') {
            return '';
        }

        if ($path[0] === '/' || preg_match('/^[A-Za-z]:\//', $path)) {
            throw new \InvalidArgumentException('Absolute paths are not allowed');
        }

        $parts = [];
        foreach (explode('/', $path) as $part) {
            if ($part === '') {
                continue;
            }

            if ($part === '.' || $part === '..') {
                throw new \InvalidArgumentException('Path traversal is not allowed');
            }

            $parts[] = $part;
        }

        return implode('/', $parts);
    }

    private function assertPathInsideUploadRoot(string $path): void
    {
        if (strpos($path, "\0") !== false) {
            throw new \InvalidArgumentException('Invalid storage path');
        }

        $candidate = $this->canonicalAbsolutePath($path);
        $root = $this->canonicalAbsolutePath($this->uploadPath);

        $candidateCompare = DIRECTORY_SEPARATOR === '\\' ? strtolower($candidate) : $candidate;
        $rootCompare = DIRECTORY_SEPARATOR === '\\' ? strtolower($root) : $root;

        if (
            $candidateCompare !== $rootCompare
            && !str_starts_with($candidateCompare, $rootCompare . '/')
        ) {
            throw new \InvalidArgumentException('Storage path escapes NAS upload root');
        }
    }

    private function canonicalAbsolutePath(string $path): string
    {
        $path = str_replace('\\', '/', trim($path));
        $path = preg_replace('#/+#', '/', $path) ?? $path;

        $prefix = '';
        if (preg_match('/^[A-Za-z]:\//', $path)) {
            $prefix = strtoupper(substr($path, 0, 2)) . '/';
            $path = substr($path, 3);
        } elseif (str_starts_with($path, '/')) {
            $prefix = '/';
            $path = ltrim($path, '/');
        } else {
            throw new \InvalidArgumentException('Storage path must be absolute');
        }

        $parts = [];
        foreach (explode('/', $path) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }

            if ($part === '..') {
                if ($parts === []) {
                    throw new \InvalidArgumentException('Invalid storage path');
                }
                array_pop($parts);
                continue;
            }

            $parts[] = $part;
        }

        return rtrim($prefix . implode('/', $parts), '/');
    }

    private function deleteDirectoryRecursive(string $absolutePath): bool
    {
        $this->assertPathInsideUploadRoot($absolutePath);

        $entries = scandir($absolutePath);
        if ($entries === false) {
            throw new \RuntimeException('Unable to read directory: ' . $absolutePath);
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $child = $absolutePath . DIRECTORY_SEPARATOR . $entry;
            $this->assertPathInsideUploadRoot($child);

            if (is_link($child) || is_file($child)) {
                if (!unlink($child)) {
                    throw new \RuntimeException('Unable to delete file: ' . $child);
                }
                continue;
            }

            if (is_dir($child)) {
                $this->deleteDirectoryRecursive($child);
            }
        }

        return rmdir($absolutePath);
    }

    private function samePath(string $a, string $b): bool
    {
        $a = $this->canonicalAbsolutePath($a);
        $b = $this->canonicalAbsolutePath($b);

        if (DIRECTORY_SEPARATOR === '\\') {
            return strtolower($a) === strtolower($b);
        }

        return $a === $b;
    }

    private function safeHeaderFilename(string $name): string
    {
        $name = str_replace(["\r", "\n", '"'], ['', '', "'"], basename($name));
        return $name !== '' ? $name : 'download';
    }
}
