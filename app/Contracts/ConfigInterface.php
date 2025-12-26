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
 * @file   ConfigInterface.php
 * @date   24/08/2020 09.42
 */

namespace App\Contracts;

interface ConfigInterface {
	/**
	 * Gets a configuration setting using a simple or nested key.
	 * Nested keys are similar to JSON paths that use the dot
	 * dot notation.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 *
	 * @return mixed
	 */
	public function get($key, $default = null);

	/**
	 * Function for setting configuration values, using
	 * either simple or nested keys.
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 *
	 * @return void
	 */
	public function set($key, $value);

	/**
	 * Function for checking if configuration values exist, using
	 * either simple or nested keys.
	 *
	 * @param  string $key
	 *
	 * @return boolean
	 */
	public function has($key);

	/**
	 * Get all of the configuration items
	 *
	 * @return array
	 */
	public function all();
}