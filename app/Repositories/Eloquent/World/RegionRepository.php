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
 * @file   RegionRepository
 * @date   03/09/2020 09:02
 */

namespace App\Repositories\Eloquent\World;

use App\Models\World\Region;
use App\Repositories\World\RegionRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class RegionRepository
 *
 * @package App\Repositories\Eloquent\World
 */
class RegionRepository extends RepositoryBase implements RegionRepositoryInterface {
	public function __construct(Region $model) {
		parent::__construct($model);
	}
}
