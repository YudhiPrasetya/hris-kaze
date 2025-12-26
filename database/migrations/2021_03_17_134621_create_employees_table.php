<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateEmployeesTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'employees');
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
		$table->string('nik');
		$this->unsignedBigIntegerForeign('pin', 'pin', 'master.att_user_info');
		$table->string('name');
		$table->string('personal_email')->nullable();
		$table->text('profile_photo_path')->nullable();
		$table->date('birth_date');
		$table->date('effective_since');
		$this->unsignedBigIntegerForeign('gender_id', 'id', 'master.genders');
		$this->unsignedBigIntegerForeign('position_id', 'id', 'master.positions', true);
		$this->unsignedBigIntegerForeign('working_shift_id', 'id', 'master.working_shifts', true);
		$this->unsignedBigIntegerForeign('user_id', 'id', 'master.users', true);
		$this->stringForeign('country_id', 2, 'iso', 'master.world_countries');
		$this->unsignedBigIntegerForeign('state_id', 'id', 'master.world_states');
		$this->unsignedBigIntegerForeign('city_id', 'id', 'master.world_cities');
		$this->unsignedBigIntegerForeign('district_id', 'id', 'master.world_districts');
		$this->unsignedBigIntegerForeign('village_id', 'id', 'master.world_villages');
		$table->text('postal_code');
		$table->text('street');
		$this->stringForeign('currency_code', 6, 'code', 'master.world_currencies');
		$table->decimal('basic_salary', 11,2);
		$table->decimal('functional_allowance', 11,2);
		$table->decimal('transport_allowance', 11,2);
		$table->decimal('meal_allowances', 11,2);
		$table->boolean('marital_status')->default(false);
		$table->boolean('has_npwp')->default(true);
		$table->integer('num_of_dependents_family')->default(0);
		$table->boolean('permanent_status')->default(true);
		$table->boolean('employee_guarantee')->default(false);

		$table->timestamps();
		$table->softDeletes();
	}
}
