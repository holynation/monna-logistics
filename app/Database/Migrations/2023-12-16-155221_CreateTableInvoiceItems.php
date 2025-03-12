<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateTableInvoiceItems extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'invoices_id' => [
                'type' => 'int',
                'constraint' => 11,
                'null' => false,
                'unsigned' => true,
            ],
            'description' => [
                'type' => 'text',
                'null' => false,
            ],
            'quantity' => [
                'type' => 'int',
                'constraint' => 11,
                'null' => false,
            ],
            'price' => [
                'type' => 'decimal',
                'constraint' => '10,2',
                'null' => false,
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
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('invoices_id', 'invoices', 'id', 'cascade', 'cascade');
        $attributes = ['COLLATE' => 'utf8_general_ci'];
        $this->forge->createTable('invoice_items', true, $attributes);

    }

    public function down()
    {
        $this->forge->dropTable('invoice_items');
    }
}
