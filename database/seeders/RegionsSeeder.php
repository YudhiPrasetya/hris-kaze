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
 * @file   RegionsSeeder.php
 * @date   2021-09-16 10:24:7
 */

namespace Database\Seeders;

use App\Helpers\SeederBase;
use App\Repositories\Eloquent\World\ContinentRepository;


class RegionsSeeder extends SeederBase {
	private ContinentRepository $repository;

	public function __construct(ContinentRepository $repository) {
		parent::__construct();
		$this->repository = $repository;
	}

	public function __invoke(array $parameters = []) {
		$seeder = new RegionsSeeder(...$parameters);
		$seeder->run();
	}

	public function run(): void {
		$data = collect(require(resource_path('seeds/world/regions.php')));
		$progress = $this->out->createProgressBar($data->count());
		$progress->setFormat($this->defaultProgressFormat('Regions'));
		$progress->start();
		$conn = $this->getConnection('master');

		$data->sort()->each(function ($data, $i) use ($conn, $progress) {
			$ret = false;

			if ($this->repository->findOneByCode($data['continent_id'])) {
				$ret = $conn->table('world_regions')->insert($this->key_filter($data, 'id'));
				$conn->flushQueryLog();
			}

			$progress->setMessage($this->getMessage($ret, "(<fg=yellow>%s</>) <fg=blue>%s</>", $data['continent_id'], $data['name']));
			$progress->advance();
		});

		$progress->setMessage('Done!');
		$progress->finish();
		$this->out->writeln('');
	}
}