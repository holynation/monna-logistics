<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateTableInvoiceTransaction extends Migration
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
            'invoices_id' => [
                'type' => 'int',
                'constraint' => 11,
                'null' => false,
            ],
            'description' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false
            ],
            'transaction_ref' => [
                'type' => 'varchar',
                'constraint' => 100,
                'null' => false,
            ],
            'amount_paid' => [
                'type' => 'decimal',
                'constraint' => '10,2',
                'null' => false,
            ],
            'payment_status' => [
                'type' => 'varchar',
                'constraint' => 50,
                'null' => false,
                'default' => 'Not Paid'
            ],
            'payment_date' => [
                'type' => 'timestamp',
                'default' => new RawSql('current_timestamp')
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
        $this->forge->createTable('invoice_transaction', true, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('invoice_transaction');
    }
}
