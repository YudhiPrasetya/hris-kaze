<?php
/**
 * This file is part of the Kaze project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   TraccarSession.php
 * @date   2021-05-29 20:50:19
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie;


class TraccarSession {
	/**
	 * The application instance.
	 *
	 * @var \Illuminate\Contracts\Foundation\Application
	 */
	protected $app;

	/**
	 * The URIs that should be excluded from CSRF verification.
	 *
	 * @var array
	 */
	protected $except = [];

	public function __construct(Application $app) {
		$this->app = $app;
	}

	public function handle(Request $request, Closure $next, $guard = null) {
		$route = get_route();

		if (!app('auth')->guard($guard)->guest() && (!$request->expectsJson() || !$request->isXmlHttpRequest()) && !Str::start($route, 'api.')) {
			if ($this->isReading($request) || $this->runningUnitTests() || $this->inExceptArray($request)) {
				return tap($next($request),
					function ($response) use ($request) {
						// $this->addCookieToResponse($request, $response);
					});
			}
		}

		return $next($request);
	}

	/**
	 * Determine if the HTTP request uses a ‘read’ verb.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return bool
	 */
	protected function isReading($request) {
		return in_array($request->method(), ['HEAD', 'GET', 'OPTIONS']);
	}

	/**
	 * Determine if the application is running unit tests.
	 *
	 * @return bool
	 */
	protected function runningUnitTests() {
		return $this->app->runningInConsole() && $this->app->runningUnitTests();
	}

	/**
	 * Determine if the request has a URI that should pass through CSRF verification.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return bool
	 */
	protected function inExceptArray($request) {
		foreach ($this->except as $except) {
			if ($except !== '/') {
				$except = trim($except, '/');
			}

			if ($request->fullUrlIs($except) || $request->is($except)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param Request                   $request
	 * @param Response|RedirectResponse $response
	 *
	 * @return Response|\Symfony\Component\HttpFoundation\Response
	 */
	protected function addCookieToResponse(Request $request, RedirectResponse|Response|JsonResource $response) {
		$config = config('session');

		$JSESSIONID = $this->createSession();

		if ($response instanceof Responsable) {
			$response = $response->toResponse($request);
		}

//		$response->headers->setCookie(
//			new Cookie(
//				'JSESSIONID', $JSESSIONID->getValue(), 0, $JSESSIONID->getPath(), false, false, false, false, 'none'
//			)
//		);
		//clock($JSESSIONID);
		// setcookie('JSESSIONID', $JSESSIONID->getValue(), ['Path' => $JSESSIONID->getPath()]);

		return $response;
	}

	/**
	 * @return \GuzzleHttp\Cookie\SetCookie|null
	 */
	private function createSession() {
		$rsp = Http::asForm()
					->post('http://35.188.167.11:8082/api/session',
						[
							'email'    => 'eq.petrucci@gmail.com',
							'password' => 'pwd4me_84$',
						]);

		return $rsp->cookies()->getCookieByName("JSESSIONID");
	}
}