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
 * @file   OperationLogRepository
 * @date   05/08/2021 05:03
 */

namespace App\Repositories\Eloquent;

use App\Models\OperationLog;
use App\Repositories\OperationLogRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class OperationLogRepository
 *
 * @package App\Repositories\Eloquent
 */
class OperationLogRepository extends RepositoryBase implements OperationLogRepositoryInterface {
	public function __construct(OperationLog $model) {
		parent::__construct($model);
	}
}
