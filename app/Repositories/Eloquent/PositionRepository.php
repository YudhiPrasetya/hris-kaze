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
 * @file   PositionRepository
 * @date   17/03/2021 13:51
 */

namespace App\Repositories\Eloquent;

use App\Models\Position;
use App\Repositories\PositionRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class PositionRepository
 *
 * @package App\Repositories\Eloquent
 */
class PositionRepository extends RepositoryBase implements PositionRepositoryInterface {
	public function __construct(Position $model) {
		parent::__construct($model);
	}
}
