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
 * @file   Factory.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Libraries\Terminal\Contracts;

use App\Libraries\Terminal\Builder;
use App\Libraries\Terminal\Fakes\BuilderFake;
use App\Libraries\Terminal\OutputLine;
use Symfony\Component\Process\Process;


interface Factory {
	/**
	 * Fake terminal.
	 *
	 * @param array $commands
	 *
	 * @return void
	 */
	public static function fake(array $commands = []);

	/**
	 * Reset the fake Terminal.
	 *
	 * @return void
	 */
	public static function reset();

	/**
	 * Create new output line(s)
	 *
	 * @param mixed  $content
	 * @param string $type
	 *
	 * @return OutputLine|OutputLine[]
	 */
	public static function line($content, string $type = Process::OUT);

	/**
	 * Parse given lines.
	 *
	 * @param mixed  $lines
	 *
	 * @param string $type
	 *
	 * @return OutputLine[]
	 */
	public static function lines($lines, string $type = Process::OUT);

	/**
	 * Create a new error line.
	 *
	 * @param string $content
	 *
	 * @return OutputLine
	 */
	public static function error(string $content);

	/**
	 * Get an instance of the Process builder class.
	 *
	 * @return Builder|BuilderFake
	 */
	public static function builder();
}