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
 * @file   ChromeConsole.php
 * @date   19/09/20 01.46
 */

namespace App\Traits;

use App\Libraries\ChromePhp\ChromePhp;
use BadMethodCallException;


trait ChromeConsole {
    /**
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     * @throws \BadMethodCallException
     */
    public static function __callStatic(string $method, array $parameters) {
        if (static::canLog($method)) {
            return ChromePhp::$method(...$parameters);
        }

        static::badMethodCall($method);
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    private static function canLog(string $method) {
        return config('app.debug') && in_array($method, get_class_methods(ChromePhp::class));
    }

    /**
     * @param string $method
     *
     * @throws \BadMethodCallException
     */
    private static function badMethodCall(string $method) {
        throw new BadMethodCallException(
            sprintf(
                'Method %s::%s does not exist.',
                ChromePhp::class,
                $method
            )
        );
    }

    /**
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call(string $method, array $parameters) {
        if (static::canLog($method)) {
            return with(
                resolve(ChromePhp::class),
                function (ChromePhp $chromePhp) use ($method, $parameters) {
                    return call_user_func_array([$chromePhp, $method], ...$parameters);
                }
            );
        }

        static::badMethodCall($method);
    }
}
