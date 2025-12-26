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
 * @file   Menu.php
 * @date   27/08/2020 02.47
 */

namespace App\Managers\Menu;

use App\Contracts\Menu as MenuContract;
use Illuminate\Support\Facades\View;


class Menu implements MenuContract {
	/**
	 * Menu collection.
	 *
	 * @var Collection
	 */
	protected $collection;

	/**
	 * List of menu builders.
	 *
	 * @var []Builder
	 */
	protected $menu = [];

	/**
	 * Initializing the Menu manager
	 */
	public function __construct() {
		// creating a collection for storing menu builders
		$this->collection = new Collection();
	}

	/**
	 * Create a new menu builder instance.
	 *
	 * @param string   $name
	 * @param callable $callback
	 *
	 * @return Builder
	 */
	public function makeOnce(string $name, callable $callback): ?Builder {
		if ($this->exists($name)) {
			return null;
		}

		return $this->make($name, $callback);
	}

	/**
	 * Check if a menu builder exists.
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function exists(string $name): bool {
		return array_key_exists($name, $this->menu);
	}

	/**
	 * Create a new menu builder instance.
	 *
	 * @param string   $name
	 * @param callable $callback
	 *
	 * @return Builder|null
	 */
	public function make(string $name, callable $callback): ?Builder {
		if (!is_callable($callback)) {
			return null;
		}

		if (!array_key_exists($name, $this->menu)) {
			$this->menu[$name] = new Builder($name, $this->loadConf($name));
		}

		// Registering the items
		call_user_func($callback, $this->menu[$name]);

		// Storing each menu instance in the collection
		$this->collection->put($name, $this->menu[$name]);

		// Make the instance available in all views
		View::share($name, $this->menu[$name]);

		return $this->menu[$name];
	}

	/**
	 * Loads and merges configuration data.
	 *
	 * @param string $name
	 *
	 * @return array
	 */
	public function loadConf(string $name) {
		$options = config('menu.settings');
		$name = strtolower($name);

		if (isset($options[$name]) && is_array($options[$name])) {
			return array_merge($options['default'], $options[$name]);
		}

		return $options['default'];
	}

	/**
	 * Return Menu builder instance from the collection by key.
	 *
	 * @param string $key
	 *
	 * @return Builder
	 */
	public function get(string $key): Builder {
		return $this->collection->get($key);
	}

	/**
	 * Return Menu builder collection.
	 *
	 * @return Collection
	 */
	public function getCollection(): Collection {
		return $this->collection;
	}

	/**
	 * Alias for getCollection.
	 *
	 * @return Collection
	 */
	public function all(): Collection {
		return $this->collection;
	}
}