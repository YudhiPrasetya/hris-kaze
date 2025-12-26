<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateAnnualLeavesTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'annual_leaves');
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
		$table->string('no');
		$table->smallInteger('year');
		$this->unsignedBigIntegerForeign('employee_id', 'id', 'master.employees');
		$table->date('used_at')->nullable();
		$table->boolean('approved')->default(false);
		$this->unsignedBigIntegerForeign('approved_by', 'id', 'master.users', true);

		$table->timestamps();
		$table->softDeletes();
	}
}
