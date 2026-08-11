<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArrivalFromOverseasAssignment extends ModelBase{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'assignment_id', 'eta_flight_date', 'eta_flight_time',
        'employee_id', 'breakfast', 'lunch', 'dinner', 'supper'
    ];

    public function __construct(array $attributes = []){
        $this->setConnection(connection('master'));

        $this->setTable(table('master.arrival_from_overseas_assigment', onlyName: true));

        parent::__construct($attributes);
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(Employee::class, 'id', 'employee_id')->without('attendance');
    }
}
