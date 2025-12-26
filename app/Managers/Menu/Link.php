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
 * @file   Link.php
 * @date   27/08/2020 02.41
 */

namespace App\Managers\Menu;

class Link {
	/**
	 * Link attributes.
	 *
	 * @var array
	 */
	public $attributes = array();

	/**
	 * Flag for active state.
	 *
	 * @var bool
	 */
	public $isActive = false;

	/**
	 * Path Information.
	 *
	 * @var array
	 */
	public $path = array();

	/**
	 * Explicit href for the link.
	 *
	 * @var string
	 */
	public $href;

	/**
	 * Reference to the menu builder.
	 *
	 * @var Builder
	 */
	protected $builder;

	/**
	 * Creates a hyper link instance.
	 *
	 * @param array   $path
	 * @param Builder $builder
	 */
	public function __construct(array $path, Builder $builder) {
		$this->path = $path;
		$this->builder = $builder;
	}

	/**
	 * Make the anchor active.
	 *
	 * @return $this
	 */
	public function active(): Link {
		$this->attributes['class'] =
			Builder::formatGroupClass(array('class' => $this->builder->conf('active_class')), $this->attributes);
		$this->isActive = true;

		return $this;
	}

	/**
	 * Set Anchor's href property.
	 *
	 * @param string $href
	 *
	 * @return $this
	 */
	public function href(string $href): Link {
		$this->href = $href;

		return $this;
	}

	/**
	 * Make the url secure.
	 *
	 * @return $this
	 */
	public function secure(): Link {
		$this->path['secure'] = true;

		return $this;
	}

	/**
	 * Check for a method of the same name if the attribute doesn't exist.
	 *
	 * @param $prop
	 *
	 * @return $this|Link|array|mixed|null
	 */
	public function __get($prop) {
		if (property_exists($this, $prop)) {
			return $this->$prop;
		}

		return $this->attr($prop);
	}

	/**
	 * Add attributes to the link.
	 *
	 * @param mixed
	 *
	 * @return $this|array|null
	 */
	public function attr() {
		$args = func_get_args();

		if (isset($args[0]) && is_array($args[0])) {
			$this->attributes = array_merge($this->attributes, $args[0]);

			return $this;
		}
		elseif (isset($args[0]) && isset($args[1])) {
			$this->attributes[$args[0]] = $args[1];

			return $this;
		}
		elseif (isset($args[0])) {
			return isset($this->attributes[$args[0]]) ? $this->attributes[$args[0]] : null;
		}

		return $this->attributes;
	}
}