<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateOauthAccessTokensTable extends MigrationBase {
    /**
     * Create a new migration instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct('master', 'oauth_access_tokens');
    }

    protected function create(Blueprint $table, Builder $schema) {
        $table->string('id', 100)->primary();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->unsignedBigInteger('client_id');
        $table->string('name')->nullable();
        $table->text('scopes')->nullable();
        $table->boolean('revoked');
        $table->timestamps();
        $table->dateTime('expires_at')->nullable();

        $this->createIndex('user_id');
    }
}
