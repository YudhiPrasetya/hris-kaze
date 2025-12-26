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
 * @file   PayrollCalculator.php
 * @date   2021-08-12 12:12:7
 */

namespace App\Libraries\Payroll;

use App\Libraries\Payroll\DataStructures;
use App\Libraries\Payroll\Taxes\Pph21;
use App\Libraries\Payroll\Taxes\Pph23;
use App\Libraries\Payroll\Taxes\Pph26;
use O2System\Spl\Datastructures\SplArrayObject;


class PayrollCalculator {
	public const PPH21 = 21;

	public const PPH23 = 23;

	public const PPH26 = 26;

	/**
	 * PayrollCalculator::NETT_CALCULATION
	 *
	 * PPh 21 ditanggung oleh perusahaan atau penyedia kerja.
	 *
	 * @var string
	 */
	const NETT_CALCULATION = 'NETT';

	/**
	 * PayrollCalculator::GROSS_CALCULATION
	 *
	 * PPh 21 ditanggung oleh pekerja/karyawan.
	 *
	 * @var string
	 */
	const GROSS_CALCULATION = 'GROSS';

	/**
	 * PayrollCalculator::GROSS_UP_CALCULATION
	 *
	 * Tanggungan PPh 21 ditambahkan sebagai tunjangan pekerja/karyawan.
	 *
	 * @var string
	 */
	const GROSS_UP_CALCULATION = 'GROSSUP';

	/**
	 * PayrollCalculator::$provisions
	 *
	 * @var \App\Libraries\Payroll\DataStructures\Provisions
	 */
	public DataStructures\Provisions $provisions;

	/**
	 * PayrollCalculator::$employee
	 *
	 * @var \App\Libraries\Payroll\DataStructures\Employee
	 */
	public DataStructures\Employee $employee;

	public DataStructures\Company $company;

	/**
	 * PayrollCalculator::$taxNumber
	 *
	 * @var int
	 */
	public int $taxNumber = 21;

	/**
	 * PayrollCalculator::$method
	 *
	 * @var string
	 */
	public string $method = 'NETT';

	/**
	 * PayrollCalculator::$result
	 *
	 * @var SplArrayObject
	 */
	public SplArrayObject $result;

	// ------------------------------------------------------------------------

	/**
	 * PayrollCalculator::__construct
	 *
	 * @param array $data
	 */
	public function __construct() {
		$this->provisions = new DataStructures\Provisions();
		$this->employee = new DataStructures\Employee();
		$this->company = new DataStructures\Company();
		$this->result = new SplArrayObject([
			'earnings'    => new SplArrayObject([
				'base'           => 0,
				'fixedAllowance' => 0,
				'annually'       => new SplArrayObject([
					'nett'  => 0,
					'gross' => 0,
				]),
			]),
			'takeHomePay' => 0,
			'method'      => 0,
		]);
	}

	/**
	 * PayrollCalculator::getCalculation
	 *
	 * @return \O2System\Spl\DataStructures\SplArrayObject
	 */
	public function getCalculation(): ?SplArrayObject {
		$this->result->method = $this->method;

		if ($this->taxNumber === self::PPH21) {
			return $this->calculateBaseOnPph21();
		}

		if ($this->taxNumber === self::PPH23) {
			return $this->calculateBaseOnPph23();
		}

		if ($this->taxNumber === self::PPH26) {
			return $this->calculateBaseOnPph26();
		}

		return null;
	}

