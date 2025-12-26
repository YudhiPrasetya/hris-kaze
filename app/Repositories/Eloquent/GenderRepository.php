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
 * @file   GenderRepository
 * @date   05/08/2021 05:03
 */

namespace App\Repositories\Eloquent;

use App\Models\Gender;
use App\Repositories\GenderRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class GenderRepository
 *
 * @package App\Repositories\Eloquent
 */
class GenderRepository extends RepositoryBase implements GenderRepositoryInterface {
	public function __construct(Gender $model) {
		parent::__construct($model);
	}
}
