<?php

namespace App\Models;

use App\Casts\DateTimeCasts;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DomesticAssignmentMeal extends ModelBase{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'position_id', 'year_employee',
        'lunch_weekday', 'dinner_weekday', 'night_weekday',
        'lunch_weekend', 'dinner_weekend', 'night_weekend',
    ];

    protected $casts = [
        'created_at' => DateTimeCasts::class,
        'updated_at' => DateTimeCasts::class,
    ];

    /**
     *
     * Domestic Assignment Meal constructor
     *
     * @param array $attributes
     *
     * @throws \App\Exceptions\SchemaNotFoundException
     */
    public function __construct(array $attributes = []){
        $this->setConnection(connection('master'));
        $this->setTable(table('master.domestic_assignment_meal', onlyName: true));

        parent::__construct($attributes);

    }
}
