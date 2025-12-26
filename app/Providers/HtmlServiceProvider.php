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
 * @file   HtmlServiceProvider.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Providers;

use App\Libraries\Html\FormBuilder;
use App\Libraries\Html\HtmlBuilder;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;


class HtmlServiceProvider extends ServiceProvider implements DeferrableProvider {
	/**
	 * Supported Blade Directives
	 *
	 * @var array
	 */
	protected array $directives = [
		'button',
		'checkbox',
		'close',
		'color',
		'date',
		'datetime',
		'datetimeLocal',
		'decode',
		'dl',
		'email',
		'email',
		'entities',
		'favicon',
		'file',
		'getSelectOption',
		'hidden',
		'image',
		'input',
		'label',
		'link',
		'linkAction',
		'linkAsset',
		'linkRoute',
		'linkSecureAsset',
		'mailto',
		'meta',
		'model',
		'number',
		'ol',
		'old',
		'open',
		'password',
		'radio',
		'reset',
		'script',
		'secureLink',
		'select',
		'selectMonth',
		'selectRange',
		'selectYear',
		'style',
		'submit',
		'tag',
		'tel',
		'text',
		'textarea',
		'time',
		'token',
		'ul',
		'url',
	];

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {
		$this->registerHtmlBuilder();
		$this->registerFormBuilder();
		$this->app->alias('html', HtmlBuilder::class);
		$this->app->alias('form', FormBuilder::class);
		$this->registerHelper();
		$this->registerBladeDirectives();
	}

	/**
	 * Register the HTML builder instance.
	 *
	 * @return void
	 */
	protected function registerHtmlBuilder() {
		// @formatter:off
		$this->app->singleton('html',function ($app) {
			return new HtmlBuilder($app['url'], $app['view']);
		});
		// @formatter:on
	}

	/**
	 * Register the form builder instance.
	 *
	 * @return void
	 */
	protected function registerFormBuilder() {
		// @formatter:off
		$this->app->singleton('form',function ($app) {
			$form = new FormBuilder(
				$app['html'],
				$app['url'],
				$app['view'],
				$app['session.store']->token(),
				$app['request']
			);

			return $form->setSessionStore($app['session.store']);
		});
		// @formatter:on
	}

	private function registerHelper() {
		foreach (File::glob(app_path('Helpers/html*.php')) as $helper) {
			require_once $helper;
		}
	}

	/**
	 * Register Blade directives.
	 *
	 * @return void
	 */
	protected function registerBladeDirectives() {
		$namespaces = [
			'Html' => get_class_methods(HtmlBuilder::class),
			'Form' => get_class_methods(FormBuilder::class),
		];

		foreach ($namespaces as $namespace => $methods) {
			foreach ($methods as $method) {
				if (in_array($method, $this->directives)) {
					$snakeMethod = Str::snake($method);
					$directive = strtolower($namespace) . '_' . $snakeMethod;
					// @formatter:off
					Blade::directive($directive,function ($expression) use ($namespace, $method) {
						return "<?php echo $namespace::$method($expression); ?>";
					});
					// @formatter:on
				}
			}
		}
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return ['html', 'form', HtmlBuilder::class, FormBuilder::class];
	}
}
