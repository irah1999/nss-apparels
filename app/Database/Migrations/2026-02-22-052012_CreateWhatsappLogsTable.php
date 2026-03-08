<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWhatsappLogsTable extends Migration
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
            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'response_log' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('whatsapp_logs');
    }

    public function down()
    {
        $this->forge->dropTable('whatsapp_logs');
    }
}
