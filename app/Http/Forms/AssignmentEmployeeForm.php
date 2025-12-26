<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;
use App\Models\Assignment;
use App\Models\Employee;


class AssignmentEmployeeForm extends Form {
	public function buildForm() {
		$this
			->add('employee_id', Field::ENTITY, ['class' => Employee::class, 'attr' => ['class_append' => 'technician']])
			->add('start_job', Field::TIME, ['attr' => ['class_append' => 'col-2']])
			->add('finish_job', Field::TIME, ['attr' => ['class_append' => 'col-2']])
			->add('travel_time', Field::TIME, ['attr' => ['class_append' => 'col-2']])
			->add('overtime', Field::TIME, ['attr' => ['class_append' => 'col-2']]);
	}
}
