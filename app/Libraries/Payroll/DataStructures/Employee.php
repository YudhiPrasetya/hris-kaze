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
 * @file   Employee.php
 * @date   2021-08-12 12:14:54
 */

namespace App\Libraries\Payroll\DataStructures;

class Employee {
	/**
	 * Employee::$permanentStatus
	 *
	 * @var bool
	 */
	public bool $permanentStatus = true;

	/**
	 * Employee::$maritalStatus
	 *
	 * @var bool
	 */
	public bool $maritalStatus = false;

	/**
	 * Employee::$hasNPWP
	 *
	 * @var bool
	 */
	public bool $hasNPWP = true;

	/**
	 * Employee::$numOfDependentsFamily
	 *
	 * @var int
	 */
	public int $numOfDependentsFamily = 0;

	/**
	 * Employee::$presences
	 *
	 * @var \App\Libraries\Payroll\DataStructures\Employee\Presences
	 */
	public Employee\Presences $presences;

	/**
	 * Employee::$earnings
	 *
	 * @var \App\Libraries\Payroll\DataStructures\Employee\Earnings
	 */
	public Employee\Earnings $earnings;

	/**
	 * Employee::$allowances
	 *
	 * @var \App\Libraries\Payroll\DataStructures\Employee\Allowances
	 */
	public Employee\Allowances $allowances;

	/**
	 * Employee::$deductions
	 *
	 * @var \App\Libraries\Payroll\DataStructures\Employee\Deductions
	 */
	public Employee\Deductions $deductions;

	/**
	 * Company::$calculateHolidayAllowance
	 *
	 * @var int
	 */
	public int $calculateHolidayAllowance = 0;

	/**
	 * Employee::$employeeGuarantee
	 * 
	 * @var bool
	 */
	public bool $employeeGuarantee = false;

	/**
	 * Employee::$bonus
	 *
	 * @var \App\Libraries\Payroll\DataStructures\Employee\Bonus
	 */
	public Employee\Bonus $bonus;

	/**
	 *
	 */
	public function __construct() {
		$this->presences = new Employee\Presences();
		$this->earnings = new Employee\Earnings();
		$this->allowances = new Employee\Allowances();
		$this->deductions = new Employee\Deductions();
		$this->bonus = new Employee\Bonus();
	}
}