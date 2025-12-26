<?php

namespace Database\Seeders;

use App\Helpers\SeederBase;
use App\Models\Position;
use App\Models\Priority;
use Illuminate\Database\Seeder;

class PrioritySeeder extends SeederBase {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$this->out->writeln('[#] Adding level of urgencies');
		$urgencies = [
			1 => 'When Possible',
			2 => 'Soon',
			3 => 'Quickly',
			4 => 'Very Quickly',
			5 => 'Immediate',
		];

		foreach ($urgencies as $level => $p) {
			$priority = new Priority(array_remove_empty(['name' => $p, 'level' => $level]));
			$priority->save();
		}
	}
}