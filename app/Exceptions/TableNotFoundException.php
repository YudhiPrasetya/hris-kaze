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
 * @file   SchemaNotFoundException.php
 * @date   18/09/2020 20.42
 */

namespace App\Exceptions;

class TableNotFoundException extends \Exception {
	public function __construct(string $table) {
		parent::__construct("Table '$table' not found!");
	}
}