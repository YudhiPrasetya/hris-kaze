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
 * @file   ChromeLoggerServiceProvider.php
 * @date   19/09/20 01.44
 */

namespace App\Providers;

use App\Libraries\ChromePhp\ChromeLogger;
use App\Libraries\ChromePhp\ChromePhp;
use Illuminate\Support\ServiceProvider;


class ChromeLoggerServiceProvider extends ServiceProvider {
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton(
            ChromePhp::class,
            function () {
                return ChromePhp::getInstance();
            }
        );

        $this->app->bind(
            ChromeLogger::class,
            function () {
                return ChromeLogger::getInstance();
            }
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {

        return [ChromeLogger::class];
    }
}
