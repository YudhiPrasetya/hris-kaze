/**
 * This file is part of the Kaze project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   locations.js
 * @date   2021-03-22 23:45:17
 */

import { select2InitialData, isValueEmpty } from './utils';

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

export function autoFetchLocations(country, state, city, district, village) {
	let $country = $(country);
	let $state = $(state);
	let $city = $(city);
	let $district = $(district);
	let $village = $(village);

	$country.select2({ allowClear: true });
	$state.select2({
		allowClear: true,
		ajax: {
			url: function () {
				let val = $country.val();
				isValueEmpty(val) && toastr.warning("Please select country first!");
				return isValueEmpty(val) ? '' :
				       '/api/v1/states/%COUNTRY.json'.replace('%COUNTRY', !isValueEmpty(val) ? val : 'ID');
			},
			...defaultAjaxOptions
		}
	});

	$city.select2({
		...defaultSelectOptions,
		ajax: {
			url: function () {
				let val = $state.val();
				isValueEmpty(val) && toastr.warning("Please select state first!");
				return isValueEmpty(val) ? '' :
				       '/api/v1/cities/%STATE.json'.replace('%STATE', !isValueEmpty(val) ? val : '1620');
			},
			...defaultAjaxOptions
		}
	});
	$district.select2({
		...defaultSelectOptions,
		ajax: {
			url: function () {
				let val = $city.val();
				isValueEmpty(val) && toastr.warning("Please select city first!");
				return isValueEmpty(val) ? '' :
				       '/api/v1/districts/%CITY.json'.replace('%CITY', !isValueEmpty(val) ? val : '143160');
			},
			...defaultAjaxOptions
		}
	});
	$village.select2({
		...defaultSelectOptions,
		ajax: {
			url: function () {
				let val = $district.val();
				isValueEmpty(val) && toastr.warning("Please select district first!");
				return isValueEmpty(val) ? '' :
				       '/api/v1/villages/%DISTRICT.json'.replace('%DISTRICT', !isValueEmpty(val) ? val : '1959');
			},
			...defaultAjaxOptions
		}
	});

	select2InitialData($state, '/api/v1/states/' + $country.val() + '.json', function (state_id) {
		select2InitialData($city, '/api/v1/cities/' + state_id + '.json', function (city_id) {
			select2InitialData($district, '/api/v1/districts/' + city_id + '.json', function (district_id) {
				select2InitialData($village, '/api/v1/villages/' + district_id + '.json');
			});
		});
	});
}