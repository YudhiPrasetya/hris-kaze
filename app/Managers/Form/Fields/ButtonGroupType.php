<?php

namespace App\Managers\Form\Fields;

class ButtonGroupType extends FormField {
	/**
	 * @inheritdoc
	 */
	public function render(array $options = [], $showLabel = true, $showField = true, $showError = true): string {
		$options['splitted'] = $this->getOption('splitted', false);
		$options['size'] = $this->getOption('size', 'md');
		$options['buttons'] = $this->getOption('buttons', []);

		return parent::render($options, $showLabel, $showField, $showError);
	}

	/**
	 * The path the template.
	 *
	 * @return string
	 */
	protected function getTemplate(): string {
		return 'buttongroup';
	}
}
