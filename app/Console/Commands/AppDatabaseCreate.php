<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;


class AppDatabaseCreate extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:db:create';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create database from available connections.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle() {
		$connections = array_keys(config("database.connections"));
		$connection = $this->askWithCompletion(
			'Which database would you like to create?',
			$connections,
			'mysql'
		);

		$schema = config("database.connections.$connection.database");
		$driver = config("database.connections.$connection.driver");

		if ($this->confirm("Do you wish to continue create '$schema' schema?")) {
			$charset = config("database.connections.$connection.charset", 'utf8mb4');
			$collation = config("database.connections.$connection.collation", 'utf8mb4_unicode_ci');

			config(["database.connections.$connection.database" => null]);

			$db = DB::connection($connection);

			try {
				if ($db->unprepared("USE $schema;")) {
					if ($this->confirm(
						"Schema '$schema' exists, would you like to drop it first (this will delete all of your data) ?"
					)) {
						if ($db->unprepared("DROP DATABASE $schema;")) {
							$this->createSchema($db, $schema, $charset, $collation);
						}
					}
				}
			}
			catch (QueryException $e) {
				if ($e->errorInfo[1] == 1049 && $driver == 'mysql') {
					// 1049: Unknown database
					// Which means the database is not exists, so let's create it
					$this->createSchema($db, $schema, $charset, $collation);
				}
				else {
					$this->error($e->getMessage());
				}
			}

			config(["database.connections.$connection.database" => $schema]);
		}

		return 0;
	}

	private function createSchema(ConnectionInterface $connection, string $name, string $charset, $collation) {
		if ($connection->unprepared("CREATE DATABASE IF NOT EXISTS $name CHARACTER SET $charset COLLATE $collation;")) {
			$this->info("Schema $name creation success.");
		}
		else {
			$this->error("Schema $name creation failed!");
		}
	}
}
