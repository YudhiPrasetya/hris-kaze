@extends('falcon::layouts.base')

@section('javascripts')
    @parent
    <script src="{{ themes('js/default-select.js') }}" defer></script>
    <script src="{{ asset('js/date.js') }}" defer></script>
@endsection

@section('content')
    @php
        $fields = collect($form->getFieldValues());
    @endphp
    @php
        $unreadNotif = auth()->user()->notifications()->where('read_at', null)->first();
    @endphp
    @if($unreadNotif)
        <div class="alert alert-info fade show py-2 px3" role="alert">
            <button class="close" type="button" data-dismiss="alert" aria-label="Close">
                <span class="font-weight-light" aria-hidden="true">x</span>
            </button>
            <div class="flex-row d-flex align-items-baseline">
                <span class="fad fa-check-circle mr-2 fs-0 position-relative" style="top: 4px;"></span>
                <p class="mb-0">
                    {{$unreadNotif->data['message']}}
                </p>
            </div>
        </div>
        @php
            $unreadNotif->markAsRead();
        @endphp

    @endif
    {!! form_start($form, ['attr' => ['autocomplete' => "off"]]) !!}
    {{-- Form content goes here --}}
    <x-bootstrap::row class="justify-content-center">
        <x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|5">
            <x-bootstrap::card>
                <x-bootstrap::card.header>
                    <x-bootstrap::row class="align-items-baseline justify-content-between">
                        <x-bootstrap::column class="d-flex flex-column align-items-baseline">
                            <h5
                                class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
                                Show Payroll
                                <small class="fs-0 text-muted d-block">Payroll for {{ $model->name }}</small>
                            </h5>
                        </x-bootstrap::column>
                    </x-bootstrap::row>
                </x-bootstrap::card.header>
                <x-bootstrap::card.body class="bg-light">
                    <div class="form-group row">
                        {!! form_label($form->month, ['label_attr' => ['class' => 'col-sm-3 col-form-label']]) !!}
                        <div class="col-sm-6">
                            {!! form_widget($form->month, ['attr' => ['class_append' => 'select2', 'data-value' => $request->input('month', date('m'))]]) !!}
                        </div>
                        <div class="col-sm-3">
                            {!! form_widget($form->year, ['value' => $request->input('year', date('Y'))]) !!}
                        </div>
                    </div>
                    <div class="form-group d-flex flex-column">
                        {!! form_row($form->submit) !!}
                    </div>
                </x-bootstrap::card.body>
            </x-bootstrap::card>
        </x-bootstrap::column>
    </x-bootstrap::row>
    {!! form_end($form, $renderRest = true) !!}
@endsection