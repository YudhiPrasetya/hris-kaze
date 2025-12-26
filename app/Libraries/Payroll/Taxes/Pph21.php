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
 * @file   Pph21.php
 * @date   2021-08-12 12:13:29
 */

namespace App\Libraries\Payroll\Taxes;

use O2System\Spl\Datastructures\SplArrayObject;


class Pph21 extends AbstractPph {
	/**
	 * PPh21::calculate
	 *
	 * @return \O2System\Spl\DataStructures\SplArrayObject
	 */
	public function calculate(): SplArrayObject {

		/**
		 * PPh21 dikenakan bagi yang memiliki penghasilan lebih dari 4500000
		 */
		if ($this->calculator->result->earnings->nett > 4500000) {
			// Annual PTKP base on number of dependents family
			$this->result->ptkp->amount = $this->calculator->provisions->state->getPtkpAmount(
				$this->calculator->employee->numOfDependentsFamily,
				$this->calculator->employee->maritalStatus
			);

			// Annual PKP (Pajak Atas Upah)
			if ($this->calculator->employee->earnings->holidayAllowance > 0 && $this->calculator->employee->bonus->getSum() === 0) {
				// Pajak Atas Upah
				$earningTax = ($this->calculator->result->earnings->annually->nett - $this->result->ptkp->amount) *
				              ($this->getRate($this->calculator->result->earnings->nett) / 100);

				// Penghasilan + THR Kena Pajak
				$this->result->pkp = ($this->calculator->result->earnings->annually->nett + $this->calculator->employee->earnings->holidayAllowance) -
				                     $this->result->ptkp->amount;

				$this->result->liability->annual = $this->result->pkp - $earningTax;
			}
			elseif ($this->calculator->employee->earnings->holidayAllowance > 0 && $this->calculator->employee->bonus->getSum() > 0) {
				// Pajak Atas Upah
				$earningTax = ($this->calculator->result->earnings->annually->nett - $this->result->ptkp->amount) *
				              ($this->getRate($this->calculator->result->earnings->nett) / 100);

				// Penghasilan + THR Kena Pajak
				$this->result->pkp = ($this->calculator->result->earnings->annually->nett + $this->calculator->employee->earnings->holidayAllowance +
				                      $this->calculator->employee->bonus->getSum()) - $this->result->ptkp->amount;
				$this->result->liability->annual = $this->result->pkp - $earningTax;
			}
			else {
				// use this if no holiday allowance
				$roundedPkp = $this->calculator->result->earnings->annually->nett - $this->result->ptkp->amount;
				if ($roundedPkp >= 0) {
					$this->result->pkp = floor(($this->calculator->result->earnings->annually->nett - $this->result->ptkp->amount) / 1000) * 1000;
				} else {
					$this->result->pkp = ceil(($this->calculator->result->earnings->annually->nett - $this->result->ptkp->amount) / 1000) * 1000;
				}
				// $this->result->liability->annual = $this->result->pkp * ($this->getRate($this->calculator->result->earnings->nett) / 100);
				$this->result->liability->annual = round($this->getRate((int)($this->result->pkp)));
			}
			
			if ($this->result->liability->annual > 0) {
				// Jika tidak memiliki NPWP dikenakan tambahan 20%
				if ($this->calculator->employee->hasNPWP === false) {
					$this->result->liability->annual += ($this->result->liability->annual * (20 / 100));
				}

				$this->result->liability->monthly = round($this->result->liability->annual / 12);
			}
			else {
				$this->result->liability->annual = 0;
				$this->result->liability->monthly = 0;
			}
		}
		
		return $this->result;
	}

	public function getRate(int $monthlyNetIncome): float|int {
		// IF (AC10<=0,0,IF(AC10<=50000000,AC10*0.05,IF(AC10<=250000000,2500000+(AC10-50000000)*0.15,IF(AC10<=500000000,32500000+(AC10-250000000)*0.25,95000000+(AC10-500000000)*0.3))))

		if ($monthlyNetIncome <= 0) {
			return 0;
		}

		// 60.000.000
		if ($monthlyNetIncome <= 60000000) {
			$this->result->rate = '5%';
			return $monthlyNetIncome * 0.05;
		}

		// 250.000.000
		if ($monthlyNetIncome <= 250000000) {
			$this->result->rate = '15%';
			return 3000000 + ($monthlyNetIncome - 60000000) * 0.15;
		}

		// 500.000.000
		if ($monthlyNetIncome <= 500000000) {
			$this->result->rate = '25%';
			return 31500000 + ($monthlyNetIncome - 250000000) * 0.25;
		}

		// 95.000.000
		$this->result->rate = '30%';
		return 94000000 + ($monthlyNetIncome - 500000000) * 0.3;
	}
}