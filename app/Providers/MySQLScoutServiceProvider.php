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
 * @file   MySQLScoutServiceProvider.php
 * @date   2020-09-28 14:31:37
 */

namespace App\Providers;

use App\Console\Commands\AppScoutMySQLIndexes;
use App\Engines\Modes\ModeContainer;
use App\Engines\MySQLEngine;
use App\Services\IndexService;
use App\Services\ModelService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Scout\EngineManager;


class MySQLScoutServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap the application services.
	 */
	public function boot() {
		if ($this->app->runningInConsole()) {
			$this->commands([
				AppScoutMySQLIndexes::class,
			]);
		}

		$this->app->make(EngineManager::class)->extend('mysql',
			function () {
				return new MySQLEngine(app(ModeContainer::class));
			});
	}

	/**
	 * Register the application services.
	 */
	public function register() {
		$this->app->singleton(ModelService::class,
			function ($app) {
				return new ModelService();
			});

		$this->app->singleton(IndexService::class,
			function ($app) {
				return new IndexService($app->make(ModelService::class));
			});

		$this->app->singleton(ModeContainer::class,
			function ($app) {
				$engineNamespace = 'App\\Engines\\Modes\\';
				$mode = $engineNamespace . Str::studly(strtolower(config('scout-driver.mysql.mode')));
				$fallbackMode = $engineNamespace .
				                Str::studly(strtolower(config('scout-driver.mysql.min_fulltext_search_fallback')));

				return new ModeContainer(new $mode(), new $fallbackMode());
			});
	}
}