<?php

namespace App\Http\Forms;

use App\Http\Forms\AccomodationDuringServiceForm;
use App\Http\Forms\DomesticAssignmentEmployeeForm;
use App\Managers\Form\Field;
use App\Managers\Form\Form;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Machine;

class DomesticAssignmentForm extends Form{
    public function buildForm(){
        $this
            ->add('assignment_no', Field::TEXT, [
                'label' => 'Letter No. (Nomor Surat)'
            ])
            ->add('letter_date', Field::DATE, [
                'label' => 'Letter Date (Tanggal Surat)',
                'attr' => ['class_append' => 'col-6, z-50']
            ])
            ->add('sr_no', Field::TEXT, [
                'label' => 'Service No. (No. Servis)'
            ])
            ->add('preService', Field::COLLECTION, [
                'type' => 'form',
                'empty_row' => false,
				'label'     => false,
				'options'   => [
                    'label' => false,
                    'class' => AccomodationPreServiceForm::class,
				],
            ])
            ->add('duringService', Field::COLLECTION, [
                'type' => 'form',
                'empty_row' => false,
				'label'     => false,
				'options'   => [
                    'label' => false,
                    'class' => AccomodationDuringServiceForm::class,
				],
            ])
			->add('is_chargeable', Field::SWITCH, [
                'label' => 'Chargeable',
                'attr' => ['class_append' => 'col-2']
            ])
			->add('charge_price',Field::INPUT_GROUP,[
                'label'   => 'Charge Price',
                'prepend' => '<span class="input-group-text charge_price_currency_symbol">Rp</span>',
                'attr'    => ['class_append' => 'text-right']
			])

            ->add('assignment_date_from', Field::DATE, [
                'label' => 'Assign Date From',
                'attr' => ['class_append' => 'col-8']
            ])
            ->add('assignment_date_to', Field::DATE, [
                'label' => 'To',
                'attr' => ['class_append' => 'col-8']
            ])

            ->add('btnDomesticAssignment', Field::BUTTON_BUTTON, [
                'label' => '<i class="fad fa-save mr-1"></i> Submit',
                'attr' => ['class' => 'btn-falcon-success btnDomesticAssignment']
            ]
        );
    }
}
