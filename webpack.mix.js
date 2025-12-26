/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   webpack.mix.js
 * @date   2020-10-29 5:31:15
 */

const fs = require('fs');
const _ = require('lodash');
const mix = require('laravel-mix');
const path = require('path');
const modules = require(__dirname + '/resources/js/mix');

require('laravel-mix-merge-manifest');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.autoload({
	jquery: ['$', 'window.jQuery', 'jQuery'],
	lodash: ['_', 'window._'],
	toastr: ['toastr', 'window.toastr'],
	flatpickr: ['flatpickr', 'window.flatpickr'],
	bootbox: ['bootbox', 'window.bootbox'],
	axios: ['axios', 'window.axios'],
	AutoNumeric: ['AutoNumeric', 'window.AutoNumeric'],
	'@fancyapps/fancybox': ['fancybox', 'window.fancybox'],
	//'tinymce': ['tinymce'],
	select2: ['select2', 'window.select2'],
	moment: ['moment', 'window.moment'],
	// turbolinks: ['turbolinks', 'TurboLinks', 'window.TurboLinks', 'window.turbolinks'],
	'popper.js': ['Popper', 'window.Popper'],
	'signature_pad': ['signature'],
	'bootstrap-table': ['bootstrapTable', 'window.bootstrapTable']
});

const THEME_PATH = path.resolve(__dirname, 'resources/themes');
let aliases = {
	ziggy: path.resolve('vendor/tightenco/ziggy/dist'),
	main: path.resolve('resources/js'),
};

mix.alias(aliases);

mix
	.options({
		cssNano: {
			discardComments: {
				removeAll: true
			}
		},
		uglify: {
			uglifyOptions: {
				comments: false
			}
		}
	})
	// .browserSync({
	//     proxy: 'https://kaze.omni.dev',
	//     open: false
	// })
	.js('node_modules/turbolinks/dist/turbolinks.js', 'public/js')
	.js('node_modules/tinymce/tinymce.js', 'public/js/tinymce')
	//.js('resources/js/app.js', 'public/js')
	//.sass('resources/sass/app.scss', 'public/css')
	.copyDirectory('resources/img/flags', 'public/images/flags')
	.copyDirectory('resources/img/logo', 'public/images/logo')
	.copyDirectory('node_modules/tinymce/icons', 'public/js/tinymce/icons')
	.copyDirectory('node_modules/tinymce/skins', 'public/js/tinymce/skins')
	.copyDirectory('node_modules/tinymce/themes', 'public/js/tinymce/themes')
	.copyDirectory('node_modules/tinymce/plugins', 'public/js/tinymce/plugins')
;
modules(mix);

// This is required for manifest.js available on public/js
mix.js('resources/js/app.js', 'public/js')
   .js('resources/js/date.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css', [
	   require('postcss-import'),
	   require('tailwindcss'),
	   require('autoprefixer')
   ]);

if (mix.inProduction()) {
	mix.version();
}

// mix.browserSync({
// 	proxy: 'kaze.omnity.dev',
// 	open: false
// });
