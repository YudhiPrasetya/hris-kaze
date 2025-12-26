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
 * @file   ModelService.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;


class ModelService {
	public $model;

	public $connectionName;

	public $tablePrefix;

	public $tableName;

	public $tablePrefixedName;

	public $indexName;

	public $searchableAs;

	protected array $fullTextIndexTypes = ['VARCHAR', 'TEXT', 'CHAR'];

	public function setModel($model) {
		$modelInstance = new $model();

		$this->model = $model;
		$this->connectionName = $modelInstance->getConnectionName() !== null ? $modelInstance->getConnectionName() :
			config('database.default');
		$this->tablePrefix = config("database.connections.$this->connectionName.prefix", '');
		$this->tableName = Str::of($modelInstance->getTable())->after('.');
		$this->tablePrefixedName = $this->tablePrefix . $this->tableName;
		$this->searchableAs = $modelInstance->searchableAs();
		$this->indexName = $this->generateIndexName(); //$modelInstance->searchableAs();

		return $this;
	}

	public function generateIndexName(array $columns = [], $maxSize = 32): string {
		$table = $this->searchableAs;
		$schema = config("database.connections.{$this->connectionName}.database", '');
		$columns = count($columns) ? $columns : $this->getFullTextIndexFields();

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

		return strtoupper(substr('FTX_' . $hash, 0, $maxSize));
	}

	public function getFullTextIndexFields() {
		$searchableFields = $this->getSearchableFields();
		$indexFields = [];

		foreach ($searchableFields as $searchableField) {

			//@TODO cache this.
			$sql = "SHOW FIELDS FROM $this->tablePrefixedName where Field = ?";
			$column = DB::connection($this->connectionName)->select($sql, [$searchableField]);

			if (!isset($column[0])) {
				continue;
			}

			$columnType = $column[0]->Type;

			// When using `$appends` to include an accessor for a field that doesn't exist,
			// an ErrorException will be thrown for `Undefined Offset: 0`
			if ($this->isFullTextSupportedColumnType($columnType)) {
				$indexFields[] = $searchableField;
			}
		}

		return $indexFields;
	}

	public function getSearchableFields() {
		$columns = $this->getAllFields();

		return array_keys((new $this->model())->forceFill($columns)->toSearchableArray());
	}

	protected function getAllFields() {
		$columns = [];

		//@TODO cache this
		foreach (DB::connection($this->connectionName)->getSchemaBuilder()->getColumnListing($this->tableName) as $column) {
			$columns[$column] = null;
		}

		return $columns;
	}

	protected function isFullTextSupportedColumnType($columnType) {
		foreach ($this->fullTextIndexTypes as $fullTextIndexType) {
			if (stripos($columnType, $fullTextIndexType) !== false) {
				return true;
			}
		}

		return false;
	}
}