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
 * @file   RoleOrPermission.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use Closure;
use Illuminate\Support\Facades\Auth;


class RoleOrPermission {
	public function handle($request, Closure $next, $roleOrPermission, $guard = null) {
		if (Auth::guard($guard)->guest()) {
			throw UnauthorizedException::notLoggedIn();
		}

		$rolesOrPermissions = is_array($roleOrPermission) ? $roleOrPermission : explode('|', $roleOrPermission);

		if (!Auth::guard($guard)->user()->hasAnyRole($rolesOrPermissions) && !Auth::guard($guard)->user()->hasAnyPermission($rolesOrPermissions)) {
			throw UnauthorizedException::forRolesOrPermissions($rolesOrPermissions);
		}

		return $next($request);
	}
}
