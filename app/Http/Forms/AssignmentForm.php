<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;
use App\Models\Customer;
use App\Models\Machine;
use App\Models\Vehicle;
use App\Models\AssignmentDestination;

class AssignmentForm extends Form {
	public function buildForm() {
        $destinations = [
            "Domestic" => "Domestic",
            "Overseas" => "Overseas"
        ];

		$this
			->add('customer_id',
				Field::ENTITY,
				[
					'class'    => Customer::class,
					'property' => 'name',
					'label'    => 'Customer',
					'attr' => ['data-placeholder' => "Select a customer"]
				]
			)
			->add('service_no', Field::TEXT, ['label' => 'Service Report No.', 'attr' => ['class_append' => 'col-12']])
            ->add('destination', Field::ENTITY, [
					'class' => AssignmentDestination::class,
					'property' => 'name',
					'label' => 'Destination',
					'attr' => ['data-class' => 'col-3']
				]
			)
			->add('purchase_order_no', Field::TEXT, ['label' => 'Purchase Order No.'])
			->add('is_chargeable', Field::SWITCH)
			->add('charge_price',
				Field::INPUT_GROUP,
				[
					'label'   => 'Charge Price',
					'prepend' => '<span class="input-group-text charge_price_currency_symbol">Rp</span>',
					'attr'    => ['class_append' => 'text-right'],
				])
			->add('product_code', Field::TEXT, ['label' => 'Product Code'])
			//->add('machine_id',
			//	Field::ENTITY,
			//	[
			//		'class'    => Machine::class,
			//		'property' => 'name',
			//		'label'    => 'Machine Type',
			//	])
			->add('customer_machine_id',
                Field::ENTITY,
                [
                    'class' => Machine::class,
                    'property' => 'name',
                    'label' => 'Machine',
                    'attr' => ['data-placeholder' => "Select a machine"]
                ]
            )
			->add('vehicle_id',
				Field::ENTITY,
				[
					'class'    => Vehicle::class,
					'property' => 'plat_number',
					'label'    => 'Vehicle',
				])
            // ->add('destination', Field::SELECT, [
            //     'label' => "Destinations",
            //     'choices' => $destinations,
            //     'attr' => ['style' => 'width: 50%']
            // ])
			->add('work_detail', Field::TEXTAREA, ['label' => 'Work Detail'])
			->add('note', Field::TEXTAREA, ['label' => 'Note'])
			//->add('is_completed', Field::SWITCH)
			//->add('next_service_date', Field::DATE)
			->add('service_date', Field::DATE, ['attr' => ['class_append' => 'col-12']])
			->add('technicians',
				Field::COLLECTION,
				[
					'type'      => 'form',
					'empty_row' => false,
					'label'     => false,
					'options'   => [
						'label' => false,
						'class' => AssignmentEmployeeForm::class,
					],
				])
			->add('parts',
				Field::COLLECTION,
				[
					'type'      => 'form',
					'empty_row' => false,
					'label'     => false,
					'options'   => [
						'label' => false,
						'class' => AssignmentPartForm::class,
					],
				])
			->add('submit',
				Field::BUTTON_SUBMIT,
				[
					'label' => '<i class="fad fa-save mr-1"></i> Submit',
					'attr'  => ['class' => 'btn-falcon-danger'],
				]);
	}
}
