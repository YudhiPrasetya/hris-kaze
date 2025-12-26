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
 * @file   AfterFormCreation.php
 * @date   27/08/2020 03.03
 */

namespace App\Events;

use App\Managers\Form\Form;


class AfterFormCreation {
	/**
	 * The form instance.
	 *
	 * @var Form
	 */
	protected $form;

	/**
	 * Create a new after form creation instance.
	 *
	 * @param Form $form
	 *
	 * @return void
	 */
	public function __construct(Form $form) {
		$this->form = $form;
	}

	/**
	 * Return the event's form.
	 *
	 * @return Form
	 */
	public function getForm() {
		return $this->form;
	}
}