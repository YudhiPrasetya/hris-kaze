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
 * @file   Theme.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Managers\Theme;

use App\Exceptions\ThemeNotFoundException;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Facades\File;
use Illuminate\View\ViewFinderInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\HtmlString as HtmlString;


define('DS', DIRECTORY_SEPARATOR);

class Theme {
	/**
	 * Theme Root Path.
	 *
	 * @var string
	 */
	protected $basePath;

	/**
	 * All Theme Information.
	 *
	 * @var ThemeInfo[]
	 */
	protected $themes;

	/**
	 * Blade View Finder.
	 *
	 * @var \Illuminate\View\ViewFinderInterface
	 */
	protected $finder;

	/**
	 * Application Container.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $app;

	/**
	 * Translator.
	 *
	 * @var \Illuminate\Contracts\Translation\Translator
	 */
	protected $lang;

	/**
	 * Config.
	 *
	 * @var Repository
	 */
	protected $config;

	/**
	 * Current Active Theme.
	 *
	 * @var string|ThemeInfo
	 */
	private $activeTheme = null;

	/**
	 * Theme constructor.
	 *
	 * @param \Illuminate\Container\Container $app
	 * @param \Illuminate\View\ViewFinderInterface $finder
	 * @param \Illuminate\Config\Repository $config
	 * @param \Illuminate\Contracts\Translation\Translator $lang
	 *
	 * @throws \App\Exceptions\EmptyDirectoryException
	 * @throws \App\Exceptions\FileNotFoundException
	 * @throws \App\Exceptions\UnsupportedFormatException
	 */
	public function __construct(Container $app, ViewFinderInterface $finder, Repository $config, Translator $lang) {
		$this->config = $config;
		$this->app = $app;
		$this->finder = $finder;
		$this->lang = $lang;
		$this->basePath = $this->config['theme.theme_path'];
		$this->scanThemes();
	}

	/**
	 * Scan for all available themes.
	 *
	 * @throws \App\Exceptions\EmptyDirectoryException
	 * @throws \App\Exceptions\FileNotFoundException
	 * @throws \App\Exceptions\UnsupportedFormatException
	 */
	private function scanThemes() {
		$themeDirectories = File::glob($this->basePath . '/*', GLOB_ONLYDIR);
		$themes = [];

		foreach ($themeDirectories as $themePath) {
			$themeConfigPath = path($themePath, $this->config['theme.config.name']);
			$themeChangelogPath = path($themePath, $this->config['theme.config.changelog']);

			if (file_exists($themeConfigPath)) {
				$themeConfig = ThemeInfo::load($themeConfigPath);
				$themeConfig->set('changelog', ThemeInfo::load($themeChangelogPath)->all());
				$themeConfig->set('path', $themePath);
				$themeConfig->set('relative', str_replace(resource_path() . '/', '', $themePath));
				$themeConfig->set('basename', basename($themeConfig->get('path')));

				if ($themeConfig->has('name')) {
					$themes[$themeConfig->get('name')] = $themeConfig;
				}
			}
		}

		$this->themes = $themes;
	}

	/**
	 * @param string $theme
	 *
	 * @throws \App\Exceptions\ThemeNotFoundException
	 */
	public function set(string $theme) {
		if (!$this->has($theme)) {
			throw new ThemeNotFoundException($theme);
		}

		$this->loadTheme($theme);
		$this->activeTheme = $theme;
	}

	/**
	 * @param string|null $theme
	 *
	 * @return bool
	 */
	public function has(?string $theme): bool {
		return array_key_exists($theme, $this->themes);
	}

	/**
	 * Map view map for particular theme.
	 *
	 * @param string $theme
	 *
	 * @return void
	 */
	private function loadTheme(string $theme) {
		if (!is_null($theme)) {
			$themeInfo = $this->getThemeInfo($theme);

			if (!is_null($themeInfo)) {
				$this->loadTheme($themeInfo->get('parent'));

				$viewPath = path($themeInfo->get('path'), $this->config['theme.folders.views']);
				$langPath = path($themeInfo->get('path'), $this->config['theme.folders.lang']);

				$paths = [];

				// Add view location
				$this->finder->prependLocation($themeInfo->get('path'));
				$this->finder->prependLocation($viewPath);

				$name = $themeInfo->get('name');
				// Add view namespace
				$this->finder->prependNamespace($name, $viewPath);
				$this->finder->prependNamespace(strtolower($name), $viewPath);
				$finder = $this->finder;

				// Prepend view paths to all namespaces
				collect($this->finder->getHints())->each(function ($items, $namespace) use ($finder, $name, $viewPath) {
					collect($items)->each(function ($path, $index) use ($finder, $name, $namespace, $viewPath) {
						$currentNamespace = $finder->getHints()[$namespace];

						$path = explode("/", $path);
						$path = array_slice($path, -1)[0];
						if ($path == 'views') $path = $namespace;

						if (!in_array("$viewPath/extensions/$path", $currentNamespace) && $path != 'themes' && strtolower($namespace) != strtolower($name)) {
							$path = "$viewPath/extensions/" . ($path == 'views' ?: $namespace);
							$finder->prependNamespace($namespace, $path);
						}
					});
				});

				if ($themeInfo->has('type') && !empty($themeInfo->get('type'))) {
					$this->finder->prependNamespace($themeInfo->get('type'), $viewPath);
				}

				// Add translator namespace
				$this->lang->addNamespace($name, $langPath);
				$this->lang->addNamespace(strtolower($name), $langPath);
				$this->lang->addJsonPath($langPath);

				// Add tranlator from .php
				if (File::exists(path($langPath, $this->lang->getLocale()))) {
					foreach (File::files(path($langPath, $this->lang->getLocale())) as $file) {
						$group = $file->getBasename('.php');
						$languages = require_once($file->getRealPath());
						$this->addLines($group, $languages, $this->lang->getLocale());
						$this->addLines($group, $languages, $this->lang->getLocale(), $name);
						$this->addLines($group, $languages, $this->lang->getLocale(), strtolower($name));
					}
				}
			}

			clock($this->finder, $this->lang);
		}
	}

