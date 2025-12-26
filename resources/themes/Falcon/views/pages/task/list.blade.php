@extends('falcon::layouts.list')

@section('title', 'Tasks')
@section('subtitle')
	<small class="fs-0 text-muted d-block">List of task</small>
@endsection
@section('new_url', route('task.create'))
@section('api_list_url', route('api.task'))

@section('columns')
	<th scope="col" data-class="va-baseline" data-field="title" data-sortable="true">Title</th>
	<th scope="col" data-class="va-baseline" data-field="dateline" data-sortable="true">Dateline</th>
	<th scope="col" data-class="va-baseline" data-field="priority.name" data-sortable="true">Level of Urgency</th>
	<th scope="col" data-class="va-baseline" data-field="assign_to" data-sortable="true">Assign To</th>
	<th scope="col" data-class="va-baseline" data-field="current_status.reason" data-sortable="true">Status</th>
@endsection