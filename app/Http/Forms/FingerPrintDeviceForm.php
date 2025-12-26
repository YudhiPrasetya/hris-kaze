<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;

class FingerPrintDeviceForm extends Form{
    public function buildForm()
    {
        $this
            ->add('no', Field::NUMBER, ['label' => 'No.Device'])
            ->add('ip_address', Field::TEXT, ['label' => 'Ip Address'])
            ->add('port', Field::NUMBER, ['label' => 'Port'])
            ->add('description', Field::TEXT, ['label' => 'Description'])
            ->add('submit', Field::BUTTON_SUBMIT, ['label' => '<i class="fad fa-save mr-1"></i> Submit', 'attr' => ['class' => 'btn-falcon-danger', 'id' => 'submit-fpdevice'],]);
    }
}
