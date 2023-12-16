<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CreateDefaultSeeder extends Seeder
{
    public function run()
    {
        $this->call('admin');
        $this->call('role');
        $this->call('permission');
        $this->call('user');
    }
}
