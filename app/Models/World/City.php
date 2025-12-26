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
 * @file   City.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Models\World;

use App\Models\ModelBase;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class City extends ModelBase {
	/**
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.world_cities'));

		parent::__construct($attributes);
	}

	public function state(): ?HasOne {
		return $this->hasOne(State::class, 'state_id', 'id');
	}

	public function districts(): HasMany {
		return $this->hasMany(District::class, 'city_id', 'id');
	}
}
