
@extends('falcon::layouts.list')

@section('title', 'Permits')
@section('subtitle')
	<small class="fs-0 text-muted d-block">List permits</small>
@endsection
@section('new_url', route('permit.create'))
@section('api_list_url', route('api.permit'))

@section('columns')
	<th scope="col" data-class="va-baseline" data-field="permit_date" data-sortable="true">Date</th>
	<th scope="col" data-class="va-baseline" data-field="employee.name" data-sortable="true">Employee</th>
	<th scope="col" data-class="va-baseline" data-field="permit_type" data-sortable="true">Type</th>
	<th scope="col" data-class="va-baseline" data-field="note" data-sortable="true">Reason</th>
	<th scope="col" data-class="va-baseline" data-field="start" data-sortable="true">From</th>
	<th scope="col" data-class="va-baseline" data-field="end" data-sortable="true">To</th>
@endsection
