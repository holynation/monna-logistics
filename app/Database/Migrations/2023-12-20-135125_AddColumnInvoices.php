<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnInvoices extends Migration
{
    public function up()
    {
        $fields = [
            'track_number' => [
                'type' => 'varchar',
                'constraint' => 150,
                'null' => false
            ],
            'bill_to_address' => [
                'type' => 'text',
                'null' => false
            ],
        ];

        $this->forge->addColumn('invoices', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('invoices', 'bill_to_address');
    }
}
