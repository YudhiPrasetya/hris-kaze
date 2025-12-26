<?php

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;
use App\Models\World\City;
use App\Models\World\Country;
use App\Models\World\District;
use App\Models\World\State;
use App\Models\World\Village;


class CustomerForm extends Form {
	public function buildForm() {
		$this
			->add('country_id',
				Field::ENTITY,
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
			->add('name', Field::TEXT)
			->add('postal_code', Field::TEXT)
			->add('email', Field::EMAIL)
			->add('street', Field::TEXTAREA)
			->add('submit',
				Field::BUTTON_SUBMIT,
				[
					'label' => '<i class="fad fa-save mr-1"></i> Submit',
					'attr'  => ['class' => 'btn-falcon-danger'],
				]);
		;
	}
}
