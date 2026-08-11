<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DomesticAssignment extends ModelBase{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'assignment_type', 'overseas_to', 'assignment_no', 'letter_date',
        'customer_id', 'is_chargeable','charge_price',
        'sr_no', 'machine_id', 'assignment_date_from',
        'assignment_date_to'
    ];

    public function __construct(array $attributes = []){
        $this->setConnection(connection('master'));
        $this->setTable(table('master.domestic_assignment'));

        parent::__construct($attributes);
    }

    // public function technicians(): HasMany{
    //     return $this->hasMany(DomesticAssignmentEmployee::class, 'assignment_id', 'id');
    // }

    public function domesticAssigmentPreServices(): HasMany{
        return $this->hasMany(DomesticAssignmentPreService::class, 'assignment_id', 'id');
    }

    public function latestPreservice(){
        return $this->hasOne(DomesticAssignmentPreService::class)->latestOfMany();
    }

    public function domesticAssignmentDuringServices(): HasMany{
        return $this->hasMany(DomesticAssignmentDuringService::class, 'assignment_id', 'id');
    }

    public function arrivalFromOverseasAssignment(): HasMany{
        return $this->hasMany(ArrivalFromOverseasAssignment::class, 'assignment_id', 'id');
    }

    public function customer(): HasOne{
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function machine(): HasOne{
        return $this->hasOne(Machine::class, 'id', 'machine_id');
    }

    // public function employees(){
    //     return $this->hasManyThrough(Employee::class, DomesticAssignmentDuringService::class, 'assignment_id', 'id', 'id', 'employee_id');
    // }
}
