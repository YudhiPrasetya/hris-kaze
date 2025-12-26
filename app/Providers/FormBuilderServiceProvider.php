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
 * @file   FormBuilderServiceProvider.php
 * @date   28/08/2020 19.30
 */

namespace App\Providers;

use App\Console\Commands\AppFormCreate;
use App\Facades\Form as FormFacade;
use App\Facades\Html;
use App\Libraries\Html\FormBuilder as LaravelForm;
use App\Libraries\Html\HtmlBuilder;
use App\Managers\Form\Form;
use App\Managers\Form\FormBuilder;
use App\Managers\Form\FormHelper;
use App\Traits\ValidatesWhenResolved;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;


class FormBuilderServiceProvider extends ServiceProvider {
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {

		$this->commands(AppFormCreate::class);
		$this->registerHtmlIfNeeded();
		$this->registerFormIfHeeded();
		$this->mergeConfigFrom(config_path('form.php'), 'laravel-form-builder');
		$this->registerFormHelper();
		// @formatter:off
		$this->app->singleton('laravel-form-builder',function ($app) {
			return new FormBuilder($app, $app['laravel-form-helper'], $app['events']);
		});
		// @formatter:on

		$this->app->alias('laravel-form-builder', FormBuilder::class);
		// @formatter:off
		$this->app->afterResolving(Form::class,function ($object, $app) {
			$request = $app->make('request');

			if (in_array(ValidatesWhenResolved::class, class_uses($object)) && $request->method() !== 'GET') {
				$form = $app->make('laravel-form-builder')->setDependenciesAndOptions($object);
				$form->buildForm();
				$form->redirectIfNotValid();
			}
		});
		// @formatter:on
		$this->registerHelper();
	}

	private function registerHelper() {
		foreach (File::glob(app_path('Helpers/form*.php')) as $helper) {
			require_once $helper;
		}
	}

	/**
	 * Add Laravel Html to container if not already set.
	 */
	private function registerHtmlIfNeeded() {
		if (!$this->app->has('html')) {
			// @formatter:off
			$this->app->singleton('html',function ($app) {
				return new HtmlBuilder($app['url'], $app['view']);
			});
			// @formatter:on

			if (!$this->aliasExists('Html')) {
				AliasLoader::getInstance()->alias('Html', Html::class);
			}
		}
	}

	/**
	 * Check if an alias already exists in the IOC.
	 *
	 * @param string $alias
	 *
	 * @return bool
	 */
	private function aliasExists($alias) {
		return array_key_exists($alias, AliasLoader::getInstance()->getAliases());
	}

	/**
	 * Add Laravel Form to container if not already set.
	 *
	 * @return void
	 */
	private function registerFormIfHeeded() {
		if (!$this->app->has('form')) {
			// @formatter:off
			$this->app->singleton('form',function ($app) {
				// LaravelCollective\HtmlBuilder 5.2 is not backward compatible and will throw an exception
				$version = substr(Application::VERSION, 0, 3);

				if (Str::is('5.4', $version)) {
					$form = new LaravelForm($app['html'], $app['url'], $app['view'], $app['session.store']->token());
				}
				else if (Str::is('5.0', $version) || Str::is('5.1', $version)) {
					$form = new LaravelForm($app['html'], $app['url'], null, $app['session.store']->token());
				}
				else {
					$form = new LaravelForm($app['html'], $app['url'], $app['view'], $app['session.store']->token());
				}

				return $form->setSessionStore($app['session.store']);
			});
			// @formatter:on

			if (!$this->aliasExists('Form')) {
				AliasLoader::getInstance()->alias('Form', FormFacade::class);
			}
		}
	}

	/**
	 * Register the form helper.
	 *
	 * @return void
	 */
	protected function registerFormHelper() {
		// @formatter:off
		$this->app->singleton('laravel-form-helper',function ($app) {
			$configuration = $app['config']->get('laravel-form-builder');

			return new FormHelper($app['view'], $app['translator'], $configuration);
		});
		// @formatter:on

		$this->app->alias('laravel-form-helper', FormHelper::class);
	}

	/**
	 * Bootstrap the service.
	 *
	 * @return void
	 */
	public function boot() {
		$this->loadViewsFrom(resource_path('views/form'), 'laravel-form-builder');
		$form = $this->app['form'];
		// @formatter:off
		$form->macro('customLabel', function ($name, $value, $options = []) use ($form) {
			if (isset($options['for']) && $for = $options['for']) {
				unset($options['for']);

				return $form->label($for, $value, $options);
			}

			return $form->label($name, $value, $options);
		});
		// @formatter:on
	}

	/**
	 * Get the services provided by this provider.
	 *
	 * @return string[]
	 */
	public function provides() {
		return ['laravel-form-builder'];
	}
}
