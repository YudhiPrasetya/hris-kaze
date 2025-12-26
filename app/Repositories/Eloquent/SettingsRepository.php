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
 * @file   SettingsRepository
 * @date   05/08/2021 01:37
 */

namespace App\Repositories\Eloquent;

use App\Models\Settings;
use App\Repositories\SettingsRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;


/**
 * Class SettingsRepository
 *
 * @package App\Repositories\Eloquent
 */
class SettingsRepository extends RepositoryBase implements SettingsRepositoryInterface {
	public function __construct(Settings $model) {
		parent::__construct($model);
	}
}
