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
 * @file   Permission.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use Closure;


class Permission {
	public function handle($request, Closure $next, $permission, $guard = null) {
		if (app('auth')->guard($guard)->guest()) {
			throw UnauthorizedException::notLoggedIn();
		}

		$permissions = is_array($permission) ? $permission : explode('|', $permission);

		foreach ($permissions as $permission) {
			if (app('auth')->guard($guard)->user()->can($permission)) {
				return $next($request);
			}
		}

		throw UnauthorizedException::forPermissions($permissions);
	}
}
