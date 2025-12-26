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
 * @file   AssignmentPartRepository
 * @date   25/03/2021 16:10
 */

namespace App\Repositories\Eloquent;

use App\Models\AssignmentPart;
use App\Repositories\AssignmentPartRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class AssignmentPartRepository
 *
 * @package App\Repositories\Eloquent
 */
class AssignmentPartRepository extends RepositoryBase implements AssignmentPartRepositoryInterface {
	public function __construct(AssignmentPart $model) {
		parent::__construct($model);
	}
}
