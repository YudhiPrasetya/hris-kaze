/**
 * This file is part of the Kaze project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   employee.js
 * @date   2021-03-22 23:40:25
 */

import _ from 'lodash';
import { autoFetchLocations } from '../../.includes/js/locations';
import { AttGateSvc } from '../../.includes/js/AttGateSvc';
import { jsonSync } from "../../.includes/js/utils";

require('./default-select');

$(function () {
	autoFetchLocations('#country_id', '#state_id', '#city_id', '#district_id', '#village_id');

	$('#currency_code')
		.select2({
			allowClear: true,
			tags: true,
			escapeMarkup: function (m) {
				if (m.indexOf(";") >= 0) {
					let part = m.split(';');
					return '<div class="row"><div class="col-2 text-primary">(' + part[0] + ')</div><div class="col">' + part[1] + '</div></div>';
				}

				return m;
			},
			templateResult: function (value) {
				let part = value.text.split(';');
				return '<div class="row white-on-hover"><div class="col-2 text-primary white-on-hover">(' + part[0] + ')</div><div class="col">' +
				       part[1] + '</div></div>';
			}
		})
		.on('select2:select', function (e) {
			let data = e.params.data;
			let part = data.text.split(';');

			$('span.basic_salary_currency_symbol,span.functional_allowance_currency_symbol,span.transport_allowance_currency_symbol,span.meal_allowances_currency_symbol')
				.html(part[0]);
		});

	$("button#submit-employee").bind('click', function (e) {
		if (document.location.pathname.indexOf('edit') >= 0) return true;
		e.preventDefault();

		//$.getJSON(route('api.settings.attendance'), function (res) {

			// let $dialogMessage = $('<div class="d-flex flex-center"><div class="fingerprint scanning"></div></div><div class="fingerprint-bg"></div>');
			// let name = $("input#name").val();
			// const $form = $('form.employee');
			// const $pin = $("input[name=pin]");
			// const $finger = $("input[name=finger]");
			// const $fingerSize = $("input[name=finger_size]");
			// const $fingerIndex = $("input[name=finger_index]");
			// const { error, result } = jsonSync('get', route('api.settings.attendance'), null, false);

			// if (error == null) {
			// 	let att = new AttGateSvc(result.service_ip, result.service_port, result.ip, result.port);

			// 	if ($pin.val().length && $finger.val().length && $fingerSize.val().length && $fingerIndex.val().length)
			// 		$form.submit();

			// 	let dialog = bootbox.dialog({
			// 		message: $dialogMessage,
			// 		closeButton: false,
			// 		onShown: (e) => {
			// 			let owner = $(e.target);

			// 			let token = att.auth("admin", "-@jY)53mL@,;/mgu");
			// 			let res = att.add_user(name);
			// 			if (_.has(res, 'error')) {
			// 				$("input[name=pin]").val(res.result.data.user.id);
			// 				$("input[name=finger]").val(res.result.data.user.fp[0]);
			// 				$("input[name=finger_size]").val(res.result.data.enrollment.finger_size);
			// 				$("input[name=finger_index]").val(res.result.data.enrollment.finger_index);
			// 				$form.submit();
			// 			}
			// 			else {
			// 				_toast('An error occurred when registering the user.', 'Register.', 'error').show();
			// 			}

			// 			owner.modal('hide');
			// 		}
			// 	})
			// }
			// else {
			// 	// TODO: display error
			// }

		//})
	});
});
