<?php
/**
 * This file is part of the Kaze project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   AttendanceReportForm.php
 * @date   2021-04-9 20:5:36
 */

namespace App\Http\Forms;

use App\Managers\Form\Field;
use App\Managers\Form\Form;
use App\Models\Employee;


class AttendanceReportForm extends Form {
	public function buildForm() {
		$months = [
			"01" => 'January',
			"02" => 'February',
			"03" => 'March',
			"04" => 'April',
			"05" => 'May',
			"06" => 'June',
			"07" => 'July',
			"08" => 'August',
			"09" => 'September',
			"10" => 'October',
			"11" => 'November',
			"12" => 'December',
		];

		$this
			->add('month',
				Field::SELECT,
				[
					'choices' => $months,
					'selected' => sprintf("%02d", intval(date('n'))),
					'attr' => [
						'data-value' => sprintf("%02d", intval(date('n'))),
						'style' => "width: 100%"
					]
				])
			->add('year', Field::NUMBER, ['value' => date('Y')])
			->add('employee',
				Field::ENTITY,
				[
					'class'      => Employee::class,
					'property'   => 'name',
					'attr' => [
						'style' => "width: 100%"
					]
				])
            ->add('rangeDateTime',
                Field::RANGE,
            )
			->add('submit',
				Field::BUTTON_SUBMIT,
				[
					'label' => '<i class="fad fa-eye mr-1"></i> Show',
					'attr'  => ['class' => 'btn-falcon-success', 'data-toggle' => 'tooltip', 'title' => 'Display results'],
				]);
	}
}
