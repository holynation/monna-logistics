<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Permission extends Migration
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
            'role_id' => [
                'type' => 'int',
                'constraint' => 11,
                'null' => false,
                'unique' => true
            ],
            'path' => [
                'type' => 'varchar',
                'constraint' => 100,
                'null' => false,
                'unique' => true,
            ],
            'permission' => [
                'type' => 'enum',
                'constraint' => ['w', 'r'],
                'null' => true,
            ],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('ID', true);
        $attributes = ['COLLATE' => 'utf8_general_ci'];
        $this->forge->createTable('permission', true, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('permission');
    }
}
