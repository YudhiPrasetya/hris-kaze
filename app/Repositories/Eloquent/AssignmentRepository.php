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
 * @file   AssignmentRepository
 * @date   22/03/2021 16:08
 */

namespace App\Repositories\Eloquent;

use App\Models\Assignment;
use App\Repositories\AssignmentRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class AssignmentRepository
 *
 * @package App\Repositories\Eloquent
 */
class AssignmentRepository extends RepositoryBase implements AssignmentRepositoryInterface {
	public function __construct(Assignment $model) {
		parent::__construct($model);
	}
}
