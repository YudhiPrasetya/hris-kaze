/**
 * This file is part of the Kaze project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   calendar.js
 * @date   2021-03-28 15:15:23
 */

import _ from 'lodash';
import moment from 'moment';
import { Calendar } from '@fullcalendar/core';
import bootstrapPlugin from '@fullcalendar/bootstrap';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
import momentPlugin from '@fullcalendar/moment';
import scrollGridPlugin from '@fullcalendar/scrollgrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import flatpickr from "flatpickr";
import { formatBytes } from "../../.includes/js/utils";


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
	$("span#holiday-title").text("Holidays in " + e);
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
		recurring = "",
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

	recurring = '<div class="media mt-3">' +
	            getStackIcon("fas fa-check") +
	            '<div class="media-body">' +
	            '<h6>Recurring</h6>' +
	            '<div class="mb-1">' + (props.recurring == 1 ? 'Yes' : 'No') + '</div>' +
	            '</div>' +
	            '</div>';

	return '<div class="modal-body px-card pb-card pt-1 fs--1">' +
	       datetime +
	       description +
	       location +
	       recurring +
	       schedules +
	       '</div>';
}

function modalFooter(e) {
	return '<div class="modal-footer d-flex justify-content-end bg-light px-card border-top-0">' +
	       '<a href="'+route('calendar.edit', {calendar: e.event.id})+'" class="btn btn-falcon-primary btn-sm"><span class="fas fa-pencil-alt fs--2 mr-2"></span> Edit</a>' +
	       // '<a href="pages/event-detail.html" class="btn btn-falcon-primary btn-sm"> See more details <span class="fas fa-angle-right fs--2 ml-1"></span></a>' +
	      '</div>'
}

function getTemplate(e) {
	return modalHeader(e.event.title, e.event.extendedProps.organizer) +
	       modalBody(e, e.event.extendedProps) +
	       modalFooter(e);
}

