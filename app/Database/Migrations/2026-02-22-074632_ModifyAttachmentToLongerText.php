<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyAttachmentToLongerText extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('whatsapp_logs', [
            'attachment' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'attachment_type' => [
                'type' => 'TEXT',
                'null' => true,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('whatsapp_logs', [
            'attachment' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'attachment_type' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ]
        ]);
    }
}
