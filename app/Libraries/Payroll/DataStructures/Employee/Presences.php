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
 * @file   Presences.php
 * @date   2021-08-12 12:16:35
 */

namespace App\Libraries\Payroll\DataStructures\Employee;

class Presences {
	/**
	 * Presences::$workDays
	 *
	 * @var int
	 */
	public int $workDays = 0;

	/**
	 * Presences::$overtime
	 *
	 * @var int
	 */
	public int $overtimeDays = 0;

	public int $overtime = 0;

	public int $overtimeHours = 0;

	public int $overtimeMinutes = 0;

	public int $rate = 0;

	public int $overtimeRate = 0;

	/**
	 * Presences::$splitShifts
	 *
	 * @var int
	 */
	public int $splitShifts = 0;

	/**
	 * Presences::$latetime
	 *
	 * @var int
	 */
	public int $latetime = 0;

	/**
	 * Presences::$travelDays
	 *
	 * @var int
	 */
	public int $travelDays = 0;

	/**
	 * Presences::$leaveDays
	 *
	 * @var int
	 */
	public int $leaveDays = 0;

	/**
	 * Presences::$indisposeDays
	 *
	 * @var int
	 */
	public int $indisposeDays = 0;

	/**
	 * Presences::$absentDays
	 *
	 * @var int
	 */
	public int $absentDays = 0;

	/**
	 * Presences::__get
	 *
	 * @param string $name
	 *
	 * @return int
	 */
	public function __get(string $name) {
		if (property_exists($this, $name)) {
			return (int)$this->{$name};
		}

		return 0;
	}

	/**
	 * Presences::__set
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set(string $name, mixed $value) {
		if (is_int($value)) {
			$this->{$name} = $value;
		}
	}

	/**
	 * Presences::getCalculatedDays
	 *
	 * @return int
	 */
	public function getCalculatedDays(): int {
		return $this->workDays + $this->leaveDays + $this->indisposeDays + $this->travelDays;
	}
}