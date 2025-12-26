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
 * @file   FormBuilder.php
 * @date   28/08/2020 20.26
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;


class FormBuilder extends Facade {
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	public static function getFacadeAccessor() {
		return 'laravel-form-builder';
	}
}
