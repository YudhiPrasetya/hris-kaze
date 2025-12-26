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
 * @file   Like.php
 * @date   2020-09-28 14:27:39
 */

namespace App\Engines\Modes;

use Laravel\Scout\Builder;


class Like extends Mode {
	protected $fields;

	public function buildWhereRawString(Builder $builder) {
		$queryString = '';
		$this->fields = $this->modelService->setModel($builder->model)->getSearchableFields();
		$queryString .= $this->buildWheres($builder);
		$queryString .= '(';

		foreach ($this->fields as $field) {
			$queryString .= "`$field` LIKE ? OR ";
		}

		$queryString = trim($queryString, 'OR ');
		$queryString .= ')';

		return $queryString;
	}

	public function buildParams(Builder $builder) {
		for ($itr = 0; $itr < count($this->fields); ++$itr) {
			$this->whereParams[] = '%' . $builder->query . '%';
		}

		return $this->whereParams;
	}

	public function isFullText() {
		return false;
	}
}