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
 * @file   MembershipRepository
 * @date   17/09/2020 02:11
 */

namespace App\Repositories\Eloquent;

use App\Models\Membership;
use App\Repositories\MembershipRepositoryInterface;


/**
 * Class MembershipRepository
 *
 * @package App\Repositories\Eloquent
 */
class MembershipRepository extends RepositoryBase implements MembershipRepositoryInterface {
	public function __construct(Membership $model) {
		parent::__construct($model);
	}
}
