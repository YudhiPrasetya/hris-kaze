@extends('falcon::layouts.base')
@section('javascripts')
    @parent
    <script src="{{ themes('js/default-select.js') }}" defer></script>
    <script src="{{ asset('js/date.js') }}" defer></script>
@endsection
@section('content')
    @php
        // dd($data);
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
                                Leave (Cuti)
                                <small class="fs-0 text-muted d-block">Add new leave (cuti)</small>
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
                            {{-- <input type="hidden" name="id_employee" value="{{ $data['employee_id'] }}" /> --}}
                            {!! form_row($form->id_employee, ['value' => $data['employee_id']]) !!}
                            <div class="mb-2">
                                <h6 class="text-600 control-label mb-0">Employee Name</h6>
                                <span id="leaveAllowance" class="form-control-plaintext text-1000 fs-0 pt-0">
                                    <strong>{{ $data['employee_name'] }}</strong>
                                </span>
                            </div>

                            {!! form_row($form->leave_date) !!}
                            {!! form_row($form->id_reason_for_leave) !!}
                            {!! form_row($form->cut_att_premium) !!}
                        </x-bootstrap::column>
                        <x-bootstrap::column>
                            <div class="mb-2">
                                <h6 class="text-600 control-label mb-0">Remaining leave quota (sisa jatah cuti)</h6>
                                <span id="leaveAllowance" class="form-control-plaintext text-1000 fs-0 pt-0">
                                    <strong>{{ $data['LeaveQuota'] }}</strong>
                                </span>

                            </div>
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