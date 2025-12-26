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
 * @file   AttendanceRepository
 * @date   22/03/2021 16:08
 */

namespace App\Repositories\Eloquent;

use App\Models\AttendancePermit;
use App\Repositories\AttendanceRepositoryInterface;


/**
 * Class AttendancePermitRepository
 *
 * @package App\Repositories\Eloquent
 */
class AttendancePermitRepository extends RepositoryBase implements AttendanceRepositoryInterface {
	public function __construct(AttendancePermit $model) {
		parent::__construct($model);
	}
}
