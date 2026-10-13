<?php
namespace App\Models;

class File
{
    public ?int $id = null;
    public int $user_id;
    public int $cluster_id;
    public ?int $folder_id = null;
    public string $filename;
    public string $original_name;
    public string $file_path;
    public int $file_size;
    public ?string $file_type = null;
    public ?string $file_category = null;
    public bool $is_starred = false;
    public ?string $last_opened = null;
    public ?string $deleted_at = null;
    public ?string $created_at = null;  // Make nullable
    public ?string $updated_at = null;
    public ?string $file_hash = null;  // Make nullable
    public int $is_reference = 0;


    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        if (empty($this->file_hash) && !empty($this->file_path)) {
            $fullPath = UPLOAD_PATH . '/' . ltrim($this->file_path, '/');
            if (file_exists($fullPath)) {
                $this->file_hash = hash_file('sha256', $fullPath);
            }
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'cluster_id' => $this->cluster_id,
            'folder_id' => $this->folder_id,
            'filename' => $this->filename,
            'original_name' => $this->original_name,
            'file_path' => $this->file_path,
            'file_size' => $this->file_size,
            'file_type' => $this->file_type,
            'file_category' => $this->file_category,
            'is_starred' => $this->is_starred ? 1 : 0,
            'last_opened' => $this->last_opened,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'file_hash' => $this->file_hash ?? '',
            'is_reference' => $this->is_reference ?? 0
        ];
    }
}