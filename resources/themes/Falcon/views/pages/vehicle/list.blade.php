@extends('falcon::layouts.list')

@section('title', 'Vehicles')
@section('subtitle')
	<small class="fs-0 text-muted d-block">List of vehicles</small>
@endsection
@section('new_url', route('vehicle.create'))
@section('api_list_url', route('api.vehicle'))

@section('columns')
	<th scope="col" data-class="va-baseline" data-field="plat_number" data-sortable="true">Plat Number</th>
	<th scope="col" data-class="va-baseline" data-field="type" data-sortable="true">Type</th>
@endsection