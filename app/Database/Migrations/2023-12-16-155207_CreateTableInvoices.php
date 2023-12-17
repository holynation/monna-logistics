<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateTableInvoices extends Migration
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
            'customers_id' => [
                'type' => 'int',
                'constraint' => 11,
                'null' => false,
                'unsigned' => true,
            ],
            'invoice_no' => [
                'type' => 'varchar',
                'constraint' => 100,
                'null' => false,
            ],
            'bill_from_name' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false,
            ],
            'bill_from_phone' => [
                'type' => 'varchar',
                'constraint' => 25,
                'null' => false,
            ],
            'bill_from_address' => [
                'type' => 'text',
                'null' => false,
            ],
            'bill_to_name' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false,
            ],
            'bill_to_phone' => [
                'type' => 'varchar',
                'constraint' => 25,
                'null' => false,
            ],
            'bill_from_address' => [
                'type' => 'text',
                'null' => false,
            ],
            'bill_to_email' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => true,
            ],
            'bill_to_city' => [
                'type' => 'varchar',
                'constraint' => 150,
                'null' => false,
            ],
            'bill_to_country' => [
                'type' => 'varchar',
                'constraint' => 150,
                'null' => false,
            ],
            'bill_to_postalcode' => [
                'type' => 'varchar',
                'constraint' => 25,
                'null' => true,
            ],
            'invoice_subtotal' => [
                'type' => 'decimal',
                'constraint' => '10,2',
                'null' => false,
            ],
            'invoice_tax' => [
                'type' => 'decimal',
                'constraint' => '10,2',
                'null' => false,
            ],
            'invoice_discount' => [
                'type' => 'decimal',
                'constraint' => '10,2',
                'null' => false,
            ],
            'invoice_total' => [
                'type' => 'decimal',
                'constraint' => '10,2',
                'null' => false,
            ],
            'invoice_date' => [
                'type' => 'date',
                'null' => false,
            ],
            'invoice_notes' => [
                'type' => 'text',
                'null' => true,
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
        $this->forge->addForeignKey('customers_id', 'customers', 'id', 'cascade', 'cascade');
        $attributes = ['COLLATE' => 'utf8_general_ci'];
        $this->forge->createTable('invoices', true, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('invoices');
    }
}
