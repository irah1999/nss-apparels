<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        // Categories
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 100],
            'image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['active', 'inactive'], 'default' => 'active'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('categories');

        // Products
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'category_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 150],
            'description' => ['type' => 'TEXT', 'null' => true],
            'colors' => ['type' => 'TEXT', 'null' => true], // JSON array of colors
            'sizes' => ['type' => 'TEXT', 'null' => true],  // JSON array of sizes
            'main_image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'additional_images' => ['type' => 'TEXT', 'null' => true], // JSON array of secondary images
            'whatsapp_number' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['active', 'inactive'], 'default' => 'active'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('products');
    }

    public function down()
    {
        $this->forge->dropTable('products');
        $this->forge->dropTable('categories');
    }
}
