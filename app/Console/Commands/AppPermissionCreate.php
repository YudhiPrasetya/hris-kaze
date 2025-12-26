<?php

namespace App\Console\Commands;

use App\Contracts\Permission as PermissionContract;
use Illuminate\Console\Command;


class AppPermissionCreate extends Command {
	protected $signature = 'app:permission:create-permission 
                {name : The name of the permission} 
                {guard? : The name of the guard}';

	protected $description = 'Create/Update a permission';

	public function handle() {
		$permissionClass = app(PermissionContract::class);

		$permission = $permissionClass::findOrCreate($this->argument('name'), $this->argument('guard'));

		$this->info("Permission `{$permission->name}` created");
	}
}
