@extends('falcon::layouts.list')

@section('title', 'Customers')
@section('subtitle')
	<small class="fs-0 text-muted d-block">List of customers</small>
@endsection
@section('new_url', route('customer.create'))
@section('api_list_url', route('api.customer'))

@section('columns')
	<th scope="col" data-class="va-baseline" data-field="customer_no" data-sortable="true">Customer No.</th>
	<th scope="col" data-class="va-baseline" data-field="name" data-sortable="true">Name</th>
	<th scope="col" data-class="va-baseline" data-field="email" data-sortable="true">Email</th>
	<th scope="col" data-class="va-baseline" data-field="phone" data-sortable="true">Phone</th>
	<th scope="col" data-class="va-baseline" data-field="contact_name" data-sortable="true">Contact Name</th>
	<th scope="col" data-class="va-baseline" data-field="is_active" data-sortable="true">Suspended</th>

@endsection
