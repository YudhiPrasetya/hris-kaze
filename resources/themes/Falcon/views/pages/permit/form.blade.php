@extends('falcon::layouts.base')
@section('javascripts')
    @parent
    <script src="{{ themes('js/default-select.js') }}" defer></script>
    <script type="module" src="{{ themes('js/permit.js') }}" defer></script>
    <script src="{{ asset('js/date.js') }}" defer></script>
@endsection
@section('content')
    @php
        $fields = collect($form->getFieldValues())
    @endphp
    {!! form_start($form, ['attr' => ['autocomplete' => 'off']]) !!}

    <x-bootstrap::row class="justify-content-center">
        <x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|9">
            <x-bootstrap::card>
                <x-bootstrap::card.header>
                    <x-bootstrap::row class="align-items-baseline justify-content-between">
                        <x-bootstrap::column breakpoint=" EXTRA_SMALL|6;SMALL|auto;MEDIUM|9"
                            class="d-flex flex-column align-items-baseline">
                            <h5
                                class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
                                @if(!$model->id)
                                    Permit
                                    <small class="fs-0 text-muted d-block">Add new permit</small>
                                @else
                                    {{ $model->id }}
                                    <small class="fs-0 text-muted d-block">Permit</small>
                                @endif
                            </h5>
                        </x-bootstrap::column>
                        <x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto" class="d-flex align-items-baseline">
                            {!! form_row($form->submit) !!}
                        </x-bootstrap::column>
                    </x-bootstrap::row>
                </x-bootstrap::card.header>
                <x-bootstrap::card.body class="bg-light">
                    <x-bootstrap::row>
                        <x-bootstrap::column breakpoint="EXTRA_SMALL|6;MEDIUM|6">
                            {!! form_row($form->permit_date) !!}
                            @php
                                $isAdmin = Auth::user()->hasAnyRole(['super-admin','admin']);
                                if(!$isAdmin){
                                    $employee = App\Models\Employee::where('user_id', Auth::user()->id)->first();
                                }
                            @endphp
                            @if(!$isAdmin)
                                <div class="flex flex-col gap-0 mb-4">
                                    <input ty="hidden" style="display: none;" id="id_employee" name="id_employee" value="{{ $employee->id }}">
                                    <label for="id_employee" class="form-label">Employee</label>
                                    <input type="text" class="form-control" value="{{ $employee->name }}" disabled>
                                </div>
                                <div class="d-none">
                                    {!! form_row($form->id_employee) !!}
                                </div>
                            @else
                                {!! form_row($form->id_employee) !!}
                            @endif

                            {{-- {!! form_row($form->id_employee) !!} --}}
                            {!! form_row($form->permit_type) !!}
                            @if(!$isAdmin)
                                <div class="d-none">{!! form_row($form->cut_att_premium) !!}</div>
                                <div class="d-none">{!! form_row($form->cut_att_salary) !!}</div>
                            @else
                                {!! form_row($form->cut_att_premium) !!}
                                {!! form_row($form->cut_att_salary) !!}
                            @endif
                        </x-bootstrap::column>
                        <x-bootstrap::column>
                            {!! form_row($form->start) !!}
                            {!! form_row($form->end) !!}
                            {!! form_row($form->note) !!}
                            {!! form_row($form->attachment_path) !!}
                        </x-bootstrap::column>
                    </x-bootstrap::row>
                </x-bootstrap::card.body>
                </x-boostrap::card>
        </x-bootstrap::column>
    </x-bootstrap::row>
    {!! form_end($form, $renderRest = true) !!}
@endsection
