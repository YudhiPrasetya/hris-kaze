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
 * @file   RoleRepository
 * @date   17/09/2020 02:11
 */

namespace App\Repositories\Eloquent;

use App\Models\Role;
use App\Repositories\RoleRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class RoleRepository
 *
 * @package App\Repositories\Eloquent\Master
 */
class RoleRepository extends RepositoryBase implements RoleRepositoryInterface {
	public function __construct(Role $model) {
		parent::__construct($model, '');
	}
}
