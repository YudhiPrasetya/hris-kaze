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
 * @file   AnnualLeaveRepository
 * @date   22/03/2021 16:08
 */

namespace App\Repositories\Eloquent;

use App\Models\AnnualLeave;
use App\Models\WorkingShift;
use App\Repositories\AnnualLeaveRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class WorkingShiftRepository
 *
 * @package App\Repositories\Eloquent
 */
class WorkingShiftRepository extends RepositoryBase implements AnnualLeaveRepositoryInterface {
	public function __construct(WorkingShift $model) {
		parent::__construct($model);
	}
}
