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
 * @file   Path.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Libraries;

class Path {
	public const SEPARATOR = DIRECTORY_SEPARATOR;

	public static function join(...$path) {
		return implode(Path::SEPARATOR, collect($path)->map(fn($p) => trim($p, Path::SEPARATOR))->toArray());
	}
}