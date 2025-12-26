<?php

namespace App\Models;

use App\Casts\DateTimeCasts;

use App\Models\Employee;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Overtime extends ModelBase{
    use SoftDeletes;
    use HasTimestamps;

    /**
     * The attributes that are mass assignable
     *
     */
    protected $fillable = [
        'overtime_date', 'id_employee',
        'start', 'end',
        'necessity', 'status'
    ];

    /**
     * The attributes that should be cast to native types
     *
     */
    protected $casts = [
        // 'overtime_date' => DateTimeCasts::class,
        'created_at' => DateTimeCasts::class,
        'updated_at' => DateTimeCasts::class,
        'deleted_at' => DateTimeCasts::class
    ];

    /**
     * The storage format of the model's data column
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $dates = ['overtime_date'];

    protected $time = ['start', 'end'];

    /**
     * @throws \App\Exeptions\SchemaNotFoundException
     *
     */
    public function __construct(array $attributes = [])
    {
        $this->setConnection(connection('master'));
        $this->setTable(table('master.overtimes'));

        return parent::__construct($attributes);
    }

    public function employee(): HasOne{
        return $this->hasOne(Employee::class, 'id', 'id_employee');
    }
}
