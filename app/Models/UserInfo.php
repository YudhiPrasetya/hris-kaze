<?php

namespace App\Models;

use App\Casts\DateTimeCasts;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


class UserInfo extends Model {
	use HasFactory;
	use SoftDeletes;
	use HasTimestamps;


	protected $fillable = [
		'pin',
		'name',
		'privilege',
		'password',
		'card',
		'group',
		'timezone',
		'verify',
		'vice_card',
		'start_datetime',
		'end_datetime',
	];

	protected $casts = [
		'created_at'     => DateTimeCasts::class,
		'updated_at'     => DateTimeCasts::class,
		'deleted_at'     => DateTimeCasts::class,
		'start_datetime' => DateTimeCasts::class,
		'end_datetime'   => DateTimeCasts::class,
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
		'start_datetime',
		'end_datetime',
	];

	protected $with = ['employee:id,pin,name,nik'];

	/**
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.att_user_info'));

		parent::__construct($attributes);
	}

	public function attendance(): HasMany {
		return $this->hasMany(AttendanceLog::class, 'pin', 'pin');
	}

	public function employee(): HasOne {
		return $this->hasOne(Employee::class, 'pin', 'pin');
	}
}
