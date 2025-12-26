<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;



class VehicleForm extends Form {
    public function buildForm() {
        $this
            ->add('plat_number', Field::TEXT)
            ->add('type', Field::TEXT)
            ->add('imei', Field::TEXT)
	        ->add('submit',
		        Field::BUTTON_SUBMIT,
		        [
			        'label' => '<i class="fad fa-save mr-1"></i> Submit',
			        'attr'  => ['class' => 'btn-falcon-danger'],
		        ])
        ;
    }
}
