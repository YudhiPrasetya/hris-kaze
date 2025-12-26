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
 * @file   Builder.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Libraries\Terminal;

use BadMethodCallException;
use Exception;
use Illuminate\Console\Command;
use InvalidArgumentException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;


class Builder {
	/**
	 * Builder extensions.
	 *
	 * @var array
	 */
	protected static array $extensions = [];

	/**
	 * Command to execute.
	 *
	 * @var string|array $command
	 */
	protected $command = [];

	/**
	 * Timeout.
	 *
	 * @var \DateTimeInterface|\DateInterval|int|null $ttl
	 */
	protected $timeout = 60;

	/**
	 * Current working directory.
	 *
	 * @var string $cwd
	 */
	protected ?string $cwd;

	/**
	 * Environment variables.
	 *
	 * @var array $environmentVariables
	 */
	protected array $environmentVariables;

	/**
	 * The callback that is run whenever there is some output available.
	 *
	 * @var callable $output
	 */
	protected $output;

	/**
	 * The input as stream resource, scalar or \Traversable, or null for no input.
	 *
	 * @var mixed|null $input
	 */
	protected $input;

	/**
	 * Determine if a process should execute in the background.
	 *
	 * @var boolean
	 */
	protected bool $inBackground = false;

	/**
	 * Retry configuration for the command.
	 *
	 * @var array|null
	 */
	protected ?array $retries = [1, 0];

	/**
	 * Command data bindings.
	 *
	 * @var array
	 */
	protected array $with = [];

	/**
	 * TTY mode.
	 *
	 * @var boolean|null
	 */
	protected ?bool $tty;

	/**
	 * Max time since last output.
	 *
	 * @var mixed
	 */
	protected $idleTimeout;

	public function __construct() {
		$this->cwd = base_path();
		$this->environmentVariables = [];
		$this->tty = null;
	}

	/**
	 * Extend the builder with a custom method.
	 *
	 * @param string $method
	 * @param callable|null $callback
	 *
	 * @return void
	 */
	public static function extend(string $method, callable $callback = null): void {
		static::$extensions[$method] = $callback;
	}

	/**
	 * Get the executable command.
	 *
	 * @return string|array $command
	 */
	public function getCommand(): array|string {
		return $this->command;
	}

	/**
	 * Set Process timeout.
	 *
	 * @param \DateInterval|\DateTimeInterface|int|null $ttl
	 *
	 * @return $this
	 */
	public function timeout(\DateInterval|\DateTimeInterface|int|null $ttl): static {
		$this->timeout = $ttl;

		return $this;
	}

	/**
	 * Set current working directory.
	 *
	 * @param string $cwd
	 *
	 * @return $this
	 */
	public function in(string $cwd) {
		$this->cwd = $cwd;

		return $this;
	}

	/**
	 * Set process environment variables.
	 *
	 * @param array $environmentVariables
	 *
	 * @return $this
	 */
	public function withEnvironmentVariables(array $environmentVariables): static {
		$this->environmentVariables = $environmentVariables;

		return $this;
	}

	/**
	 * Set input.
	 *
	 * @param mixed $input
	 *
	 * @return $this
	 */
	public function input(mixed $input): static {
		$this->input = $input;

		return $this;
	}

	/**
	 * Retry an operation a given number of times.
	 *
	 * @param int $times
	 * @param int $sleep
	 *
	 * @return $this
	 */
	public function retries(int $times, int $sleep = 0): static {
		$this->retries = [$times, $sleep];

		return $this;
	}

	/**
	 * Bind command data.
	 *
	 * @param mixed $key
	 * @param mixed|null $value
	 *
	 * @return $this
	 */
	public function with(mixed $key, mixed $value = null): static {
		$this->with = array_merge($this->with, is_array($key) ? $key : [$key => $value]);

		return $this;
	}

	/**
	 * Enable TTY mode.
	 *
	 * @return $this
	 */
	public function enableTty(): static {
		return $this->tty(true);
	}

	/**
	 * Enable or disable the TTY mode.
	 *
	 * @param bool $tty
	 *
	 * @return $this
	 */
	public function tty(bool $tty): static {
		$this->tty = $tty;

		return $this;
	}

	/**
	 * Disable TTY mode.
	 *
	 * @return $this
	 */
	public function disableTty(): static {
		return $this->tty(false);
	}

	/**
	 * Set max time since last output.
	 *
	 * @param mixed $timeout
	 *
	 * @return $this
	 */
	public function idleTimeout(mixed $timeout): static {
		$this->idleTimeout = $timeout;

		return $this;
	}

	/**
	 * Execute a given command.
	 *
	 * @param mixed|null $command
	 * @param callable|null $output
	 *
	 * @return Response
	 */
	public function run(mixed $command = null, callable $output = null): Response {
		return $this->execute($command, $output);
	}

	/**
	 * Execute a given command.
	 *
	 * @param mixed|null $command
	 * @param callable|null $output
	 *
	 * @return Response
	 */
	public function execute(mixed $command = null, callable $output = null): Response {
		if (is_callable($command)) {
			[$command, $output] = [null, $command];
		}

		if (!is_null($command)) {
			$this->command($command);
		}

		if (!is_null($output)) {
			$this->output($output);
		}

		[$times, $sleep] = $this->retries;

		if ($times <= 1) {
			return $this->runProcess($this->process());
		}

		return $this->retry($times,
			$sleep,
			function ($attempts) {
				return $this->runProcess($this->process())->throw();
			});
	}

