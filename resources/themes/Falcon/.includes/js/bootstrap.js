/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   bootstrap.js
 * @date   2020-10-29 5:31:15
 */

// window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
	// window.Popper = require('popper.js').default;
	// window.$ = window.jQuery = require('jquery');
	// window.toastr = require('toastr');
	window.Ziggy = require('../../../../js/ziggy').Ziggy;
	window.route = function (route, params) {
		let r = require('../../../../../vendor/tightenco/ziggy/dist');
		// let ziggy = require('../../../../js/ziggy').Ziggy;
		return r(route, params, undefined, window.Ziggy);
	};

	//  require('select2/dist/js/select2.full');

	require('bootstrap');
	require('bootstrap-table/dist/bootstrap-table');
	require('bootstrap-table/dist/bootstrap-table-locale-all');
	//require('./scrollbar');
	require('../../../../js/OverlayScrollbars');
	require('../../../../js/affix');
	// require('@fancyapps/fancybox');
	require('../../../../js/datetimepicker');
	// require('../../../../js/bootstrap-datetimepicker');
	// require('../../../../js/sticky-kit');
	require('select2');
	require('bootbox');
}
catch (e) {
}

// window.TurboLinks = require('turbolinks');
// window.TurboLinks.start();

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

//window.axios = require('axios');

//window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });