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
 * @file   Media.php
 * @date   2020-10-29 5:31:14
 */

namespace App\View\Components\Bootstrap;

use Illuminate\View\Component;


class Media extends Component {
	public string $variant;

	public string $title;

	public ?string $subtitle;

	public ?string $icon;

	public int $size;

	/**
	 * Media constructor.
	 *
	 * @param string      $title
	 * @param string|null $subtitle
	 * @param int         $headerSize
	 * @param string      $variant
	 * @param string|null $icon
	 */
	public function __construct(string $title, ?string $subtitle = null, int $size = 5, string $variant = 'primary', ?string $icon = null) {
		$this->variant = $variant;
		$this->title = $title;
		$this->subtitle = $subtitle;
		$this->icon = $icon;
		$this->size = $size;
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\View\View|string
	 */
	public function render() {
		return view('components.bootstrap.media');
	}
}
