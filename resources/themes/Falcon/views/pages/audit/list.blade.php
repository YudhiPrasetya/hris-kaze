@extends('falcon::layouts.list')


@section('title', 'Audit')
@section('subtitle')
	<small class="fs-0 text-muted d-block">List of user activities</small>
@endsection
@section('api_list_url', route('api.audit'))
@section('data-table')
	@parent
	data-sort-name="created_at"
	data-sort-order="desc"
@endsection

@section('columns')
	<th scope="col" data-class="va-baseline" data-field="auditable_type" data-sortable="true">Section</th>
	<th scope="col" data-class="va-baseline" data-field="event" data-sortable="true">Event</th>
	<th scope="col" data-class="va-baseline" data-field="user" data-sortable="true">By</th>
	<th scope="col" data-class="text-center va-baseline text-nowrap" data-sortable="true" data-field="at">At</th>
@endsection