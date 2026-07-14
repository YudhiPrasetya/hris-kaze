<?php

namespace App\Models;

use App\Casts\DateTimeCasts;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DomesticAssignmentEmployee extends ModelBase{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'assignment_id', 'employee_id', 'check_in_date', 'check_in_at', 'pre_service_breakfast',
        'pre_service_lunch', 'pre_service_dinner', 'pre_service_supper',
        'check_out_date', 'assignment_date', 'start_assignment', 'end_assignment', 'overtime',
        'during_service_breakfast', 'during_service_lunch', 'during_service_dinner', 'during_service_supper',
    ];

    protected $casts = [
		'created_at' => DateTimeCasts::class,
		'updated_at' => DateTimeCasts::class,
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
		$this->setTable(table('master.domestic_assignment_employees', onlyName: true));

		parent::__construct($attributes);
	}
}
