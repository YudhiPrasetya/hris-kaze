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
 * @file   AppMigrate.php
 * @date   2020-10-29 5:31:13
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


class AppMigrate extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:migrate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Migrate and seed';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle() {
		$limit = ini_get('memory_limit');
		ini_set('memory_limit', '-1');

		File::delete(storage_path('logs/seed.log'));

		$this->call('app:clear', ['-y' => true]);
		$this->call('migrate:fresh');

		$before = [
			'RolesAndPermissions',
			'Position',
			'AttendanceReason',
			'Gender',
			'Priority',
			'Menu',
			'World',
		];
		$after = [
			'Jetstream',
		];

		$this->seed($before);
		$this->call('app:user:create-admin');
		$this->seed($after);
		// $this->call('app:clear', ['-y' => true]);
		// ini_set('memory_limit', $limit);

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
