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
 * @file   FieldValidator.php
 * @date   29/08/2020 04.25
 */

namespace App\Libraries\DTO;

use ReflectionProperty;


abstract class FieldValidator {
	protected static array $typeMapping = [
		'int'   => 'integer',
		'bool'  => 'boolean',
		'float' => 'double',
	];

	public bool $isNullable;

	public bool $isMixed;

	public bool $isMixedArray;

	public bool $hasDefaultValue;

	public array $allowedTypes = [];

	public array $allowedArrayTypes = [];

	protected bool $hasTypeDeclaration;

	public static function fromReflection(ReflectionProperty $property): FieldValidator {
		$docDefinition = null;

		if ($property->getDocComment()) {
			preg_match(DocblockFieldValidator::DOCBLOCK_REGEX, $property->getDocComment(), $matches);
			$docDefinition = $matches[0] ?? null;
		}

		if ($docDefinition) {
			return new DocblockFieldValidator($docDefinition, $property->isDefault());
		}

		return new PropertyFieldValidator($property);
	}

	public function isValidType($value): bool {
		if (!$this->hasTypeDeclaration) {
			return true;
		}

		if ($this->isMixed) {
			return true;
		}

		if (is_iterable($value) && $this->isMixedArray) {
			return true;
		}

		if ($this->isNullable && $value === null) {
			return true;
		}

		if (is_iterable($value)) {
			foreach ($this->allowedArrayTypes as $type) {
				$isValid = $this->assertValidArrayTypes($type, $value);

				if ($isValid) {
					return true;
				}
			}
		}

		foreach ($this->allowedTypes as $type) {
			$isValidType = $this->assertValidType($type, $value);

			if ($isValidType) {
				return true;
			}
		}

		return false;
	}

	private function assertValidArrayTypes(string $type, $collection): bool {
		foreach ($collection as $value) {
			if (!$this->assertValidType($type, $value)) {
				return false;
			}
		}

		return true;
	}

	private function assertValidType(string $type, $value): bool {
		return $value instanceof $type || gettype($value) === $type;
	}
}