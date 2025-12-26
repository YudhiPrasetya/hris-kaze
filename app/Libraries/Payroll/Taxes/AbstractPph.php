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
 * @file   AbstractPph.php
 * @date   2021-08-12 12:17:21
 */

namespace App\Libraries\Payroll\Taxes;

use App\Libraries\Payroll\PayrollCalculator;
use O2System\Spl\Datastructures\SplArrayObject;


abstract class AbstractPph {
	/**
	 * AbstractPph::$calculator
	 *
	 * @var \App\Libraries\Payroll\PayrollCalculator
	 */
	public PayrollCalculator $calculator;

	/**
	 * AbstractPph::$liability
	 *
	 * @var \O2System\Spl\DataStructures\SplArrayObject
	 */
	public SplArrayObject $result;

	/**
	 * AbstractPph::__construct
	 *
	 * @param \App\Libraries\Payroll\PayrollCalculator $calculator
	 */
	public function __construct(PayrollCalculator &$calculator) {
		$this->calculator =& $calculator;
		$this->result = new SplArrayObject([
			'ptkp'      => new SplArrayObject([
				'status' => $this->calculator->provisions->state->getPtkp($this->calculator->employee->numOfDependentsFamily,
					$this->calculator->employee->maritalStatus),
				'amount' => 0,
			]),
			'pkp'       => 0,
			'liability' => new SplArrayObject([
				'rule'    => 21,
				'monthly' => 0,
				'annual'  => 0,
			]),
			'rate' => 0
		]);
	}

	/**
	 * AbstractPph::getRate
	 *
	 * @param int $monthlyNetIncome
	 *
	 * @return float
	 */
	public function getRate(int $monthlyNetIncome): float|int {
		$rate = 5;

		if ($monthlyNetIncome > 5000000 && $monthlyNetIncome < 250000000) {
			$rate = 15;
		}
		elseif ($monthlyNetIncome > 250000000 && $monthlyNetIncome < 500000000) {
			$rate = 25;
		}
		elseif ($monthlyNetIncome > 500000000) {
			$rate = 30;
		}

		$this->result->rate = $rate . '%';

		return $rate;
	}

	/**
	 * AbstractPph::calculate
	 *
	 * @return SplArrayObject
	 */
	abstract public function calculate(): SplArrayObject;
}