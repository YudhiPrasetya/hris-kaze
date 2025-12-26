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
 * @file   Request.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Traits;

use Illuminate\Support\Str;


trait Request {
	public function isSubmitted(): bool {
		$methods = ['POST', 'PUT', 'DELETE', 'PATCH'];

		return in_array(Str::upper($this->method()), $methods);
	}
}
