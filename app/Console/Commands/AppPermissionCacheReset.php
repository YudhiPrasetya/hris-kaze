<?php

namespace App\Console\Commands;

use App\Managers\Permission\PermissionRegistrar;
use Illuminate\Console\Command;


class AppPermissionCacheReset extends Command {
	protected $signature = 'app:permission:cache-reset';

	protected $description = 'Reset the permission cache';

	public function handle() {
		if (app(PermissionRegistrar::class)->forgetCachedPermissions()) {
			$this->info('Permission cache flushed.');
		}
		else {
			$this->error('Unable to flush cache.');
		}
	}
}
