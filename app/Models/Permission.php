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
 * @file   Permission.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Models;

use App\Contracts\Permission as PermissionContract;
use App\Exceptions\PermissionAlreadyExists;
use App\Exceptions\PermissionDoesNotExist;
use App\Managers\Permission\Guard;
use App\Managers\Permission\PermissionRegistrar;
use App\Traits\HasRoles;
use App\Traits\RefreshesPermissionCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;


class Permission extends Model implements PermissionContract, AuditableInterface {
	use Auditable;
	use HasRoles;
	use RefreshesPermissionCache;
	use HasTimestamps;


	protected $guarded = ['id'];

	/**
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
		$this->setConnection(connection('master'));
		$this->setTable(table('master.permissions', onlyName: true));

		parent::__construct($attributes);
	}

	public static function create(array $attributes = []): Model|Builder {
		$attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);

		$permission =
			static::getPermissions(['name' => $attributes['name'], 'guard_name' => $attributes['guard_name']])->first();

		if ($permission) {
			throw PermissionAlreadyExists::create($attributes['name'], $attributes['guard_name']);
		}

		return static::query()->create($attributes);
	}

	/**
	 * Get the current cached permissions.
	 */
	protected static function getPermissions(array $params = []): Collection {
		return app(PermissionRegistrar::class)
			->setPermissionClass(static::class)
			->getPermissions($params);
	}

	/**
	 * Find a permission by its name (and optionally guardName).
	 *
	 * @param string      $name
	 * @param string|null $guardName
	 *
	 * @return \App\Contracts\Permission
	 * @throws \App\Exceptions\PermissionDoesNotExist
	 *
	 */
	public static function findByName(string $name, ?string $guardName = null): PermissionContract {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
		$permission = static::getPermissions(['name' => $name, 'guard_name' => $guardName])->first();
		if (!$permission) {
			throw PermissionDoesNotExist::create($name, $guardName);
		}

		return $permission;
	}

	/**
	 * Find a permission by its id (and optionally guardName).
	 *
	 * @param int         $id
	 * @param string|null $guardName
	 *
	 * @return \App\Contracts\Permission
	 * @throws \App\Exceptions\PermissionDoesNotExist
	 *
	 */
	public static function findById(int $id, ?string $guardName = null): PermissionContract {
		$guardName = $guardName ?? Guard::getDefaultName(static::class);
		$permission = static::getPermissions(['id' => $id, 'guard_name' => $guardName])->first();

		if (!$permission) {
			throw PermissionDoesNotExist::withId($id, $guardName);
		}

		return $permission;
	}

	/**
	 * Find or create permission by its name (and optionally guardName).
	 *
	 * @param string      $name
	 * @param string|null $guardName
	 *
	 * @return \App\Contracts\Permission
	 */
	public static function findOrCreate(string $name, ?string $guardName = null): PermissionContract {
		$guardName = $guardName ?? Guard::getDefaultName(static::class);
		$permission = static::getPermissions(['name' => $name, 'guard_name' => $guardName])->first();

		if (!$permission) {
			return static::query()->create(['name' => $name, 'guard_name' => $guardName]);
		}

		return $permission;
	}

	public function getTable() {
		return config('permission.table_names.permissions', parent::getTable());
	}

	/**
	 * A permission can be applied to roles.
	 */
	public function roles(): BelongsToMany {
		return $this->belongsToMany(
			config('permission.models.role'),
			config('permission.table_names.role_has_permissions'),
			'permission_id',
			'role_id'
		);
	}

	/**
	 * A permission belongs to some users of the model associated with its guard.
	 */
	public function users(): BelongsToMany {
		return $this->morphedByMany(
			getModelForGuard($this->attributes['guard_name']),
			'model',
			config('permission.table_names.model_has_permissions'),
			'permission_id',
			config('permission.column_names.model_morph_key')
		);
	}
}
