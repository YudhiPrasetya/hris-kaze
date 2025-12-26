<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;


class CalendarEventForm extends Form {
	public function buildForm() {
		$this
			->add('title', Field::TEXT)
			->add('start_date', Field::DATE)
			->add('description', Field::TEXTAREA)
			->add('recurring', Field::CHECKBOX)
			->add('submit',
				Field::BUTTON_SUBMIT,
				[
					'label' => '<i class="fad fa-save mr-1"></i> Submit',
					'attr'  => ['class' => 'btn-falcon-danger'],
				]);
	}
}
