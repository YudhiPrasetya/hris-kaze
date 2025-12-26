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
 * @file   Config.php
 * @date   24/08/2020 09.40
 */

namespace App\Libraries\Config;

use App\Contracts\ParserInterface;
use App\Exceptions\EmptyDirectoryException;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\UnsupportedFormatException;
use App\Libraries\Config\Parser\Ini;
use App\Libraries\Config\Parser\Json;
use App\Libraries\Config\Parser\Php;
use App\Libraries\Config\Parser\Xml;
use App\Libraries\Config\Parser\Yaml;


class Config extends AbstractConfig {
	/**
	 * All formats supported by Config.
	 *
	 * @var array
	 */
	protected $supportedParsers = [
		Php::class,
		Ini::class,
		Json::class,
		Xml::class,
		Yaml::class
	];

	/**
	 * Json constructor.
	 *
	 * @param string|null          $values
	 * @param ParserInterface|null $parser
	 * @param bool                 $string
	 *
	 * @throws EmptyDirectoryException
	 * @throws FileNotFoundException
	 * @throws UnsupportedFormatException
	 */
	public function __construct(string $values, ParserInterface $parser = null, bool $string = false) {
		if ($string === true) {
			$this->loadFromString($values, $parser);
		}
		else {
			$this->loadFromFile($values, $parser);
		}

		parent::__construct($this->data);
	}

	/**
	 * Loads configuration from string.
	 *
	 * @param string          $configuration String with configuration
	 * @param ParserInterface $parser        Configuration parser
	 */
	protected function loadFromString(string $configuration, ParserInterface $parser) {
		$this->data = [];
		// Try to parse string
		$this->data = array_replace_recursive($this->data, (array)$parser->parseString($configuration));
	}

	/**
	 * Loads configuration from file.
	 *
	 * @param string|array         $path   Filenames or directories with configuration
	 * @param ParserInterface|null $parser Configuration parser
	 *
	 * @throws EmptyDirectoryException If `$path` is an empty directory
	 * @throws FileNotFoundException
	 * @throws UnsupportedFormatException
	 */
	protected function loadFromFile($path, ParserInterface $parser = null) {
		$paths = $this->getValidPath($path);
		$this->data = [];

		foreach ($paths as $path) {
			if ($parser === null) {
				// Get file information
				$info = pathinfo($path);
				$parts = explode('.', $info['basename']);
				$extension = array_pop($parts);

				// Skip the `dist` extension
				if ($extension === 'dist') {
					$extension = array_pop($parts);
				}

				// Get file parser
				$parser = $this->getParser($extension);

				// Try to load file
				$this->data = array_replace_recursive($this->data, (array)$parser->parseFile($path));

				// Clean parser
				$parser = null;
			}
			else {
				// Try to load file using specified parser
				$this->data = array_replace_recursive($this->data, (array)$parser->parseFile($path));
			}
		}
	}

	/**
	 * Checks `$path` to see if it is either an array, a directory, or a file.
	 *
	 * @param string|array $path
	 *
	 * @return array
	 *
	 * @throws EmptyDirectoryException If `$path` is an empty directory
	 * @throws  FileNotFoundException  If a file is not found at `$path`
	 */
	protected function getValidPath($path): array {
		// If `$path` is array
		if (is_array($path)) {
			return $this->getPathFromArray($path);
		}

		// If `$path` is a directory
		if (is_dir($path)) {
			$paths = glob($path . '/*.*');
			if (empty($paths)) {
				throw new EmptyDirectoryException("Configuration directory: [$path] is empty");
			}

			return $paths;
		}

		// If `$path` is not a file, throw an exception
		if (!file_exists($path)) {
			throw new FileNotFoundException("Configuration file: [$path] cannot be found");
		}

		return [$path];
	}

	/**
	 * Gets an array of paths
	 *
	 * @param array $path
	 *
	 * @return array
	 *
	 * @return array
	 * @throws FileNotFoundException   If a file is not found at `$path`
	 * @throws \App\Exceptions\EmptyDirectoryException
	 * @throws \App\Exceptions\FileNotFoundException
	 */
	protected function getPathFromArray(array $path): array {
		$paths = [];

		foreach ($path as $unverifiedPath) {
			try {
				// Check if `$unverifiedPath` is optional
				// If it exists, then it's added to the list
				// If it doesn't, it throws an exception which we catch
				if ($unverifiedPath[0] !== '?') {
					$paths = array_merge($paths, $this->getValidPath($unverifiedPath));
					continue;
				}

				$optionalPath = ltrim($unverifiedPath, '?');
				$paths = array_merge($paths, $this->getValidPath($optionalPath));
			}
			catch (FileNotFoundException $e) {
				// If `$unverifiedPath` is optional, then skip it
				if ($unverifiedPath[0] === '?') {
					continue;
				}

				// Otherwise rethrow the exception
				throw $e;
			}
		}

		return $paths;
	}

	/**
	 * Gets a parser for a given file extension.
	 *
	 * @param string $extension
	 *
	 * @return ParserInterface
	 *
	 * @throws UnsupportedFormatException If `$extension` is an unsupported file format
	 */
	protected function getParser(string $extension): ParserInterface {
		foreach ($this->supportedParsers as $parser) {
			if (in_array($extension, $parser::getSupportedExtensions())) {
				return new $parser();
			}
		}

		// If none exist, then throw an exception
		throw new UnsupportedFormatException('Unsupported configuration format');
	}

	/**
	 * Static method for loading a Json instance.
	 *
	 * @param string|array         $values Filenames or string with configuration
	 * @param ParserInterface|null $parser Configuration parser
	 * @param bool                 $string Enable loading from string
	 *
	 * @return Config
	 * @throws EmptyDirectoryException
	 * @throws FileNotFoundException
	 * @throws UnsupportedFormatException
	 */
	public static function load($values, ParserInterface $parser = null, bool $string = false): Config {
		return new static($values, $parser, $string);
	}
}