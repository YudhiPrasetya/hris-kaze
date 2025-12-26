<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;


class Team extends JetstreamTeam implements AuditableInterface, ModelInterface {
	use Auditable;
	use Searchable;
	use HasTimestamps;


	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'personal_team' => 'boolean',
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'personal_team',
	];

	/**
	 * The event map for the model.
	 *
	 * @var array
	 */
	protected $dispatchesEvents = [
		'created' => TeamCreated::class,
		'updated' => TeamUpdated::class,
		'deleted' => TeamDeleted::class,
	];

	/**
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.teams', onlyName: true));

		parent::__construct($attributes);
	}

	public function __set($key, $value) {
		$this->setAttribute($key, $value);
		// This will make automatic update on any changes
		$this->update();
	}

	public function setOwner(User $user): Model|bool {
		return $user->hasOne($user, 'user_id', 'id')
		            ->save($this);
	}
}