	/**
	 * PayrollCalculator::calculateBaseOnPph21
	 *
	 * @return SplArrayObject
	 */
	private function calculateBaseOnPph21(): SplArrayObject {
		// Gaji + Penghasilan teratur
		$this->result->earnings->base = $this->employee->earnings->base;
		$this->result->earnings->baseTotal = $this->result->earnings->base + $this->employee->earnings->fixedAllowance;
		$this->result->earnings->fixedAllowance = $this->employee->earnings->fixedAllowance;

		// Penghasilan bruto bulanan merupakan gaji pokok ditambah tunjangan tetap
		$this->result->earnings->gross = $this->result->earnings->base + $this->employee->earnings->fixedAllowance;

		if ($this->employee->calculateHolidayAllowance > 0) {
			$this->result->earnings->holidayAllowance = $this->employee->calculateHolidayAllowance * $this->result->earnings->gross;
		}

		// Penghasilan tidak teratur overtime
		if ($this->provisions->company->calculateOvertime === true) {
			if ($this->provisions->state->overtimeRegulationCalculation) {
				//  Berdasarkan Kepmenakertrans No. 102/MEN/VI/2004
				if ($this->employee->presences->overtime > 1) {
					$overtime1stHours = 1 * 1.5 * 1 / 173 * $this->result->earnings->gross;
					$overtime2ndHours = ($this->employee->presences->overtime - 1) * 2 * 1 / 173 * $this->result->earnings->gross;
					$this->result->earnings->overtime = $overtime1stHours + $overtime2ndHours;
				}
				else {
					$this->result->earnings->overtime = $this->employee->presences->overtime * 1.5 * 1 / 173 * $this->result->earnings->gross;
				}
			}
			else {
				if ($this->provisions->company->overtimeRate > 0) {
					$this->provisions->company->overtimeRate = floor($this->employee->presences->overtime / $this->provisions->company->numOfWorkingDays /
					                                                 $this->provisions->company->numOfWorkingHours);
				}

				$this->result->earnings->overtime = $this->employee->presences->overtime * $this->provisions->company->overtimeRate;
			}

			$this->result->earnings->overtime = floor($this->result->earnings->overtime);

			// Lembur ditambahkan sebagai pendapatan bruto bulanan
			$this->result->earnings->gross += $this->result->earnings->overtime;
		}

		// split shifts
		if ($this->provisions->company->calculateSplitShifts) {
			$this->result->earnings->splitShifts = $this->provisions->company->splitShiftsRate * $this->employee->presences->splitShifts;

			// Split Shift ditambahkan sebagai pendapatan bruto bulanan
			$this->result->earnings->gross += $this->result->earnings->splitShifts;
		}

		$this->result->earnings->annually->gross = $this->result->earnings->gross * 12;

		if ($this->employee->permanentStatus === false) {
			$this->company->allowances->BPJSKesehatan = 0;
			$this->employee->deductions->BPJSKesehatan = 0;

			$this->employee->allowances->JKK = 0;
			$this->employee->allowances->JKM = 0;

			$this->employee->allowances->JHT = 0;
			$this->employee->deductions->JHT = 0;

			$this->employee->allowances->JIP = 0;
			$this->employee->deductions->JIP = 0;

			// Set result allowances, bonus, deductions
			$this->result->offsetSet('allowances', $this->employee->allowances);
			$this->result->offsetSet('bonus', $this->employee->bonus);
			$this->result->offsetSet('deductions', $this->employee->deductions);

			// Pendapatan bersih
			$this->result->earnings->nett = $this->result->earnings->gross + $this->result->allowances->getSum() - $this->result->deductions->getSum();
			$this->result->earnings->annually->nett = $this->result->earnings->nett * 12;

			$this->result->offsetSet('taxable', (new Pph21($this))->result);

			// Pengurangan Penalty
			$this->employee->deductions->offsetSet('penalty',
				new SplArrayObject([
					'late'   => $this->employee->presences->latetime * $this->provisions->company->latetimePenalty,
					'absent' => $this->employee->presences->absentDays * $this->provisions->company->absentPenalty,
				]));

			// Tunjangan Hari Raya
			if ($this->employee->earnings->holidayAllowance > 0) {
				$this->result->allowances->offsetSet('holiday', $this->employee->earnings->holidayAllowance);
			}

			$this->result->takeHomePay = $this->result->earnings->nett + $this->employee->earnings->holidayAllowance + $this->employee->bonus->getSum() -
			                             $this->employee->deductions->penalty->getSum();
			$this->result->allowances->offsetSet('positionTax', 0);
			$this->result->allowances->offsetSet('pph21Tax', 0);
		}
		else {
			if ($this->provisions->company->calculateBPJSKesehatan === true) {
				// Calculate BPJS Kesehatan Allowance & Deduction
				// $this->company->allowances->BPJSKesehatan = $this->result->earnings->gross * (4 / 100);
				// $this->employee->deductions->BPJSKesehatan = $this->result->earnings->gross * (1 / 100);
				
				// Guarantee is covered by employee paid by company
				if ($this->employee->employeeGuarantee) {
					$this->company->allowances->BPJSKesehatan = $this->result->earnings->base * (5 / 100);
					$this->employee->deductions->BPJSKesehatan = 0;
					// Maximum number of dependents family is 5
					if ($this->employee->numOfDependentsFamily > 5) {
						$this->employee->deductions->BPJSKesehatan += ($this->employee->deductions->BPJSKesehatan * ($this->employee->numOfDependentsFamily - 5));
					}
				} else {
					$this->company->allowances->BPJSKesehatan = $this->result->earnings->base * (4 / 100);
					$this->employee->deductions->BPJSKesehatan = $this->result->earnings->base * (1 / 100);
					// Maximum number of dependents family is 5
					if ($this->employee->numOfDependentsFamily > 5) {
						$this->employee->deductions->BPJSKesehatan += ($this->employee->deductions->BPJSKesehatan * ($this->employee->numOfDependentsFamily - 5));
					}
				}
			}

			if ($this->provisions->company->JKK === true) {
				$this->company->allowances->JKK = ceil($this->result->earnings->base * ($this->provisions->state->getJKKRiskGradePercentage($this->provisions->company->riskGrade) / 100));
				// $this->employee->allowances->JKK = $this->company->allowances->JKK;
		
				/*
				if ($this->result->earnings->gross < $this->provisions->state->highestWage) {
					$this->employee->allowances->JKK = $this->result->earnings->base *
					                                   ($this->provisions->state->getJKKRiskGradePercentage($this->provisions->company->riskGrade) / 100);
				}
				elseif ($this->result->earnings->gross >= $this->provisions->state->provinceMinimumWage &&
				        $this->result->earnings->gross >= $this->provisions->state->highestWage) {
					$this->employee->allowances->JKK = $this->provisions->state->highestWage *
					                                   $this->provisions->state->getJKKRiskGradePercentage($this->provisions->company->riskGrade);
				}
				*/
			}

			if ($this->provisions->company->JKM === true) {
				$this->company->allowances->JKM = $this->result->earnings->base * (0.30 / 100);
				/*
				if ($this->result->earnings->gross < $this->provisions->state->highestWage) {
					$this->employee->allowances->JKM = $this->result->earnings->gross * (0.30 / 100);
				}
				elseif ($this->result->earnings->gross >= $this->provisions->state->provinceMinimumWage &&
				        $this->result->earnings->gross >= $this->provisions->state->highestWage) {
					$this->employee->allowances->JKM = $this->provisions->state->highestWage * (0.30 / 100);
				}
				*/
			}

			if ($this->provisions->company->JHT === true) {
				
				// Guarantee is covered by employee paid by company
				if ($this->employee->employeeGuarantee) {
					$this->company->allowances->JHT = $this->result->earnings->base * (5.7 / 100);
					$this->employee->deductions->JHT = 0;
				} else {
					$this->company->allowances->JHT = $this->result->earnings->base * (3.7 / 100);
					$this->employee->deductions->JHT = $this->result->earnings->base * (2 / 100);
				}
				/*
				if ($this->result->earnings->gross < $this->provisions->state->highestWage) {
					$this->company->allowances->JHT = $this->result->earnings->gross * (3.7 / 100);
					$this->employee->deductions->JHT = $this->result->earnings->gross * (2 / 100);
				}
				elseif ($this->result->earnings->gross >= $this->provisions->state->provinceMinimumWage &&
				        $this->result->earnings->gross >= $this->provisions->state->highestWage) {
					$this->company->allowances->JHT = $this->provisions->state->highestWage * (3.7 / 100);
					$this->employee->deductions->JHT = $this->provisions->state->highestWage * (2 / 100);
				}
				*/
			}

			if ($this->provisions->company->JIP === true) {

				// Guarantee is covered by employee paid by company
				if ($this->employee->employeeGuarantee) {
					$this->company->allowances->JIP = $this->result->earnings->base * (3 / 100);
					$this->employee->deductions->JIP = 0;
				} else {
					$this->company->allowances->JIP = $this->result->earnings->base * (2 / 100);
					$this->employee->deductions->JIP = $this->result->earnings->base * (1 / 100);
				}
				/*
				if ($this->result->earnings->gross < $this->provisions->state->highestWage) {
					$this->company->allowances->JIP = $this->result->earnings->gross * (2 / 100);
					$this->employee->deductions->JIP = $this->result->earnings->gross * (1 / 100);
				}
				elseif ($this->result->earnings->gross >= $this->provisions->state->provinceMinimumWage &&
				        $this->result->earnings->gross >= $this->provisions->state->highestWage) {
					$this->company->allowances->JIP = 7000000 * (2 / 100);
					$this->employee->deductions->JIP = 7000000 * (1 / 100);
				}
				*/
			}

			// Set result allowances, bonus, deductions
			$this->result->offsetSet('allowances', $this->employee->allowances);
			$this->result->offsetSet('bonus', $this->employee->bonus);
			$this->result->offsetSet('deductions', $this->employee->deductions);

			// set deduction presence if not presence
			//$unWork = $this->provisions->company->numOfWorkingDays - $this->employee->presences->workDays;

			//if ($unWork > 0) {
			//	$this->result->deductions->offsetSet('presence',
			//		$this->employee->earnings->base / $this->provisions->company->numOfWorkingDays * $unWork
			//	);
			//}

			// gross
			$premi = 0;
			if ($this->employee->presences->absentDays > 2) {
				$premi = 0;
			}
			else if ($this->employee->presences->absentDays === 1) {
				$premi = $this->employee->presences->rate / 2;
			}
			else {
				$premi = $this->employee->presences->rate * 1;
			}

			$overtime = $this->employee->presences->overtimeRate * $this->employee->presences->overtimeDays;

			$this->result->earnings->overtime = $overtime;
			$this->result->earnings->attendance_premium = $premi;
			// $this->employee->deductions->attendance_premium = $premi;
			
			$this->result->earnings->annually->gross = (($this->result->earnings->base +
													   $this->result->allowances->getSum() +
													   $this->company->allowances->BPJSKesehatan +
													   $this->company->allowances->JKK +
													   $this->company->allowances->JKM +
													   $premi + $overtime) - $this->employee->deductions->JIP -
													   $this->employee->deductions->JHT) * 12;
													   
			$this->result->earnings->gross = $this->result->earnings->base +
			                                 $this->result->allowances->getSum() +
											 $this->company->allowances->BPJSKesehatan +
											 $this->company->allowances->JKK +
											 $this->company->allowances->JKM +
			                                 $premi + $overtime;
			
			$monthlyPositionTax = round($this->result->earnings->annually->gross * (5 / 100));
			$thr = 0;
			
			if ($monthlyPositionTax > $this->provisions->state->provinceMinimumWage) {
				/**
				 * According to Undang-Undang Direktur Jenderal Pajak Nomor PER-32/PJ/2015 Pasal 21 ayat 3
				 * Position Deduction is 5% from Annual Gross Income
				 */
				// $monthlyPositionTax = $this->result->earnings->gross * (5 / 100);
				// $monthlyPositionTax = $this->provisions->state->provinceMinimumWage;
				$monthlyPositionTax = $this->provisions->state->provinceMaximumWage;
			} else {
				$monthlyPositionTax = ($this->result->earnings->gross + ($thr / 12)) * (5 / 100);
			}
			// Pendapatan bersih
			$this->result->earnings->annually->nett = round($this->result->earnings->gross - 
													  $monthlyPositionTax -
													  $this->employee->deductions->JIP -
													  $this->employee->deductions->JHT) * 12;										  
			
			$this->result->earnings->nett = $this->result->earnings->gross -
											$this->employee->deductions->JIP -
											$this->employee->deductions->JHT;
			// $this->result->earnings->nett = $this->result->earnings->gross + $this->result->allowances->getSum() - $this->result->deductions->getSum();
			$pph21 = new Pph21($this);
			$this->result->offsetSet('taxable', $pph21->calculate());
			// $this->result->offsetSet('company', $this->company->allowances);
			$this->result->offsetSet('company', $this->company);

			// Pengurangan Penalty
			$this->employee->deductions->offsetSet('penalty',
				new SplArrayObject([
					'late'   => $this->employee->presences->latetime * $this->provisions->company->latetimePenalty,
					'absent' => $this->employee->presences->absentDays * $this->provisions->company->absentPenalty,
				]));

			// Tunjangan Hari Raya
			if ($this->employee->earnings->holidayAllowance > 0) {
				$this->result->allowances->offsetSet('holiday', $this->employee->earnings->holidayAllowance);
			}

			switch ($this->method) {
				// Pajak ditanggung oleh perusahaan
				case self::NETT_CALCULATION:
					$this->result->takeHomePay = $this->result->earnings->nett +
					                             $this->employee->earnings->holidayAllowance +
					                             $this->employee->bonus->getSum() -
					                             $this->employee->deductions->penalty->getSum();
					$this->result->company->allowances->offsetSet('positionTax', $monthlyPositionTax);
					$this->result->company->allowances->offsetSet('pph21Tax', $this->result->taxable->liability->monthly);
					break;
				// Pajak ditanggung oleh karyawan
				case self::GROSS_CALCULATION:
					//$this->result->takeHomePay = $this->result->earnings->nett +
					//                             $this->employee->earnings->holidayAllowance +
					//                             $this->employee->bonus->getSum() -
					//                             $this->employee->deductions->penalty->getSum() -
					//                             $this->result->taxable->liability->monthly -
					//                             $monthlyPositionTax;
					$this->result->takeHomePay = $this->result->earnings->baseTotal +
					                             $this->employee->earnings->holidayAllowance +
					                             $this->employee->bonus->getSum() -
					                             $this->employee->deductions->penalty->getSum() -
					                             $this->result->taxable->liability->monthly -
												 $this->employee->deductions->BPJSKesehatan -
												 $this->employee->deductions->JIP -
												 $this->employee->deductions->JHT;
					// $this->result->deductions->offsetSet('positionTax', $monthlyPositionTax);
					$this->result->deductions->offsetSet('pph21Tax', $this->result->taxable->liability->monthly);
					// $this->result->deductions->offsetSet('rate', $this->result->taxable->rate);
					break;
				// Pajak ditanggung oleh perusahaan sebagai tunjangan pajak.
				case self::GROSS_UP_CALCULATION:
					$this->result->takeHomePay =
						$this->result->earnings->nett + $this->employee->earnings->holidayAllowance + $this->employee->bonus->getSum() -
						$this->employee->deductions->penalty->getSum();
					$this->result->deductions->offsetSet('positionTax', $monthlyPositionTax);
					$this->result->deductions->offsetSet('pph21Tax', $this->result->taxable->liability->monthly);
					$this->result->allowances->offsetSet('positionTax', $monthlyPositionTax);
					$this->result->allowances->offsetSet('pph21Tax', $this->result->taxable->liability->monthly);
					break;
			}
		}
		return $this->result;
	}

