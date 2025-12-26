<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateVehiclesTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'vehicles');
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
		$table->string('plat_number');
		$table->string('type');
		$table->string('imei');
		$table->string('device_id');

		$this->createUnique('device_id');

		$table->timestamps();
		$table->softDeletes();
	}
}
