<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateAttendancesTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'attendances');
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
		$this->unsignedBigIntegerForeign('employee_id', 'id', 'master.employees', false);
		$this->unsignedBigIntegerForeign('attendance_reason_id', 'id', 'master.attendance_reasons', false);
		$this->unsignedBigIntegerForeign('annual_leave_id', 'id', 'master.annual_leaves', true);
		$table->date('at');
		$table->time('start')->nullable();
		$table->time('end')->nullable();
		$table->time('overtime')->nullable();
		$table->text('detail')->nullable();
		$table->binary('attachment')->nullable();

		$table->timestamps();
		$table->softDeletes();
	}
}
