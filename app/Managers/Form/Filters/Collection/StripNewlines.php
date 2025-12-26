<?php

namespace App\Managers\Form\Filters\Collection;

use App\Managers\Form\Filters\FilterInterface;


/**
 * Class StripNewlines
 *
 * @package App\Managers\Form\Filters\Collection
 * @author  Djordje Stojiljkovic <djordjestojilljkovic@gmail.com>
 */
class StripNewlines implements FilterInterface {
	/**
	 * @param mixed $value
	 * @param array $options
	 *
	 * @return mixed
	 */
	public function filter($value, $options = []): string {
		return str_replace(["\n", "\r"], '', $value);
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return 'StripNewlines';
	}
}