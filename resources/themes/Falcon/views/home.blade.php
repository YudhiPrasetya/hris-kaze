@extends('falcon::layouts.base')

@section('content')
	<x-bootstrap::card class="mb-3">
		<x-bootstrap::card.body class="rounded-soft bg-gradient">
			<x-bootstrap::row gutters="{{ false }}" class="text-white align-items-base">
				<x-bootstrap::column>
					<h4 class="text-white mb-0 today-payment">Today Rp. 0</h4>
					<p class="fs--1 font-weight-semi-bold yesterday-payment">Yesterday <span class="opacity-50">Rp. 0</span></p>
				</x-bootstrap::column>
				<x-bootstrap::column breakpoint="AUTO|7" class="d-none d-sm-block">
					<select class="custom-select custom-select-sm mb-3" id="dashboard-chart-select">
{{--						@foreach($chartSelections as $key => $selection)--}}
{{--							<option value="{{ $key }}" @if ($loop->first) selected="selected" @endif>{{ $selection }}</option>--}}
{{--						@endforeach--}}
					</select>
				</x-bootstrap::column>
			</x-bootstrap::row>
			<canvas class="max-w-100 rounded" id="chart-payments" width="1618" height="375" aria-label="Line chart" role="img"></canvas>
		</x-bootstrap::card.body>
	</x-bootstrap::card>
	<x-bootstrap::row>
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|8">
			<div class="media mt-5 mb-3">
				<span class="fa-stack mr-2 ml-n1">
					<i class="fas fa-circle fa-stack-2x text-300"></i>
					<i class="fa-inverse fa-stack-1x text-primary fad fa-running"></i>
				</span>
				<div class="media-body">
					<h5 class="mb-0 text-primary position-relative">
						<span class="bg-200 pr-3 text-nunito font-weight-semi-bold">Recent Activities</span>
						<span class="border position-absolute absolute-vertical-center w-100 z-index--1 l-0"></span>
					</h5>
					<span>All user recent activities.</span>
				</div>
			</div>
			<x-bootstrap::card class="mb-3">
				<x-bootstrap::card.body class="rounded-soft overflow-hidden p-0">
					<table
						class="bootstrap-table-custom table-striped"
						data-method="get"
						data-pagination="true"
						data-page-size="10"
						data-show-refresh="true"
						data-side-pagination="server"
						data-sort-name="created_at"
						data-sort-order="desc"
						data-url="{{ route('api.audit.latest') }}">
						<thead class="thead-light">
						<tr>
							<th scope="col" data-class="text-center va-baseline font-weight-medium" data-field="no" data-sortable="false" data-width="75">#</th>
							<th scope="col" data-class="va-baseline" data-field="auditable_type" data-sortable="true">Model</th>
							<th scope="col" data-class="va-baseline" data-field="event" data-sortable="true">Event</th>
							<th scope="col" data-class="va-baseline" data-field="user" data-sortable="true">By</th>
							<th scope="col" data-class="text-center va-baseline text-nowrap" data-sortable="true" data-field="created_at">At</th>
							<th scope="col" data-class="text-center va-baseline text-nowrap bootstrap-table-actions" data-field="actions" data-sortable="false" data-formatter="actionsFormatter" data-width="154">Actions</th>
						</tr>
						</thead>
					</table>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
	</x-bootstrap::row>
@endsection
