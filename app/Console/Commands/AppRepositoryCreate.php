<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use ReflectionClass;
use ReflectionException;


class AppRepositoryCreate extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:repository:create {?--force} {?--all}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create repository for model';

	/**
	 * @var Filesystem
	 */
	protected Filesystem $filesystem;

	/**
	 * AppRepositoryCreate constructor.
	 *
	 * @param \Illuminate\Filesystem\Filesystem $filesystem
	 */
	public function __construct(Filesystem $filesystem) {
		parent::__construct();

		$this->filesystem = $filesystem;
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle() {
		$models = $this->getModels(base_path('app/Models'));
		$namespace = null;
		$class = null;
		$repositoryClass = null;
		$repositoryInterface = null;
		$exists = true;

		if ($this->option('all')) {
			foreach (array_keys($models) as $class) {
				if (!interface_exists($class))
					$this->createRepository($models, $class);
			}
		}
		else {
			while ($exists) {
				$class = $this->askWithCompletion(
					'Select the model class for the repository class that will be created ?',
					array_keys($models)
				);

				if (empty($class)) {
					$this->error("Please choose the model to create the repository class.");
					continue;
				}

				$exists = false;
			}

			$this->createRepository($models, $class);
		}

		return 0;
	}

	/**
	 * @param string $path
	 * @param string $namespace
	 *
	 * @return array
	 */
	private function getModels(string $path, string $namespace = "App\\Models"): array {
		$models = [];

		foreach (scandir($path) as $file) {
			if (in_array($file, ['.', '..'])) continue;
			$pathname = "$path/$file";

			if (is_dir($pathname)) {
				$models = array_merge(
					$models,
					$this->getModels(
						$pathname,
						sprintf("%s\\%s", $namespace, $file)
					)
				);
			}
			else {
				$class = $this->filesystem->name($pathname);

				if ("$namespace\\$class" != "App\Models\ModelBase") {
					new ReflectionClass("$namespace\\$class");
					$models["$namespace\\$class"] = str_replace("App\\Models\\", '', $namespace);
				}
			}
		}

		return $models;
	}

	private function createRepository($models, $class) {
		$namespace = $models[$class];
		$class = array_slice(explode("\\", $class), -1)[0];
		$repositoryClass = "$namespace/$class" . "Repository";
		$repositoryInterface = $repositoryClass . "Interface";

		$exists = $this->isClassExists($repositoryInterface, $repositoryClass);
		$repositoryClass = $class . "Repository";
		$repositoryInterface = $repositoryClass . "Interface";

		if ($exists) {
			$this->warn(
				sprintf(
					"[?] File Exists: One of this class already created <fg=cyan>%s</> or <fg=cyan>%s</>.",
					$repositoryInterface,
					$repositoryClass
				)
			);
		}

		try {
			//$reflection = new ReflectionClass("App\\Models\\$namespace\\$class");
			if ($namespace == "App\\Models") $namespace = null;
			$class = $this->fixClassname("App\\Models\\$namespace\\$class");
			$reflection = new ReflectionClass($class);
			$connection = $reflection->newInstance()->getConnectionName();
			$paths = [
				// Eloquent
				'app/Repositories'                     => [
					'stub'      => "EloquentRepositoryInterface",
					'classname' => "EloquentRepositoryInterface",
					'file'      => 'EloquentRepositoryInterface',
					'namespace' => "App\\Repositories",
				],

				// Base repository class
				'app/Repositories/Eloquent'            => [
					'stub'           => "RepositoryBase",
					'file'           => 'RepositoryBase',
					'classname'      => "RepositoryBase",
					'namespace'      => "App\\Repositories\\Eloquent",
					'eloquent_class' => "App\\Repositories\\EloquentRepositoryInterface",
				],

				// Provider
				'app/Providers'                        => [
					'stub'                  => "RepositoryServiceProvider",
					'file'                  => 'RepositoryServiceProvider',
					'namespace'             => "App\\Providers",
					'classname'             => 'RepositoryServiceProvider',
					'eloquent_class'        => "App\\Repositories\\EloquentRepositoryInterface",
					'repository_base_class' => "App\\Repositories\\Eloquent\\RepositoryBase",
				],

				// Repository interface
				"app/Repositories/$namespace"          => [
					'stub'           => "RepositoryInterface",
					'file'           => $reflection->getShortName() . "RepositoryInterface",
					'namespace'      => "App\\Repositories\\$namespace",
					'classname'      => $reflection->getShortName() . "RepositoryInterface",
					'eloquent_class' => "App\\Repositories\\EloquentRepositoryInterface",
				],

				// Repository class
				"app/Repositories/Eloquent/$namespace" => [
					'stub'                  => "Repository",
					'file'                  => $reflection->getShortName() . "Repository",
					'namespace'             => "App\\Repositories\\Eloquent\\$namespace",
					'classname'             => $reflection->getShortName() . "Repository",
					'connection'            => $connection,
					'repository_base_class' => "App\\Repositories\\Eloquent\\RepositoryBase",
					'interface_class'       => "App\\Repositories\\$namespace\\" . $reflection->getShortName() .
					                           "RepositoryInterface",
					'interface'             => $reflection->getShortName() . "RepositoryInterface",
					'model'                 => $reflection->getShortName(),
					'model_class'           => $reflection->getName(),
				],
			];

			$success = false;

			foreach ($paths as $path => $params) {
				$filename = base_path("$path/" . str_replace('\\', '/', $params['file']) . ".php");
				foreach ($params as &$param) {
					$param = $this->fixClassname($param);
				}

				if (!$this->exists($filename) || $this->option('force')) {
					if ($params['classname'] == 'RepositoryServiceProvider') continue;
					$success = $this->create(
						$params['stub'],
						$params,
						$filename,
						$this->option('force')
					);
				}
			}

			if ($success) {
				$this->register(
					"App\\Repositories\\$namespace\\" . $reflection->getShortName() . "RepositoryInterface",
					"App\\Repositories\\Eloquent\\$namespace\\" . $reflection->getShortName() . "Repository"
				);
			}
		}
		catch (ReflectionException $e) {
			$this->message(false, $this->formatMessage(false, 'ReflectionException', $e->getMessage()));
		}
		catch (FileNotFoundException $e) {
			$this->message(false, $this->formatMessage(false, 'FileNotFoundException', $e->getMessage()));
		}
	}

	private function fixClassname(?string $classname): string {
		$classname = str_replace("\\\\", "\\", $classname);
		return trim($classname, "\\");
	}

	/**
	 * @param string $repositoryInterfacename
	 * @param string $repositoryClassname
	 *
	 * @return bool
	 */
	private function isClassExists(string $repositoryInterfacename, string $repositoryClassname) {
		return $this->exists(base_path('app/Repositories/Eloquent/' . $repositoryClassname . '.php')) ||
		       $this->exists(base_path('app/Repositories/' . $repositoryInterfacename . '.php'));
	}

	/**
	 * @param string $path
	 *
	 * @return bool
	 */
	private function exists(string $path): bool {
		return $this->filesystem->exists($path);
	}

	/**
	 * @param string $stub
	 * @param array  $params
	 * @param string $path
	 * @param bool   $force
	 *
	 * @return bool
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	private function create(string $stub, array $params, string $path, bool $force = false) {
		$ret = $this->put($this->getContent($stub, $params), $path, $force);

		$this->message(
			$ret,
			$this->formatMessage(
				true,
				'Create',
				"Class <fg=cyan>" . $params['classname'] . '</>' .
				($ret ? ' successfully created' : ' failed to create')
			)
		);

		return $ret;
	}

	/**
	 * @param string $contents
	 * @param string $path
	 * @param bool   $force
	 *
	 * @return bool
	 */
	private function put(string $contents, string $path, bool $force = false) {
		$path = str_replace('\\', '/', $path);
		$parts = explode('/', str_replace(base_path() . '/', '', $path));

		foreach (array_slice($parts, 0, -1) as $key => $part) {
			$location = implode('/', array_slice($parts, 0, $key + 1));
			if (!$this->exists(base_path($location))) {
				$this->filesystem->makeDirectory(base_path($location));
			}
		}

		// $path = base_path(implode('/', $parts));

		if ($force && $this->exists($path)) {
			$ret = $this->filesystem->delete($path);
			$this->message(
				$ret,
				$this->formatMessage(
					false,
					'Delete',
					"Class <fg=cyan>" . $this->filesystem->name($path) . '</>' .
					($ret ? ' successfully deleted' : ' failed to delete')
				)
			);
		}

		if ($this->exists($path) && !$force && $this->filesystem->isFile($path)) {
			if ($this->confirm(
				"File " . $this->filesystem->basename($path) . " exists. Would you like forced to replace it ?"
			)) {
				$this->filesystem->delete($path);
			}
			else {
				return false;
			}
		}

		$this->filesystem->put($path, $contents);

		return true;
	}

	private function message(bool $success, $message) {
		if ($success) {
			$this->info($message);
		}
		else {
			$this->error($message);
		}
	}

	private function formatMessage(bool $ok, string $title, $message) {
		return sprintf(
			($ok ? '<fg=default>' . ($this->option('force') ? '[!]' : '[+]') . '</>' : '<fg=red>[-]</>') . ' %-11s: %s',
			$title,
			$message
		);
	}

	/**
	 * @param string $stub
	 * @param array  $params
	 *
	 * @return string
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	private function getContent(string $stub, array $params): string {
		$default = [
			'{{ datetime }}' => date('d/m/Y H:i'),
			'{{ year }}'     => date('Y')
		];

		$new = [];
		array_walk(
			$params,
			function ($value, $key) use (&$new) {
				$new["{{ $key }}"] = $value;
			}
		);

		$params = $new;

		$default = array_filter(
			array_merge($default, $params),
			function ($value) {
				return !is_null($value);
			}
		);

		$contents = $this->filesystem->get($this->resolveStubPath($stub));
		$contents = str_replace(array_keys($default), array_values($default), $contents);

		return $contents;
	}

	/**
	 * Resolve the fully-qualified path to the stub.
	 *
	 * @param string $stub
	 *
	 * @return string
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	private function resolveStubPath($stub) {
		$current = __DIR__ . '/stubs/repository/' . $stub . '.php.stub';

		if ($this->exists($customPath = base_path(trim($stub, '/')))) {
			return $customPath;
		}
		else {
			if ($this->exists($current)) return $current;
			throw new FileNotFoundException();
		}
	}

	/**
	 * @param $repositoryInterface
	 * @param $repositoryClass
	 *
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 * @throws \ReflectionException
	 */
	protected function register($repositoryInterface, $repositoryClass) {
		$repositoryInterface = $this->fixClassname($repositoryInterface);
		$repositoryClass = $this->fixClassname($repositoryClass);

		$class = new ReflectionClass($repositoryClass);
		$interface = new ReflectionClass($repositoryInterface);
		$classExists = $this->exists($interface->getFileName()) && $this->exists($class->getFileName());
		$re = '/[\W]public function register\(\) (?:\{)(?<class>[^\}]*)(?:\})/m';
		$reDatetime = '/@date   (?<datetime>[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4} [0-9]{1,2}[:\.][0-9]{1,2})/m';
		$provider = base_path('app/Providers/RepositoryServiceProvider.php');
		$content = $this->filesystem->get($provider);

		// Update datetime on file
		preg_match_all($reDatetime, $content, $matches);
		if (isset($matches['datetime'])) {
			$content = preg_replace($reDatetime, "@date   " . date('d/m/Y H.i'), $content);
		}

		preg_match_all($re, $content, $matches);

		$services = null;
		if (isset($matches['class'][0])) {
			$services = explode(';', preg_replace('/[\n\t]/', '', trim($matches['class'][0])));
			$services = array_map(
				function ($value) {
					return trim($value);
				},
				array_slice($services, 0, -1)
			);
		}

		if (is_null($services) || count($services) == 0) {
			// TODO: throw exception
		}

		if ($classExists) {
			$s = sprintf("\$this->app->bind(%s::class, %s::class)", $interface->getShortName(), $class->getShortName());
			if (!in_array($s, $services)) $services[] = $s;
		}
		else {
			// Remove the class and the interface because the both files doesn't exists
			$services = array_filter(
				$services,
				function ($value) use ($interface, $class) {
					$exists = strpos($value, $interface->getShortName()) !== false;
					$exists &= strpos($value, $class->getShortName()) !== false;

					return !$exists;
				}
			);
		}

		$services = $this->join('		', $services);

		$subst = <<<PHP
	public function register() {
{$services}
	}
PHP;

		$result = preg_replace($re, $subst, $content);
		$re = '/^use (?<class>[\w\\\\]+)+;/mi';
		preg_match_all($re, $result, $classes);
		$classes['class'] = array_map(
			function ($value) {
				return trim($value);
			},
			$classes['class']
		);

		$orig = $this->join("use ", $classes['class']);

		if ($classExists) {
			if (!in_array($repositoryInterface, $classes['class'])) $classes['class'][] = $interface->getName();
			if (!in_array($repositoryClass, $classes['class'])) $classes['class'][] = $class->getName();
		}
		else {
			$classes['class'] = array_filter(
				$classes['class'],
				function ($value) use ($interface, $class) {
					$exists = strpos($value, $interface->getName()) !== false;
					$exists &= strpos($value, $class->getName()) !== false;

					return !$exists;
				}
			);
		}

		sort($classes['class']);
		$classes = $this->join("use ", $classes['class']);
		$result = str_replace($orig, $classes, $result);
		$ret = $this->filesystem->put($provider, $result);
		$this->message(
			$ret,
			$this->formatMessage(
				true,
				'Update',
				'Class <fg=cyan>RepositoryServiceProvider</> ' . ($ret ? 'successfully updated' : 'failed to update!')
			)
		);
	}

	private function join(string $prefix, array $arr): string {
		return implode(
			"\n",
			array_map(
				function ($value) use ($prefix) {
					return "$prefix$value;";
				},
				array_map(
					function ($value) {
						return trim($value);
					},
					$arr
				)
			)
		);
	}
}
