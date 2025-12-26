@extends('falcon::layouts.base')

@section('javascripts')
@endsection

@section('content')
	<x-bootstrap::row class="justify-content-center">
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|9">
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto;MEDIUM|8" class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								{{ $model->service_no }}<br />
								<small class="fs-0 text-muted d-block">Assignment</small>
							</h5>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto" class="d-flex align-items-baseline">
							<a href="{{ route('assignment.edit', ['assignment' => $model->id]) }}" class="btn-falcon-success btn mr-1" role="button"><i class="fad fa-pencil-alt mr-2"></i>Edit</a>
							<button class="btn-falcon-danger btn" type="button" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $model->id }}').submit();">
								<i class="fad fa-trash-alt mr-2"></i>Delete
							</button>
							<form id="delete-form-{{ $model->id }}" action="{{ route('assignment.destroy', ['assignment' => $model->id]) }}" method="POST" style="display: none;">
								<input type="hidden" name="_method" value="DELETE">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</form>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light">
					<div class="fancy-tab">
						<div class="nav-bar">
							<div class="nav-bar-item px-3 px-sm-4 active">Service Info</div>
							<div class="nav-bar-item px-3 px-sm-4">Technicians</div>
							<div class="nav-bar-item px-3 px-sm-4">Parts</div>
							<div class="nav-bar-item px-3 px-sm-4">Tracker</div>
						</div>
						<div class="fancy-tab-contents mt-3 overflow-hidden">
							<div class="fancy-tab-content active">
								<x-bootstrap::row>
									<x-bootstrap::column breakpoint="EXTRA_SMALL|6;MEDIUM|4">
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Purchase Order No.</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->purchase_order_no }}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Product Code</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->product_code }}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Chargeable</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0">
												<span class="badge badge-pill badge-{{ $model->is_chargeable ? 'success' : 'danger' }}">{{ $model->is_chargeable ? 'Yes' : 'No' }}</span>
											</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Vehicle</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->getVehicle()?->type }} &mdash; {{ $model->getVehicle()?->plat_number }}</span>
										</div>
									</x-bootstrap::column>
									<x-bootstrap::column>
										<x-bootstrap::row>
											<x-bootstrap::column>
												<div class="form-group">
													<h6 class="text-600 control-label mb-1">Service Report No.</h6>
													<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->service_no }}</span>
												</div>
											</x-bootstrap::column>
											<x-bootstrap::column>
												<div class="form-group">
													<h6 class="text-600 control-label mb-1">Service date</h6>
													<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->service_date->format('l, d F Y') }}</span>
												</div>
											</x-bootstrap::column>
										</x-bootstrap::row>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Status</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->currentStatus()?->first()->reason }}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Customer</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0 border-bottom">{{ $model->getCustomer()->name }}</span>
											<span id="name" class="form-control-plaintext text-1000 fs-0">
												<i class="fad fa-map-marked-alt fs-0 text-red mr-2"></i> {{ $model->getCustomer()->address() }}
											</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Machine</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0 border-bottom">{{ $model->getCustomerMachine()?->getMachine()->name }}</span>
											<span id="name" class="form-control-plaintext text-1000 fs-0 border-bottom">{{ $model->getCustomerMachine()?->getMachine()->type }}</span>
											<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->getCustomerMachine()?->serial_number }}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Work Detail</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0">{!! $model->work_detail !!}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Note</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0">{!! $model->note !!}</span>
										</div>
									</x-bootstrap::column>
								</x-bootstrap::row>
							</div>
							<div class="fancy-tab-content">
								<div class="table-responsive">
									<table class="table table-hover bg-white table-technicians">
										<thead class="thead-dark">
										<tr>
											<th class="va-baseline text-center fs-0">No.</th>
											<th class="va-baseline text-nowrap fs-0">Name</th>
											<th class="text-center fs-0">Start Job</th>
											<th class="text-center fs-0">Finish Job</th>
											<th class="text-center fs-0">Travel Time</th>
											<th class="text-center fs-0">Overtime</th>
										</tr>
										</thead>
										<tbody>
										@foreach($model->technicians()->get() as $child)
											<tr>
												<td class="va-baseline text-center fs-0">{{ $loop->index + 1 }}</td>
												<td class="va-baseline fs-0">{!! $child->employee->link() !!}</td>
												<td class="text-center fs-0">{{ $child->start_job }}</td>
												<td class="text-center fs-0">{{ $child->finish_job }}</td>
												<td class="text-center fs-0">{{ $child->travel_time }}</td>
												<td class="text-center fs-0">{{ $child->overtime }}</td>
											</tr>
										@endforeach
										</tbody>
									</table>
								</div>
							</div>
							<div class="fancy-tab-content">
								<div class="table-responsive">
									<table class="table table-hover bg-white table-parts">
										<thead class="thead-dark">
										<tr>
											<th class="va-baseline text-center fs-0" width="100">No.</th>
											<th class="va-baseline text-nowrap fs-0">Name</th>
											<th class="va-baseline text-nowrap fs-0">Type</th>
											<th class="va-baseline text-center fs-0" width="100">Qty</th>
											<th class="va-baseline text-center fs-0" width="200">Unit</th>
										</tr>
										</thead>
										<tbody>
										@foreach($model->parts()->get() as $child)
											<tr>
												<td class="va-baseline text-center fs-0">{{ $loop->index + 1 }}</td>
												<td class="va-baseline fs-0">{{ $child->part_name }}</td>
												<td class="va-baseline fs-0">{{ $child->part_type }}</td>
												<td class="va-baseline text-center fs-0">{{ $child->qty }}</td>
												<td class="va-baseline text-center fs-0">{{ $child->unit }}</td>
											</tr>
										@endforeach
										</tbody>
									</table>
								</div>
							</div>
							<div class="fancy-tab-content"></div>
						</div>
					</div>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
	</x-bootstrap::row>
@endsection