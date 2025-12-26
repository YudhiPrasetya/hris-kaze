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
 * @file   UserInfoRepository
 * @date   05/08/2021 05:03
 */

namespace App\Repositories\Eloquent;

use App\Models\UserInfo;
use App\Repositories\UserInfoRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class UserInfoRepository
 *
 * @package App\Repositories\Eloquent
 */
class UserInfoRepository extends RepositoryBase implements UserInfoRepositoryInterface {
	public function __construct(UserInfo $model) {
		parent::__construct($model);
	}
}
