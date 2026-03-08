<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateConfigurationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'config_key' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'config_value' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addUniqueKey('config_key');
        $this->forge->createTable('configurations');
    }

    public function down()
    {
        $this->forge->dropTable('configurations');
    }
}
