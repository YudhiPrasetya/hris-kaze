<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateOauthPersonalAccessClientsTable extends MigrationBase {
    /**
     * Create a new migration instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct('master', 'oauth_personal_access_clients');
    }

    protected function create(Blueprint $table, Builder $schema) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('client_id');
        $table->timestamps();
    }
}
