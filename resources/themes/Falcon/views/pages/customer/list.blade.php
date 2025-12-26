@extends('falcon::layouts.list')

@section('title', 'Customers')
@section('subtitle')
	<small class="fs-0 text-muted d-block">List of customers</small>
@endsection
@section('new_url', route('customer.create'))
@section('api_list_url', route('api.customer'))

@section('columns')
	<th scope="col" data-class="va-baseline" data-field="name" data-sortable="true">Name</th>
	<th scope="col" data-class="va-baseline" data-field="email" data-sortable="true">Email</th>
	<th scope="col" data-class="va-baseline" data-field="country.name" data-sortable="true">Country</th>
	<th scope="col" data-class="va-baseline" data-field="state.name" data-sortable="true">State</th>
	<th scope="col" data-class="va-baseline" data-field="city.name" data-sortable="true">City</th>
@endsection