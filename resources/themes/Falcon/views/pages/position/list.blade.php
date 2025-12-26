@extends('falcon::layouts.list')

@section('title', 'Positions')

@section('subtitle')
    <small class="fs-0 text-muted d-block">
        List of positions
    </small>
@endsection

@section('new_url', route('position.create'))
@section('api_list_url', route('api.position'))

@section('columns')
    <th class="col" data-class="va-baseline" data-field="name" data-sortable="true">
        Name
    </th>
@endsection
