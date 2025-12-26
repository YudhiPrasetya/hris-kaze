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
 * @file   RepositoryBase.php
 * @date   03/09/2020 09:02
 */

namespace App\Repositories\Eloquent;

use App\Repositories\EloquentRepositoryInterface;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;


/**
 * Class RepositoryBase
 *
 * @package App\Repositories\Eloquent
 */
class RepositoryBase implements EloquentRepositoryInterface {
	/**
	 * @var Model
	 */
	protected $model;

	/**
	 * @var false|string
	 */
	private $className;

	/**
	 * BaseRepository constructor.
	 *
	 * @param Model $model
	 */
	public function __construct(Model $model) {
		$this->model = $model;
		$this->className = get_called_class();
	}

	/**
	 * @param array $attributes
	 *
	 * @return Model
	 */
	public function create(array $attributes): Model {
		return $this->getQueryBuilder()->create($attributes);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function getQueryBuilder(): Builder {
		return $this->model->newQuery();
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function getModel(): Model {
		return $this->model;
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function all(): Collection {
		return $this->model->all();
	}

	/**
	 * @param $id
	 */
	public function find($id) {
		return $this->getQueryBuilder()->find($id);
	}

	/**
	 * @param array $criteria
	 *
	 * @return mixed
	 */
	public function findOneBy(array $criteria): ?Model {
		return $this->getQueryBuilder()->where($criteria)->get()->first();
	}

	/**
	 * @return string
	 */
	public function getTableName(): string {
		return Str::pascalToSnakeCase(substr($this->getClassName(), 0, -strlen('Repository')));
	}

	/**
	 * @return string
	 */
	public function getClassName(): string {
		return $this->className;
	}

	/**
	 * @param array      $criteria
	 * @param array|null $orderBy
	 * @param null       $limit
	 * @param null       $offset
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): Collection {
		$builder = $this->getQueryBuilder()->where($criteria);

		foreach ($orderBy as $column => $direction) {
			$builder->orderBy($column, $direction);
		}

		$builder->limit($limit)
		        ->offset($offset);

		return $builder->get();
	}

	/**
	 * @param $name
	 * @param $arguments
	 *
	 * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|object|null
	 * @throws \ReflectionException
	 */
	public function __call($name, $arguments) {
		//$class = get_called_class();
		$class = new ReflectionClass($this);

		if ($class->hasMethod($name)) {
			return $this->$name(...$arguments);
		}

		$class = new ReflectionClass($this->model);
		if ($class->hasMethod($name)) {
			return $this->model->$name(...$arguments);
		}

		$builder = $this->getQueryBuilder();
		$class = new ReflectionClass(get_class($builder));

		if ($class->hasMethod($name)) {
			return $builder->$name(...$arguments);
		}

		$method = explode('_', Str::snake($name));
		$isFind = false;
		$onlyOne = false;
		$hasBy = false;
		$hasOr = false;
		$field = [];
		$result = null;

		collect($method)->each(
			function ($item, $k) use (&$field, &$isFind, &$onlyOne, &$hasBy, &$hasOr) {
				if ($item === 'find' || $item === 'select' || $item === 'get') $isFind = true;
				else if ($item === 'one') $onlyOne = true;
				else if ($item === 'by') $hasBy = true;
				else if ($item === 'or') {
					$hasOr = true;
					$field[] = '';
				}
				else if ($item === 'and') $field[] = '';
				else {
					$field[] = $item;
				}
			}
		);

		$field = implode('_', $field);
		$field = explode('__', $field);
		$fields = [];
		collect($field)->each(
			function ($item, $key) use ($arguments, &$fields, &$onlyOne) {
				$fields[$item] = count($arguments) === 1 ? $arguments[0] : $arguments[$key];
			}
		);

		if ($isFind) {
			if ($hasBy) {
				if ($hasOr) {
					$builder->orWhere($fields);
				}
				else {
					$builder->where($fields);
				}
			}
			else {
				$builder->find($arguments[0]);
			}

			if ($onlyOne) {
				$result = $builder->first();
			}
			else {
				$result = $builder->get();
			}
		}

		return $result;
	}

	/**
	 * @param string $query
	 * @param array  $bindings
	 */
	protected function statement(string $query, array $bindings = []) {
		$this->getConnection()->statement($query, $bindings);
	}

	/**
	 * @return \Illuminate\Database\ConnectionInterface
	 */
	protected function getConnection(): ConnectionInterface {
		return $this->model->getConnection();
	}
}
