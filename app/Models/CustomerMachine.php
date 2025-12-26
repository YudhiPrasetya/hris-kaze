<?php

namespace App\Models;

use App\Casts\DateTimeCasts;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


class CustomerMachine extends ModelBase {
	use HasFactory;
	use SoftDeletes;
	use HasTimestamps;


	protected $fillable = [
		'serial_number',
		'customer_id',
		'machine_id',
	];

	protected $casts = [
		'created_at' => DateTimeCasts::class,
		'updated_at' => DateTimeCasts::class,
		'deleted_at' => DateTimeCasts::class,
	];

	/**
	 * Assignment constructor.
	 *
	 * @param array $attributes
	 *
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.customer_machines', onlyName: true));

		parent::__construct($attributes);
	}

	public function getCustomer(): HasOne {
		return $this->customer()->first();
	}

	public function customer(): HasOne {
		return $this->hasOne(Customer::class, 'id', 'customer_id');
	}

	public function getMachine(): Machine|Model|HasOne {
		return $this->machine()->first();
	}

	public function machine(): HasOne {
		return $this->hasOne(Machine::class, 'id', 'machine_id');
	}
}
