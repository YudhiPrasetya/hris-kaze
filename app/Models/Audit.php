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
 * @file   Audit.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Models;

use App\Casts\JsonCasts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Audit as AuditTrait;
use OwenIt\Auditing\Contracts\Audit as AuditContract;
use ReflectionClass;


class Audit extends ModelBase implements AuditContract {
	use  AuditTrait;


	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'old_values' => JsonCasts::class,
		'new_values' => JsonCasts::class,
	];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = [
		'created_at',
		'updated_at',
		// 'deleted_at',
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id',
		'user_type',
		'user_id',
		'event',
		'auditable_type',
		'auditable_id',
		'old_values',
		'new_values',
		'url',
		'ip_address',
		'user_agent',
		'tags',
		'created_at',
		'updated_at',
	];

	/**
	 * Specify the connection, since this implements multitenant solution
	 * Called via constructor to facilitate testing
	 *
	 * @param array $attributes
	 *
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.audits', onlyName: true));

		parent::__construct($attributes);
	}

	/**
	 * @throws \ReflectionException
	 */
	public function type(): ReflectionClass {
		return (new ReflectionClass($this->auditable_type));
	}

	public function getUser(): User|Model|HasOne {
		return $this->user()->first();
	}

	public function user(): HasOne {
		return $this->hasOne(User::class, 'id', 'user_id');
	}
}
