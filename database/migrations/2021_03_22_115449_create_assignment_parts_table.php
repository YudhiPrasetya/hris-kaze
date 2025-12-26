<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateAssignmentPartsTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'assignment_parts');
	}

	/**
	 * @param \Illuminate\Database\Schema\Blueprint $table
	 * @param \Illuminate\Database\Schema\Builder   $schema
	 *
	 * @throws \App\Exceptions\SchemaNotFoundException
	 * @throws \App\Exceptions\TableNotFoundException
	 */
	protected function create(Blueprint $table, Builder $schema) {
		$this->unsignedBigIntegerForeign('assignment_id', 'id', 'master.assignments');
		$table->string('part_name');
		$table->string('part_type');
		$table->integer('qty');
		$table->string('unit');

		$this->createUnique('assignment_id', 'part_name', 'part_type');
		$table->timestamps();
		$table->softDeletes();
	}
}