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
 * @file   ChromeLogger.php
 * @date   19/09/20 01.47
 */

namespace App\Facades;

use App\Libraries\ChromePhp\ChromeLogger as Logger;
use Illuminate\Support\Facades\Facade;


class ChromeLogger extends Facade {
    /**
     * @return string
     */
    protected static function getFacadeAccessor() {
        return Logger::class;
    }
}
