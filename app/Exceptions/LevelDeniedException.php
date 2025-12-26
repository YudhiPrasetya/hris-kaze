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
 * @file   LevelDeniedException.php
 * @date   24/08/2020 05.09
 */

namespace App\Exceptions;

class LevelDeniedException extends AccessDeniedException {
	/**
	 * LevelDeniedException constructor.
	 *
	 * @param $level
	 */
	public function __construct($level) {
		parent::__construct(sprintf("You don't have a required [%s] level.", $level));
	}
}