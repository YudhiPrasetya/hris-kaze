<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;
use App\Models\Gender;
use App\Models\JobTitle;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkingShift;
use App\Models\World\Country;
use App\Models\World\Currency;


class EmployeeForm extends Form {
	public function buildForm() {
		$this
			->add('nik', Field::TEXT, ['label' => 'NIK'])
			->add('pin', Field::HIDDEN)
			->add('finger', Field::HIDDEN)
			->add('finger_size', Field::HIDDEN)
			->add('finger_index', Field::HIDDEN)
			->add('name', Field::TEXT)
			->add('street', Field::TEXTAREA)
			->add('postal_code', Field::TEXT)
			->add('birth_date', Field::DATE, [
				'wrapper'    => [
					'class_append' => 'form-inline',
				],
				'label_attr' => [
					'class_append' => 'col-4 pl-0 justify-content-start',
				],
			])
			->add('effective_since', Field::DATE)
            ->add('leave_allowance', Field::NUMBER)
			->add('marital_status', Field::SWITCH)
			->add('has_npwp', Field::SWITCH)
			->add('num_of_dependents_family', Field::NUMBER, [
				'wrapper'    => [
					'class_append' => 'form-inline',
				],
				'label_attr' => [
					'class_append' => 'col-4 pl-0 justify-content-start',
				],
				'attr'       => [
					'size'         => 2,
					'max-length'   => 2,
					'class_append' => 'col-2',
				],
			])
			->add('permanent_status', Field::SWITCH)
			->add('employee_guarantee', Field::SWITCH)
			->add('profile_photo_path',
				'browse',
				[
					'btnLabel' => 'Pick Image',
					'attr'     => [
						'accept'      => "image/*",
						'data-target' => 'targetPreview',
						'onchange'    => 'javascript: previewImage(event);',
					],
				])
			->add('working_shift_id',
				Field::ENTITY,
				[
					'class'         => WorkingShift::class,
					'property'      => 'time',
					'label'         => 'Working Shift',
					'query_builder' => function (WorkingShift $workingShift) {
						return $workingShift->select(['id', 'start', 'end'])
						                    ->selectRaw('CONCAT("{\"key\":\"", id, "\",\"labelWithKey\": false, \"value\":\"", start, " - ", end, "\"}") as time');
					},
				])
			->add('position_id',
				Field::ENTITY,
				[
					'class'    => Position::class,
					'property' => 'name',
					'label'    => 'Position',
				])
            ->add('job_title_id', Field::ENTITY,
                [
                    'class' => JobTitle::class,
                    'property' => 'nameDescription',
                    'label' => 'Job Title',
                    'query_builder' => function(JobTitle $jobTitle){
                        return $jobTitle->select(['id', 'name', 'description'])
                                        ->selectRaw('CONCAT("{\"key\":\"", id, "\", \"labelWithKey\": false, \"value\":\"", name, " - ", description, "\"}") as nameDescription');
                    }
                ])
            // ->add('job_title_id',
            //     Field::ENTITY,
            //     [
            //         'class' => JobTitle::class,
            //         'property' => 'name',
            //         'label' => 'Job Title',
            //     ])
			->add('gender_id',
				Field::ENTITY,
				[
					'class'      => Gender::class,
					'property'   => 'name',
					'label'      => 'Gender',
					'wrapper'    => [
						'class_append' => 'form-inline',
					],
					'label_attr' => [
						'class_append' => 'col-4 pl-0 justify-content-start',
					],
					'attr'       => [
						'data-class' => 'col-3',
					],
				])
			->add('currency_code',
				Field::ENTITY,
				[
					'class'         => Currency::class,
					'attr'          => ['data-placeholder' => 'Select a currency'],
					'property'      => 'name',
					'label'         => 'Currency',
					'property_key'  => 'code',
					'query_builder' => function (Currency $currency) {
						return $currency->select('code')
						                ->selectRaw('CONCAT("{\"key\":\"", symbol, "\",\"value\":\"", name, "\"}") as name')
						                ->where('name', '<>', 'NULL');
					},
				])
			->add('user_id',
				Field::ENTITY,
				[
					'class'      => User::class,
					'property'   => 'username',
					'label'      => 'Linked to user',
					'help_block' => ['text' => 'If the employee has access to login.', 'attr' => ['class' => 'form-text text-nunito text-warning fs--1']],
				])
			->add('country_id',
				'entity',
				[
					'class'    => Country::class,
					'property' => 'name',
					'label'    => 'Country',
					'attr'     => ['data-placeholder' => "Select a country"],
				])
			->add('state_id', Field::SELECT, ['label' => 'State', 'attr' => ['data-placeholder' => "Select a state"]])
			->add('city_id', Field::SELECT, ['label' => 'City', 'attr' => ['data-placeholder' => "Select a city"]])
			->add('district_id', Field::SELECT, ['label' => 'District', 'attr' => ['data-placeholder' => "Select a district"]])
			->add('village_id', Field::SELECT, ['label' => 'Village', 'attr' => ['data-placeholder' => "Select a village"]])
			->add('basic_salary',
				Field::INPUT_GROUP,
				[
					'label'   => 'Basic Salary',
					'prepend' => '<span class="input-group-text basic_salary_currency_symbol">Rp</span>',
					'attr'    => ['class_append' => 'text-right'],
				])
			->add('functional_allowance',
				Field::INPUT_GROUP,
				[
					'label'   => 'Functional',
					'prepend' => '<span class="input-group-text functional_allowance_currency_symbol">Rp</span>',
					'attr'    => ['class_append' => 'text-right'],
				])
			->add('transport_allowance',
				Field::INPUT_GROUP,
				[
					'label'   => 'Transportation',
					'prepend' => '<span class="input-group-text transport_allowance_currency_symbol">Rp</span>',
					'attr'    => ['class_append' => 'text-right'],
				])
			->add('meal_allowances',
				Field::INPUT_GROUP,
				[
					'label'   => 'Meal',
					'prepend' => '<span class="input-group-text meal_allowances_currency_symbol">Rp</span>',
					'attr'    => ['class_append' => 'text-right'],
				])
			->add('other_allowance',
				Field::INPUT_GROUP,
				[
					'label'   => 'Other',
					'prepend' => '<span class="input-group-text other_allowances_currency_symbol">Rp</span>',
					'attr'    => ['class_append' => 'text-right'],
				])
			->add('attendance_premium',
				Field::INPUT_GROUP,
				[
					'label'   => 'Attendance',
					'prepend' => '<span class="input-group-text other_allowances_currency_symbol">Rp</span>',
					'attr'    => ['class_append' => 'text-right'],
				])
			->add('overtime',
				Field::INPUT_GROUP,
				[
					'label'   => 'Overtime',
					'prepend' => '<span class="input-group-text other_allowances_currency_symbol">Rp</span>',
					'attr'    => ['class_append' => 'text-right'],
				])
			->add('submit',
				Field::BUTTON_SUBMIT,
				[
					'label' => '<i class="fad fa-save mr-1"></i> Submit',
					// 'attr'  => ['class' => 'btn-falcon-danger', 'id' => 'submit-employee'],
					'attr'  => ['class' => 'btn-falcon-danger', 'id' => 'submit-employee1'],
				]);
	}
}
