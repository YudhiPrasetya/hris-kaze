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

use App\Models\Attendance;
use App\Repositories\AttendanceRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class AttendanceRepository
 *
 * @package App\Repositories\Eloquent
 */
class AttendanceRepository extends RepositoryBase implements AttendanceRepositoryInterface {
	public function __construct(Attendance $model) {
		parent::__construct($model);
	}
}
