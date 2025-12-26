<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

class CreateAssignmentEmployeesTable extends MigrationBase
{
	public function __construct() {
		parent::__construct('master', 'assignment_employees');
	}

	/**
	 * @param \Illuminate\Database\Schema\Blueprint $table
	 * @param \Illuminate\Database\Schema\Builder   $schema
	 *
	 * @throws \App\Exceptions\SchemaNotFoundException
	 * @throws \App\Exceptions\TableNotFoundException
	 */
	protected function create(Blueprint $table, Builder $schema) {
		$this->unsignedBigIntegerForeign('assignment_id', 'id', 'master.assignments');
		$this->unsignedBigIntegerForeign('employee_id', 'id', 'master.employees');
		$table->time('start_job');
		$table->time('finish_job');
		$table->time('travel_time');
		$table->time('overtime')->nullable();
		$table->timestamps();
		$table->softDeletes();

		$this->createUnique('assignment_id', 'employee_id');
		$this->createIndex('assignment_id', 'employee_id');
	}
}