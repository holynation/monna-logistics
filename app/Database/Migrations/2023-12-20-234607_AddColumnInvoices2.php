<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnInvoices2 extends Migration
{
    public function up()
    {
        $field = [
            'invoice_status' => [
                'type' => 'enum',
                'constraint' => ['pending','processing','in-transit','cancelled','completed'],
                'default' => 'pending',
            ]
        ];
        $this->forge->addColumn('invoices', $field);
    }

    public function down()
    {
        $this->forge->dropColumn('invoices', 'invoice_status');
    }
}
