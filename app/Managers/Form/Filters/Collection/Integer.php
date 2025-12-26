<?php

namespace App\Managers\Form\Filters\Collection;

use App\Managers\Form\Filters\FilterInterface;


/**
 * Class Integer
 *
 * @package App\Managers\Form\Filters\Collection
 * @author  Djordje Stojiljkovic <djordjestojilljkovic@gmail.com>
 */
class Integer implements FilterInterface {
	/**
	 * @param mixed $value
	 * @param array $options
	 *
	 * @return mixed
	 */
	public function filter($value, $options = []): int {
		$value = (int)((string)$value);

		return $value;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return 'Integer';
	}
}