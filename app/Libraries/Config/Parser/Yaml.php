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
 * @file   Yaml.php
 * @date   24/08/2020 16.00
 */

namespace App\Libraries\Config\Parser;

use App\Contracts\ParserInterface;
use App\Exceptions\ParseException;
use Symfony\Component\Yaml\Yaml as YamlParser;


class Yaml implements ParserInterface {

	/**
	 * {@inheritDoc}
	 */
	public static function getSupportedExtensions(): array {
		return ['yaml', 'yml'];
	}

	/**
	 * {@inheritDoc}
	 * Loads a YAML/YML file as an array
	 *
	 * @param string $filename File to parse
	 *
	 * @throws ParseException If If there is an error parsing the YAML file
	 */
	public function parseFile(string $filename): ?array {
		try {
			$data = YamlParser::parseFile($filename, YamlParser::PARSE_CONSTANT);
		}
		catch (\Exception $exception) {
			throw new ParseException(
				[
					'message'   => 'Error parsing YAML file',
					'exception' => $exception,
				]
			);
		}

		return $this->parse($data, $filename);
	}

	/**
	 * Completes parsing of YAML/YML data
	 *
	 * @param mixed|null  $data
	 * @param string|null $filename
	 *
	 * @return array|null
	 */
	protected function parse($data = null, ?string $filename = null) {
		return $data;
	}

	/**
	 * {@inheritDoc}
	 * Loads a YAML/YML string as an array
	 *
	 * @param string $config String to parse
	 *
	 * @return array|null
	 * @throws ParseException If If there is an error parsing the YAML string
	 */
	public function parseString(string $config): ?array {
		try {
			$data = YamlParser::parse($config, YamlParser::PARSE_CONSTANT);
		}
		catch (\Exception $exception) {
			throw new ParseException(
				[
					'message'   => 'Error parsing YAML string',
					'exception' => $exception,
				]
			);
		}

		return $this->parse($data);
	}
}