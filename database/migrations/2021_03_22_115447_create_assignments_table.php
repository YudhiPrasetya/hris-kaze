<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateAssignmentsTable extends MigrationBase
{
	public function __construct() {
		parent::__construct('master', 'assignments');
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
		$table->string('service_no');
		$table->string('purchase_order_no');
		$this->unsignedBigIntegerForeign('customer_id', 'id', 'master.customers');
		$table->boolean('is_chargeable')->default(false);
		$table->string('product_code');
		$this->unsignedBigIntegerForeign('customer_machine_id', 'id', 'master.customer_machines');
		$table->text('work_detail');
		$table->text('note');
		$table->boolean('is_completed')->nullable();
		$table->date('next_service_date')->nullable();
		$table->date('service_date');
		$this->unsignedBigIntegerForeign('vehicle_id', 'id', 'master.vehicles', true);

		$table->timestamps();
		$table->softDeletes();
	}
}
