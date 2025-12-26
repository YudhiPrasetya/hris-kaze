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
 * @file   State.php
 * @date   2021-08-12 12:17:2
 */

namespace App\Libraries\Payroll\DataStructures\Provisions;

class State {
	/**
	 * State::$overtimeRegulationCalculation
	 *
	 * @var bool
	 */
	public bool $overtimeRegulationCalculation = true;

	/**
	 * State::$provinceMinimumWage
	 *
	 * @var int
	 */
	public int $provinceMinimumWage = 3940972;


	/** 
	 * State::$provinceMaximumWage
	 * 
	 * @var int
	 */
	public int $provinceMaximumWage = 500000;

	/**
	 * State::$highestWage
	 *
	 * @var int
	 */
	public int $highestWage = 8000000;

	/**
	 * State::$additionalPTKPforMarriedEmployees
	 *
	 * @var int
	 */
	public int $additionalPTKPforMarriedEmployees = 4500000;

	// ------------------------------------------------------------------------

	/**
	 * State::$listOfPTKP
	 *
	 * @var array
	 */
	protected array $listOfPTKP = [
		'TK/0' => 54000000,
		'TK/1' => 58500000,
		'TK/2' => 63000000,
		'TK/3' => 67500000,

		'K/0' => 58500000,
		'K/1' => 63000000,
		'K/2' => 67500000,
		'K/3' => 72000000,

		'K/I/0' => 108000000,
		'K/I/1' => 112500000,
		'K/I/2' => 117000000,
		'K/I/3' => 121500000,
	];

	/**
	 * State::$listOfJKKRiskGradePercentage
	 *
	 * @var array
	 */
	protected array $listOfJKKRiskGradePercentage = [
		1 => 0.24,
		2 => 0.54,
		3 => 0.89,
		4 => 1.27,
		5 => 1.74,
	];

	/**
	 * State::__get
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
	 * State::__set
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
	 * State::getListOfPTKP
	 *
	 * @return array
	 */
	public function getListOfPTKP(): array {
		return $this->listOfPTKP;
	}

	/**
	 * State::getPtkp
	 *
	 * @param int $numOfDependentsFamily
	 * @param bool $married
	 *
	 * @return string
	 */
	public function getPtkp(int $numOfDependentsFamily, bool $married): string {
		if ($married) {
			if ($numOfDependentsFamily >= 3) {
				return 'K/3';
			}

			if ($numOfDependentsFamily === 2) {
				return 'K/2';
			}

			if ($numOfDependentsFamily === 1) {
				return 'K/1';
			}

			return 'K/0';
		}
		else {
			if ($numOfDependentsFamily >= 3) {
				return 'TK/3';
			}

			if ($numOfDependentsFamily === 2) {
				return 'TK/2';
			}

			if ($numOfDependentsFamily === 1) {
				return 'TK/1';
			}

			return 'TK/0';
		}
	}

	/**
	 * State::getPtkpAmount
	 *
	 * @param int $numOfDependentsFamily
	 * @param bool $married
	 *
	 * @return float
	 */
	public function getPtkpAmount(int $numOfDependentsFamily, bool $married): float|int {
		if ($married) {
			if ($numOfDependentsFamily >= 3) {
				return $this->listOfPTKP['K/3'];
			}

			if ($numOfDependentsFamily === 2) {
				return $this->listOfPTKP['K/2'];
			}

			if ($numOfDependentsFamily === 1) {
				return $this->listOfPTKP['K/1'];
			}

			return $this->listOfPTKP['K/0'];
		}
		else {
			if ($numOfDependentsFamily >= 3) {
				return $this->listOfPTKP['TK/3'];
			}

			if ($numOfDependentsFamily === 2) {
				return $this->listOfPTKP['TK/2'];
			}

			if ($numOfDependentsFamily === 1) {
				return $this->listOfPTKP['TK/1'];
			}

			return $this->listOfPTKP['TK/0'];
		}
	}

	/**
	 * State::getListOfPTKP
	 *
	 * @return array
	 */
	public function getListOfJKKRiskGradePercentage(): array {
		return $this->listOfJKKRiskGradePercentage;
	}

	/**
	 * State::getJKKRiskGradePercentage
	 *
	 * @param int $companyRiskGrade
	 *
	 * @return float
	 */
	public function getJKKRiskGradePercentage(int $companyRiskGrade): float {
		if (array_key_exists($companyRiskGrade, $this->listOfJKKRiskGradePercentage)) {
			return $this->listOfJKKRiskGradePercentage[$companyRiskGrade];
		}

		return $this->listOfJKKRiskGradePercentage[2];
	}

	/**
	 * State::getBPJSKesehatanGrade
	 *
	 * @param int $grossTotalIncome
	 *
	 * @return int
	 */
	public function getBPJSKesehatanGrade(int $grossTotalIncome): int {
		if ($grossTotalIncome <= 4000000) {
			return 2;
		}

		if ($grossTotalIncome >= 8000000) {
			return 1;
		}

		return 3;
	}
}