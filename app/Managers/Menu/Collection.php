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
 * @file   Collection.php
 * @date   27/08/2020 02.40
 */

namespace App\Managers\Menu;

use Illuminate\Support\Collection as CollectionBase;


/**
 * Class Collection
 *
 * @package App\Managers\Menu
 */
class Collection extends CollectionBase {
	/**
	 * Add attributes to a collection of items.
	 *
	 * @param mixed
	 *
	 * @return Collection
	 */
	public function attr() {
		$args = func_get_args();

		$this->each(
			function ($item) use ($args) {
				if (count($args) >= 2) {
					$item->attr($args[0], $args[1]);
				}
				else {
					$item->attr($args[0]);
				}
			}
		);

		return $this;
	}

	/**
	 * Add meta data to a collection of items.
	 *
	 * @param mixed
	 *
	 * @return Collection
	 */
	public function data() {
		$args = func_get_args();

		$this->each(
			function ($item) use ($args) {
				if (count($args) >= 2) {
					$item->data($args[0], $args[1]);
				}
				else {
					$item->data($args[0]);
				}
			}
		);

		return $this;
	}

	/**
	 * Appends text or HTML to a collection of items.
	 *
	 * @param string
	 *
	 * @return Collection
	 */
	public function append($html) {
		$this->each(
			function ($item) use ($html) {
				$item->title .= $html;
			}
		);

		return $this;
	}

	/**
	 * Prepends text or HTML to a collection of items.
	 *
	 * @param mixed $html
	 * @param mixed $key
	 *
	 * @return Collection
	 */
	public function prepend($html, $key = null) {
		$this->each(
			function ($item) use ($html) {
				$item->title = $html . $item->title;
			}
		);

		return $this;
	}
}