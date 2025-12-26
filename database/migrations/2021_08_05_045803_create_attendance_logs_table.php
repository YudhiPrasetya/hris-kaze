<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

class CreateAttendanceLogsTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'att_attendance_logs');
	}

	protected function create(Blueprint $table, Builder $schema): void {
		$table->id();
		$this->unsignedBigIntegerForeign('pin', 'pin', 'master.att_user_info');
		$table->dateTime('time');
		$table->integer('status');
		$table->boolean('verify');
		$table->integer('workcode');
		$table->longText('reserved_1');
		$table->longText('reserved_2');
		$table->timestamps();
		$table->softDeletes();
	}
}
