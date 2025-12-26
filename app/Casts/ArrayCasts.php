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
 * @file   ArrayCasts.php
 * @date   2020-10-29 5:31:13
 */

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;


/**
 * Class ArrayCasts
 *
 * @name ArrayCasts
 * @package App\Casts
 */
class ArrayCasts implements CastsAttributes {
	/**
	 * Cast the given value.
	 *
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @param string                              $key
	 * @param mixed                               $value
	 * @param array                               $attributes
	 *
	 * @return mixed
	 */
	public function &get($model, string $key, $value, array $attributes) {
		return new ArrayObject($value);
	}

	/**
	 * Prepare the given value for storage.
	 *
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @param string                              $key
	 * @param array                               $value
	 * @param array                               $attributes
	 *
	 * @return mixed
	 */
	public function set($model, string $key, $value, array $attributes) {
		return $value instanceof ArrayObject ? $value->__toArray() : $value;
	}
}
