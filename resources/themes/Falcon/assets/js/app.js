/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   app.js
 * @date   2020-10-30 5:40:29
 */

import _ from 'lodash';
import { createAction, fancytabSetPosition, getCookie, setCookie, setNavbarTopWidth } from '../../.includes/js/utils';
import tinymce from 'tinymce';


require('../../.includes/js/bootstrap');
require('../../../../js/toggleProp');

let shadowEnabled = true;

function navbarShadow(b) {
	let $h = $('.navbar:not(.navbar-vertical)');
	//console.log(0 < b.scrollTop() && a);
	0 < $('.os-viewport').scrollTop() ? $h.addClass('navbar-glass-shadow') : $h.removeClass('navbar-glass-shadow');
}

function autoDisable($owner) {
	$.each($owner.data('target').split(','), function (i, value) {
		let $target = $("[name=" + value + "]");
		$target.toggleDisabled($owner.is(':checked'));
	});
}

window._toast = function(messge, title, variant = 'warning', icon = 'exclamation-triangle') {
	let $t = $(
		'<div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000" style="width: 350px; max-width: 500px">' +
		'<div class="toast-header bg-' + variant + ' text-white d-flex align-items-center">' +
		'<i class="fad fa-' + icon + ' mr-2"></i>' +
		'<strong class="mr-auto">' + title + '</strong>' +
		//'<small>'+time+'</small>' +
		'<button type="button" class="ml-2 close fs-0 text-black" data-dismiss="toast" aria-label="Close">' +
		'<i class="fal fa-times" aria-hidden="true"></i>' +
		'</button>' +
		'</div>' +
		'<div class="toast-body">' +
		messge +
		'</div>' +
		'</div>').appendTo('.toast-container');
	$t.on('hidden.bs.toast', function () {
		$(this).remove();
	});

	return {
		$el: $t,
		show: function () {
			$t.toast('show');
		},
		hide: function () {
			$t.toast('hide');
		}
	};
}

function loadSuccessBsTable(data) {
	if (data.sender instanceof $.BootstrapTable) {
		if (_.has(data.sender.data[0], 'actions')) {
			if (_.get(data.sender.data[0], 'actions').length) {
				data.sender.bootstrapTable('hideColumn', 'actions');
			}
		}
	}
}

function postBodyBsTable(data) {
	let scrollbarOptions = {
		scrollbars: {
			autoHide: 'leave',
			autoHideDelay: 450
		}
	};

	// $('.table-toolbar-button').tooltip({ boundary: 'window' });
	//if (data.sender.data.length > 0) {
	//$('.fixed-table-body').addClass('wrapper scrollbar-dynamic').scrollbar();
	data.sender.$tableBody.addClass('wrapper scrollbar-dynamic pb-2 bg-white').overlayScrollbars(scrollbarOptions);
	$('.bootstrap-table .fixed-table-toolbar .columns .dropdown-menu').overlayScrollbars(scrollbarOptions);
	$('.bootstrap-table .fixed-table-container .table td a[href*=mailto]').each(function () {
		$(this)
			.data('toggle', 'tooltip')
			.data('title', 'Mail Me')
			.tooltip({ boundary: 'window' });
	});
	$('.bootstrap-table .fixed-table-container .table td [data-toggle=tooltip]').each(function () {
		$(this)
			.data('toggle', 'tooltip')
			.tooltip({ boundary: 'window' });
	});
}

