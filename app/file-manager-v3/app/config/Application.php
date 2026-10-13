<?php
namespace App\Config;

class Application
{
    public static function get(string $key, $default = null)
    {
        $config = [
            'app_name' => 'EFACTOR File Manager',
            'app_version' => '2.0',
            'timezone' => 'Asia/Kolkata',
            
            'allowed_extensions' => [
                'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico',
                'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
                'txt', 'rtf', 'csv', 'odt', 'ods', 'odp',
                'zip', 'rar', '7z', 'tar', 'gz', 'bz2',
                'mp4', 'webm', 'avi', 'mkv', 'mov', 'wmv', 'flv',
                'mp3', 'wav', 'flac', 'aac', 'ogg', 'wma',
                'js', 'css', 'html', 'htm', 'json', 'xml', 'php', 'sql',
                'py', 'java', 'cpp', 'c', 'h', 'sh', 'bash'
            ],
            
            'max_file_size' => 104857600, // 100MB
            
            'storage_limit_default' => 10737418240, // 10GB
            
            'clusters' => [
                'my-drive' => ['name' => 'My Drive', 'default' => true],
                'shared' => ['name' => 'Shared with Me', 'default' => false],
                'computers' => ['name' => 'Computers', 'default' => false],
                'projects' => ['name' => 'Projects', 'default' => false],
                'images' => ['name' => 'Images', 'default' => false],
                'videos' => ['name' => 'Videos', 'default' => false],
                'documents' => ['name' => 'Documents', 'default' => false],
                'archives' => ['name' => 'Archives', 'default' => false],
                'starred' => ['name' => 'Starred', 'default' => false],
                'trash' => ['name' => 'Trash', 'default' => false]
            ]
        ];
        
        return $config[$key] ?? $default;
    }
}