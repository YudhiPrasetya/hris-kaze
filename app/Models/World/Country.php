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
 * @file   Country.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Models\World;

use App\Models\Merchant;
use App\Models\ModelBase;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Country extends ModelBase {
	public $incrementing = false;

	protected $primaryKey = 'iso';

	/**
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.world_countries'));

		parent::__construct($attributes);
	}

	public function states(): HasMany {
		return $this->hasMany(State::class, 'country_id', 'iso');
		// return $this->belongsToMany(State::class, Country::class, 'iso', 'iso', 'iso', 'country_id');
	}

	public function currencies(): HasMany {
		return $this->hasMany(Currency::class, 'country_id', 'iso');
	}
}
