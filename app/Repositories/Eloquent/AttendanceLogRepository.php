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
 * @file   AttendanceLogRepository
 * @date   05/08/2021 05:03
 */

namespace App\Repositories\Eloquent;

use App\Models\AttendanceLog;
use App\Repositories\AttendanceLogRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class AttendanceLogRepository
 *
 * @package App\Repositories\Eloquent
 */
class AttendanceLogRepository extends RepositoryBase implements AttendanceLogRepositoryInterface {
	public function __construct(AttendanceLog $model) {
		parent::__construct($model);
	}
}
