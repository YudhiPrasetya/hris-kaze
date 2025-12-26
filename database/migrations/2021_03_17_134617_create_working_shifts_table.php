<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateWorkingShiftsTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'working_shifts');
	}

	protected function create(Blueprint $table, Builder $schema): void {
		$table->id();
		$table->time('start');
		$table->time('end');
		$table->timestamps();
		$table->softDeletes();
	}
}
