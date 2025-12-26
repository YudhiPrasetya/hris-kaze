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
 * @file   roles.php
 * @date   17/09/2020 08.43
 */

use App\Models\Permission;


return array(
	[
		'name'        => 'Super Admin',
		'slug'        => 'super-admin',
		'description' => 'Super administrator users can perform any action.',
		'level'       => 99,
		'permissions' => Permission::all(),
	],
	[
		'name'        => 'Admin',
		'slug'        => 'admin',
		'description' => 'Administrator users almost can perform any action.',
		'level'       => 50,
		'permissions' => Permission::all(),
	],
	[
		'name'        => 'Director',
		'slug'        => 'director',
		'description' => 'director users have the ability to read, create, and update.',
		'level'       => 12,
		'permissions' => [],
	],
	[
		'name'        => 'Manager',
		'slug'        => 'manager',
		'description' => 'Manager users have the ability to read, create, and update.',
		'level'       => 11,
		'permissions' => [],
	],
	[
		'name'        => 'Operator',
		'slug'        => 'operator',
		'description' => 'Operator users have the ability to read, create, and update.',
		'level'       => 10,
		'permissions' => [],
	],
	[
		'name'        => 'Sales',
		'slug'        => 'sales',
		'description' => 'Sales users have the ability to read.',
		'level'       => 9,
		'permissions' => [],
	],
	[
		'name'        => 'Technician',
		'slug'        => 'technician',
		'description' => 'Technician Role',
		'level'       => 8,
		'permissions' => [],
	],
	[
		'name'        => 'Employee',
		'slug'        => 'employee',
		'description' => 'Employee Role',
		'level'       => 2,
		'permissions' => [],
	],
	[
		'name'        => 'User',
		'slug'        => 'user',
		'description' => 'User Role',
		'level'       => 1,
		'permissions' => [],
	],
	[
		'name'        => 'Unverified',
		'slug'        => 'unverified',
		'description' => 'Unverified Role',
		'level'       => 0,
		'permissions' => [],
	],
);
