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
 * @file   Pph23.php
 * @date   2021-08-12 12:13:41
 */

namespace App\Libraries\Payroll\Taxes;

use O2System\Spl\Datastructures\SplArrayObject;


class Pph23 extends AbstractPph {
	public function calculate(): SplArrayObject {
		$this->result->offsetSet('liability',
			new SplArrayObject([
				'rule'   => 23,
				'amount' => 0,
			]));
		$this->result->liability->amount = $this->calculator->employee->earnings->base * ($this->getRate($this->calculator->employee->earnings->base) / 100);

		// Jika tidak memiliki NPWP dikenakan tambahan 100%
		if ($this->calculator->employee->hasNPWP === false) {
			$this->result->liability->amount += ($this->result->liability->amount * 100 / 100);
		}

		return $this->result;
	}
}