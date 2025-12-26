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
 * @file   CountriesSeeder.php
 * @date   2021-09-16 10:28:58
 */

namespace Database\Seeders;

use App\Helpers\SeederBase;
use App\Repositories\Eloquent\World\CurrencyRepository;
use App\Repositories\Eloquent\World\RegionRepository;


class CountriesSeeder extends SeederBase {
	private RegionRepository $regionRepository;

	private CurrencyRepository $currencyRepository;

	public function __construct(RegionRepository $regionRepository, CurrencyRepository $currencyRepository) {
		parent::__construct();
		$this->regionRepository = $regionRepository;
		$this->currencyRepository = $currencyRepository;
	}

	public function __invoke(array $parameters = []) {
		$seeder = new CountriesSeeder(...$parameters);
		$seeder->run();
	}

	public function run(): void {
		$data = collect(require(resource_path('seeds/world/countries.php')));
		$progress = $this->out->createProgressBar($data->count());
		$progress->setFormat($this->defaultProgressFormat('Countries'));
		$progress->start();
		$conn = $this->getConnection('master');

		$data->sort()->each(function ($data, $i) use ($conn, $progress) {
			$region = $this->regionRepository->findOneByName($data['region']);
			$currency = $this->currencyRepository->findOneByCode($data['currency_code']);

			$date = new \DateTime();
			$data['continent_id'] = $data['continent'];
			$data['region_id'] = $region?->id;
			$data['currency_id'] = $currency?->code;
			$data['north'] = $data['north'] ?? 0;
			$data['south'] = $data['south'] ?? 0;
			$data['east'] = $data['east'] ?? 0;
			$data['west'] = $data['west'] ?? 0;
			$data['longitude'] = $data['longitude'] ?? 0;
			$data['latitude'] = $data['latitude'] ?? 0;
			$data['created_at'] = $date;
			$data['updated_at'] = $date;
			$data = $this->key_filter($data, 'region', 'continent', 'currency_code', 'currency_name', 'neighbours');
			$ret = $conn->table('world_countries')->insert($data);
			$conn->flushQueryLog();

			$progress->setMessage($this->getMessage($ret, "(<fg=yellow>%s</>) <fg=blue>%s</>", $data['iso'], $data['name']));
			$progress->advance();
		});

		$progress->setMessage('Done!');
		$progress->finish();
		$this->out->writeln('');
	}
}