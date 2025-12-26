<?php
/*
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   ZonesSeeder.php
 * @date   2021-09-16 10:36:54
 */

namespace Database\Seeders;

use App\Helpers\SeederBase;
use App\Repositories\Eloquent\World\CountryRepository;


class ZonesSeeder extends SeederBase {
	private CountryRepository $repository;

	public function __construct(CountryRepository $repository) {
		parent::__construct();
		$this->repository = $repository;
	}

	public function __invoke(array $parameters = []) {
		$seeder = new ZonesSeeder(...$parameters);
		$seeder->run();
	}

	public function run(): void {
		$data = collect(require(resource_path('seeds/world/zones.php')));
		$progress = $this->out->createProgressBar($data->count());
		$progress->setFormat($this->defaultProgressFormat('Zones'));
		$progress->start();
		$conn = $this->getConnection('master');

		$data->sort()->each(function ($data, $i) use ($conn, $progress) {
			$ret = false;
			$country = $this->repository->findOneByIso3($data['iso_alpha_3']);

			if ($country) {
				$data['country_id'] = $country->iso;
				$ret = $conn->table('world_zones')->insert($this->key_filter($data, 'iso_alpha_3'));
				$conn->flushQueryLog();
			}

			$progress->setMessage($this->getMessage($ret, "(<fg=yellow>%s</>) <fg=blue>%s</>", $data['iso_alpha_3'], $data['name']));
			$progress->advance();
		});

		$progress->setMessage('Done!');
		$progress->finish();
		$this->out->writeln('');
	}
}