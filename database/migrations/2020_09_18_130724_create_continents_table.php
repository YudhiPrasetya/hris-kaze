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
 * @file   2020_09_18_130724_create_continents_table.php
 * @date   2020-10-29 5:31:14
 */

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;


class CreateContinentsTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'world_continents');
	}

	protected function create(Blueprint $table, Builder $schema) {
		$table->string('code', 2)->primary();
		$table->string('name');
		$table->timestamps();
		$table->softDeletes();
	}
}
