/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   default-select.js
 * @date   2020-10-29 5:31:15
 */

// require('select2');

$(function () {
	$('select.select2, select.custom-select').each(function () {
		let $parent = $(this);
		let value = $(this).data('value');
		let options = {
			allowClear: true,
			tags: true
		};

		if (!value) {
			$parent.children().each(function () {
				if ($(this).attr('selected') === "selected") {
					value = $(this).val();
					$parent.data('value', value);
				}
			});
		}

		$(this).select2(options);
		$(this).val(value).trigger('change');
	});
});