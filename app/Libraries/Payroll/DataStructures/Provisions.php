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
 * @file   Provisions.php
 * @date   2021-08-12 12:15:3
 */

namespace App\Libraries\Payroll\DataStructures;

class Provisions {
	/**
	 * Provision::$state
	 *
	 * @var \App\Libraries\Payroll\DataStructures\Provisions\State
	 */
	public Provisions\State $state;

	/**
	 * Provision::$company
	 *
	 * @var \App\Libraries\Payroll\DataStructures\Provisions\Company
	 */
	public Provisions\Company $company;

	// ------------------------------------------------------------------------

	/**
	 * Provisions::__construct
	 */
	public function __construct() {
		$this->state = new Provisions\State();
		$this->company = new Provisions\Company();
	}
}