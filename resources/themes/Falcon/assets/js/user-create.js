/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   user-create.js
 * @date   2020-10-29 5:31:15
 */

require('main/toggleProp');

$(function() {
	$('[data-toggle=check]').each(function () {
		$(this).on('click', function () {
			let $parent = $($(this).data('parent'));

			if ($parent) {
				$parent.find('input[type=checkbox]').each(function () {
					$(this).toggleCheckbox();
				});
			}
		});
	});

	$('select#role').on("select2:select", function (e) {
		$.getJSON(route('api.permission.role', {role: $(this).val()}), function ($result) {
			$('input[id*=permissions]').checked(false);
			$($result).each(function (i,v) {
				$('[id*=permissions]').filter(function() { return $(this).attr('name') === "permissions["+v.name+"]"}).checked(true);
			});
		});
	});
});