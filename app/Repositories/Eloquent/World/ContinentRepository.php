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
 * @file   ContinentRepository
 * @date   03/09/2020 09:02
 */

namespace App\Repositories\Eloquent\World;

use App\Models\World\Continent;
use App\Repositories\World\ContinentRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class ContinentRepository
 *
 * @package App\Repositories\Eloquent\World
 */
class ContinentRepository extends RepositoryBase implements ContinentRepositoryInterface {
	public function __construct(Continent $model) {
		parent::__construct($model);
	}
}
