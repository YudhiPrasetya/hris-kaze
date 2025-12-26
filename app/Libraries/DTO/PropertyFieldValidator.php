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
 * @file   PropertyFieldValidator.php
 * @date   29/08/2020 04.29
 */

namespace App\Libraries\DTO;

use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;


class PropertyFieldValidator extends FieldValidator {
	public function __construct(ReflectionProperty $property) {
		$this->hasTypeDeclaration = $property->hasType();
		$this->hasDefaultValue = $property->isDefault();
		$this->isNullable = $this->resolveAllowsNull($property);
		$this->isMixed = $this->resolveIsMixed($property);
		$this->isMixedArray = $this->resolveIsMixedArray($property);
		$this->allowedTypes = $this->resolveAllowedTypes($property);
		$this->allowedArrayTypes = [];
	}

	private function resolveAllowsNull(ReflectionProperty $property): bool {
		if (!$property->getType()) {
			return true;
		}

		return $property->getType()->allowsNull();
	}

	private function resolveIsMixed(ReflectionProperty $property): bool {
		return $property->hasType() === false;
	}

	private function resolveIsMixedArray(ReflectionProperty $property): bool {
		$reflectionType = $property->getType();

		if (!$reflectionType instanceof ReflectionNamedType) {
			return false;
		}

		// We cast to array to support future union types in PHP 8
		$types = [$reflectionType];

		foreach ($types as $type) {
			if (in_array($type->getName(), ['iterable', 'array'])) {
				return true;
			}
		}

		return false;
	}

	private function resolveAllowedTypes(ReflectionProperty $property): array {
		// We cast to array to support future union types in PHP 8
		$types = [$property->getType()];

		return $this->normaliseTypes(...$types);
	}

	private function normaliseTypes(?ReflectionType ...$types): array {
		return array_filter(
			array_map(
				function (?ReflectionType $type) {
					if ($type instanceof ReflectionNamedType) {
						$type = $type->getName();
					}

					return self::$typeMapping[$type] ?? $type;
				},
				$types
			)
		);
	}
}