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
 * @file   EnumBase.php
 * @date   2020-10-29 5:31:13
 */

namespace App\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;


/**
 * Class EnumBase
 *
 * @package App\Enums
 */
abstract class EnumBase extends Enum {
	/**
	 * @var array
	 */
	protected array $formats;

	/**
	 * @var string
	 */
	private ?string $component;

	/**
	 * @var array
	 */
	private array $parameters;

	/**
	 * @param string $method
	 * @param mixed  $parameters
	 *
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters) {
		$ret = parent::__callStatic($method, $parameters);
		if ($ret instanceof EnumBase) return $ret->_init(...$parameters);

		return $ret;
	}

	/**
	 * @param mixed ...$parameters
	 *
	 * @return $this
	 */
	private function _init(...$parameters): self {
		$this->component = null;
		$this->parameters = array_remove_empty($parameters);
		$this->parameters = collect($this->parameters)->map(function($item, $key) {
			if (is_double($item)) return doubleval($item);
			if (is_float($item)) return floatval($item);
			if (is_numeric($item) || is_int($item)) return intval($item);

			return $item;
		})->toArray();

		$this->formats = [];
		$this->__init(...$parameters);

		return $this;
	}

	/**
	 * @param mixed ...$parameters
	 *
	 * @return mixed
	 */
	abstract protected function __init(...$parameters);

	public function __call($method, $parameters) {
		$ret = parent::__call($method, $parameters);
		if ($ret instanceof EnumBase) return $ret->_init(...$parameters);

		return $ret;
	}

	public function __get($name): string {
		$this->setComponent($name);
		return $this->__toString();
	}

	/**
	 * @param string $component
	 */
	public function setComponent(string $component) {
		$this->component = $component;
	}

	public function isComponentEmpty(): bool {
		return empty($this->component);
	}

	public function isValueEmpty(): bool {
		return empty($this->value);
	}

	public function isParameterEmpty(): bool {
		return count($this->parameters) == 0;
	}

	public function __toString(): string {
		return $this->formatted();
	}

	private function formatted(): string {
		$formatted = '';
		$self = $this;
		$matchAll = false;
		$match = 0;
		if (!$self->isParameterEmpty()) $match++;
		if (!$self->isComponentEmpty()) $match++;
		if (!$self->isValueEmpty()) $match++;

		collect($this->formats)->each(function($item, $key) use($self, &$matchAll, &$match, &$formatted) {
			if (!$matchAll) {
				list($matches, $combine, $parts) = $this->parseFormat($item);

				if ($combine->get('parts')->count() == $match) {
					$formatted = Str::of($item);
					$pos = 0;
					$currentMatch = 0;

					$parts->each(function($part, $index) use(&$formatted, $self, &$pos, &$currentMatch) {
						$formatted = $part->set($formatted, $this->hasParameters() ? $this->parameters[$pos] : null);

						if (($part->type->isDigit || $part->type->isString) && !$self->isParameterEmpty()) {
							if ($part->isParamMatch()) {
								$pos++;
								$currentMatch++;
							}
						}
						if ($part->type->isComponent && !$self->isComponentEmpty()) $currentMatch++;
						if ($part->type->isValue && !$self->isValueEmpty()) $currentMatch++;
					});

					if ($currentMatch == $match) {
						$matchAll = true;
					}
				}
			}
		});

		return $formatted->replace('--', '')->trim('-');
	}

