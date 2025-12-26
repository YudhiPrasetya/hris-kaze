<?php

namespace App\Managers\Form\Fields;

class StaticType extends FormField {
	/**
	 * @inheritdoc
	 */
	public function render(array $options = [], $showLabel = true, $showField = true, $showError = false): string {
		$this->setupStaticOptions($options);

		return parent::render($options, $showLabel, $showField, $showError);
	}

	/**
	 * Setup static field options.
	 *
	 * @param array $options
	 *
	 * @return void
	 */
	private function setupStaticOptions(&$options) {
		$options['elemAttrs'] = $this->formHelper->prepareAttributes($this->getOption('attr'));
	}

	/**
	 * @inheritdoc
	 */
	public function getAllAttributes(): array {
		// No input allowed for Static fields.
		return [];
	}

	/**
	 * @inheritdoc
	 */
	protected function getTemplate() {
		return 'static';
	}

	/**
	 * @inheritdoc
	 */
	protected function getDefaults(): array {
		return [
			'tag'  => 'div',
			'attr' => ['class' => 'form-control-static', 'id' => $this->getName()]
		];
	}
}
