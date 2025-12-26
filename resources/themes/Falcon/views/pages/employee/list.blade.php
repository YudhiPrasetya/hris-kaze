@extends('falcon::layouts.list')

@section('title', 'Employee')
@section('subtitle')
	<small class="fs-0 text-muted d-block">List of employee</small>
@endsection
@section('new_url', route('employee.create'))
@section('api_list_url', route('api.employee'))

@section('columns')
	<th scope="col" data-class="va-baseline" data-field="name" data-sortable="true">Name</th>
	<th scope="col" data-class="va-baseline" data-field="position.name" data-sortable="true">Position</th>
	<th scope="col" data-class="va-baseline" data-field="age" data-sortable="true">Age</th>
	<th scope="col" data-class="va-baseline" data-field="effective_since" data-sortable="true">Effective Since</th>
@endsection