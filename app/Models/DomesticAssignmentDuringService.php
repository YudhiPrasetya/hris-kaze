<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DomesticAssignmentDuringService extends ModelBase
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'assignment_id', 'employee_id',
        'check_out_date', 'assignment_date',
        'during_service_breakfast', 'during_service_lunch',
        'during_service_dinner', 'during_service_supper',
        'start_job', 'finish_job',
        'overtime', 'overseas_meal',
        'foreign_currency', 'overseas_meal_in_foreign_currency',
        'exchange_rate_history'
    ];

    public function __construct(array $attributes = []){
        $this->setConnection(connection('master'));
        $this->setTable(table('master.domestic_assignment_during_service', onlyName: true));

        parent::__construct($attributes);
    }

    public function domesticAssignment(): BelongsTo{
        return $this->belongsTo(DomesticAssignment::class, 'assignment_id', 'id');
    }

    public function getEmployee(): Model|HasOne|Employee{
        return $this->employee()->first();
    }

    public function employee():HasOne{
        return $this->hasOne(Employee::class, 'id', 'employee_id')->without('attendance');
    }
}
