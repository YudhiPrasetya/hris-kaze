<?php

namespace App\Models;

use App\Casts\DateTimeCasts;
use App\Models\World\Country;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


class AttendanceLog extends Model {
	use HasFactory;
	use SoftDeletes;
	use HasTimestamps;


	protected $fillable = [
		'pin',
		'time',
		'status',
		'verify',
		'workcode',
		'reserved_1',
		'reserved_2',
	];

	protected $casts = [
		'created_at' => DateTimeCasts::class,
		'updated_at' => DateTimeCasts::class,
		'deleted_at' => DateTimeCasts::class,
		'time'       => DateTimeCasts::class,
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
		'time',
	];

	/**
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.att_attendance_logs'));

		parent::__construct($attributes);
	}

	public function userInfo(): HasOne {
		return $this->hasOne(UserInfo::class, 'pin', 'pin');
	}
}
