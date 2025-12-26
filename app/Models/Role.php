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
 * @file   Role.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Models;

use App\Contracts\Role as RoleContract;
use App\Exceptions\GuardDoesNotMatch;
use App\Exceptions\RoleAlreadyExists;
use App\Exceptions\RoleDoesNotExist;
use App\Managers\Permission\Guard;
use App\Traits\HasPermissions;
use App\Traits\RefreshesPermissionCache;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;


class Role extends Model implements RoleContract, AuditableInterface {
	use Auditable;
	use HasPermissions;
	use RefreshesPermissionCache;
	use HasTimestamps;


	protected $guarded = ['id'];

	/**
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
		$this->setConnection(connection('master'));
		$this->setTable(table('master.roles', onlyName: true));

		parent::__construct($attributes);
	}

	public static function create(array $attributes = []) {
		$attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);

		if (static::where('name', $attributes['name'])->where('guard_name', $attributes['guard_name'])->first()) {
			throw RoleAlreadyExists::create($attributes['name'], $attributes['guard_name']);
		}

		return static::query()->create($attributes);
	}

	/**
	 * Find a role by its name and guard name.
	 *
	 * @param string      $name
	 * @param string|null $guardName
	 *
	 * @return \App\Contracts\Role|\App\Models\Role
	 *
	 * @throws \App\Exceptions\RoleDoesNotExist
	 */
	public static function findByName(string $name, ?string $guardName = null): RoleContract {
		$guardName = $guardName ?? Guard::getDefaultName(static::class);
		$role = static::where('name', $name)->where('guard_name', $guardName)->first();

		if (!$role) {
			throw RoleDoesNotExist::named($name);
		}

		return $role;
	}

	public static function findById(int $id, ?string $guardName = null): RoleContract {
		$guardName = $guardName ?? Guard::getDefaultName(static::class);

		$role = static::where('id', $id)->where('guard_name', $guardName)->first();

		if (!$role) {
			throw RoleDoesNotExist::withId($id);
		}

		return $role;
	}

	/**
	 * Find or create role by its name (and optionally guardName).
	 *
	 * @param string      $name
	 * @param string|null $guardName
	 *
	 * @return \App\Contracts\Role
	 */
	public static function findOrCreate(string $name, ?string $guardName = null): RoleContract {
		$guardName = $guardName ?? Guard::getDefaultName(static::class);

		$role = static::where('name', $name)->where('guard_name', $guardName)->first();

		if (!$role) {
			return static::query()->create(['name' => $name, 'guard_name' => $guardName]);
		}

		return $role;
	}

	public function getTable() {
		return config('permission.table_names.roles', parent::getTable());
	}

	/**
	 * A role may be given various permissions.
	 */
	public function permissions(): BelongsToMany {
		return $this->belongsToMany(
			config('permission.models.permission'),
			config('permission.table_names.role_has_permissions'),
			'role_id',
			'permission_id'
		);
	}

	/**
	 * A role belongs to some users of the model associated with its guard.
	 */
	public function users(): BelongsToMany {
		return $this->morphedByMany(
			getModelForGuard($this->attributes['guard_name']),
			'model',
			config('permission.table_names.model_has_roles'),
			'role_id',
			config('permission.column_names.model_morph_key')
		);
	}

	/**
	 * Determine if the user may perform the given permission.
	 *
	 * @param string|Permission $permission
	 *
	 * @return bool
	 *
	 * @throws \App\Exceptions\GuardDoesNotMatch
	 */
	public function hasPermissionTo($permission): bool {
		if (config('permission.enable_wildcard_permission', false)) {
			return $this->hasWildcardPermission($permission, $this->getDefaultGuardName());
		}

		$permissionClass = $this->getPermissionClass();

		if (is_string($permission)) {
			$permission = $permissionClass->findByName($permission, $this->getDefaultGuardName());
		}

		if (is_int($permission)) {
			$permission = $permissionClass->findById($permission, $this->getDefaultGuardName());
		}

		if (!$this->getGuardNames()->contains($permission->guard_name)) {
			throw GuardDoesNotMatch::create($permission->guard_name, $this->getGuardNames());
		}

		return $this->permissions->contains('id', $permission->id);
	}
}
