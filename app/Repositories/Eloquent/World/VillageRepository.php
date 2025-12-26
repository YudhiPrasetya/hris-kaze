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
 * @file   VillageRepository
 * @date   03/09/2020 09:02
 */

namespace App\Repositories\Eloquent\World;

use App\Models\World\Village;
use App\Repositories\World\VillageRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class VillageRepository
 *
 * @package App\Repositories\Eloquent\World
 */
class VillageRepository extends RepositoryBase implements VillageRepositoryInterface {
	public function __construct(Village $model) {
		parent::__construct($model);
	}
}
