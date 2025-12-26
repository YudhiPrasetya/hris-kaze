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
 * @file   CityRepository
 * @date   03/09/2020 09:02
 */

namespace App\Repositories\Eloquent\World;

use App\Models\World\City;
use App\Repositories\World\CityRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class CityRepository
 *
 * @package App\Repositories\Eloquent\World
 */
class CityRepository extends RepositoryBase implements CityRepositoryInterface {
	public function __construct(City $model) {
		parent::__construct($model);
	}
}
