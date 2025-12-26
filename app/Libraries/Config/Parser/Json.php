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
 * @file   Json.php
 * @date   24/08/2020 15.43
 */

namespace App\Libraries\Config\Parser;

use App\Contracts\ParserInterface;
use App\Exceptions\ParseException;


class Json implements ParserInterface {

	/**
	 * {@inheritDoc}
	 */
	public static function getSupportedExtensions(): array {
		return ['json'];
	}

	/**
	 * {@inheritDoc}
	 * Parses an JSON file as an array
	 *
	 * @param string $filename File to parse
	 *
	 * @return array|null
	 * @throws ParseException If there is an error parsing the JSON file
	 */
	public function parseFile(string $filename): ?array {
		$data = json_decode(file_get_contents($filename), true);

		return $this->parse($data, $filename);
	}

	/**
	 * Completes parsing of JSON data
	 *
	 * @param mixed|null  $data
	 * @param string|null $filename
	 *
	 * @return array|null
	 * @throws ParseException If there is an error parsing the JSON data
	 */
	protected function parse($data = null, ?string $filename = null): ?array {
		if (json_last_error() !== JSON_ERROR_NONE) {
			$error_message = 'Syntax error';
			if (function_exists('json_last_error_msg')) {
				$error_message = json_last_error_msg();
			}

			$error = [
				'message' => $error_message,
				'type'    => json_last_error(),
				'file'    => $filename,
			];
			throw new ParseException($error);
		}

		return $data;
	}

	/**
	 * {@inheritDoc}
	 * Parses an JSON string as an array
	 *
	 * @param string $config String to parse
	 *
	 * @return array|null
	 * @throws ParseException If there is an error parsing the JSON string
	 */
	public function parseString(string $config): ?array {
		$data = json_decode($config, true);

		return $this->parse($data);
	}
}