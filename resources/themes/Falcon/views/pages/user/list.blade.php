@extends('falcon::layouts.list')

@section('title', 'Users')
@section('subtitle')
	<small class="fs-0 text-muted d-block">List of registered users</small>
@endsection
@section('new_url', route('user.create'))
@section('api_list_url', route('api.user'))

@section('columns')
	<th scope="col" data-class="text-center" data-field="profile_photo_path" data-sortable="true"></th>
	<th scope="col" data-class="va-baseline" data-field="name" data-sortable="true">Name</th>
	<th scope="col" data-class="va-baseline" data-field="username" data-sortable="true">Username</th>
	<th scope="col" data-class="va-baseline" data-field="email" data-sortable="true">Email</th>
	<th scope="col" data-class="va-baseline" data-field="enabled" data-sortable="true">Enabled</th>
	<th scope="col" data-class="va-baseline" data-field="email_verified_at" data-sortable="true">Verified At</th>
	<th scope="col" data-class="va-baseline" data-field="last_login" data-sortable="true">Last Login</th>
@endsection