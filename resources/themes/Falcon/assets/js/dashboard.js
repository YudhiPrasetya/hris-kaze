/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   dashboard.js
 * @date   2020-10-30 5:40:29
 */

import { chart, MONTHS } from 'Falcon/utils';

$(function () {
	let $chart = document.getElementById("chart-payments");
	if ($chart) {
		let $chartSelection = $("#dashboard-chart-select");
		let _chart = chart($chart, {
			type: "line",
			data: {
				//labels: MONTHS,
				datasets: [
					{
						borderWidth: 2,
						//data: x.map(function (b) {
						//	return (3.14 * b).toFixed(2);
						//}),
						borderColor: "rgba(255, 255, 255, 0.8)",
						backgroundColor: function () {
							let b = $chart.getContext("2d");
							if (false) {
								let x = b.createLinearGradient(0, 0, 0, b.canvas.height);
								return x.addColorStop(0, "rgba(44,123,229, 0.5)"),
									x.addColorStop(1, "transparent"),
									x;
							}
							else {
								let e = b.createLinearGradient(0, 0, 0, 250);
								return e.addColorStop(0, "rgba(255, 255, 255, 0.3)"),
									e.addColorStop(1, "rgba(255, 255, 255, 0)"),
									e;
							}
						}()
					}
				]
			},
			options: {
				responsive: true,
				maintainAspectRatio: true,
				legend: {
					display: false
				},
				tooltips: {
					mode: "x-axis",
					xPadding: 20,
					yPadding: 10,
					displayColors: false,
					callbacks: {
						label: function (b) {
							return MONTHS[b.index];
						},
						title: function () {
							return null;
						}
					}
				},
				hover: {
					mode: "label"
				},
				scales: {
					xAxes: [
						{
							scaleLabel: {
								show: false,
								labelString: "Month"
							},
							ticks: {
								fontColor: "rgba(255,255,255, 0.7)",
								fontStyle: 600
							},
							gridLines: {
								color: "rgba(255,255,255, 0.1)",
								zeroLineColor: "rgba(255,255,255, 0.1)",
								lineWidth: 1
							}
						}
					],
					yAxes: [
						{
							display: false
						}
					]
				}
			}
		});

		/*
		 $chartSelection.on("change", function (b) {
		 $.getJSON(route('api.chart.payment.monthly'), function (result) {
		 let selected = result.data[b.target.value]
		 let payment = b.target.value;

		 if (result.data[payment + '_today'].length) {
		 $('.today-payment').html('Today Rp. ' + result.data[payment + '_today'][0].amount);
		 }
		 else {
		 $('.today-payment').html('Today Rp. 0');
		 }

		 if (result.data[payment + '_yesterday'].length) {
		 $('.yesterday-payment').html('Yesterday Rp. <span class="opacity-50">' + result.data[payment + '_yesterday'][0].amount + '</span>');
		 }
		 else {
		 $('.yesterday-payment').html('Yesterday Rp. <span class="opacity-50">0</span>');
		 }

		 _chart.data.labels = selected.map(function (v) {
		 return v.name;
		 });
		 _chart.options.tooltips.callbacks = {
		 label: function (i) {
		 let val = selected[i.index];
		 return "Total Transactions: " + val.total + ", " + "Total Amount: Rp. " + (val.amount || 0);
		 }
		 };
		 _chart.data.datasets[0].data = selected.map(function (v) {
		 return v.total;
		 });
		 _chart.update();
		 });
		 });

		 $chartSelection.trigger('change');
		 */
	}
});