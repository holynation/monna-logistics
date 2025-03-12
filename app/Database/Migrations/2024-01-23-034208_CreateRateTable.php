<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateRateTable extends Migration {
	public function up() {
		$fields = [
			'id' => [
				'type' => 'int',
				'constraint' => 11,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'name' => [
				'type' => 'varchar',
				'constraint' => 100,
				'null' => false,
			],
			'amount' => [
				'type' => 'decimal',
				'constraint' => '10,2',
				'null' => false,
			],
			'date_created' => [
				'type' => 'timestamp',
				'default' => new RawSql('current_timestamp'),
			],
		];

		$this->forge->addField($fields);
		$this->forge->addField("date_modified timestamp not null default current_timestamp on update current_timestamp");
		$this->forge->addKey('id', true);
		$attributes = ['COLLATE' => 'utf8_general_ci'];
		$this->forge->createTable('rates', true, $attributes);
	}

	public function down() {
		$this->forge->dropTable('rates');
	}
}
