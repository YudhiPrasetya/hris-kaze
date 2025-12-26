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
 * @file   UnableToResolveFilterException.php
 * @date   28/08/2020 14.39
 */

namespace App\Exceptions;

use Exception;


class UnableToResolveFilterException extends Exception {
	public function __construct($message = "", $code = 0, \Throwable $previous = null) {
		$message = "Passed filter can't be resolved.";
		parent::__construct($message, $code, $previous);
	}
}