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
 * @file   Currency.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Models\World;

use App\Models\ModelBase;


class Currency extends ModelBase {
	/**
	 * Currency constructor.
	 *
	 * @param array $attributes
	 *
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.world_currencies'));

		parent::__construct($attributes);
	}
}
