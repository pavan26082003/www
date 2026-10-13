<?php
namespace App\Services;

use App\Config\Application;

class StorageService
{
    private string $uploadPath;

    public function __construct()
    {
        $this->uploadPath = rtrim(UPLOAD_PATH, '/') . '/';
        $this->ensureUploadDirectory();
    }

    private function ensureUploadDirectory(): void
    {
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    /**
     * Create user directory
     */
    public function createUserDirectory(int $userId): string
    {
        $userDir = $this->uploadPath . 'user_' . $userId . '/';
        if (!is_dir($userDir)) {
            mkdir($userDir, 0755, true);
        }
        return $userDir;
    }

    /**
     * Create cluster directory for user
     */
    public function createClusterDirectory(int $userId, string $clusterSlug): string
    {
        $userDir = $this->createUserDirectory($userId);
        $clusterDir = $userDir . $clusterSlug . '/';
        if (!is_dir($clusterDir)) {
            mkdir($clusterDir, 0755, true);
        }
        return $clusterDir;
    }

    /**
     * Generate unique filename
     */
    public function generateUniqueFilename(string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid() . '.' . $extension;
    }

    /**
     * Get full file path from relative path
     */
    public function getFullPath(string $relativePath): string
    {
        $fullPath = $this->uploadPath . ltrim($relativePath, '/');
        return str_replace(['\\', '//'], ['/', '/'], $fullPath);
    }

    

    /**
     * Save uploaded file
     */
    public function saveUploadedFile(string $tmpPath, string $destinationPath): bool
    {
        $dir = dirname($destinationPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        if (!file_exists($tmpPath)) {
            throw new \Exception('Temporary file not found: ' . $tmpPath);
        }
        
        return move_uploaded_file($tmpPath, $destinationPath);
    }

    /**
     * Delete file from disk
     */
    public function deleteFile(string $relativePath): bool
    {
        $fullPath = $this->getFullPath($relativePath);
        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    /**
     * Delete directory and all contents
     */
    public function deleteDirectory(string $relativePath): bool
    {
        $fullPath = $this->getFullPath($relativePath);
        if (!is_dir($fullPath)) {
            return false;
        }
        
        $files = array_diff(scandir($fullPath), ['.', '..']);
        foreach ($files as $file) {
            $path = $fullPath . '/' . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        return rmdir($fullPath);
    }

    /**
     * Get file content
     */
    public function getFileContent(string $relativePath): string
    {
        $fullPath = $this->getFullPath($relativePath);
        if (!file_exists($fullPath)) {
            throw new \Exception('File not found');
        }
        return file_get_contents($fullPath);
    }

    /**
     * Get file mime type
     */
    public function getMimeType(string $relativePath): string
    {
        $fullPath = $this->getFullPath($relativePath);
        if (!file_exists($fullPath)) {
            return 'application/octet-stream';
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fullPath);
        finfo_close($finfo);
        return $mimeType;
    }

    /**
     * Get file size
     */
    public function getFileSize(string $relativePath): int
    {
        $fullPath = $this->getFullPath($relativePath);
        if (!file_exists($fullPath)) {
            return 0;
        }
        return filesize($fullPath);
    }

    /**
     * Check if file exists
     */
    public function fileExists(string $relativePath): bool
    {
        return file_exists($this->getFullPath($relativePath));
    }

    /**
     * Get all files in directory recursively
     */
    public function getFilesRecursive(string $relativePath): array
    {
        $fullPath = $this->getFullPath($relativePath);
        if (!is_dir($fullPath)) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($fullPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $relative = str_replace($this->uploadPath, '', $file->getPathname());
                $relative = ltrim(str_replace('\\', '/', $relative), '/');
                $files[] = [
                    'path' => $relative,
                    'name' => $file->getFilename(),
                    'size' => $file->getSize()
                ];
            }
        }

        return $files;
    }

    /**
     * Create zip from files
     */
    public function createZip(array $files, string $zipName): string
    {
        if (empty($files)) {
            throw new \Exception('No files to zip');
        }

        $zip = new \ZipArchive();
        $zipPath = sys_get_temp_dir() . '/' . $zipName . '.zip';
        
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \Exception('Could not create zip file');
        }

        foreach ($files as $file) {
            $fullPath = $this->getFullPath($file['relative_path']);
            if (file_exists($fullPath)) {
                $zip->addFile($fullPath, $file['name']);
            }
        }

        $zip->close();
        return $zipPath;
    }

    /**
     * Send file as download
     */
    public function downloadFile(string $relativePath, string $downloadName = null): void
    {
        $fullPath = $this->getFullPath($relativePath);
        if (!file_exists($fullPath)) {
            throw new \Exception('File not found');
        }

        $mimeType = $this->getMimeType($relativePath);
        $downloadName = $downloadName ?? basename($relativePath);

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Content-Length: ' . filesize($fullPath));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        readfile($fullPath);
        exit;
    }

    /**
     * Send file as preview (inline)
     */
    public function previewFile(string $relativePath): void
    {
        $fullPath = $this->getFullPath($relativePath);
        if (!file_exists($fullPath)) {
            throw new \Exception('File not found');
        }

        $mimeType = $this->getMimeType($relativePath);
        $name = basename($relativePath);

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . $name . '"');
        header('Cache-Control: public, max-age=3600');
        header('X-Content-Type-Options: nosniff');
        header('Access-Control-Allow-Origin: *');
        
        readfile($fullPath);
        exit;
    }
}