<?php

namespace App\Http\Forms\Settings;

use App\Managers\Form\Field;
use App\Managers\Form\Form;


class AttendanceForm extends Form {
	public function buildForm() {
		$cutoff = [
			"user_defined" => 'User Defined',
			"end_of_month" => 'End of Month'
		];

		$this
			->add('ip', Field::TEXT, ['label' => 'IP'])
			->add('port', Field::TEXT)
			->add('service_ip', Field::TEXT, ['label' => 'IP'])
			->add('service_port', Field::TEXT, ['label' => 'Port'])
			->add('user', Field::TEXT, ['label' => 'Username'])
			->add('password', Field::PASSWORD, ['label' => 'Password'])

			->add('serial_number', Field::TEXT)
			// ->add('start', Field::TIME, ['label' => 'Start', 'value' => '09:00'])
			// ->add('end', Field::TIME, ['label' => 'End', 'value' => '17:00'])
			->add('cutoff',
				Field::SELECT,
				[
					'label' => 'Cut Off Date',
					'choices' => $cutoff,
					'attr' => [
						'style' => "width: 100%"
					]
				])
			->add('cutoff_date', Field::TEXT, ['attr' => [
				'class_append' => 'd-inline col-2 mt-2 mx-1 text-center'
			]])
			->add('submit',
				Field::BUTTON_SUBMIT,
				[
					'label' => '<i class="fad fa-save mr-1"></i> Submit',
					'attr'  => ['class' => 'btn-falcon-danger'],
				]);
	}
}
