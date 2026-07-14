<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;
// use App\Models\Assignment;
use App\Models\Employee;


class AccomodationPreServiceForm extends Form {
	public function buildForm() {
		$this
			->add('employee_id', Field::TEXT, ['attr' => ['class_append' => 'technician']])
			->add('employee_name', Field::TEXT)
			// ->add('check_in', Field::DATE, ['attr' => ['class_append' => 'col-2']])
			->add('breakfast', Field::CHECKBOX, ['attr' => ['class_append' => 'col-2']])
			->add('lunch', Field::CHECKBOX, ['attr' => ['class_append' => 'col-2']])
			->add('dinner', Field::CHECKBOX, ['attr' => ['class_append' => 'col-2']])
			->add('supper', Field::CHECKBOX, ['attr' => ['class_append' => 'col-2']]);
	}
}
