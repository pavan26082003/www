<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDeviceUiSchemas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'model_name' => [
                'type' => 'VARCHAR',
                'constraint' => 191,
            ],
            'app_id' => [
                'type' => 'VARCHAR',
                'constraint' => 191,
                'null' => true,
            ],
            'version' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'default' => '1.0.0',
            ],
            'schema_json' => [
                'type' => 'LONGTEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('model_name');
        $this->forge->addKey('app_id');
        $this->forge->createTable('device_ui_schemas', true);
    }

    public function down()
    {
        $this->forge->dropTable('device_ui_schemas', true);
    }
}
