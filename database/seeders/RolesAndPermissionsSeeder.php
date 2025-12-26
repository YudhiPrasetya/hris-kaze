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
 * @file   RolesAndPermissionsSeeder.php
 * @date   2020-10-29 5:31:14
 */

namespace Database\Seeders;

use App\Helpers\SeederBase;
use App\Managers\Permission\PermissionRegistrar;
use App\Models\Permission;
use App\Models\Role;
use App\Repositories\Eloquent\PermissionRepository;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


class RolesAndPermissionsSeeder extends SeederBase {
	public function run() {
		$permissions = collect([]);

		// Reset cached roles and permissions
		app()[PermissionRegistrar::class]->forgetCachedPermissions();

		$this->out->writeln('[#] Adding permissions');

		// create permissions
		foreach (File::glob(resource_path('seeds/permissions/*.php')) as $file) {
			$perms = require($file);
			$perms = collect($perms)->map(function ($permission) {
				$this->message(true, "Add Permission: %s", $permission['description']);

				return [
					'name'        => $permission['name'],
					'guard_name'  => 'web',
					'description' => $permission['description'],
					'created_at'  => $permission['created_at'],
					'updated_at'  => $permission['updated_at'],
				];
			});

			$permissions = $permissions->merge($perms);
		}

		Permission::insert($permissions->toArray());

		// create roles and assign created permissions
		$perms = collect(require(resource_path('seeds/roles.php')));
		$perms->each(function ($item, $key) {
			Role::create(['name' => $item['slug'], 'level' => $item['level'], 'alias' => $item['name']])
			    ->givePermissionTo($item['permissions']);
		});
	}
}
