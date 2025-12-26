@extends('falcon::layouts.base')

@section('javascripts')
	@parent
	<script src="{{ themes('js/default-select.js') }}" defer></script>
	<script src="{{ asset('js/date.js') }}" defer></script>
	<script src="{{ themes('js/assignment.js') }}" defer></script>
@endsection

@section('content')
	@php
		$fields = collect($form->getFieldValues());
	@endphp
	{!! form_start($form, ['attr' => ['autocomplete' => "off"]]) !!}
	<x-bootstrap::row class="justify-content-center">
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|9">
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto;MEDIUM|9" class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								@if(!$model->name)
									Assignment
									<small class="fs-0 text-muted d-block">Add new service assignment</small>
								@else
									{{ $model->name }}
									<small class="fs-0 text-muted d-block">Assignment</small>
								@endif
							</h5>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto" class="d-flex align-items-baseline">
							{!! form_row($form->submit) !!}
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light">
					<div class="fancy-tab">
						<div class="nav-bar">
							<div class="nav-bar-item px-3 px-sm-4 active">Service Info</div>
							<div class="nav-bar-item px-3 px-sm-4">Technicians</div>
							<div class="nav-bar-item px-3 px-sm-4">Parts</div>
						</div>
						<div class="fancy-tab-contents mt-3 overflow-hidden">
							<div class="fancy-tab-content active">
								<x-bootstrap::row>
									<x-bootstrap::column breakpoint="EXTRA_SMALL|6;MEDIUM|4">
										{!! form_row($form->purchase_order_no) !!}
										{!! form_row($form->product_code) !!}
										{!! form_row($form->is_chargeable) !!}
										{!! form_row($form->vehicle_id, ['attr' => ['data-value' => $model->vehicle_id]]) !!}
                                        {!! form_row($form->destination) !!}
									</x-bootstrap::column>
									<x-bootstrap::column>
										<x-bootstrap::row>
											<x-bootstrap::column>{!! form_row($form->service_no) !!}</x-bootstrap::column>
											<x-bootstrap::column>{!! form_row($form->service_date) !!}</x-bootstrap::column>
										</x-bootstrap::row>
										{!! form_row($form->customer_id, ['attr' => ['data-value' => $model->customer_id]]) !!}
										{!! form_row($form->customer_machine_id, ['attr' => ['data-value' => $model->customer_machine_id]]) !!}

										{!! form_row($form->work_detail, ['attr' => ['class' => 'tinymce', 'rows' => 10]]) !!}
										{!! form_row($form->note, ['attr' => ['class' => 'tinymce', 'rows' => 10]]) !!}
									</x-bootstrap::column>
								</x-bootstrap::row>
							</div>
							<div class="fancy-tab-content">
								<div id="toolbar" class="technician collection-container pb-3 pt-1" data-prototype="{{ form_row($form->technicians->prototype()) }}">
									<button type="button" class="btn btn-falcon-primary add-technician">
										<span class="fa-layers fa-fw">
											<i class="fad fa-user-hard-hat"></i>
											<i class="fas fa-plus" data-fa-transform="shrink-10 down-4.2 right-10 up-10"></i>
										</span>
										<span class="d-none d-sm-inline-block ml-1">Add</span>
									</button>
								</div>
								<div class="table-responsive">
									<table class="table table-hover bg-white table-technicians">
										<thead class="thead-dark">
										<tr>
											<th class="va-baseline text-center fs-0">No.</th>
											<th class="va-baseline fs-0" width="400">Name</th>
											<th class="text-center fs-0">Start Job</th>
											<th class="text-center fs-0">Finish Job</th>
											<th class="text-center fs-0">Travel Time</th>
											<th class="text-center fs-0">Overtime</th>
											<th class="text-center fs-0">Actions</th>
										</tr>
										</thead>
										<tbody>
										@foreach($form->technicians->getChildren() as $child)
											@php(debug($child->employee_id->getOption('selected')))
											<tr>
												<td class="va-baseline text-center fs-0">{{ $loop->index + 1 }}</td>
												<td class="va-baseline fs-0">{!! form_widget($child->employee_id, ['attr' => ['data-value' => $child->employee_id->getOption('selected')]]) !!}</td>
												<td class="text-center fs-0">{!! form_widget($child->start_job, ['attr' => ['type' => 'time', 'class' => 'form-control', 'size' => 8]]) !!}</td>
												<td class="text-center fs-0">{!! form_widget($child->finish_job, ['attr' => ['type' => 'time', 'class' => 'form-control', 'size' => 8]]) !!}</td>
												<td class="text-center fs-0">{!! form_widget($child->travel_time, ['attr' => ['type' => 'time', 'class' => 'form-control', 'size' => 8]]) !!}</td>
												<td class="text-center fs-0">{!! form_widget($child->overtime, ['attr' => ['type' => 'time', 'class' => 'form-control', 'size' => 8]]) !!}</td>
												<td class="text-center"><button role="button" type="button" class="btn btn-falcon-danger text-danger remove-mdr"><i class="fad fa-trash"></i></button></td>
											</tr>
										@endforeach
										</tbody>
									</table>
								</div>
							</div>
							<div class="fancy-tab-content">
								<div id="toolbar" class="part collection-container pb-3 pt-1" data-prototype="{{ form_row($form->parts->prototype()) }}">
									<button type="button" class="btn btn-falcon-primary add-part">
										<span class="fa-layers fa-fw">
											<i class="fad fa-cogs"></i>
											<i class="fas fa-plus" data-fa-transform="shrink-10 down-4.2 right-13 up-10"></i>
										</span>
										<span class="d-none d-sm-inline-block ml-1">Add</span>
									</button>
								</div>
								<div class="table-responsive">
									<table class="table table-hover bg-white table-parts">
										<thead class="thead-dark">
										<tr>
											<th class="va-baseline text-center fs-0">No.</th>
											<th class="va-baseline fs-0" width="500">Name</th>
											<th class="va-baseline fs-0" width="400">Type</th>
											<th class="text-center fs-0" width="100">Qty</th>
											<th class="va-baseline fs-0" width="200">Unit</th>
											<th class="text-center fs-0">Actions</th>
										</tr>
										</thead>
										<tbody>
										@foreach($form->parts->getChildren() as $child)
											<tr>
												<td class="va-baseline text-center fs-0">{{ $loop->index + 1 }}</td>
												<td class="va-baseline fs-0">{!! form_widget($child->part_name) !!}</td>
												<td class="text-center fs-0">{!! form_widget($child->part_type) !!}</td>
												<td class="text-center fs-0">{!! form_widget($child->qty) !!}</td>
												<td class="text-center fs-0">{!! form_widget($child->unit) !!}</td>
												<td class="text-center"><button role="button" type="button" class="btn btn-falcon-danger text-danger remove-mdr"><i class="fad fa-trash"></i></button></td>
											</tr>
										@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
	</x-bootstrap::row>
	{!! form_end($form, $renderRest = true) !!}
@endsection
