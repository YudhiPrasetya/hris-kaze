<?php

namespace App\Models;

use App\Casts\DateTimeCasts;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


class AssignmentEmployee extends Model {
	use HasFactory;
	use SoftDeletes;
	use HasTimestamps;


	protected $fillable = [
		'assignment_id',
		'employee_id',
		'start_job',
		'finish_job',
		'travel_time',
		'overtime',
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
		$this->setTable(table('master.assignment_employees', onlyName: true));

		parent::__construct($attributes);
	}

	public function assignment(): HasOne {
		return $this->hasOne(Assignment::class, 'id', 'assignment_id');
	}

	public function getEmployee(): Employee|Model|HasOne {
		return $this->employee()->first();
	}

	public function employee(): HasOne {
		return $this->hasOne(Employee::class, 'id', 'employee_id');
	}
}
