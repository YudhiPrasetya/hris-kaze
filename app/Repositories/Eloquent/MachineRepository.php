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
 * @file   MachineRepository
 * @date   27/03/2021 13:16
 */

namespace App\Repositories\Eloquent;

use App\Models\Machine;
use App\Repositories\MachineRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class MachineRepository
 *
 * @package App\Repositories\Eloquent
 */
class MachineRepository extends RepositoryBase implements MachineRepositoryInterface {
	public function __construct(Machine $model) {
		parent::__construct($model);
	}
}
