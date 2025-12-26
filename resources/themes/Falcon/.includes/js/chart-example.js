/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   chart-example.js
 * @date   2020-10-29 5:31:15
 */

import { chart, isScrolledIntoView } from './utils';

// Chart.js example
let e, t = window.utils,
	x = [
		3,
		1,
		4,
		1,
		5,
		9,
		2,
		6,
		5,
		3,
		5,
		8,
		9,
		7,
		9,
		3,
		2,
		3,
		8,
		4,
		6,
		2,
		6,
		4,
		3,
		3,
		8,
		3,
		2,
		7,
		9,
		5,
		0,
		2,
		8,
		8,
		4,
		1,
		9,
		7
	], a = [
		"9:00 AM",
		"10:00 AM",
		"11:00 AM",
		"12:00 PM",
		"1:00 PM",
		"2:00 PM",
		"3:00 PM",
		"4:00 PM",
		"5:00 PM",
		"6:00 PM",
		"7:00 PM",
		"8:00 PM"
	], o = document.getElementById("chart-payments");
if (o) {
	e = chart(o, {
		type: "line",
		data: {
			labels: a.map(function (b) {
				return b.substring(0, b.length - 3);
			}),
			datasets: [
				{
					borderWidth: 2,
					data: x.map(function (b) {
						return (3.14 * b).toFixed(2);
					}),
					borderColor: "rgba(255, 255, 255, 0.8)",
					backgroundColor: function () {
						let b = o.getContext("2d");
						if (true) {
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
			legend: {
				display: !1
			},
			tooltips: {
				mode: "x-axis",
				xPadding: 20,
				yPadding: 10,
				displayColors: !1,
				callbacks: {
					label: function (b) {
						return a[b.index] + " - " + b.yLabel + " USD";
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
							show: !0,
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
						display: !1
					}
				]
			}
		}
	});
}

$("#dashboard-chart-select").on("change", function (b) {
	var x = {
		credit_debit_sale: [4, 1, 6, 2, 7, 12, 4, 6, 5, 4, 5, 10].map(function (b) {
			return (3.14 * b).toFixed(2);
		}),
		credit_debit_refund: [3, 1, 4, 1, 5, 9, 2, 6, 5, 3, 5, 8].map(function (b) {
			return (3.14 * b).toFixed(2);
		}),
		credit_debit_void_sale: [1, 0, 2, 1, 2, 1, 1, 0, 0, 1, 0, 2].map(function (b) {
			return (3.14 * b).toFixed(2);
		})
	};
	e.data.datasets[0].data = x[b.target.value];
	e.update();
});

let d, u, f = document.getElementById("real-time-user");
if (f) {
	d = chart(f, {
		type: "bar",
		data: {
			labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25],
			datasets: [
				{
					label: 'Users',
					backgroundColor: "rgba(255,255,255, 0.3)",
					data: [
						183,
						163,
						176,
						172,
						166,
						161,
						164,
						159,
						172,
						173,
						184,
						163,
						99,
						173,
						183,
						167,
						160,
						183,
						163,
						176,
						172,
						166,
						173,
						188,
						175
					],
					barPercentage: .9,
					categoryPercentage: 1
				}
			]
		},
		options: {
			legend: {
				display: !1
			},
			scales: {
				yAxes: [
					{
						display: !1,
						stacked: !0
					}
				],
				xAxes: [
					{
						stacked: !1,
						ticks: {
							display: !1
						},
						gridLines: {
							color: "rgba(255,255,255, 0.1)",
							display: !1
						}
					}
				]
			}
		}
	});

	u = $(".real-time-user");
	setInterval(function () {
		var x = Math.floor(60 * Math.random() + 60);
		d.data.datasets.forEach(function (b) {
			b.data.shift();
		});
		d.update();
		setTimeout(function () {
			d.data.datasets.forEach(function (b) {
				b.data.push(x);
			});
			d.update();
			u.text(x);
		}, 500);
	}, 2e3);

	var c_up = $("[data-countup]");
	c_up.length && c_up.each(function (b, x) {
		function e() {
			return isScrolledIntoView(x) && !o && (o || ($({
				countNum: 0
			}).animate({
				countNum: a.count
			}, {
				duration: a.duration || 1e3,
				easing: "linear",
				step: function () {
					t.text((a.prefix ? a.prefix : "") + Math.floor(this.countNum));
				},
				complete: function () {
					var b;
					switch (a.format) {
						case "comma":
							t.text((a.prefix ? a.prefix : "") +
							       this.countNum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
							break;
						case "space":
							t.text((a.prefix ? a.prefix : "") +
							       this.countNum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "));
							break;
						case "alphanumeric":
							t.text((a.prefix ? a.prefix : "") +
							       ((b = this.countNum) < 1e6 ? (b / 1e3).toFixed(2) + "k" :
							        b < 1e9 ? (b / 1e6).toFixed(2) + "m" :
							        b < 1e12 ? (b / 1e9).toFixed(2) + "b" : (b / 1e12).toFixed(2) + "t"));
							break;
						default:
							t.text((a.prefix ? a.prefix : "") + this.countNum);
					}
				}
			}), o = !0)),
				o;
		}

		let t = $(x),
			a = t.data("countup"),
			o = !1;
		e();
		$(window).on('scroll', function () {
			e();
		});
	});
}