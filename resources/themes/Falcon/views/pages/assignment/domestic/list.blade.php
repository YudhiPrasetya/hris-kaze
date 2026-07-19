@extends('falcon::layouts.list')

@section('title', 'Domestic Assignments')
@section('subtitle')
	<small class="fs-0 text-muted d-block">List of domestic assignment</small>
@endsection
@section('new_url', route('assignment-domestic.create'))
{{-- @section('new_url', route('api.assignment-domestic-addNew')) --}}
@section('api_list_url', route('api.assignment-domestic'))

@section('columns')
	<th scope="col" data-class="va-baseline" data-field="assignment_no" data-sortable="true">No. Assignment</th>
	<th scope="col" data-class="va-baseline" data-field="letter_date" data-sortable="true">Letter Date</th>
	{{-- <th scope="col" data-class="va-baseline" data-field="assignment_date" data-sortable="true">Assignment Date</th> --}}
	<th scope="col" data-class="va-baseline" data-field="customer.name" data-sortable="true">Customer</th>
	<th scope="col" data-class="va-baseline" data-field="machine.name" data-sortable="true">Machine</th>
	<th scope="col" data-class="va-baseline text-center" data-field="is_chargeable" data-sortable="true">Chargeable</th>
	{{-- <th scope="col" data-class="va-baseline text-center" data-field="total_worker" data-sortable="true">Total Technicians</th> --}}
	{{-- <th scope="col" data-class="va-baseline text-center" data-field="current_status.reason" data-sortable="true">Status</th> --}}
@endsection
