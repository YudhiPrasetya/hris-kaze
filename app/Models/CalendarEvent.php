<?php

namespace App\Models;

use App\Casts\DateTimeCasts;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class CalendarEvent extends ModelBase {
	use HasFactory;
	use SoftDeletes;
	use HasTimestamps;


	protected $fillable = [
		'title',
		'start_date',
		'description',
		'recurring',
	];

	protected $casts = [
		'created_at' => DateTimeCasts::class,
		'updated_at' => DateTimeCasts::class,
		'deleted_at' => DateTimeCasts::class,
	];

	/**
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.calendar_events', onlyName: true));

		parent::__construct($attributes);
	}
}
