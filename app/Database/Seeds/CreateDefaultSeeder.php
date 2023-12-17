<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CreateDefaultSeeder extends Seeder
{
    public function run()
    {
        $this->call('Admin');
        $this->call('Role');
        $this->call('Permission');
        $this->call('User');
    }
}
