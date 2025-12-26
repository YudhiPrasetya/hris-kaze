/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   report.js
 * @date   2020-10-29 5:31:15
 */

import { isValueEmpty, select2InitialData } from "../../.includes/js/utils";


/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   report.js
 * @date   2020-10-25 23:3:2
 */

import moment from 'moment';

function processQuery(params) {
	// Query parameters will be ?search=[term]&type=public
	return {
		search: params.term
	};
}

function processResults(data) {
	data.results = $.map(data.results, function (obj) {
		obj.text = (obj.text || obj.mid) || obj.name; // replace name with the property used for the text
		obj.id = obj._id || obj.id;
		return obj;
	});

	return data;
}

let defaultAjaxOptions = {
	dataType: 'json',
	data: processQuery,
	processResults: processResults
};

function formattedDate(date) {
	return date.format('YYYY-MM-DD');
}

$(function () {
	$('select#merchant').select2({
		allowClear: true,
		ajax: {
			url: route('api.merchant.select2'),
			...defaultAjaxOptions
		}
	});

	$('select#payment_type').on('select2:select', function () {
		if ($(this).val() == 'cdcp') {
			$('[class*=cdcp_]').removeClass('d-none');
			$('[class*=qr_]').addClass('d-none');
		}

		if ($(this).val() == 'qr') {
			$('[class*=cdcp_]').addClass('d-none');
			$('[class*=qr_]').removeClass('d-none');
		}
	});

	select2InitialData($('select#merchant'), route('api.merchant.select2'));

	let $from = $('.date[name=from_date]');
	let $to = $('.date[name=to_date]');
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

	$from.datetimepicker({
		format: "Y-MM-DD",
		allowInputToggle: true,
		sideBySide: false,
		showTodayButton: true,
		keepOpen: true,
		icons: icons,
		focusOnShow: true
	});
	$to.datetimepicker({
		format: "Y-MM-DD",
		allowInputToggle: true,
		sideBySide: false,
		showTodayButton: true,
		keepOpen: true,
		icons: icons,
		focusOnShow: true,
		useCurrent: false //Important! See issue #1075
	});

	// To download or search transaction we limited only for 7 days from selected date
	// so here's the magic begin
	// Initialize min and max date
	let from = $from.val() ? moment($from.val()) : moment().subtract(7, 'days');
	let to = $to.val() ? moment($to.val()) : moment();

	$from.data("DateTimePicker").maxDate(formattedDate(moment()));
	$to.data("DateTimePicker").minDate(formattedDate(from));
	$to.data("DateTimePicker").maxDate(formattedDate($to.val() ? moment(from).add(7, 'days') : moment()));

	$from.val(formattedDate(from));
	$to.val(formattedDate(to));

	// trigger the changes
	$from.trigger('dp.change');
	$to.trigger('dp.change');

	// detect changes
	$from.on("dp.change", function (e) {
		try {
			let current = e.date;
			// set max date of end date to current date for preventing error
			$to.data("DateTimePicker").maxDate(formattedDate(moment()));
			// set min date of end date to selected date
			$to.data("DateTimePicker").minDate(formattedDate(e.date));
			// set max date
			let max = e.date.add(7, 'days');

			// check if the max from selected date is after current date
			if (max.isAfter(moment().toDate())) {
				// if the max is after the current date, set max date of end date to current date
				$to.data("DateTimePicker").maxDate(formattedDate(moment()));
				$to.val(formattedDate(moment()));
			}
			else {
				// ta da....
				$to.data("DateTimePicker").maxDate(formattedDate(max));
				// only change the end date if the current max date is after or before start date
				if (moment($to.val()).isAfter(max) || moment($to.val()).isBefore(current)) $to.val(formattedDate(max));
			}

			// trigger whatever the changes
			$to.trigger('dp.change');
		}
		catch (e) {
		}
	});
});