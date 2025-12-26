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
 * @file   UtmsProvider.php
 * @date   2020-11-3 19:9:40
 */

namespace App\Managers\Socialite\Providers;

use App\Libraries\Path;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;


class UtmsProvider extends AbstractProvider implements ProviderInterface {
	public function getAccessTokenResponse($code) {
		$response = $this->getHttpClient()->post($this->getTokenUrl(),
			[
				'headers'     => [
					'Accept'        => 'application/json',
					'Authorization' => 'Basic ' . base64_encode(env('UTMS_CLIENT_ID') . ':' . env('UTMS_CLIENT_SECRET')),
				],
				'form_params' => $this->getTokenFields($code),
			]);

		return json_decode($response->getBody(), true);
	}

	protected function getTokenUrl() {
		return Path::join(env('UTMS_ENDPOINT'), 'tms/rest/v2/oauth/token');
	}

	protected function getTokenFields($code) {
		return [
			'grant_type' => 'password',
			'username'   => $code['username'],
			'password'   => $code['password'],
		];
	}

	protected function getAuthUrl($state) {
		// TODO: Implement getAuthUrl() method.
	}

	protected function getUserByToken($token) {

	}

	protected function mapUserToObject(array $user) {

	}

	protected function getRequestOptions($token) {
		return [
			'headers' => [
				'Accept'        => 'application/json',
				'Authorization' => 'Bearer ' . $token,
			],
		];
	}
}