$(function () {
	let scrollContainer = null;
	let $scrollbar = $('.scrollbar-dynamic');
	let $navbarScrollbar = null;
	let scrollbarOptions = {
		scrollbars: {
			autoHide: 'leave',
			autoHideDelay: 450
		}
	};

	tinymce.init({
		selector: '.tinymce',
		plugins: 'advlist anchor autolink autoresize autosave charmap code codesample colorpicker contextmenu directionality emoticons fullscreen help hr image imagetools importcss insertdatetime legacyoutput link lists media nonbreaking noneditable pagebreak paste preview print quickbars save searchreplace spellchecker tabfocus table template textcolor textpattern toc visualblocks visualchars wordcount',
		menubar: 'file edit view insert format tools table tc help',
		toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment | code',
		quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
		noneditable_noneditable_class: 'mceNonEditable',
		toolbar_mode: 'sliding'
	});

	$('.custom-switch input.custom-control-input').on('change', function () {
		$(this).val($(this).is(':checked') ? 1 : 0);
	});
	$('.main .wrapper').overlayScrollbars(scrollbarOptions);
	$('.navbar-collapse').overlayScrollbars({
		...scrollbarOptions,
		overflowBehavior: {
			x: 'hidden'
		}
	});

	$('a[href*=mailto]').each(function () {
		$(this)
			.data('toggle', 'tooltip')
			.data('title', 'Mail Me')
			.tooltip({ boundary: 'window' });
	});

	toastr.options = {
		"closeButton": true,
		"debug": false,
		"newestOnTop": true,
		"progressBar": false,
		"positionClass": "toast-top-right",
		//"preventDuplicates": true,
		"onclick": null,
		"showDuration": "300",
		"hideDuration": "1000",
		"timeOut": "5000",
		"extendedTimeOut": "1000",
		"showEasing": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut"
	};

	let boostrapTableIcons = {
		paginationSwitchDown: 'fa-chevron-down',
		paginationSwitchUp: 'fa-chevron-up',
		refresh: 'fa-sync',
		toggleOff: 'fa-toggle-off',
		toggleOn: 'fa-toggle-on',
		columns: 'fa-th-list',
		detailOpen: 'fa-plus',
		detailClose: 'fa-minus',
		fullscreen: 'fa-arrows-alt',
		search: 'fa-search',
		clearSearch: 'fa-trash'
	};

	$('.bootstrap-table')
		.bootstrapTable({
			classes: "table table-hover bg-white",
			toggle: "table",
			locale: "en",
			buttonsClass: "falcon-default",
			cache: false,
			contentType: "application/json",
			dataType: "json",
			//search: true,
			loadingFontSize: "1rem",
			showRefresh: true,
			showToggle: false,
			showColumns: true,
			showColumnsSearch: true,
			showColumnsToggleAll: true,
			pagination: true,
			paginationLoop: true,
			showExtendedPagination: true,
			showPaginationSwitch: true,
			paginationPreText: '<i class="fa fa-chevron-left"></i>',
			paginationNextText: '<i class="fa fa-chevron-right"></i>',
			icons: boostrapTableIcons,
			pageList: "[10, 25, 50, 100, 200, All]",
			searchOnEnterKey: false,
			sidePagination: "server",
			//sortName: "name",
			//sortOrder: "asc",
			loadingTemplate: '<div class="row"><div class="col"><i class="fa fa-spinner fa-spin fa-fw fs-2 text-primary"></i></div></div>'
		})
		.on('load-success.bs.table', loadSuccessBsTable)
		.on('post-body.bs.table', postBodyBsTable);

	$('.bootstrap-table-custom')
		.bootstrapTable({
			classes: "table table-hover bg-white",
			toggle: "table",
			locale: "en",
			searchOnEnterKey: false,
			sidePagination: "server",
			buttonsClass: "falcon-default",
			pagination: true,
			paginationLoop: true,
			showExtendedPagination: true,
			showPaginationSwitch: true,
			icons: boostrapTableIcons,
			showToggle: false,
			showRefresh: true,
			paginationPreText: '<i class="fa fa-chevron-left"></i>',
			paginationNextText: '<i class="fa fa-chevron-right"></i>',
			pageList: "[10, 25, 50, 100, 200, All]",
			loadingTemplate: '<div class="row"><div class="col"><i class="fa fa-spinner fa-spin fa-fw fs-2 text-primary"></i></div></div>'
		})
		.on('load-success.bs.table', loadSuccessBsTable)
		.on('post-body.bs.table', postBodyBsTable);

	$('.bootstrap-table-custom-local')
		.bootstrapTable({
			classes: "table table-hover bg-white",
			toggle: "table",
			locale: "en",
			searchOnEnterKey: false,
			buttonsClass: "falcon-default",
			pagination: true,
			paginationLoop: true,
			showExtendedPagination: true,
			showPaginationSwitch: false,
			icons: boostrapTableIcons,
			showToggle: false,
			showRefresh: false,
			paginationPreText: '<i class="fa fa-chevron-left"></i>',
			paginationNextText: '<i class="fa fa-chevron-right"></i>',
			pageList: "[10, 25, 50, 100, 200, All]",
			loadingTemplate: '<div class="row"><div class="col"><i class="fa fa-spinner fa-spin fa-fw fs-2 text-primary"></i></div></div>'
		})
		.on('load-success.bs.table', loadSuccessBsTable)
		.on('post-body.bs.table', postBodyBsTable);

	$('.bootstrap-table-free')
		.bootstrapTable({
			classes: "table table-hover bg-white",
			toggle: "table",
			locale: "en",
			buttonsClass: "falcon-default",
			paginationPreText: '<i class="fa fa-chevron-left"></i>',
			paginationNextText: '<i class="fa fa-chevron-right"></i>',
			pageList: "[10, 25, 50, 100, 200, All]",
			loadingTemplate: '<div class="row"><div class="col"><i class="fa fa-spinner fa-spin fa-fw fs-2 text-primary"></i></div></div>'
		})
		.on('load-success.bs.table', loadSuccessBsTable)
		.on('post-body.bs.table', postBodyBsTable);

	window.actionsFormatter = (value, row, index, field) => {
		return createAction(row.actions);
	};

	window.previewImage = function (event) {
		if (event.target.files.length > 0) {
			let targetPreview = $(event.target).data('target');
			let output = document.getElementById(targetPreview);

			$(output).fadeOut(function () {
				let $self = $(this), t;
				t = setTimeout(function () {
					$(event.target).parent().find('label').html(event.target.files[0].name);
					output.src = URL.createObjectURL(event.target.files[0]);
					output.onload = function () {
						URL.revokeObjectURL(output.src); // free memory
						$self.fadeIn(function () {
							clearTimeout(t);
						});
					};
				}, 450);
			});
		}
	};

	window.simpleBrowseFilename = function (event) {
		if (event.target.files.length > 0) {
			let targetPreview = $(event.target).data('target');
			let output = document.getElementById(targetPreview);

			$(output).html(event.target.files[0].name);
		}
	};

	$('[data-toggle=disabled]').each(function () {
		let $self = $(this);

		$self.on('change', function () {
			autoDisable($(this));
		});

		autoDisable($self);
	});

	/*
	 $scrollbar.each(function () {
	 let $this = $(this);

	 });

	 $scrollbar.scrollbar({
	 onUpdate: function (x) {
	 let $owner = x.parent().parent();
	 let $parent = x.parent();
	 // let $scrollx = $parent.children('.scroll-x');
	 let $scrolly = $parent.children('.scroll-y');

	 if ($owner.hasClass('main')) {
	 $scrolly.css({
	 bottom: $scrolly.offset().top + 'px',
	 height: (x.height() - $scrolly.offset().top) + 'px',
	 minHeight: (x.height() - $scrolly.offset().top) + 'px'
	 });
	 }
	 },
	 onScroll: function (options) {
	 // console.log('jQuery Scrollbar: onScroll', options);
	 if (this.container.parent().parent().hasClass('main')) {
	 shadowEnabled = true;
	 navbarShadow($.extend(options, {
	 scrollTop: function () {
	 return options.scroll;
	 }
	 }));
	 }
	 }
	 });
	 */

	let $navbarVerticalToggle = $('.navbar-vertical-toggle');
	let $window = $(window);
	let $navbarCollapse = $(".navbar-collapse");

	if (JSON.parse(localStorage.getItem('isNavbarVerticalCollapsed')))
		$('html').addClass('navbar-vertical-collapsed');

	$window.utils = {
		breakpoints: {
			lg: 992,
			md: 768,
			sm: 576,
			xl: 1200,
			xs: 0,
			xxl: 1400
		}
	};

	let b, classes = $('.navbar-vertical').attr('class');
	classes && (b = $window.utils.breakpoints[classes.split(" ").filter(function (b) {
		return 0 === b.indexOf('navbar-expand-');
	}).pop().split("-").pop()]);

	$('.os-viewport').on('scroll', function () {
		//if ($window.width() < b) {
		//shadowEnabled = true;
		navbarShadow($navbarCollapse);
		//}
	});

	$navbarCollapse.on('show.bs.collapse', function () {
		if ($window.width() < b) {
			shadowEnabled = false;
			navbarShadow($window);
		}
	});

	$navbarVerticalToggle.on("hidden.bs.collapse", function () {
		shadowEnabled = !($navbarVerticalToggle.hasClass('show') && $window.width() < b);
		navbarShadow($window);
	});

	$navbarCollapse.hover(function (x) {
		setTimeout(function () {
			if ($(x.currentTarget).is(':hover')) {
				$('.navbar-vertical-collapsed').addClass("navbar-vertical-collapsed-hover");
			}
		}, 100);
	}, function () {
		$('.navbar-vertical-collapsed').removeClass("navbar-vertical-collapsed-hover");
	});

	$navbarVerticalToggle.on('click', function (e) {
		let collapsed = JSON.parse(localStorage.getItem('isNavbarVerticalCollapsed'));

		localStorage.setItem("isNavbarVerticalCollapsed", !collapsed);
		$('html').toggleClass('navbar-vertical-collapsed');
		setNavbarTopWidth();
		$(e.currentTarget()).trigger('navbar.vertical.toggle');
	});

	$window.on('resize', function () {
		setNavbarTopWidth();
	});

	// Fancy Scroll
	$('[data-fancyscroll]').each(function () {
		$(this).animate({
			scrollTop: $($(this).data('target')).top - ($('.navbar-top').innerHeight() + 10)
		});
	});

	// Fancy Tab
	$(".fancy-tab").each(function () {
		let $fancy = $(this);
		let $navbar = $fancy.find(".nav-bar");
		let $navbarItem = $fancy.find(".nav-bar-item.active");

		$navbar.append("<div class='tab-indicator'></div>");

		let $tabIndicator = $navbar.find(".tab-indicator");
		let navbarItemIndex = $navbarItem.index();

		fancytabSetPosition($tabIndicator, $fancy, $navbarItem);

		$navbar.find(".nav-bar-item").on("click", function (b) {
			let $a = $(b.currentTarget),
				x = $a.index(),
				e = $fancy.find(".fancy-tab-contents").find(".fancy-tab-content").eq(x);

			$a.siblings().removeClass("active");
			$a.addClass("active");
			e.siblings().removeClass("active");
			e.addClass("active");

			fancytabSetPosition($tabIndicator, $fancy, $a);

			if (x - navbarItemIndex <= 0) {
				$tabIndicator.addClass("transition-reverse");
			}
			else {
				$tabIndicator.removeClass("transition-reverse");
			}

			navbarItemIndex = x;
		});

		$(window).on("resize", function () {
			fancytabSetPosition($tabIndicator, $fancy, $fancy.find(".nav-bar-item.active"));
		});

		let $tab = $('[role=tablist]');
		if ($tab.length) {
			$tab.on('shown.bs.tab', function () {
				fancytabSetPosition($tabIndicator, $fancy, $fancy.find(".nav-bar-item.active"));
			});
		}
	});

	$('.dropdown-on-hover').on('mouseenter mouseleave', function (e) {
		let closest = $(e.target).closest(".dropdown-on-hover"),
			t = $(".dropdown-menu", closest);
		setTimeout(function () {
			let b = e.type !== "click" && closest.is(":hover");
			t.toggleClass("show", b);
			closest.toggleClass("show", b);
			$('[data-toggle="dropdown"]', closest).attr("aria-expanded", b);
		}, "mouseleave" === e.type ? 100 : 0);
	});

	// Cookie notice
	let cookieNoticeOptions = {
		autoShow: true,
		autoShowDelay: false,
		showOnce: false,
		cookieExpireTime: ((60 * 60 * 24) * 30)
	};
	let $notice = $('.notice');

	$notice.each(function (b, x) {
		let e, t = $(x), a = $.extend(cookieNoticeOptions, t.data('options'));

		a.showOnce && (e = getCookie('cookieNotice'));
		a.autoShow && null === e && setTimeout(function () {
			return t.toast('show');
		}, a.autoShowDelay);
	});

	$notice.on('hidden.bs.toast', function (b) {
		let e = $.extend(cookieNoticeOptions, $(b.currentTarget).data('options'));
		e.showOnce && setCookie('cookieNotice', !1, e.cookieExpireTime);
	});

	$(document).on("click", "[data-toggle='notice']", function (b) {
		b.preventDefault();
		var x = $(b.currentTarget),
			e = $(x.attr('href'));

		e.hasClass('show') ? e.toast('hide') : e.toast('show');
	});

	$('input[type="color"]').on('change', function () {
		$(this).attr('value', $(this).val());
	});

	$('[data-toggle="popover"]').popover();
	$('[data-toggle="tooltip"]').tooltip({ boundary: 'window' });

	setNavbarTopWidth();

	$('.fancybox64').each(function () {
		let title = $(this).attr('title');
		let data = $(this).attr('href');

		if (data.length) {
			$(this).fancybox({
				'overlayShow': true,
				'href': data,
				'titlePosition': 'inside',
				'title': title
			});
		}
		else {
			$(this).on('click', function (e) {
				e.preventDefault();
				//toastr.warning('No image found.', 'Image Not Found');
				_toast('No image found.', 'Image Not Found', 'warning').show();
				return false;
			});
		}
	});
});
