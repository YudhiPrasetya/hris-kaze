/**
 * This file is part of the Kaze project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   calendar-small.js
 * @date   2021-04-9 8:8:6
 */

import { Calendar } from '@fullcalendar/core';

import bootstrapPlugin from '@fullcalendar/bootstrap';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
import momentPlugin from '@fullcalendar/moment';
import scrollGridPlugin from '@fullcalendar/scrollgrid';
import timeGridPlugin from '@fullcalendar/timegrid';


let $loading = $(
	'<div class="row row no-gutters align-items-center">' +
	'<div class="col d-flex justify-content-center">' +
	'<div class="fa-3x">' +
	'<i class="fas fa-spinner fa-pulse"></i>' +
	'</div>' +
	'</div>' +
	'</div>'
);

function x(e) {
	return $(".calendar-title").text(e);
}

function getStackIcon(e, t) {
	return '<span class="fa-stack ml-n1 mr-3">' +
	       '<i class="fas fa-circle fa-stack-2x text-200"></i>' +
	       '<i class="' + e + ' fa-stack-1x text-primary" data-fa-transform=' + t + '></i>' +
	       '</span>';
}

function modalHeader(title, props) {
	return '<div class="modal-header px-card bg-light border-0 flex-between-center">' +
	       '<div>' +
	       '<h5 class="mb-0">' + title + '</h5>' +
	       // (props.organizer ? '<p class="mb-0 fs--1 mt-1">by <a href="#!">' + props.organizer + '</a></p>' : '') +
	       '</div>' +
	       '<button class="close fs-0 px-card" data-dismiss="modal" aria-label="Close"><span class="fas fa-times"></span></button>' +
	       '</div>';
}

function modalBody(e, props) {
	console.log(moment(e.event.start).format("dddd, MMMM D, YYYY"));
	let description = "",
		datetime = "",
		location = "",
		schedules = "",
		body = '<div class="modal-body px-card pb-card pt-1 fs--1">';

	if (props.description) {
		description = '<div class="media mt-3">' +
		              getStackIcon("fas fa-align-left") +
		              '<div class="media-body">' +
		              '<h6>Description</h6>' +
		              '<p class="mb-0">' + props.description.split(" ").slice(0, 30).join(" ") + '</p>' +
		              '</div>' +
		              '</div>';
	}

	datetime = '<div class="media mt-3">' +
	           getStackIcon("fas fa-calendar-check") +
	           '<div class="media-body">' +
	           '<h6>Date and Time</h6>' +
	           '<p class="mb-1">' +
	           moment(e.event.start).format("dddd, MMMM D, YYYY") +
	           (e.event.end ? "– <br/>" + (window.dayjs && window.dayjs(e.event.end).subtract(1, "day").format("dddd, MMMM D, YYYY, h:mm A")) : '') +
	           '</p>' +
	           '</div>' +
	           '</div>';

	if (props.location) {
		location = '<div class="media mt-3">' +
		           getStackIcon("fas fa-map-marker-alt") +
		           '<div class="media-body">' +
		           '<h6>Location</h6>' +
		           '<div class="mb-1">' + e.event.extendedProps.location + '</div>' +
		           '</div>' +
		           '</div>';
	}

	if (props.schedules) {
		schedules = '<div class="media mt-3">' +
		            getStackIcon("fas fa-clock") +
		            '<div class="media-body">' +
		            '<h6>Schedule</h6>' +
		            '<ul class="list-unstyled timeline mb-0">' +
		            e.event.extendedProps.schedules.map(function (e) {
			            return '<li>' + e.title + '</li>';
		            }).join("") +
		            '</ul>' +
		            '</div>' +
		            '</div>';
	}

	return '<div class="modal-body px-card pb-card pt-1 fs--1">' +
	       datetime +
	       description +
	       location +
	       schedules +
	       '</div>';
}

function modalFooter(e) {
	return '<div class="modal-footer d-flex justify-content-end bg-light px-card border-top-0">' +
	       '<a href="' + route('calendar.edit', { calendar: e.event.id }) +
	       '" class="btn btn-falcon-primary btn-sm"><span class="fas fa-pencil-alt fs--2 mr-2"></span> Edit</a>' +
	       // '<a href="pages/event-detail.html" class="btn btn-falcon-primary btn-sm"> See more details <span class="fas fa-angle-right fs--2
	       // ml-1"></span></a>' +
	       '</div>';
}

