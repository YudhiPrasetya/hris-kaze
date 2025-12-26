<?php
/*
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   PlaceholderRequest.php
 * @date   2021-07-8 14:2:39
 */

namespace App\Libraries\Placeholder;

use Illuminate\Http\Request;


/**
 * Class PlaceholderRequest
 * Image placeholder request parser
 *
 * @package Libraries\Placeholder
 */
class PlaceholderRequest {
	/**
	 * @var array
	 */
	public static $requestPath;

	/**
	 * @var array
	 */
	public static $queryString;

	/**
	 * @var array
	 */
	public static $config;

	/**
	 * Get/parse path + query string parameters
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return bool
	 */
	public static function parseParameters(Request $request) {
		// no caching
		// self::cachingHeaders(); // Verify if the browser has a cached version

		self::$requestPath = self::requestPath($request);
		self::$queryString = self::queryString($request);

		if (self::$requestPath) {
			// Merge two arrays
			// Order is important ! The query string shouldn't erase request path !
			$config = array_merge(self::$queryString, self::$requestPath);
			self::$config = $config;

			return true;
		}

		return false;
	}

	/**
	 * Extract string from request & parse it (main parameters)
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return array|false
	 */
	public static function requestPath(Request $request) {
		$scriptName = str_replace('\\', '/', dirname($request->getScriptName()));
		$configString = substr_replace($request->getUri(), '', 0, strlen(route('image-placeholder')) + 1);
		//$configString = substr($configString, 0, strpos($configString, '?'));
		// Clear query string from path
		$configString = preg_replace('/\?.*/', '', $configString);

		if ($configString != '') {
			// Numeric indexed URL params (extracted from path)
			$params = explode('/', $configString);

			// Parse parameters (and get named configuration)
			return self::parseRequestPath($params);
		}

		return false;
	}

	/**
	 * Verify parameters and name them
	 *
	 * @param array List of numeric indexed URL params (extracted from path)
	 *
	 * @return array
	 */
	public static function parseRequestPath($params) {
		$requestPathConfig = array();

		// First parameter is numeric only (but 'x' allowed) !!
		$params[0] = preg_replace('/[^0-9x]/', '', $params[0]);
		$dimensions = explode('x', $params[0]);
		$dimensions = array_slice($dimensions, 0, 2); // Keep only two first numbers found

		$requestPathConfig['width'] = intval($dimensions[0]);
		$requestPathConfig['height'] = intval($dimensions[1] ?? $dimensions[0]); // Detect square...

		// Second & third parameters must be an hex color
		if (!empty($params[1]) && preg_match('/^[a-fA-F0-9]{6}$/i', $params[1]))
			$requestPathConfig['bgColor'] = $params[1];

		if (!empty($params[2]) && preg_match('/^[a-fA-F0-9]{6}$/i', $params[2]))
			$requestPathConfig['textColor'] = $params[2];

		// Quality
		if (!empty($params[3]) && preg_match('/^[0-9]{1,3}$/i', $params[3]))
			$requestPathConfig['quality'] = $params[3] > 100 ? 100 : $params[3];

		// Extension
		if (!empty($params[4]) && preg_match('/^\.(png|gif|webp|jpeg|wbmp)$/i', $params[4]))
			$requestPathConfig['ext'] = $params[4];
		// debug($requestPathConfig);

		return $requestPathConfig;
	}

	/**
	 * Get query string (additional parameters)
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return array
	 */
	public static function queryString(Request $request) {
		$queryString = array();

		if (function_exists('mb_parse_str'))
			mb_parse_str($request->getQueryString(), $queryString);
		else
			parse_str($request->getQueryString(), $queryString);

		return $queryString;
	}

	/**
	 * Stop the script if the browser already has image in its cache
	 */
	public static function cachingHeaders() {
		header("Cache-Control: private, max-age=10800, pre-check=10800");
		header("Pragma: private");
		header("Expires: " . date(DATE_RFC822, strtotime("1 day")));

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			header('Last-Modified: ' . $_SERVER['HTTP_IF_MODIFIED_SINCE'], true, 304);
			exit;
		}
		else {
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT', true, 200);
		}
	}
}
