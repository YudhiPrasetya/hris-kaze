<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateCustomersTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'customers');
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
		$table->string('email');
		$this->stringForeign('country_id', 2, 'iso', 'master.world_countries');
		$this->unsignedBigIntegerForeign('state_id', 'id', 'master.world_states');
		$this->unsignedBigIntegerForeign('city_id', 'id', 'master.world_cities');
		$this->unsignedBigIntegerForeign('district_id', 'id', 'master.world_districts');
		$this->unsignedBigIntegerForeign('village_id', 'id', 'master.world_villages');
		$table->text('postal_code');
		$table->text('street');

		$table->timestamps();
		$table->softDeletes();
	}
}