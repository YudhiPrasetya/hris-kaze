/**
 * This file is part of the Kaze project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   assignment.js
 * @date   2021-03-27 4:38:7
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

let totalTechnicians = 0;
let totalParts = 0;

function removeRow() {
	let $parent = $(this).parent().parent();
	let $tbody = $parent.parent();

	$parent.remove();
	$tbody.children().each(function () {
		let index = $(this).index();

		$(this).children(':nth-child(1)').html(index + 1);
		$(this).children().each(function () {
			let $el = $(this).children(':first-child');
			let name = $el.attr('name');
			if (name !== undefined) {
				$el.attr('name', name.replace(/\d+/g, index));
			}
		});
	});
}

$(function () {

	let $machine = $('#customer_machine_id');
	let $customer = $('#customer_id');

	$machine.select2({
		allowClear: true,
		ajax: {
			url: function () {
				let val = $customer.val();
				isValueEmpty(val) && toastr.warning("Please select customer first!");
				return isValueEmpty(val) ? '' : route('api.customer.machine.select', { customer: val });
			},
			...defaultAjaxOptions
		}
	});

	if ($customer.val()) {
		select2InitialData($machine, route('api.customer.machine.select', { customer: $customer.val() }));
	}

	$('button.add-technician').on('click', function (e) {
		e.preventDefault();

		let $table = $('table.table-technicians tbody');
		let count = totalTechnicians; // $table.children('tr:not(.no-records-found)').length;
		let $container = $('.technician.collection-container');
		let $proto = $($container.data('prototype').replace(/__NAME__/g, count));

		let $row = $('<tr>');
		let $btnRemove = $(
			'<button role="button" type="button" class="btn btn-falcon-danger text-danger remove-' + count + '"><i class="fad fa-trash"></i></button>');
		let $elNo = $('<td class="text-center">').html(count + 1);
		let $selectTechnician = $proto.find('select.technician');
		let $elTechnician = $('<td>').append($selectTechnician);
		let $start = $('<input type="time" class="form-control" name="technicians['+count+'][start_job]" size="8">');
		let $finish = $('<input type="time" class="form-control" name="technicians['+count+'][finish_job]" size="8">');
		let $travel = $('<input type="time" class="form-control" name="technicians['+count+'][travel_time]" size="8">');
		let $overtime = $('<input type="time" class="form-control" name="technicians['+count+'][overtime]" size="8">');
		let $elStart = $('<td>').append($start);
		let $elFinish = $('<td>').append($finish);
		let $elTravel = $('<td>').append($travel);
		let $elOvertime = $('<td>').append($overtime);
		let $elAction = $('<td class="text-center">').append($btnRemove);

		$selectTechnician.select2({
			...defaultSelectOptions,
			ajax: {
				url: route('api.employee.select'),
				...defaultAjaxOptions
			}
		});

		$table.append(
			$row.append($elNo)
			    .append($elTechnician)
			    .append($elStart)
			    .append($elFinish)
			    .append($elTravel)
			    .append($elOvertime)
			    .append($elAction)
		);

		$btnRemove.on('click', removeRow);
		totalTechnicians++;
	});

	$('button.add-part').on('click', function (e) {
		e.preventDefault();

		let $table = $('table.table-parts tbody');
		let count = totalParts;
		let $container = $('.technician.collection-container');
		let $proto = $($container.data('prototype').replace(/__NAME__/g, count));

		let $row = $('<tr>');
		let $btnRemove = $(
			'<button role="button" type="button" class="btn btn-falcon-danger text-danger remove-' + count + '"><i class="fad fa-trash"></i></button>');
		let $elNo = $('<td class="text-center">').html(count + 1);
		let $elName = $('<td>').append('<input type="text" name="parts['+count+'][part_name]" class="form-control">');
		let $elType = $('<td>').append('<input type="text" name="parts['+count+'][part_type]" class="form-control">');
		let $elQty = $('<td>').append('<input type="text" name="parts['+count+'][qty]" class="form-control text-center" minlength="1" maxlength="3" value="0">');
		let $elUnit = $('<td>').append('<input type="text" name="parts['+count+'][unit]" class="form-control">');
		let $elAction = $('<td class="text-center">').append($btnRemove);

		$table.append(
			$row.append($elNo)
			    .append($elName)
			    .append($elType)
			    .append($elQty)
			    .append($elUnit)
			    .append($elAction)
		);

		$btnRemove.on('click', removeRow);
		totalParts++;
	});
});