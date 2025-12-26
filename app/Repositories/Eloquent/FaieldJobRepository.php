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
 * @file   FaieldJobRepository
 * @date   03/09/2020 09:02
 */

namespace App\Repositories\Eloquent;

use App\Models\FaieldJob;
use App\Repositories\FaieldJobRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class FaieldJobRepository
 *
 * @package App\Repositories\Eloquent\Master
 */
class FaieldJobRepository extends RepositoryBase implements FaieldJobRepositoryInterface {
	public function __construct(FaieldJob $model) {
		parent::__construct($model);
	}
}
