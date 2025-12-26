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
 * @file   Role.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use Closure;
use Illuminate\Support\Facades\Auth;


class Role {
	public function handle($request, Closure $next, $role, $guard = null) {
		if (Auth::guard($guard)->guest()) {
			throw UnauthorizedException::notLoggedIn();
		}

		$roles = is_array($role) ? $role : explode('|', $role);

		if (!Auth::guard($guard)->user()->hasAnyRole($roles)) {
			throw UnauthorizedException::forRoles($roles);
		}

		return $next($request);
	}
}
