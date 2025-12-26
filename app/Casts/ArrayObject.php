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
 * @file   ArrayObject.php
 * @date   2020-10-29 5:31:13
 */

namespace App\Casts;

use App\Traits\Path;
use ArrayAccess;
use Illuminate\Support\Collection;
use Iterator;


class ArrayObject implements ArrayAccess, Iterator {
	use Path;


	private array $data;

	public function __construct(array $data) {
		$this->data = $data;
		$this->__toObject();
	}

	private function __toObject() {
		foreach ($this->data as &$d) {
			if (is_array($d)) $d = new ArrayObject($d);
		}
	}

	/**
	 * Return the current element
	 *
	 * @link https://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 */
	public function current() {
		return (is_array($this->data) ? current($this->data) : null);
	}

	/**
	 * Move forward to next element
	 *
	 * @link https://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next() {
		return (is_array($this->data) ? next($this->data) : null);
	}

	/**
	 * Return the key of the current element
	 *
	 * @link https://php.net/manual/en/iterator.key.php
	 * @return string|float|int|bool|null scalar on success, or null on failure.
	 */
	public function key() {
		return (is_array($this->data) ? key($this->data) : null);
	}

	/**
	 * Checks if current position is valid
	 *
	 * @link https://php.net/manual/en/iterator.valid.php
	 * @return bool The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 */
	public function valid() {
		return (is_array($this->data) ? key($this->data) !== null : false);
	}

	/**
	 * Rewind the Iterator to the first element
	 *
	 * @link https://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind() {
		return (is_array($this->data) ? reset($this->data) : null);
	}

	public function __toJson(): Json {
		return new Json($this->__toArray());
	}

	public function __toArray(): array {
		$collection = [];

		foreach ($this->data as $k => $v) {
			if (is_numeric($k)) $collection[] = $v instanceof ArrayObject ? $v->__toArray() : $v;
			else $collection[$k] = $v instanceof ArrayObject ? $v->__toArray() : ($v instanceof Collection ? (new ArrayObject($v->toArray()))->__toArray() : $v);
		}

		return $collection;
	}

	public function __toCollection(): Collection {
		return collect($this->__toArray());
	}
}
