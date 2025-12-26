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
 * @file   AppFormCreate.php
 * @date   27/08/2020 13.47
 */

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;


class AppFormCreate extends GeneratorCommand {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'app:form:create';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a form builder class';

	/**
	 * Type of the file generated.
	 *
	 * @var string
	 */
	protected $type = 'Form';

	/**
	 * @var AppFormGenerator
	 */
	private $formGenerator;

	public function __construct(Filesystem $files, AppFormGenerator $formGenerator) {
		parent::__construct($files);
		$this->formGenerator = $formGenerator;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments() {
		return array(
			array('name', InputArgument::REQUIRED, 'Full class name of the desired form class.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions() {
		return array(
			array('table', 't', InputOption::VALUE_OPTIONAL, 'Table for the form'),
			array('fields', 'f', InputOption::VALUE_OPTIONAL, 'Fields for the form'),
			array('namespace', null, InputOption::VALUE_OPTIONAL, 'Class namespace'),
			array('path', 'p', InputOption::VALUE_OPTIONAL, 'File path', 'app/Http/Forms')
		);
	}

	/**
	 * Replace the class name for the given stub.
	 *
	 * @param string $stub
	 * @param string $name
	 *
	 * @return string
	 * @throws \App\Exceptions\SchemaNotFoundException
	 * @throws \Doctrine\DBAL\Exception
	 */
	protected function replaceClass($stub, $name): string {
		$formGenerator = $this->formGenerator;

		$stub = str_replace(
			'{{class}}',
			$formGenerator->getClassInfo($name)->className,
			$stub
		);

		$stub = str_replace(
			'{{fields}}',
			$formGenerator->getFieldsVariable($this->option('fields'), $this->option('table')),
			$stub
		);

		return str_replace(
			'{{imports}}',
			$formGenerator->getImports()->join("\n"),
			$stub
		);
	}

	/**
	 * Replace the namespace for the given stub.
	 *
	 * @param string $stub
	 * @param string $name
	 *
	 * @return $this
	 */
	protected function replaceNamespace(&$stub, $name) {
		$path = $this->option('path');
		$namespace = $this->option('namespace');

		if (!$namespace) {
			$namespace = $this->formGenerator->getClassInfo($name)->namespace;

			if ($path) {
				$namespace = str_replace('/', '\\', trim($path, '/'));
				foreach ($this->getAutoload() as $autoloadNamespace => $autoloadPath) {
					if (preg_match('|' . $autoloadPath . '|', $path)) {
						$namespace = str_replace([$autoloadPath, '/'], [$autoloadNamespace, '\\'], trim($path, '/'));
					}
				}
			}
		}

		$stub = str_replace('{{namespace}}', $namespace, $stub);

		return $this;
	}

	/**
	 * Get psr-4 namespace.
	 *
	 * @return array
	 */
	protected function getAutoload() {
		$composerPath = base_path('/composer.json');

		if (!file_exists($composerPath)) return [];

		$composer = json_decode(
			file_get_contents(
				$composerPath
			),
			true
		);

		return Arr::get($composer, 'autoload.psr-4', []);
	}

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub() {
		return __DIR__ . '/stubs/form/form-class-template.stub';
	}

	/**
	 * @inheritdoc
	 */
	protected function getPath($name) {
		$optionsPath = $this->option('path');

		if ($optionsPath !== null) {
			return join(
				'/',
				[
					$this->laravel->basePath(),
					trim($optionsPath, '/'),
					$this->getNameInput() . '.php'
				]
			);
		}

		return parent::getPath($name);
	}

	/**
	 * Get the desired class name from the input.
	 *
	 * @return string
	 */
	protected function getNameInput() {
		return str_replace('/', '\\', $this->argument('name')).'Form';
	}
}