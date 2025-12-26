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
 * @file   MigrationBase.php
 * @date   25/08/2020 21.48
 */

namespace App\Helpers;

use App\Exceptions\MethodNotFoundException;
use App\Exceptions\SchemaNotFoundException;
use App\Exceptions\TableNotFoundException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Database\Schema\ForeignKeyDefinition;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;
use ReflectionClass;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;


abstract class MigrationBase extends Migration {
	/**
	 * @var string
	 */
	protected string $tableName;

	/**
	 * @var Blueprint
	 */
	protected Blueprint $table;

	/**
	 * @var string
	 */
	protected string $schemaName;

	/**
	 * @var \Symfony\Component\Console\Style\SymfonyStyle
	 */
	protected SymfonyStyle $out;

	/**
	 * @var \Illuminate\Database\Schema\Builder
	 */
	protected Builder $schema;

    /**
     * @var array
     */
	private array $tables = [];

    /**
     * MigrationBase constructor.
     *
     * @param string $schema
     * @param        $table
     */
	public function __construct(string $schema, $table) {
		extract(config('settings.tables.' . $schema));
		print_r(config('settings.tables.' . $schema));

		$this->out = new SymfonyStyle(new ArgvInput(), new ConsoleOutput());
		$this->schemaName = $schema;
		if (!is_array($table)) $this->setTable($table);
		else $this->tables = $table;
		$this->setConnection($this->getConnection());
		$this->schema = Schema::connection($this->getConnection());
		$this->prepare();
	}

    /**
     * @param string $name
     */
	protected function setTable(string $name) {
		$this->tableName = $name;
	}

    /**
     * @param string $connection
     */
	protected function setConnection(?string $connection) {
		$this->connection = $connection;
	}

    /**
     *
     */
	protected function prepare() {
	}

	/**
	 * Run the migrations.
	 *
	 * @return void
	 * @throws \ReflectionException
	 * @throws \App\Exceptions\MethodNotFoundException
	 */
	public function up() {
		$self = $this;
		$schema = $this->schema;
		$class = new ReflectionClass(get_called_class());

		if (count($this->tables)) {
			$exists = false;

			foreach ($this->tables as $table) {
				$method = 'create_' . $table;

				if ($class->hasMethod($method)) {
					$this->setTable($table);
					$method = $class->getMethod($method);

					$this->schema->create(
						$table,
						function (Blueprint $table) use ($method, $self, $schema) {
							$schema->disableForeignKeyConstraints();
							$self->table = $table;
							$method->getClosure($self)->call($self, $table, $schema);
							$schema->enableForeignKeyConstraints();
						}
					);

					$exists = true;
				}
				else {
					$exists = false;
					break;
				}
			}

			if (!$exists) {
				throw new MethodNotFoundException($table);
			}
		}
		else {
			if ($class->getMethod('create')->getDeclaringClass()->getShortName() == get_called_class()) {
				$schema->create(
					$this->tableName,
					function (Blueprint $table) use ($self, $schema) {
						$schema->disableForeignKeyConstraints();
						$self->table = $table;
						call_user_func([$self, 'create'], $table, $schema);
						$schema->enableForeignKeyConstraints();
					}
				);

				return;
			}

			if ($class->getMethod('creates')->getDeclaringClass()->getShortName() == get_called_class()) {
				$this->creates($schema);

				return;
			}

			throw new MethodNotFoundException('create', 'creates');
		}
	}

    /**
     * @param \Illuminate\Database\Schema\Builder $schema
     */
	protected function creates(Builder $schema) {
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		$this->schema->disableForeignKeyConstraints();

		if (count($this->tables)) {
			foreach ($this->tables as $table) {
				$this->schema->dropIfExists($table);
			}
		}
		else {
			$this->schema->dropIfExists($this->tableName);
		}

		$this->schema->enableForeignKeyConstraints();
	}

    /**
     * @param \Illuminate\Database\Schema\Blueprint $table
     * @param \Illuminate\Database\Schema\Builder   $schema
     */
	protected function create(Blueprint $table, Builder $schema) {
	}

    /**
     * @param mixed ...$columns
     *
     * @return \Illuminate\Support\Fluent
     */
	protected function createUnique(...$columns): Fluent {
		return $this->table->unique($columns, $this->uniq(...$columns));
	}

    /**
     * @param mixed ...$column
     *
     * @return string
     */
	protected function uniq(...$column): string {
		return $this->generateIndexKeyname($column, 'UNIQ');
	}

    /**
     * @param        $columns
     * @param string $prefix
     * @param int    $maxSize
     *
     * @return string
     */
	private function generateIndexKeyname($columns, string $prefix = '', $maxSize = 30): string {
		$table = $this->tableName;
		$schema = $this->schemaName;

		$columns = array_map(
			static function ($column) use ($table, $schema) {
				return ["$schema.$table", $column];
			},
			is_array($columns) ? $columns : (array)$columns
		);
		$hash = implode(
			'',
			array_map(
				static function ($column) {
					return dechex(crc32($column[0])) . dechex(crc32($column[1]));
				},
				$columns
			)
		);

		return strtoupper(substr($prefix . '_' . $hash, 0, $maxSize));
	}

