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
 * @file   AppPermissionUpdate.php
 * @date   2020-10-29 5:31:13
 */

namespace App\Console\Commands;

#use App\Models\Master\Permission;
#use App\Models\Master\Role;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Eloquent\RoleRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;


#use App\Repositories\Eloquent\Master\RoleRepository;

class AppPermissionUpdate extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:permission:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update all permissions';

	/**
	 * @var \App\Repositories\Eloquent\RoleRepository|null
	 */
	protected $repository = null;

	public function __construct(RoleRepository $repository) {
		parent::__construct();

		$this->repository = $repository;
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle() {
		$before = [
			'RolesAndPermissions',
			'Menu',
		];

		$after = [
			'Jetstream',
		];

		$this->seed($before);
		$this->call('app:user:create-admin');
		$this->seed($after);
		$this->call('app:clear');

		return 0;
	}

	public function seed(array $list) {
		foreach ($list as $class) {
			if (!empty($class)) {
				$this->call('db:seed', ['--class' => $class . 'Seeder']);
			}
		}
	}
}
