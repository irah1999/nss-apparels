<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name'       => 'Super Admin',
            'email'      => 'admin@nss.com',
            'password'   => password_hash('admin123', PASSWORD_BCRYPT),
            'role'       => 'admin',
            'status'     => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('users')->insert($data);

        // Default Configurations
        $configs = [
            ['config_key' => 'whatsapp_api_id', 'config_value' => ''],
            ['config_key' => 'whatsapp_token', 'config_value' => ''],
            ['config_key' => 'meta_app_id', 'config_value' => ''],
            ['config_key' => 'meta_app_secret', 'config_value' => ''],
        ];

        $this->db->table('configurations')->insertBatch($configs);
    }
}
