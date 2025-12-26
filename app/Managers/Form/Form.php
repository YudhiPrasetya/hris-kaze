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
 * @file   Form.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Managers\Form;

use App\Events\AfterFieldCreation;
use App\Events\AfterFormValidation;
use App\Events\BeforeFormValidation;
use App\Managers\Form\Fields\FormField;
use App\Managers\Form\Filters\FilterResolver;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;


class Form {
	/**
	 * All fields that are added.
	 *
	 * @var array
	 */
	protected array $fields = [];

	/**
	 * Model to use.
	 *
	 * @var mixed
	 */
	protected $model = [];

	/**
	 * @var EventDispatcher
	 */
	protected EventDispatcher $eventDispatcher;

	/**
	 * @var FormHelper
	 */
	protected FormHelper $formHelper;

	/**
	 * Form options.
	 *
	 * @var array
	 */
	protected array $formOptions = [
		'method' => 'GET',
		'url'    => null,
		'attr'   => [],
	];

	/**
	 * Form specific configuration.
	 *
	 * @var array
	 */
	protected array $formConfig = [];

	/**
	 * Additional data which can be used to build fields.
	 *
	 * @var array
	 */
	protected array $data = [];

	/**
	 * Wether errors for each field should be shown when calling form($form) or form_rest($form).
	 *
	 * @var bool
	 */
	protected bool $showFieldErrors = true;

	/**
	 * Enable html5 validation.
	 *
	 * @var bool
	 */
	protected bool $clientValidationEnabled = true;

	/**
	 * Name of the parent form if any.
	 *
	 * @var string|null
	 */
	protected ?string $name = null;

	/**
	 * @var FormBuilder
	 */
	protected FormBuilder $formBuilder;

	/**
	 * @var ValidatorFactory
	 */
	protected ValidatorFactory $validatorFactory;

	/**
	 * @var ?Validator
	 */
	protected ?Validator $validator = null;

	/**
	 * @var Request
	 */
	protected Request $request;

	/**
	 * List of fields to not render.
	 *
	 * @var array
	 **/
	protected array $exclude = [];

	/**
	 * Wether the form is beign rebuild.
	 *
	 * @var bool
	 */
	protected bool $rebuilding = false;

	/**
	 * @var string
	 */
	protected string $templatePrefix = '';

	/**
	 * @var string
	 */
	protected string $languageName = '';

	/**
	 * @var string
	 */
	protected string $translationTemplate = '';

	/**
	 * To filter and mutate request values or not.
	 *
	 * @var bool
	 */
	protected bool $lockFiltering = false;

	/**
	 * Define the error bag name for the form.
	 *
	 * @var string
	 */
	protected string $errorBag = 'default';

	/**
	 * Add field before another field.
	 *
	 * @param string $name      Name of the field before which new field is added.
	 * @param string $fieldName Field name which will be added.
	 * @param string $type
	 * @param array  $options
	 * @param bool   $modify
	 *
	 * @return $this
	 */
	public function addBefore(string $name, string $fieldName, string $type = 'text', array $options = [],
		bool $modify = false
	): self {
		$offset = array_search($name, array_keys($this->fields));
		$beforeFields = array_slice($this->fields, 0, $offset);
		$afterFields = array_slice($this->fields, $offset);
		$this->fields = $beforeFields;
		$this->add($fieldName, $type, $options, $modify);
		$this->fields += $afterFields;

		return $this;
	}

	/**
	 * Create a new field and add it to the form.
	 *
	 * @param string $name
	 * @param string $type
	 * @param array  $options
	 * @param bool   $modify
	 *
	 * @return $this
	 */
	public function add(string $name, string $type = 'text', array $options = [], bool $modify = false): self {
		$this->formHelper->checkFieldName($name, get_class($this));

		if ($this->rebuilding && !$this->has($name)) {
			return $this;
		}

		$this->addField($this->makeField($name, $type, $options), $modify);

		return $this;
	}

