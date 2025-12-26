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
 * @file   InvalidInstanceException.php
 * @date   28/08/2020 14.38
 */

namespace App\Exceptions;

use App\Managers\Form\Filters\FilterInterface;
use Exception;
use Throwable;


class InvalidInstanceException extends Exception {
	public function __construct($message = "", $code = 0, Throwable $previous = null) {
		$message = 'Filter object must implement ' . FilterInterface::class;
		parent::__construct($message, $code, $previous);
	}
}