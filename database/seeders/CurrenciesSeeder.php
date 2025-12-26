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
 * @file   CurrenciesSeeder.php
 * @date   2021-09-16 10:15:37
 */

namespace Database\Seeders;

use App\Helpers\SeederBase;


class CurrenciesSeeder extends SeederBase {
	public function run(): void {
		## Currencies
		$data = collect(require(resource_path('seeds/world/currencies.php')));
		$progress = $this->out->createProgressBar($data->count());
		$progress->setFormat($this->defaultProgressFormat('Currencies'));
		$progress->start();
		$conn = $this->getConnection('master');

		$data->sort()->each(function ($data, $i) use ($conn, $progress) {
			$ret = $conn->table('world_currencies')->insert($data);
			$conn->flushQueryLog();
			$progress->setMessage($this->getMessage($ret, "(<fg=yellow>%s</>) <fg=blue>%s</>", $data['symbol'], $data['name']));
			$progress->advance();
		});

		$progress->setMessage('Done!');
		$progress->finish();
		$this->out->writeln('');
	}
}