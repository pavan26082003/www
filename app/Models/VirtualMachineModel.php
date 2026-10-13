<?php

namespace App\Models;

use CodeIgniter\Model;

class VirtualMachineModel extends Model
{
    protected $table = 'virtual_machines';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'name',
        'os_type',
        'cpu_cores',
        'memory_mb',
        'disk_size_gb',
        'disk_path',
        'storage_path',
        'server_ip',
        'ip_address',
        'state',
        'application_url',
    ];
}