	/**
	 * Check if form has field.
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function has(string $name): bool {
		return array_key_exists($name, $this->fields);
	}

	/**
	 * Add a FormField to the form's fields.
	 *
	 * @param \App\Managers\Form\Fields\FormField $field
	 * @param false                               $modify
	 *
	 * @return $this
	 */
	protected function addField(FormField $field, bool $modify = false): self {
		if (!$modify && !$this->rebuilding) {
			$this->preventDuplicate($field->getRealName());
		}

		if (in_array($field->getType(), ['file', 'browse'])) {
			$this->formOptions['files'] = true;
		}

		$this->fields[$field->getRealName()] = $field;

		return $this;
	}

	/**
	 * Prevent adding fields with same name.
	 *
	 * @param string $name
	 *
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	protected function preventDuplicate(string $name) {
		if ($this->has($name)) {
			throw new InvalidArgumentException('Field [' . $name . '] already exists in the form ' . get_class($this));
		}
	}

	/**
	 * Create the FormField object.
	 *
	 * @param string $name
	 * @param string $type
	 * @param array  $options
	 *
	 * @return FormField
	 */
	protected function makeField(string $name, string $type = 'text', array $options = []): FormField {
		$this->setupFieldOptions($name, $options);

		$fieldName = $this->getFieldName($name);
		$fieldType = $this->getFieldType($type);
		$field = new $fieldType($fieldName, $type, $this, $options);

		$this->eventDispatcher->dispatch(new AfterFieldCreation($this, $field));

		return $field;
	}

	/**
	 * Set up options on single field depending on form options.
	 *
	 * @param string $name
	 * @param        $options
	 */
	protected function setupFieldOptions(string $name, array &$options) {
		$options['real_name'] = $name;
	}

	/**
	 * If form is named form, modify names to be contained in single key (parent[child_field_name]).
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	protected function getFieldName(string $name): string {
		$formName = $this->getName();

		if ($formName !== null) {
			if (strpos($formName, '[') !== false || strpos($formName, ']') !== false) {
				return $this->formHelper->transformToBracketSyntax(
					$this->formHelper->transformToDotSyntax(
						$formName . '[' . $name . ']'
					)
				);
			}

			return $formName . '[' . $name . ']';
		}

		return $name;
	}

	/**
	 * Returns the name of the form.
	 *
	 * @return string|null
	 */
	public function getName(): ?string {
		return $this->name;
	}

	/**
	 * Set the name of the form.
	 *
	 * @param string $name
	 * @param bool   $rebuild
	 *
	 * @return $this
	 */
	public function setName(string $name, bool $rebuild = true): self {
		$this->name = $name;

		if ($rebuild) {
			$this->rebuildForm();
		}

		return $this;
	}

	/**
	 * Returns and checks the type of the field.
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	protected function getFieldType(string $type): string {
		return $this->formHelper->getFieldType($type);
	}

	/**
	 * Rebuild the form from scratch.
	 *
	 * @return $this
	 */
	public function rebuildForm(): self {
		$this->rebuilding = true;

		// If form is plain, buildForm method is empty, so we need to take
		// existing fields and add them again
		if (get_class($this) === 'App\Managers\Form\Form') {
			foreach ($this->fields as $name => $field) {
				// Remove any temp variables added in previous instance
				$options = Arr::except($field->getOptions(), 'tmp');
				$this->add($name, $field->getType(), $options);
			}
		}
		else {
			$this->buildForm();
		}

		$this->rebuilding = false;

		return $this;
	}

	/**
	 * Build the form.
	 *
	 * @return mixed
	 */
	public function buildForm() {
		return null;
	}

	/**
	 * Add field before another field.
	 *
	 * @param string $name      Name of the field after which new field is added.
	 * @param string $fieldName Field name which will be added.
	 * @param string $type
	 * @param array  $options
	 * @param bool   $modify
	 *
	 * @return $this
	 */
	public function addAfter(string $name, string $fieldName, string $type = 'text', array $options = [],
		bool $modify = false
	): self {
		$offset = array_search($name, array_keys($this->fields));
		$beforeFields = array_slice($this->fields, 0, $offset + 1);
		$afterFields = array_slice($this->fields, $offset + 1);
		$this->fields = $beforeFields;
		$this->add($fieldName, $type, $options, $modify);
		$this->fields += $afterFields;

		return $this;
	}

