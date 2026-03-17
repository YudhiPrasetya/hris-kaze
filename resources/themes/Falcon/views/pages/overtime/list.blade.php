@extends('falcon::layouts.list')

@section('title', 'Overtimes')
@section('subtitle')
	<small class="fs-0 text-muted d-block">List of overtimes</small>
@endsection
@section('api_list_url', route('api.attendance.overtime'))

@section('columns')
	<th scope="col" data-class="va-baseline" data-field="overtime_date" data-sortable="true">Date</th>
	<th scope="col" data-class="va-baseline" data-field="employee.name" data-sortable="true">Employee</th>
	<th scope="col" data-class="va-baseline" data-field="start" data-sortable="true">Start</th>
	<th scope="col" data-class="va-baseline" data-field="end" data-sortable="true">End</th>
	<th scope="col" data-class="va-baseline" data-field="overtime" data-sortable="true">Overtime</th>
	<th scope="col" data-class="va-baseline" data-field="necessity" data-sortable="true">Necessity</th>
	<th scope="col" data-class="va-baseline" data-field="overtime_confirmed" data-sortable="true">Overtime Confirmation</th>
@endsection