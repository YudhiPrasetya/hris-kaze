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
 * @file   Terminal.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Libraries\Terminal;

use App\Libraries\Terminal\Contracts\Factory;
use App\Libraries\Terminal\Fakes\BuilderFake;
use App\Libraries\Terminal\Fakes\ResponseFake;
use Symfony\Component\Process\Process;


class Terminal implements Factory {
	/**
	 * Indicate whether the terminal are captured.
	 *
	 * @var boolean
	 */
	protected static $fake = false;

	/**
	 * Use a fake Terminal.
	 *
	 * @param array $commands
	 *
	 * @return void
	 */
	public static function fake(array $commands = []) {
		static::$fake = true;

		BuilderFake::setCommands($commands);
	}

	/**
	 * Reset the fake Terminal.
	 *
	 * @return void
	 */
	public static function reset() {
		static::$fake = false;

		BuilderFake::setCommands([]);
		BuilderFake::setCaptured([]);
	}

	/**
	 * Create a fake response.
	 *
	 * @param mixed $lines
	 *
	 * @param null  $process
	 *
	 * @return ResponseFake
	 */
	public static function response($lines = null, $process = null) {
		if ($lines instanceof Process) {
			[$lines, $process] = [$process, $lines];
		}

		if (is_null($process)) {
			$process = new Process([]);
		}

		return new ResponseFake($process, static::lines($lines));
	}

	/**
	 * Parse given lines.
	 *
	 * @param mixed  $lines
	 * @param string $type
	 *
	 * @return OutputLine[]
	 */
	public static function lines($lines, string $type = Process::OUT) {
		return array_map(function ($line) use ($type) {
			return static::line($line, $type);
		},
			is_array($lines) ? $lines : [$lines]);
	}

	/**
	 * Create new output line(s)
	 *
	 * @param mixed  $content
	 * @param string $type
	 *
	 * @return OutputLine|OutputLine[]
	 */
	public static function line($content, string $type = Process::OUT) {
		if ($content instanceof OutputLine) {
			return $content;
		}

		if (is_array($content)) {
			return static::lines($content);
		}

		return new OutputLine(
			(string)$type,
			(string)$content
		);
	}

	/**
	 * Create a new error line.
	 *
	 * @param string $content
	 *
	 * @return OutputLine
	 */
	public static function error(string $content) {
		return static::line($content, Process::ERR);
	}

	/**
	 * Dynamically pass method calls to a new Builder instance.
	 *
	 * @param string $method
	 * @param array  $parameters
	 *
	 * @return Builder|BuilderFake
	 */
	public static function __callStatic(string $method, array $parameters) {
		return call_user_func([static::builder(), $method], ...$parameters);
	}

	/**
	 * Get an instance of the Process builder class.
	 *
	 * @return Builder|BuilderFake
	 */
	public static function builder() {
		$class = static::$fake ? BuilderFake::class : Builder::class;

		return new $class;
	}
}