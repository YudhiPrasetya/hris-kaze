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
 * @file   Earnings.php
 * @date   2021-08-12 12:16:9
 */

namespace App\Libraries\Payroll\DataStructures\Employee;

class Earnings {
	/**
	 * Earnings::$base
	 *
	 * @var int
	 */
	public int $base = 0;

	/**
	 * Earnings::$fixedAllowance
	 *
	 * @var int
	 */
	public int $fixedAllowance = 0;

	/**
	 * Earnings::$overtime
	 *
	 * @var int
	 */
	public int $overtime = 0;

	/**
	 * Earnings::$splitShifts
	 *
	 * @var int
	 */
	public int $splitShifts = 0;

	/**
	 * Earnings::$holidayAllowance
	 *
	 * @var int
	 */
	public int $holidayAllowance = 0;

	/**
	 * Earnings::$functionalAllowance
	 * 
	 * @var int
	 */
	public int $functionalAllowance = 0;

	/**
	 * Earnings::$transportAllowance
	 * 
	 * @var int
	 */
	public int $transportAllowance = 0;

	/**
	 * Earnings::$mealAllowances
	 * 
	 * @var int
	 */
	public int $mealAllowances = 0;



	/**
	 * Earnings::$otherAllowance
	 * 
	 * @var int
	 */
	public int $otherAllowance = 0;

	/**
	 * Earnings::$attendaceEarnings
	 * 
	 * @var int
	 */
	public int $attendanceEarnings = 0;

	/**
	 * Earnings::$overtimeEarnings
	 * 
	 * @var int
	 */
	public int $overtimeEarnings = 0;

	/**
	 * Earnings::$eidEarnings
	 * 
	 * @var int
	 */
	public int $eidEarnings = 0;
}