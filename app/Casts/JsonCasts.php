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
 * @file   JsonCasts.php
 * @date   2020-10-29 5:31:13
 */

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;


class JsonCasts implements CastsAttributes {
	/**
	 * Cast the given value.
	 *
	 * @param Model  $model
	 * @param string $key
	 * @param mixed  $value
	 * @param array  $attributes
	 *
	 * @return mixed
	 */
	public function &get($model, string $key, $value, array $attributes) {
		$json = new Json($value);

		return $json;
	}

	/**
	 * Prepare the given value for storage.
	 *
	 * @param Model  $model
	 * @param string $key
	 * @param array  $value
	 * @param array  $attributes
	 *
	 * @return mixed
	 */
	public function set($model, string $key, $value, array $attributes) {
		if ($value instanceof Json) {
			return (string)$value;
		}

		return ($value instanceof Json) ? (string) $value : $value;
	}
}
