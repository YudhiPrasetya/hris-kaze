<?php

namespace App\Console\Commands;

use App\Contracts\Permission as PermissionContract;
use App\Contracts\Role as RoleContract;
use App\Managers\Permission\PermissionRegistrar;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;


class AppPermissionCreateRole extends Command {
	protected $signature = 'app:permission:role
        {name : The name of the role}
        {--c|create= : Create permission/role if not exists}
        {--r|revoke= : Revoke permissions from role}
        {--g|guard= : The name of the guard}
        {permissions? : A list of permissions to assign to the role, separated by | }';

	protected $description = 'Create a role';

	public function handle() {
		$roleClass = app(RoleContract::class);

		if ($this->option('create') && !$this->option('revoke')) {
			/**
			 * @var $role \App\Models\Role
			 */
			$role = $roleClass::findOrCreate($this->argument('name'), $this->argument('guard'));
			$permissions = $this->makePermissions($this->argument('permissions'));
		}
		else {
			/**
			 * @var $role \App\Models\Role
			 */
			$role = $roleClass::findByName($this->argument('name'), $this->argument('guard'));
			$permissions = $this->findPermissions($this->argument('permissions'));
		}

		if ($this->option('revoke')) {
			$permissions?->each(function(Permission $item) use($role) {
				$item->removeRole($role);
			});
		}
		else {
			$role->givePermissionTo($permissions);
		}

		Role::bootRefreshesPermissionCache();
		$this->info("Role `{$role->name}` created");
		$this->call('app:clear', ['-y' => true]);
	}

	/**
	 * @param array|null|string $string
	 */
	protected function makePermissions(string $string = null) {
		if (empty($string)) {
			return;
		}

		$permissionClass = app(PermissionContract::class);
		$permissions = explode('|', $string);
		$models = [];

		foreach ($permissions as $permission) {
			$models[] = $permissionClass::findOrCreate(trim($permission), $this->argument('guard'));
		}

		return collect($models);
	}

	protected function findPermissions(string $string = null) {
		if (empty($string)) {
			return;
		}

		$permissionClass = app(PermissionContract::class);
		$permissions = explode('|', $string);
		$models = [];

		foreach ($permissions as $permission) {
			$models[] = $permissionClass::findByName(trim($permission), $this->argument('guard'));
		}

		return collect($models);
	}
}
