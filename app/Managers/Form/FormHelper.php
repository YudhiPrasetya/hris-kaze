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
 * @file   FormHelper.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Managers\Form;

use App\Events\AfterCollectingFieldRules;
use App\Managers\Form\Fields\FormField;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Translation\Translator;
use InvalidArgumentException;


class FormHelper {

	/**
	 * @var array
	 */
	protected static $reservedFieldNames = [
		'save',
	];

	/**
	 * All available field types
	 *
	 * @var array
	 */
	protected static $availableFieldTypes = [
		'text'           => 'InputType',
		'input_group'    => 'InputGroupType',
		'email'          => 'InputType',
		'url'            => 'InputType',
		'tel'            => 'InputType',
		'search'         => 'InputType',
		'password'       => 'InputType',
		'hidden'         => 'InputType',
		'number'         => 'InputType',
		'date'           => 'DateType',
		'datetime'       => 'DateType',
		'file'           => 'FileType',
		'browse'         => 'BrowseType',
		'image'          => 'InputType',
		'color'          => 'InputType',
		'datetime-local' => 'InputType',
		'month'          => 'InputType',
		'range'          => 'InputType',
		'time'           => 'DateType',
		'week'           => 'InputType',
		'select'         => 'SelectType',
		'textarea'       => 'TextareaType',
		'button'         => 'ButtonType',
		'buttongroup'    => 'ButtonGroupType',
		'submit'         => 'ButtonType',
		'reset'          => 'ButtonType',
		'radio'          => 'CheckableType',
		'checkbox'       => 'CheckableType',
		'switch'         => 'SwitchType',
		'choice'         => 'ChoiceType',
		'form'           => 'ChildFormType',
		'entity'         => 'EntityType',
		'collection'     => 'CollectionType',
		'repeated'       => 'RepeatedType',
		'static'         => 'StaticType',
	];

	/**
	 * @var View
	 */
	protected $view;

	/**
	 * @var Translator
	 */
	protected $translator;

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * @var FormBuilder
	 */
	protected $formBuilder;

	/**
	 * Custom types
	 *
	 * @var array
	 */
	private $customTypes = [];

	/**
	 * @param View       $view
	 * @param Translator $translator
	 * @param array      $config
	 */
	public function __construct(View $view, Translator $translator, array $config = []) {
		$this->view = $view;
		$this->translator = $translator;
		$this->config = $config;
		$this->loadCustomTypes();
	}

	/**
	 * Load custom field types from config file.
	 */
	private function loadCustomTypes() {
		$customFields = (array)$this->getConfig('custom_fields');

		if (!empty($customFields)) {
			foreach ($customFields as $fieldName => $fieldClass) {
				$this->addCustomField($fieldName, $fieldClass);
			}
		}
	}

	/**
	 * @param null  $key
	 * @param null  $default
	 * @param array $customConfig
	 *
	 * @return array|\ArrayAccess|mixed
	 */
	public function getConfig($key = null, $default = null, $customConfig = []) {
		$config = array_replace_recursive($this->config, $customConfig);

		if ($key) {
			return Arr::get($config, $key, $default);
		}

		return $config;
	}

	/**
	 * Add custom field.
	 *
	 * @param $name
	 * @param $class
	 *
	 * @return mixed
	 */
	public function addCustomField($name, $class): array {
		if (!$this->hasCustomField($name)) {
			return $this->customTypes[$name] = $class;
		}

		throw new InvalidArgumentException('Custom field [' . $name . '] already exists on this form object.');
	}

	/**
	 * Check if custom field with provided name exists
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function hasCustomField($name): bool {
		return array_key_exists($name, $this->customTypes);
	}

	/**
	 * @return View
	 */
	public function getView(): View {
		return $this->view;
	}

	/**
	 * Merge options array.
	 *
	 * @param array $targetOptions
	 * @param array $sourceOptions
	 *
	 * @return array
	 */
	public function mergeOptions(array $targetOptions, array $sourceOptions): array {
		return array_replace_recursive($targetOptions, $sourceOptions);
	}

	/**
	 * Get proper class for field type.
	 *
	 * @param $type
	 *
	 * @return string
	 */
	public function getFieldType($type): string {
		$types = array_keys(static::$availableFieldTypes);

		if (!$type || trim($type) == '') {
			throw new InvalidArgumentException('Field type must be provided.');
		}

		if ($this->hasCustomField($type)) {
			return $this->customTypes[$type];
		}

		if (!in_array($type, $types)) {
			throw new InvalidArgumentException(
				sprintf(
					'Unsupported field type [%s]. Available types are: %s',
					$type,
					join(', ', array_merge($types, array_keys($this->customTypes)))
				)
			);
		}

		$namespace = __NAMESPACE__ . '\\Fields\\';

		return $namespace . static::$availableFieldTypes[$type];
	}

	/**
	 * Convert array of attributes to html attributes.
	 *
	 * @param $options
	 *
	 * @return string
	 */
	public function prepareAttributes($options): ?string {
		if (!$options) {
			return null;
		}

		$attributes = [];

		foreach ($options as $name => $option) {
			if ($option !== null) {
				$name = is_numeric($name) ? $option : $name;
				$attributes[] = "$name=\"$option\"";
			}
		}

		return implode(' ', $attributes);
	}

