@extends('falcon::layouts.list')

@section('title', 'Job Titles')

@section('subtitle')
    <small class="fs-0 text-muted d-block">
        List of job titles
    </small>
@endsection

@section('new_url', route('jobtitle.create'))
@section('api_list_url', route('api.jobtitle'))

@section('columns')
    <th class="col" data-class="va-baseline" data-field="name" data-sortable="true">
        Name
    </th>
    <th class="col" data-class="va-baseline" data-field="description" data-sortable="true">
        Description
    </th>
@endsection
