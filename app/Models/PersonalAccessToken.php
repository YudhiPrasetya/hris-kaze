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
 * @file   PersonalAccessToken.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;


class PersonalAccessToken extends ModelBase {
	use HasTimestamps;


	/**
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	public function __construct(array $attributes = []) {
		$this->setConnection(connection('master'));
		$this->setTable(table('master.personal_access_tokens', onlyName: true));

		parent::__construct($attributes);
	}
}
