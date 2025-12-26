<?php

namespace App\Http\Middleware;

use App\Exceptions\ThemeNotFoundException;
use App\Facades\ChromeLogger;
use App\Facades\Theme;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ApplyTheme {
	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure                 $next
	 *
	 * @return mixed
	 * @throws \App\Exceptions\ThemeNotFoundException
	 */
	public function handle(Request $request, Closure $next) {
		if (!$request->expectsJson()) {
			$theme = null;

			if ($request->session()->has('theme')) {
				$theme = $request->session()->get('theme');
			}
			else if (Auth::check()) {
				$theme = Auth::user()->config->theme;
			}
			else {
				$theme = config('theme.active', 'Falcon');
			}

			if (!empty($theme) && is_string($theme)) {
				if (!Theme::has($theme)) throw new ThemeNotFoundException($theme);
				else {
					// set the theme
					Theme::set($theme);
					/**
					 * @var $response \Illuminate\Http\Response
					 */
					$request->session()->put('theme', $theme);
				}
			}
		}

		return $next($request);
	}
}
