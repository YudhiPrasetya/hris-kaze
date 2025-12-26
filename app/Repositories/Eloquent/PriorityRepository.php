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
 * @file   PriorityRepository
 * @date   22/03/2021 16:15
 */

namespace App\Repositories\Eloquent;

use App\Models\Priority;
use App\Repositories\PriorityRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class PriorityRepository
 *
 * @package App\Repositories\Eloquent
 */
class PriorityRepository extends RepositoryBase implements PriorityRepositoryInterface {
	public function __construct(Priority $model) {
		parent::__construct($model);
	}
}
