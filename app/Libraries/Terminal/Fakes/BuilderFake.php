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
 * @file   BuilderFake.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Libraries\Terminal\Fakes;

use App\Libraries\Terminal\Builder;
use App\Libraries\Terminal\Terminal;
use PHPUnit\Framework\Assert;
use Symfony\Component\Process\Process;


class BuilderFake extends Builder {
	/**
	 * Captured executed terminal commands.
	 *
	 * @var array
	 */
	protected static array $captured = [];

	/**
	 * Fake commands.
	 *
	 * @var array
	 */
	protected static array $commands = [];

	/**
	 * Capture a given command.
	 *
	 * @param Builder $builder
	 *
	 * @return void
	 */
	public static function capture(Builder $builder) {
		static::$captured[] = $builder;
	}

	/**
	 * Set captured commands.
	 *
	 * @param array $captured
	 *
	 * @return void
	 */
	public static function setCaptured(array $captured) {
		static::$captured = $captured;
	}

	/**
	 * Set fake commands.
	 *
	 * @param array $commands
	 *
	 * @return void
	 */
	public static function setCommands(array $commands) {
		static::$commands = [];

		foreach ($commands as $command => $response) {
			if (is_numeric($command)) {
				[$command, $response] = [$response, null];
			}

			static::$commands[$command] = static::parseResponse($command, $response);
		}
	}

	/**
	 * Parse response for a given command.
	 *
	 * @param string $command
	 * @param        $response
	 *
	 * @return ResponseFake
	 */
	public static function parseResponse(string $command, $response) {
		if ($response instanceof ResponseFake) {
			return $response;
		}

		return Terminal::response($response, Process::fromShellCommandline($command));
	}

	/**
	 * Determine if the terminal should run a given command.
	 *
	 * @param string $command
	 *
	 * @return bool
	 */
	public static function shouldRun(string $command) {
		return !isset(static::$commands[$command]);
	}

	/**
	 * Assert if a given command was executed.
	 *
	 * @param mixed $command
	 *
	 * @return void
	 */
	public static function assertExecuted($command, int $times = 1) {
		$filter = is_callable($command) ? $command : function ($captured) use ($command) {
			return $captured->toString() == Terminal::toString($command);
		};

		$count = count(array_filter(static::$captured, $filter));

		Assert::assertTrue($count === $times,
			sprintf(
				'The command was executed %s times instead of expected %s times.',
				$count,
				$times
			));
	}

	/**
	 * Run a given process.
	 *
	 * @param \Symfony\Component\Process\Process $process
	 *
	 * @return \App\Libraries\Terminal\Contracts\Response
	 */
	public function runProcess(Process $process) {
		Terminal::capture($this);

		return static::$commands[$this->toString()] ?? Terminal::response();
	}
}