	/**
	 * Take another form and add it's fields directly to this form.
	 *
	 * @param string $class
	 * @param array  $options
	 * @param bool   $modify
	 *
	 * @return $this
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	public function compose(string $class, array $options = [], bool $modify = false): self {
		$options['class'] = $class;

		// If we pass a ready made form just extract the fields.
		if ($class instanceof Form) {
			$fields = $class->getFields();
		}
		elseif ($class instanceof Fields\ChildFormType) {
			$fields = $class->getForm()->getFields();
		}
		elseif (is_string($class)) {
			// If its a string of a class make it the usual way.
			$options['model'] = $this->model;
			$options['name'] = $this->name;

			$form = $this->formBuilder->create($class, $options);
			$fields = $form->getFields();
		}
		else {
			throw new InvalidArgumentException(
				"[{$class}] is invalid. Please provide either a full class name, Form or ChildFormType"
			);
		}

		foreach ($fields as $field) {
			$this->addField($field, $modify);
		}

		return $this;
	}

	/**
	 * Get all fields.
	 *
	 * @return FormField[]
	 */
	public function getFields() {
		return $this->fields;
	}

	/**
	 * Remove field with specified name from the form.
	 *
	 * @param string|string[] $names
	 *
	 * @return $this
	 */
	public function remove(string $names): self {
		foreach (is_array($names) ? $names : func_get_args() as $name) {
			if ($this->has($name)) {
				unset($this->fields[$name]);
			}
		}

		return $this;
	}

	/**
	 * Take only the given fields from the form.
	 *
	 * @param string|string[] $fieldNames
	 *
	 * @return $this
	 */
	public function only($fieldNames): self {
		$newFields = [];

		foreach (is_array($fieldNames) ? $fieldNames : func_get_args() as $fieldName) {
			$newFields[$fieldName] = $this->getField($fieldName);
		}

		$this->fields = $newFields;

		return $this;
	}

	/**
	 * Get single field instance from form object.
	 *
	 * @param string $name
	 *
	 * @return FormField|null
	 */
	public function getField(string $name): ?FormField {
		if ($this->has($name)) {
			return $this->fields[$name];
		}

		$this->fieldDoesNotExist($name);

		return null;
	}

	/**
	 * Throw an exception indicating a field does not exist on the class.
	 *
	 * @param string $name
	 *
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	protected function fieldDoesNotExist(string $name) {
		throw new InvalidArgumentException('Field [' . $name . '] does not exist in ' . get_class($this));
	}

	/**
	 * Modify existing field. If it doesn't exist, it is added to form.
	 *
	 * @param string $name
	 * @param string $type
	 * @param array  $options
	 * @param bool   $overwriteOptions
	 *
	 * @return Form
	 */
	public function modify(string $name, string $type = 'text', array $options = [],
		bool $overwriteOptions = false
	): Form {
		// If we don't want to overwrite options, we merge them with old options.
		if ($overwriteOptions === false && $this->has($name)) {
			$options = $this->formHelper->mergeOptions(
				$this->getField($name)->getOptions(),
				$options
			);
		}

		return $this->add($name, $type, $options, true);
	}

	/**
	 * Render full form.
	 *
	 * @param array $options
	 * @param bool  $showStart
	 * @param bool  $showFields
	 * @param bool  $showEnd
	 *
	 * @return string
	 */
	public function renderForm(array $options = [], bool $showStart = true, bool $showFields = true, bool $showEnd = true): string {
		return $this->render($options, $this->fields, $showStart, $showFields, $showEnd);
	}

	/**
	 * Render the form.
	 *
	 * @param array       $options
	 * @param FormField[] $fields
	 * @param bool        $showStart
	 * @param bool        $showFields
	 * @param bool        $showEnd
	 *
	 * @return string
	 */
	protected function render(array $options, array $fields, bool $showStart, bool $showFields, bool $showEnd): string {
		$formOptions = $this->buildFormOptionsForFormBuilder($this->formHelper->mergeOptions($this->formOptions, $options));

		$this->setupNamedModel();

		return $this->formHelper->getView()
		                        ->make($this->getTemplate())
		                        ->with(compact('showStart', 'showFields', 'showEnd'))
		                        ->with('formOptions', $formOptions)
		                        ->with('fields', $fields)
		                        ->with('model', $this->getModel())
		                        ->with('exclude', $this->exclude)
		                        ->with('form', $this)
		                        ->render();
	}

