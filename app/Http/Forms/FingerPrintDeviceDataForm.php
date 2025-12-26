<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;
use App\Models\FingerPrintDevice;

class FingerPrintDeviceDataForm extends Form{
    public function buildForm()
    {
        $this
            ->add(
                'finger_print_device_id',
                Field::ENTITY,
                [
                    'class' => FingerPrintDevice::class,
                    'property' => 'device',
                    'label' => 'Device',
                    'query_builder' => function(FingerPrintDevice $fingerPrintDevice){
                        return $fingerPrintDevice->select(['id', 'no', 'ip_address', 'port'])->selectRaw(
                            'CONCAT("{\"key\":\"", id, "\",\"labelWithKey\": false, \"value\":\"", ip_address, "\"}") as device');
                    }
                ]
            )
            ->add('from', Field::DATE, ['label' => 'From', 'attr' => ['class_append' => 'col-5']])
            ->add('to', Field::DATE, ['label' => 'To', 'attr' => ['class_append' => 'col-5']])
            ->add('submit', Field::BUTTON_SUBMIT, [
                'label' => '<i class="fad fa-download mr-1"></i> Pull Data',
                'attr' => ['class' => 'btn-falcon-success', 'id' => 'submit_pulldata']
            ]);

    }
}
