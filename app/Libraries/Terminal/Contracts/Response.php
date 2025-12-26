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

namespace App\Libraries\Terminal\Contracts;

use IteratorAggregate;
use Symfony\Component\Process\Exception\ProcessFailedException;


interface Response extends IteratorAggregate {
	/**
	 * Check if the process ended successfully.
	 *
	 * @return boolean
	 */
	public function ok();

	/**
	 * Check if the process ended successfully.
	 *
	 * @return boolean
	 */
	public function successful();

	/**
	 * Get the process output.
	 *
	 * @return string
	 */
	public function output();

	/**
	 * Get the process output.
	 *
	 * @return string
	 */
	public function __toString();

	/**
	 * Return an array of outputed lines.
	 *
	 * @return array
	 */
	public function lines();

	/**
	 * Throw an exception if the process was not successful.
	 *
	 * @return Response
	 *
	 * @throws ProcessFailedException
	 */
	public function throw();

	/**
	 * Get the underlying process instance.
	 *
	 * @return \Symfony\Component\Process\Process
	 */
	public function process();
}