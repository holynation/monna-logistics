<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Permission extends Seeder
{
    public function run()
    {
        $data = [
            [
                'role_id' => '1',
                'path' => 'vc/admin/dashboard',
                'permission' => 'w'
            ],
            [
                'role_id' => '1',
                'path' => 'vc/admin/profile',
                'permission' => 'w'
            ],
        ];

        $this->db->table('permission')->insertBatch($data);
    }
}
