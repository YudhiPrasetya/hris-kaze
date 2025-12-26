/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   utils.js
 * @date   2020-10-29 5:31:15
 */

import Chart from 'chart.js';


export const MONTHS = [
	'January',
	'February',
	'March',
	'April',
	'May',
	'June',
	'July',
	'August',
	'September',
	'October',
	'November',
	'December'
];

export function setCookie(b, x, e) {
	var t = new Date();
	t.setTime(t.getTime() + e);
	document.cookie = b + "=" + x + ";expires=" + t.toUTCString();
}

export function getCookie(b) {
	let x = document.cookie.match("(^|;) ?" + b + "=([^;]*)(;|$)");
	return x ? x[2] : x;
}

export function fancytabSetPosition($tabIndicator, $fancy, $navbarItem) {
	let t = $navbarItem.position().left,
		a = $fancy.find(".nav-bar").outerWidth() - (t + $navbarItem.outerWidth());

	$tabIndicator.css({
		left: t,
		//right: a
		width: $navbarItem.outerWidth()
	});
}

export function setNavbarTopWidth() {
	let b = $('.main .content').width() + 30;
	//$('.navbar-top').outerWidth(b);
}

export function chart(b, x) {
	var e = b.getContext("2d");
	return new Chart(e, x);
}

export function isScrolledIntoView(b) {
	let x = $(b),
		e = $(window).height(),
		t = x.offset().top,
		a = x.height(),
		o = $(window).scrollTop();
	return t <= o + e && o <= t + a;
}

export function createAction(value, row, index, field) {
	let act = [];

	$.each(value, (i, item) => {
		if (item) {
			let { icon, attr, type, text, tooltip } = item;
			let $element = $("<" + type + " />");

			$.each(attr, (key, value) => $element.attr(key, value));
			if (icon) $element.append('<i class="' + icon + '"></i>');
			if (text) $element.append('<span class="ml-2">' + text + '</span>');
			if (tooltip) {
				$element.attr('data-toggle', 'tooltip');
				$element.attr('title', tooltip);
			}
			$element.addClass('pointer');
			$element.addClass('ml-1');
			$element.addClass('table-toolbar-button');
			if (item.content) {
				$element = $('<div class="d-inline">' + $element[0].outerHTML + item.content + '</div>');
			}

			act.push($element[0].outerHTML);
		}
	});

	//console.log(actions);

	return act.join('');
}

export function clearSelect2($select) {
	$select.val(null).trigger("change");
}

export function select2InitialData($owner, url, callback, key = 'name') {
	if ($owner.data('value')) {
		$.ajax({
			type: 'GET',
			url: url,
			cache: false,
			success: function (data, textStatus, jqXHR) {
				$.each(data.results, function (i, v) {
					// create the option and append to Select2
					let option = new Option(v[key], v.id, v.id == $owner.data('value'), v.id == $owner.data('value'));
					$owner.append(option);
				});

				$owner.trigger('change');

				// manually trigger the `select2:select` event
				$owner.trigger({
					type: 'select2:select',
					params: {
						data: data.results
					}
				});

				callback && callback($owner.data('value'));
			}
		});
	}
}

export function getImage(url, $target, key = 'image') {
	$.ajax({
		type: 'GET',
		url: url,
		cache: false,
		success: function (data, textStatus, jqXHR) {
			$target.fadeOut(function () {
				$target.attr('src', data[key]);
				$target.fadeIn();
			});
		}
	});
}

export function getData(url, callback) {
	$.ajax({
		type: 'GET',
		url: url,
		cache: false,
		success: callback
	});
}

export function isValueEmpty(val) {
	return val == 0 || val == null;
}

export function initDateTimeComponent(type) {
	let icons = {
		time: 'fa fa-clock',
		date: 'fa fa-calendar',
		up: 'fa fa-chevron-up',
		down: 'fa fa-chevron-down',
		previous: 'fa fa-chevron-left',
		next: 'fa fa-chevron-right',
		today: 'fa fa-desktop',
		clear: 'fa fa-trash-o',
		close: 'fa fa-times'
	};

	switch (type) {
		case "date":
			$(".datepicker").each(function () {
				let $self = $(this);
				$(this).datetimepicker({
					format: "Y-MM-DD",
					allowInputToggle: true,
					sideBySide: false,
					showTodayButton: true,
					keepOpen: true,
					icons: icons
				});
			});
			break;

		case "datetime":
			$(".datetimepicker").each(function () {
				let $self = $(this);
				$(this).datetimepicker({
					format: "Y-MM-D hh:mm",
					allowInputToggle: true,
					sideBySide: false,
					showTodayButton: true,
					keepOpen: true,
					icons: icons
				});
			});
			break;

		case "time":
			$(".timepicker").each(function () {
				let $self = $(this);
				$(this).datetimepicker({
					format: 'HH:mm',
					allowInputToggle: true,
					keepOpen: true,
					icons: icons
				});
			});
			break;
	}
}

export function formatBytes(bytes, decimals = 2) {
	if (bytes == 0) return '0 Bytes';

	var k = 1024,
		dm = decimals || 2,
		sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
		i = Math.floor(Math.log(bytes) / Math.log(k));
	
	return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

export function simpleAjax(method, url, callback) {
	var xhr = new XMLHttpRequest();
	xhr.withCredentials = true;
	xhr.open(method, url, true);
	xhr.onreadystatechange = function () {
		if (xhr.readyState == 4) {
			callback(JSON.parse(xhr.responseText));
		}
	};
	if (method == 'POST') {
		xhr.setRequestHeader('Content-type', 'application/json');
	}
	xhr.send();
}

export function jsonSync(method, url, headers = null, crossDomain = false, data = null, success = () => {}, error = () => {}) {
	let result = {
		error: null,
		result: null
	};

	$.ajax({
		type: method,
		url: url,
		data: data && JSON.stringify(data),
		dataType: 'json',
		headers: headers,
		async: false, // this is by default false, so not need to mention
		crossDomain: crossDomain, // tell the browser to allow cross domain calls.
		success: (res) => {
			result.result = res;
			success && success(res);
		},
		error: (err) => {
			result.error = err;
			error && error(err);
		}
	});

	return result;
}


export function jsonAsync(method, url, headers = null, crossDomain = false, data = null, success = () => {}, error = () => {}) {
	$.ajax({
		type: method,
		url: url,
		data: data && JSON.stringify(data),
		dataType: 'json',
		headers: headers,
		async: true, // this is by default false, so not need to mention
		crossDomain: crossDomain, // tell the browser to allow cross domain calls.
		success: success,
		error: error
	});
}