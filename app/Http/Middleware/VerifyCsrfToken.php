<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Str;


class VerifyCsrfToken extends Middleware {
	/**
	 * The URIs that should be excluded from CSRF verification.
	 *
	 * @var array
	 */
	protected $except = [
		//
	];

	public function handle($request, Closure $next) {
		if ($this->isReading($request) || $this->runningUnitTests() || $this->inExceptArray($request) || $this->exceptIClock($request) ||
		    $this->tokensMatch($request)) {
			return tap($next($request), function ($response) use ($request) {
				if ($this->shouldAddXsrfTokenCookie()) {
					$this->addCookieToResponse($request, $response);
				}
			});
		}

		throw new TokenMismatchException('CSRF token mismatch.');
	}

	/**
	 * The attendance device doesn't know about authentication in this framework, so let's make an exception
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return bool
	 */
	protected function exceptIClock(Request $request): bool {
		$route = get_route($request->getUri());

		return Str::startsWith($route, 'iclock');
	}
}
