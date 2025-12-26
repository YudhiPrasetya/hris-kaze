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
 * @file   Xml.php
 * @date   24/08/2020 15.54
 */

namespace App\Libraries\Config\Parser;

use App\Contracts\ParserInterface;
use App\Exceptions\ParseException;


class Xml implements ParserInterface {

	/**
	 * {@inheritDoc}
	 */
	public static function getSupportedExtensions(): array {
		return ['xml'];
	}

	/**
	 * {@inheritDoc}
	 * Parses an XML file as an array
	 *
	 * @param string $filename File to parse
	 *
	 * @return array|null
	 * @throws ParseException If there is an error parsing the XML file
	 */
	public function parseFile(string $filename): ?array {
		libxml_use_internal_errors(true);
		$data = simplexml_load_file($filename, null, LIBXML_NOERROR);

		return $this->parse($data, $filename);
	}

	/**
	 * Completes parsing of XML data
	 *
	 * @param mixed|null  $data
	 * @param string|null $filename
	 *
	 * @return array|null
	 * @throws ParseException If there is an error parsing the XML data
	 */
	protected function parse($data = null, ?string $filename = null): ?array {
		if ($data === false) {
			$errors = libxml_get_errors();
			$latestError = array_pop($errors);
			$error = [
				'message' => $latestError->message,
				'type'    => $latestError->level,
				'code'    => $latestError->code,
				'file'    => $filename,
				'line'    => $latestError->line,
			];
			throw new ParseException($error);
		}

		$data = json_decode(json_encode($data), true);

		return $data;
	}

	/**
	 * {@inheritDoc}
	 * Parses an XML string as an array
	 *
	 * @param string $config String to parse
	 *
	 * @return array|null
	 * @throws ParseException If there is an error parsing the XML string
	 */
	public function parseString(string $config): ?array {
		libxml_use_internal_errors(true);
		$data = simplexml_load_string($config, null, LIBXML_NOERROR);

		return $this->parse($data);
	}
}