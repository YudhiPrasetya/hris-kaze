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
 * @file   2020_09_18_144421_create_timezones_table.php
 * @date   2020-10-29 5:31:14
 */

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;


class CreateTimezonesTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'world_timezones');
	}

	protected function create(Blueprint $table, Builder $schema) {
		$table->id();

		// $this->unsignedBigIntegerForeign('timezone_abbreviation_id', 'id', 'world_timezone_abbreviations');
		$this->unsignedBigIntegerForeign('zone_id', 'id', 'master.world_zones');
		$this->unsignedBigIntegerForeign('timezone_abbreviation_id', 'id', 'master.world_timezone_abbreviations', true);
		//$table->string('abbreviation', '6');
		//$table->string('abbreviation_desc')->nullable();
		$table->decimal('time_start', 11, 0);
		$table->integer('gmt_offset');
		$table->char('dst', 1);
		$table->string('utc_offset');
		$table->timestamps();
		$table->softDeletes();
	}
}
