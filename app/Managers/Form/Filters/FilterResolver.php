<?php

namespace App\Managers\Form\Filters;

use App\Managers\Form\Filters\Exception\InvalidInstanceException;
use App\Managers\Form\Filters\Exception\UnableToResolveFilterException;


/**
 * Class FilterResolver
 *
 * @package App\Managers\Form\Filters
 * @author  Djordje Stojiljkovic <djordjestojilljkovic@gmail.com>
 */
class FilterResolver {
	/**
	 * Method instance used to resolve filter parameter to
	 * FilterInterface object from filter Alias or object itself.
	 *
	 * @param $filter
	 *
	 * @return \App\Managers\Form\Filters\FilterInterface|mixed
	 * @throws \App\Managers\Form\Filters\Exception\UnableToResolveFilterException
	 * @throws \Exception
	 */
	public static function instance($filter) {
		if (!is_string($filter)) {
			return self::validateFilterInstance($filter);
		}

		if (class_exists($filter)) {
			return self::validateFilterInstance(new $filter());
		}

		if ($filter = FilterResolver::resolveFromCollection($filter)) {
			return self::validateFilterInstance($filter);
		}

		$ex = new UnableToResolveFilterException();
		throw $ex;
	}

	/**
	 * @param $filter
	 *
	 * @return mixed
	 * @throws \Exception
	 *
	 */
	private static function validateFilterInstance($filter): FilterInterface {
		if (!$filter instanceof FilterInterface) {
			throw new InvalidInstanceException();
		}

		return $filter;
	}

	/**
	 * @param  $filterName
	 *
	 * @return FilterInterface|null
	 */
	public static function resolveFromCollection($filterName): ?FilterInterface {
		$filterClass = self::getCollectionNamespace() . $filterName;

		if (class_exists($filterClass)) {
			return new $filterClass;
		}

		return null;
	}

	/**
	 * @return string
	 */
	public static function getCollectionNamespace(): string {
		return "\\App\\Managers\\Form\\Filters\\Collection\\";
	}
}