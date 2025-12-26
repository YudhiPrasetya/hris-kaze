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
 * @file   MenuRepository
 * @date   14/09/2020 07:45
 */

namespace App\Repositories\Eloquent;

use App\Models\Menu;
use App\Repositories\MenuRepositoryInterface;
use App\Repositories\Eloquent\RepositoryBase;
use Illuminate\Support\Facades\Cache;


/**
 * Class MenuRepository
 *
 * @package App\Repositories\Eloquent\Master
 */
class MenuRepository extends RepositoryBase implements MenuRepositoryInterface {
	public function __construct(Menu $model) {
		parent::__construct($model);
	}

	public function parentOnly() {
		return $this->getQueryBuilder()
		            ->where(['parent_id' => null])
		            ->orderBy('level')
		            ->get();
	}
}
