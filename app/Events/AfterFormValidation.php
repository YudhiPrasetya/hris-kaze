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
 * @file   AfterFormValidation.php
 * @date   27/08/2020 03.07
 */

namespace App\Events;

use App\Managers\Form\Form;
use Illuminate\Contracts\Validation\Validator;


class AfterFormValidation {
	/**
	 * The form instance.
	 *
	 * @var Form
	 */
	protected $form;

	/**
	 * The validator instance.
	 *
	 * @var Validator
	 */
	protected $validator;

	/**
	 * Indicates if the form is valid.
	 *
	 * @var bool
	 */
	protected $valid;

	/**
	 * Create a new after form validation instance.
	 *
	 * @param Form      $form
	 * @param Validator $validator
	 * @param bool      $valid
	 *
	 * @return void
	 */
	public function __construct(Form $form, Validator $validator, $valid) {
		$this->form = $form;
		$this->validator = $validator;
		$this->valid = $valid;
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
	 * Return the event's validator.
	 *
	 * @return Validator
	 */
	public function getValidator() {
		return $this->validator;
	}

	/**
	 * Return wether the form is valid.
	 *
	 * @return bool
	 */
	public function isValid() {
		return $this->valid;
	}
}