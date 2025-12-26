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
 * @file   ResponseFake.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Libraries\Terminal\Fakes;

use App\Libraries\Terminal\Response;
use Symfony\Component\Process\Process;


class ResponseFake extends Response {
	/**
	 * Response lines.
	 *
	 * @var array
	 */
	protected array $lines;

	/**
	 * Exit code.
	 *
	 * @var int
	 */
	protected int $exitCode;

	/**
	 * Instantiate a new process instance.
	 *
	 * @param \Symfony\Component\Process\Process $process
	 * @param array                              $lines
	 * @param int                                $exitCode
	 */
	public function __construct(Process $process, array $lines = [], int $exitCode = 0) {
		parent::__construct($process);

		$this->lines = $lines;
		$this->exitCode = $exitCode;
	}

	/**
	 * Check if the process ended successfully.
	 *
	 * @return bool
	 */
	public function successful(): bool {
		return $this->exitCode === 0;
	}

	/**
	 * Get the process output.
	 *
	 * @return string
	 */
	public function output() {
		return implode(PHP_EOL,
			array_map(function ($line) {
				return (string)$line;
			},
				$this->lines));
	}

	/**
	 * Get output iterator.
	 *
	 * @return \Generator
	 */
	public function getIterator() {
		foreach ($this->lines as $line) {
			yield $line;
		}
	}

	/**
	 * Indicate the the current response has failed.
	 *
	 * @return ResponseFake
	 */
	public function shouldFail() {
		$this->exitCode = 1;

		return $this;
	}
}