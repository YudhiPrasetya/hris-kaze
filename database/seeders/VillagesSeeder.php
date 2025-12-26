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
 * @file   VillagesSeeder.php
 * @date   2020-10-29 5:31:14
 */

namespace Database\Seeders;

use App\Helpers\SeederBase;
use App\Repositories\Eloquent\World\CityRepository;
use App\Repositories\Eloquent\World\DistrictRepository;
use App\Repositories\Eloquent\World\StateRepository;
use Illuminate\Support\Facades\DB;


class VillagesSeeder extends SeederBase {
	private StateRepository $stateRepository;

	private CityRepository $cityRepository;

	private DistrictRepository $districtRepository;

	public function __construct(StateRepository $stateRepository, CityRepository $cityRepository, DistrictRepository $districtRepository) {
		parent::__construct();

		$this->stateRepository = $stateRepository;
		$this->cityRepository = $cityRepository;
		$this->districtRepository = $districtRepository;
	}

	/**
	 * @throws \JsonException
	 */
	public function __invoke(array $parameters = []) {
		$seeder = new VillagesSeeder(...$parameters);
		$seeder->run();
	}

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 * @throws \JsonException
	 */
	public function run(): void {
		$adm2 = collect($this->get('world/administrative_2'));
		$adm3 = collect($this->get('world/administrative_3'));
		$data = collect($this->get('world/administrative_4'));
		$rows = collect([]);
		$progress = $this->out->createProgressBar($data->count());
		$progress->setFormat($this->defaultProgressFormat('Villages'));
		$progress->start();
		$conn = $this->getConnection('master');

		$data->sort()->each(function ($data, $i) use ($progress, $conn, $adm2, $adm3, &$rows) {
			$adm2key = $adm2->search(fn($item, $key) => array_search($data['adm2_code'], $item) !== false);
			$adm3key = $adm3->search(fn($item, $key) => array_search($data['adm3_code'], $item) !== false);
			$success = false;

			if ($adm3key !== false && $adm2key !== false) {
				$_city = $adm2->get($adm2key);
				$_district = $adm3->get($adm3key);

				$state = $this->stateRepository->findOneByCodeAndCountryId($data['state_code'], 'ID');
				$city = $this->cityRepository->findOneByNameAndStateIdAndCountryId($_city['name'], $state->id, 'ID');

				if (!is_null($state) && !is_null($city)) {
					$district = $this->districtRepository->findOneByNameAndCityIdAndStateIdAndCountryId($_district['name'], $city->id, $state->id, 'ID');

					if (!is_null($district)) {
						$date = new \DateTime();
						$data['state_id'] = $state->id;
						$data['city_id'] = $city->id;
						$data['district_id'] = $district->id;
						$data['created_at'] = $date;
						$data['updated_at'] = $date;
						$rows->add($this->key_filter($data, 'id', 'code', 'state_code', 'adm1_code', 'adm1_id', 'adm2_code', 'adm2_id', 'adm3_code', 'adm3_id'));
						$success = true;
					}
				}
			}

			if (!$success) {
				$this->logFailed(json_encode($data, JSON_THROW_ON_ERROR));
			}

			$progress->setMessage($this->getMessage($success, "(<fg=yellow>%s</>) <fg=blue>%s</>", $data['country_id'], $data['name']));
			$progress->advance();

			if ($rows->count() >= 1000 || $progress->getProgress() == $progress->getMaxSteps()) {
				$conn->table('world_villages')->insert($rows->toArray());
				$conn->flushQueryLog();
				$rows = collect([]);
			}
		});

		$progress->setMessage('Done!');
		$progress->finish();
		$this->out->writeln('');
	}
}
