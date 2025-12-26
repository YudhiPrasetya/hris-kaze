@extends('falcon::layouts.list')

@section('title', 'Assignments')
@section('subtitle')
	<small class="fs-0 text-muted d-block">List of assignment</small>
@endsection
@section('new_url', route('assignment.create'))
@section('api_list_url', route('api.assignment'))

@section('columns')
	<th scope="col" data-class="va-baseline" data-field="service_no" data-sortable="true">Service Report</th>
	<th scope="col" data-class="va-baseline" data-field="service_date" data-sortable="true">Date</th>
	<th scope="col" data-class="va-baseline" data-field="purchase_order_no" data-sortable="true">Purchase Order</th>
	<th scope="col" data-class="va-baseline" data-field="customer.name" data-sortable="true">Customer</th>
	<th scope="col" data-class="va-baseline" data-field="destination" data-sortable="true">Destination</th>
	<th scope="col" data-class="va-baseline text-center" data-field="is_chargeable" data-sortable="true">Chargeable</th>
	<th scope="col" data-class="va-baseline text-center" data-field="total_worker" data-sortable="true">Total Technicians</th>
	<th scope="col" data-class="va-baseline text-center" data-field="current_status.reason" data-sortable="true">Status</th>
@endsection
