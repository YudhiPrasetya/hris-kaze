@extends('falcon::layouts.base')

@section('javascripts')
	@parent
	<script src="{{ themes('js/default-select.js') }}" defer></script>
	<script src="{{ asset('js/date.js') }}" defer></script>
@endsection

@section('content')
	@php
		$fields = collect($form->getFieldValues());
	@endphp
	{!! form_start($form, ['attr' => ['autocomplete' => "off"]]) !!}
	<x-bootstrap::row class="justify-content-center">
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|5">
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								Attendance Report
								<small class="fs-0 text-muted d-block">List of all attendance</small>
							</h5>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light">
					<x-bootstrap::row class="small-gutters">
						<x-bootstrap::column breakpoint="EXTRA_SMALL|3" class="d-flex align-items-baseline">
							{!! form_row($form->year) !!}
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|5" class="d-flex align-items-baseline">
							{!! form_row($form->month) !!}
						</x-bootstrap::column>
						<x-bootstrap::column class="d-flex align-items-end justify-content-end">
							<div class="form-group d-flex flex-column">
								{!! form_row($form->submit) !!}
							</div>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
	</x-bootstrap::row>
	{!! form_end($form, $renderRest = true) !!}
	@if($request?->isMethod('POST'))
	<x-bootstrap::row class="justify-content-center mt-4">
		<x-bootstrap::column>
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column breakpoint=" EXTRA_SMALL|6;SMALL|auto;MEDIUM|8" class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								Result
								<small class="fs-0 text-muted d-block">List of all attendance</small>
							</h5>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto;MEDIUM|4" class="d-flex align-items-baseline justify-content-end">
							<a href="https://kaze.omni.dev/task/create" role="button" data-toggle="tooltip" title="Download" class="btn btn-falcon-primary ml-2">
								<span class="fad fa-download mr-1"></span>
								<span>Download</span>
							</a>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light overflow-hidden p-0">
					@include('themes::Falcon.views.layouts.table', [
						'method' => 'get',
						'hasActions' => true,
						'hasToolbar' => true,
						'data' => [
							'url' => route('api.attendance.report', ['start' => sprintf("%s-%s-01", $request->input('year'), $request->input('month'))]),
							'page-size' => 25,
							'show-refresh' => 'true',
							'method' => 'get',
						],
						'columns' => [
							[
								'title' => 'Name',
								'attrs' => [
									'field' => 'employee_name',
									'class' => 'va-baseline text-nowrap',
									'sortable' => 'true',
								],
							],
							[
								'title' => 'Present',
								'attrs' => [
									'field' => 'present',
									'class' => 'va-baseline text-nowrap text-center',
									'sortable' => 'true',
								],
							],
							[
								'title' => 'Sick',
								'attrs' => [
									'field' => 'sick',
									'class' => 'va-baseline text-nowrap text-center',
									'sortable' => 'true',
								],
							],
							[
								'title' => 'Business Trip',
								'attrs' => [
									'field' => 'business_trip',
									'class' => 'va-baseline text-nowrap text-center',
									'sortable' => 'true',
								],
							],
							[
								'title' => 'Permit',
								'attrs' => [
									'field' => 'permit',
									'class' => 'va-baseline text-nowrap text-center',
									'sortable' => 'true',
								],
							],
							[
								'title' => 'Absent',
								'attrs' => [
									'field' => 'absent',
									'class' => 'va-baseline text-nowrap text-center',
									'sortable' => 'true',
								],
							],
							[
								'title' => 'Annual Leave',
								'attrs' => [
									'field' => 'annual_leave',
									'class' => 'va-baseline text-nowrap text-center',
									'sortable' => 'true',
								],
							],
						],
					])
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
	</x-bootstrap::row>
	@endif
@endsection