	/**
	 * @param $formOptions
	 *
	 * @return array
	 */
	protected function buildFormOptionsForFormBuilder(array $formOptions): array {
		$reserved = ['method', 'url', 'route', 'action', 'files'];
		$formAttributes = Arr::get($formOptions, 'attr', []);

		// move string value to `attr` to maintain backward compatibility
		foreach ($formOptions as $key => $formOption) {
			if (!in_array($formOption, $reserved) && is_string($formOption)) {
				$formAttributes[$key] = $formOption;
			}
		}

		return array_merge(
			$formAttributes,
			Arr::only($formOptions, $reserved)
		);
	}

	/**
	 * Set namespace to model if form is named so the data is bound properly.
	 * Returns true if model is changed, otherwise false.
	 *
	 * @return bool
	 */
	protected function setupNamedModel(): bool {
		if (!$this->getModel() || !$this->getName()) {
			return false;
		}

		$dotName = $this->getNameKey();
		$model = $this->formHelper->convertModelToArray($this->getModel());
		$isCollectionFormModel = (bool)preg_match('/^.*\.\d+$/', $dotName);
		$isCollectionPrototype = strpos($dotName, '__NAME__') !== false;

		if (!Arr::get($model, $dotName) && !$isCollectionFormModel && !$isCollectionPrototype) {
			$newModel = [];
			Arr::set($newModel, $dotName, $model);
			$this->model = $newModel;

			return true;
		}

		return false;
	}

	/**
	 * Get model that is bind to form object.
	 *
	 * @return mixed
	 */
	public function getModel() {
		return $this->model;
	}

	/**
	 * Set model to form object.
	 *
	 * @param mixed $model
	 *
	 * @return $this
	 * @deprecated deprecated since 1.6.31, will be removed in 1.7 - pass model as option when creating a form
	 */
	public function setModel($model): self {
		$this->model = $model;

		$this->rebuildForm();

		return $this;
	}

	/**
	 * Get dot notation key for the form.
	 *
	 * @return string
	 **/
	public function getNameKey(): string {
		return $this->formHelper->transformToDotSyntax($this->name);
	}

	/**
	 * Get template from options if provided, otherwise fallback to config.
	 *
	 * @return mixed
	 */
	protected function getTemplate(): string {
		return $this->getTemplatePrefix() . $this->getFormOption('template', $this->getConfig('form'));
	}

	/**
	 * Get template prefix that is prepended to all template paths.
	 *
	 * @return string
	 */
	public function getTemplatePrefix() {
		if ($this->templatePrefix !== null) {
			return $this->templatePrefix;
		}

		return $this->getConfig('template_prefix');
	}

	/**
	 * Set a template prefix for the form and its fields.
	 *
	 * @param string $prefix
	 *
	 * @return $this
	 */
	public function setTemplatePrefix(string $prefix): self {
		$this->templatePrefix = (string)$prefix;

		return $this;
	}

	/**
	 * Get the passed config key using the custom form config, if any.
	 *
	 * @param string|null $key
	 * @param null        $default
	 *
	 * @return array|\ArrayAccess|mixed
	 */
	public function getConfig(string $key = null, $default = null) {
		return $this->formHelper->getConfig($key, $default, $this->formConfig);
	}

	/**
	 * Get single form option.
	 *
	 * @param      $option
	 * @param null $default
	 *
	 * @return array|\ArrayAccess|mixed
	 */
	public function getFormOption(string $option, $default = null) {
		return Arr::get($this->formOptions, $option, $default);
	}

	/**
	 * Render rest of the form.
	 *
	 * @param bool $showFormEnd
	 * @param bool $showFields
	 *
	 * @return string
	 */
	public function renderRest(bool $showFormEnd = true, bool $showFields = true): string {
		$fields = $this->getUnrenderedFields();

		return $this->render([], $fields, false, $showFields, $showFormEnd);
	}

