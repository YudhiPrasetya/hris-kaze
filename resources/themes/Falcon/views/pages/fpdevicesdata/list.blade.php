{{-- @extends('falcon::layouts.base')

@section('content')
	<x-bootstrap::row class="small-gutters">
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|8">
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="row no-gutters align-items-center">
						<x-bootstrap::column>
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0">
                                Pull Data
								<small class="fs-0 text-muted d-block">List of data that pull from devices</small>
							</h5>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL;MEDIUM|4" class="ml-auto text-right pl-0">
							<a href="{{ route('fingerprintdevicedata.create') }}" role="button" id="attendance-create" class="btn btn-falcon-primary">
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
								'title' => 'Device ID',
								'attrs' => [
									'field' => 'finger_print_device_id',
									'class' => 'va-baseline text-nowrap',
									'sortable' => 'true',
								],
							],
							[
								'title' => 'NIK',
								'attrs' => [
									'field' => 'nik',
									'class' => 'va-baseline text-nowrap',
									'sortable' => 'true',
								],
							],
							[
								'title' => 'Timestamps',
								'attrs' => [
									'field' => 'start',
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
@endsection --}}


@extends('falcon::layouts.list')

@section('title', 'Pull Data From Device')
@section('subtitle')
	<small class="fs-0 text-muted d-block">List of data that pull from devices</small>
@endsection
@section('new_url', route('devicelog.create'))
@section('api_list_url', route('api.devicelog'))

@section('columns')
	<th scope="col" data-class="va-baseline" data-field="finger_print_device_id" data-sortable="true">No.Device</th>

	<th scope="col" data-class="va-baseline" data-field="nik" data-sortable="true">NIK Karyawan</th>

	<th scope="col" data-class="va-baseline" data-field="timestamps" data-sortable="true">Timestamps</th>

@endsection
