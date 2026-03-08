<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAttachmentToWhatsappLogs extends Migration
{
    public function up()
    {
        $this->forge->addColumn('whatsapp_logs', [
            'attachment' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'message'
            ],
            'attachment_type' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'after' => 'attachment'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('whatsapp_logs', ['attachment', 'attachment_type']);
    }
}
