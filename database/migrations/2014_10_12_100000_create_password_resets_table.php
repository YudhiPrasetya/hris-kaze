<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreatePasswordResetsTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'password_resets');
	}

	protected function create(Blueprint $table, Builder $schema) {
		$table->string('email');
		$table->string('token');
		$table->timestamp('created_at')->nullable();

		$this->createIndex('email');
	}
}
