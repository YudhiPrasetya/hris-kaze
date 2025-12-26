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
 * @file   CountryNeighbourRepository
 * @date   08/09/2020 12:03
 */

namespace App\Repositories\Eloquent\World;

use App\Models\World\CountryNeighbour;
use App\Repositories\World\CountryNeighbourRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class CountryNeighbourRepository
 *
 * @package App\Repositories\Eloquent\World
 */
class CountryNeighbourRepository extends RepositoryBase implements CountryNeighbourRepositoryInterface {
	public function __construct(CountryNeighbour $model) {
		parent::__construct($model, '{{ connection }}');
	}
}
