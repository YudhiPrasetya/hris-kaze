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
 * @file   Crumb.php
 * @date   2020-10-29 5:31:14
 */

namespace App\Managers\Breadcrumbs;

class Crumb {
	/**
	 * The crumb title.
	 *
	 * @var string|array
	 */
	protected $title;

	/**
	 * The crumb URL.
	 *
	 * @var ?string
	 */
	protected ?string $url;

	protected $route;

	/**
	 * Construct the crumb instance.
	 *
	 * @param string|array            $title
	 * @param string|null             $url
	 *
	 * @param mixed|array|string|null $route
	 */
	public function __construct($title, ?string $url = null, $route = null) {
		$this->title = $title;
		$this->url = $url;
		$this->route = $route;
	}

	/**
	 * Get the crumb URL.
	 *
	 * @return string|null
	 */
	public function url(): ?string {
		return $this->url;
	}

	public function truncated(): bool {
		$isIcon = substr($this->title(), 0, 3) == '<i ';
		$isIcon = substr($this->title(), 0, 6) == '<span ' || $isIcon;

		return !$isIcon;
	}

	/**
	 * Get the crumb title.
	 *
	 * @return string|array
	 */
	public function title() {
		return trim($this->title);
	}
}