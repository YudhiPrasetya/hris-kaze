<?php
/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   ModelBase.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Models;

use App\Casts\DateTimeCasts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;


class ModelBase extends Model implements AuditableInterface, ModelInterface {
	use Auditable, Searchable, HasFactory, Notifiable;


	//use SoftDeletes;

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
		'db_timestamp',
	];

	protected $casts = [
		'db_timestamp' => DateTimeCasts::class,
		'created_at'   => DateTimeCasts::class,
		'updated_at'   => DateTimeCasts::class,
		'deleted_at'   => DateTimeCasts::class,
	];

	public function __construct(array $attributes = []) {
		parent::__construct($attributes);
	}

	public function __set($key, $value) {
		$this->setAttribute($key, $value);
		// This will make automatic update on any changes
		$this->update();
	}

	/**
	 * @param Builder                               $query
	 * @param array                                 $columns
	 * @param                                       $search
	 */
	public function scopeWhereLike(Builder $query, array $columns, $search) {
		$self = $this;
		$query->where(function ($q) use ($columns, $search, $self) {
			$columns = collect($columns);

			if (($idx = $columns->search('*')) !== false) {
				$columns->offsetUnset($idx);
				$columns = $columns->merge($self->getFields());
			}

			$columns->each(function ($column) use ($search, $q) {
				$q->orWhere($column, 'LIKE', "%{$search}%");
			});
		});
	}

	public function getFields(): Collection {
		$columns = collect([]);

		foreach ($this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTableName()) as $column) {
			if ($columns->search($column) === false) {
				$columns->add($column);
			}
		}

		return $columns;
	}

	public function getTableName(): string {
		return Str::afterLast($this->getTable(), '.');
	}

	public function scopeStartWith(Builder $query, array $columns, $search): void {
		$self = $this;
		$query->where(function ($q) use ($columns, $search, $self) {
			$columns = collect($columns);

			if (($idx = $columns->search('*')) !== false) {
				$columns->offsetUnset($idx);
				$columns = $columns->merge($self->getFields());
			}

			$columns->each(function ($column) use ($search, $q) {
				$q->orWhere($column, 'LIKE', "{$search}%");
			});
		});
	}

	public function getPrimaryKey(): string {
		return $this->primaryKey;
	}

	/**
	 * Route notifications for the Slack channel.
	 *
	 * @param \Illuminate\Notifications\Notification $notification
	 *
	 * @return string
	 */
	public function routeNotificationForSlack($notification) {
		return env('LOG_SLACK_WEBHOOK_URL');
	}

	public function mailto(string $field = 'email'): ?string {
		return $this->{$field} ? '<a href="mailto:' . $this->{$field} . '">' . $this->{$field} . '</a>' : null;
	}

	protected function createLink(string $label, string $route, array $params = []): string {
		$url = route($route, count($params) ? $params : $this->{$this->primaryKey});
		$target = (parse_url($url, PHP_URL_HOST) === env('APP_DOMAIN') ? '_self' : '_blank');

		return sprintf('<a href="%s" target="%s">%s</a>', $url, $target, $label);
	}
}