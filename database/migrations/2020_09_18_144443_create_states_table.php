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
 * @file   2020_09_18_144443_create_states_table.php
 * @date   2020-10-29 5:31:14
 */

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;


class CreateStatesTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'world_states');
	}

	protected function create(Blueprint $table, Builder $schema) {
		$table->id();
		$table->string('code', 45)->nullable();
		$table->string('name');
		//$table->string('ascii_name');
		//$table->integer('geonameid');
		$this->stringForeign('country_id', 2, 'iso', 'master.world_countries');
		$table->timestamps();
		$table->softDeletes();
	}
}
