<?php
/*
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   ImagePlaceholderAttributes.php
 * @date   2021-07-8 14:2:39
 */

namespace App\Libraries\Placeholder;

class ImagePlaceholderAttributes {

	/**
	 * ImagePlaceholderAttributes constructor.
	 *
	 * @param string $request_uri
	 */
	public function __construct(string $request_uri) {
		$uri = explode('?', $request_uri, 2);

		$this->uri = substr($uri[0], 1);
		$this->get = isset($uri[1]) ? explode('&', $uri[1]) : null;

		if ($this->haveSize()) {
			$this->width = (int)$this->imageSize('width');
			$this->height = (int)$this->imageSize('height');
		}

		$this->styles = $this->imageAttributes();
	}

	public function haveSize() {
		return is_numeric($this->imageSize('width'));
	}

	/**
	 * Get width and height of an image.
	 *
	 * @param string|null $attribute
	 *
	 * @return array|mixed
	 */
	public function imageSize(string $attribute = null) {
		if (strpos($this->uri, '-') > -1) {
			$attributes = explode('-', $this->uri);
		}
		elseif (strpos($this->uri, 'x')) {
			$attributes = explode('x', $this->uri);
		}
		else {
			$attributes = [$this->uri];
		}

		$sizes = [
			'width'  => $attributes[0],
			'height' => isset($attributes[1]) ? $attributes[1] : $attributes[0]
		];

		if (isset($attribute)) {
			return $sizes[$attribute];
		}

		return $sizes;
	}

	/**
	 * Get image attributes.
	 *
	 * @return array
	 */
	public function imageAttributes(): array {
		$attributes = [];

		if (is_array($this->get)) {
			foreach ($this->get as $attribute) {
				$explode = explode('=', $attribute);
				$attributes[$explode[0]] = $explode[1];
			}
		}

		return $attributes;
	}
}
