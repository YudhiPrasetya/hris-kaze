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
 * @file   DistrictRepository
 * @date   03/09/2020 09:02
 */

namespace App\Repositories\Eloquent\World;

use App\Models\World\District;
use App\Repositories\World\DistrictRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class DistrictRepository
 *
 * @package App\Repositories\Eloquent\World
 */
class DistrictRepository extends RepositoryBase implements DistrictRepositoryInterface {
	public function __construct(District $model) {
		parent::__construct($model);
	}
}
