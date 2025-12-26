<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateOauthClientsTable extends MigrationBase {
    /**
     * Create a new migration instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct('master', 'oauth_clients');
    }

    protected function create(Blueprint $table, Builder $schema) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('name');
        $table->string('secret', 100)->nullable();
        $table->string('provider')->nullable();
        $table->text('redirect');
        $table->boolean('personal_access_client');
        $table->boolean('password_client');
        $table->boolean('revoked');
        $table->timestamps();

        $this->createIndex('user_id');
    }
}
