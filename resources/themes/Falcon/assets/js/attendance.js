/**
 * This file is part of the Kaze project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   attendance.js
 * @date   2021-04-8 23:54:31
 */

import { isValueEmpty, select2InitialData } from "../../.includes/js/utils";


require('select2');

function processQuery(params) {
	// Query parameters will be ?search=[term]&type=public
	return {
		search: params.term
	};
}

function processResults(data) {
	data.results = $.map(data.results, function (obj) {
		obj.text = obj.text || obj.name; // replace name with the property used for the text
		return obj;
	});

	return data;
}

let defaultSelectOptions = {
	allowClear: true,
	tags: true
};

let defaultAjaxOptions = {
	dataType: 'json',
	data: processQuery,
	processResults: processResults
};

$(function () {
	let $annualLeave = $('#annual_leave_id');
	let $reason = $('#attendance_reason_id');
	let $employee = $('#employee_id');
	let $start = $('#start_at');
	let $end = $('#end_at');

	$employee.select2({
		allowClear: true,
		ajax: {
			url: function () {
				return route('api.employee.attendance.available', {start: $start.val(), end: $end.val()})
			},
			...defaultAjaxOptions
		}
	});

	if ($employee.data('value')) {
		select2InitialData($employee, route('api.employee.attendance.available', {start: $start.val(), end: $end.val()}));
		$employee.prop("disabled", true);
	}

	$annualLeave.select2({
		allowClear: true,
		ajax: {
			url: function () {
				let val = $('#employee_id').val();
				isValueEmpty(val) && toastr.warning("Please select employee first!");
				return isValueEmpty(val) ? '' : route('api.annual.employee', { employee: val });
			},
			...defaultAjaxOptions
		}
	});

	if (($employee.data('value') || $employee.val()) && $reason.val() === '5') {
		select2InitialData($annualLeave, route('api.annual.employee', { employee: $employee.val() }));
	}
	else {
		$annualLeave.prop("disabled", true);
	}

	$reason.on('change', function () {
		$annualLeave.prop("disabled", $(this).val() !== '5');
	});
});