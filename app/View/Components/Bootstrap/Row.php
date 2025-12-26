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
 * @file   Row.php
 * @date   20/09/2020 03.20
 */

namespace App\View\Components\Bootstrap;

use Illuminate\View\Component;


class Row extends Component {
	/**
	 * @var bool
	 */
	public bool $gutters = true;

	/**
	 * Row constructor.
	 *
	 * @param bool $gutters
	 */
	public function __construct(bool $gutters = true) {
		$this->gutters = $gutters;
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\View\View|string
	 */
	public function render() {
		return view('components.bootstrap.row');
	}
}
