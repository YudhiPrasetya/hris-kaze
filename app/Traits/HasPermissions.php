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
 * @file   HasPermissions.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Traits;

use App\Contracts\Permission;
use App\Exceptions\GuardDoesNotMatch;
use App\Exceptions\PermissionDoesNotExist;
use App\Exceptions\WildcardPermissionInvalidArgument;
use App\Managers\Permission\Guard;
use App\Managers\Permission\PermissionRegistrar;
use App\Managers\Permission\WildcardPermission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;


trait HasPermissions {
	private $permissionClass;

	public static function bootHasPermissions() {
		static::deleting(
			function ($model) {
				if (method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
					return;
				}

				$model->permissions()->detach();
			}
		);
	}

	/**
	 * Scope the model query to certain permissions only.
	 *
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 * @param string|array|\Spatie\Permission\Contracts\Permission|\Illuminate\Support\Collection $permissions
	 *
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopePermission(Builder $query, $permissions): Builder {
		$permissions = $this->convertToPermissionModels($permissions);

		$rolesWithPermissions = array_unique(
			array_reduce(
				$permissions,
				function ($result, $permission) {
					return array_merge($result, $permission->roles->all());
				},
				[]
			)
		);

		return $query->where(
			function (Builder $query) use ($permissions, $rolesWithPermissions) {
				$query->whereHas(
					'permissions',
					function (Builder $subQuery) use ($permissions) {
						$subQuery->whereIn(
							config('permission.table_names.permissions') . '.id',
							\array_column($permissions, 'id')
						);
					}
				);
				if (count($rolesWithPermissions) > 0) {
					$query->orWhereHas(
						'roles',
						function (Builder $subQuery) use ($rolesWithPermissions) {
							$subQuery->whereIn(
								config('permission.table_names.roles') . '.id',
								\array_column($rolesWithPermissions, 'id')
							);
						}
					);
				}
			}
		);
	}

	/**
	 * @param string|array|\App\Contracts\Permission|\Illuminate\Support\Collection $permissions
	 *
	 * @return array
	 */
	protected function convertToPermissionModels($permissions): array {
		if ($permissions instanceof Collection) {
			$permissions = $permissions->all();
		}

		$permissions = is_array($permissions) ? $permissions : [$permissions];

		return array_map(
			function ($permission) {
				if ($permission instanceof Permission) {
					return $permission;
				}

				return $this->getPermissionClass()->findByName($permission, $this->getDefaultGuardName());
			},
			$permissions
		);
	}

	public function getPermissionClass() {
		if (!isset($this->permissionClass)) {
			$this->permissionClass = app(PermissionRegistrar::class)->getPermissionClass();
		}

		return $this->permissionClass;
	}

	protected function getDefaultGuardName(): string {
		return Guard::getDefaultName($this);
	}

	/**
	 * @deprecated since 2.35.0
	 * @alias      of hasPermissionTo()
	 */
	public function hasUncachedPermissionTo($permission, $guardName = null): bool {
		return $this->hasPermissionTo($permission, $guardName);
	}

	/**
	 * Determine if the model may perform the given permission.
	 *
	 * @param string|int|\App\Contracts\Permission $permission
	 * @param string|null                                        $guardName
	 *
	 * @return bool
	 * @throws PermissionDoesNotExist
	 */
	public function hasPermissionTo($permission, $guardName = null): bool {
		if (config('permission.enable_wildcard_permission', false)) {
			return $this->hasWildcardPermission($permission, $guardName);
		}

		$permissionClass = $this->getPermissionClass();

		if (is_string($permission)) {
			$permission = $permissionClass->findByName(
				$permission,
				$guardName ?? $this->getDefaultGuardName()
			);
		}

		if (is_int($permission)) {
			$permission = $permissionClass->findById(
				$permission,
				$guardName ?? $this->getDefaultGuardName()
			);
		}

		if (!$permission instanceof Permission) {
			throw new PermissionDoesNotExist;
		}

		return $this->hasDirectPermission($permission) || $this->hasPermissionViaRole($permission);
	}

