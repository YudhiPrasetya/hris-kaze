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
 * @file   MethodNotFoundException.php
 * @date   17/09/2020 00.22
 */

namespace App\Exceptions;

class MethodNotFoundException extends \Exception {
	public function __construct(...$methods) {
		parent::__construct(sprintf("Class %s doesn't have any of this methods %s.", get_called_class(), implode(', ', $methods)));
	}
}