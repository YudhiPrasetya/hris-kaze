<?php

namespace App\Managers\Form\Fields;

class CheckableType extends FormField {

	/**
	 * @inheritdoc
	 */
	protected string $valueProperty = 'checked';

	/**
	 * @inheritdoc
	 */
	public function getDefaults(): array {
		return [
			'attr'    => ['class' => null, 'id' => $this->getName()],
			'value'   => 1,
			'checked' => null
		];
	}

	/**
	 * @inheritdoc
	 */
	protected function getTemplate(): string {
		return $this->type;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function isValidValue($value): bool {
		return $value !== null;
	}
}
