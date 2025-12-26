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
 * @file   WeakPasswordException.php
 * @date   5/09/2020 21.00
 */

namespace App\Exceptions;

use Exception;
use Throwable;


class WeakPasswordException extends Exception {
	public function __construct($message = "") {
		parent::__construct($message);
	}
}