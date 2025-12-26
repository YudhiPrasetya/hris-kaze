<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

class CreateCustomerMachinesTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'customer_machines');
	}

	/**
	 * @param \Illuminate\Database\Schema\Blueprint $table
	 * @param \Illuminate\Database\Schema\Builder   $schema
	 *
	 * @throws \App\Exceptions\SchemaNotFoundException
	 * @throws \App\Exceptions\TableNotFoundException
	 */
	protected function create(Blueprint $table, Builder $schema) {
		$table->id();
		$this->unsignedBigIntegerForeign('customer_id', 'id', 'master.customers');
		$this->unsignedBigIntegerForeign('machine_id', 'id', 'master.machines');
		$table->string('serial_number');

		$table->timestamps();
		$table->softDeletes();
	}
}
