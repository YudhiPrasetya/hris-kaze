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
 * @file   2020_09_18_144509_create_districts_table.php
 * @date   2020-10-29 5:31:14
 */

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateDistrictsTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'world_districts');
	}

	protected function create(Blueprint $table, Builder $schema) {
		$table->id();
		$table->string('name');
		$this->unsignedBigIntegerForeign('city_id', 'id', 'master.world_cities');
		$this->unsignedBigIntegerForeign('state_id', 'id', 'master.world_states');
		$this->stringForeign('country_id', 2, 'iso', 'master.world_countries');
		$table->timestamps();
		$table->softDeletes();
	}
}
