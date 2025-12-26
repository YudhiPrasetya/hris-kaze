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
 * @file   TimezoneRepository
 * @date   03/09/2020 09:02
 */

namespace App\Repositories\Eloquent\World;

use App\Models\World\Timezone;
use App\Repositories\World\TimezoneRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class TimezoneRepository
 *
 * @package App\Repositories\Eloquent\World
 */
class TimezoneRepository extends RepositoryBase implements TimezoneRepositoryInterface {
	public function __construct(Timezone $model) {
		parent::__construct($model);
	}
}
