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
 * @file   EnsureEmailIsVerified.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Laravel\Fortify\Features;


class EnsureEmailIsVerified {
	/**
	 * Handle an incoming request.
	 *
	 * @param Request     $request
	 * @param Closure     $next
	 * @param string|null $redirectToRoute
	 *
	 * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
	 */
	public function handle(Request $request, Closure $next, $redirectToRoute = null) {
		if ($request->user() instanceof User) {
			// Because I'm superman
			if ($request->user()->hasRole('super-admin')) return $next($request);
		}

		if (Features::enabled(Features::emailVerification())) {
			if (!$request->user() || ($request->user() instanceof MustVerifyEmail && !$request->user()->hasVerifiedEmail())) {
				return $request->expectsJson()
					? abort(403, 'Your email address is not verified.')
					: Redirect::route($redirectToRoute ?: 'verification.notice');
			}
		}

		return $next($request);
	}
}
