<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Laravel\Jetstream\Membership as JetstreamMembership;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;


class Membership extends JetstreamMembership implements AuditableInterface, ModelInterface {
	use Auditable;
	use Searchable;
	use HasTimestamps;


	/**
	 * Indicates if the IDs are auto-incrementing.
	 *
	 * @var bool
	 */
	public $incrementing = true;

	/**
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.membership', onlyName: true));

		parent::__construct($attributes);
	}

	public function __set($key, $value) {
		$this->setAttribute($key, $value);
		// This will make automatic update on any changes
		$this->update();
	}

	public function user() {
		return $this->hasOne(User::class, 'user_id', 'id')
		            ->getResults();
	}

	public function setUser(User $user) {
		return $user->hasOne($user, 'user_id', 'id')
		            ->save($this);
	}
}