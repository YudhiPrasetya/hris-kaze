<?php

namespace Database\Seeders;

use App\Helpers\SeederBase;
use App\Models\AttendanceReason;
use Illuminate\Database\Seeder;

class AttendanceReasonSeeder extends SeederBase
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $this->out->writeln('[#] Adding attendance reasons');
	    $reasons = [
		    'Present',
		    'Sick',
		    'Business Trip',
		    'Permit',
		    'Absent',
		    'Annual Leave',
	    ];

	    foreach ($reasons as $p) {
		    $reason = new AttendanceReason(array_remove_empty(['name' => $p]));
		    $reason->save();
	    }
    }
}
