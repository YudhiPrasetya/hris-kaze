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
 * @file   ParserInterface.php
 * @date   24/08/2020 09.42
 */

namespace App\Contracts;

interface ParserInterface {
	/**
	 * Returns an array of allowed file extensions for this parser
	 *
	 * @return array
	 */
	public static function getSupportedExtensions(): array;

	/**
	 * Parses a configuration from file `$filename` and gets its contents as an array
	 *
	 * @param string $filename
	 *
	 * @return array|null
	 */
	public function parseFile(string $filename): ?array;

	/**
	 * Parses a configuration from string `$config` and gets its contents as an array
	 *
	 * @param string $config
	 *
	 * @return array|null
	 */
	public function parseString(string $config): ?array;
}