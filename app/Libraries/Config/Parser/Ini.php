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
 * @file   Ini.php
 * @date   24/08/2020 15.41
 */

namespace App\Libraries\Config\Parser;

use App\Contracts\ParserInterface;
use App\Exceptions\ParseException;


class Ini implements ParserInterface {

	/**
	 * {@inheritDoc}
	 */
	public static function getSupportedExtensions(): array {
		return ['ini'];
	}

	/**
	 * {@inheritDoc}
	 * Parses an INI file as an array
	 *
	 * @param string $filename File to parse
	 *
	 * @return array|null
	 * @throws ParseException If there is an error parsing the INI file
	 */
	public function parseFile(string $filename): ?array {
		$data = @parse_ini_file($filename, true);

		return $this->parse($data, $filename);
	}

	/**
	 * Completes parsing of INI data
	 *
	 * @param mixed|null  $data
	 * @param string|null $filename
	 *
	 * @return array|null
	 * @throws ParseException If there is an error parsing the INI data
	 */
	protected function parse($data = null, ?string $filename = null): ?array {
		if (!$data) {
			$error = error_get_last();

			// Parse functions may return NULL but set no error if the string contains no parsable data
			if (!is_array($error)) {
				$error["message"] = "No parsable content in data.";
			}

			$error["file"] = $filename;

			// if string contains no parsable data, no error is set, resulting in any previous error
			// persisting in error_get_last(). in php 7 this can be addressed with error_clear_last()
			if (function_exists("error_clear_last")) {
				error_clear_last();
			}

			throw new ParseException($error);
		}

		return $this->expandDottedKey($data);
	}

	/**
	 * Expand array with dotted keys to multidimensional array
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	protected function expandDottedKey(array $data): array {
		foreach ($data as $key => $value) {
			if (($found = strpos($key, '.')) !== false) {
				$newKey = substr($key, 0, $found);
				$remainder = substr($key, $found + 1);
				$expandedValue = $this->expandDottedKey([$remainder => $value]);

				if (isset($data[$newKey])) {
					$data[$newKey] = array_merge_recursive($data[$newKey], $expandedValue);
				}
				else {
					$data[$newKey] = $expandedValue;
				}

				unset($data[$key]);
			}
		}

		return $data;
	}

	/**
	 * {@inheritDoc}
	 * Parses an INI string as an array
	 *
	 * @param string $config String to parse
	 *
	 * @return array|null
	 * @throws ParseException If there is an error parsing the INI string
	 */
	public function parseString(string $config): ?array {
		$data = @parse_ini_string($config, true);

		return $this->parse($data);
	}
}