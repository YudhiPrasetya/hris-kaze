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
 * @file   Company.php
 * @date   2021-08-12 12:14:42
 */

namespace App\Libraries\Payroll\DataStructures;

class Company {
	/**
	 * Company::$allowances
	 *
	 * @var \App\Libraries\Payroll\DataStructures\Company\Allowances
	 */
	public Company\Allowances $allowances;

	/**
	 * Employee::__construct
	 */
	public function __construct() {
		$this->allowances = new Company\Allowances();
	}
}