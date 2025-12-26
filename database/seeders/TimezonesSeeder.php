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
 * @file   TimezonesSeeder.php
 * @date   2021-09-16 10:21:50
 */

namespace Database\Seeders;

use App\Helpers\SeederBase;
use App\Repositories\Eloquent\World\TimezoneAbbreviationRepository;
use App\Repositories\Eloquent\World\ZoneRepository;


class TimezonesSeeder extends SeederBase {
	private TimezoneAbbreviationRepository $abbreviationRepository;

	private ZoneRepository $zoneRepository;

	public function __construct(TimezoneAbbreviationRepository $abbreviationRepository, ZoneRepository $zoneRepository) {
		parent::__construct();
		$this->abbreviationRepository = $abbreviationRepository;
		$this->zoneRepository = $zoneRepository;
	}

	public function __invoke(array $parameters = []) {
		$seeder = new TimezonesSeeder(...$parameters);
		$seeder->run();
	}

	public function run(): void {
		$data = collect(require(resource_path('seeds/world/timezones.php')));
		$rows = collect([]);
		$progress = $this->out->createProgressBar($data->count());
		$progress->setFormat($this->defaultProgressFormat('Timezones'));
		$progress->start();
		$conn = $this->getConnection('master');

		$data->sort()->each(function ($data, $i) use ($progress, $conn, &$rows) {
			$abbr = $this->abbreviationRepository->findOneByCodeAndName($data['abbreviation'], $data['abbreviation_desc']);
			$zone = $this->zoneRepository->findOneByNameAndCountryId($data['zone'], $data['country_id']);
			$date = new \DateTime();
			$data['zone_id'] = $zone?->id;
			$data['timezone_abbreviation_id'] = $abbr?->id;
			$data['created_at'] = $date;
			$data['updated_at'] = $date;
			if ($zone) $rows[] = $this->key_filter($data, 'id', 'zone', 'abbreviation', 'abbreviation_desc', 'country_id');

			$progress->setMessage(
				$this->getMessage(!is_null($zone),
					"(<fg=yellow>%s</>) <fg=blue>%s</>",
					$data['abbreviation'],
					$data['zone'])
			);

			$progress->advance();

			if ($rows->count() >= 1000 || $progress->getProgress() == $progress->getMaxSteps()) {
				$conn->table('world_timezones')->insert($rows->toArray());
				$conn->flushQueryLog();
				$rows = collect([]);
			}
		});

		$progress->setMessage('Done!');
		$progress->finish();
		$this->out->writeln('');
	}
}