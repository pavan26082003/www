<?php
namespace App\Helpers;

class FileHelper
{
    public static function formatSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public static function getFileCategory(string $mimeType): string
    {
        if (strpos($mimeType, 'image/') === 0) return 'image';
        if (strpos($mimeType, 'video/') === 0) return 'video';
        if (strpos($mimeType, 'audio/') === 0) return 'audio';
        if (strpos($mimeType, 'text/') === 0) return 'text';
        if (strpos($mimeType, 'application/pdf') === 0) return 'pdf';
        if (strpos($mimeType, 'application/zip') === 0) return 'archive';
        if (strpos($mimeType, 'application/x-rar') === 0) return 'archive';
        return 'other';
    }

    public static function getExtension(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    public static function sanitizeFilename(string $filename): string
    {
        // Remove any path information
        $filename = basename($filename);
        // Replace spaces with underscores
        $filename = str_replace(' ', '_', $filename);
        // Remove any non-alphanumeric characters except dots and underscores
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        return $filename;
    }

    public static function formatItems(array $items): array
    {
        return array_map(function($item) {
            $isFolder = isset($item['type']) && $item['type'] === 'folder';
            $isShared = isset($item['is_shared']) ? (bool)$item['is_shared'] : false;
            $isOwner = isset($item['is_owner']) ? (bool)$item['is_owner'] : true;
            $depth = isset($item['depth']) ? (int)$item['depth'] : 0;
            $permission = isset($item['permissions']) ? $item['permissions'] : 'view';
            
            // For shared items, check if download is allowed
            $canDownload = $isOwner || in_array($permission, ['download', 'edit', 'share', 'full']);
            
            return [
                'id' => (int)$item['id'],
                'name' => $item['display_name'] ?? $item['name'] ?? 'Untitled',
                'is_folder' => $isFolder,
                'created_at' => $item['created_at'] ?? null,
                'is_starred' => isset($item['is_starred']) ? (bool)$item['is_starred'] : false,
                'file_size' => $isFolder ? null : (int)($item['file_size'] ?? 0),
                'file_size_formatted' => $isFolder ? null : self::formatSize($item['file_size'] ?? 0),
                'file_type' => $isFolder ? null : ($item['file_type'] ?? null),
                'cluster_id' => (int)($item['cluster_id'] ?? 0),
                'type' => $isFolder ? 'folder' : 'file',
                'can_download' => $canDownload,
                'is_shared' => $isShared,
                'shared_by' => $item['shared_by'] ?? null,
                'is_owner' => $isOwner,
                'permission' => $permission,
                'parent_id' => $item['parent_id'] ?? null,
                'depth' => $depth,
                'folder_id' => $item['folder_id'] ?? null
            ];
        }, $items);
    }

    public static function isPreviewable(array $file): array
    {
        $extension = self::getExtension($file['original_name'] ?? $file['name'] ?? '');
        $mimeType = $file['file_type'] ?? '';
        
        // Images
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];
        if (in_array($extension, $imageExtensions) || strpos($mimeType, 'image/') === 0) {
            return [true, 'image'];
        }
        
        // Videos
        $videoExtensions = ['mp4', 'webm', 'avi', 'mkv', 'mov', 'wmv', 'flv'];
        if (in_array($extension, $videoExtensions) || strpos($mimeType, 'video/') === 0) {
            return [true, 'video'];
        }
        
        // Audio
        $audioExtensions = ['mp3', 'wav', 'flac', 'aac', 'ogg', 'wma'];
        if (in_array($extension, $audioExtensions) || strpos($mimeType, 'audio/') === 0) {
            return [true, 'audio'];
        }
        
        // PDF
        if ($extension === 'pdf' || $mimeType === 'application/pdf') {
            return [true, 'pdf'];
        }
        
        // Text/Code files
        $codeExtensions = ['txt', 'html', 'htm', 'css', 'js', 'json', 'xml', 'php', 'py', 'java', 'cpp', 'c', 'h', 'sql', 'sh', 'bash', 'rb', 'go', 'rs', 'ts', 'jsx', 'tsx', 'vue'];
        if (in_array($extension, $codeExtensions)) {
            return [true, 'text'];
        }
        
        // Office files
        $officeExtensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp'];
        if (in_array($extension, $officeExtensions)) {
            return [true, 'office'];
        }
        
        return [false, 'none'];
    }
}