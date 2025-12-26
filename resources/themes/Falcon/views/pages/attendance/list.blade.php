@extends('falcon::layouts.base')

@section('javascripts')
	@parent
	<script src="{{ themes('js/calendar-small.js') }}" defer></script>
@endsection

@section('content')
	<x-bootstrap::row class="small-gutters">
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|4" class="pr-0 pr-md-3 pb-3 pb-md-0">
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="row no-gutters align-items-center">
						<x-bootstrap::column breakpoint="AUTO" class="d-flex justify-content-end order-md-1">
							<button type="button" class="btn icon-item icon-item-sm icon-item-hover shadow-none p-0 mr-1" data-event="prev"><i class="fad fa-arrow-left"></i></button>
							<button type="button" class="btn icon-item icon-item-sm icon-item-hover shadow-none p-0 mr-1" data-event="next"><i class="fad fa-arrow-right"></i></button>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|auto;MEDIUM|auto" class="order-md-2">
							<h4 class="mb-0 fs-0 fs-sm-1 fs-lg-1 text-nunito font-weight-semi-bold calendar-title">{{ date('F Y') }}</h4>
						</x-bootstrap::column>
						<x-bootstrap::column class="col d-flex justify-content-center order-md-3 text-center"></x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|auto;MEDIUM|auto" class="d-flex justify-content-end order-md-4">
							<button class="btn btn-falcon-primary btn-sm mr-2" data-toggle="tooltip" title="Today" type="button" data-event="today"><i class="fad fa-calendar-day"></i></button>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="p-0 overflow-hidden">
					<div id="calendar"></div>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|8">
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="row no-gutters align-items-center">
						<x-bootstrap::column>
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0">
								Presence &mdash; <span id="presence-date">{{ date('l, d F Y') }}</span>
								<small class="fs-0 text-muted d-block">List of presence of employees</small>
							</h5>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL;MEDIUM|4" class="ml-auto text-right pl-0">
							<a href="{{ route('attendance.create') }}" role="button" id="attendance-create" class="btn btn-falcon-primary">
								<i class="fas fa-file-plus"></i>
								<span class="d-none d-sm-inline-block ml-1">New</span>
							</a>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light p-0 overflow-hidden">
					@include('themes::Falcon.views.layouts.table', [
						'method' => 'get',
						'hasActions' => true,
						'hasToolbar' => true,
						'data' => [
							// 'url' => route('api.attendance'),
							'page-size' => 25,
							'show-refresh' => 'true',
							'method' => 'get',
						],
						'columns' => [
							[
								'title' => 'Name',
								'attrs' => [
									'field' => 'employee.name',
									'class' => 'va-baseline text-nowrap',
									'sortable' => 'true',
								],
							],
							[
								'title' => 'Date',
								'attrs' => [
									'field' => 'at',
									'class' => 'va-baseline text-nowrap',
									'sortable' => 'true',
								],
							],
							[
								'title' => 'Start',
								'attrs' => [
									'field' => 'start',
									'class' => 'va-baseline text-nowrap',
									'sortable' => 'true',
								],
							],
							[
								'title' => 'End',
								'attrs' => [
									'field' => 'end',
									'class' => 'va-baseline text-nowrap',
									'sortable' => 'true',
								],
							],
							[
								'title' => 'Overtime',
								'attrs' => [
									'field' => 'overtime',
									'class' => 'va-baseline text-nowrap',
									'sortable' => 'true',
								],
							],
							[
								'title' => 'Status',
								'attrs' => [
									'field' => 'reason.name',
									'class' => 'va-baseline text-nowrap',
									'sortable' => 'true',
								],
							],
							[
								'title' => 'Attachment',
								'attrs' => [
									'field' => 'attactment',
									'class' => 'va-baseline text-nowrap',
									'sortable' => 'true',
								],
							],
						],
					])
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
	</x-bootstrap::row>
	<div class="modal theme-modal fade" id="eventDetails">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content border"></div>
		</div>
	</div>
@endsection