<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateKvmTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'name' => ['type'=>'VARCHAR','constraint'=>100],
            'os_type' => ['type'=>'VARCHAR','constraint'=>50,'default'=>'linux'],
            'cpu_cores' => ['type'=>'INT','constraint'=>11,'default'=>2],
            'memory_mb' => ['type'=>'INT','constraint'=>11,'default'=>2048],
            'disk_size_gb' => ['type'=>'DECIMAL','constraint'=>'10,2','default'=>'20.00'],
            'disk_path' => ['type'=>'VARCHAR','constraint'=>255],
            'storage_path' => ['type'=>'VARCHAR','constraint'=>255],
            'server_ip' => ['type'=>'VARCHAR','constraint'=>45,'null'=>true],
            'ip_address' => ['type'=>'VARCHAR','constraint'=>45,'null'=>true],
            'state' => ['type'=>'VARCHAR','constraint'=>20,'default'=>'stopped'],
            'application_url' => ['type'=>'VARCHAR','constraint'=>255,'null'=>true],
            'created_at' => ['type'=>'DATETIME','null'=>true],
            'updated_at' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('virtual_machines', true, ['ENGINE'=>'InnoDB','CHARSET'=>'utf8mb4','COLLATE'=>'utf8mb4_unicode_ci']);

        $this->forge->addField([
            'id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'vm_id' => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'null'=>true],
            'name' => ['type'=>'VARCHAR','constraint'=>100],
            'disk_path' => ['type'=>'VARCHAR','constraint'=>255],
            'storage_path' => ['type'=>'VARCHAR','constraint'=>255],
            'format' => ['type'=>'VARCHAR','constraint'=>20,'default'=>'qcow2'],
            'allocated_gb' => ['type'=>'DECIMAL','constraint'=>'10,2','default'=>'20.00'],
            'used_gb' => ['type'=>'DECIMAL','constraint'=>'10,2','default'=>'0.00'],
            'status' => ['type'=>'VARCHAR','constraint'=>20,'default'=>'available'],
            'created_at' => ['type'=>'DATETIME','null'=>true],
            'updated_at' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('vm_id');
        $this->forge->addUniqueKey('disk_path');
        $this->forge->addForeignKey('vm_id','virtual_machines','id','SET NULL','CASCADE');
        $this->forge->createTable('virtual_disks', true, ['ENGINE'=>'InnoDB','CHARSET'=>'utf8mb4','COLLATE'=>'utf8mb4_unicode_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('virtual_disks', true);
        $this->forge->dropTable('virtual_machines', true);
    }
}
