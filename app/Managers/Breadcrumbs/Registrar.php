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
 * @file   Registrar.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Managers\Breadcrumbs;

use App\Exceptions\DefinitionAlreadyExistsException;
use App\Exceptions\DefinitionNotFoundException;
use Closure;


class Registrar {
	/**
	 * Breadcrumb definitions.
	 *
	 * @var array
	 */
	protected array $definitions = [];

	/**
	 * Get a definition for a route name.
	 *
	 * @param string $name
	 *
	 * @return Closure
	 * @throws DefinitionNotFoundException
	 */
	public function get(string $name): Closure {
		if (!$this->has($name)) {
			throw new DefinitionNotFoundException("No breadcrumbs defined for route [{$name}].");
		}

		return $this->definitions[$name];
	}

	/**
	 * Return whether a definition exists for a route name
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function has(string $name): bool {
		return array_key_exists($name, $this->definitions);
	}

	/**
	 * Set the registration for a route name.
	 *
	 * @param string  $name
	 * @param Closure $definition
	 *
	 * @return void
	 * @throws DefinitionAlreadyExistsException
	 */
	public function set(string $name, Closure $definition) {
		if ($this->has($name)) {
			throw new DefinitionAlreadyExistsException(
				"Breadcrumbs have already been defined for route [{$name}]."
			);
		}

		$this->definitions[$name] = $definition;
	}

	public function parent() {
		return $this->definitions;
	}
}