<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomesticAssignmentDuringService extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'assignment_id', 'employee_id',
        'check_out_date', 'assignment_date',
        'during_service_breakfast', 'during_service_lunch',
        'during_service_dinner', 'during_service_supper',
        'start_job', 'finish_job',
        'overtime'
    ];

    public function __construct(array $attributes = []){
        $this->setConnection(connection('master'));
        $this->setTable(table('master.domestic_assignment_during_service', onlyName: true));

        parent::__construct($attributes);
    }
}