	/**
	 * Get all fields that are not rendered.
	 *
	 * @return array
	 */
	protected function getUnrenderedFields(): array {
		$unrenderedFields = [];

		foreach ($this->fields as $field) {
			if (!$field->isRendered()) {
				$unrenderedFields[] = $field;
				continue;
			}
		}

		return $unrenderedFields;
	}

	/**
	 * Renders the rest of the form up until the specified field name.
	 *
	 * @param string $field_name
	 * @param bool   $showFormEnd
	 * @param bool   $showFields
	 *
	 * @return string
	 */
	public function renderUntil(string $field_name, bool $showFormEnd = true, bool $showFields = true): string {
		if (!$this->has($field_name)) {
			$this->fieldDoesNotExist($field_name);
		}

		$fields = $this->getUnrenderedFields();

		$i = 1;
		foreach ($fields as $key => $value) {
			if ($value->getRealName() == $field_name) {
				break;
			}
			$i++;
		}

		$fields = array_slice($fields, 0, $i, true);

		return $this->render([], $fields, false, $showFields, $showFormEnd);
	}

	/**
	 * Get all form options.
	 *
	 * @return array
	 */
	public function getFormOptions(): array {
		return $this->formOptions;
	}

	/**
	 * Set form options.
	 *
	 * @param array $formOptions
	 *
	 * @return $this
	 */
	public function setFormOptions(array $formOptions): self {
		$this->formOptions = $this->formHelper->mergeOptions($this->formOptions, $formOptions);
		$this->checkIfNamedForm();
		$this->pullFromOptions('data', 'addData');
		$this->pullFromOptions('model', 'setupModel');
		$this->pullFromOptions('errors_enabled', 'setErrorsEnabled');
		$this->pullFromOptions('client_validation', 'setClientValidationEnabled');
		$this->pullFromOptions('template_prefix', 'setTemplatePrefix');
		$this->pullFromOptions('language_name', 'setLanguageName');
		$this->pullFromOptions('translation_template', 'setTranslationTemplate');

		return $this;
	}

	/**
	 * Check if form is named form.
	 *
	 * @return void
	 */
	protected function checkIfNamedForm() {
		if ($this->getFormOption('name')) {
			$this->name = Arr::pull($this->formOptions, 'name', $this->name);
		}
	}

	/**
	 * Get an option from provided options and call method with that value.
	 *
	 * @param string $name
	 * @param string $method
	 */
	protected function pullFromOptions(string $name, string $method) {
		if (Arr::get($this->formOptions, $name) !== null) {
			$this->{$method}(Arr::pull($this->formOptions, $name));
		}
	}

	/**
	 * Set single form option on form.
	 *
	 * @param string $option
	 * @param mixed  $value
	 *
	 * @return $this
	 */
	public function setFormOption(string $option, $value): self {
		$this->formOptions[$option] = $value;

		return $this;
	}

	/**
	 * Get form http method.
	 *
	 * @return string
	 */
	public function getMethod(): string {
		return $this->formOptions['method'];
	}

	/**
	 * Set form http method.
	 *
	 * @param string $method
	 *
	 * @return $this
	 */
	public function setMethod(string $method): self {
		$this->formOptions['method'] = $method;

		return $this;
	}

	/**
	 * Get form action url.
	 *
	 * @return string
	 */
	public function getUrl(): string {
		return $this->formOptions['url'];
	}

	/**
	 * Set form action url.
	 *
	 * @param string $url
	 *
	 * @return $this
	 */
	public function setUrl(string $url): self {
		$this->formOptions['url'] = $url;

		return $this;
	}

	/**
	 * Get field dynamically.
	 *
	 * @param string $name
	 *
	 * @return FormField|null
	 */
	public function __get(string $name): ?FormField {
		if ($this->has($name)) {
			return $this->getField($name);
		}

		return null;
	}

	/**
	 * Check if field exists when fetched using magic methods.
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function __isset(string $name): bool {
		return $this->has($name);
	}

	/**
	 * Set the Event Dispatcher to fire Laravel events.
	 *
	 * @param EventDispatcher $eventDispatcher
	 *
	 * @return $this
	 */
	public function setEventDispatcher(EventDispatcher $eventDispatcher): self {
		$this->eventDispatcher = $eventDispatcher;

		return $this;
	}

