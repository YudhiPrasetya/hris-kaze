<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateTasksTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'tasks');
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
		$this->unsignedBigIntegerForeign('employee_id', 'id', 'master.employees', true);
		$this->unsignedBigIntegerForeign('priority_id', 'id', 'master.priorities', true);
		$table->date('dateline');
		$table->string('title');
		$table->text('description');

		$table->timestamps();
		$table->softDeletes();
	}
}
