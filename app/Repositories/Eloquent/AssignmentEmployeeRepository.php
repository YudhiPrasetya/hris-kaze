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
 * @file   AssignmentEmployeeRepository
 * @date   25/03/2021 16:10
 */

namespace App\Repositories\Eloquent;

use App\Models\AssignmentEmployee;
use App\Repositories\AssignmentEmployeeRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class AssignmentEmployeeRepository
 *
 * @package App\Repositories\Eloquent
 */
class AssignmentEmployeeRepository extends RepositoryBase implements AssignmentEmployeeRepositoryInterface {
	public function __construct(AssignmentEmployee $model) {
		parent::__construct($model);
	}
}
