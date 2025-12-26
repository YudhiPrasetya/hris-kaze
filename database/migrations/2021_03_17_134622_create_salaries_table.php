<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateSalariesTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'salaries');
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
		$this->unsignedBigIntegerForeign('employee_id', 'id', 'master.employees');
		$this->stringForeign('currency_code', 6, 'code', 'master.world_currencies');

		$table->timestamps();
		$table->softDeletes();
	}
}