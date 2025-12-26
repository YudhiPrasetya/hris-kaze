/**
 * This file is part of the Kaze project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   tracker.js
 * @date   2021-05-27 17:56:12
 */

import _ from "lodash";


let simpleAjax = (method, url, callback) => {
	var xhr = new XMLHttpRequest();
	xhr.withCredentials = true;
	xhr.open(method, url, true);
	xhr.onreadystatechange = function () {
		if (xhr.readyState == 4) {
			callback(JSON.parse(xhr.responseText));
		}
	};
	if (method == 'POST') {
		xhr.setRequestHeader('Content-type', 'application/json');
	}
	xhr.send();
};

$(function () {
	if ($('#map').length) {
		let trackerData = { devices: [] }, trackerLatest = [], trackerDevices = [], dragged = false, mousedown = false, first = true;
		let currentDevice = null;

		$('a.list-group-item-action.tracker-item').click(function (e) {
			e.preventDefault();

			$('a.list-group-item-action.tracker-item').removeClass('active');
			$(this).addClass('active');
			currentDevice = $(this).data('imei');
			geojson.features[0].geometry.coordinates = [];
			dragged = false;
			mousedown = false;
			first = true;

			let index = _.findIndex(trackerData.devices, function (o) {
				return o.uniqueId == currentDevice;
			});

			if (index === -1) {
				_toast('Not found.', 'Device not registered', 'danger').show();
			}

			return false;
		});

		mapboxgl.accessToken = 'pk.eyJ1IjoicmFtZGhhbmkxOSIsImEiOiJjaWd2M2IyMHowZ29odzdtNWZmMjVrajZ1In0.ivJTANe4hb5MKPOo_Z0cdg';
		var map = new mapboxgl.Map({
			container: 'map',
			style: 'mapbox://styles/mapbox/streets-v11',
			// center: [106.77816, -6.538147222222222],
			// longitude, latitude
			zoom: 3, // starting zoom
			antialias: true
		});

		// Add zoom and rotation controls to the map.
		map.addControl(new mapboxgl.NavigationControl());
		map.addControl(new mapboxgl.AttributionControl({
			compact: true
		}));
		map.addControl(new mapboxgl.FullscreenControl());
		let geolocate = new mapboxgl.GeolocateControl({
			positionOptions: {
				enableHighAccuracy: true
			},
			trackUserLocation: true
		});

		map.addControl(geolocate);
		var scale = new mapboxgl.ScaleControl({
			maxWidth: 80,
			unit: 'imperial'
		});
		map.addControl(scale);

		scale.setUnit('metric');

		var geojson = {
			'type': 'FeatureCollection',
			'features': [
				{
					'type': 'Feature',
					'geometry': {
						'type': 'LineString',
						'coordinates': []
					}
				}
			]
		};
		// A single point that animates along the route.
		// Coordinates are initially set to origin.
		var point = {
			'type': 'FeatureCollection',
			'features': [
				{
					'type': 'Feature',
					'properties': {},
					'geometry': {
						'type': 'Point',
						'coordinates': []
					}
				}
			]
		};

		// A single point that animates along the route.
		// Coordinates are initially set to origin.

		var index = 0;
		var animation; // to store and cancel the animation
		var fnDragg = function () {
			dragged = true;
		};
		var fnMouseDown = function () {
			mousedown = true;
		};

		map.on('drag', fnDragg);
		map.on('dragend', fnDragg);
		map.on('dragstart', fnDragg);
		map.on('touchmove', fnDragg);
		map.on('mousedown', fnMouseDown);
		map.on('touchstart', fnMouseDown);
		map.on('wheel', () => {
			fnDragg();
			fnMouseDown();
		});
		map.on('rotate', () => {
			fnDragg();
			fnMouseDown();
		});
		map.on('pitch', () => {
			fnDragg();
			fnMouseDown();
		});
		map.on('zoom', () => {
			fnDragg();
			fnMouseDown();
		});

		// animated in a circle as a sine wave along the map.
		function animateLine(timestamp) {
			let index = _.findIndex(trackerData.devices, function (o) {
				return o.uniqueId == currentDevice;
			});

			if (index >= 0) {
				let device = trackerData.devices[index];

				if (device.positions.length == 0) {
					if (first) {
						_toast('Location not found.', 'Not Found.', 'warning').show();
						first = false;
					}
				}
				else {
					let position = device.positions.slice(-1)[0];
					let coord = [position.longitude, position.latitude];

					if (!dragged && !mousedown) {
						// Center the map on the coordinates.
						map.flyTo({
							center: coord,
							zoom: 16,
							essential: true,
							speed: 3, // make the flying slow
							curve: 1 // change the speed at which it zooms out
						});
					}

					geojson.features[0].geometry.coordinates = [];
					_.each(device.positions, pos => {
						geojson.features[0].geometry.coordinates.push([pos.longitude, pos.latitude]);
					});

					// Calculate the distance in kilometers between route start/end point.
					var lineDistance = turf.length(geojson.features[0]);

					// Number of steps to use in the arc and animation, more steps means
					// a smoother arc and animation, but too many steps will result in a
					// low frame rate
					var steps = 500;

					// Draw an arc between the `origin` & `destination` of the two points
					for (var i = 0; i < lineDistance; i += lineDistance / steps) {
						var segment = turf.along(geojson.features[0], i);
						geojson.features[0].geometry.coordinates.push(segment.geometry.coordinates);
					}

					if (geojson.features[0].geometry.coordinates.length > 0) {
						// append new coordinates to the lineString
						// geojson.features[0].geometry.coordinates.push(coord);

						// then update the map
						// map.getSource('line').setData(geojson);

						var parts = geojson.features[0].geometry.coordinates.slice(-2);

						// Update point geometry to a new position based on counter denoting
						// the index to access the arc
						point.features[0].geometry.coordinates = geojson.features[0].geometry.coordinates.slice(-1)[0];

						if (parts.length > 0) {
							var start = parts[0];
							var end = parts.length === 1 ? start : parts[1];

							if (start || end) {
								// Calculate the bearing to ensure the icon is rotated to match the route arc
								// The bearing is calculated between the current point and the next point, except
								// at the end of the arc, which uses the previous point and the current point
								point.features[0].properties.bearing = turf.bearing(
									turf.point(start),
									turf.point(end)
								);
							}
						}
					}

					// Update the source with this new data
					map.getSource('point').setData(point);
				}
			}

			requestAnimationFrame(animateLine);
		}

		map.on('load', function () {
			map.loadImage("/assets/themes/Falcon/images/marker.png", function (error, image) {
				if (error) throw error;

				// Add the image to the map style.
				map.addImage('yellow-car', image);

				// geolocate.trigger();
				// map.addSource('line', {
				// 	'type': 'geojson',
				// 	'data': geojson
				// });

				map.addSource('point', {
					'type': 'geojson',
					'data': point
				});

				map.addLayer({
					'id': 'point',
					'source': 'point',
					'type': 'symbol',
					'layout': {
						// This icon is a part of the Mapbox Streets style.
						// To view all images available in a Mapbox style, open
						// the style in Mapbox Studio and click the "Images" tab.
						// To add a new image to the style at runtime see
						// https://docs.mapbox.com/mapbox-gl-js/example/add-image/
						'icon-image': 'yellow-car',
						'icon-size': 0.07,
						'icon-rotate': ['get', 'bearing'],
						'icon-rotation-alignment': 'map',
						'icon-allow-overlap': true,
						'icon-ignore-placement': true
					}
				});

				// add the line which will be modified in the animation
				// map.addLayer({
				// 	'id': 'line-animation',
				// 	'type': 'line',
				// 	'source': 'line',
				// 	'paint': {
				// 		'line-color': '#ED6498',
				// 		'line-width': 2,
				// 		'line-opacity': 0.8
				// 	}
				// });

				animateLine();
			});
		});

		// Please don't use axios it will send x-xsrf-token that not allowed on traccar server
		simpleAjax('GET', 'http://omnity.hopto.org:60083/api/session?token=4uUwjxMcE1ndxRsy6XpIzMnCkZyDjDzy', user => {
			console.log(user);
			// get list of devices
			simpleAjax('GET', 'http://omnity.hopto.org:60083/api/devices', devices => {
				trackerData.devices = _.map(devices, device => {
					return { ...device, positions: [] };
				});

				_.each(trackerData.devices, device => {
					if (device.status === 'online') {
						$('small#tracker-' + device.uniqueId).css('background-color', '#00D27A');
					}
					else {
						$('small#tracker-' + device.uniqueId).css('background-color', '#E63757');
					}
				});

				// get latest positions of all devices
				simpleAjax('GET', 'http://omnity.hopto.org:60083/api/positions', positions => {
					_.each(positions, pos => {
						let index = _.findIndex(trackerData.devices, device => {
							return device.id === pos.deviceId;
						});
						trackerData.devices[index].positions.push(pos);
					});

					const socket = new WebSocket('ws://omnity.hopto.org:60083/api/socket');
					socket.addEventListener('open', (event) => {
						//  console.log(event);
						console.info("socket open.");
					});

					socket.addEventListener('close', (event) => {
						//  console.log(event);
						console.info("socket close");
					});

					socket.addEventListener('error', (event) => {
						console.error("socket error");
					});

					socket.addEventListener('message', (event) => {
						var data = JSON.parse(event.data);

						if (_.has(data, 'devices')) {
							_.each(data.devices, device => {
								var exists = false;

								if (device.status === 'online') {
									$('small#tracker-' + device.uniqueId).css('background-color', '#00D27A');
								}
								else {
									$('small#tracker-' + device.uniqueId).css('background-color', '#E63757');
								}

								let dev = { ...device, positions: [] };

								_.each(trackerData.devices, (trackerDevice, idx) => {
									if (trackerDevice.uniqueId == dev.uniqueId) {
										exists = true;
										dev.positions = trackerData.devices[idx].positions;
										trackerData.devices[idx] = dev;
									}
								});

								if (!exists) {
									trackerData.devices.push(dev);
								}
							});
						}

						if (_.has(data, 'positions')) {
							_.each(data.positions, pos => {
								let index = _.findIndex(trackerData.devices, device => {
									return device.id === pos.deviceId;
								});

								if (index >= 0) {
									trackerData.devices[index].positions.push(pos);
								}

								trackerData.devices[index].positions = trackerData.devices[index].positions.slice(-2);
							});
						}
					});
				});
			});
		});
	}
});
