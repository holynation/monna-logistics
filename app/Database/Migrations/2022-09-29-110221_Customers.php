<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Customers extends Migration
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
                'constraint' => 255,
                'null' => false,
            ],
            'lastname' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false,
            ],
            'middlename' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => true,
            ],
            'email' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false,
                'unique' => true
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
        $this->forge->addField("date_modified timestamp not null default current_timestamp on update current_timestamp");
        $this->forge->addKey('ID', true);
        $attributes = ['COLLATE' => 'utf8_general_ci'];
        $this->forge->createTable('customers', true, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('customers');
    }
}
