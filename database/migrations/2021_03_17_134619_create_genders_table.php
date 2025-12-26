<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateGendersTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'genders');
	}

	protected function create(Blueprint $table, Builder $schema) {
		$table->id();
		$table->string('name');

		$table->timestamps();
		$table->softDeletes();
	}
}
