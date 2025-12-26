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
use App\Repositories\AnnualLeaveRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class AnnualLeaveRepository
 *
 * @package App\Repositories\Eloquent
 */
class AnnualLeaveRepository extends RepositoryBase implements AnnualLeaveRepositoryInterface {
	public function __construct(AnnualLeave $model) {
		parent::__construct($model);
	}
}
