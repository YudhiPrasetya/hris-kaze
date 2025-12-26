<?php

namespace App\Providers;

use App\Facades\Breadcrumbs;
use App\Managers\Breadcrumbs\Manager as BreadcrumbsManager;
use Illuminate\Support\ServiceProvider;


class BreadcrumbsServiceProvider extends ServiceProvider {
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->mergeConfigFrom(config_path('breadcrumbs.php'), 'breadcrumbs');
		$this->app->singleton('breadcrumbs', BreadcrumbsManager::class);
	}

	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot() {
		$this->loadViewsFrom(resource_path('views/breadcrumbs'), 'breadcrumbs');

		$file = base_path('routes/breadcrumbs.php');
		if (file_exists($file)) require $file;
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return ['breadcrumbs'];
	}
}
