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
 * @file   2020_09_18_145847_create_languages_table.php
 * @date   2020-10-30 6:9:57
 */

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;


class CreateLanguagesTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'world_languages');
	}

	protected function create(Blueprint $table, Builder $schema) {
		$table->id();
		$table->string('name');
		$this->stringForeign('country_id', 2, 'iso', 'master.world_countries');
		$table->boolean('is_official')->default(false);
		$table->double('percentage');
		$table->timestamps();
		$table->softDeletes();
	}
}
