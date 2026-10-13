<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAppHomePageSettings extends Migration
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
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'layout_json' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'schema_json' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'schema_version' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'default' => '1.0.0',
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
        $this->forge->addUniqueKey('user_id');
        $this->forge->createTable('app_home_page_settings', true);
    }

    public function down()
    {
        $this->forge->dropTable('app_home_page_settings', true);
    }
}
