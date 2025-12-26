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
 * @file   AbstractParser.php
 * @date   24/08/2020 10.01
 */

namespace App\Libraries\Config\Parser;

use App\Contracts\ParserInterface;


abstract class AbstractParser implements ParserInterface {
	/**
	 * String with configuration
	 *
	 * @var string
	 */
	protected $config;

	/**
	 * Sets the string with configuration
	 *
	 * @param string $config
	 * @param string $filename
	 *
	 * @codeCoverageIgnore
	 */
	public function __construct($config, $filename = null) {
		$this->config = $config;
	}
}