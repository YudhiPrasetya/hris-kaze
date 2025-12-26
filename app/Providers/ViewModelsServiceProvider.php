<?php

namespace App\Providers;

use App\Console\Commands\AppViewModelCreate;
use Illuminate\Support\ServiceProvider;


class ViewModelsServiceProvider extends ServiceProvider {
	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register() {
		if ($this->app->runningInConsole()) {
			$this->commands(
				[
					AppViewModelCreate::class,
				]
			);
		}
	}

	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot() {
		//
	}
}
