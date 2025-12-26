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
 * @file   JetstreamSeeder.php
 * @date   2020-10-29 5:31:14
 */

namespace Database\Seeders;

use App\Helpers\SeederBase;
use App\Models\Membership;
use App\Models\Team;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\Facades\DB;


class JetstreamSeeder extends SeederBase {

    private $user;

    public function __construct(UserRepository $userRepository) {
        parent::__construct();
        $this->user = $userRepository->findOneByUsername('admin');
    }

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        $this->out->writeln('[#] Adding default Jetstream teams');
        $team = new Team(['name' => 'Super Administrator', 'personal_team' => true]);
        $team->setOwner($this->user)->save();
        $membership = new Membership(['name' => 'Super Administrator', 'team_id' => $team->id, 'role' => 'super-admin']);
        $membership->setUser($this->user)->save();
        DB::statement('INSERT INTO team_user(team_id, user_id, role) VALUE(1, 1, "super-admin")');
    }
}
