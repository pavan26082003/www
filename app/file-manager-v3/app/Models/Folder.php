<?php
namespace App\Models;

class Folder
{
    public ?int $id = null;
    public int $user_id;
    public int $cluster_id;
    public ?int $parent_id = null;
    public string $name;
    public bool $is_starred = false;
    public ?string $last_opened = null;
    public ?string $deleted_at = null;
    public ?string $created_at = null;  // Make nullable
    public ?string $updated_at = null;  // Make nullable

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'cluster_id' => $this->cluster_id,
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'is_starred' => $this->is_starred ? 1 : 0,
            'last_opened' => $this->last_opened,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}