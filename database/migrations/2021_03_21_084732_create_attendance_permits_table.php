<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateAttendancePermitsTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'attendance_permits');
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
		$this->unsignedBigIntegerForeign('attendance_id', 'id', 'master.attendances', true)
		     ->cascadeOnDelete();
		$table->time('start');
		$table->time('end')->nullable();
		$table->text('reason');

		$table->timestamps();
		$table->softDeletes();
	}
}