	/**
	 * @param string $themeName
	 *
	 * @return \App\Managers\Theme\ThemeInfo|null
	 */
	public function getThemeInfo(string $themeName): ?ThemeInfo {
		return isset($this->themes[$themeName]) ? $this->themes[$themeName] : null;
	}

	private function addLines(string $name, array $lines, string $locale, string $namespace = '*') {
		$lines = $this->convertLines($lines, $name);
		$this->lang->addLines($lines, $locale, $namespace);
	}

	private function convertLines(array $lines, ?string $path = null): array {
		$group = [];

		foreach ($lines as $key => $line) {
			$paths = implode(".", array_filter([$path, $key]));

			if (is_array($line)) {
				$group = array_merge($group, $this->convertLines($line, $paths));
			}
			else {
				$group[$paths] = $line;
			}
		}

		return $group;
	}

	/**
	 * @param string|null $theme
	 * @param false $collection
	 *
	 * @return \App\Managers\Theme\ThemeInfo|\App\Managers\Theme\ThemeInfo[]
	 */
	public function get(string $theme = null, $collection = false) {
		if (is_null($theme) || !$this->has($theme)) {
			return !$collection ? $this->themes[$this->activeTheme]->all() : $this->themes[$this->activeTheme];
		}

		return !$collection ? $this->themes[$theme]->all() : $this->themes[$theme];
	}

	/**
	 * @return \App\Managers\Theme\ThemeInfo[]
	 */
	public function all() {
		return $this->themes;
	}

	/**
	 * @param string $path
	 * @param bool|null $secure
	 *
	 * @return string
	 */
	public function assets(string $path, bool $secure = null): string {
		$url = $this->app['url']->asset($this->getFullPath($path), $secure);
		if (env('APP_ENV') == 'local') return $url . '?t=' . md5(time() . rand(0, 1000));

		return $url;
	}

	/**
	 * @param string $path
	 *
	 * @return string|null
	 */
	public function getFullPath(string $path): ?string {
		$splitThemeAndPath = explode(':', $path);
		$fullPath = null;

		if (count($splitThemeAndPath) > 1) {
			if (!is_null($splitThemeAndPath[0])) {
				return null;
			}

			$themeName = $splitThemeAndPath[0];
			$path = $splitThemeAndPath[1];
		}
		else {
			$themeName = $this->activeTheme;
			$path = $splitThemeAndPath[0];
		}

		$themeName = $themeName ?? config('theme.active');
		$themeInfo = $this->getThemeInfo($themeName);

		if ($this->config['theme.symlink']) {
			$themePath = str_replace(base_path('public') . DS, '', $this->config['theme.symlink_path']);
			$themePath = path($themePath, $themeInfo->get('basename')) . DS;
		}
		else {
			$themePath = str_replace(base_path('public') . DS, '', $themeInfo->get('relative')) . DS;
		}

		$assetPath = $this->config['theme.folders.assets'] . DS;
		// $fullPath = $themePath . $assetPath . $path;
		$fullPath = $assetPath . $themePath . $path;

		if (!file_exists($fullPath) && $themeInfo->has('parent') && !empty($themeInfo->get('parent'))) {
			$themePath = str_replace(
				             base_path() . DS,
				             '',
				             $this->getThemeInfo($themeInfo->get('parent'))->get('path')
			             ) . DS;

			$fullPath = $assetPath . $themePath . $path;
			// return $fullPath;
		}

		return $fullPath;
	}

	/**
	 * @param string $path
	 * @param string $manifestDirectory
	 *
	 * @return \Illuminate\Support\HtmlString|string
	 * @throws \Exception
	 */
	public function themeMix(string $path, string $manifestDirectory = ''): \Illuminate\Support\HtmlString|string {
		return mix($this->getFullPath($path), $manifestDirectory);
	}

	/**
	 * @param string $fallback
	 * @param array $replace
	 *
	 * @return array|Application|Translator|string|null
	 */
	public function lang(string $fallback, array $replace = []): array|string|Translator|Application|null {
		$splitLang = explode('::', $fallback);

		if (count($splitLang) > 1) {
			$fallback = empty($splitLang[0]) ? $fallback = $splitLang[1] : $splitLang[0] . '::' . $splitLang[1];
		}
		else {
			$fallback = $this->current() . '::' . $splitLang[0];
			if (!$this->lang->has($fallback)) {
				$fallback = $this->getThemeInfo($this->current())->get('parent') . '::' . $splitLang[0];
			}
		}

		return trans($fallback, $replace);
	}

	/**
	 * @param false $collection
	 *
	 * @return ThemeInfo|string|null
	 */
	public function current(bool $collection = false): string|ThemeInfo|null {
		return !$collection ? $this->activeTheme : $this->getThemeInfo($this->activeTheme);
	}
}