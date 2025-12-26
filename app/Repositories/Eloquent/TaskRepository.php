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
 * @file   TaskRepository
 * @date   22/03/2021 16:15
 */

namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Repositories\TaskRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class TaskRepository
 *
 * @package App\Repositories\Eloquent
 */
class TaskRepository extends RepositoryBase implements TaskRepositoryInterface {
	public function __construct(Task $model) {
		parent::__construct($model);
	}
}
