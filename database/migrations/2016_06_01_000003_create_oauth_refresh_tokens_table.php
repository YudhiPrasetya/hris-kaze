<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;


class CreateOauthRefreshTokensTable extends MigrationBase {
    /**
     * Create a new migration instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct('master', 'oauth_refresh_tokens');
    }

    protected function create(Blueprint $table, Builder $schema) {
        $table->string('id', 100)->primary();
        $table->string('access_token_id', 100)->index();
        $table->boolean('revoked');
        $table->dateTime('expires_at')->nullable();

        $this->createIndex('access_token_id');
    }
}
