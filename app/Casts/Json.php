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
 * @file   Json.php
 * @date   2020-10-29 5:31:13
 */

namespace App\Casts;

use App\Traits\Path;
use Illuminate\Support\Collection;
use stdClass;


class Json {
	use Path;


	/**
	 * @var array
	 */
	private $data = [];

	/**
	 * Json constructor.
	 *
	 * @param                      $data
	 * @param \App\Casts\Json|null $parent
	 */
	public function __construct($data) {
		if (is_string($data)) $data = json_decode($data, true);
		if (is_array($data)) $this->data = $this->__toJson($data);
	}

	private function __toJson(array &$data) {
		$class = new \stdClass();

		foreach ($data as $k => $value) {
			if (is_array($value)) {
				$class->$k = new Json($value);
			}
			else $class->$k = $value;
		}

		return $class;
	}

	public function join(string $glue) {
		return implode($glue, $this->__toArray());
	}

	public function __toArray(?object $obj = null) {
		$collection = [];
		$obj = is_null($obj) ? $this->data : $obj;

		foreach ($obj as $k => $v) {
			if (is_numeric($k)) $collection[] = $v instanceof Json ? $v->__toArray() : $v;
			else $collection[$k] = $v instanceof Json ? $v->__toArray() : $v;
		}

		return $collection;
	}

	public function __toCollection(?object $obj = null): Collection {
		$collection = collect([]);
		$obj = is_null($obj) ? $this->data : $obj;

		foreach ($obj as $k => $v) {
			$collection->put($k, $v instanceof Json ? $v->__toArray() : $v);
		}

		return $collection;
	}

	/**
	 * @return string
	 */
	public function __toString(): string {
		return json_encode($this->__toArray());
	}

	/**
	 * @param int $options
	 * @param int $depth
	 *
	 * @return false|string
	 */
	public function print(int $options = 0, int $depth = 512) {
		return json_encode($this->__toArray(), $options, $depth);
	}
}
