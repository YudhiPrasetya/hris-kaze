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
 * @file   CurrencyRepository
 * @date   03/09/2020 09:02
 */

namespace App\Repositories\Eloquent\World;

use App\Models\World\Currency;
use App\Repositories\World\CurrencyRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class CurrencyRepository
 *
 * @package App\Repositories\Eloquent\World
 */
class CurrencyRepository extends RepositoryBase implements CurrencyRepositoryInterface {
	public function __construct(Currency $model) {
		parent::__construct($model);
	}
}
