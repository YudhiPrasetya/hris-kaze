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
 * @file   FingerprintRepository
 * @date   05/08/2021 05:03
 */

namespace App\Repositories\Eloquent;

use App\Models\Fingerprint;
use App\Repositories\FingerprintRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class FingerprintRepository
 *
 * @package App\Repositories\Eloquent
 */
class FingerprintRepository extends RepositoryBase implements FingerprintRepositoryInterface {
	public function __construct(Fingerprint $model) {
		parent::__construct($model);
	}
}
