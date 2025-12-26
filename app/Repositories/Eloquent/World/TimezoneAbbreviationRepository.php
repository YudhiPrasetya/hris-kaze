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
 * @file   TimezoneAbbreviationRepository
 * @date   03/09/2020 09:02
 */

namespace App\Repositories\Eloquent\World;

use App\Models\World\TimezoneAbbreviation;
use App\Repositories\World\TimezoneAbbreviationRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class TimezoneAbbreviationRepository
 *
 * @package App\Repositories\Eloquent\World
 */
class TimezoneAbbreviationRepository extends RepositoryBase implements TimezoneAbbreviationRepositoryInterface {
	public function __construct(TimezoneAbbreviation $model) {
		parent::__construct($model);
	}
}
