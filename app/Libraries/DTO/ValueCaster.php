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
 * @file   ValueCaster.php
 * @date   29/08/2020 04.36
 */

namespace App\Libraries\DTO;

class ValueCaster {
	public function cast($value, FieldValidator $validator) {
		return $this->shouldBeCastToCollection($value)
			? $this->castCollection($value, $validator->allowedArrayTypes)
			: $this->castValue($value, $validator->allowedTypes);
	}

	public function shouldBeCastToCollection(array $values): bool {
		if (empty($values)) {
			return false;
		}

		foreach ($values as $key => $value) {
			if (is_string($key)) {
				return false;
			}

			if (!is_array($value)) {
				return false;
			}
		}

		return true;
	}

	public function castCollection($values, array $allowedArrayTypes) {
		$castTo = null;

		foreach ($allowedArrayTypes as $type) {
			if (!is_subclass_of($type, DataTransferObject::class)) {
				continue;
			}

			$castTo = $type;

			break;
		}

		if (!$castTo) {
			return $values;
		}

		$casts = [];

		foreach ($values as $value) {
			$casts[] = new $castTo($value);
		}

		return $casts;
	}

	public function castValue($value, array $allowedTypes) {
		$castTo = null;

		foreach ($allowedTypes as $type) {
			if (!is_subclass_of($type, DataTransferObject::class)) {
				continue;
			}

			$castTo = $type;

			break;
		}

		if (!$castTo) {
			return $value;
		}

		return new $castTo($value);
	}
}