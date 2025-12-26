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
 * @file   ThemeNotFoundException.php
 * @date   24/08/2020 15.22
 */

namespace App\Exceptions;

use Throwable;


class ThemeNotFoundException extends \Exception {
	public function __construct(?string $name) {
		parent::__construct(
			"Theme [ $name ] not found! Maybe you're missing a " . config('theme.config.name') . ' file.'
		);
	}
}