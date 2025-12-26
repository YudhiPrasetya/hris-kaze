<?php
/**
 * This file is part of the Laravel project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   TelescopeMonitorRepository
 * @date   03/09/2020 09:02
 */

namespace App\Repositories\Eloquent;

use App\Models\TelescopeMonitor;
use App\Repositories\TelescopeMonitorRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class TelescopeMonitorRepository
 *
 * @package App\Repositories\Eloquent\Master
 */
class TelescopeMonitorRepository extends RepositoryBase implements TelescopeMonitorRepositoryInterface {
	public function __construct(TelescopeMonitor $model) {
		parent::__construct($model);
	}
}
