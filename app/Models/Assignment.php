<?php

namespace App\Models;

use App\Casts\DateTimeCasts;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStatus\HasStatuses;


class Assignment extends ModelBase {
	use HasFactory;
	use HasTimestamps;
	use SoftDeletes;
	use HasStatuses;


	protected $fillable = [
		'customer_id',
		'customer_machine_id',
        'vehicle_id',
		'service_no',
		'purchase_order_no',
		'is_chargeable',
		'product_code',
		'machine_type',
		'work_detail',
		'note',
		'service_date',
		'next_service_date',
        'destination'
	];

	protected $casts = [
		'created_at'        => DateTimeCasts::class,
		'updated_at'        => DateTimeCasts::class,
		'deleted_at'        => DateTimeCasts::class,
		'service_date'      => DateTimeCasts::class,
		'next_service_date' => DateTimeCasts::class,
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
		$this->setTable(table('master.assignments', onlyName: true));

		parent::__construct($attributes);
	}

	public function getCustomer(): Customer|Model|HasOne {
		return $this->customer()->first();
	}

	public function customer(): HasOne {
		return $this->hasOne(Customer::class, 'id', 'customer_id');
	}

	public function getVehicle(): Vehicle|Model|HasOne {
		return $this->vehicle()->first();
	}

	public function vehicle(): HasOne {
		return $this->hasOne(Vehicle::class, 'id', 'vehicle_id');
	}

	public function technicians(): HasMany {
		return $this->hasMany(AssignmentEmployee::class, 'assignment_id', 'id');
	}

	public function parts(): HasMany {
		return $this->hasMany(AssignmentPart::class, 'assignment_id', 'id');
	}

	public function getCustomerMachine(): CustomerMachine|Model|HasOne {
		return $this->customerMachine()->first();
	}

	public function customerMachine(): HasOne {
		return $this->hasOne(CustomerMachine::class, 'id', 'customer_machine_id');
	}

	public function currentStatus(): MorphOne {
		return $this->morphOne($this->getStatusModelClassName(), 'model', 'model_type', $this->getModelKeyColumnName())
		            ->latest('id');
	}
}
