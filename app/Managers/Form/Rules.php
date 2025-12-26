<?php

namespace App\Managers\Form;

use InvalidArgumentException;


class Rules {
	/**
	 * @var string|null
	 */
	protected $fieldName;

	/**
	 * @var array
	 */
	protected $rules;

	/**
	 * @var array
	 */
	protected $attributes;

	/**
	 * @var array
	 */
	protected $messages;

	/**
	 * @param array $rules
	 * @param array $attributes
	 * @param array $messages
	 */
	public function __construct(array $rules, array $attributes = [], array $messages = []) {
		$this->rules = $rules;
		$this->attributes = $attributes;
		$this->messages = $messages;
	}

	/**
	 * @param string $name
	 *
	 * @return \App\Managers\Form\Rules
	 */
	public function setFieldName(string $name): self {
		$this->fieldName = $name;

		return $this;
	}

	/**
	 * @param string      $rule
	 * @param string|null $fieldName
	 */
	public function addFieldRule(string $rule, string $fieldName = null) {
		$rules = $this->getFieldRules($fieldName);
		$rules[] = $rule;
		$this->setFieldRules($rules, $fieldName);
	}

	/**
	 * @param string|null $fieldName
	 *
	 * @return array|mixed
	 */
	public function getFieldRules(string $fieldName = null) {
		$fieldName = $this->ensureFieldName($fieldName);

		$rules = $this->rules;

		return isset($rules[$fieldName]) ? $rules[$fieldName] : [];
	}

	/**
	 * @param string $fieldName
	 *
	 * @return string|null
	 */
	protected function ensureFieldName(string $fieldName): string {
		if (!$fieldName) {
			if (!$this->fieldName) {
				throw new InvalidArgumentException("Field functions on non-field Rules need explicit field name");
			}

			$fieldName = $this->fieldName;
		}

		return $fieldName;
	}

	/**
	 * @param array       $rules
	 * @param string|null $fieldName
	 */
	public function setFieldRules(array $rules, string $fieldName = null) {
		$fieldName = $this->ensureFieldName($fieldName);
		$this->rules[$fieldName] = $rules;
	}

	/**
	 * @param mixed|array $rules
	 *
	 * @return $this
	 */
	public function append($rules): self {
		if (is_array($rules)) {
			$rules = static::fromArray($rules);
		}

		$this->rules = array_replace_recursive($this->rules, $rules->getRules());
		$this->attributes = array_replace_recursive($this->attributes, $rules->getAttributes());
		$this->messages = array_replace_recursive($this->messages, $rules->getMessages());

		return $this;
	}

	/**
	 * @param array[] $rules
	 *
	 * @return static
	 */
	static public function fromArray(array $rules): Rules {
		if (!$rules) {
			return new static([]);
		}

		$rules += [
			'rules'          => [],
			'attributes'     => [],
			'error_messages' => [],
		];

		return new static($rules['rules'], $rules['attributes'], $rules['error_messages']);
	}

	/**
	 * @return array
	 */
	public function getRules(): array {
		return $this->rules;
	}

	/**
	 * @return array
	 */
	public function getAttributes(): array {
		return $this->attributes;
	}

	/**
	 * @return array
	 */
	public function getMessages(): array {
		return $this->messages;
	}

}
