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
 * @file   Path.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Traits;

use App\Casts\ArrayObject;
use App\Casts\Json;
use Illuminate\Support\Str;


/**
 * Trait Path
 *
 * @property mixed $data
 *
 * @package App\Traits
 */
trait Path {
	/**
	 * @param string $name
	 *
	 * @return \App\Casts\Json|mixed|null
	 */
	public function &__get(string $name) {
		if (!$this->__has($name))
			$name = str_replace('_', '.', Str::snake($name));

		return $this->__getValue($name);
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return \App\Casts\Json|mixed|null
	 */
	public function __set(string $name, $value) {
		if (!$this->__has($name))
			$name = str_replace('_', '.', Str::snake($name));

		$v = $value;

		if (!$this->__has($name)) $this->add($name);
		if ($this instanceof Json && is_array($v)) $v = new Json($v);
		if ($this instanceof ArrayObject && is_array($v)) $v = new ArrayObject($v);

		return $this->__setValue($name, $v);
	}

	private function &__getValue(string $path, $default = null) {
		if ($this->keyExists($path, $this->data)) {
			if ($this instanceof Json) return $this->data->$path;
			if ($this instanceof ArrayObject) return $this->data[$path];
		}

		if (strpos($path, '.') !== false) {
			$parent = $this->getPath($path);

			if ($this->keyExists($parent, $this->data)) {
				if ($this instanceof Json) $p = $this->data->$parent;
				if ($this instanceof ArrayObject) $p = $this->data[$parent];

				return $p->__getValue($this->getPath($path, 1, null, true), $default);
			}
		}

		return $default;
	}

	private function keyExists(string $key, $obj): bool {
		$keys = [];
		if ($this instanceof Json) $keys = array_keys(get_object_vars($obj));
		if ($this instanceof ArrayObject) $keys = array_keys($obj);

		return in_array($key, $keys);
	}

	private function getPath(string $path, int $start = 0, ?int $length = 1, bool $toPath = true) {
		$part = array_slice(explode('.', $path), $start, $length);

		return $toPath ? implode('.', $part) : $part;
	}

	/**
	 * @param string $path
	 * @param array  $ref
	 *
	 * @return bool
	 */
	private function __has(string $path): bool {
		if ($this->keyExists($path, $this->data))
			return true;

		if (strpos($path, '.') !== false) {
			$parent = $this->getPath($path);

			if ($this->keyExists($parent, $this->data)) {
				if ($this instanceof Json) return $this->data->$parent->__has($this->getPath($path, 1, null, true));
				if ($this instanceof ArrayObject) return $this->data[$parent]->__has(
					$this->getPath($path, 1, null, true)
				);
			}
		}

		return false;
	}

	private function add($key) {
		if (strpos($key, '.') !== false) {
			$parent = $this->getPath($key);

			if (!$this->keyExists($parent, $this->data)) {
				if ($this instanceof Json) {
					$this->data->$parent = new Json([]);
					$this->data->$parent->add($this->getPath($key, 1, null, true));
				}

				if ($this instanceof ArrayObject) {
					$this->data[$parent] = new ArrayObject([]);
					$this->data[$parent]->add($this->getPath($key, 1, null, true));
				}
			}
			else {
				if ($this instanceof Json) {
					$this->data->$parent->add($this->getPath($key, 1, null, true));
				}

				if ($this instanceof ArrayObject) {
					$this->data[$parent]->add($this->getPath($key, 1, null, true));
				}
			}
		}
		else {
			if ($this instanceof Json) $this->data->$key = null;
			if ($this instanceof ArrayObject) $this->data[$key] = null;
		}
	}

	private function __setValue(string $path, $value) {
		if (!$this->keyExists($path, $this->data)) $this->add($path);

		if ($this->keyExists($path, $this->data)) {
			if ($this instanceof Json) $this->data->$path = $value;
			if ($this instanceof ArrayObject) $this->data[$path] = $value;

			return $this;
		}

		if (strpos($path, '.') !== false) {
			$parent = $this->getPath($path);

			if ($this->keyExists($parent, $this->data)) {
				if ($this instanceof Json)
					return $this->data->$parent->__setValue($this->getPath($path, 1, null, true), $value);

				if ($this instanceof ArrayObject)
					return $this->data[$parent]->__setValue($this->getPath($path, 1, null, true), $value);
			}
		}

		return $this;
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public function __isset($name) {
		return $this->__has($name);
	}

	/**
	 * Gets a configuration setting using a simple or nested key.
	 * Nested keys are similar to JSON paths that use the dot
	 * dot notation.
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function &get($key, $default = null) {
		return $this->__getValue($key, $default);
	}

	/**
	 * Function for setting configuration values, using
	 * either simple or nested keys.
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return void
	 */
	public function set($key, $value) {
		return $this->offsetSet($key, $value);
	}

	/**
	 * Offset to set
	 *
	 * @link https://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to assign the value to.
	 *                      </p>
	 * @param mixed $value  <p>
	 *                      The value to set.
	 *                      </p>
	 *
	 * @return mixed
	 */
	public function offsetSet($offset, $value) {
		return $this->__setValue($offset, $value);
	}

	/**
	 * Whether a offset exists
	 *
	 * @link https://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset <p>
	 *                      An offset to check for.
	 *                      </p>
	 *
	 * @return bool true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 */
	public function offsetExists($offset) {
		return $this->has($offset);
	}

	/**
	 * Function for checking if configuration values exist, using
	 * either simple or nested keys.
	 *
	 * @param string $key
	 *
	 * @return boolean
	 */
	public function has($key): bool {
		return $this->__has($key);
	}

	/**
	 * Offset to retrieve
	 *
	 * @link https://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to retrieve.
	 *                      </p>
	 *
	 * @return mixed Can return all value types.
	 */
	public function &offsetGet($offset) {
		return $this->__getValue($offset);
	}

	public function remove($key): bool {
		return $this->__remove($key);
	}

	private function __remove($key): bool {
		if ($this->keyExists($key, $this->data)) {
			if ($this instanceof Json) unset($this->data->$key);
			if ($this instanceof ArrayObject) unset($this->data[$key]);

			return true;
		}

		if (strpos($key, '.') !== false) {
			$parent = $this->getPath($key);

			if ($this instanceof Json) {
				if ($this->keyExists($parent, $this->data) && $this->data->$parent instanceof Json)
					return $this->data->$parent->__remove($this->getPath($key, 1, null, true));
			}

			if ($this instanceof ArrayObject) {
				if ($this->keyExists($parent, $this->data) && $this->data[$parent] instanceof ArrayObject)
					return $this->data[$parent]->__remove($this->getPath($key, 1, null, true));
			}
		}

		return false;
	}

	/**
	 * Offset to unset
	 *
	 * @link https://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to unset.
	 *                      </p>
	 *
	 * @return bool
	 */
	public function offsetUnset($offset): bool {
		return $this->__remove($offset);
	}
}