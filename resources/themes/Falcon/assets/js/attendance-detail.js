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
	window.rowStyle = function (row, index) {
		if (row.event || row.weekend) {
			return {
				css: {
					'background-color': row.event ? '#fce9ec' : '#e8f1fc',
					'color': row.event ? '#E63757' : '#2C7BE5',
				}
			};
		}

		return {};
	};
});