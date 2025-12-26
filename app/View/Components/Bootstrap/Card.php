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
 * @file   Card.php
 * @date   20/09/2020 03.20
 */

namespace App\View\Components\Bootstrap;

use Illuminate\View\Component;


class Card extends Component {
	/**
	 * Create a new component instance.
	 *
	 * @return void
	 */
	public function __construct() {
		//
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\View\View|string
	 */
	public function render() {
		return view('components.bootstrap.card');
	}
}
