<?php

namespace Database\Seeders;

use App\Helpers\SeederBase;
use App\Models\Gender;
use App\Models\Priority;
use Illuminate\Database\Seeder;

class GenderSeeder extends SeederBase
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $this->out->writeln('[#] Adding genders');
	    $genders = [
		    1 => 'Male',
		    2 => 'Female',
	    ];

	    foreach ($genders as $gender) {
		    $gender = new Gender(array_remove_empty(['name' => $gender]));
		    $gender->save();
	    }
    }
}
