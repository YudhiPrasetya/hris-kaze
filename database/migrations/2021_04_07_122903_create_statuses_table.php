<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateStatusesTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'statuses');
	}

	protected function create(Blueprint $table, Builder $schema) {
		$table->id();
		$table->string('name');
		$table->string('reason')->nullable();
		$table->morphs('model');
		$table->timestamps();
	}
}
