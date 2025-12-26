<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;
use App\Models\Customer;
use App\Models\Machine;


class CustomerMachineForm extends Form {
	public function buildForm() {
		$this
			->add('customer_id',
				Field::ENTITY,
				[
					'class'    => Customer::class,
					'property' => 'name',
					'label'    => 'Customer',
					'attr'     => ['data-placeholder' => "Select a customer"],
				])
			->add('machine_id',
				Field::ENTITY,
				[
					'class'    => Machine::class,
					'property' => 'name',
					'label'    => 'Machine',
					'attr'     => ['data-placeholder' => "Select a machine"],
				])
			->add('serial_number', Field::TEXT)
			->add('submit',
				Field::BUTTON_SUBMIT,
				[
					'label' => '<i class="fad fa-save mr-1"></i> Submit',
					'attr'  => ['class' => 'btn-falcon-danger'],
				]);
	}
}
