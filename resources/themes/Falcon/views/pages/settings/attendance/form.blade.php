@extends('falcon::layouts.base')

@section('javascripts')
	@parent
	<script src="{{ asset('js/date.js') }}" defer></script>
	<script src="{{ themes('js/default-select.js') }}" defer></script>
	<script src="{{ themes('js/attendance-settings.js') }}" defer></script>
@endsection

@section('content')
	{!! form_start($form, ['attr' => ['autocomplete' => "off"]]) !!}
	<x-bootstrap::row class="justify-content-center">
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;LARGE|6">
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column breakpoint=" EXTRA_SMALL|6;SMALL|auto;MEDIUM|8" class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">Attendance Settings</h5>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto" class="d-flex align-items-baseline">
							{!! form_row($form->submit) !!}
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light">
					<x-bootstrap::row>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|3;LARGE|4" class="border-right">
							<div class="nav flex-column nav-pills pt-1" id="v-pills-tab" role="tablist" aria-orientation="vertical" data-spy="affix" data-prefix="nav" data-offset-top="1106" data-target=".os-viewport">
								<a class="nav-link active" id="v-pills-connection-tab" data-toggle="pill" href="#v-pills-connection" role="tab" aria-controls="v-pills-connection" aria-selected="true">Connection</a>
								<a class="nav-link" id="v-pills-time-tab" data-toggle="pill" href="#v-pills-time" role="tab" aria-controls="v-pills-time" aria-selected="true">Time</a>
							</div>
						</x-bootstrap::column>
						<x-bootstrap::column class="background-transparent">
							<div class="tab-content" id="v-pills-tabContent">
								<div class="tab-pane fade show active pt-1" id="v-pills-connection" role="tabpanel" aria-labelledby="v-pills-connection-tab">
									<x-bootstrap::media variant="primary" class="mb-4" icon="fad fa-fingerprint" title="Device" subtitle="Attendance device." />
									<div class="form-group row">
										{!! form_label($form->serial_number, ['label_attr' => ['class' => 'col-sm-4 col-form-label']]) !!}
										<div class="col-sm-8">
											{!! form_widget($form->serial_number, ['value' => $data['serial_number']->value]) !!}
										</div>
									</div>
									<div class="form-group row">
										{!! form_label($form->ip, ['label_attr' => ['class' => 'col-sm-4 col-form-label']]) !!}
										<div class="col-sm-4">
											{!! form_widget($form->ip, ['value' => $data['ip']->value]) !!}
										</div>
									</div>
									<div class="form-group row">
										{!! form_label($form->port, ['label_attr' => ['class' => 'col-sm-4 col-form-label']]) !!}
										<div class="col-sm-3">
											{!! form_widget($form->port, ['value' => $data['port']->value]) !!}
										</div>
									</div>
									<div class="form-group row">
										{!! form_label($form->user, ['label_attr' => ['class' => 'col-sm-4 col-form-label']]) !!}
										<div class="col-sm-6">
											{!! form_widget($form->user, ['value' => $data['user']->value]) !!}
										</div>
									</div>
									<div class="form-group row">
										{!! form_label($form->password, ['label_attr' => ['class' => 'col-sm-4 col-form-label']]) !!}
										<div class="col-sm-6">
											{!! form_widget($form->password, ['value' => $data['password']->value]) !!}
										</div>
									</div>
									<x-bootstrap::media variant="primary" class="my-4" icon="fad fa-browser" title="Service" subtitle="Employee personal information." />
									<div class="form-group row">
										{!! form_label($form->service_ip, ['label_attr' => ['class' => 'col-sm-4 col-form-label']]) !!}
										<div class="col-sm-4">
											{!! form_widget($form->service_ip, ['value' => $data['service_ip']->value]) !!}
										</div>
									</div>
									<div class="form-group row">
										{!! form_label($form->service_port, ['label_attr' => ['class' => 'col-sm-4 col-form-label']]) !!}
										<div class="col-sm-3">
											{!! form_widget($form->service_port, ['value' => $data['service_port']->value]) !!}
										</div>
									</div>
								</div>
								<div class="tab-pane fade pt-1" id="v-pills-time" role="tabpanel" aria-labelledby="v-pills-time-tab">
									<div class="form-group row">
										{!! form_label($form->cutoff, ['label_attr' => ['class' => 'col-sm-3 col-form-label']]) !!}
										<div class="col-sm-9">
											@php
												$isUserDefined = is_numeric($data['cutoff']->value);
											@endphp
											{!! form_widget($form->cutoff, ['attr' => ['data-value' => $isUserDefined ? 'user_defined' : 'end_of_month']]) !!}
											<p id="cutoff_date" class="{{ $isUserDefined ? '' : 'd-none' }}">On the {!! form_widget($form->cutoff_date, ['value' => $data['cutoff']->value === 'end_of_month' ? '25' : $data['cutoff']->value]) !!} every month.</p>
										</div>
									</div>
								</div>
							</div>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
	</x-bootstrap::row>
	{!! form_end($form, $renderRest = true) !!}
@endsection