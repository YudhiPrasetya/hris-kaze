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
 * @date   2021-08-12 12:16:54
 */

namespace App\Libraries\Payroll\DataStructures\Provisions;

class Company {
	/**
	 * Company::$numOfWorkingDays
	 *
	 * @var int
	 */
	public int $numOfWorkingDays = 25;

	/**
	 * Company::$numOfWorkingHours
	 *
	 * @var int
	 */
	public int $numOfWorkingHours = 8;

	/**
	 * Company::$calculateOvertime
	 *
	 * @var bool
	 */
	public bool $calculateOvertime = true;

	/**
	 * Company::$overtimeRate
	 *
	 * @var int
	 */
	public int $overtimeRate = 0;

	/**
	 * Company::$calculateSplitShifts
	 *
	 * @var bool
	 */
	public bool $calculateSplitShifts = true;

	/**
	 * Company::$splitShiftsRate
	 *
	 * @var int
	 */
	public int $splitShiftsRate = 0;

	/**
	 * Company::$calculateBPJSKesehatan
	 *
	 * @var bool
	 */
	public bool $calculateBPJSKesehatan = true;

	/**
	 * Company::$JKK
	 *
	 * @var bool
	 */
	public bool $JKK = false;

	public bool $fixed_JKK = false;

	/**
	 * Company::$JKM
	 *
	 * @var bool
	 */
	public bool $JKM = false;

	/**
	 * Company::$JHT
	 *
	 * @var bool
	 */
	public bool $JHT = false;

	/**
	 * Company::$JIP
	 *
	 * @var bool
	 */
	public bool $JIP = false;

	/**
	 * Company::$riskGrade
	 *
	 * @var int
	 */
	public int $riskGrade = 2;

	/**
	 * Company::$absentPenalty
	 *
	 * @var int
	 */
	public int $absentPenalty = 0;

	/**
	 * Company::$latetimePenalty
	 *
	 * @var int
	 */
	public int $latetimePenalty = 0;
}