<?php

namespace App\Providers;

use App\Console\Commands\AppPermissionCacheReset;
use App\Console\Commands\AppPermissionCreate;
use App\Console\Commands\AppPermissionCreateRole;
use App\Console\Commands\AppPermissionShow;
use App\Contracts\Permission as PermissionContract;
use App\Contracts\Role as RoleContract;
use App\Managers\Permission\PermissionRegistrar;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;


class PermissionServiceProvider extends ServiceProvider {
	public function boot(PermissionRegistrar $permissionLoader) {
		$this->registerMacroHelpers();
		$this->commands(
			[
				AppPermissionCacheReset::class,
				AppPermissionCreateRole::class,
				AppPermissionCreate::class,
				AppPermissionShow::class,
			]
		);

		$this->registerModelBindings();

		$permissionLoader->clearClassPermissions();
		$permissionLoader->registerPermissions();

		$this->app->singleton(
			PermissionRegistrar::class,
			function ($app) use ($permissionLoader) {
				return $permissionLoader;
			}
		);
	}

	protected function registerMacroHelpers() {
		if (!method_exists(Route::class, 'macro')) { // Lumen
			return;
		}

		Route::macro(
			'role',
			function ($roles = []) {
				if (!is_array($roles)) {
					$roles = [$roles];
				}

				$roles = implode('|', $roles);
				$this->middleware("role:$roles");

				return $this;
			}
		);

		Route::macro(
			'permission',
			function ($permissions = []) {
				if (!is_array($permissions)) {
					$permissions = [$permissions];
				}

				$permissions = implode('|', $permissions);

				$this->middleware("permission:$permissions");

				return $this;
			}
		);
	}

	protected function registerModelBindings() {
		$config = $this->app->config['permission.models'];

		if (!$config) {
			return;
		}

		$this->app->bind(PermissionContract::class, $config['permission']);
		$this->app->bind(RoleContract::class, $config['role']);
	}

	public function register() {
		$this->mergeConfigFrom(
			config_path('permission.php'),
			'permission'
		);

		$this->registerBladeExtensions();
	}

	protected function registerBladeExtensions() {
		$this->app->afterResolving(
			'blade.compiler',
			function (BladeCompiler $bladeCompiler) {
				$bladeCompiler->directive(
					'role',
					function ($arguments) {
						list($role, $guard) = explode(',', $arguments . ',');

						return "<?php if(auth({$guard})->check() && auth({$guard})->user()->hasRole({$role})): ?>";
					}
				);
				$bladeCompiler->directive(
					'elserole',
					function ($arguments) {
						list($role, $guard) = explode(',', $arguments . ',');

						return "<?php elseif(auth({$guard})->check() && auth({$guard})->user()->hasRole({$role})): ?>";
					}
				);
				$bladeCompiler->directive(
					'endrole',
					function () {
						return '<?php endif; ?>';
					}
				);

				$bladeCompiler->directive(
					'hasrole',
					function ($arguments) {
						list($role, $guard) = explode(',', $arguments . ',');

						return "<?php if(auth({$guard})->check() && auth({$guard})->user()->hasRole({$role})): ?>";
					}
				);
				$bladeCompiler->directive(
					'endhasrole',
					function () {
						return '<?php endif; ?>';
					}
				);

				$bladeCompiler->directive(
					'hasanyrole',
					function ($arguments) {
						list($roles, $guard) = explode(',', $arguments . ',');

						return "<?php if(auth({$guard})->check() && auth({$guard})->user()->hasAnyRole({$roles})): ?>";
					}
				);
				$bladeCompiler->directive(
					'endhasanyrole',
					function () {
						return '<?php endif; ?>';
					}
				);

				$bladeCompiler->directive(
					'hasallroles',
					function ($arguments) {
						list($roles, $guard) = explode(',', $arguments . ',');

						return "<?php if(auth({$guard})->check() && auth({$guard})->user()->hasAllRoles({$roles})): ?>";
					}
				);
				$bladeCompiler->directive(
					'endhasallroles',
					function () {
						return '<?php endif; ?>';
					}
				);

				$bladeCompiler->directive(
					'unlessrole',
					function ($arguments) {
						list($role, $guard) = explode(',', $arguments . ',');

						return "<?php if(!auth({$guard})->check() || ! auth({$guard})->user()->hasRole({$role})): ?>";
					}
				);
				$bladeCompiler->directive(
					'endunlessrole',
					function () {
						return '<?php endif; ?>';
					}
				);
			}
		);
	}
}
