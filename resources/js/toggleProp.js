/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   toggleProp.js
 * @date   2020-10-29 5:31:15
 */

$.fn.isDisabled = function() {
	return this.is(':disabled');
};

$.fn.isChecked = function() {
	return this.is(':checked');
};

$.fn.toggleDisabled = function() {
	if (arguments.length) {
		if (arguments[0]) this.removeAttr('disabled');
		else this.prop('disabled', 'disabled');
	}
	else {
		if (this.isDisabled()) this.removeAttr('disabled');
		else this.prop('disabled', 'disabled');
	}

	return this;
};

$.fn.toggleCheckbox = function() {
	this.prop('checked', !this.isChecked());

	return this;
};

$.fn.checked = function(value) {
	if (this.attr('type') === 'checkbox' || this.attr('type') === 'radio')
		this.prop('checked', value);

	return this;
};
