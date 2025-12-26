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
 * @file   UserRepository
 * @date   03/09/2020 09:02
 */

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Eloquent\RepositoryBase;
use App\Repositories\UserRepositoryInterface;


/**
 * Class UserRepository
 *
 * @package App\Repositories\Eloquent\Master
 */
class UserRepository extends RepositoryBase implements UserRepositoryInterface {
	/**
	 * UserRepository constructor.
	 *
	 * @param \App\Models\User $model
	 *
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(User $model) {
		parent::__construct($model);
	}
}
