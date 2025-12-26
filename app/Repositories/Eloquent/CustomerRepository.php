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
 * @file   CustomerRepository
 * @date   25/03/2021 16:10
 */

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\CustomerRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class CustomerRepository
 *
 * @package App\Repositories\Eloquent
 */
class CustomerRepository extends RepositoryBase implements CustomerRepositoryInterface {
	public function __construct(Customer $model) {
		parent::__construct($model);
	}
}
