<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveForeignCascadeUpdate extends Migration
{
    public function up()
    {
        $this->forge->dropForeignKey('invoice_items', 'invoice_items_invoices_id_foreign');

        $this->forge->addForeignKey('invoices_id', 'invoices', 'id', '','cascade', 'fk_invoices_items_invoices_id');
        $this->forge->processIndexes('invoice_items');
    }

    public function down()
    {
        $this->forge->dropForeignKey('invoice_items', 'fk_invoices_items_invoices_id');

        $this->forge->addForeignKey('invoices_id', 'invoices', 'id', 'cascade', 'cascade');
        $this->forge->processIndexes('invoice_items');
    }
}
