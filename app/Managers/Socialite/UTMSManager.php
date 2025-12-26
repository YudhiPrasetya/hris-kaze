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
 * @file   UTMSManager.php
 * @date   2020-11-3 19:6:35
 */

namespace App\Managers\Socialite;

use Laravel\Socialite\SocialiteManager;


class UTMSManager extends SocialiteManager {
	protected function createUtmsDriver() {
		$config = $this->app['config']['services.utms'];

		// warning provider class must be a full namespace path
		return $this->buildProvider(
		// Orange require an OpenID/Connect provider
		// for basic OAuth2 login use standard Socialite model One/Two
			'GeoToBe\Http\SocialAuth\OrangeProvider',
			$config
		);
	}
}