	/**
	 * @param object $model
	 *
	 * @return array|object|null
	 */
	public function convertModelToArray($model) {
		if (!$model) {
			return null;
		}

		if ($model instanceof Model) {
			return $model->toArray();
		}

		if ($model instanceof Collection) {
			return $model->all();
		}

		return $model;
	}

	/**
	 * Format the label to the proper format.
	 *
	 * @param $name
	 *
	 * @return string
	 */
	public function formatLabel($name): ?string {
		if (!$name) {
			return null;
		}

		if ($this->translator->has($name)) {
			$translatedName = $this->translator->get($name);

			if (is_string($translatedName)) {
				return $translatedName;
			}
		}

		return ucfirst(str_replace('_', ' ', $name));
	}

	/**
	 * @param FormField $field
	 *
	 * @return RulesParser
	 */
	public function createRulesParser(FormField $field): RulesParser {
		return new RulesParser($field);
	}

	/**
	 * @param FormField[] $fields
	 *
	 * @return \App\Managers\Form\Rules
	 */
	public function mergeFieldsRules($fields): Rules {
		$rules = new Rules([]);

		foreach ($fields as $field) {
			$rules->append($this->getFieldValidationRules($field));
		}

		return $rules;
	}

	/**
	 * @param FormField $field
	 *
	 * @return array
	 */
	public function getFieldValidationRules(FormField $field) {
		$fieldRules = $field->getValidationRules();

		if (is_array($fieldRules)) {
			$fieldRules = Rules::fromArray($fieldRules)->setFieldName($field->getNameKey());
		}

		$formBuilder = $field->getParent()->getFormBuilder();
		$formBuilder->fireEvent(new AfterCollectingFieldRules($field, $fieldRules));

		return $fieldRules;
	}

	/**
	 * @param array $fields
	 *
	 * @return array
	 */
	public function mergeAttributes(array $fields): array {
		$attributes = [];

		foreach ($fields as $field) {
			$attributes = array_merge($attributes, $field->getAllAttributes());
		}

		return $attributes;
	}

	/**
	 * Alter a form's values recursively according to its fields.
	 *
	 * @param Form  $form
	 * @param array $values
	 *
	 * @return void
	 */
	public function alterFieldValues(Form $form, array &$values) {
		// Alter the form's child forms recursively
		foreach ($form->getFields() as $name => $field) {
			if (method_exists($field, 'alterFieldValues')) {
				$fullName = $this->transformToDotSyntax($name);

				$subValues = (array)Arr::get($values, $fullName);
				$field->alterFieldValues($subValues);
				Arr::set($values, $fullName, $subValues);
			}
		}

		// Alter the form itself
		$form->alterFieldValues($values);
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public function transformToDotSyntax($string): string {
		if ($string == null) {
			return '';
		}
		return str_replace(['.', '[]', '[', ']'], ['_', '', '.', ''], $string);
	}

	/**
	 * Alter a form's validity recursively, and add messages with nested form prefix.
	 *
	 * @param \App\Managers\Form\Form $form
	 * @param \App\Managers\Form\Form $mainForm
	 * @param                         $isValid
	 *
	 * @return void
	 */
	public function alterValid(Form $form, Form $mainForm, &$isValid) {
		// Alter the form itself
		$messages = $form->alterValid($mainForm, $isValid);

		// Add messages to the existing Bag
		if ($messages) {
			$messageBag = $mainForm->getValidator()->getMessageBag();
			$this->appendMessagesWithPrefix($messageBag, $form->getName(), $messages);
		}

		// Alter the form's child forms recursively
		foreach ($form->getFields() as $name => $field) {
			if (method_exists($field, 'alterValid')) {
				$field->alterValid($mainForm, $isValid);
			}
		}
	}

	/**
	 * Add unprefixed messages with prefix to a MessageBag.
	 *
	 * @param \Illuminate\Contracts\Support\MessageBag $messageBag
	 * @param                                          $prefix
	 * @param array                                    $keyedMessages
	 *
	 * @return void
	 */
	public function appendMessagesWithPrefix(MessageBag $messageBag, $prefix, array $keyedMessages) {
		foreach ($keyedMessages as $key => $messages) {
			if ($prefix) {
				$key = $this->transformToDotSyntax($prefix . '[' . $key . ']');
			}

			foreach ((array)$messages as $message) {
				$messageBag->add($key, $message);
			}
		}
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public function transformToBracketSyntax($string): string {
		$name = explode('.', $string);
		if ($name && count($name) == 1) {
			return $name[0];
		}

		$first = array_shift($name);

		return $first . '[' . implode('][', $name) . ']';
	}

	/**
	 * @return Translator
	 */
	public function getTranslator(): Translator {
		return $this->translator;
	}

	/**
	 * Check if field name is valid and not reserved.
	 *
	 * @param string $name
	 * @param string $className
	 *
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function checkFieldName($name, $className): bool {
		if (!$name || trim($name) == '') {
			throw new InvalidArgumentException(
				"Please provide valid field name for class [{$className}]"
			);
		}

		if (in_array($name, static::$reservedFieldNames)) {
			throw new InvalidArgumentException(
				"Field name [{$name}] in form [{$className}] is a reserved word. Please use a different field name." .
				"\nList of all reserved words: " . join(', ', static::$reservedFieldNames)
			);
		}

		return true;
	}
}
