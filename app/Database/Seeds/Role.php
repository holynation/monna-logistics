<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Role extends Seeder
{
    public function run()
    {
        $data = [
            'role_title' => 'superadmin',
            'status'    => '1',
        ];

        $this->db->table('role')->insert($data);
    }
}
