<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateCalendarEventsTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'calendar_events');
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
		$table->date('start_date');
		$table->date('end_date')->nullable();
		$table->boolean('recurring')->default(false);
		$table->string('title');
		$table->text('description')->nullable();

		$table->timestamps();
		$table->softDeletes();
	}
}