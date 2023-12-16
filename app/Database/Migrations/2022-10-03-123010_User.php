<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class User extends Migration
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
            'username' => [
                'type' => 'varchar',
                'constraint' => 100,
                'null' => false,
                'unique' => true
            ],
            'password' => [
                'type' => 'varchar',
                'constraint' => 150,
                'null' => false,
            ],
            'user_type' => [
                'type' => 'enum',
                'constraint' => ['admin','customers'],
                'null' => false,
                'default' => 'customers'
            ],
            'user_table_id' => [
                'type' => 'int',
                'constraint' => 11,
                'null' => false
            ],
            'has_change_password' => [
                'type' => 'tinyint',
                'constraint' => 1,
                'null' => false,
                'default' => 0,
            ],
            'status' => [
                'type' => 'tinyint',
                'constraint' => 1,
                'null' => false,
                'default' => 1,
            ],
            'date_created' => [
                'type' => 'timestamp',
                'default' => new RawSql('current_timestamp')
            ]
        ];

        $this->forge->addField($fields);
        $this->forge->addField("last_login timestamp not null default current_timestamp on update current_timestamp");
        $this->forge->addKey('ID', true);
        $attributes = ['COLLATE' => 'utf8_general_ci'];
        $this->forge->createTable('user', true, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('user');
    }
}
