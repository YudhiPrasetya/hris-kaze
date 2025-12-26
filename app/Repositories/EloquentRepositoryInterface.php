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
 * @file   EloquentRepositoryInterface.php
 * @date   03/09/2020 09:02
 */

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


/**
 * Interface EloquentRepositoryInterface
 *
 * @package App\Repositories
 */
interface EloquentRepositoryInterface {
	/**
	 * @return \Illuminate\Support\Collection
	 */
	public function all(): Collection;

	/**
	 * @param array $attributes
	 *
	 * @return Model
	 */
	public function create(array $attributes): Model;

	/**
	 * @param $id
	 *
	 * @return Model
	 */
	public function find($id);

	/**
	 * @param array $criteria
	 *
	 * @return \Illuminate\Database\Eloquent\Model|null
	 */
		public function findOneBy(array $criteria): ?Model;

	/**
	 * @param array      $criteria
	 * @param array|null $orderBy
	 * @param null       $limit
	 * @param null       $offset
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): Collection;
}