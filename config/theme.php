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
 * @file   theme.php
 * @date   24/08/2020 09.31
 */

return [
	/*
	|--------------------------------------------------------------------------
	| Default active Theme
	|--------------------------------------------------------------------------
	|
	| Default active themename. like as
	| 'active' => 'themeone',
	|
	*/
	'active'       => 'Falcon',

	/*
	|--------------------------------------------------------------------------
	| Themes path
	|--------------------------------------------------------------------------
	|
	| This path used for save the generated theme. This path also will added
	| automatically to list of scanned folders.
	|
	*/
	'theme_path'   => base_path('resources/themes'),

	/*
	|--------------------------------------------------------------------------
	| Symbolic link
	|--------------------------------------------------------------------------
	|
	| If you theme_path is not in public folder then symlink must be true
	| otherwise theme assets not working. If your theme_path under public folder
	| then symlink can be false or true as your wish.
	|
	*/
	'symlink'      => false,

	/*
	|--------------------------------------------------------------------------
	| Symbolic path
	|--------------------------------------------------------------------------
	|
	| you can change your
	|
	*/
	'symlink_path' => public_path('themes'),

	/*
	|--------------------------------------------------------------------------
	| Theme types where you can set default theme for particular middleware.
	|--------------------------------------------------------------------------
	| 'types'     => [
	|       'enable' => true or false,
	|       'middleware' => [
	|           'middlewareName'      => 'themeName',
	|       ]
	|   ],
	|
	| For Example route
	| Route::get('/', function () {
	|       return view('welcome');
	| })->middleware('example');
	|
	|
	*/
	'types'        => [
		'enable'     => false,
		'middleware' => [
			'example' => 'admin',
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Theme config name and change log file name
	|--------------------------------------------------------------------------
	|
	| Here is the config for theme.json file and changelog
	| for version control status
	|
	*/
	'config'       => [
		'name'      => 'theme.json',
		'changelog' => 'changelog.yml',
	],

	/*
	|--------------------------------------------------------------------------
	| Themes folder structure
	|--------------------------------------------------------------------------
	|
	| Here you may update theme folder structure.
	|
	*/
	'folders'      => [
		'assets'  => 'assets',
		'views'   => 'views',
		'lang'    => 'lang',
		'lang/en' => 'lang/en',

		'css' => 'assets/css',
		'js'  => 'assets/js',
		'img' => 'assets/img',

		'layouts' => 'views/layouts',
	],

	/*
	|--------------------------------------------------------------------------
	| Theme Stubs
	|--------------------------------------------------------------------------
	|
	| Default theme stubs.
	|
	*/
	'stubs'        => [
		'path'  => base_path('app/Console/Commands/stubs/theme'),
		'files' => [
			'css'    => 'assets/css/app.css',
			'js'     => 'assets/js/app.js',
			'layout' => 'views/layouts/master.blade.php',
			'page'   => 'views/welcome.blade.php',
			'lang'   => 'lang/en/messages.php',
		],
	],
];