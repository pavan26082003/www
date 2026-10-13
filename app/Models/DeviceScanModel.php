<?php

namespace App\Models;

use CodeIgniter\Model;

class DeviceScanModel extends Model
{
    protected $table = 'device_scans';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'company',
        'username',
        'scan_value',
        'created_at'
    ];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $dateFormat = 'datetime';
    protected $returnType = 'array'; // ✅ CI4.1.1 compatible
    
    public function saveScan($data)
    {
        return $this->insert($data);
    }
    
    public function getLatestScan($company, $username)
    {
        return $this->where('company', $company)
                    ->where('username', $username)
                    ->orderBy('created_at', 'DESC')
                    ->first();
    }
}
