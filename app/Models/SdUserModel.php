<?php

namespace App\Models;

use CodeIgniter\Model;

class SdUserModel extends Model
{
    protected $table = 'sd_users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['email', 'phone', 'password', 'status'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'email' => 'required|max_length[191]|valid_email|is_unique[sd_users.email,id,{id}]',
        'phone' => 'required|exact_length[10]|numeric|is_unique[sd_users.phone,id,{id}]',
        'password' => 'required|min_length[8]|max_length[255]',
        'status' => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'email' => [
            'required' => 'Email address is required.',
            'valid_email' => 'Enter a valid email address.',
            'is_unique' => 'This email address is already registered.',
        ],
        'phone' => [
            'required' => 'Mobile number is required.',
            'exact_length' => 'Mobile number must contain exactly 10 digits.',
            'numeric' => 'Mobile number must contain digits only.',
            'is_unique' => 'This mobile number is already registered.',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}
