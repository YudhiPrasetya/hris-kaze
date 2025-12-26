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
 * @file   Route.php
 * @date   29/08/2020 03.34
 */

namespace App\Libraries;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\Routing\Matcher\TraceableUrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use \_;


class Route {
	/**
	 * @var null
	 */
	private static $instance = null;

	/**
	 * @var array
	 */
	public $nodes = [];

	/**
	 * @var \Symfony\Component\Routing\Matcher\TraceableUrlMatcher
	 */
	private $matcher;

	/**
	 * @var \Illuminate\Routing\Router
	 */
	private $router;

	/**
	 * @var \Illuminate\Http\Request
	 */
	private $request;

	private $routes;

	public function __construct() {
		$this->nodes = [];
		$this->router = \Illuminate\Support\Facades\Route::getFacadeRoot();
		$this->routes = $this->router->getRoutes();
		$this->request = $this->router->getCurrentRequest();
		$context = RequestContext::fromUri($this->request->getUri());
		$this->matcher = new TraceableUrlMatcher($this->getRouteCollection(), $context);
		$parent = '';
		$paramPos = 0;

		foreach ($this->getBreadcrumbsPaths() as $path) {
			if ($node = $this->createBreadcrumbsNode($path, $parent, $this->request->getBaseUrl(), $paramPos)) {
				$this->nodes[] = $node;
				$parent = $path;
			}
		}
	}

	/**
	 * @return RouteCollection
	 */
	public function getRouteCollection(): RouteCollection {
		$collection = new RouteCollection();
		$it = $this->router->getRoutes()->getIterator();

		while ($it->valid()) {
			/**
			 * @var $route \Illuminate\Routing\Route
			 */
			$route = $it->current();
			$collection->add(empty($route->getName()) ? '' : $route->getName(), $route->toSymfonyRoute());
			$it->next();
		}

		return $collection;
	}

	/**
	 * Get all breadcrumbs paths from current request path
	 *
	 * @param string|null $path
	 *
	 * @return array
	 */
	public function getBreadcrumbsPaths(?string $path = null) {
		$parts = array();
		//$pathInfo = trim($path ?? $this->router->getCurrentRoute()->uri(), '/');
		$pathInfo = trim($path ?? $this->router->getCurrentRequest()->path(), '/');

		if ($pathInfo) {
			$parts = explode('/', $pathInfo);
		}

		array_unshift($parts, '/');

		$path = '';
		$paths = array();

		foreach ($parts as $part) {
			$path .= $part;
			$paths[] = $path;

			if ('/' !== $part) {
				$path .= '/';
				$paths[] = $path;
			}
		}

		return $paths;
	}

	/**
	 * Create a breadcrumbs node from path
	 *
	 * @param $path
	 * @param $parent
	 * @param $baseUrl
	 *
	 * @return false|mixed
	 */
	private function createBreadcrumbsNode($path, $parent, $baseUrl, &$pos) {
		$traces = $this->matcher->getTraces($path);
		$actions = ['edit', 'create'];

		foreach ($traces as $trace) {
			// it will get all matches routes
			// ex.
			// url: http://dev-web.mdd.co.id/mobile-user/aiko/add-detail
			//     - /
			//     - /mobile-user
			//     - /mobile-user/aiko
			//     - /mobile-user/aiko/add-detail
			if (TraceableUrlMatcher::ROUTE_MATCHES == $trace['level']) {
				//$request = Request::createFromBase(SymfonyRequest::create($path));
				//$route = $this->routes->getByName($trace['name'])->bind($request);
				$model = null;

				//if (isset($route->signatureParameters()[0])) {
				//	$class = $route->signatureParameters()[0]->getType()->getName();
				//	if (Str::of($class)->contains(["App\Models"])) {
				//		$model = $class::find($route->parameters());
				//		$model = call_user_func([$class, 'find'], $route->parameters[$route->parameterNames[$pos]]);
				//		$pos = count($route->parameters()) - 1;
				//	}
				//}

				//$trace['route'] = $route;
				//$trace['url'] = route($trace['name'], $route->parameters());
				$trace['url'] = '';
				$trace['active'] = $this->router->get($trace['path'])->matches($this->request);
				list($name, $action) = explode('.', Str::endsWith($trace['name'], '.') ? $trace['name'] : $trace['name'] . '.');
				$trace['title'] = Str::of($name)->plural()->title();

				if ($action) {
					$index = _::indexOf($actions, $action);

					if ($index !== -1) {
						$trace['title'] = Str::title($actions[$index]);
					}

					if ($action == 'show') {
						$trace['title'] = ($model ?? new class { public string $name = ''; })->name;
					}
				}

				return $trace;
			}
		}

		return false;
	}

	/**
	 * @return Route
	 */
	public static function getInstance(): Route {
		if (empty(static::$instance)) {
			static::$instance = new Route();
		}

		return static::$instance;
	}

	/**
	 * @param string $path
	 *
	 * @return bool
	 */
	public function isCurrent(string $path): bool {
		$traces = $this->matcher->getTraces($path);

		foreach ($traces as $trace) {
			if (TraceableUrlMatcher::ROUTE_MATCHES == $trace['level']) {
				return $this->router->get($trace['path'])->matches($this->request);
			}
		}

		return false;
	}

	/**
	 * @return \Illuminate\Routing\Route|null
	 */
	public function getCurrentRoute(): ?\Illuminate\Routing\Route {
		return $this->router->current();
	}

	/**
	 * @return array
	 */
	public function getNodes(): array {
		return $this->nodes;
	}
}
