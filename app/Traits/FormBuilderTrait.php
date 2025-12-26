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
 * @file   FormBuilderTrait.php
 * @date   28/08/2020 21.16
 */

namespace App\Traits;

use Illuminate\Support\Facades\App;


trait FormBuilderTrait {

	/**
	 * Create a Form instance.
	 *
	 * @param string $name    Full class name of the form class.
	 * @param array  $options Options to pass to the form.
	 * @param array  $data    additional data to pass to the form.
	 *
	 * @return \App\Managers\Form\Form
	 */
	protected function form($name, array $options = [], array $data = []) {
		return App::make('laravel-form-builder')->create($name, $options, $data);
	}

	/**
	 * Create a plain Form instance.
	 *
	 * @param array $options Options to pass to the form.
	 * @param array $data    additional data to pass to the form.
	 *
	 * @return \App\Managers\Form\Form
	 */
	protected function plain(array $options = [], array $data = []) {
		return App::make('laravel-form-builder')->plain($options, $data);
	}
}