	/**
	 * Get form helper.
	 *
	 * @return FormHelper
	 */
	public function getFormHelper(): FormHelper {
		return $this->formHelper;
	}

	/**
	 * Set the form helper only on first instantiation.
	 *
	 * @param FormHelper $formHelper
	 *
	 * @return $this
	 */
	public function setFormHelper(FormHelper $formHelper): self {
		$this->formHelper = $formHelper;

		return $this;
	}

	/**
	 * Add custom field.
	 *
	 * @param $name
	 * @param $class
	 *
	 * @return $this
	 */
	public function addCustomField(string $name, string $class): self {
		if ($this->rebuilding && $this->formHelper->hasCustomField($name)) {
			return $this;
		}

		$this->formHelper->addCustomField($name, $class);

		return $this;
	}

	/**
	 * Returns wether form errors should be shown under every field.
	 *
	 * @return bool
	 */
	public function haveErrorsEnabled(): bool {
		return $this->showFieldErrors;
	}

	/**
	 * Enable or disable showing errors under fields
	 *
	 * @param bool $enabled
	 *
	 * @return $this
	 */
	public function setErrorsEnabled(bool $enabled): self {
		$this->showFieldErrors = (bool)$enabled;

		return $this;
	}

	/**
	 * Is client validation enabled?
	 *
	 * @return bool
	 */
	public function clientValidationEnabled(): bool {
		return $this->clientValidationEnabled;
	}

	/**
	 * Enable/disable client validation.
	 *
	 * @param bool $enable
	 *
	 * @return $this
	 */
	public function setClientValidationEnabled(bool $enable): self {
		$this->clientValidationEnabled = (bool)$enable;

		return $this;
	}

	/**
	 * Get single additional data.
	 *
	 * @param string|null $name
	 * @param null        $default
	 *
	 * @return array|\ArrayAccess|mixed
	 */
	public function getData(string $name = null, $default = null) {
		if (is_null($name)) {
			return $this->data;
		}

		return Arr::get($this->data, $name, $default);
	}

	/**
	 * Add any additional data that field needs (ex. array of choices).
	 *
	 * @param string $name
	 * @param mixed  $data
	 *
	 * @deprecated deprecated since 1.6.20, will be removed in 1.7 - use 3rd param on create, or 2nd on plain method to
	 *             pass data will be switched to protected in 1.7.
	 */
	public function setData(string $name, $data) {
		$this->data[$name] = $data;
	}

	/**
	 * Add multiple peices of data at once.
	 *
	 * @param $data
	 *
	 * @return $this
	 * @deprecated deprecated since 1.6.12, will be removed in 1.7 - use 3rd param on create, or 2nd on plain method to
	 *             pass data will be switched to protected in 1.7.
	 */
	public function addData(array $data): self {
		foreach ($data as $key => $value) {
			$this->setData($key, $value);
		}

		return $this;
	}

	/**
	 * Get the language name.
	 *
	 * @return string
	 */
	public function getLanguageName(): ?string {
		return $this->languageName;
	}

	/**
	 * Set a language name, used as prefix for translated strings.
	 *
	 * @param string $prefix
	 *
	 * @return $this
	 */
	public function setLanguageName(string $prefix): self {
		$this->languageName = (string)$prefix;

		return $this;
	}

	/**
	 * Get the translation template.
	 *
	 * @return string
	 */
	public function getTranslationTemplate(): ?string {
		return $this->translationTemplate;
	}

	/**
	 * Set a translation template, used to determine labels for fields.
	 *
	 * @param string $template
	 *
	 * @return $this
	 */
	public function setTranslationTemplate(string $template): self {
		$this->translationTemplate = (string)$template;

		return $this;
	}

	/**
	 * Returns the instance of the FormBuilder.
	 *
	 * @return FormBuilder
	 */
	public function getFormBuilder(): FormBuilder {
		return $this->formBuilder;
	}

	/**
	 * Set form builder instance on helper so we can use it later.
	 *
	 * @param FormBuilder $formBuilder
	 *
	 * @return $this
	 */
	public function setFormBuilder(FormBuilder $formBuilder): self {
		$this->formBuilder = $formBuilder;

		return $this;
	}

