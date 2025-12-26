<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateUserInfosTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'att_user_info');
	}

	protected function create(Blueprint $table, Builder $schema): void {
		$table->unsignedBigInteger('pin')->primary();
		$table->string('name');
		$table->smallInteger('privilege');
		$table->string('password')->nullable();
		$table->string('card');
		$table->smallInteger('group');
		$table->string('timezone');
		$table->boolean('verify');
		$table->string('vice_card')->nullable();
		$table->integer('start_datetime');
		$table->integer('end_datetime');
		$table->timestamps();
		$table->softDeletes();
	}
}
