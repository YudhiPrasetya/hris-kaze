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
 * @file   LikeExpanded.php
 * @date   2020-09-28 14:28:21
 */

namespace App\Engines\Modes;

use Laravel\Scout\Builder;


class LikeExpanded extends Mode {
	protected $fields;

	public function buildWhereRawString(Builder $builder) {
		$queryString = '';
		$this->fields = $this->modelService->setModel($builder->model)->getSearchableFields();
		$queryString .= $this->buildWheres($builder);
		$words = explode(' ', $builder->query);
		$queryString .= '(';

		foreach ($this->fields as $field) {
			foreach ($words as $word) {
				$queryString .= "`$field` LIKE ? OR ";
			}
		}

		$queryString = trim($queryString, 'OR ');
		$queryString .= ')';

		return $queryString;
	}

	public function buildParams(Builder $builder) {
		$words = explode(' ', $builder->query);

		for ($i = 0; $i < count($this->fields); ++$i) {
			foreach ($words as $word) {
				$this->whereParams[] = '%' . $word . '%';
			}
		}

		return $this->whereParams;
	}

	public function isFullText() {
		return false;
	}
}