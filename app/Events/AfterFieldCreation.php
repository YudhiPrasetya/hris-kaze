<?php
/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   AfterFieldCreation.php
 * @date   27/08/2020 03.09
 */

namespace App\Events;

use App\Managers\Form\Fields\FormField;
use App\Managers\Form\Form;


class AfterFieldCreation {
	/**
	 * The form instance.
	 *
	 * @var Form
	 */
	protected $form;

	/**
	 * The field instance.
	 *
	 * @var FormField
	 */
	protected $field;

	/**
	 * Create a new after field creation instance.
	 *
	 * @param Form      $form
	 * @param FormField $field
	 *
	 * @return void
	 */
	public function __construct(Form $form, FormField $field) {
		$this->form = $form;
		$this->field = $field;
	}

	/**
	 * Return the event's form.
	 *
	 * @return Form
	 */
	public function getForm() {
		return $this->form;
	}

	/**
	 * Return the event's field.
	 *
	 * @return FormField
	 */
	public function getField() {
		return $this->field;
	}
}