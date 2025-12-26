<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

class AddTwoFactorColumnsToUsersTable extends MigrationBase
{
	public function __construct() {
		parent::__construct('master', 'users');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		$this->schema->table(
			'users',
			function (Blueprint $table) {
				$table->dropColumn('two_factor_secret');
				$table->dropColumn('two_factor_recovery_codes');
			}
		);
	}

	protected function creates(Builder $schema) {
		$schema->table(
			'users',
			function (Blueprint $table) {
				$table->text('two_factor_secret')
				      ->after('password')
				      ->nullable();

				$table->text('two_factor_recovery_codes')
				      ->after('two_factor_secret')
				      ->nullable();
			}
		);
	}
}
