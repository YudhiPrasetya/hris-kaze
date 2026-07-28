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
            // ->add('assignment_type', Field::SELECT, [
            //     'choices' => ['0' =>'Please select an assignment type', 'Domestic' => 'Domestic', 'Overseas' => 'Overseas'],
            //     'label' => 'Assignment Type',
            //     'attr' => ['class_append' => 'col-4 assignmentType', 'data-placeholder' => 'Please select an assignment type']
            // ])
            // ->add('overseas_to', Field::TEXT, [
            //     'label' => 'Overseas To',
            //     'attr' => ['class_append' => 'overseasTo']
            // ])
            ->add('assignment_no', Field::TEXT, [
                'label' => 'Letter No. (Nomor Surat)'
            ])
            ->add('letter_date', Field::DATE, [
                'label' => 'Letter Date (Tanggal Surat)',
                'attr' => ['class_append' => 'col-6']
            ])
            ->add('sr_no', Field::TEXT, [
                'label' => 'Service No. (No. Servis)'
            ])
            // ->add('customer_id', Field::ENTITY, [
            //     'class' => Customer::class,
            //     'property' => 'name',
            //     'label' => 'Customer',
            // ])

            // ->add('customer', Field::ENTITY, [
            //     'class' => Customer::class,
            //     'property' => 'name',
            //     'label' => 'Customer',
            // ])

            // ->add('machine_id', Field::ENTITY, [
            //     'class' => Machine::class,
            //     'property' => 'name',
            //     'label' => 'Machine (Mesin)',
            //     // 'attr' => ['multiple' => 'multiple', 'name' => 'machines[]']
            // ])

            // ->add('machine', Field::ENTITY, [
            //     'class' => Machine::class,
            //     'property' => 'name',
            //     'label' => 'Machine (Mesin)',
            //     // 'attr' => ['multiple' => 'multiple', 'name' => 'machines[]']
            // ])

            // ->add('machine_id', Field::SELECT, [
            //     'label' => "Macihne"
            // ])

            // ->add('employee_id', Field::ENTITY, [
            //     'class' => Employee::class,
            //     'property' => 'name',
            //     'label' => 'Technisians',
            //     'attr' => ['multiple' => 'multiple', 'name' => 'technisians[]']
            // ])
            // ->add('employee_id', Field::ENTITY, [
            //     'label' => 'Technisians',
            //     'class' => Employee::class,
            //     'property' => 'name',
            //     'attr' => ['multiple' => 'multiple', 'name' => 'employees[]']
            // ])
            // ->add('accPreService', Field::SWITCH,[
            //     'label' => 'Accomodation Pre Service'
            // ])
            ->add('checkIn', Field::DATE, [
                'label' => 'Check In Date',
                'attr' => ['class_append' => 'col-4']
            ])
            ->add('checkInAt', Field::TEXT, [
                'label' => 'Check In At',
            ])
            ->add('checkOut', Field::DATE, [
                'label' => 'Check Out',
                'attr' => ['class_append' => 'col-4']
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
			// ->add('technicians',
			// 	Field::COLLECTION,
			// 	[
			// 		'type'      => 'form',
			// 		'empty_row' => false,
			// 		'label'     => false,
			// 		'options'   => [
			// 			'label' => false,
			// 			'class' => DomesticAssignmentEmployeeForm::class,
			// 		],
			// 	])
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
                'attr' => ['class_append' => 'col-10']
            ])
            ->add('assignment_date_to', Field::DATE, [
                'label' => 'To',
                'attr' => ['class_append' => 'col-9']
            ])

            // ->add('assignment_date', Field::DATE, [
            //     'label' => 'Assignment Date',
            //     'attr' => ['class_append' => 'col-6 z-40']
            // ])

			// ->add('submit',Field::BUTTON_SUBMIT, [
            //     'label' => '<i class="fad fa-save mr-1"></i> Submit',
            //     'attr'  => ['class' => 'btn-falcon-danger'],
            // ]

			// ->add('submit',Field::BUTTON_SUBMIT, [
            //     'label' => '<i class="fad fa-save mr-1"></i> Submit',
            //     'attr'  => ['class' => 'btn-falcon-danger btnDomesticAssignment'],
            // ]

            ->add('eta_flight_date', Field::DATE, [
                'label' => 'ETA Flight Date',
                'attr' => ['class_append' => 'col-4 etaFlightDate']
            ])
            ->add('eta_flight_time', Field::TEXT, [
                'label' => 'ETA Flight Time',
                'attr' => ['class_append' => 'col-4 etaFlightTime']
            ])

            ->add('btnDomesticAssignment', Field::BUTTON_BUTTON, [
                'label' => '<i class="fad fa-save mr-1"></i> Submit',
                'attr' => ['class' => 'btn-falcon-success btnDomesticAssignment']
            ]
        );
    }
}
