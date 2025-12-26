<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;



class WorkingShiftForm extends Form {
    public function buildForm() {
        $this->add('start', Field::TIME, ['attr' => ['class_append' => 'col-12']])
             ->add('end', Field::TIME, ['attr' => ['class_append' => 'col-12']])
	        ->add('submit',
		        Field::BUTTON_SUBMIT,
		        [
			        'label' => '<i class="fad fa-save mr-1"></i> Submit',
			        'attr'  => ['class' => 'btn-falcon-danger'],
		        ]);
    }
}
