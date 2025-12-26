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
 * @file   StatesSeeder.php
 * @date   2020-10-29 5:31:14
 */

namespace Database\Seeders;

use App\Helpers\SeederBase;
use App\Repositories\Eloquent\World\CountryRepository;


class StatesSeeder extends SeederBase {
	private CountryRepository $repository;

	public function __construct(CountryRepository $repository) {
		parent::__construct();
		$this->repository = $repository;
	}

	/**
	 * @throws \JsonException
	 */
	public function __invoke(array $parameters = []) {
		$seeder = new StatesSeeder(...$parameters);
		$seeder->run();
	}

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 * @throws \JsonException
	 */
	public function run(): void {
		$data = $this->getJson('world/states');
		$progress = $this->out->createProgressBar($data->count());
		$progress->setFormat($this->defaultProgressFormat('States'));
		$progress->start();
		$rows = collect([]);
		$conn = $this->getConnection('master');

		$data->sort()->each(function ($data, $i) use ($conn, $progress, &$rows) {
			$country = $this->repository->findOneByIso($data['country_code']);

			if ($country) {
				$date = new \DateTime();
				$data['code'] = $data['state_code'];
				$data['country_id'] = $country->iso;
				$data['created_at'] = $date;
				$data['updated_at'] = $date;
				$rows->add($this->key_filter($data, 'id', 'country_code', 'state_code'));
			}

			if (is_null($country)) {
				$this->logFailed(json_encode($data, JSON_THROW_ON_ERROR));
			}

			$progress->setMessage($this->getMessage(!is_null($country), "(<fg=yellow>%s</>) <fg=blue>%s</>", $data['country_code'], $data['name']));
			$progress->advance();

			if ($rows->count() >= 1000 || $progress->getProgress() == $progress->getMaxSteps()) {
				$conn->table('world_states')->insert($rows->toArray());
				$conn->flushQueryLog();
				$rows = collect([]);
			}
		});

		$progress->setMessage('Done!');
		$progress->finish();
		$this->out->writeln('');
	}
}
