<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;
use App\Models\Customer;



class MachineForm extends Form {
    public function buildForm() {
        $this
            ->add('customer_id', Field::ENTITY,
                [
                    'class' => Customer::class,
                    'property' => 'name',
                    'label' => 'Customer',
                    'attr' => ['data-placeholder' => 'Select a customer']
                ]
            )
            ->add('name', Field::TEXT, ['label' => 'Machine Name'])
            ->add('type', Field::TEXT, ['label' => 'Machine Type'])
            ->add('serial_number', Field::TEXT, ['label' => 'Machine Serial Number'])
	        ->add('submit',
		        Field::BUTTON_SUBMIT,
		        [
			        'label' => '<i class="fad fa-save mr-1"></i> Submit',
			        'attr'  => ['class' => 'btn-falcon-danger'],
		        ])
        ;
    }
}
