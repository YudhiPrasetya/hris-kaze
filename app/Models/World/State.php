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
 * @file   State.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Models\World;

use App\Models\ModelBase;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class State extends ModelBase {
	/**
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.world_states'));

		parent::__construct($attributes);
	}

	public function country(): ?HasOne {
		return $this->hasOne(Country::class, 'iso', 'country_id');
		// return $this->belongsTo(Country::class, 'iso', 'country_id');
	}

	public function cities(): HasMany {
		return $this->hasMany(City::class, 'state_id', 'id');
	}
}