	/**
	 * PayrollCalculator::calculateBaseOnPph23
	 *
	 * @return \O2System\Spl\DataStructures\SplArrayObject
	 */
	private function calculateBaseOnPph23(): SplArrayObject {
		// Gaji + Penghasilan teratur
		$this->result->earnings->base = $this->employee->earnings->base;
		$this->result->earnings->fixedAllowance = $this->employee->earnings->fixedAllowance;

		// Penghasilan bruto bulanan merupakan gaji pokok ditambah tunjangan tetap
		$this->result->earnings->gross = $this->result->earnings->base + $this->employee->earnings->fixedAllowance;

		if ($this->employee->calculateHolidayAllowance > 0) {
			$this->result->earnings->holidayAllowance = $this->employee->calculateHolidayAllowance * $this->result->earnings->gross;
		}

		// Set result allowances, bonus, deductions
		$this->result->offsetSet('allowances', $this->employee->allowances);
		$this->result->offsetSet('bonus', $this->employee->bonus);
		$this->result->offsetSet('deductions', $this->employee->deductions);

		// Pendapatan bersih
		$this->result->earnings->nett = $this->result->earnings->gross + $this->result->allowances->getSum() - $this->result->deductions->getSum();
		$this->result->earnings->annually->nett = $this->result->earnings->nett * 12;

		$this->result->offsetSet('taxable', (new Pph23($this))->calculate());

		switch ($this->method) {
			// Pajak ditanggung oleh perusahaan
			case self::NETT_CALCULATION:
				$this->result->takeHomePay = $this->result->earnings->nett + $this->employee->earnings->holidayAllowance + $this->employee->bonus->getSum();
				break;
			// Pajak ditanggung oleh karyawan
			case self::GROSS_CALCULATION:
				$this->result->takeHomePay = $this->result->earnings->nett + $this->employee->bonus->getSum() - $this->result->taxable->liability->amount;
				$this->result->deductions->offsetSet('pph23Tax', $this->result->taxable->liability->amount);
				break;
			// Pajak ditanggung oleh perusahaan sebagai tunjangan pajak.
			case self::GROSS_UP_CALCULATION:
				$this->result->takeHomePay = $this->result->earnings->nett + $this->employee->bonus->getSum();
				$this->result->allowances->offsetSet('pph23Tax', $this->result->taxable->liability->amount);
				break;
		}

		return $this->result;
	}

