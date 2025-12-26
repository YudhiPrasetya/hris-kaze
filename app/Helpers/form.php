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
 * @file   form.php
 * @date   27/08/2020 04.34
 */

use App\Managers\Form\Fields\FormField;
use App\Managers\Form\Form;


if (!function_exists('form')) {
	function form(Form $form, array $options = []): string {
		return $form->renderForm($options);
	}
}

if (!function_exists('form_start')) {
	function form_start(Form $form, array $options = []): string {
		return $form->renderForm($options, true, false, false);
	}
}

if (!function_exists('form_end')) {
	function form_end(Form $form, $showFields = true): string {
		return $form->renderRest(true, $showFields);
	}
}

if (!function_exists('form_rest')) {
	function form_rest(Form $form): string {
		return $form->renderRest(false);
	}
}

if (!function_exists('form_until')) {
	function form_until(Form $form, $field_name): string {
		return $form->renderUntil($field_name, false);
	}
}

if (!function_exists('form_row')) {
	function form_row(FormField $formField, array $options = []): string {
		return $formField->render($options);
	}
}

if (!function_exists('form_rows')) {
	function form_rows(Form $form, array $fields, array $options = []): string {
		return implode(
			array_map(
				function ($field) use ($form, $options) {
					return $form->has($field) ? $form->getField($field)->render($options) : '';
				},
				$fields
			)
		);
	}
}

if (!function_exists('form_label')) {
	function form_label(FormField $formField, array $options = []): string {
		return $formField->render($options, true, false, false);
	}
}

if (!function_exists('form_widget')) {
	function form_widget(FormField $formField, array $options = []): string {
		return $formField->render($options, false, true, false);
	}
}

if (!function_exists('form_errors')) {
	function form_errors(FormField $formField, array $options = []): string {
		return $formField->render($options, false, false, true);
	}
}

if (!function_exists('form_fields')) {
	function form_fields(Form $form, array $options = []): string {
		return $form->renderForm($options, false, true, false);
	}
}
