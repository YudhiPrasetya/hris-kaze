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
 * @file   ViewModel.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Http\ViewModels;

use App\Repositories\EloquentRepositoryInterface;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Response;


abstract class ViewModel implements Arrayable, Responsable {
	/**
	 * @var array
	 */
	protected array $ignore = [];

	/**
	 * @var string
	 */
	protected string $view = '';

	/**
	 * Get the instance as an array.
	 *
	 * @return array
	 * @throws \ReflectionException
	 */
	public function toArray(): array {
		return $this->items()
		            ->all();
	}

	/**
	 * @return \Illuminate\Support\Collection
	 * @throws \ReflectionException
	 */
	protected function items(): Collection {
		$class = new ReflectionClass($this);

		// @formatter:off
		$publicProperties = collect($class
			->getProperties(ReflectionProperty::IS_PUBLIC))
			->reject(function (ReflectionProperty $property) {
				return $this->shouldIgnore($property->getName());
			})
			->mapWithKeys(function (ReflectionProperty $property) {
				return [$property->getName() => $this->{$property->getName()}];
			});

		$publicMethods = collect($class
			->getMethods(ReflectionMethod::IS_PUBLIC))
			->reject(function (ReflectionMethod $method) {
				return $this->shouldIgnore($method->getName());
			})
			->mapWithKeys(function (ReflectionMethod $method) {
				return [$method->getName() => $this->createVariableFromMethod($method)];
			});
		// @formatter:on

		return $publicProperties->merge($publicMethods);
	}

	protected function shouldIgnore(string $methodName): bool {
		if (Str::startsWith($methodName, '__')) {
			return true;
		}

		return in_array($methodName, $this->ignoredMethods());
	}

	protected function ignoredMethods(): array {
		return array_merge(
			[
				'toArray',
				'toResponse',
				'view',
			],
			$this->ignore
		);
	}

	protected function createVariableFromMethod(ReflectionMethod $method) {
		if ($method->getNumberOfParameters() === 0) {
			return $this->{$method->getName()}();
		}

		return Closure::fromCallable([$this, $method->getName()]);
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \ReflectionException
	 */
	public function toResponse($request): Response {
		if ($request->wantsJson()) {
			return new JsonResponse($this->items());
		}

		if ($this->view) {
			return response()->view($this->view, $this);
		}

		return new JsonResponse($this->items());
	}

	/**
	 * @param string $view
	 *
	 * @return $this
	 */
	public function view(string $view): ViewModel {
		$this->view = $view;

		return $this;
	}
}
