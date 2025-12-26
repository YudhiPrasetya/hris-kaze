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
 * @file   IndexService.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Services;

use App\Events\ModelIndexCreated;
use App\Events\ModelIndexDropped;
use App\Events\ModelIndexIgnored;
use App\Events\ModelIndexUpdated;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;


class IndexService {
	protected $modelService;

	public function __construct(ModelService $modelService) {
		$this->modelService = $modelService;
	}

	public function setModel($model) {
		$this->modelService->setModel($model);
	}

	public function getAllSearchableModels($directories) {
		$searchableModels = [];

		foreach ($directories as $directory) {
			$files = glob($directory . '/*.php');

			foreach ($files as $file) {
				if (basename($file) == 'ModelBase.php' || basename($file) == 'ModelInterface.php') continue;

				$class = getClassFullNameFromFile($file);

				if (!class_exists($class) || !in_array(Searchable::class, class_uses($class))) {
					$isSearchable = false;
					foreach (class_parents($class) as $parent) {
						if (in_array(Searchable::class, class_uses($parent))) {
							$isSearchable = true;
							break;
						}
					}

					if (!$isSearchable) {
						continue;
					}
				}

				$modelInstance = new $class();
				$connectionName = $modelInstance->getConnectionName() !== null ?
					$modelInstance->getConnectionName() : config('database.default');

				$isMySQL = config("database.connections.$connectionName.driver") === 'mysql';

				if ($isMySQL) {
					$searchableModels[] = $class;
				}
			}
		}

		return $searchableModels;
	}

	public function createOrUpdateIndex() {
		if ($this->indexAlreadyExists()) {
			if ($this->indexNeedsUpdate()) {
				$this->updateIndex();
			}
			else {
				event(new ModelIndexIgnored($this->modelService->indexName));
			}
		}
		else {
			$this->createIndex();
		}
	}

	protected function indexAlreadyExists() {
		$self = $this;
		$tableName = $this->modelService->tablePrefixedName;
		$indexName = $this->modelService->indexName;
		$fields = $this->modelService->getFullTextIndexFields();
		$fields = array_chunk($fields, 16);
		$fields = collect($fields)->map(function($items, $key) use($self, $tableName) {
			$indexName = $self->modelService->generateIndexName($items);
			return !empty(DB::connection($this->modelService->connectionName)
			         ->select("SHOW INDEX FROM $tableName WHERE Key_name = ?", [$indexName]));
		})->toArray();


		//return !empty(DB::connection($this->modelService->connectionName)
		//                ->select("SHOW INDEX FROM $tableName WHERE Key_name = ?", [$indexName]));
		return in_array(true, $fields);
	}

	protected function indexNeedsUpdate() {
		$currentIndexFields = $this->modelService->getFullTextIndexFields();
		$expectedIndexFields = $this->getIndexFields();

		return $currentIndexFields != $expectedIndexFields;
	}

	protected function getIndexFields() {
		$self = $this;
		$indexName = $this->modelService->indexName;
		$tableName = $this->modelService->tablePrefixedName;
		$fields = $this->modelService->getFullTextIndexFields();
		$index = collect([]);

		$fields = array_chunk($fields, 16);
		collect($fields)->each(function ($items, $key) use ($self, $tableName, &$index) {
			$indexName = $self->modelService->generateIndexName($items);
			$index->merge(DB::connection($self->modelService->connectionName)->select("SHOW INDEX FROM $tableName WHERE Key_name = ?", [$indexName]));
		});

		//$index = DB::connection($this->modelService->connectionName)
		//           ->select("SHOW INDEX FROM $tableName WHERE Key_name = ?", [$indexName]);

		$indexFields = [];

		foreach ($index as $idx) {
			$indexFields[] = $idx->Column_name;
		}

		return $indexFields;
	}

	protected function updateIndex() {
		$this->dropIndex();
		$this->createOrUpdateIndex();
		event(new ModelIndexUpdated($this->modelService->indexName));
	}

	public function dropIndex() {
		$self = $this;
		$indexName = $this->modelService->indexName;
		$tableName = $this->modelService->tablePrefixedName;
		$fields = $this->modelService->getFullTextIndexFields();

		if ($this->indexAlreadyExists()) {
			$fields = array_chunk($fields, 16);
			collect($fields)->each(function ($items, $key) use ($self, $tableName) {
				$indexName = $self->modelService->generateIndexName($items);
				DB::connection($this->modelService->connectionName)
				  ->statement("ALTER TABLE $tableName DROP INDEX $indexName");
				event(new ModelIndexDropped($this->modelService->indexName));
			});
		}
	}

	protected function createIndex() {
		$self = $this;
		$indexName = $this->modelService->indexName;
		$tableName = $this->modelService->tablePrefixedName;
		$fields = $this->modelService->getFullTextIndexFields();

		if (count($fields) == 0) {
			return;
		}

		$fields = array_chunk($fields, 16);
		collect($fields)->each(function ($items, $key) use ($self, $tableName) {
			$indexFields = implode(',', array_map(fn($indexField) => "`$indexField`", $items));
			$indexName = $self->modelService->generateIndexName($items);
			DB::connection($self->modelService->connectionName)->statement("CREATE FULLTEXT INDEX $indexName ON $tableName ($indexFields)");
			event(new ModelIndexCreated($indexName, $indexFields));
		});
	}

	public function getAppNamespace() {
		return Container::getInstance()->getNamespace();
	}
}