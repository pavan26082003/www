<?php

namespace App\Models;

use CodeIgniter\Model;

class VirtualDiskModel extends Model
{
    protected $table = 'virtual_disks';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'vm_id',
        'name',
        'disk_path',
        'storage_path',
        'format',
        'allocated_gb',
        'used_gb',
        'status',
    ];

    public function withVm(): array
    {
        return $this->select('virtual_disks.*, virtual_machines.name AS vm_name')
            ->join('virtual_machines', 'virtual_machines.id = virtual_disks.vm_id', 'left')
            ->orderBy('virtual_disks.id', 'DESC')
            ->get()
            ->getResultArray();
    }
}
