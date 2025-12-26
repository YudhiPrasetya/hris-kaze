/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   mix.js
 * @date   2020-10-29 5:31:15
 */

const path = require('path');
const _ = require('lodash');
const fs = require('fs');
const mime = require('mime');
const THEME_PATH = path.join(__dirname, '../themes');
const RESOURCE_PATH = path.join(__dirname, '../');
const BASE_PATH = path.join(__dirname, '../../');

module.exports = function (mix) {
	rmdir(path.join(BASE_PATH, 'public/assets'));
	rmdir(path.join(BASE_PATH, 'public/css'));
	rmdir(path.join(BASE_PATH, 'public/images'));
	rmdir(path.join(BASE_PATH, 'public/js'));

	!fs.existsSync(path.join(BASE_PATH, 'public/assets')) && fs.mkdirSync(path.join(BASE_PATH, 'public/assets'));
	!fs.existsSync(path.join(BASE_PATH, 'public/css')) && fs.mkdirSync(path.join(BASE_PATH, 'public/css'));
	!fs.existsSync(path.join(BASE_PATH, 'public/images')) && fs.mkdirSync(path.join(BASE_PATH, 'public/images'));
	!fs.existsSync(path.join(BASE_PATH, 'public/js')) && fs.mkdirSync(path.join(BASE_PATH, 'public/js'));

	add_theme_assets(mix);
	add_global_assets(mix);
};

function rmdir(dir) {
	let list = fs.readdirSync(dir);

	for(let i = 0; i < list.length; i++) {
		let filename = path.join(dir, list[i]);
		let stat = fs.statSync(filename);

		if(filename == "." || filename == "..") {
			// pass these files
		} else if(stat.isDirectory()) {
			// rmdir recursively
			rmdir(filename);
		} else {
			// rm fiilename
			fs.unlinkSync(filename);
		}
	}

	fs.rmdirSync(dir);
}

function get_asset_path(filename) {
	return path.join(RESOURCE_PATH, filename);
}

function get_module_path(filename) {
	return path.join(BASE_PATH, 'node_modules', filename);
}

/**
 * Walk through directories
 *
 * @param dir
 * @param callback
 * @returns {[]}
 */
function walk(dir, callback = false) {
	let results = [];
	let list = fs.readdirSync(dir);

	_.each(list, function (file) {
		let base = file;
		file = path.join(dir, file);
		let stat = fs.statSync(file);

		if (stat && stat.isDirectory()) {
			/* Recurse into a subdirectory */
			results = results.concat(walk(file, callback));
		}
		else {
			/* Is a file */
			let pathWoExt = file.substr(0, file.length - path.extname(file).length);
			/* and not minified */
			if (pathWoExt.substr(-4) !== ".min") results.push(file);
			callback && callback(file);
		}
	});

	return results;
}

/**
 * Get all installed themes
 *
 * @returns {[]}
 */
function get_themes(mix) {
	let results = [];
	let list = fs.readdirSync(THEME_PATH);

	_.each(list, function (file) {
		file = path.join(THEME_PATH, file);
		let stat = fs.statSync(file);

		if (stat && stat.isDirectory()) {
			let metadata = path.join(file, 'theme.json');

			if (fs.existsSync(metadata)) {
				let theme = JSON.parse(fs.readFileSync(metadata));
				theme.path = path.parse(metadata);
				results.push(theme);

				if (fs.existsSync(path.join(theme.path.dir, '.includes/js'))) {
					let inc = _.set({}, theme.name, path.resolve(theme.path.dir, '.includes/js'));
					mix.alias(inc);
				}
			}
		}
	});

	return results;
}

/**
 * Get aseet type
 *
 * @param mime
 * @param dir
 * @returns {string}
 */
function get_file_type(mime, dir = null) {
	switch (mime) {
		case 'text/css':
			return 'css';
		case 'text/x-scss':
			return 'sass';
		case 'application/javascript':
			return 'js';
		case 'application/font-woff':           // .woff
		case 'application/font-sfnt':           // .ttf
		case 'application/vnd.ms-fontobject':   // .eot
		case 'font/woff':                       // .woff
		case 'font/woff2':                      // .woff2
		case 'font/ttf':                        // .ttf
			return 'fonts';
		case 'image/png':
		case 'image/gif':
		case 'image/jpg':
		case 'image/jpeg':
		case 'image/bmp':
		case 'image/svg':
		case 'image/svg+xml':
		case 'image/ico':
			if (!_.isEmpty(dir)) {
				if (dir.indexOf('fonts') >= 0 || dir.indexOf('font') >= 0) return 'fonts';
			}
			return 'images';
	}
}

