@extends('falcon::layouts.list')

@section('title', 'Machines')
@section('subtitle')
	<small class="fs-0 text-muted d-block">List of machines</small>
@endsection
@section('new_url', route('machine.create'))
@section('api_list_url', route('api.machine'))

@section('columns')
	<th scope="col" data-class="va-baseline" data-field="name" data-sortable="true">Name</th>
	<th scope="col" data-class="va-baseline" data-field="type" data-sortable="true">Type</th>
@endsection