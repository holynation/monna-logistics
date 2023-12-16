<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Admin extends Migration
{
    public function up()
    {
        $fields = [
            'ID' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'firstname' => [
                'type' => 'varchar',
                'constraint' => 100,
                'null' => false,
                'unique' => true
            ],
            'lastname' => [
                'type' => 'varchar',
                'constraint' => 100,
                'null' => false,
            ],
            'middlename' => [
                'type' => 'varchar',
                'constraint' => 100,
                'null' => true,
            ],
            'email' => [
                'type' => 'varchar',
                'constraint' => 100,
                'null' => false
            ],
            'phone_number' => [
                'type' => 'varchar',
                'constraint' => 30,
                'null' => true
            ],
            'role_id' => [
                'type' => 'int',
                'constraint' => 11,
                'null' => false,
            ],
            'status' => [
                'type' => 'tinyint',
                'constraint' => 1,
                'null' => false,
                'default' => 1,
            ],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('ID', true);
        $attributes = ['COLLATE' => 'utf8_general_ci'];
        $this->forge->createTable('admin', true, $attributes);
    }

    public function down()
    {
         $this->forge->dropTable('admin');
    }
}
