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
 * @file   LanguageRepository
 * @date   03/09/2020 09:02
 */

namespace App\Repositories\Eloquent\World;

use App\Models\World\Language;
use App\Repositories\World\LanguageRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class LanguageRepository
 *
 * @package App\Repositories\Eloquent\World
 */
class LanguageRepository extends RepositoryBase implements LanguageRepositoryInterface {
	public function __construct(Language $model) {
		parent::__construct($model);
	}
}
