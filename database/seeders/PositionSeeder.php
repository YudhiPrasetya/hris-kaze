<?php

namespace Database\Seeders;

use App\Helpers\SeederBase;
use App\Models\Position;


class PositionSeeder extends SeederBase {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$this->out->writeln('[#] Adding positions');
		$positions = [
			'Director',
			'Manager',
			'Sales',
		];

		foreach ($positions as $p) {
			$position = new Position(array_remove_empty(['name' => $p]));
			$position->save();
		}
	}
}