function getTemplate(e) {
	return modalHeader(e.event.title, e.event.extendedProps.organizer) +
	       modalBody(e, e.event.extendedProps) +
	       modalFooter(e);
}

function getAttendanceOnDate($table, dateStr) {
	$table.bootstrapTable('refreshOptions', {
		url: route('api.attendance', { date: moment(dateStr).format('YYYY-MM-DD') })
	});
}

$(function () {
	let lastDate = moment(new Date()).format('YYYY-MM-DD');
	let $attendanceList = $('.bootstrap-table-custom');
	let $modal = $("#addEvent");
	let calendarEl = document.getElementById('calendar');
	let calendar = new Calendar(calendarEl, {
		schedulerLicenseKey: '1234567890-fcs-1863216000000',
		plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin, bootstrapPlugin, momentPlugin, scrollGridPlugin],
		initialView: 'dayGridMonth',
		themeSystem: 'bootstrap',
		selectable: true,
		fixedWeekCount: false,
		dayHeaderFormat: {
			weekday: 'short'
		},
		validRange: function(nowDate) {
			return {
				end: nowDate
			};
		},
		aspectRatio: 1.0,
		customButtons: {
			addEvent: {
				text: 'Add Event',
				icon: 'fad fa-calendar-plus',
				click: function () {
					alert('clicked the custom button!');
				}
			}
		},
		buttonIcons: {
			prev: 'fad fa-chevron-left',
			next: 'fad fa-chevron-right'
		},
		buttonText: {
			today: 'Today',
			month: 'Month',
			week: 'Week',
			day: 'Day',
			list: 'List'
		},
		headerToolbar: false,
		footerToolbar: false,
		eventTimeFormat: {
			hour: "numeric",
			minute: "2-digit",
			omitZeroMinute: true,
			meridiem: true
		},
		eventSources: [
			{
				url: route('api.calendar.events.national'),
				method: 'GET',
				failure: function () {
					alert('there was an error while fetching events!');
				}
			}
		],
		eventClick: function (e) {
			if (e.event.url) {
				window.open(e.event.url, "_blank");
				e.jsEvent.preventDefault();
			}
			else {
				e = getTemplate(e);
				$("#eventDetails .modal-content").html(e);
				$("#eventDetails").modal("show");
			}
		},
		dateClick: function (e) {
			let d = moment(new Date()).format('YYYY-MM-DD');
			let activeDate = moment(e.dateStr);

			if (!moment(lastDate).isSame(e.dateStr)) {
				$('#presence-date').html(activeDate.format('dddd, MMMM D YYYY'));
				getAttendanceOnDate($attendanceList, e.dateStr);
			}

			lastDate = e.dateStr;
			$('#attendance-create').attr('href', route('attendance.create', {date: activeDate.format('YYYY-MM-DD')}));
		}
	});

	$modal.on('show.bs.modal', function () {
		let $content = $(this).find('.modal-content');
		$content.html($loading);
		$.get(route('calendar.create'), function (rsp) {
		});
	});
	calendar.render();
	getAttendanceOnDate($attendanceList, moment().format('YYYY-MM-DD'));

	$(".navbar-vertical-toggle").on("navbar.vertical.toggle", function () {
		return calendar.updateSize();
	});

	$(document).on("click", "[data-fc-view]", function (e) {
		e.preventDefault();

		let a = $(e.currentTarget);

		a.parent().find(".active").removeClass("active");
		a.addClass("active");
		$("[data-view-title]").text(a.text());
		calendar.changeView(a.data("fc-view"));
		x(calendar.currentData.viewTitle);
	});

	$(document).on("click", "[data-event]", function (e) {
		switch ($(e.currentTarget).data("event")) {
			case "prev":
				calendar.prev();
				x(calendar.currentData.viewTitle);
				break;

			case "next":
				calendar.next();
				x(calendar.currentData.viewTitle);
				break;

			case "today":
			default:
				calendar.today();
				x(calendar.currentData.viewTitle);
				getAttendanceOnDate($attendanceList, moment().format('YYYY-MM-DD'));
				break;
		}
	});
});