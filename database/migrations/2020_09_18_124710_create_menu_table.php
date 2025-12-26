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
 * @file   2020_09_18_124710_create_menu_table.php
 * @date   2020-10-29 5:31:14
 */

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;


class CreateMenuTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'menu');
	}

	protected function create(Blueprint $table, Builder $schema) {
		$table->id();
		$table->string('name')->nullable();
		$this->unsignedBigIntegerForeign('parent_id', 'id', 'master.menu', true)->nullable();
		$table->string('title')->nullable();
		$table->string('route')->nullable();
		$table->json('attrs')->nullable();
		$this->unsignedBigIntegerForeign('role_id', 'id', 'master.roles', true);
		$table->boolean('divider')->default(false);
		$table->boolean('enabled')->default(true);
		$table->unsignedInteger('level')->default(0);
		$table->timestamps();
		$table->softDeletes();
	}
}
