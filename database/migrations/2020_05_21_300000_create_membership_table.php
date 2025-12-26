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
 * @file   2020_05_21_300000_create_membership_table.php
 * @date   2020-10-29 5:31:14
 */

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateMembershipTable extends MigrationBase {
    public function __construct() {
        parent::__construct('master', 'membership');
    }

    protected function create(Blueprint $table, Builder $schema) {
        $table->id();
        $this->unsignedBigIntegerForeign('user_id', 'id', 'master.users');
        $table->string('name');
        $table->string('role');
	    $this->unsignedBigIntegerForeign('team_id', 'id', 'master.teams');
        $table->timestamps();
    }
}
