@extends('falcon::layouts.base')

@section('javascripts')
	@parent
	<script src="{{ themes('js/dashboard.js') }}"></script>
	<script src="{{ themes('js/attendance-detail.js') }}"></script>
@endsection

@section('content')
	<x-bootstrap::row class="justify-content-center">
		<x-bootstrap::column>
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column breakpoint=" EXTRA_SMALL|6;SMALL|auto;MEDIUM|8" class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								Attendance Report Detail &dash; {{ (new \DateTime($data['start']))->format('F Y') }}
								<small class="fs-0 text-muted d-block">Detailed list of attendance</small>
							</h5>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto;MEDIUM|4" class="d-flex align-items-baseline justify-content-end">
							<a href="{{ route('report.attendance.employee.download', ['employee' => $model->id]) }}" role="button" data-toggle="tooltip" title="Download" class="btn btn-falcon-primary ml-2">
								<span class="fad fa-download mr-1"></span>
								<span>Download</span>
							</a>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light px-0">
					<div class="row-info px-3">
						<x-bootstrap::row>
							<x-bootstrap::column breakpoint="MEDIUM|1" class="col-label"><span id="name" class="form-control-plaintext">Employee</span></x-bootstrap::column>
							<x-bootstrap::column>
								<span id="name" class="form-control-plaintext text-1000 fs-0">
									<span>{!! $model->link() !!}</span>
								</span>
							</x-bootstrap::column>
						</x-bootstrap::row>
						<x-bootstrap::row>
							<x-bootstrap::column breakpoint="MEDIUM|1" class="col-label"><span id="name" class="form-control-plaintext">NIK</span></x-bootstrap::column>
							<x-bootstrap::column>
								<span id="name" class="form-control-plaintext text-1000 fs-0">
									<span>{{ $model->nik }}</span>
								</span>
							</x-bootstrap::column>
						</x-bootstrap::row>
						<x-bootstrap::row>
							<x-bootstrap::column breakpoint="MEDIUM|1" class="col-label"><span id="name" class="form-control-plaintext">Position</span></x-bootstrap::column>
							<x-bootstrap::column>
								<span id="name" class="form-control-plaintext text-1000 fs-0">
									<span>{{ $model->position()->first()->name }}</span>
								</span>
							</x-bootstrap::column>
						</x-bootstrap::row>
					</div>
					<table
						class="bootstrap-table-free"
						data-method="get"
						data-pagination="false"
						data-show-pagination-switch="false"
						data-page-size="31"
						data-show-refresh="true"
						data-sort-name="created_at"
						data-sort-order="desc"
						data-url="{{ route('api.attendance.report.employee', ['employee' => $model->id, 'start' => $data['start']]) }}"
						data-row-style="rowStyle">
						<thead class="thead-light">
						<tr>
							<th rowspan="2" scope="col" data-class="text-center va-middle font-weight-medium" data-field="no" data-sortable="false" width="50">#</th>
							<th rowspan="2" scope="col" data-class="va-middle text-nowrap" data-field="date" data-sortable="false">Date</th>
							<th colspan="3" scope="col" data-class="text-center va-baseline" data-sortable="false">Working Hours</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle text-nowrap" data-field="total" data-sortable="false">Total Hours</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle" data-field="present" data-sortable="false" data-width="35">Present</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle text-nowrap" data-field="sick" data-sortable="false" data-width="35">Sick</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle text-nowrap" data-field="business_trip" data-sortable="false" data-width="35">Business Trip</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle text-nowrap" data-field="permit" data-sortable="false" data-width="35">Permit</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle text-nowrap" data-field="annual_leave" data-sortable="false" data-width="35">Annual Leave</th>
							<th rowspan="2" scope="col" data-class="text-center va-middle text-nowrap" data-field="absent" data-sortable="false" data-width="35">Absent</th>
							<th rowspan="2" scope="col" data-class="va-middle" data-field="remark" data-sortable="false">Remark</th>
						</tr>
						<tr>
							<th scope="col" data-class="text-center va-middle text-nowrap" data-field="start" data-sortable="false">Check-In</th>
							<th scope="col" data-class="text-center va-middle text-nowrap" data-field="end" data-sortable="false">Check-Out</th>
							<th scope="col" data-class="text-center va-middle text-nowrap" data-field="overtime" data-sortable="false">Overtime</th>
						</tr>
						</thead>
					</table>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
	</x-bootstrap::row>
@endsection