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
 * @file   AfterCollectingFieldRules.php
 * @date   27/08/2020 03.17
 */

namespace App\Events;

use App\Managers\Form\Fields\FormField;
use App\Managers\Form\Rules;


class AfterCollectingFieldRules {
	/**
	 * The field instance.
	 *
	 * @var FormField
	 */
	protected $field;

	/**
	 * The field's rules.
	 *
	 * @var Rules
	 */
	protected $rules;

	/**
	 * Create a new after field creation instance.
	 *
	 * @param FormField $field
	 * @param Rules     $rules
	 */
	public function __construct(FormField $field, Rules $rules) {
		$this->field = $field;
		$this->rules = $rules;
	}

	/**
	 * Return the event's field.
	 *
	 * @return FormField
	 */
	public function getField() {
		return $this->field;
	}

	/**
	 * Return the event's field's rules.
	 *
	 * @return Rules
	 */
	public function getRules() {
		return $this->rules;
	}
}