	/**
	 * Returns the validator instance.
	 *
	 * @return Validator
	 */
	public function getValidator() {
		return $this->validator;
	}

	/**
	 * Set the Validator instance on this so we can use it later.
	 *
	 * @param ValidatorFactory $validator
	 *
	 * @return $this
	 */
	public function setValidator(ValidatorFactory $validator): self {
		$this->validatorFactory = $validator;

		return $this;
	}

	/**
	 * Exclude some fields from rendering.
	 *
	 * @param array $fields
	 *
	 * @return $this
	 */
	public function exclude(array $fields): self {
		$this->exclude = array_merge($this->exclude, $fields);

		return $this;
	}

	/**
	 * Disable all fields in a form.
	 */
	public function disableFields() {
		foreach ($this->fields as $field) {
			$field->disable();
		}
	}

	/**
	 * Enable all fields in a form.
	 */
	public function enableFields() {
		foreach ($this->fields as $field) {
			$field->enable();
		}
	}

	/**
	 * Get validation rules for the form.
	 *
	 * @param array $overrideRules
	 *
	 * @return array
	 */
	public function getRules(array $overrideRules = []): array {
		$fieldRules = $this->formHelper->mergeFieldsRules($this->fields);

		return array_merge($fieldRules->getRules(), $overrideRules);
	}

	/**
	 * Redirects to a destination when form is invalid.
	 *
	 * @param string|null $destination
	 */
	public function redirectIfNotValid(string $destination = null) {
		if (!$this->isValid()) {
			$response = redirect($destination);

			if (is_null($destination)) {
				$response = $response->back();
			}

			$response = $response->withErrors($this->getErrors(), $this->getErrorBag())->withInput();

			throw new HttpResponseException($response);
		}
	}

	/**
	 * Check if the form is valid.
	 *
	 * @return bool
	 */
	public function isValid() {
		if (!$this->validator) {
			$this->validate();
		}

		$isValid = !$this->validator->fails();
		$this->formHelper->alterValid($this, $this, $isValid);
		$this->eventDispatcher->dispatch(new AfterFormValidation($this, $this->validator, $isValid));

		return $isValid;
	}

	public function isSubmitted(): bool {
		$methods = ['POST', 'PUT', 'DELETE', 'PATCH'];

		return in_array(Str::upper($this->request->method()), $methods);
	}

	/**
	 * Validate the form.
	 *
	 * @param array $validationRules
	 * @param array $messages
	 *
	 * @return Validator
	 */
	public function validate($validationRules = [], $messages = []): ?Validator {
		$fieldRules = $this->formHelper->mergeFieldsRules($this->fields);
		$rules = array_merge($fieldRules->getRules(), $validationRules);
		$messages = array_merge($fieldRules->getMessages(), $messages);

		$this->validator = $this->validatorFactory->make($this->getRequest()->all(), $rules, $messages);
		$this->validator->setAttributeNames($fieldRules->getAttributes());
		$this->eventDispatcher->dispatch(new BeforeFormValidation($this, $this->validator));

		return $this->validator;
	}

	/**
	 * Get current request.
	 *
	 * @return \Illuminate\Http\Request
	 */
	public function getRequest(): ?Request {
		return $this->request;
	}

	/**
	 * Set request on form.
	 *
	 * @param Request $request
	 *
	 * @return $this
	 */
	public function setRequest(Request $request): self {
		$this->request = $request;

		return $this;
	}

	/**
	 * Get validation errors.
	 *
	 * @return array
	 */
	public function getErrors(): array {
		if (!$this->validator || !$this->validator instanceof Validator) {
			throw new InvalidArgumentException(
				sprintf(
					'Form %s was not validated. To validate it, call "isValid" method before retrieving the errors',
					get_class($this)
				)
			);
		}

		return $this->validator->getMessageBag()->getMessages();
	}

	public function getErrorBag(): string {
		return $this->errorBag;
	}

	/**
	 * Optionally change the validation result, and/or add error messages.
	 *
	 * @param Form $mainForm
	 * @param bool $isValid
	 *
	 * @return void|array
	 */
	public function alterValid(Form $mainForm, bool &$isValid) {
		// return ['name' => ['Some other error about the Name field.']];
	}

