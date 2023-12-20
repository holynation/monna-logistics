<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnInvoices2 extends Migration
{
    public function up()
    {
        $field = [
            'invoice_status' => [
                'type' => 'varchar',
                'constraint' => 50,
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
