<?php

namespace App\Models;

use App\Casts\DateTimeCasts;
use App\Casts\JsonCasts;
use App\Traits\HasRoles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;


class User extends Authenticatable implements AuditableInterface/*, VerifyEmail*/, ModelInterface {
	// use Queueable;
	use Notifiable;
	use SoftDeletes;
	use HasTimestamps;
	use Auditable;
	use HasRoles;
	use Searchable;


	//use MustVerifyEmail;
	use HasApiTokens;
	use HasFactory;
	use HasProfilePhoto;
	use HasTeams;
	use TwoFactorAuthenticatable;


	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'username',
		'email',
		'email_verified_at',
		'password',
		'config',
		'enabled',
		'profile_photo_path',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
		'two_factor_recovery_codes',
		'two_factor_secret',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => DateTimeCasts::class,
		'config'            => JsonCasts::class,
		'last_login'        => DateTimeCasts::class,
		'created_at'        => DateTimeCasts::class,
		'updated_at'        => DateTimeCasts::class,
		'deleted_at'        => DateTimeCasts::class,
	];

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @var array
	 */
	protected $appends = [
		'profile_photo_url',
	];

	/**
	 * The storage format of the model's date columns.
	 *
	 * @var string
	 */
	protected $dateFormat = 'Y-m-d H:i:s';

	protected $dates = [
		'last_login',
		'created_at',
		'updated_at',
		'deleted_at',
	];

	/**
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.users', onlyName: true));

		parent::__construct($attributes);
	}

	public function __set($key, $value) {
		$this->setAttribute($key, $value);
		// This will make automatic update on any changes
		$this->update();
	}

	public function link(string $target = '_self'): string {
		return sprintf('<a href="%s" target="' . $target . '">%s</a>', route("user.show", $this->{$this->primaryKey}), $this->username);
	}

	public function linkName(string $target = '_self'): string {
		return sprintf('<a href="%s" target="' . $target . '">%s</a>', route("user.show", $this->{$this->primaryKey}), $this->name);
	}

	public function emailLink(string $target = '_self'): string {
		return sprintf('<a href="mailto:%s" target="' . $target . '">%s</a>', $this->email, $this->email);
	}

	public function slackLink(): string {
		return sprintf('<%s|%s>', route("user.show", $this->{$this->primaryKey}), $this->username);
	}

	public function getPrimaryKey(): string {
		return $this->primaryKey;
	}

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

	public function getTableName() {
		return Str::afterLast($this->getTable(), '.');
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

	/**
	 * Route notifications for the mail channel.
	 *
	 * @param \Illuminate\Notifications\Notification $notification
	 *
	 * @return array|string
	 */
	public function routeNotificationForMail($notification) {
		// Return name and email address...
		return [$this->email => $this->name];
	}
}
