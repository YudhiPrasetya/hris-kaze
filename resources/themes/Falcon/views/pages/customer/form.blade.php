@extends('falcon::layouts.base')

@section('javascripts')
	@parent
	<script src="{{ themes('js/default-select.js') }}" defer></script>
	<script src="{{ themes('js/customer.js') }}" defer></script>
@endsection

@section('content')
	@php
		$fields = collect($form->getFieldValues());
	@endphp
	{!! form_start($form, ['attr' => ['autocomplete' => "off"]]) !!}
	<x-bootstrap::row>
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|9">
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto;MEDIUM|9" class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								@if(!$model->name)
									Customer
									<small class="fs-0 text-muted d-block">Add new customer</small>
								@else
									{{ $model->name }}
									<small class="fs-0 text-muted d-block">Customer</small>
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
							<div class="nav-bar-item px-3 px-sm-4 active">Info</div>
						</div>
						<div class="fancy-tab-contents mt-3 overflow-hidden">
							<div class="fancy-tab-content active">
								<x-bootstrap::row>
									<x-bootstrap::column>
										{!! form_row($form->name) !!}
										{!! form_row($form->email) !!}
										{!! form_row($form->country_id, ['attr' => ['data-value' => $model->country_id ? $model->country_id : 'ID']]) !!}
										{!! form_row($form->state_id, ['attr' => ['data-value' => $model->state_id]]) !!}
										{!! form_row($form->city_id, ['attr' => ['data-value' => $model->city_id]]) !!}
										{!! form_row($form->district_id, ['attr' => ['data-value' => $model->district_id]]) !!}
										{!! form_row($form->village_id, ['attr' => ['data-value' => $model->village_id]]) !!}
										{!! form_row($form->postal_code) !!}
										{!! form_row($form->street) !!}
									</x-bootstrap::column>
								</x-bootstrap::row>
							</div>
						</div>
					</div>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
		@include('falcon::pages.customer.info')
	</x-bootstrap::row>
	{!! form_end($form, $renderRest = true) !!}
@endsection
