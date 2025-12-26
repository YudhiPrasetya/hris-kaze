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
 * @file   Response.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Libraries\Terminal;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class Response implements Contracts\Response {
	/**
	 * Process.
	 *
	 * @var Process
	 */
	protected Process $process;

	/**
	 * Instantiate a new response instance.
	 *
	 * @param \Symfony\Component\Process\Process $process
	 */
	public function __construct(Process $process) {
		$this->process = $process;
	}

	/**
	 * Return an array of outputed lines.
	 *
	 * @return array
	 */
	public function lines() {
		$result = [];

		foreach ($this->getIterator() as $line) {
			$result[] = $line;
		}

		return $result;
	}

	/**
	 * Get output iterator.
	 *
	 * @return \Generator
	 */
	public function getIterator() {
		foreach ($this->process() as $type => $line) {
			yield new OutputLine($type, $line);
		}
	}

	/**
	 * Get the underlying process instance.
	 *
	 * @return \Symfony\Component\Process\Process
	 */
	public function process() {
		return $this->process;
	}

	/**
	 * Throw an exception if the process was not successful.
	 *
	 * @return Response
	 *
	 * @throws ProcessFailedException
	 */
	public function throw() {
		if ($this->successful()) {
			return $this;
		}

		throw new ProcessFailedException($this->process());
	}

	/**
	 * Check if the process ended successfully.
	 *
	 * @return bool
	 */
	public function successful(): bool {
		return $this->process()->isSuccessful();
	}

	/**
	 * Check if the process ended successfully.
	 *
	 * @return bool
	 */
	public function ok(): bool {
		return $this->successful();
	}

	public function nok(): bool {
		return !$this->successful();
	}

	/**
	 * Get the process output.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->output();
	}

	/**
	 * Get the process output.
	 *
	 * @return string
	 */
	public function output() {
		return $this->process()->getOutput();
	}

	/**
	 * Dynamically forward calls to the process instance.
	 *
	 * @param string $method
	 * @param array  $parameters
	 *
	 * @return mixed
	 */
	public function __call(string $method, array $parameters = []) {
		return call_user_func([$this->process(), $method], ...$parameters);
	}
}