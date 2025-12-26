<?php

namespace App\Models;

use App\Casts\DateTimeCasts;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ReasonForLeave
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $number_of_days
 * @property string|null $attachment_requirement
 * @property mixed|string|null $created_at
 * @property mixed|string|null $updated_at
 * @property mixed|string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave newQuery()
 * @method static \Illuminate\Database\Query\Builder|ReasonForLeave onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave query()
 * @method static Builder|ModelBase startWith(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave whereAttachmentRequirement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave whereId($value)
 * @method static Builder|ModelBase whereLike(array $columns, $search)
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave whereNumberOfDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReasonForLeave whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ReasonForLeave withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ReasonForLeave withoutTrashed()
 * @mixin \Eloquent
 */
class ReasonForLeave extends ModelBase{
    use SoftDeletes;
    use HasTimestamps;

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'name', 'number_of_days', 'attachment_requirement'
    ];

    /**
     * The attributes that should be cast to native types
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

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * @throws \App\Exceptions\SchemaNotFoundException
     */
    public function __construct(array $attributes = [])
    {
        $this->setConnection(connection('master'));
        $this->setTable(table('master.reasons_for_leave'));

        parent::__construct($attributes);
    }
}
