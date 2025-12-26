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
 * @file   HasTrack2.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;


trait HasTrack2 {
	public function scopeWhereCardNumber(Builder $query, $search, $column = 'track_2_data') {
		$self = $this;
		$query->where(function ($q) use ($column, $search, $self) {
			$q->orWhere($column, 'LIKE', "%{$search}%D%");
		});
	}
}