	/**
	 * Command to execute.
	 *
	 * @param array|string $command
	 *
	 * @return Builder
	 */
	public function command(array|string $command): static {
		$this->command = $command;

		return $this;
	}

	/**
	 * Set output handler.
	 *
	 * @param mixed $output
	 *
	 * @return $this
	 */
	public function output(mixed $output): static {
		$this->output = $this->parseOutput($output);

		return $this;
	}

	/**
	 * Parse a given output.
	 *
	 * @param mixed $output
	 *
	 * @return callable
	 */
	protected function parseOutput(mixed $output): callable {
		if (is_callable($output)) {
			return $output;
		}

		if ($output instanceof OutputInterface) {
			return $this->wrapOutput([$output, 'write']);
		}

		if ($output instanceof Command) {
			return $this->wrapOutput([$output->getOutput(), 'write']);
		}

		throw new InvalidArgumentException(sprintf(
			'Terminal output must be a %s, an instance of "%s" or an instance of "%s" but "%s" was given.',
			'callable',
			'Symfony\Component\Console\Output\OutputInterface',
			'Illuminate\Console\Command',
			($type = gettype($output)) === 'object' ? get_class($output) : $type
		));
	}

	/**
	 * Wrap output callback.
	 *
	 * @param callable $callback
	 *
	 * @return callable
	 */
	protected function wrapOutput(callable $callback): callable {
		return function ($data) use ($callback) {
			return call_user_func($callback, $data);
		};
	}

	/**
	 * Run a given process.
	 *
	 * @param Process $process
	 *
	 * @return Response
	 */
	public function runProcess(Process $process) {
		$process->{$this->inBackground ? 'start' : 'run'}($this->output);

		return new Response($process);
	}

	/**
	 * Make a new process instance.
	 *
	 * @return \Symfony\Component\Process\Process
	 */
	public function process() {
		$parameters = [
			$command = $this->prepareCommand($this->command),
			$this->cwd,
			$this->environmentVariables,
			$this->input,
			$this->getSeconds($this->timeout),
		];

		$process = is_string($command)
			? Process::fromShellCommandline(...$parameters)
			: new Process(...$parameters);

		if (!is_null($this->tty)) {
			$process->setTty($this->tty);
		}

		if (!is_null($this->idleTimeout)) {
			$process->setIdleTimeout($this->getSeconds($this->idleTimeout));
		}

		return $process;
	}

	/**
	 * Prepare a given command.
	 *
	 * @param mixed $command
	 *
	 * @return string
	 */
	protected function prepareCommand($command) {
		if (!is_string($command)) {
			return $command;
		}

		return preg_replace_callback('/\{\{\s?\$(\w+)\s?\}\}/u',
			function ($matches) use ($command) {
				$this->environmentVariables[$key = 'terminal_' . $matches[1]] = $this->with[$matches[1]] ?? '';

				return sprintf('"${:%s}"', $key);
			},
			$command);
	}

	/**
	 * Get timeout seconds.
	 *
	 * @param int|\DateTime $timeout
	 *
	 * @return int
	 */
	protected function getSeconds($timeout) {
		if ($timeout instanceof \DateInterval) {
			$timeout = (new \DateTime())->add($timeout);
		}

		if ($timeout instanceof \DateTime) {
			return $timeout->getTimestamp() - (new \DateTime())->getTimestamp();
		}

		return $timeout;
	}

	/**
	 * Retry an operation a given number of times.
	 *
	 * @param int $times
	 * @param int $sleep
	 * @param callable $callback
	 *
	 * @return mixed
	 *
	 * @throws \Exception
	 */
	public function retry(int $times, int $sleep = 0, callable $callback = null) {
		$attempts = 0;

		beginning:
		$attempts++;
		$times--;

		try {
			return $callback($attempts);
		}
		catch (Exception $e) {
			if ($times < 1) {
				throw $e;
			}

			if ($sleep) {
				usleep($sleep * 1000);
			}

			goto beginning;
		}
	}

	/**
	 * Execute a given comman in the background.
	 *
	 * @param mixed $command
	 * @param callable|null $callback
	 *
	 * @return Response
	 */
	public function executeInBackground($command = null, callable $callback = null) {
		return $this->inBackground()
		            ->execute($command, $callback);
	}

	/**
	 * Execute a process in the background.
	 *
	 * @return $this
	 */
	public function inBackground() {
		$this->inBackground = true;

		return $this;
	}

	/**
	 * Get the current command as string.
	 *
	 * @param string|array|null $command
	 *
	 * @return string
	 */
	public function toString($command = null) {
		if (!is_null($command)) {
			$this->command($command);
		}

		if (is_string($this->command)) {
			return $this->command;
		}

		return $this->process()->getCommandLine();
	}

	/**
	 * Dynamically forward calls to the process instance.
	 *
	 * @param string $method
	 * @param array $parameters
	 *
	 * @return mixed
	 */
	public function __call(string $method, array $parameters) {
		if (isset(static::$extensions[$method])) {
			return static::$extensions[$method]($this);
		}

		if (method_exists($process = $this->process(), $method)) {
			return $process->{$method}(...$parameters);
		}

		throw new BadMethodCallException(sprintf(
			'Call to undefined method %s::%s()',
			static::class,
			$method
		));
	}
}