<?php

namespace App\Managers\Form\Fields;

class ButtonType extends FormField {
	/**
	 * @inheritdoc
	 */
	public function getAllAttributes(): array {
		// Don't collect input for buttons.
		return [];
	}

	/**
	 * @inheritdoc
	 */
	protected function getTemplate(): string {
		return 'button';
	}

	/**
	 * @inheritdoc
	 */
	protected function getDefaults(): array {
		return [
			'wrapper' => false,
			'attr'    => [
				'type'         => $this->type,
				'class_append' => $this->getConfig('defaults.button.field_class'),
			],
		];
	}
}
