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
 * @file   DTOCache.php
 * @date   29/08/2020 04.34
 */

namespace App\Libraries\DTO;

use Closure;


class DTOCache {
	private static array $cache = [];

	public static function resolve(string $class, Closure $closure): array {
		if (!isset(self::$cache[$class])) {
			self::$cache[$class] = $closure();
		}

		return self::$cache[$class];
	}
}