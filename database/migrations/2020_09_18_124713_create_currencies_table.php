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
 * @file   2020_09_18_124713_create_currencies_table.php
 * @date   2020-10-29 5:31:14
 */

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;


class CreateCurrenciesTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'world_currencies');
	}

	protected function create(Blueprint $table, Builder $schema) {
		$table->string('code', 6)->primary();
		$table->string('name');
		$table->string('symbol')->nullable();
		$table->timestamps();
		$table->softDeletes();
	}
}
