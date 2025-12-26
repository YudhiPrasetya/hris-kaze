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
 * @file   Manager.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Managers\Breadcrumbs;

use App\Exceptions\DefinitionAlreadyExistsException;
use App\Exceptions\DefinitionNotFoundException;
use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;


class Manager {
	/**
	 * The view factory.
	 *
	 * @var Factory
	 */
	protected Factory $view;

	/**
	 * The config repository.
	 *
	 * @var Repository
	 */
	protected Repository $config;

	/**
	 * The breadcrumb generator.
	 *
	 * @var Generator
	 */
	protected Generator $generator;

	/**
	 * Create the instance of the manager.
	 *
	 * @param Factory    $view
	 * @param Repository $config
	 * @param Generator  $generator
	 *
	 * @return void
	 */
	public function __construct(Factory $view, Repository $config, Generator $generator) {
		$this->view = $view;
		$this->config = $config;
		$this->generator = $generator;
	}

	/**
	 * Register a breadcrumb definition by passing it off to the registrar.
	 *
	 * @param string  $route
	 * @param Closure $definition
	 *
	 * @return void
	 * @throws DefinitionAlreadyExistsException
	 */
	public function for(string $route, Closure $definition) {
		$this->generator->register($route, $definition);
	}

	public function has($route): bool {
		return $this->generator->has($route);
	}

	/**
	 * Render the breadcrumbs as an HTML string
	 *
	 * @param array|null $parameters
	 *
	 * @return  View
	 * @throws DefinitionNotFoundException
	 */
	public function render(?array $parameters = null): ?View {
		if ($breadcrumbs = $this->generator->generate($parameters)) {
			return $this->view->make($this->config->get('breadcrumbs.view'), compact('breadcrumbs'));
		}

		return null;
	}
}