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
 * @file   2020_09_18_144456_create_cities_table.php
 * @date   2020-10-29 5:31:14
 */

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateCitiesTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'world_cities');
	}

	/**
	 * @param \Illuminate\Database\Schema\Blueprint $table
	 * @param \Illuminate\Database\Schema\Builder   $schema
	 *
	 * @throws \App\Exceptions\SchemaNotFoundException
	 * @throws \App\Exceptions\TableNotFoundException
	 */
	protected function create(Blueprint $table, Builder $schema) {
		$table->id();
		$table->string('name');
		//$table->string('ascii_name');
		//$table->integer('geonameid');
		$this->unsignedBigIntegerForeign('state_id', 'id', 'master.world_states');
		$this->stringForeign('country_id', 2, 'iso', 'master.world_countries');
		$table->double('longitude')->default(0);
		$table->double('latitude')->default(0);
		$table->timestamps();
		$table->softDeletes();
	}
}
