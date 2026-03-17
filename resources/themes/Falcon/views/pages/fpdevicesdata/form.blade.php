@extends('falcon::layouts.base');

@section('javascripts')
    @parent
    <script src="{{ themes('js/default-select.js') }}" defer></script>
    <script src="{{ asset('js/date.js') }}" defer></script>
@endsection
{{-- @section('api_list_url', route('api.fingerprintdevice')) --}}

@section('content')
    @php
        $fields = collect($form->getFieldValues())
    @endphp
    {{-- @if(session('status'))
    <div class="mb-4 font-medium text-sm text-green-600">
        {{ session('status') }}
    </div>
    @endif --}}

    {!! form_start($form, ['attr' => ['autocomplete' => "off"]]) !!}
    <x-bootstrap::row class="justify-content-center">
        <x-bootstrap::column breakpoint="EXTRA_SMALL|10;MEDIUM|5">
            <x-bootstrap::card>
                <x-bootstrap::card.header>
                    <x-bootstrap::row class="align-items-baseline justify-content-between">
                        <x-bootstrap::column breakpoint=" EXTRA_SMALL|6;SMALL|auto;MEDIUM|9"
                            class="d-flex flex-column align-items-baseline">
                            <h5
                                class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
                                Pull Data
                                <small class="fs-0 text-muted d-block">Pull data from finger print device</small>
                            </h5>
                        </x-bootstrap::column>

                    </x-bootstrap::row>
                </x-bootstrap::card.header>
                <x-bootstrap::card.body class="bg-light">
                    <x-bootstrap::row class="small-gutters">
                        <x-bootstrap::column>
                            {!!form_row($form->finger_print_device_id, ['attr' => ['data-value' => $model->finger_print_device_id]])!!}
                        </x-bootstrap::column>
                    </x-bootstrap::row>
                    <x-bootstrap::row>
                        <x-bootstrap::column breakpoint="EXTRA_SMALL|10;SMALL|auto;MEDIUM|6">
                            {!! form_row($form->from) !!}
                        </x-bootstrap::column>
                        <x-bootstrap::column breakpoint="EXTRA_SMALL|10;SMALL|auto;MEDIUM|6">
                            {!! form_row($form->to) !!}
                        </x-bootstrap::column>
                    </x-bootstrap::row>
                    <x-bootstrap::row>
                        <x-bootstrap::column class="d-flex align-items-end justify-content-end">
                            <div class="form-group d-flex flex-column">
                                {!! form_row($form->submit) !!}
                            </div>
                        </x-bootstrap::column>

                    </x-bootstrap::row>
                </x-bootstrap::card.body>
                </x-boostrap::card>
        </x-bootstrap::column>
    </x-bootstrap::row>
    {!! form_end($form, $renderRest = true) !!}
@endsection