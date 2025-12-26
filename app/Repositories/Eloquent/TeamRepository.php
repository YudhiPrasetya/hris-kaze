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
 * @file   TeamRepository
 * @date   17/09/2020 02:11
 */

namespace App\Repositories\Eloquent;

use App\Models\Team;
use App\Repositories\TeamRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class TeamRepository
 *
 * @package App\Repositories\Eloquent
 */
class TeamRepository extends RepositoryBase implements TeamRepositoryInterface {
	public function __construct(Team $model) {
		parent::__construct($model);
	}
}
