<?php

namespace App\Http\Forms;

use App\Managers\Form\Form;
use App\Managers\Form\Field;

class PositionForm extends Form {
    public function buildForm() {
        // Add fields here...
        $this->add('name', Field::TEXT, ['attr' => ['class_append' => 'col-12']])
        ->add('submit', Field::BUTTON_SUBMIT, ['label' => '<i class="fad fa-save mr-1"></i> Submit', 'attr' => ['class' => 'btn-falcon-success']]);
    }
}
