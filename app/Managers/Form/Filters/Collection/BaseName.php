<?php

namespace App\Managers\Form\Filters\Collection;

use App\Managers\Form\Filters\FilterInterface;


/**
 * Class BaseName
 *
 * @package App\Managers\Form\Filters\Collection
 * @author  Djordje Stojiljkovic <djordjestojilljkovic@gmail.com>
 */
class BaseName implements FilterInterface {
	/**
	 * @param string $value
	 * @param array  $options
	 *
	 * @return string
	 */
	public function filter($value, $options = []): string {
		$value = (string)$value;

		return basename($value);
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return 'BaseName';
	}
}