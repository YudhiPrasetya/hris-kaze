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
 * @file   ThemeInfo.php
 * @date   24/08/2020 09.36
 */

namespace App\Managers\Theme;

use App\Contracts\ConfigInterface;
use App\Libraries\Config\Config;


/**
 * Class ThemeInfo
 *
 * @method ThemeInfo get(string $key, $default = null)
 * @method void set(string $key, mixed $value)
 * @method bool has($key)
 * @method ThemeInfo merge(ConfigInterface $config)
 * @method ThemeInfo[] all()
 * @method mixed offsetGet($offset)
 * @method bool offsetExists($offset)
 * @method void offsetSet($offset, mixed $value)
 * @method void offsetUnset($offset)
 * @method ThemeInfo current()
 * @method mixed key()
 * @method mixed next()
 * @method mixed rewind()
 * @method bool valid()
 * @method void remove($key)
 *
 * @see     \App\Libraries\Config\Config
 *
 * @package App\Managers\Theme
 */
class ThemeInfo extends Config {

}