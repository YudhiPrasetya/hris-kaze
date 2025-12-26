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
 * @file   ParentType.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Managers\Form\Fields;

use App\Managers\Form\Form;
use App\Managers\Form\Rules;
use Illuminate\Support\Arr;


abstract class ParentType extends FormField {

	/**
	 * @var FormField[]
	 */
	protected array $children;

	/**
	 * ParentType constructor.
	 *
	 * @param                         $name
	 * @param                         $type
	 * @param \App\Managers\Form\Form $parent
	 * @param array                   $options
	 *
	 * @throws \App\Exceptions\FilterAlreadyBindedException
	 */
	public function __construct($name, $type, Form $parent, array $options = []) {
		$this->children = [];

		parent::__construct($name, $type, $parent, $options + ['copy_options_to_children' => true]);

		// If there is default value provided and  setValue was not triggered
		// in the parent call, make sure we generate child elements.
		if ($this->hasDefault) {
			$this->createChildren();
		}

		$this->checkIfFileType();
	}

	/**
	 * Populate children array.
	 *
	 * @return mixed
	 */
	abstract protected function createChildren();

	/**
	 * Check if field has type property and if it's file add enctype/multipart to form.
	 *
	 * @return void
	 */
	protected function checkIfFileType() {
		if (in_array($this->getOption('type'), ['file', 'browse'])) {
			$this->parent->setFormOption('files', true);
		}
	}

	/**
	 * @param mixed $val
	 *
	 * @return \App\Managers\Form\Fields\ParentType
	 */
	public function setValue($val): self {
		parent::setValue($val);
		$this->createChildren();

		return $this;
	}

	/**
	 * @param array $options
	 * @param bool  $showLabel
	 * @param bool  $showField
	 * @param bool  $showError
	 *
	 * @return string
	 */
	public function render(array $options = [], bool $showLabel = true, bool $showField = true, bool $showError = true): string {
		$options['children'] = $this->children;

		return parent::render($options, $showLabel, $showField, $showError);
	}

	/**
	 * Get all children of the choice field.
	 *
	 * @return mixed
	 */
	public function getChildren() {
		return $this->children;
	}

	/**
	 * Remove child.
	 *
	 * @param string $key
	 *
	 * @return $this
	 */
	public function removeChild(string $key) {
		if ($this->getChild($key)) {
			unset($this->children[$key]);
		}

		return $this;
	}

	/**
	 * Get a child of the choice field.
	 *
	 * @param string $key
	 *
	 * @return array|\ArrayAccess|mixed
	 */
	public function getChild(string $key) {
		return Arr::get($this->children, $key);
	}

	/**
	 * @inheritdoc
	 */
	public function setOption(string $name, $value) {
		parent::setOption($name, $value);

		if ($this->options['copy_options_to_children']) {
			foreach ((array)$this->children as $key => $child) {
				$this->children[$key]->setOption($name, $value);
			}
		}

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function setOptions($options): self {
		parent::setOptions($options);

		if ($this->options['copy_options_to_children']) {
			foreach ((array)$this->children as $key => $child) {
				$this->children[$key]->setOptions($options);
			}
		}

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function isRendered(): bool {
		foreach ((array)$this->children as $key => $child) {
			if ($child->isRendered()) {
				return true;
			}
		}

		return parent::isRendered();
	}

	/**
	 * Get child dynamically.
	 *
	 * @param string $name
	 *
	 * @return FormField
	 */
	public function __get(string $name) {
		return $this->getChild($name);
	}

	public function __clone() {
		foreach ((array)$this->children as $key => $child) {
			$this->children[$key] = clone $child;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function disable(): self {
		foreach ($this->children as $field) {
			$field->disable();
		}

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function enable(): self {
		foreach ($this->children as $field) {
			$field->enable();
		}

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function getValidationRules(): Rules {
		$rules = parent::getValidationRules();
		$childrenRules = $this->formHelper->mergeFieldsRules($this->children);

		return $rules->append($childrenRules);
	}
}
