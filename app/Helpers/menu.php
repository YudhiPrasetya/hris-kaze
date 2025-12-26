<?php
/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   menu.php
 * @date   27/08/2020 02.49
 */

use App\Contracts\Menu as MenuContract;
use App\Managers\Menu\Menu;


if (!function_exists('menu')) {
	function menu(): Menu {
		return app()->get(MenuContract::class);
	}
}