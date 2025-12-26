<?php

use App\Contracts\Theme as ThemeContract;
use App\Managers\Theme\Theme;
use App\Managers\Theme\ThemeInfo;


if (!function_exists('theme')) {
	function theme(): Theme {
		return app()->get(ThemeContract::class);
	}
}

if (!function_exists('themes')) {
	/**
	 * Generate an asset path for the theme.
	 *
	 * @param string    $path
	 * @param bool|null $secure
	 *
	 * @return string
	 */
	function themes($path, $secure = null): string {
		return theme()->assets($path, $secure);
	}
}

if (!function_exists('theme_mix')) {
	/**
	 * Get the current theme path to a versioned Mix file.
	 *
	 * @param        $path
	 * @param string $manifestDirectory
	 *
	 * @return \Illuminate\Support\HtmlString|mixed|string
	 * @throws \Exception
	 */
	function theme_mix($path, $manifestDirectory = '') {
		return theme()->themeMix($path, $manifestDirectory);
	}
}

if (!function_exists('theme_path')) {
	/**
	 * Get the current theme path to a versioned Mix file.
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	function theme_path($path): string {
		return theme()->getFullPath($path);
	}
}

if (!function_exists('lang')) {
	/**
	 * Get lang content from current theme.
	 *
	 * @param       $fallback
	 * @param array $replace
	 *
	 * @return \Illuminate\Contracts\Translation\Translator|string
	 */
	function lang(string $fallback, array $replace = []) {
		return theme()->lang($fallback, $replace);
	}
}

if (!function_exists('current_theme_name')) {
	/**
	 * Get current active theme name only or themeinfo collection.
	 *
	 * @return null|string|ThemeInfo
	 */
	function current_theme_name() {
		return theme()->current();
	}
}

if (!function_exists('current_theme')) {
	/**
	 * Get current active theme name only or themeinfo collection.
	 *
	 * @param bool $collection
	 *
	 * @return null|string|ThemeInfo
	 */
	function current_theme($collection = false) {
		return theme()->current($collection);
	}
}
