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
 * @file   TelescopeEntryTagRepository
 * @date   03/09/2020 09:02
 */

namespace App\Repositories\Eloquent;

use App\Models\TelescopeEntryTag;
use App\Repositories\TelescopeEntryTagRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class TelescopeEntryTagRepository
 *
 * @package App\Repositories\Eloquent\Master
 */
class TelescopeEntryTagRepository extends RepositoryBase implements TelescopeEntryTagRepositoryInterface {
	public function __construct(TelescopeEntryTag $model) {
		parent::__construct($model);
	}
}
