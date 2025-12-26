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
 * @file   2020_09_16_144234_create_audits_table.php
 * @date   2020-10-30 6:9:57
 */

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateAuditsTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'audits');
	}

	protected function create(Blueprint $table, Builder $schema) {
		$table->id();
		$table->string('user_type')->nullable();
		//$table->unsignedBigInteger('user_id')->nullable();
		$this->unsignedBigIntegerForeign('user_id', 'id', 'master.users', true);
		$table->string('event');
		//$table->morphs('auditable');
		$table->string('auditable_type');
		$table->string('auditable_id');
		//$this->index(["auditable_type", "auditable_id"]);
		$this->createIndex('auditable_type', 'auditable_id');
		$table->longText('old_values')->nullable();
		$table->longText('new_values')->nullable();
		$table->text('url')->nullable();
		$table->ipAddress('ip_address')->nullable();
		$table->string('user_agent', 1023)->nullable();
		$table->string('tags')->nullable();
		$table->timestamps();

		$this->createIndex('user_id', 'user_type');
	}
}
