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
 * @file   AppScoutMySQLIndexes.php
 * @date   2020-09-28 14:32:42
 */

namespace App\Console\Commands;

use App\Events\ModelIndexCreated;
use App\Events\ModelIndexDropped;
use App\Events\ModelIndexIgnored;
use App\Events\ModelIndexUpdated;
use App\Services\IndexService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;


class AppScoutMySQLIndexes extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:scout:mysql-index {model?} {--D|drop}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create MySQL FULLTEXT indexes for searchable models';

	protected $indexService;

	/**
	 * Create a new command instance.
	 *
	 * @param IndexService $indexService
	 */
	public function __construct(IndexService $indexService) {
		parent::__construct();
		$this->indexService = $indexService;
	}

	/**
	 * Execute the console command.
	 *
	 * @param Dispatcher $events
	 *
	 * @return mixed
	 */
	public function handle(Dispatcher $events) {
		$events->listen(ModelIndexCreated::class,
			function ($event) {
				$this->comment("Index '$event->indexName' created with fields: $event->indexFields");
			});

		$events->listen(ModelIndexUpdated::class,
			function ($event) {
				$this->comment("Index '$event->indexName' updated");
			});

		$events->listen(ModelIndexDropped::class,
			function ($event) {
				$this->comment("Index '$event->indexName' dropped");
			});

		$events->listen(ModelIndexIgnored::class,
			function ($event) {
				$this->comment("Existing Index '$event->indexName' ignored");
			});

		$model = $this->argument('model');
		$drop = $this->option('drop');

		if (!$model) {
			$modelDirectories = config('scout-driver.mysql.model_paths');
			$searchableModels = $this->indexService->getAllSearchableModels($modelDirectories);

			foreach ($searchableModels as $searchableModel) {
				$drop ? $this->dropModelIndex($searchableModel) : $this->createOrUpdateModelIndex($searchableModel);
			}
		}
		else {
			$drop ? $this->dropModelIndex($model) : $this->createOrUpdateModelIndex($model);
		}
	}

	private function createOrUpdateModelIndex($searchableModel) {
		$this->info("Creating index for $searchableModel...");
		$this->indexService->setModel($searchableModel);
		$this->indexService->createOrUpdateIndex();
	}

	private function dropModelIndex($searchableModel) {
		$this->info("Dropping index for $searchableModel...");
		$this->indexService->setModel($searchableModel);
		$this->indexService->dropIndex();
	}
}