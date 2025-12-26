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
 * @file   ZoneRepository
 * @date   03/09/2020 09:02
 */

namespace App\Repositories\Eloquent\World;

use App\Models\World\Zone;
use App\Repositories\World\ZoneRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class ZoneRepository
 *
 * @package App\Repositories\Eloquent\World
 */
class ZoneRepository extends RepositoryBase implements ZoneRepositoryInterface {
	public function __construct(Zone $model) {
		parent::__construct($model);
	}
}
