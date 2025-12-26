@extends('falcon::layouts.base')

@section('content')
	<x-bootstrap::card class="mb-4">
		<x-bootstrap::card.header>
			<x-bootstrap::row class="align-items-center justify-content-between">
				<x-bootstrap::column breakpoint="EXTRA_SMALL;MEDIUM|8" class="d-flex align-items-center pr-0">
					<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0">
						@yield('title', 'Title')
						@yield('subtitle', null)
					</h5>
				</x-bootstrap::column>
				<x-bootstrap::column breakpoint="EXTRA_SMALL;MEDIUM|4" class="ml-auto text-right pl-0">
					@hasSection('new_url')
						<a href="@yield('new_url', '#')" role="button" class="btn btn-falcon-primary">
							<i class="fas fa-file-plus"></i>
							<span class="d-none d-sm-inline-block ml-1">New</span>
						</a>
					@endif
				</x-bootstrap::column>
			</x-bootstrap::row>
		</x-bootstrap::card.header>
		<x-bootstrap::card.body class="p-0 overflow-hidden">
			<div class="bg-light">
				@php
					$id = md5(rand(0, 1000));
				@endphp
				<div id="toolbar-{{ $id }}" class="d-flex align-items-start">
					@yield('toolbar')
				</div>
				<table
					@section('data-table')
					class="bootstrap-table table-striped"
					data-method="get"
					data-page-size="25"
					data-search="true"
					data-toolbar="#toolbar-{{ $id }}"
					@show
					data-url="@yield('api_list_url', '#')">
					<thead class="thead-light">
					<tr>
						{{--<th data-checkbox="true"></th>--}}
						<th scope="col" data-class="text-center va-baseline font-weight-medium" data-field="no" data-sortable="false" data-width="75">#</th>
						@yield('columns')
						<th scope="col" data-class="text-center va-baseline text-nowrap bootstrap-table-actions" data-field="actions" data-sortable="false" data-formatter="actionsFormatter" data-width="154">Actions</th>
					</tr>
					</thead>
				</table>
			</div>
		</x-bootstrap::card.body>
	</x-bootstrap::card>
@endsection
