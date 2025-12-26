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
 * @file   DocblockFieldValidator.php
 * @date   29/08/2020 04.26
 */

namespace App\Libraries\DTO;

use Illuminate\Support\Str;


class DocblockFieldValidator extends FieldValidator {
	public const DOCBLOCK_REGEX = '/@var ((?:(?:[\w?|\\\\<>])+(?:\[])?)+)/';

	public function __construct(string $definition, bool $hasDefaultValue = false) {
		preg_match(DocblockFieldValidator::DOCBLOCK_REGEX, $definition, $matches);

		$definition = $matches[1] ?? '';

		$this->hasTypeDeclaration = $definition !== '';
		$this->hasDefaultValue = $hasDefaultValue;
		$this->isNullable = $this->resolveNullable($definition);
		$this->isMixed = $this->resolveIsMixed($definition);
		$this->isMixedArray = $this->resolveIsMixedArray($definition);
		$this->allowedTypes = $this->resolveAllowedTypes($definition);
		$this->allowedArrayTypes = $this->resolveAllowedArrayTypes($definition);
	}

	private function resolveNullable(string $definition): bool {
		if (!$definition) {
			return true;
		}

		if (Str::contains($definition, ['mixed', 'null', '?'])) {
			return true;
		}

		return false;
	}

	private function resolveIsMixed(string $definition): bool {
		return Str::contains($definition, ['mixed']);
	}

	private function resolveIsMixedArray(string $definition): bool {
		$types = $this->normaliseTypes(...explode('|', $definition));

		foreach ($types as $type) {
			if (in_array($type, ['iterable', 'array'])) {
				return true;
			}
		}

		return false;
	}

	private function normaliseTypes(?string ...$types): array {
		// @formatter:off
		return array_filter(array_map(function (?string $type) {
			return self::$typeMapping[$type] ?? $type;
		},$types));
		// @formatter:on
	}

	private function resolveAllowedTypes(string $definition): array {
		return $this->normaliseTypes(...explode('|', $definition));
	}

	private function resolveAllowedArrayTypes(string $definition): ?array {
		// @formatter:off
		return $this->normaliseTypes(...array_map(function (string $type) {
			if (!$type) {
				return null;
			}

			if (strpos($type, '[]') !== false) {
				return str_replace('[]', '', $type);
			}

			if (strpos($type, 'iterable<') !== false) {
				return str_replace(['iterable<', '>'], ['', ''], $type);
			}

			return null;
		},explode('|', $definition)));
		// @formatter:on
	}
}