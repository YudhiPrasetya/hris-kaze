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
 * @file   CustomerMachineRepository
 * @date   05/08/2021 05:03
 */

namespace App\Repositories\Eloquent;

use App\Models\CustomerMachine;
use App\Repositories\CustomerMachineRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class CustomerMachineRepository
 *
 * @package App\Repositories\Eloquent
 */
class CustomerMachineRepository extends RepositoryBase implements CustomerMachineRepositoryInterface {
	public function __construct(CustomerMachine $model) {
		parent::__construct($model);
	}
}
