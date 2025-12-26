@extends('falcon::layouts.list')

@section('title', 'Reason For Leave')

@section('subtitle')
    <small class="fs-0 text-muted d-block">
        List of reasons for leave
    </small>
@endsection

@section('new_url', route('reasonforleave.create'))
@section('api_list_url', route('api.reasonforleave'))

@section('columns')
    <th class="col" data-class="va-baseline" data-field="name" data-sortable="true">
        Name
    </th>

    <th class="col" data-class="va-baseline" data-field="number_of_days" data-sortable="true">
        Number Of Days
    </th>

    <th class="col" data-class="va-baseline" data-field="attachment_requirement" data-sortable="true">
        Attachment
    </th>
@endsection
