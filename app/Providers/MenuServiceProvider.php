<?php

namespace App\Providers;

use App\Contracts\Menu as MenuContract;
use App\Managers\Menu\Menu;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;


class MenuServiceProvider extends ServiceProvider {
	// Patterns and Replace string for lm-attr
	// Remove with next major version

	const LM_ATTRS_PATTERN = '/(\s*)@lm-attrs\s*\((\$[^)]+)\)/';

	const LM_ATTRS_REPLACE = '$1<?php $lm_attrs = $2->attr(); ob_start(); ?>';

	// Patterns and Replace string for lm-endattr
	// Remove with next major version

	const LM_ENDATTRS_PATTERN = '/(?<!\w)(\s*)@lm-endattrs(\s*)/';

	const LM_ENDATTRS_REPLACE = '$1<?php echo \App\Managers\Menu\Builder::mergeStatic(ob_get_clean(), $lm_attrs); ?>$2';

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register() {
		$this->mergeConfigFrom(config_path('menu.php'), 'menu');
		// $this->mergeConfigFrom(__DIR__ . '/../../config/views.php', 'menu.views');

		$this->app->singleton(
			Menu::class,
			function ($app) {
				return new Menu();
			}
		);
		$this->app->alias(Menu::class, MenuContract::class);
		$this->app->alias(Menu::class, 'menu');
		$this->registerHelper();
	}

	/*
	 * Extending Blade engine. Remove with next major version
	 *
	 * @deprecated
	 * @return void
	 */

	/**
	 * Register All Helpers.
	 *
	 * @return void
	 */
	public function registerHelper() {
		foreach (File::glob(__DIR__ . '/../../helpers/menu*.php') as $filename) {
			require_once $filename;
		}
	}

	/*
	 * Adding custom Blade directives.
	 */

	/**
	 * Bootstrap the application events.
	 */
	public function boot() {
		$this->bladeDirectives();
		$this->bladeExtensions();
		$this->loadViewsFrom(resource_path('views/menu'), 'menu');
	}

	protected function bladeDirectives() {
		/*
		 * Buffers the output if there's any.
		 * The output will be passed to mergeStatic()
		 * where it is merged with item's attributes
		 */
		Blade::directive(
			'lm_attrs',
			function ($expression) {
				return '<?php $lm_attrs = ' . $expression . '->attr(); ob_start(); ?>';
			}
		);

		/*
		 * Reads the buffer data using ob_get_clean()
		 * and passes it to MergeStatic().
		 * mergeStatic() takes the static string,
		 * converts it into a normal array and merges it with others.
		 */
		Blade::directive(
			'lm_endattrs',
			function ($expression) {
				return '<?php echo \App\Managers\Menu\Builder::mergeStatic(ob_get_clean(), $lm_attrs); ?>';
			}
		);
	}

	protected function bladeExtensions() {
		Blade::extend(
			function ($view, $compiler) {
				if (preg_match(self::LM_ATTRS_PATTERN, $view)) {
					Log::debug(
						"menu: @lm-attrs/@lm-endattrs is deprecated. Please switch to @lm_attrs and @lm_endattrs"
					);
				}

				return preg_replace(self::LM_ATTRS_PATTERN, self::LM_ATTRS_REPLACE, $view);
			}
		);

		Blade::extend(
			function ($view, $compiler) {
				return preg_replace(self::LM_ENDATTRS_PATTERN, self::LM_ENDATTRS_REPLACE, $view);
			}
		);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return [
			Menu::class,
			MenuContract::class
		];
	}
}
