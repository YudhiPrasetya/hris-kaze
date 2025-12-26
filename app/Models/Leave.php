<?php
namespace App\Models;

use App\Casts\DateTimeCasts;
use App\Casts\ImageCasts;

use App\Models\Employee;
use App\Models\ReasonForLeave;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Leave extends ModelBase{
    use SoftDeletes;
    use HasTimestamps;

    protected $fillable = [
        'leave_date',
        'id_employee',
        'id_reason_for_leave',
        'start', 'end', 'note',
        'attachment_path', 'cut_att_allowance'
    ];

    protected $casts = [
        'attachment_path' => ImageCasts::class,
        'created_at' => DateTimeCasts::class,
        'updated_at' => DateTimeCasts::class,
        'deleted_at' => DateTimeCasts::class
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $dates = [
        'leave_date',
        'start',
        'end'
    ];

    /**
     * @throws \App\Exceptions\SchemaNotFoundException
     *
     */
    public function __construct(array $attributes = [])
    {
        $this->setConnection(connection('master'));
        $this->setTable(table('master.leaves'));

        return parent::__construct($attributes);
    }

    public function employee(): HasOne{
        return $this->hasOne(Employee::class, 'id', 'id_employee');
    }

    public function reasonForLeave(): HasOne{
        return $this->hasOne(ReasonForLeave::class, 'id', 'id_reason_for_leave');
    }
}
