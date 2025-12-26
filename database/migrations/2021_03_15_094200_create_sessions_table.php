<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

class CreateSessionsTable extends MigrationBase
{
	public function __construct() {
		parent::__construct('master', 'sessions');
	}

	protected function create(Blueprint $table, Builder $schema) {
		$table->string('id')->primary();
		$this->unsignedBigIntegerForeign('user_id', 'id', 'master.users', true);
		//$table->foreignId('user_id')->nullable();
		$table->string('ip_address', 45)->nullable();
		$table->text('user_agent')->nullable();
		$table->text('payload');
		$table->integer('last_activity');

		//$this->createIndex('user_id');
		$this->createIndex('last_activity');
	}
}
