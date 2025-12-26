<?php
/**
 * This file is part of the Kaze project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   Task.php
 * @date   2021-03-24 7:16:43
 */

namespace App\Models;

use App\Casts\DateTimeCasts;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStatus\Events\StatusUpdated;
use Spatie\ModelStatus\HasStatuses;


class Task extends ModelBase {
	use HasFactory;
	use SoftDeletes;
	use HasTimestamps;
	use HasStatuses;


	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'employee_id',
		'priority_id',
		'dateline',
		'title',
		'description',
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
		'dateline'   => DateTimeCasts::class,
	];

	/**
	 * The storage format of the model's date columns.
	 *
	 * @var string
	 */
	protected $dateFormat = 'Y-m-d H:i:s';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
		'dateline',
	];

	/**
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.tasks', onlyName: true));

		parent::__construct($attributes);
	}

	public function getEmployee(): Employee|Model|HasOne {
		return $this->employee()->first();
	}

	public function employee(): HasOne {
		return $this->hasOne(Employee::class, 'id', 'employee_id');
	}

	public function getPriority(): Priority|Model|HasOne {
		return $this->priority()->first();
	}

	public function priority(): HasOne {
		return $this->hasOne(Priority::class, 'id', 'priority_id');
	}

	public function forceSetStatus(string $name, ?string $reason = null): self {
		$oldStatus = $this->latestStatus();
		$newStatus = $this->morphMany($this->getStatusModelClassName(), 'model', 'model_type', $this->getModelKeyColumnName())->updateOrCreate([
			'name'   => $name,
			'reason' => $reason,
		]);

		event(new StatusUpdated($oldStatus, $newStatus, $this));

		return $this;
	}

	public function currentStatus(): MorphOne {
		return $this->morphOne($this->getStatusModelClassName(), 'model', 'model_type', $this->getModelKeyColumnName())
		            ->latest('id');
	}
}
