<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Closure;


class Authenticate extends Middleware {
	/**
	 * Get the path the user should be redirected to when they are not authenticated.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return string|null
	 */
	protected function redirectTo($request) {
		if (!$request->expectsJson()) {
			return route('login');
		}
	}

	public function handle($request, Closure $next, ...$guards) {
		$ret = parent::handle($request, $next, ...$guards);
		$route = $this->getRoute();

		/**
		 * @var \App\Models\User
		 */
		$user = Auth::user();

		if ($user) {
			if (!$user->enabled) {
				Auth::logout();
				$request->session()->flash('danger', 'You account is disabled');

				return redirect(RouteServiceProvider::LOGIN);
			}
		}

		collect(['access.dashboard', $route])->each(function ($permission) use ($user) {
			if ($permission !== 'home' && Str::before($permission, '.') !== 'api') {
				if (!$user->hasDirectPermission($permission)) {
					Auth::logout();
					throw UnauthorizedException::forPermissions([$permission]);
				}
			}
		});

		return $ret;
	}

	protected function getRoute(): string {
		$aliases = [
			'index'   => 'index',
			'show'    => 'show',

			// Create New
			'create'  => 'create',
			'store'   => 'create',

			// Edit / Update
			'edit'    => 'edit',
			'update'  => 'edit',

			// Delete
			'destroy' => 'destroy',
		];

		$name = Route::getCurrentRoute()->getName();
		$section = Str::beforeLast($name, ".");

		if (Str::contains($section, ['home', 'api'])) return $name;

		if (isset($aliases[Str::afterLast($name, ".")])) {
			$action = $aliases[Str::afterLast($name, ".")];

			return "{$section}.{$action}";
		}

		return $name;
	}
}
