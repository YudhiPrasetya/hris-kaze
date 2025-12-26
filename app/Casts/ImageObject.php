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
 * @file   ImageObject.php
 * @date   2020-10-29 5:31:13
 */

namespace App\Casts;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;


/**
 * Class ImageObject
 *
 * @package App\Casts
 */
class ImageObject {
	/**
	 * @var Stringable|null
	 */
	private ?Stringable $data;

	/**
	 * @var Collection|mixed
	 */
	private Collection $mimeTypes;

	/**
	 * ImageObject constructor.
	 *
	 * @param string $data
	 */
	public function __construct(?string $data) {
		$this->data = Str::of($data);
		$this->mimeTypes = require(resource_path('etc/mime_types.php'));
	}

	public function __get($name) {
		return $this->toString();
	}

	/**
	 * @return string
	 */
	public function getMimeType(): string {
		return $this->data->between('data:', ';');
	}

	/**
	 * @return string
	 */
	public function getExtension(): string {
		return $this->mimeTypes->where('mime-type', $this->data->between('data:', ';'))->get('extension');
	}

	public function isEmpty() {
		return $this->data->isEmpty();
	}

	/**
	 * @return false|mixed
	 */
	public function toImage() {
		return base64_decode($this->data->after(','));
	}

	public function toString(): ?string {
		return $this->data->__toString();
	}

	/**
	 * @return string|null
	 */
	public function __toString(): string {
		return $this->toString() ?? '';
	}
}