	/**
	 * @param mixed ...$columns
	 *
	 * @return \Illuminate\Support\Fluent
	 */
	protected function createIndex(...$columns): Fluent {
		return $this->table->index($columns, $this->idx(...$columns));
	}

	/**
	 * @param mixed ...$column
	 *
	 * @return string
	 */
	protected function idx(...$column): string {
		return $this->generateIndexKeyname($column, 'IDX');
	}

	/**
	 * @param mixed ...$columns
	 *
	 * @return \Illuminate\Support\Fluent
	 */
	protected function createPrimary(...$columns): Fluent {
		return $this->table->primary($columns, $this->pk(...$columns));
	}

	/**
	 * @param mixed ...$column
	 *
	 * @return string
	 */
	protected function pk(...$column): string {
		return $this->generateIndexKeyname($column, 'PK');
	}

	/**
	 * @param string $column
	 * @param string $reference
	 * @param string $on
	 * @param bool   $nullable
	 *
	 * @return \Illuminate\Database\Schema\ForeignKeyDefinition
	 * @throws \App\Exceptions\SchemaNotFoundException
	 * @throws \App\Exceptions\TableNotFoundException
	 */
	protected function unsignedBigIntegerForeign(string $column, string $reference, string $on,
		bool $nullable = false): ForeignKeyDefinition {
		$column = $this->table->unsignedBigInteger($column); // ->index($this->idx($column));
		$column->nullable($nullable);

		return $this->foreign($column, $reference, $this->getSchemaTable($on));
	}

	/**
	 * @param \Illuminate\Database\Schema\ColumnDefinition $column
	 * @param string                                       $reference
	 * @param string                                       $on
	 *
	 * @return \Illuminate\Database\Schema\ForeignKeyDefinition
	 */
	protected function foreign(ColumnDefinition $column, string $reference, string $on): ForeignKeyDefinition {
		return $this->table->foreign($column->get('name'), $this->fk($column->get('name')))
		                   ->references($reference)
		                   ->on($on);
	}

	/**
	 * @param mixed ...$column
	 *
	 * @return string
	 */
	protected function fk(...$column): string {
		return $this->generateIndexKeyname($column, 'FK');
	}

	/**
	 * @param string $on
	 *
	 * @return string
	 * @throws \App\Exceptions\SchemaNotFoundException
	 * @throws \App\Exceptions\TableNotFoundException
	 */
	private function getSchemaTable(string $on): string {
		if ($this->hasTable($on)) {
			$part = explode('.', $on);

			if (count($part) == 2) {
				$schema = config('settings.tables.' . $part[0] . '.schema');
				$on = $part[1];
			}
			else {
				$schema = $this->schemaName;
			}

			return "$schema.$on";
		}

		throw new TableNotFoundException($on);
	}

	/**
	 * @param string $table
	 *
	 * @return bool
	 * @throws \App\Exceptions\SchemaNotFoundException
	 */
	protected function hasTable(string $table): bool {
		$on = $table;
		$settings = config('settings.tables');
		$schema = $this->schemaName;
		$connection = $this->getConnection();

		if (strpos($table, '.') !== false) {
			list($schema, $table) = explode('.', $table);

			$connection = $settings[$schema]['connection'];
			$schema = $settings[$schema]['schema'];
		}

		if ("{$this->schemaName}.{$this->tableName}" == "$schema.$table") {
			return true;
		}

		return Schema::connection($connection)->hasTable($table);
	}

	/**
	 * @param string $column
	 * @param string $reference
	 * @param string $on
	 * @param bool   $nullable
	 *
	 * @return \Illuminate\Database\Schema\ForeignKeyDefinition
	 * @throws \App\Exceptions\SchemaNotFoundException
	 * @throws \App\Exceptions\TableNotFoundException
	 */
	protected function unsignedIntegerForeign(string $column, string $reference, string $on,
		bool $nullable = false): ForeignKeyDefinition {
		$column = $this->table->unsignedInteger($column)->index($this->idx($column));
		$column->nullable($nullable);

		return $this->foreign($column, $reference, $this->getSchemaTable($on));
	}

	/**
	 * @param string $column
	 * @param int    $length
	 * @param string $reference
	 * @param string $on
	 * @param bool   $nullable
	 *
	 * @return \Illuminate\Database\Schema\ForeignKeyDefinition
	 * @throws \App\Exceptions\SchemaNotFoundException
	 * @throws \App\Exceptions\TableNotFoundException
	 */
	protected function stringForeign(string $column, int $length, string $reference, string $on,
		bool $nullable = false): ForeignKeyDefinition {
		$column = $this->table->string($column, $length)->index($this->idx($column));
		$column->nullable($nullable);

		return $this->foreign($column, $reference, $this->getSchemaTable($on));
	}
}