	/**
	 * Get all Request values from all fields, and nothing else.
	 *
	 * @param bool $with_nulls
	 *
	 * @return array|\ArrayAccess|mixed
	 */
	public function getFieldValues(bool $with_nulls = true) {
		$request_values = $this->getRequest()->all();
		$values = [];

		foreach ($this->getAllAttributes() as $attribute) {
			$value = Arr::get($request_values, $attribute);

			if ($with_nulls || $value !== null) {
				Arr::set($values, $attribute, $value);
			}
		}

		// If this form is a child form, cherry pick a part
		if ($this->getName()) {
			$prefix = $this->getNameKey();
			$values = Arr::get($values, $prefix);
		}

		// Allow form-specific value alters
		$this->formHelper->alterFieldValues($this, $values);

		return $values;
	}

	/**
	 * Get all form field attributes, including child forms, in a flat array.
	 *
	 * @return array
	 */
	public function getAllAttributes(): array {
		return $this->formHelper->mergeAttributes($this->fields);
	}

	/**
	 * Optionally mess with this form's $values before it's returned from getFieldValues().
	 *
	 * @param array $values
	 *
	 * @return void
	 */
	public function alterFieldValues(array &$values) {
	}

	/**
	 * Method filterFields used as *Main* method for starting filtering and request field mutating process.
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function filterFields(): self {
		// If filtering is unlocked/allowed we can start with filtering process.
		if (!$this->isFilteringLocked()) {
			$filters = array_filter($this->getFilters());

			if (count($filters)) {
				$dotForm = $this->getNameKey();
				$request = $this->getRequest();
				$requestData = $request->all();

				foreach ($filters as $field => $fieldFilters) {
					$dotField = $this->formHelper->transformToDotSyntax($field);
					$fieldData = Arr::get($requestData, $dotField);

					if ($fieldData !== null) {
						// Assign current Raw/Unmutated value from request.
						$localDotField = preg_replace('#^' . preg_quote("$dotForm.", '#') . '#', '', $dotField);
						$localBracketField = $this->formHelper->transformToBracketSyntax($localDotField);
						$this->getField($localBracketField)->setRawValue($fieldData);

						foreach ($fieldFilters as $filter) {
							$filterObj = FilterResolver::instance($filter);
							$fieldData = $filterObj->filter($fieldData);
						}

						Arr::set($requestData, $dotField, $fieldData);
					}
				}

				foreach ($requestData as $name => $value) {
					$request[$name] = $value;
				}
			}
		}

		return $this;
	}

	/**
	 * Method isFilteringLocked used to check
	 * if current filteringLocked property status is set to true.
	 *
	 * @return bool
	 */
	public function isFilteringLocked(): bool {
		return !$this->lockFiltering ? false : true;
	}

	/**
	 * Method getFilters used to return array of all binded filters to form fields.
	 *
	 * @return array
	 */
	public function getFilters(): array {
		$filters = [];

		foreach ($this->getFields() as $field) {
			$filters[$field->getName()] = $field->getFilters();
		}

		return $filters;
	}

	/**
	 * If lockFiltering is set to true then we will not
	 * filter fields and mutate request data binded to fields.
	 *
	 * @return \App\Managers\Form\Form
	 */
	public function lockFiltering(): self {
		$this->lockFiltering = true;

		return $this;
	}

	/**
	 * Unlock fields filtering/mutating.
	 *
	 * @return \App\Managers\Form\Form
	 */
	public function unlockFiltering(): self {
		$this->lockFiltering = false;

		return $this;
	}

	/**
	 * Method getRawValues returns Unfiltered/Unmutated fields -> values.
	 *
	 * @return array
	 */
	public function getRawValues(): array {
		$rawValues = [];

		foreach ($this->getFields() as $field) {
			$rawValues[$field->getName()] = $field->getRawValue();
		}

		return $rawValues;
	}

	/**
	 * Setup model for form, add namespace if needed for child forms.
	 *
	 * @param $model
	 *
	 * @return $this
	 */
	protected function setupModel($model): self {
		$this->model = $model;
		$this->setupNamedModel();

		return $this;
	}
}
