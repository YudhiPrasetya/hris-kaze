
@extends('falcon::layouts.list')

@section('title', 'Overtimes')
@section('subtitle')
	<small class="fs-0 text-muted d-block">List of overtimes</small>
@endsection
@section('new_url', route('ot.create'))
@section('api_list_url', route('api.ot'))

@section('columns')
	<th scope="col" data-class="va-baseline" data-field="overtime_date" data-sortable="true">Date</th>
	<th scope="col" data-class="va-baseline" data-field="employee.name" data-sortable="true">Employee</th>
	<th scope="col" data-class="va-baseline" data-field="start" data-sortable="true">From</th>
	<th scope="col" data-class="va-baseline" data-field="end" data-sortable="true">To</th>
	<th scope="col" data-class="va-baseline" data-field="necessity" data-sortable="true">Reason</th>
@endsection
