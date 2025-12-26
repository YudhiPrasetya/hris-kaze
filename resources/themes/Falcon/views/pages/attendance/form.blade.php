
@extends('falcon::layouts.base')

@section('javascripts')
	@parent
	<script src="{{ themes('js/default-select.js') }}" defer></script>
	<script src="{{ asset('js/date.js') }}" defer></script>
	<script src="{{ themes('js/attendance.js') }}" defer></script>
@endsection

@section('content')
	@php
		$fields = collect($form->getFieldValues());
	@endphp
	{!! form_start($form, ['attr' => ['autocomplete' => "off"]]) !!}
	<x-bootstrap::row class="justify-content-center">
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;SMALL|12;MEDIUM|9;LARGE|7">
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column breakpoint=" EXTRA_SMALL|6;SMALL|auto;MEDIUM|8" class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								@if(!$model->employee)
									Attendance
									<small class="fs-0 text-muted d-block">Add Attendance</small>
								@else
									{{ $model->employee->name }}
									<small class="fs-0 text-muted d-block">Attendance</small>
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
						<x-bootstrap::column>
							<x-bootstrap::row class="small-gutters">
								<x-bootstrap::column breakpoint="EXTRA_SMALL|3">{!! form_row($form->at) !!}</x-bootstrap::column>
								<x-bootstrap::column breakpoint="EXTRA_SMALL|2">{!! form_row($form->start) !!}</x-bootstrap::column>
								<x-bootstrap::column breakpoint="EXTRA_SMALL|2">{!! form_row($form->end) !!}</x-bootstrap::column>
								<x-bootstrap::column breakpoint="EXTRA_SMALL|2">{!! form_row($form->overtime) !!}</x-bootstrap::column>
							</x-bootstrap::row>

							{!! form_row($form->employee_id, ['attr' => ['data-value' => $model->employee_id]]) !!}
							<x-bootstrap::row>
								<x-bootstrap::column>{!! form_row($form->attendance_reason_id, ['attr' => ['data-value' => $model->attendance_reason_id]]) !!}</x-bootstrap::column>
								<x-bootstrap::column>{!! form_row($form->annual_leave_id, ['attr' => ['data-value' => $model->annual_leave_id]]) !!}</x-bootstrap::column>
							</x-bootstrap::row>
							{!! form_row($form->detail, ['attr' => ['class' => 'tinymce']]) !!}
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
	</x-bootstrap::row>
	{!! form_end($form, $renderRest = true) !!}
@endsection
