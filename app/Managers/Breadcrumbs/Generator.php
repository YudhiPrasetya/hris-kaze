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
 * @file   Generator.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Managers\Breadcrumbs;

use App\Exceptions\DefinitionNotFoundException;
use Closure;
use Illuminate\Contracts\Routing\Registrar as Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;


class Generator {
	/**
	 * The router.
	 *
	 * @var Router
	 */
	protected Router $router;

	/**
	 * The breadcrumb registrar.
	 *
	 * @var Registrar
	 */
	protected Registrar $registrar;

	/**
	 * The breadcrumb trail.
	 *
	 * @var Collection
	 */
	protected Collection $breadcrumbs;

	/**
	 * Create a new instance of the generator.
	 *
	 * @param Router    $router
	 * @param Registrar $registrar
	 *
	 */
	public function __construct(Router $router, Registrar $registrar) {
		$this->router = $router;
		$this->registrar = $registrar;
		$this->breadcrumbs = new Collection;
	}

	/**
	 * Register a definition with the registrar.
	 *
	 * @param string  $name
	 * @param Closure $definition
	 *
	 * @return void
	 * @throws \App\Exceptions\DefinitionAlreadyExistsException
	 */
	public function register(string $name, Closure $definition) {
		$this->registrar->set($name, $definition);
	}

	public function has(string $name): bool {
		return $this->registrar->has($name);
	}

	/**
	 * Generate the collection of breadcrumbs from the given route.
	 *
	 * @param array|null $parameters
	 *
	 * @return Collection
	 * @throws DefinitionNotFoundException
	 */
	public function generate(array $parameters = null): Collection {
		$route = $this->router->current();

		$parameters = isset($parameters) ? Arr::wrap($parameters) : $route->parameters;

		if ($route && $this->registrar->has($route->getName())) {
			$this->call($route->getName(), $parameters);
		}

		return $this->breadcrumbs;
	}

	/**
	 * Call the breadcrumb definition with the given parameters.
	 *
	 * @param string $name
	 * @param array  $parameters
	 *
	 * @return void
	 * @throws DefinitionNotFoundException
	 */
	protected function call(string $name, array $parameters) {
		$definition = $this->registrar->get($name);
		$parameters = Arr::prepend(array_values($parameters), $this);
		call_user_func_array($definition, $parameters);
	}

	/**
	 * Call a parent route with the given parameters.
	 *
	 * @param string $name
	 * @param mixed  $parameters
	 *
	 * @return void
	 * @throws DefinitionNotFoundException
	 */
	public function parent(string $name, ...$parameters) {
		$this->call($name, $parameters);
	}

	/**
	 * Add a breadcrumb to the collection.
	 *
	 * @param string|array            $title
	 * @param string                  $url
	 *
	 * @param mixed|string|array|null $route
	 *
	 * @return void
	 */
	public function add($title, string $url, $route = null) {
		$this->breadcrumbs->push(new Crumb($title, $url, $route));
	}
}