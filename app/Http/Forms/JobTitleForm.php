<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;

class JobTitleForm extends Form{
    public function buildForm()
    {
        $this->add('name', Field::TEXT, ['attr' => ['class_append' => 'col-12']])->add('description', Field::TEXTAREA, ['attr' => ['class_append' => 'col-12']])->add('submit', Field::BUTTON_SUBMIT, [
            'label' => '<i class="fad fa-save mr-1"></i> Submit', 'attr' => ['class' => 'btn-falcon-success']
        ]);
    }
}
