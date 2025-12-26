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
 * @file   HashManager.php
 * @date   5/09/2020 13.45
 */

namespace App\Managers\Hashing;

use Illuminate\Hashing\HashManager as HashManagerBase;


class HashManager extends HashManagerBase {
	/**
	 * Create an instance of the md5 hash Driver.
	 *
	 * @return Md5Hasher
	 */
	public function createMD5Driver() {
		return new Md5Hasher();
	}
}