<?php

namespace App\Managers\Form\Fields;

class SelectType extends FormField {
	/**
	 * The name of the property that holds the value.
	 *
	 * @var string
	 */
	protected string $valueProperty = 'selected';

	/**
	 * @inheritdoc
	 */
	public function getDefaults(): array {
		return [
			'choices'     => [],
			'empty_value' => null,
			'selected'    => null,
			'attr'        => [
				'data-placeholder' => 'Please select...'
			]
		];
	}

	/**
	 * @inheritdoc
	 */
	protected function getTemplate(): string {
		return 'select';
	}
}
