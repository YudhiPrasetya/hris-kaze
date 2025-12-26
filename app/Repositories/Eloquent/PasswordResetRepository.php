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
 * @file   PasswordResetRepository
 * @date   03/09/2020 09:02
 */

namespace App\Repositories\Eloquent;

use App\Models\PasswordReset;
use App\Repositories\PasswordResetRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class PasswordResetRepository
 *
 * @package App\Repositories\Eloquent\Master
 */
class PasswordResetRepository extends RepositoryBase implements PasswordResetRepositoryInterface {
	public function __construct(PasswordReset $model) {
		parent::__construct($model);
	}
}
