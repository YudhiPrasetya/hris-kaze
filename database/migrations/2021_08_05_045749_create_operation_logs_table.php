<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateOperationLogsTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'att_operation_logs');
	}

	protected function create(Blueprint $table, Builder $schema): void {
		$table->id();
		$table->integer('op_type');
		$table->integer('op_who');
		$table->datetime('op_time');
		$table->longText('value_1');
		$table->longText('value_2');
		$table->longText('value_3');
		$table->integer('reserved_op_type');
		$table->timestamps();
		$table->softDeletes();
	}
}
