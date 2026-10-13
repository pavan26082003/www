<?php
namespace App\Models;

class SyncSession
{
    public ?int $id = null;
    public int $user_id;
    public string $session_id;
    public string $status = 'idle'; // idle, running, completed, failed
    public ?string $started_at = null;
    public ?string $completed_at = null;
    public int $files_synced = 0;
    public int $total_files = 0;
    public ?string $last_sync_at = null;
    public ?string $error_message = null;

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
            'session_id' => $this->session_id,
            'status' => $this->status,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'files_synced' => $this->files_synced,
            'total_files' => $this->total_files,
            'last_sync_at' => $this->last_sync_at,
            'error_message' => $this->error_message
        ];
    }
}