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
 * @file   TelescopeTagRepository
 * @date   25/08/2020 12:35
 */

namespace App\Repositories\Eloquent;

use App\Models\TelescopeEntryTag;
use App\Repositories\TelescopeTagRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class TelescopeTagRepository
 *
 * @package App\Repositories\Eloquent\Master
 */
class TelescopeTagRepository extends RepositoryBase implements TelescopeTagRepositoryInterface {
	public function __construct(TelescopeEntryTag $model) {
		parent::__construct($model, '{{ connection }}');
	}
}