	/**
	 * Validates a wildcard permission against all permissions of a user.
	 *
	 * @param string|int|\App\Contracts\Permission $permission
	 * @param string|null                                        $guardName
	 *
	 * @return bool
	 */
	protected function hasWildcardPermission($permission, $guardName = null): bool {
		$guardName = $guardName ?? $this->getDefaultGuardName();

		if (is_int($permission)) {
			$permission = $this->getPermissionClass()->findById($permission, $guardName);
		}

		if ($permission instanceof Permission) {
			$permission = $permission->name;
		}

		if (!is_string($permission)) {
			throw WildcardPermissionInvalidArgument::create();
		}

		foreach ($this->getAllPermissions() as $userPermission) {
			if ($guardName !== $userPermission->guard_name) {
				continue;
			}

			$userPermission = new WildcardPermission($userPermission->name);

			if ($userPermission->implies($permission)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Return all the permissions the model has, both directly and via roles.
	 */
	public function getAllPermissions(): Collection {
		/** @var Collection $permissions */
		$permissions = $this->permissions;

		if ($this->roles) {
			$permissions = $permissions->merge($this->getPermissionsViaRoles());
		}

		return $permissions->sort()->values();
	}

	/**
	 * Return all the permissions the model has via roles.
	 */
	public function getPermissionsViaRoles(): Collection {
		return $this->loadMissing('roles', 'roles.permissions')
			->roles->flatMap(
				function ($role) {
					return $role->permissions;
				}
			)->sort()->values();
	}

	/**
	 * Determine if the model has the given permission.
	 *
	 * @param string|int|\App\Contracts\Permission $permission
	 *
	 * @return bool
	 * @throws PermissionDoesNotExist
	 */
	public function hasDirectPermission($permission): bool {
		$permissionClass = $this->getPermissionClass();

		if (is_string($permission)) {
			$permission = $permissionClass->findByName($permission, $this->getDefaultGuardName());
		}

		if (is_int($permission)) {
			$permission = $permissionClass->findById($permission, $this->getDefaultGuardName());
		}

		if (!$permission instanceof Permission) {
			throw new PermissionDoesNotExist;
		}

		return $this->permissions->contains('id', $permission->id);
	}

	/**
	 * Determine if the model has, via roles, the given permission.
	 *
	 * @param \App\Contracts\Permission $permission
	 *
	 * @return bool
	 */
	protected function hasPermissionViaRole(Permission $permission): bool {
		return $this->hasRole($permission->roles);
	}

	/**
	 * Determine if the model has any of the given permissions.
	 *
	 * @param array ...$permissions
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function hasAnyPermission(...$permissions): bool {
		$permissions = collect($permissions)->flatten();

		foreach ($permissions as $permission) {
			if ($this->checkPermissionTo($permission)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * An alias to hasPermissionTo(), but avoids throwing an exception.
	 *
	 * @param string|int|\App\Contracts\Permission $permission
	 * @param string|null                                        $guardName
	 *
	 * @return bool
	 */
	public function checkPermissionTo($permission, $guardName = null): bool {
		try {
			return $this->hasPermissionTo($permission, $guardName);
		}
		catch (PermissionDoesNotExist $e) {
			return false;
		}
	}

	/**
	 * Determine if the model has all of the given permissions.
	 *
	 * @param array ...$permissions
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function hasAllPermissions(...$permissions): bool {
		$permissions = collect($permissions)->flatten();

		foreach ($permissions as $permission) {
			if (!$this->hasPermissionTo($permission)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Remove all current permissions and set the given ones.
	 *
	 * @param string|array|\App\Contracts\Permission|\Illuminate\Support\Collection $permissions
	 *
	 * @return $this
	 */
	public function syncPermissions(...$permissions) {
		$this->permissions()->detach();

		return $this->givePermissionTo($permissions);
	}

	/**
	 * A model may have multiple direct permissions.
	 */
	public function permissions(): BelongsToMany {
		return $this->morphToMany(
			config('permission.models.permission'),
			'model',
			config('permission.table_names.model_has_permissions'),
			config('permission.column_names.model_morph_key'),
			'permission_id'
		);
	}

	/**
	 * Grant the given permission(s) to a role.
	 *
	 * @param string|array|\App\Contracts\Permission|\Illuminate\Support\Collection $permissions
	 *
	 * @return $this
	 */
	public function givePermissionTo(...$permissions) {
		$permissions = collect($permissions)
			->flatten()
			->map(
				function ($permission) {
					if (empty($permission)) {
						return false;
					}

					return $this->getStoredPermission($permission);
				}
			)
			->filter(
				function ($permission) {
					return $permission instanceof Permission;
				}
			)
			->each(
				function ($permission) {
					$this->ensureModelSharesGuard($permission);
				}
			)
			->map->id
			->all();

		$model = $this->getModel();

		if ($model->exists) {
			$this->permissions()->sync($permissions, false);
			$model->load('permissions');
		}
		else {
			$class = \get_class($model);

			$class::saved(
				function ($object) use ($permissions, $model) {
					static $modelLastFiredOn;
					if ($modelLastFiredOn !== null && $modelLastFiredOn === $model) {
						return;
					}
					$object->permissions()->sync($permissions, false);
					$object->load('permissions');
					$modelLastFiredOn = $object;
				}
			);
		}

		$this->forgetCachedPermissions();

		return $this;
	}

	/**
	 * @param string|array|\App\Contracts\Permission|\Illuminate\Support\Collection $permissions
	 *
	 * @return \App\Contracts\Permission|\App\Contracts\Permission[]|\Illuminate\Support\Collection
	 */
	protected function getStoredPermission($permissions) {
		$permissionClass = $this->getPermissionClass();

		if (is_numeric($permissions)) {
			return $permissionClass->findById($permissions, $this->getDefaultGuardName());
		}

		if (is_string($permissions)) {
			return $permissionClass->findByName($permissions, $this->getDefaultGuardName());
		}

		if (is_array($permissions)) {
			return $permissionClass
				->whereIn('name', $permissions)
				->whereIn('guard_name', $this->getGuardNames())
				->get();
		}

		return $permissions;
	}

	protected function getGuardNames(): Collection {
		return Guard::getNames($this);
	}

	/**
	 * @param \App\Contracts\Permission|\Spatie\Permission\Contracts\Role $roleOrPermission
	 *
	 * @throws \App\Exceptions\GuardDoesNotMatch
	 */
	protected function ensureModelSharesGuard($roleOrPermission) {
		if (!$this->getGuardNames()->contains($roleOrPermission->guard_name)) {
			throw GuardDoesNotMatch::create($roleOrPermission->guard_name, $this->getGuardNames());
		}
	}

	/**
	 * Forget the cached permissions.
	 */
	public function forgetCachedPermissions() {
		app(PermissionRegistrar::class)->forgetCachedPermissions();
	}

	/**
	 * Revoke the given permission.
	 *
	 * @param \App\Contracts\Permission|\App\Contracts\Permission[]|string|string[] $permission
	 *
	 * @return $this
	 */
	public function revokePermissionTo($permission) {
		$this->permissions()->detach($this->getStoredPermission($permission));
		$this->forgetCachedPermissions();
		$this->load('permissions');

		return $this;
	}

	public function getPermissionNames(): Collection {
		return $this->permissions->pluck('name');
	}

	/**
	 * Check if the model has All of the requested Direct permissions.
	 *
	 * @param array ...$permissions
	 *
	 * @return bool
	 */
	public function hasAllDirectPermissions(...$permissions): bool {
		$permissions = collect($permissions)->flatten();

		foreach ($permissions as $permission) {
			if (!$this->hasDirectPermission($permission)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check if the model has Any of the requested Direct permissions.
	 *
	 * @param array ...$permissions
	 *
	 * @return bool
	 */
	public function hasAnyDirectPermission(...$permissions): bool {
		$permissions = collect($permissions)->flatten();

		foreach ($permissions as $permission) {
			if ($this->hasDirectPermission($permission)) {
				return true;
			}
		}

		return false;
	}
}
