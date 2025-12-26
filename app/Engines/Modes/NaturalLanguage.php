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
 * @file   NaturalLanguage.php
 * @date   2020-09-28 14:29:33
 */

namespace App\Engines\Modes;

use Laravel\Scout\Builder;


class NaturalLanguage extends Mode {
	public function buildWhereRawString(Builder $builder) {
		$queryString = '';
		$queryString .= $this->buildWheres($builder);
		$indexFields = implode(',', $this->modelService->setModel($builder->model)->getFullTextIndexFields());
		$queryString .= "MATCH($indexFields) AGAINST(? IN NATURAL LANGUAGE MODE";

		if (config('scout.mysql.query_expansion')) {
			$queryString .= ' WITH QUERY EXPANSION';
		}

		$queryString .= ')';

		return $queryString;
	}

	public function buildSelectColumns(Builder $builder) {
		$indexFields = implode(',', $this->modelService->setModel($builder->model)->getFullTextIndexFields());

		return "*, MATCH($indexFields) AGAINST(? IN NATURAL LANGUAGE MODE) AS relevance";
	}

	public function buildParams(Builder $builder) {
		$this->whereParams[] = $builder->query;

		return $this->whereParams;
	}

	public function isFullText() {
		return true;
	}
}