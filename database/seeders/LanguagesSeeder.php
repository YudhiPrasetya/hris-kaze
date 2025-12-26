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
 * @file   LanguagesSeeder.php
 * @date   2021-09-16 10:33:8
 */

namespace Database\Seeders;

use App\Helpers\SeederBase;
use App\Repositories\Eloquent\World\CountryRepository;


class LanguagesSeeder extends SeederBase {
	private CountryRepository $repository;

	public function __construct(CountryRepository $repository) {
		parent::__construct();
		$this->repository = $repository;
	}

	public function __invoke(array $parameters = []) {
		$seeder = new LanguagesSeeder(...$parameters);
		$seeder->run();
	}

	public function run(): void {
		$data = collect(require(resource_path('seeds/world/languages.php')));
		$progress = $this->out->createProgressBar($data->count());
		$progress->setFormat($this->defaultProgressFormat('Languages'));
		$progress->start();
		$conn = $this->getConnection('master');

		$data->sort()->each(function ($data, $i) use ($conn, $progress) {
			$ret = false;
			$country = $this->repository->findOneByIso3($data['CountryCode']);

			if (!empty($country)) {
				$data['country_id'] = $country->iso;
				$data['is_official'] = $data['is_official'] == 'T';

				$ret = $conn->table('world_languages')->insert($this->key_filter($data, 'CountryCode'));
				$conn->flushQueryLog();
			}

			$progress->setMessage($this->getMessage($ret, "(<fg=yellow>%s</>) <fg=blue>%s</>", $data['CountryCode'], $data['name']));
			$progress->advance();
		});

		$progress->setMessage('Done!');
		$progress->finish();
		$this->out->writeln('');
	}
}