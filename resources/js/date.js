/**
 * This file is part of the Kaze project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   date.js
 * @date   2021-03-22 19:44:30
 */

$(function() {
	let $date = $('.date.datepicker');
	let $time = $('.time.timepicker');
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

	$date.datetimepicker({
		format: "Y-MM-DD",
		allowInputToggle: true,
		sideBySide: false,
		showTodayButton: true,
		keepOpen: true,
		icons: icons,
		focusOnShow: true
	});

	$time.datetimepicker({
		format: "HH:mm",
		icons: icons,
		showTodayButton: false
	});
});