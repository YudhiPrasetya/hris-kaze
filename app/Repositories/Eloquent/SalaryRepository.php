<?php
/**
 * This file is part of the Laravel project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   SalaryRepository
 * @date   05/08/2021 05:03
 */

namespace App\Repositories\Eloquent;

use App\Models\Salary;
use App\Repositories\SalaryRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class SalaryRepository
 *
 * @package App\Repositories\Eloquent
 */
class SalaryRepository extends RepositoryBase implements SalaryRepositoryInterface {
	public function __construct(Salary $model) {
		parent::__construct($model);
	}
}
