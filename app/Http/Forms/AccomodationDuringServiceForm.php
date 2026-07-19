<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;
// use App\Models\Assignment;
use App\Models\Employee;


class AccomodationDuringServiceForm extends Form {
	public function buildForm() {
		$this
			->add('employee_id', Field::TEXT, ['attr' => ['class_append' => 'technisian']])
			->add('employee_name', Field::TEXT)
			->add('assignment_date', Field::DATE, ['attr' => ['class_append' => 'col-2']])
			->add('breakfast', Field::CHECKBOX, ['attr' => ['class_append' => 'col-2']])
			->add('start_job', Field::TEXT, ['attr' => [
                'class_append' => 'col-2',
                'placeholer' => "HH:MM",
            ]])
			->add('lunch', Field::CHECKBOX, ['attr' => ['class_append' => 'col-2']])
			->add('finish_job', Field::TEXT, ['attr' => [
                'class_append' => 'col-2',
                'placeholer' => "HH:MM"
            ]])
			->add('dinner', Field::CHECKBOX, ['attr' => ['class_append' => 'col-2']]);
	}
}
