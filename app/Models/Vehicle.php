<?php

namespace App\Models;

use App\Casts\DateTimeCasts;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class Vehicle extends ModelBase {
	use HasFactory;
	use SoftDeletes;
	use HasTimestamps;


	protected $fillable = [
		'plat_number',
		'type',
		'imei',
		'device_id',
	];

	protected $casts = [
		'created_at' => DateTimeCasts::class,
		'updated_at' => DateTimeCasts::class,
		'deleted_at' => DateTimeCasts::class,
	];

	/**
	 * Vehicle constructor.
	 *
	 * @param array $attributes
	 *
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.vehicles', onlyName: true));

		parent::__construct($attributes);
	}
}
