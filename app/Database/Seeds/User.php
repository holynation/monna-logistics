<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class User extends Seeder
{
    public function run()
    {
        helper('string');
        $password = encode_password('_12345678');
        $data = [
            'username' => 'admin@gmail.com',
            'password' => $password,
            'user_type' => 'admin',
            'user_table_id' => 1,
            'status'    => '1',
        ];

        $this->db->table('user')->insert($data);
    }
}
