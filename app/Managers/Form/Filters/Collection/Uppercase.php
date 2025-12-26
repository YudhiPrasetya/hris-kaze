<?php

namespace App\Managers\Form\Filters\Collection;

use App\Managers\Form\Filters\FilterInterface;


/**
 * Class Uppercase
 *
 * @package App\Managers\Form\Filters\Collection
 * @author  Djordje Stojiljkovic <djordjestojilljkovic@gmail.com>
 */
class Uppercase implements FilterInterface {
	/**
	 * @var string $encoding
	 */
	protected $encoding = null;

	/**
	 * StringToUpper constructor.
	 *
	 * @param array $options
	 */
	public function __construct($options = []) {
		if (!array_key_exists('encoding', $options) && function_exists('mb_internal_encoding')) {
			$options['encoding'] = mb_internal_encoding();
		}

		if (array_key_exists('encoding', $options)) {
			$this->setEncoding($options['encoding']);
		}
	}

	/**
	 * @param mixed $value
	 * @param array $options
	 *
	 * @return string
	 */
	public function filter($value, $options = []): string {
		$value = (string)$value;
		if ($this->getEncoding()) {
			return mb_strtoupper($value, $this->getEncoding());
		}

		return strtoupper($value);
	}

	/**
	 * @return string
	 */
	public function getEncoding(): string {
		return $this->encoding;
	}

	/**
	 * @param null $encoding
	 *
	 * @return \App\Managers\Form\Filters\Collection\Uppercase
	 *
	 * @throws \Exception
	 */
	public function setEncoding($encoding): self {
		if ($encoding !== null) {
			if (!function_exists('mb_strtoupper')) {
				throw new \Exception('mbstring extension is required for value mutating.');
			}

			$encoding = (string)$encoding;
			if (!in_array(strtolower($encoding), array_map('strtolower', mb_list_encodings()))) {
				throw new \Exception('The given encoding ' . $encoding . ' is not supported by mbstring ext.');
			}
		}

		$this->encoding = $encoding;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return 'Uppercase';
	}
}