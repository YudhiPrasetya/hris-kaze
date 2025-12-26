<?php

use App\Helpers\MigrationBase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;

class CreateUsersTable extends MigrationBase
{
	public function __construct() {
		parent::__construct('master', 'users');
	}

	protected function create(Blueprint $table, Builder $schema) {
		$table->id();
		$table->string('username');
		$table->string('name');
		$table->string('email');
		$table->timestamp('email_verified_at')->nullable();
		$table->string('password');
		$table->rememberToken();
		$table->string('current_team_id')->nullable();
		$table->text('profile_photo_path')->nullable();
		$table->json('config')->nullable();
		$table->boolean('enabled')->default(true);
		$table->timestamp('last_login')->nullable();
		$table->timestamps();
		$table->softDeletes();

		$this->createUnique('username');
		$this->createUnique('email');

	}

	protected function prepare() {
		$currentTable = $this->tableName;
		$currentSchema = $this->schemaName;
		$configs = config('settings.tables');

		foreach ($configs as $config) {
			$builder = Schema::connection($config['connection']);
			$builder->disableForeignKeyConstraints();
			$tables = $builder->getAllTables();

			foreach ($tables as $table) {
				$f = 'Tables_in_' . $config['schema'];
				if ($table->$f != 'migrations') {
					try {
						$builder->drop($table->$f);
					}
					catch (Exception $e) {

					}
				}
			}

			$builder->enableForeignKeyConstraints();
		}

		$this->schemaName = $currentSchema;
		$this->tableName = $currentTable;
	}
}