	/**
	 * PayrollCalculator::calculateBaseOnPph26
	 *
	 * @return \O2System\Spl\DataStructures\SplArrayObject
	 */
	private function calculateBaseOnPph26() {
		// Gaji + Penghasilan teratur
		$this->result->earnings->base = $this->employee->earnings->base;
		$this->result->earnings->fixedAllowance = $this->employee->earnings->fixedAllowance;

		// Penghasilan bruto bulanan merupakan gaji pokok ditambah tunjangan tetap
		$this->result->earnings->gross = $this->result->earnings->base + $this->employee->earnings->fixedAllowance;

		if ($this->employee->calculateHolidayAllowance > 0) {
			$this->result->earnings->holidayAllowance = $this->employee->calculateHolidayAllowance * $this->result->earnings->gross;
		}

		// Set result allowances, bonus, deductions
		$this->result->offsetSet('allowances', $this->employee->allowances);
		$this->result->offsetSet('bonus', $this->employee->bonus);
		$this->result->offsetSet('deductions', $this->employee->deductions);

		// Pendapatan bersih
		$this->result->earnings->nett = $this->result->earnings->gross + $this->result->allowances->getSum() - $this->result->deductions->getSum();
		$this->result->earnings->annually->nett = $this->result->earnings->nett * 12;

		$this->result->offsetSet('taxable', (new Pph26($this))->calculate());

		switch ($this->method) {
			// Pajak ditanggung oleh perusahaan
			case self::NETT_CALCULATION:
				$this->result->takeHomePay = $this->result->earnings->nett + $this->employee->earnings->holidayAllowance + $this->employee->bonus->getSum();
				break;
			// Pajak ditanggung oleh karyawan
			case self::GROSS_CALCULATION:
				$this->result->takeHomePay = $this->result->earnings->nett + $this->employee->bonus->getSum() - $this->result->taxable->liability->amount;
				$this->result->deductions->offsetSet('pph26Tax', $this->result->taxable->liability->amount);
				break;
			// Pajak ditanggung oleh perusahaan sebagai tunjangan pajak.
			case self::GROSS_UP_CALCULATION:
				$this->result->takeHomePay = $this->result->earnings->nett + $this->employee->bonus->getSum();
				$this->result->allowances->offsetSet('pph26Tax', $this->result->taxable->liability->amount);
				break;
		}

		return $this->result;
	}
}