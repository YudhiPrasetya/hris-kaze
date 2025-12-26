<?php

namespace App\Models;

use App\Casts\DateTimeCasts;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FingerPrintDeviceData extends ModelBase{
    use HasFactory;
    use SoftDeletes;
    use HasTimestamps;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'finger_print_device_id', 'nik', 'timestamps'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => DateTimeCasts::class,
        'updated_at' => DateTimeCasts::class,
        'deleted_at' => DateTimeCasts::class,
    ];

    /**
     * The storage format of the model's data columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * @throws \App\Exceptions\SchemaNotFoundexception
     */
    public function __construct(array $attributes = []){
        $this->setConnection(connection('master'));
        $this->setTable(table('master.finger_print_device_data', onlyName: true));

        parent::__construct($attributes);
    }

}
