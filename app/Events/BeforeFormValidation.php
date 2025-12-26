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
 * @file   BeforeFormValidation.php
 * @date   27/08/2020 03.16
 */

namespace App\Events;

use App\Managers\Form\Form;
use Illuminate\Contracts\Validation\Validator;


class BeforeFormValidation {
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
	 * Create a new event instance.
	 *
	 * @param Form      $form
	 * @param Validator $validator
	 *
	 * @return void
	 */
	public function __construct(Form $form, Validator $validator) {
		$this->form = $form;
		$this->validator = $validator;
	}

	/**
	 * Get the Form instance of this event.
	 *
	 * @return Form
	 */
	public function getForm() {
		return $this->form;
	}

	/**
	 * Get the Validator instance of this event.
	 *
	 * @return Validator
	 */
	public function getValidator() {
		return $this->validator;
	}
}