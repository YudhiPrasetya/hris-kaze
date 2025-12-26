<?php

namespace App\Managers\Form\Filters\Collection;

use App\Managers\Form\Filters\FilterInterface;


/**
 * Class BaseName
 *
 * @package App\Managers\Form\Filters\Collection
 * @author  Djordje Stojiljkovic <djordjestojilljkovic@gmail.com>
 */
class HtmlEntities implements FilterInterface {

	/**
	 * Second arg of htmlentities function.
	 *
	 * @var integer
	 */
	protected $quoteStyle;

	/**
	 * Third arg of htmlentities function.
	 *
	 * @var string
	 */
	protected $encoding;

	/**
	 * Fourth arg of htmlentities function.
	 *
	 * @var string
	 */
	protected $doubleQuote;

	/**
	 * HtmlEntities constructor.
	 *
	 * @param array $options
	 */
	public function __construct(array $options = []) {
		if (!isset($options['quotestyle'])) {
			$options['quotestyle'] = ENT_COMPAT;
		}

		if (!isset($options['encoding'])) {
			$options['encoding'] = 'UTF-8';
		}

		if (isset($options['charset'])) {
			$options['encoding'] = $options['charset'];
		}

		if (!isset($options['doublequote'])) {
			$options['doublequote'] = true;
		}

		$this->setQuoteStyle($options['quotestyle']);
		$this->setEncoding($options['encoding']);
		$this->setDoubleQuote($options['doublequote']);
	}

	/**
	 * Returns the charSet property
	 *
	 * Proxies to {@link getEncoding()}
	 *
	 * @return string
	 */
	public function getCharSet(): string {
		return $this->getEncoding();
	}

	/**
	 * @return string
	 */
	public function getEncoding(): string {
		return $this->encoding;
	}

	/**
	 * @param string $encoding
	 *
	 * @return \App\Managers\Form\Filters\Collection\HtmlEntities
	 */
	public function setEncoding($encoding): self {
		$this->encoding = (string)$encoding;

		return $this;
	}

	/**
	 * Sets the charSet property.
	 *
	 * Proxies to {@link setEncoding()}.
	 *
	 * @param string $charSet
	 *
	 * @return \App\Managers\Form\Filters\Collection\HtmlEntities
	 */
	public function setCharSet($charSet): string {
		return $this->setEncoding($charSet);
	}

	/**
	 * @param string $value
	 * @param array  $options
	 *
	 * @return mixed
	 *
	 * @throws \Exception
	 */
	public function filter($value, $options = []): string {
		$value = (string)$value;
		$filtered = htmlentities(
			$value,
			$this->getQuoteStyle(),
			$this->getEncoding(),
			$this->getDoubleQuote()
		);

		if (strlen($value) && !strlen($filtered)) {
			if (!function_exists('iconv')) {
				$ex = new \Exception('Encoding mismatch has resulted in htmlentities errors.');
				throw $ex;
			}

			$enc = $this->getEncoding();
			$value = iconv('', $enc . '//IGNORE', $value);
			$filtered = htmlentities($value, $this->getQuoteStyle(), $enc, $this->getDoubleQuote());

			if (!strlen($filtered)) {
				$ex = new \Exception('Encoding mismatch has resulted in htmlentities errors.');
				throw $ex;
			}
		}

		return $filtered;
	}

	/**
	 * @return integer
	 */
	public function getQuoteStyle(): string {
		return $this->quoteStyle;
	}

	/**
	 * @param integer $style
	 *
	 * @return \App\Managers\Form\Filters\Collection\HtmlEntities
	 */
	public function setQuoteStyle($style): self {
		$this->quoteStyle = $style;

		return $this;
	}

	/**
	 * Returns the doubleQuote property.
	 *
	 * @return boolean
	 */
	public function getDoubleQuote(): string {
		return $this->doubleQuote;
	}

	/**
	 * Sets the doubleQuote property.
	 *
	 * @param boolean $doubleQuote
	 *
	 * @return \App\Managers\Form\Filters\Collection\HtmlEntities
	 */
	public function setDoubleQuote($doubleQuote): self {
		$this->doubleQuote = (boolean)$doubleQuote;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return 'HtmlEntities';
	}
}