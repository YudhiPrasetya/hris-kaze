<?php

namespace App\Models;

use App\Casts\DateTimeCasts;
use App\Casts\ImageCasts;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permit extends ModelBase{
    use SoftDeletes;
    use HasTimestamps;

    /**
     * The attibutes that are mass assignable
     *
     */
    protected $fillable = [
        'permit_date', 'id_employee',
        'permit_type',
        'id_reason_for_leave', 'start', 'end', 'note',
        'attachment_path', 'cut_att_allowance'
    ];

    /**
     * The attributes that should be cast to native types
     *
     */
    protected $casts = [
        'attachment_path' => ImageCasts::class,
        'created_at' => DateTimeCasts::class,
        'updated_at' => DateTimeCasts::class,
        'deleted_at' => DateTimeCasts::class
    ];

    /**
     * The storage format of the model's data columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $dates = [
        'permit_date', 'start', 'end'
    ];

    /**
     * @throws \App\Exceptions\SchemaNotFoundException
     */
    public function __construct(array $attributes = [])
    {
        $this->setConnection(connection('master'));
        $this->setTable(table('master.permits'));

        parent::__construct($attributes);
    }

    public function employee(): HasOne{
        return $this->hasOne(Employee::class, 'id', 'id_employee');
    }

    // public function reasonForLeave(): HasOne{
    //     return $this->hasOne(ReasonForLeave::class, 'id', 'id_reason_for_leave');
    // }
}
