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
 * @file   AuditRepository
 * @date   03/09/2020 09:02
 */

namespace App\Repositories\Eloquent;

use App\Models\Audit;
use App\Repositories\AuditRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class AuditRepository
 *
 * @package App\Repositories\Eloquent\Master
 */
class AuditRepository extends RepositoryBase implements AuditRepositoryInterface {
	public function __construct(Audit $model) {
		parent::__construct($model);
	}
}
