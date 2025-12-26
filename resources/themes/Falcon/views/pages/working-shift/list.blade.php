@extends('falcon::layouts.list')

@section('title', 'Working Shifts')
@section('subtitle')
	<small class="fs-0 text-muted d-block">List of working shifts</small>
@endsection
@section('new_url', route('workingshift.create'))
@section('api_list_url', route('api.working-shift'))

@section('columns')
	<th scope="col" data-class="va-baseline" data-field="start" data-sortable="true">Start</th>
	<th scope="col" data-class="va-baseline" data-field="end" data-sortable="true">End</th>
@endsection