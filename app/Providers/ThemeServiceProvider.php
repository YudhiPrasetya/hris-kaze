<?php

namespace App\Providers;

use App\Console\Commands\AppThemeCreate;
use App\Console\Commands\AppThemeList;
use App\Contracts\Theme as ThemeContract;
use App\Managers\Theme\Theme;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;


class ThemeServiceProvider extends ServiceProvider {
	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register() {
		$this->mergeConfigFrom(config_path('theme.php'), 'theme');
		$this->registerTheme();
		$this->registerHelper();
		$this->consoleCommand();
		$this->registerMiddleware();

		$this->loadViewsFrom(resource_path('themes'), 'themes');
	}

	/**
	 * Register theme required components .
	 *
	 * @return void
	 */
	public function registerTheme() {
		$this->app->singleton(
			Theme::class,
			function ($app) {
				return new Theme(
					$app,
					$this->app['view']->getFinder(),
					$this->app['config'],
					$this->app['translator']
				);
			}
		);
		$this->app->alias(Theme::class, ThemeContract::class);
		$this->app->alias(Theme::class, 'theme');
	}

	/**
	 * Register All Helpers.
	 *
	 * @return void
	 */
	public function registerHelper() {
		foreach (glob(__DIR__ . '/../Helpers/theme*.php') as $filename) {
			require_once $filename;
		}
	}

	/**
	 * Add Commands.
	 *
	 * @return void
	 */
	public function consoleCommand() {
		$this->registerCommands();
		// Assign commands.
		$this->commands('theme.create', 'theme.list');
	}

	/**
	 * Register commands.
	 *
	 * @return void
	 */
	public function registerCommands() {
		$this->app->singleton('theme.list', AppThemeList::class);
		$this->app->singleton(
			'theme.create',
			function ($app) {
				return new AppThemeCreate($app['config'], $app['files']);
			}
		);
	}

	/**
	 * Add Theme Types Middleware.
	 *
	 * @return void
	 */
	public function registerMiddleware() {
		if (config('theme.types.enable')) {
			$themeTypes = config('theme.types.middleware');
			foreach ($themeTypes as $middleware => $themeName) {
				$this->app['router']->aliasMiddleware($middleware, '\App\Http\Middleware\Route:' . $themeName);
			}
		}
	}

	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot() {
		if (!File::exists(public_path('themes')) && !File::exists(config('theme.symlink_path')) &&
		    config('theme.symlink') && File::exists(config('theme.theme_path'))) {

			App::make('files')->link(
				config('theme.theme_path'),
				config('theme.symlink_path', public_path('themes'))
			);
		}
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return [
			Theme::class,
			ThemeContract::class
		];
	}
}
