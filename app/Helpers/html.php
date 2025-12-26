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
 * @file   html.php
 * @date   27/08/2020 11.02
 */

if (!function_exists('link_to')) {
	/**
	 * Generate a HTML link.
	 *
	 * @param string $url
	 * @param string $title
	 * @param array  $attributes
	 * @param bool   $secure
	 * @param bool   $escape
	 *
	 * @return string
	 */
	function link_to($url, $title = null, $attributes = [], $secure = null, $escape = true): string {
		return app('html')->link($url, $title, $attributes, $secure, $escape);
	}
}

if (!function_exists('link_to_asset')) {
	/**
	 * Generate a HTML link to an asset.
	 *
	 * @param string $url
	 * @param string $title
	 * @param array  $attributes
	 * @param bool   $secure
	 *
	 * @return string
	 */
	function link_to_asset($url, $title = null, $attributes = [], $secure = null): string {
		return app('html')->linkAsset($url, $title, $attributes, $secure);
	}
}

if (!function_exists('link_to_route')) {
	/**
	 * Generate a HTML link to a named route.
	 *
	 * @param string $name
	 * @param string $title
	 * @param array  $parameters
	 * @param array  $attributes
	 *
	 * @return string
	 */
	function link_to_route($name, $title = null, $parameters = [], $attributes = []): string {
		return app('html')->linkRoute($name, $title, $parameters, $attributes);
	}
}

if (!function_exists('link_to_action')) {
	/**
	 * Generate a HTML link to a controller action.
	 *
	 * @param string $action
	 * @param string $title
	 * @param array  $parameters
	 * @param array  $attributes
	 *
	 * @return string
	 */
	function link_to_action($action, $title = null, $parameters = [], $attributes = []): string {
		return app('html')->linkAction($action, $title, $parameters, $attributes);
	}
}
