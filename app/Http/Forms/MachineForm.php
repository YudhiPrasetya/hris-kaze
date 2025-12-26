<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;



class MachineForm extends Form {
    public function buildForm() {
        $this
            ->add('name', Field::TEXT)
            ->add('type', Field::TEXT)
	        ->add('submit',
		        Field::BUTTON_SUBMIT,
		        [
			        'label' => '<i class="fad fa-save mr-1"></i> Submit',
			        'attr'  => ['class' => 'btn-falcon-danger'],
		        ])
        ;
    }
}
