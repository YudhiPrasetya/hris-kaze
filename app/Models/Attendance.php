<?php

namespace App\Models;

use App\Casts\DateTimeCasts;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


class Attendance extends ModelBase {
	use HasFactory;
	use SoftDeletes;
	use HasTimestamps;


	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'employee_id',
		'attendance_reason_id',
		'annual_leave_id',
		'at',
		'end',
		'start',
		'overtime',
		'detail',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'created_at' => DateTimeCasts::class,
		'updated_at' => DateTimeCasts::class,
		'deleted_at' => DateTimeCasts::class,
		'at'         => DateTimeCasts::class,
	];

	/**
	 * The storage format of the model's date columns.
	 *
	 * @var string
	 */
	protected $dateFormat = 'Y-m-d H:i:s';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
		'at',
	];

	/**
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.attendances', onlyName: true));

		parent::__construct($attributes);
	}

	public function employee(): HasOne {
		return $this->hasOne(Employee::class, 'id', 'employee_id');
	}

	public function getEmployee(): HasOne|Employee {
		return $this->employee()->first();
	}

	public function reason(): HasOne {
		return $this->hasOne(AttendanceReason::class, 'id', 'attendance_reason_id');
	}

	public function annualLeave(): HasOne {
		return $this->hasOne(AnnualLeave::class, 'id', 'annual_leave_id');
	}

	public function workDays(): int {

	}
}
