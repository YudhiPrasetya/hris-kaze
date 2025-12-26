
@extends('falcon::layouts.list')

@section('title', 'Leaves (Cuti)')
@section('subtitle')
	<small class="fs-0 text-muted d-block">List leaves (cuti)</small>
@endsection
@section('new_url', route('leave.create'))
@section('api_list_url', route('api.leave'))

@section('columns')
	<th scope="col" data-class="va-baseline" data-field="leave_date" data-sortable="true">Date</th>
	<th scope="col" data-class="va-baseline" data-field="employee.name" data-sortable="true">Employee</th>
	<th scope="col" data-class="va-baseline" data-field="reason_for_leave.name" data-sortable="true">Type</th>
	<th scope="col" data-class="va-baseline" data-field="note" data-sortable="true">Reason</th>
	<th scope="col" data-class="va-baseline" data-field="start" data-sortable="true">From</th>
	<th scope="col" data-class="va-baseline" data-field="end" data-sortable="true">To</th>
	<th scope="col" data-class="va-baseline" data-field="attachment_path" data-sortable="true">Attachment</th>
@endsection
