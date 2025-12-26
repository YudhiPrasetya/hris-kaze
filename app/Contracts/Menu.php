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
 * @date   27/08/2020 02.41
 */

namespace App\Contracts;

use App\Managers\Menu\Builder;
use App\Managers\Menu\Collection;


interface Menu {
	/**
	 * Initializing the Menu manager
	 */
	public function __construct();

	/**
	 * Create a new menu builder instance.
	 *
	 * @param string   $name
	 * @param callable $callback
	 *
	 * @return Builder|null
	 */
	public function makeOnce(string $name, callable $callback): ?Builder;

	/**
	 * Check if a menu builder exists.
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function exists(string $name): bool;

	/**
	 * Create a new menu builder instance.
	 *
	 * @param string   $name
	 * @param callable $callback
	 *
	 * @return Builder|null
	 */
	public function make(string $name, callable $callback): ?Builder;

	/**
	 * Loads and merges configuration data.
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function loadConf(string $name);

	/**
	 * Return Menu builder instance from the collection by key.
	 *
	 * @param string $key
	 *
	 * @return Builder
	 */
	public function get(string $key): Builder;

	/**
	 * Return Menu builder collection.
	 *
	 * @return Collection
	 */
	public function getCollection(): Collection;

	/**
	 * Alias for getCollection.
	 *
	 * @return Collection
	 * @see \App\Contracts\MenuContract::getCollection()
	 */
	public function all(): Collection;
}