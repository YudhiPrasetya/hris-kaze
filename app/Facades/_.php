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
 * @date   24/08/2020 09.33
 */

namespace App\Facades;

use App\Managers\Theme\ThemeInfo;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\HtmlString;


/**
 * Class Theme
 *
 * @method static ThemeInfo[] all()
 * @method static string assets(string $path, bool $secure = null)
 * @method static ThemeInfo|null|string current($collection = false)
 * @method static ThemeInfo|ThemeInfo[] get(string $theme = null, $collection = false)
 * @method static string|null getFullPath(string $path)
 * @method static ThemeInfo|null getThemeInfo(string $themeName)
 * @method static bool has(string $theme)
 * @method static array|Application|Translator|string|null lang(string $fallback, array $replace = [])
 * @method static void set(string $theme)
 * @method static HtmlString|string themeMix(string $path, string $manifestDirectory = '')
 *
 * @package App\Facades
 */
class _ extends Facade {
	protected static function getFacadeAccessor() {
		return '_';
	}
}