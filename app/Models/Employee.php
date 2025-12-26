<?php

namespace App\Models;

use App\Casts\DateTimeCasts;
use App\Casts\ImageCasts;
use App\Models\JobTitle;
use App\Models\World\City;
use App\Models\World\Country;
use App\Models\World\Currency;
use App\Models\World\District;
use App\Models\World\State;
use App\Models\World\Village;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Jetstream\HasProfilePhoto;


class Employee extends ModelBase {
	use HasProfilePhoto;
	use SoftDeletes;
	use HasTimestamps;


	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'nik',
		'pin',
		'user_id',
		'att_id',
		'name',
		'profile_photo_path',
		'street',
		'birth_date',
		'effective_since',
        'leave_allowance',
		'basic_salary',
		'functional_allowance',
		'transport_allowance',
		'meal_allowances',
		'other_allowance',
		'attendance_premium',
		'overtime',
		'currency_code',
		'country_id',
		'state_id',
		'city_id',
		'district_id',
		'village_id',
		'position_id',
        'job_title_id',
		'postal_code',
		'gender_id',
		'marital_status',
		'has_npwp',
		'num_of_dependents_family',
		'permanent_status',
		'employee_guarantee',
	];

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @var array
	 */
	protected $appends = [
		// 'profile_photo_url',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'profile_photo_path' => ImageCasts::class,
		'effective_since'    => DateTimeCasts::class,
		'created_at'         => DateTimeCasts::class,
		'updated_at'         => DateTimeCasts::class,
		'deleted_at'         => DateTimeCasts::class,
	];

	protected $with = ['attendance:employee_id'];

	/**
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.employees', onlyName: true));

		parent::__construct($attributes);
	}

	public function link(string $target = '_self'): string {
		return $this->createLink($this->name, 'employee.show');
	}

	public function getPosition(): Model|HasOne|Position {
		return $this->position()->first();
	}

	public function position(): HasOne {
		return $this->hasOne(Position::class, 'id', 'position_id');
	}

	public function user(): User|Model|HasOne|null {
		return $this->hasOne(User::class, 'id', 'user_id')->first();
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

	public function tasks(): HasMany {
		return $this->hasMany(Task::class, 'employee_id', 'id');
	}

	public function assignments(): HasMany {
		return $this->hasMany(AssignmentEmployee::class, 'employee_id', 'id');
	}

	public function customerMachine(): HasOne {
		return $this->hasOne(CustomerMachine::class, 'id', 'employee_id');
	}

	public function annualLeaves(): HasMany {
		return $this->hasMany(AnnualLeave::class, 'employee_id', 'id');
	}

	public function attendance(): HasMany {
		return $this->hasMany(Attendance::class, 'employee_id', 'id');
	}

	public function userInfo(): HasOne {
		return $this->hasOne(UserInfo::class, 'pin', 'pin');
	}

	public function currencyCode(): string {
		$currency = $this->currency()->first();

		return sprintf("(%s) %s", $currency->code, $currency->symbol);
	}

	public function currency(): HasOne {
		return $this->hasOne(Currency::class, 'code', 'currency_code');
	}

	public function currencySymbol(): string {
		$currency = $this->currency()->first();

		return $currency->symbol;
	}

	public function getWorkingShift(): WorkingShift|HasOne {
		return $this->workingShift()->first();
	}

	public function workingShift(): HasOne {
		return $this->hasOne(WorkingShift::class, 'id', 'working_shift_id');
	}

    public function jobTitle(): HasOne{
        return $this->hasOne(JobTitle::class, 'id', 'job_title_id');
    }
}
