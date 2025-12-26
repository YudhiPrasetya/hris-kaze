@extends('falcon::layouts.base')

@section('javascripts')
	@parent
	<script src="{{ themes('js/default-select.js') }}" defer></script>
	<script src="{{ themes('js/employee.js') }}" defer></script>
	<script src="{{ asset('js/date.js') }}" defer></script>
@endsection

@section('content')
	@php
		$fields = collect($form->getFieldValues());
	@endphp
	{!! form_start($form, ['attr' => ['autocomplete' => "off", 'class' => 'employee']]) !!}
	<x-bootstrap::row>
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|9">
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto;MEDIUM|9" class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								@if(!$model->name)
									Employee
									<small class="fs-0 text-muted d-block">Register new employee</small>
								@else
									{{ $model->name }}
									<small class="fs-0 text-muted d-block">Employee</small>
								@endif
							</h5>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto" class="d-flex align-items-baseline">
							{!! form_row($form->submit) !!}
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light">
					<x-bootstrap::row>
						<x-bootstrap::column breakpoint="EXTRA_SMALL;MEDIUM|3" class="d-flex align-items-start justify-content-center border-right">
							<div class="avatar w-90 mb-3">
								<figure class="figure w-100">
									<img id="targetPreview" src="@if(!$model->profile_photo_path->isEmpty()){!! $model->profile_photo_path->toString() !!}@else{{ route('image-placeholder', ['size' => 512, 'bgColor' => 'EEF0F2', 'textColor' => 'ffffff']) }}@endif" class="figure-img img-fluid rounded-circle img-thumbnail w-100 transition-all" alt="Profile Picture" />
									<figcaption class="figure-caption">
										<div class="d-flex justify-content-center">
											{!! form_widget($form->profile_photo_path) !!}
										</div>
									</figcaption>
								</figure>
							</div>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL;MEDIUM|9" class="pb-6">
							{!! form_row($form->name) !!}

							{!! form_row($form->nik) !!}

							{!! form_row($form->position_id) !!}

							{!! form_row($form->job_title_id, ['attr' => ['class_append' => 'select2', 'data-value' => $fields->get('job_title_id')]]) !!}

							{!! form_row($form->working_shift_id, ['attr' => ['class_append' => 'select2', 'data-value' => $fields->get('working_shift_id')]]) !!}

							{!! form_row($form->user_id, ['attr' => ['class_append' => 'select2', 'data-value' => $fields->get('user_id')]]) !!}

							{!! form_row($form->effective_since, ['attr' => ['value' => $fields->get('effective_since')]]) !!}

                            {!! form_row($form->leave_allowance, ['attr' => ['value' => $fields->get('leave_allowance')]]) !!}
						</x-bootstrap::column>
					</x-bootstrap::row>
					<x-bootstrap::row>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|3" class="border-right">
							<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical" data-spy="affix" data-prefix="nav" data-offset-top="665" data-target=".os-viewport">
								<a class="nav-link active" id="v-pills-info-tab" data-toggle="pill" href="#v-pills-info" role="tab" aria-controls="v-pills-info" aria-selected="true">Info</a>
								<a class="nav-link" id="v-pills-salary-tab" data-toggle="pill" href="#v-pills-salary" role="tab" aria-controls="v-pills-salary" aria-selected="true">Salary</a>
							</div>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|9" class="background-transparent">
							<div class="tab-content" id="v-pills-tabContent">
								<div class="tab-pane fade show active" id="v-pills-info" role="tabpanel" aria-labelledby="v-pills-info-tab">
									<x-bootstrap::media variant="primary" class="mb-4" icon="fad fa-info" title="Personal Information" subtitle="Employee personal information." />
									{!! form_row($form->gender_id, ['attr' => ['class_append' => 'select2', 'data-value' => $fields->get('gender_id')]]) !!}
									{!! form_row($form->birth_date, ['attr' => ['value' => $fields->get('birth_date')]]) !!}
									{!! form_row($form->marital_status, ['attr' => ['value' => $fields->get('marital_status')]]) !!}
									{!! form_row($form->num_of_dependents_family, ['attr' => ['value' => $fields->get('num_of_dependents_family')]]) !!}
									{!! form_row($form->has_npwp, ['value' => $fields->get('has_npwp')]) !!}
									{!! form_row($form->permanent_status, ['value' => $fields->get('permanent_status')]) !!}
									{!! form_row($form->employee_guarantee, ['value' => $fields->get('employee_guarantee')]) !!}

									<x-bootstrap::media variant="primary" class="mt-6 mb-4" icon="fad fa-info" title="Address" subtitle="Employee address." />
									{!! form_row($form->country_id, ['attr' => ['data-value' => $model->country_id ? $model->country_id : 'ID']]) !!}
									{!! form_row($form->state_id, ['attr' => ['data-value' => $model->state_id]]) !!}
									{!! form_row($form->city_id, ['attr' => ['data-value' => $model->city_id]]) !!}
									{!! form_row($form->district_id, ['attr' => ['data-value' => $model->district_id]]) !!}
									{!! form_row($form->village_id, ['attr' => ['data-value' => $model->village_id]]) !!}
									{!! form_row($form->postal_code) !!}
									{!! form_row($form->street) !!}
								</div>
								<div class="tab-pane fade" id="v-pills-salary" role="tabpanel" aria-labelledby="v-pills-salary-tab">
									<x-bootstrap::row>
										<x-bootstrap::column breakpoint="MEDIUM|6">
											<x-bootstrap::media variant="primary" class="mb-4" icon="fad fa-money-check-alt" title="Earnings" subtitle="Employee basic salary." />
											{!! form_row($form->currency_code, ['attr' => ['data-value' => $model->currency_code ? $model->currency_code : 'IDR']]) !!}
											{!! form_row($form->basic_salary) !!}
											{!! form_row($form->attendance_premium) !!}
											{!! form_row($form->overtime) !!}
										</x-bootstrap::column>
										<x-bootstrap::column breakpoint="MEDIUM|6">
											<x-bootstrap::media variant="primary" class="mb-4" icon="fad fa-info" title="Allowance" subtitle="Salary allowance based on position." />
											{!! form_row($form->functional_allowance) !!}
											{!! form_row($form->transport_allowance) !!}
											{!! form_row($form->meal_allowances) !!}
											{!! form_row($form->other_allowance) !!}
										</x-bootstrap::column>
									</x-bootstrap::row>
								</div>
							</div>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
		@include('falcon::pages.employee.info')
	</x-bootstrap::row>
	{!! form_end($form, $renderRest = true) !!}
@endsection
