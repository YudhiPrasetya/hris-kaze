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
 * @file   Allowances.php
 * @date   2021-08-12 12:15:37
 */

namespace App\Libraries\Payroll\DataStructures\Employee;

use O2System\Spl\Datastructures\SplArrayObject;

class Allowances extends SplArrayObject {
    /**
     * Allowances::BPJSKesehatan
     * 
     * @var int
     */
    public int $BPJSKesehatan = 0;

    /**
     * Allowances::$JP
     * 
     * @var int
     */
    public int $JP = 0;

    /**
     * Allowances::$JHT
     * 
     * @var int
     */
    public int $JHT = 0;

    /**
     * Allowances::$transportAllowance
     * @var int
     */
    public int $transportAllowance = 0;

    /**
     * Allowances::$mealAllowances
     * 
     * @var int
     */
    public int $mealAllowances = 0;

    /**
     * Allowances::$otherAllowance
     * 
     * @var int
     */
    public int $otherAllowance = 0;

}