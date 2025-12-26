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
 * @file   FilterAlreadyBindedException.php
 * @date   28/08/2020 14.41
 */

namespace App\Exceptions;

use Exception;


class FilterAlreadyBindedException extends Exception {
	public function __construct($filter, $field) {
		$message = sprintf('Filter with name: %filter already assigned for field: %field', $filter, $field);

		parent::__construct($message);
	}
}