$(function () {
	let $modal = $("#addEvent");
	let currentDate = null;
	let calendarEl = document.getElementById('calendar');
	let calendar = new Calendar(calendarEl, {
		schedulerLicenseKey: '1234567890-fcs-1863216000000',
		plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin, bootstrapPlugin, momentPlugin, scrollGridPlugin],
		initialView: 'dayGridMonth',
		themeSystem: 'bootstrap',
		selectable: true,
		fixedWeekCount: false,
		dayHeaderFormat: {
			weekday: 'long'
		},
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
				failure: function() {
					// alert('there was an error while fetching events!');
				},
				success: function (result, xmlhttpRequest) {
					let $eventList = $('div#event-list');
					$eventList.html('');

					if (result.length == 0) {
						$eventList.html('<div class="row"><div class="col d-flex justify-content-center align-items-center"><h5 class="mb-0 fs-1 text-nunito font-weight-light list-group-item text-500 notification-flush bg-100 border-0">No holidays this month</h5></div></div>');
						return;
					}

					let total = 0;
					let currentMonth = currentDate.end.getMonth() - 1;
					currentMonth = currentMonth < 0 ? 11 : currentMonth;

					_.each(result, function (item) {
						let date = new Date(item.start);
						let c = new Date();

						if (date.getMonth() == currentMonth) {
							// @formatter:off
							let $template = $('<a class="list-group-item notification notification-flush bg-100 d-flex justify-content-start align-items-start border-t-0 border-x-0" href="'+route('calendar.edit', {calendar: item.id })+'">' +
							                  '<div class="row w-100">' +
								                  '<div class="col-1 pr-0">' +
										              '<div class="notification-avatar">' +
											              '<div class="avatar avatar-xl mr-3 rounded-circle border bg-300 d-flex align-items-center justify-content-center">' +
											                  '<h6 class="mb-0 text-nunito font-weight-semi-bold">'+date.getDate()+'</h6>' +
											              '</div>' +
								                      '</div>' +
								                  '</div>' +
								                  '<div class="col-10 pr-0">' +
										              '<div class="notification-body">' +
										                  '<p class="mb-1 font-weight-bold">'+item.title+'</p>' +
										                  '<div>'+(item.description ? item.description : '')+'</div>' +
									                  '</div>' +
								                  '</div>' +
									              '<div class="col pr-0">' +
							                          '<button class="btn-falcon-danger btn btn-sm ml-1" type="button" onclick="event.preventDefault(); document.getElementById(\'delete-form-'+item.id+'\').submit();">' +
										                  '<i class="fad fa-trash-alt"></i>' +
										              '</button>' +
										              '<form id="delete-form-'+item.id+'" action="'+route('calendar.destroy', {'calendar': item.id})+'" method="POST" style="display: none;">' +
										                  '<input type="hidden" name="_method" value="DELETE">' +
										                  '<input type="hidden" name="_token" value="'+item.token+'">' +
										              '</form>' +
									              '</div>' +
							                  '</div>' +
							                  '</a>');
							$eventList.append($template);
							total++;
							// @formatter:on
						}
					});

					if (total == 0) {
						$eventList.html('<div class="row"><div class="col d-flex justify-content-center align-items-center"><h5 class="mb-0 fs-1 text-nunito font-weight-light list-group-item text-500 notification-flush bg-100 border-0">No holidays this month</h5></div></div>');
					}
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
			console.log(e);
		},
		dateClick: function (e) {
			// $modal.modal("show");
			//$("#addEvent [name='startDate']").flatpickr.setDate([e.dateStr]);
		},
		datesSet: function (e) {
			currentDate = e;
			console.log(e)
		}
	});

	$modal.on('show.bs.modal', function () {
		let $content = $(this).find('.modal-content');
		$content.html($loading);
		$.get(route('calendar.create'), function (rsp) {
			console.log(rsp)
		});
	});
	calendar.render();

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
				break;
		}
	});

	// @formatter:off
	/*
	function x(e) {
		return $(c[CALENDAR_TITLE]).text(e);
	}

	var l, s = _0x17bd19, c = {
		ADD_EVENT_FORM: "#addEventForm",
		ADD_EVENT_MODAL: "#addEvent",
		ACTIVE: ".active",
		CALENDAR: "appCalendar",
		CALENDAR_TITLE: ".calendar-title",
		NAVBAR_VERTICAL_TOGGLE: ".navbar-vertical-toggle",
		EVENT_DETAILS_MODAL: "#eventDetails",
		EVENT_DETAILS_MODAL_CONTENT: "#eventDetails .modal-content",
		DATA_EVENT: "[data-event]",
		DATA_CALENDAR_VIEW: "[data-fc-view]",
		DATA_VIEW_TITLE: "[data-view-title]",
		INPUT_TITLE: '[name="title"]'
	}, e = {
		CLICK: "click",
		NAVBAR_VERTICAL_TOGGLE: "navbar.vertical.toggle",
		SHOWN_BS_MODAL: "shown.bs.modal",
		SUBMIT: "submit"
	}, a = {
		EVENT: "event"
	}, n = {
		ACTIVE: "active"
	}, t = events.reduce(function (e, t) {
		var a = s;
		return t.schedules ? e[concat](t[schedules][concat](t)) : e[concat](t);
	}, []), o = document.getElementById(c[CALENDAR]);
	o && (l = renderCalendar(o, {
		headerToolbar: !1,
		dayMaxEvents: 2,
		height: 800,
		stickyHeaderDates: !1,
		views: {
			week: {
				eventLimit: 3
			}
		},
		eventTimeFormat: {
			hour: "numeric",
			minute: "2-digit",
			omitZeroMinute: !0,
			meridiem: !0
		},
		events: t,
		eventClick: function (e) {
			var t = s;
			e[event][url] ? (window[open](e[event].url, _blank),
				e.jsEvent[preventDefault]()) : (e = getTemplate(e),
				$(c[EVENT_DETAILS_MODAL_CONTENT])[html](e),
				$(c.EVENT_DETAILS_MODAL)[modal](show));
		},
		dateClick: function (e) {
			var t = s;
			$(c.ADD_EVENT_MODAL)[modal]("show"),
				document[querySelector]("#addEvent [name='startDate']")._flatpickr.setDate([e.dateStr]);
		}
	}),
		x(calendar.currentData.viewTitle),
		$(document).on("click", "[data-event]", function (e) {
			var t = s
				, e = e.currentTarget();
			switch ($(e).data("event")) {
				case "prev":
					calendar.prev(),
						x(calendar.currentData.viewTitle);
					break;
				case "next":
					calendar.next(),
						x(calendar.currentData.viewTitle);
					break;
				case "today":
				default:
					calendar.today(),
						x(calendar.currentData.viewTitle);
			}
		}),
		$(document).on("click", "[data-fc-view]", function (e) {
			var t = s;
			e.preventDefault();
			var a = $(e.currentTarget())
				, e = a.text();
			a.parent().find(".active").removeClass("active"),
				a.addClass("active"),
				$("[data-view-title]").text(e),
				calendar.changeView(a.data("fc-view")),
				x(calendar.currentData.viewTitle);
		}),
		document.querySelector("#addEventForm").addEventListener("submit", function (e) {
			var t = s;
			e.preventDefault();
			var a = e.target
				, x = a.title
				, n = a.startDate
				, o = a.endDate
				, r = a.label
				, i = a.description
				, a = a.allDay;
			calendar.addEvent({
				title: x.value,
				start: n.value,
				end: ovalue ? o.value : null,
				allDay: a.checked,
				className: a.checked && r.value ? "bg-soft-" + r.value : "",
				description: i.value
			}),
				e.target.reset(),
				$("#addEvent").modal("hide");
		})),
		$("#addEvent").on("shown.bs.modal", function (e) {
			e.currentTarget.querySelector('[name="title"]').focus();
		});
	 */
	// @formatter:on
});