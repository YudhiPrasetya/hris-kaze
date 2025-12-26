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
 * @file   ImmutableDataTransferObject.php
 * @date   29/08/2020 04.36
 */

namespace App\Libraries\DTO;

class ImmutableDataTransferObject {
	protected DataTransferObject $dataTransferObject;

	public function __construct(DataTransferObject $dataTransferObject) {
		foreach (get_object_vars($dataTransferObject) as $k => $v) {
			if (is_subclass_of($v, DataTransferObject::class)) {
				$dataTransferObject->{$k} = new self($v);
			};
		}

		$this->dataTransferObject = $dataTransferObject;
	}

	public function __get($name) {
		return $this->dataTransferObject->{$name};
	}

	public function __set($name, $value) {
		throw DataTransferObjectError::immutable($name);
	}

	public function __call($name, $arguments) {
		return call_user_func_array([$this->dataTransferObject, $name], $arguments);
	}
}