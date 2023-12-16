<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Role extends Migration
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
            'role_title' => [
                'type' => 'varchar',
                'constraint' => 100,
                'null' => false
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
        $this->forge->createTable('role', true, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('role');
    }
}
