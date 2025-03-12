<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateInvoiceItemsColumn3 extends Migration {
	public function up() {
		$fields = [
			'custom_value' => [
				'type' => 'decimal',
				'constraint' => '10,2',
				'null' => true,
			],
			'rates_id' => [
				'type' => 'int',
				'constraint' => 11,
				'null' => false,
			],

		];

		$fields2 = [
			'quantity' => [
				'name' => 'weight',
				'type' => 'int',
				'constraint' => 11,
				'null' => false,
				'comment' => 'unit in kg',
			],
		];

		$fields3 = [
			'package_fee' => [
				'type' => 'decimal',
				'constraint' => '10,2',
				'null' => true,
			],
			'certificate_fee' => [
				'type' => 'decimal',
				'constraint' => '10,2',
				'null' => true,
			],

		];
		$this->forge->addColumn('invoice_items', $fields);
		$this->forge->modifyColumn('invoice_items', $fields2);
		$this->forge->addColumn('invoices', $fields3);
	}

	public function down() {
		$fields = [
			'weight' => [
				'name' => 'quantity',
				'type' => 'int',
				'constraint' => 11,
				'null' => false,
			],
		];
		$this->forge->dropColumn('invoice_items', ['custom_value', 'rates_id']);
		$this->forge->modifyColumn('invoice_items', $fields);
		$this->forge->dropColumn('invoices', ['package_fee', 'certificate_fee']);
	}
}
