<?php

use Illuminate\Support\Collection;


if (!function_exists('getModelForGuard')) {
	/**
	 * @param string $guard
	 *
	 * @return mixed
	 */
	function getModelForGuard(string $guard) {
		return collect(config('auth.guards'))
			->map(
				function ($guard) {
					if (!isset($guard['provider'])) {
						return null;
					}

					return config("auth.providers.{$guard['provider']}.model");
				}
			)->get($guard);
	}
}
