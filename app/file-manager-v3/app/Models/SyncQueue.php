<?php
namespace App\Models;

class SyncQueue
{
    public ?int $id = null;
    public int $user_id;
    public string $session_id;
    public string $item_type; // file, folder
    public int $item_id;
    public string $action; // create, update, delete
    public string $status = 'pending'; // pending, processing, completed, failed
    public ?string $error_message = null;
    public string $created_at;
    public ?string $processed_at = null;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}