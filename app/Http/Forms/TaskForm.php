<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;
use App\Models\Employee;
use App\Models\Priority;


class TaskForm extends Form {
	public function buildForm() {
		$this
			->add('employee_id',
				Field::ENTITY,
				[
					'class'    => Employee::class,
					'property' => 'name',
					'label'    => 'To',
				])
			->add('priority_id',
				Field::ENTITY,
				[
					'class'         => Priority::class,
					'property'      => 'name',
					'label'         => 'Level of Urgency',
					'query_builder' => function (Priority $priority) {
						// If query builder option is not provided, all data is fetched
						return $priority->orderBy('level', 'DESC');
					},
				])
			->add('dateline', Field::DATE, ['label' => 'Due Date', 'attr' => ['class_append' => 'col-5']])
			->add('title', Field::TEXT)
			->add('description', Field::TEXTAREA)
			->add('submit',
				Field::BUTTON_SUBMIT,
				[
					'label' => '<i class="fad fa-save mr-1"></i> Submit',
					'attr'  => ['class' => 'btn-falcon-danger'],
				]);
	}
}
