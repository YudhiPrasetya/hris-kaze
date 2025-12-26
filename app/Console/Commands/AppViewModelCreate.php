<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;


class AppViewModelCreate extends GeneratorCommand {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:view-model:create {name}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new ViewModel class';

	protected $type = 'ViewModel';

	/**
	 * Execute the console command.
	 *
	 * @return bool|int|void|null
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function handle() {
		if (parent::handle() === false) {
			if (!$this->option('force')) {
				return;
			}
		}

		return 0;
	}

	protected function getNameInput() {
		$name = trim($this->argument('name'));
		$name .= strpos($name, $this->type) === false ? $this->type : null;

		return $name;
	}

	protected function getStub() {
		return __DIR__ . '/stubs/view-model/DummyViewModel.stub';
	}

	protected function getDefaultNamespace($rootNamespace) {
		if ($this->isCustomNamespace()) {
			return $rootNamespace;
		}

		return $rootNamespace . '\Http\ViewModels';
	}

	protected function isCustomNamespace(): bool {
		return Str::contains($this->argument('name'), '/');
	}

	protected function getOptions(): array {
		return [
			['force', null, InputOption::VALUE_NONE, 'Create the class even if the view-model already exists'],
		];
	}
}
