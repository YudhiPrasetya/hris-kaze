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
 * @file   2020_09_18_144334_create_countries_table.php
 * @date   2020-10-29 5:31:14
 */

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;


class CreateCountriesTable extends MigrationBase {
	public function __construct() {
		parent::__construct('master', 'world_countries');
	}

	protected function create(Blueprint $table, Builder $schema) {
		$table->string('iso', 2)->primary();
		$table->string('iso3', 3);
		$table->unsignedInteger('iso_numeric')->nullable();
		$table->string('fips', 2)->nullable();
		$table->string('equivalent_fips_code')->nullable();
		$table->unsignedInteger('geonameid');
		$table->string('name');
		$table->string('local_name')->nullable();
		$table->string('capital')->nullable();
		$table->decimal('area', 10, 2)->nullable();
		$this->stringForeign('continent_id', 2, 'code', 'master.world_continents');
		$this->unsignedBigIntegerForeign('region_id', 'id', 'master.world_regions', true);
		//$table->text('neighbours')->nullable();
		$table->integer('population')->nullable();
		$table->string('languages')->nullable();
		$table->smallInteger('indep_year')->nullable();
		$table->string('government_form')->nullable();
		$table->string('head_of_state')->nullable();
		$this->stringForeign('currency_id', 6, 'code', 'master.world_currencies', true);
		$table->string('tld')->nullable();
		$table->string('postal_code_format')->nullable();
		$table->string('postal_code_regex')->nullable();
		$table->string('phone')->nullable();
		$table->decimal('life_expectancy',3,1)->nullable();
		$table->double('gnp')->nullable();
		$table->double('gnp_old')->nullable();
		$table->double('north')->default(0);
		$table->double('south')->default(0);
		$table->double('east')->default(0);
		$table->double('west')->default(0);
		$table->double('longitude')->default(0);
		$table->double('latitude')->default(0);
		$table->timestamps();
		$table->softDeletes();

		$this->createIndex('iso3');
	}
}
