<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveForeignCascadeUpdateTransaction extends Migration
{
    public function up()
    {
        $this->forge->dropForeignKey('invoice_transaction', 'invoice_transaction_customers_id_foreign');

        // $this->forge->addForeignKey('customers_id', 'customers', 'id', '','cascade', 'fk_invoices_trans_invoices_id');
        // $this->forge->processIndexes('invoice_transaction');
    }

    public function down()
    {
        // $this->forge->dropForeignKey('invoice_transaction', 'fk_invoices_trans_invoices_id');

        $this->forge->addForeignKey('customers_id', 'customers', 'id', 'cascade', 'cascade');
        $this->forge->processIndexes('invoice_transaction');
    }
}
