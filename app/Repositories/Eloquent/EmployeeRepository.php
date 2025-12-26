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
 * @file   EmployeeRepository
 * @date   17/03/2021 13:51
 */

namespace App\Repositories\Eloquent;

use App\Models\Employee;
use App\Repositories\EmployeeRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class EmployeeRepository
 *
 * @package App\Repositories\Eloquent
 */
class EmployeeRepository extends RepositoryBase implements EmployeeRepositoryInterface {
	public function __construct(Employee $model) {
		parent::__construct($model);
	}
}
