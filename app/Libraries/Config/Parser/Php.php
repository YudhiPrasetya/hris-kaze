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
 * @file   Php.php
 * @date   24/08/2020 15.51
 */

namespace App\Libraries\Config\Parser;

use App\Contracts\ParserInterface;
use App\Exceptions\ParseException;
use App\Exceptions\UnsupportedFormatException;


class Php implements ParserInterface {

	/**
	 * {@inheritDoc}
	 */
	public static function getSupportedExtensions(): array {
		return ['php'];
	}

	/**
	 * {@inheritDoc}
	 * Loads a PHP file and gets its' contents as an array
	 *
	 * @param string $filename File to parse
	 *
	 * @return array|null
	 * @throws ParseException             If the PHP file throws an exception
	 * @throws UnsupportedFormatException If the PHP file does not return an array
	 */
	public function parseFile(string $filename): ?array {
		// Run the fileEval the string, if it throws an exception, rethrow it
		try {
			$data = require($filename);
		}
		catch (\Exception $exception) {
			throw new ParseException(
				[
					'message'   => 'PHP file threw an exception',
					'exception' => $exception,
				]
			);
		}

		// Complete parsing
		return $this->parse($data, $filename);
	}

	/**
	 * Completes parsing of PHP data
	 *
	 * @param mixed|null  $data
	 * @param string|null $filename
	 *
	 * @return array|null
	 * @throws UnsupportedFormatException If there is an error parsing the PHP data
	 */
	protected function parse($data = null, ?string $filename = null): ?array {
		// If we have a callable, run it and expect an array back
		if (is_callable($data)) {
			$data = call_user_func($data);
		}

		// Check for array, if its anything else, throw an exception
		if (!is_array($data)) {
			throw new UnsupportedFormatException('PHP data does not return an array');
		}

		return $data;
	}

	/**
	 * {@inheritDoc}
	 * Loads a PHP string and gets its' contents as an array
	 *
	 * @param string $config String to parse
	 *
	 * @return array|null
	 * @throws ParseException             If the PHP string throws an exception
	 * @throws UnsupportedFormatException If the PHP string does not return an array
	 */
	public function parseString(string $config): ?array {
		// Handle PHP start tag
		$config = trim($config);
		if (substr($config, 0, 2) === '<?') {
			$config = '?>' . $config;
		}

		// Eval the string, if it throws an exception, rethrow it
		try {
			$data = $this->isolate($config);
		}
		catch (\Exception $exception) {
			throw new ParseException(
				[
					'message'   => 'PHP string threw an exception',
					'exception' => $exception,
				]
			);
		}

		// Complete parsing
		return $this->parse($data);
	}

	/**
	 * Runs PHP string in isolated method
	 *
	 * @param string $EGsfKPdue7ahnMTy
	 *
	 * @return array
	 */
	protected function isolate(string $EGsfKPdue7ahnMTy): ?array {
		return eval($EGsfKPdue7ahnMTy);
	}
}