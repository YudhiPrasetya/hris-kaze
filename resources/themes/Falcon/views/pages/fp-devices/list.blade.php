@extends('falcon::layouts.list')

@section('title', 'Finger Print Devices')
@section('subtitle')
	<small class="fs-0 text-muted d-block">List of finger print devices</small>
@endsection
@section('new_url', route('fingerprintdevice.create'))
@section('api_list_url', route('api.fingerprintdevice'))

@section('columns')
	<th scope="col" data-class="va-baseline" data-field="no" data-sortable="true">No.Device</th>

	<th scope="col" data-class="va-baseline" data-field="ip_address" data-sortable="true">Ip Address</th>

	<th scope="col" data-class="va-baseline" data-field="port" data-sortable="true">Port</th>

	<th scope="col" data-class="va-baseline" data-field="description" data-sortable="true">Description</th>
@endsection
