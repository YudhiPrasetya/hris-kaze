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
 * @file   DateTimeObject.php
 * @date   2020-10-29 5:31:13
 */

namespace App\Casts;

use DateTime;
use DateTimeZone;


class DateTimeObject extends DateTime {
	/**
	 * @var string|null
	 */
	private ?string $format;

	/**
	 * @var mixed|string|null
	 */
	private ?string $value;

	/**
	 * DateTimeObject constructor.
	 *
	 * @param string             $time
	 * @param \DateTimeZone|null $timezone
	 *
	 * @throws \Exception
	 */
	public function __construct($time = 'now', DateTimeZone $timezone = null) {
		parent::__construct($time, $timezone);

		$this->format = null;
		$this->value = $time;
	}

	/**
	 * @param string $format
	 *
	 * @return $this
	 */
	public function setFormat(string $format): self {
		$this->format = $format;

		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString(): string {
		return empty($this->value) ? '-' : $this->format($this->format);
	}
}
