<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomesticAssignmentPreService extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'assignment_id', 'employee_id',
        'check_in_date', 'check_in_at',
        'pre_service_breakfast', 'pre_service_lunch',
        'pre_service_dinner', 'pre_service_supper'
    ];

    public function __construct(array $attributes = []){
        $this->setConnection(connection('master'));
        $this->setTable(table('master.domestic_assignment_pre_service', onlyName: true));

        parent::__construct($attributes);
    }
    public function domesticAssignment(): BelongsTo{
        return $this->belongsTo(DomesticAssignment::class, 'assignment_id', 'id');
    }

    public function employee(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(Employee::class, 'id', 'employee_id')->without('attendance');
    }
}
