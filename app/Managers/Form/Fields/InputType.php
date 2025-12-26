<?php

namespace App\Managers\Form\Fields;

class InputType extends FormField {
	/**
	 * @inheritdoc
	 */
	protected function getTemplate(): string {
		return 'text';
	}

}
