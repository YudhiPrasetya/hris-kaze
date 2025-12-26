/**
 * This file is part of the Kaze project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   attendance-detail.js
 * @date   2021-08-10 11:52:26
 */

$(function () {
	const $date = $('p#cutoff_date');
	$('select#cutoff').on('change', function () {
		if ($(this).val() === 'user_defined') {
			$date.removeClass('d-none');
		}
		else {
			$date.addClass('d-none');
		}
	});
});