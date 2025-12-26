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
 * @file   CitiesSeeder.php
 * @date   2020-10-29 5:31:14
 */

namespace Database\Seeders;

use App\Helpers\SeederBase;
use App\Repositories\Eloquent\World\CountryRepository;
use App\Repositories\Eloquent\World\StateRepository;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\ProgressBar;


class CitiesSeeder extends SeederBase {
	private CountryRepository $countryRepository;

	private StateRepository $stateRepository;

	private ProgressBar $progress;

	private ConnectionInterface $connection;

	public function __construct(CountryRepository $countryRepository, StateRepository $stateRepository) {
		parent::__construct();

		$this->countryRepository = $countryRepository;
		$this->stateRepository = $stateRepository;
	}

	/**
	 * @throws \JsonException
	 */
	public function __invoke(array $parameters = []) {
		$seeder = new CitiesSeeder(...$parameters);
		$seeder->run();
	}

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 * @throws \JsonException
	 */
	public function run(): void {
		$cities = $this->getJson('world/cities');
		$kota = collect($this->get('world/administrative_2'));
		$this->progress = $this->out->createProgressBar($cities->count() + $kota->count());
		$this->progress->setFormat($this->defaultProgressFormat('Cities'));
		$this->progress->start();
		$this->connection = $this->getConnection('master');

		$this->addIndonesiaCities($kota);
		$this->addWorldCities($cities);

		$this->progress->setMessage('Done!');
		$this->progress->finish();
		$this->out->writeln('');
	}

	/**
	 * @throws \JsonException
	 */
	protected function addWorldCities(Collection $cities): void {
		$rows = collect([]);

		$cities->sort()->each(function ($data, $i) use (&$rows, $cities) {
			if ($data['country_code'] !== 'ID') {
				$country = $this->countryRepository->findOneByIso($data['country_code']);
				$state = $this->stateRepository->findOneByCodeAndCountryId($data['state_code'], $data['country_code']);

				if ($country && $state) {
					$date = new \DateTime();
					$data['state_id'] = $state->id;
					$data['country_id'] = $country->iso;
					$data['created_at'] = $date;
					$data['updated_at'] = $date;
					$rows->add($this->key_filter($data, 'id', 'state_code', 'country_code'));
				}

				if (!($country && $state)) {
					$this->logFailed(json_encode($data, JSON_THROW_ON_ERROR));
				}

				$this->progress->setMessage($this->getMessage(($country && $state),
					"(<fg=yellow>%s - %s</>) <fg=blue>%s</>",
					$data['country_code'],
					$state->code,
					$data['name']));
				$this->progress->advance();

				if ($rows->count() >= 1000 || $this->progress->getProgress() == $this->progress->getMaxSteps()) {
					$this->connection->table('world_cities')->insert($rows->toArray());
					$this->connection->flushQueryLog();
					$rows = collect([]);
				}
			}
		});

		$this->connection->table('world_cities')->insert($rows->toArray());
		$this->connection->flushQueryLog();
	}

	/**
	 * @throws \JsonException
	 */
	protected function addIndonesiaCities(Collection $cities): void {
		$rows = collect([]);

		$cities->sort()->each(function ($data, $i) use (&$rows) {
			$state = $this->stateRepository->findOneByCodeAndCountryId($data['state_code'], $data['country_id']);

			if ($state) {
				$date = new \DateTime();
				$data['state_id'] = $state->id;
				$data['longitude'] = 0;
				$data['latitude'] = 0;
				$data['created_at'] = $date;
				$data['updated_at'] = $date;
				$rows->add($this->key_filter($data, 'id', 'code', 'state_code', 'adm1_code', 'adm1_id'));
			}

			if (is_null($state)) {
				$this->logFailed(json_encode($data, JSON_THROW_ON_ERROR));
			}

			$this->progress->setMessage($this->getMessage(!is_null($state),
				"(<fg=yellow>%s - %s</>) <fg=blue>%s</>",
				$data['country_id'],
				$state->code,
				$data['name']));
			$this->progress->advance();

			if ($rows->count() >= 1000 || $this->progress->getProgress() == $this->progress->getMaxSteps()) {
				$this->connection->table('world_cities')->insert($rows->toArray());
				$this->connection->flushQueryLog();
				$rows = collect([]);
			}
		});

		$this->connection->table('world_cities')->insert($rows->toArray());
		$this->connection->flushQueryLog();
	}
}
