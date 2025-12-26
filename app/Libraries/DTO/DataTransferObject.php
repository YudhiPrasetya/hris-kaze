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
 * @file   DataTransferObject.php
 * @date   29/08/2020 04.21
 */

declare(strict_types=1);

namespace App\Libraries\DTO;

use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionProperty;


abstract class DataTransferObject {
	/**
	 * @var bool
	 */
	protected bool $ignoreMissing = false;

	/**
	 * @var array
	 */
	protected array $exceptKeys = [];

	/**
	 * @var array
	 */
	protected array $onlyKeys = [];

	public function __construct(array $parameters = []) {
		$validators = $this->getFieldValidators();
		$valueCaster = $this->getValueCaster();

		foreach ($validators as $field => $validator) {
			if (!isset($parameters[$field]) && !$validator->hasDefaultValue && !$validator->isNullable) {
				throw DataTransferObjectError::uninitialized(static::class, $field);
			}

			$value = $parameters[$field] ?? $this->{$field} ?? null;
			$value = $this->castValue($valueCaster, $validator, $value);

			if (!$validator->isValidType($value)) {
				throw DataTransferObjectError::invalidType(
					static::class,
					$field,
					$validator->allowedTypes,
					$value
				);
			}

			$this->{$field} = $value;

			unset($parameters[$field]);
		}

		if (!$this->ignoreMissing && count($parameters)) {
			throw DataTransferObjectError::unknownProperties(array_keys($parameters), static::class);
		}
	}

	/**
	 * @return FieldValidator[]
	 */
	protected function getFieldValidators(): array {
		return DTOCache::resolve(
			static::class,
			function () {
				$class = new ReflectionClass(static::class);

				$properties = [];

				foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
					// Skip static properties
					if ($reflectionProperty->isStatic()) {
						continue;
					}

					$field = $reflectionProperty->getName();

					$properties[$field] = FieldValidator::fromReflection($reflectionProperty);
				}

				return $properties;
			}
		);
	}

	protected function getValueCaster(): ValueCaster {
		return new ValueCaster();
	}

	/**
	 * @param ValueCaster    $valueCaster
	 * @param FieldValidator $fieldValidator
	 * @param mixed          $value
	 *
	 * @return mixed
	 */
	protected function castValue(ValueCaster $valueCaster, FieldValidator $fieldValidator, $value) {
		if (is_array($value)) {
			return $valueCaster->cast($value, $fieldValidator);
		}

		return $value;
	}

	/**
	 * @param array $parameters
	 *
	 * @return ImmutableDataTransferObject|static
	 */
	public static function immutable(array $parameters = []): ImmutableDataTransferObject {
		return new ImmutableDataTransferObject(new static($parameters));
	}

	/**
	 * @param array $arrayOfParameters
	 *
	 * @return ImmutableDataTransferObject[]|static[]
	 */
	public static function arrayOf(array $arrayOfParameters): array {
		return array_map(
			function ($parameters) {
				return new static($parameters);
			},
			$arrayOfParameters
		);
	}

	/**
	 * @param string ...$keys
	 *
	 * @return static
	 */
	public function only(string ...$keys): DataTransferObject {
		$dataTransferObject = clone $this;

		$dataTransferObject->onlyKeys = [...$this->onlyKeys, ...$keys];

		return $dataTransferObject;
	}

	/**
	 * @param string ...$keys
	 *
	 * @return static
	 */
	public function except(string ...$keys): DataTransferObject {
		$dataTransferObject = clone $this;

		$dataTransferObject->exceptKeys = [...$this->exceptKeys, ...$keys];

		return $dataTransferObject;
	}

	public function toArray(): array {
		if (count($this->onlyKeys)) {
			$array = Arr::only($this->all(), $this->onlyKeys);
		}
		else {
			$array = Arr::except($this->all(), $this->exceptKeys);
		}

		$array = $this->parseArray($array);

		return $array;
	}

	public function all(): array {
		$data = [];
		$class = new ReflectionClass(static::class);
		$properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

		foreach ($properties as $reflectionProperty) {
			// Skip static properties
			if ($reflectionProperty->isStatic()) {
				continue;
			}

			$data[$reflectionProperty->getName()] = $reflectionProperty->getValue($this);
		}

		return $data;
	}

	protected function parseArray(array $array): array {
		foreach ($array as $key => $value) {
			if ($value instanceof DataTransferObject || $value instanceof DataTransferObjectCollection) {
				$array[$key] = $value->toArray();

				continue;
			}

			if (!is_array($value)) {
				continue;
			}

			$array[$key] = $this->parseArray($value);
		}

		return $array;
	}
}