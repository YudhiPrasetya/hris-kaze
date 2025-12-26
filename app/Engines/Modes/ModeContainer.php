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
 * @file   ModeContainer.php
 * @date   2020-09-28 14:20:24
 */

namespace App\Engines\Modes;

class ModeContainer {
	public $mode;

	public $fallbackMode;

	public function __construct($mode, $fallbackMode) {
		$this->mode = $mode;
		$this->fallbackMode = $fallbackMode;
	}
}