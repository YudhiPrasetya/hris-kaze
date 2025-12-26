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
 * @date   2020-10-29 5:31:14
 */

return [
	'defaults'        => [
		'wrapper_class'       => 'form-group',
		'wrapper_error_class' => 'has-error',
		'label_class'         => 'control-label',
		'field_class'         => 'form-control',
		'field_error_class'   => 'is-invalid',
		//'help_block_class'    => 'help-block',
		'help_block_class'    => 'form-text text-nunito text-muted fs--1',
		'error_class'         => 'invalid-feedback text-danger mt-1',
		'required_class'      => 'required',
		'button'              => [
			'field_class' => 'btn',
		],
		'file'                => [
			'field_class' => 'custom-file-input',
		],
		'datetime'            => [
			'field_class' => 'form-control datetime datepicker',
		],
		'datetime-local'      => [
			'field_class' => 'form-control datetime datepicker',
		],
		'date'                => [
			'field_class' => 'form-control date datepicker',
		],
		'time'                => [
			'field_class' => 'form-control time timepicker',
		],
		'browse'              => [
			'field_class' => '',
		],
		'submit'              => [
			'field_class' => 'btn',
		],
		'switch'              => [
			//'wrapper_class' => "custom-control custom-switch",
			'label_class' => 'custom-control-label',
			'field_class' => 'custom-control-input',
		],
		'checkbox'            => [
			'wrapper_class' => "custom-control custom-checkbox",
			'label_class'   => 'custom-control-label',
			'field_class'   => 'custom-control-input',
		],
		'radio'               => [
			'wrapper_class' => 'custom-control custom-radio',
			'label_class'   => 'custom-control-label',
			'field_class'   => 'custom-control-input',
		],
		'entity'              => [
			'field_class' => 'custom-select',
		],
		'select'              => [
			'field_class' => 'custom-select',
		],

		// Override a class from a field.
		//'text'                => [
		//    'wrapper_class'   => 'form-field-text',
		//    'label_class'     => 'form-field-text-label',
		//    'field_class'     => 'form-field-text-field',
		//]
		//'radio'               => [
		//    'choice_options'  => [
		//        'wrapper'     => ['class' => 'form-radio'],
		//        'label'       => ['class' => 'form-radio-label'],
		//        'field'       => ['class' => 'form-radio-field'],
		//],
	],
	// Templates
	'form'            => 'laravel-form-builder::form',
	'file'            => 'laravel-form-builder::file',
	'browse'          => 'laravel-form-builder::browse',
	'text'            => 'laravel-form-builder::text',
	'date'            => 'laravel-form-builder::date',
	'input_group'     => 'laravel-form-builder::input_group',
	'textarea'        => 'laravel-form-builder::textarea',
	'button'          => 'laravel-form-builder::button',
	'buttongroup'     => 'laravel-form-builder::buttongroup',
	'radio'           => 'laravel-form-builder::radio',
	'checkbox'        => 'laravel-form-builder::checkbox',
	'switch'          => 'laravel-form-builder::switch',
	'select'          => 'laravel-form-builder::select',
	'choice'          => 'laravel-form-builder::choice',
	'repeated'        => 'laravel-form-builder::repeated',
	'child_form'      => 'laravel-form-builder::child_form',
	'collection'      => 'laravel-form-builder::collection',
	'static'          => 'laravel-form-builder::static',

	// Remove the laravel-form-builder:: prefix above when using template_prefix
	'template_prefix' => '',

	'default_namespace' => '',

	'custom_fields' => [
		// 'datetime' => App\Forms\Fields\Datetime::class
	],
];
