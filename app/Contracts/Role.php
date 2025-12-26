<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;


interface Role {
	/**
	 * Find a role by its name and guard name.
	 *
	 * @param string      $name
	 * @param string|null $guardName
	 *
	 * @return \App\Contracts\Role
	 *
	 * @throws \App\Exceptions\RoleDoesNotExist
	 */
	public static function findByName(string $name, ?string $guardName): self;

	/**
	 * Find a role by its id and guard name.
	 *
	 * @param int         $id
	 * @param string|null $guardName
	 *
	 * @return \App\Contracts\Role
	 *
	 * @throws \App\Exceptions\RoleDoesNotExist
	 */
	public static function findById(int $id, ?string $guardName): self;

	/**
	 * Find or create a role by its name and guard name.
	 *
	 * @param string      $name
	 * @param string|null $guardName
	 *
	 * @return \App\Contracts\Role
	 */
	public static function findOrCreate(string $name, ?string $guardName): self;

	/**
	 * A role may be given various permissions.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function permissions(): BelongsToMany;

	/**
	 * Determine if the user may perform the given permission.
	 *
	 * @param string|\App\Contracts\Permission $permission
	 *
	 * @return bool
	 */
	public function hasPermissionTo($permission): bool;
}
