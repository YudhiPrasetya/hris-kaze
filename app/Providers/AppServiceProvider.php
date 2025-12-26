<?php

namespace App\Providers;

use App\Managers\Hashing\HashManager;
use App\Managers\Socialite\Providers\UtmsProvider;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;


class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		if ($this->app->isLocal()) {
			$this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
			$this->app->register(TelescopeServiceProvider::class);
		}

		$this->mergeConfigFrom(config_path('settings.php'), 'settings');
		$this->app->bind('_',_::class);

		$this->registerHasher();
	}

	private function registerHasher() {
		// @formatter:off
		$this->app->extend('hash',function ($singleton, $app) {
			return new HashManager($app);
		});

		$this->app->extend('hash.driver',function ($singleton, $app) {
			return $app['hash']->driver();
		});
		// @formatter:on
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		$this->addMacros();
		$this->addSocialiteProviders();
	}

	protected function addMacros() {
		Stringable::macro('pos', function (string $value) {
			return strpos($this->value, $value);
		});
		Stringable::macro('ipos', function (string $value) {
			return stripos($this->value, $value);
		});
		Str::macro('concat', function ($glue, ...$str) {
			return implode($glue, $str);
		});

		$separate = function (string $text, int $length, int $start = 0) {
			$arr = [];
			$arr[] = substr($text, $start, $length);

			if (strlen(substr($text, $start + $length)) > 0) {
				$arr = array_merge($arr, Str::separate($text, $length, $start + $length));
			}

			return $arr;
		};
		Str::macro('separate', $separate);
		Stringable::macro('separate', $separate);
		Collection::macro('indexOf', function($value) {
			return array_search($value, $this->items);
		});
		Builder::macro('whereExcept', function(string $name, ...$values) {
			return $this->whereNotIn($name, $values[0]);
		});
		Builder::macro('whereOnly', function(string $name, ...$values) {
			return $this->whereIn($name, $values[0]);
		});
	}

	protected function addSocialiteProviders() {
		$socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
		$socialite->extend(
			'utms',
			function ($app) use ($socialite) {
				$config = $app['config']['services.utms'];
				return $socialite->buildProvider(UtmsProvider::class, $config);
			}
		);
	}

	public function provides() {
		return [
			_::class,
			'_',
			// Hashing
			'hash',
			'hash.driver'
		];
	}
}