/**
 * Set target path for mix
 *
 * @param file
 * @param theme
 * @returns {string}
 */
function set_target_path(file, theme) {
	let basedir = path.join(__dirname, '../../resources');
	let dirname = path.basename(file.dir);
	let parts = [];
	let type = get_file_type(file.mimeType, file.dir);
	let filename = type === 'sass' ? file.name + '.css' : file.base;
	let exclude = ['css', 'sass', 'js', 'images', 'img', 'fonts'];
	type = type === 'sass' ? 'css' : type;

	parts.push('public/assets');
	parts.push(theme.path.dir.replace(basedir, '').substr(1));
	parts.push(type);
	if (exclude.indexOf(dirname) === -1) parts.push(dirname);

	return parts.join('/');
}

/**
 * Add asset file to mix
 * @param theme
 * @param file
 * @param mix
 */
function add_asset(theme, file, mix) {
	file = path.parse(file);
	file.path = path.join(file.dir, file.base);
	file.mimeType = mime.getType(path.join(file.dir, file.base));
	file.target = set_target_path(file, theme);
	let type = get_file_type(file.mimeType);

	if (_.get(theme.assets, type).indexOf(file) === -1) {
		let collection = _.get(theme.assets, type);
		collection.push(file);
		_.set(theme.assets, type, collection);

		switch (type) {
			case "css":
				mix.css(file.path, file.target);
				break;
			case "sass":
				mix.sass(file.path, file.target);
				break;
			case "js":
				mix.js(file.path, file.target)
				   .extract([], path.join('public/assets/themes', theme.name, 'js/vendor'))
				   .mergeManifest();
				break;
			case "images":
			case "fonts":
				mix.copy(file.path, file.target);
				break;
		}
	}
}

/**
 * Watch the directory
 *
 * @param location
 * @param type
 * @param theme
 * @param mix
 */
function watch(location, type, theme, mix) {
	if (fs.existsSync(location)) {
		fs.watch(location, (eventType, filename) => {
			// could be either 'rename' or 'change'. new file event and delete
			// also generally emit 'rename'
			let fullpath = path.join(location, filename);

			if (eventType === 'rename') {
				if (fs.existsSync(fullpath)) {
					let stat = fs.statSync(fullpath);
					let mimeType = mime.getType(fullpath);

					if (stat.isDirectory()) watch(location, type, theme, mix);
					if (stat.isFile()) {
						console.info('[+][' + type.padEnd(5, ' ') + ']', 'Add:', fullpath.replace(RESOURCE_PATH, ''));
						add_asset(theme, fullpath, mix);
					}
				}
				else {
					console.info('[+][' + type.padEnd(5, ' ') + ']', 'Deleted:', fullpath.replace(RESOURCE_PATH, ''));
				}
			}

			if (eventType === 'change') {

			}
		});
	}
}

/**
 * Watch all theme asset files and dynamically added to mix
 *
 * @param theme
 * @param mix
 */
function watchThemeAssets(theme, mix) {
	['css', 'img', 'js', 'sass', 'fonts'].forEach(function (dir) {
		let assetDir = path.join(theme.path.dir, 'assets', dir);
		watch(assetDir, dir, theme, mix);
	});
}

/**
 * Walk through theme folder assets
 * @param mix
 */
function add_theme_assets(mix) {
	let themes = get_themes(mix);

	themes.forEach(function (theme) {
		theme.assets = {
			css: [],
			sass: [],
			js: [],
			images: [],
			fonts: []
		};

		walk(path.join(theme.path.dir, 'assets'), (file) => add_asset(theme, file, mix));
		// watchThemeAssets(theme, mix);
		//let js = theme.assets.js.map(function (v) {
		//	return v.path
		//});
		//mix.js(js, path.join('public/assets/themes', theme.name, 'js'))
		//   .extract(theme.extract, path.join('public/assets/themes', theme.name, 'js/vendor'))
		//   .mergeManifest();
	});
}

function add_global_assets(mix) {
	mix
		.copy(get_asset_path('js/fontawesome.js'), 'public/js')
		.copy(get_module_path('mapbox-gl/dist/mapbox-gl.js'), 'public/js')
	;
}