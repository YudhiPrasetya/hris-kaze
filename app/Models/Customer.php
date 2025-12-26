<?php

namespace App\Models;

use App\Casts\DateTimeCasts;
use App\Models\World\City;
use App\Models\World\Country;
use App\Models\World\District;
use App\Models\World\State;
use App\Models\World\Village;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


class Customer extends ModelBase {
	use HasFactory;
	use SoftDeletes;
	use HasTimestamps;


	protected $fillable = [
		'name',
		'email',
		'country_id',
		'state_id',
		'city_id',
		'district_id',
		'village_id',
		'postal_code',
		'street',
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
		$this->setTable(table('master.customers', onlyName: true));

		parent::__construct($attributes);
	}

	public function address(): string {
		return collect([
			$this->street,
			$this->village()->first()?->name,
			$this->district()->first()?->name,
			$this->city()->first()?->name . ' ' . $this->postal_code,
			$this->state()->first()?->name,
			$this->country()->first()?->name,
		])->implode(', ');
	}

	public function village(): HasOne {
		return $this->hasOne(Village::class, 'id', 'village_id');
	}

	public function district(): HasOne {
		return $this->hasOne(District::class, 'id', 'district_id');
	}

	public function city(): HasOne {
		return $this->hasOne(City::class, 'id', 'city_id');
	}

	public function state(): HasOne {
		return $this->hasOne(State::class, 'id', 'state_id');
	}

	public function country(): HasOne {
		return $this->hasOne(Country::class, 'iso', 'country_id');
	}

	public function serviceReports(): HasMany {
		return $this->hasMany(Assignment::class, 'customer_id', 'id');
	}

	public function machines(): HasMany {
		return $this->hasMany(CustomerMachine::class, 'customer_id', 'id');
	}
}