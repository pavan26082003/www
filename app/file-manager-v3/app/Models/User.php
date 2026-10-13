<?php
namespace App\Models;

class User
{
    public ?int $id = null;
    public string $username;
    public string $email;
    public string $password_hash;
    public int $storage_used = 0;
    public int $storage_limit = 10737418240;
    public string $created_at;
    public string $updated_at;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}