	private function parseFormat(string $format): ?array {
		$re = '/\{(?<optional>\?{0,1})(?<part>[\w\[0-9\|:\-\]\|]+)\}/mi';
		$ret = preg_match_all($re, $format, $matches);
		$self = $this;

		if ($ret !== false || $ret > 0) {
			$optionals = $matches['optional'];
			$parts = $matches['part'];
			$raws = $matches[0];
			$pos = 0;

			return [
				$ret,
				collect(['parts' => collect($parts), 'optionals' => collect($optionals)]),
				collect($parts)->map(function ($item, $key) use ($self, $optionals, $raws, &$pos, $format) {
					$raw = $raws[$key];
					$item = Str::of($item);
					list($total, $type) = $self->getFormatType($raw);

					$std = new class {
						public int $index;
						/**
						 * @var mixed
						 */
						public $key;
						public ?string $name = null;
						public ?object $type = null;
						/**
						 * @var mixed|null
						 */
						public $parameter = null;
						public ?string $format = null;

						public function isParamMatch(): bool {
							if ($this->type->isDigit && is_numeric($this->parameter)) return true;
							if ($this->type->isString && is_string($this->parameter)) return true;

							return false;
						}

						public function set(string $formatted, $value = null): Stringable {
							if ($this->type->isDigit) {
								$accepted = $this->type->digit->inRange(intval($value));
								$formatted = str_replace($this->type->raw, $accepted ? $value : '', $formatted);
							}
							else if ($this->type->isString) {
								if ($this->type->string->hasSelection()) {
									if ($this->type->string->isAccepted($value)) {
										$formatted = str_replace($this->type->raw, $value, $formatted);
									}
								}
								else {
									$formatted = str_replace($this->type->raw, $value, $formatted);
								}
							}
							else if ($this->type->isComponent) {
								$formatted = str_replace($this->type->raw, $this->type->component, $formatted);
							}
							else if ($this->type->isValue) {
								if ($this->type->value->hasSelection()) {
									if ($this->type->value->isAccepted()) {
										$formatted = str_replace($this->type->raw, $this->type->value->value, $formatted);
									}
									else {
										$formatted = str_replace($this->type->raw, '', $formatted);
									}
								}
								else {
									$formatted = str_replace($this->type->raw, $value, $formatted);
								}
							}

							return Str::of($formatted);
						}
					};

					$std->format = $format;
					$std->index = $key;
					$std->key = $self->key;
					$delimpos = $item->pos(':');
					$std->name = $type->name ?? $item->substr(0, $delimpos !== false ? $delimpos : $item->length());

					if (($type->isDigit || $type->isString) && !$self->isParameterEmpty()) {
						$std->parameter = $self->parameters[$pos];
						$pos++;
					}

					$type->name = $type->name ?? $std->name;
					$type->identifier = $type->identifier ?? $item;
					$type->raw = $type->raw ?? $raws[$key];
					$type->isComponent = $item == 'component';
					$type->isValue = $type->name == 'value';
					$type->isOptional = $type->isOptional ?? $optionals[$key] == '?';

					if ($type->isValue && $delimpos !== false) {
						$type->value->selection = explode('|', $item->substr($delimpos !== false ? $delimpos + 1 : $item->length()));
					}

					if ($type->isValue && $delimpos === false) {
						$type->value->selection[] = $this->value;
					}

					$std->type = $type;

					return $std;
				}),
			];
		}

		return [0, collect(['parts' => collect([]), 'optionals' => collect([])]), collect([])];
	}

	private function getFormatType(string $raw) {
		$re = '/\{(\?)?([ds]):?(\[(?:(\[??[^\[].*)?\]))?\}/mi';
		//debug(Str::of($raw)->matchAll($re));
		$ret = preg_match_all($re, (string) $raw, $matches, PREG_PATTERN_ORDER);
		$std = new class {
			public ?string $name = null;
			public ?string $identifier = null;
			public ?string $raw = null;

			public bool $isDigit = false;
			public bool $isString = false;
			public bool $isComponent = false;
			public bool $isValue = false;
			public ?bool $isOptional = null;

			// Separate the type
			public ?object $digit = null;
			public ?object $string = null;
			public ?object $value = null;
			public ?object $component = null;
		};

		$std->value = new class {
			/**
			 * @var mixed|null
			 */
			public $value = null;
			public ?string $key = null;
			public array $selection = [];

			public function hasSelection(): bool { return count($this->selection()) > 0; }
			public function isAccepted(): bool {
				return $this->hasSelection() ? in_array($this->value, $this->selection()) : false;
			}
			public function selection(): array { return $this->selection;}
		};
		$std->value->key = $this->key;
		$std->value->value = $this->value;

		$std->component = new class {
			public ?string $value = null;

			public function __toString(): string { return $this->value; }
		};
		$std->component->value = $this->component;

		if ($ret !== false && count($matches[0])) {
			$std->name = $matches[2][0] == 'd' ? 'digit' : 'string';
			$std->identifier = $matches[2][0];
			$std->raw = $matches[0][0];
			$std->isDigit = $matches[2][0] == 'd';
			$std->isString = $matches[2][0] == 's';
			$std->isOptional = $matches[1][0] == '?';

			if ($std->isString) {
				$std->string = new class {
					public array $selection = [];

					public function hasSelection(): bool { return count($this->selection) > 0; }
					public function isAccepted(?string $value): bool {
						return $this->hasSelection() ? in_array($value, $this->selection) : false;
					}
					public function selection(): array { return $this->selection; }
				};
				$std->string->selection = explode('|', $matches[4][0]);
			}

			if ($std->isDigit) {
				$std->digit = new class {
					public int $from = 0;
					public int $until = 0;

					public function inRange(int $value): bool {
						return $value >= $this->from || $value <= $this->until;
					}
				};
				$range = explode(':', $matches[3][0]);
				$std->digit->from = intval($range[0]);
				$std->digit->until = intval($range[1]);
			}
		}

		//return [$ret, $ret !== false && $ret > 0 ? [intval($matches[2][0]), intval($matches[3][0])] : [0, 0]];
		return [$ret, $std];
	}

	private function hasParameters(): bool {
		return count($this->parameters);
	}

	private function trim(?string $str): string {
		return trim(str_replace('--', '', $str), '-');
	}

	protected function setFormat(...$formats) {
		$this->formats = array_remove_empty($formats);
	}
}
