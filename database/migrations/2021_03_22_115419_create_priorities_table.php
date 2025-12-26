<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreatePrioritiesTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'priorities');
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
		$table->string('name');
		$table->smallInteger('level')->default(0);
		$table->text('description')->nullable();

		$table->timestamps();
		$table->softDeletes();
	}
}
