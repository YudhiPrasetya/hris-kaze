<?php

namespace App\Managers\Form\Fields;

class TextareaType extends FormField {
	/**
	 * @inheritdoc
	 */
	protected function getTemplate(): string {
		return 'textarea';
	}
}
