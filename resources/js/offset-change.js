/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   offset-change.js
 * @date   2020-10-29 5:31:15
 */

$.fn.onPositionChanged = function (callback, millis) {
	if (millis == null) millis = 100;
	let o = $(this[0]); // our jquery object
	if (o.length < 1) return o;

	let lastPos = null;
	let lastOff = null;

	setInterval(function () {
		if (o == null || o.length < 1) return o; // abort if element is non existend eny more
		if (lastPos == null) lastPos = o.position();
		if (lastOff == null) lastOff = o.offset();

		let newPos = o.position();
		let newOff = o.offset();

		if (lastPos.top != newPos.top || lastPos.left != newPos.left) {
			$(this).trigger('onPositionChanged', { lastPos: lastPos, newPos: newPos });
			if (typeof (callback) == "function") callback(lastPos, newPos);
			lastPos = o.position();
		}

		if (lastOff.top != newOff.top || lastOff.left != newOff.left) {
			$(this).trigger('onOffsetChanged', { lastOff: lastOff, newOff: newOff });
			if (typeof (callback) == "function") callback(lastOff, newOff);
			lastOff = o.offset();
		}
	}, millis);

	return o;
};