@extends('falcon::layouts.base')

@section('javascripts')
    @parent
    <script src="{{ themes('js/default-select.js') }}" defer></script>
    <script src="{{ asset('js/date.js') }}" defer></script>
    {{-- <script src="{{ asset('js/domesticAssignment.js') }}"></script> --}}

    <script src="{{ asset('js/jquery-3.7.1.min.js') }}" defer></script>
    <script src="{{ asset('js/select2.min.js') }}" defer></script>
    <script src="{{ asset('js/jquery.mask.min.js') }}" defer></script>
    <script src="{{ asset('js/sweetalert2.js') }}" defer></script>
    <script src="{{ asset('js/jquery-ui.min.js') }}" defer></script>
    <script src="{{ asset('js/money.js') }}" defer></script>
    <script type="module" src="{{ asset('js/domesticAssignment-edit.js') }}" defer></script>
@endsection

@section('content')
    @php
        $fields = collect($form->getFieldValues());
    @endphp
    {!! form_start($form,['attr' => ['autocomplete' => 'off']]) !!}
        {{ method_field('PUT') }}

        <x-bootstrap::row class="justify-content-center">
            <x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|12">
                <x-bootstrap::card>
                    <x-bootstrap::card.header>
                        <x-bootstrap::row class="align-items-baseline justify-content-between">
                            <x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto;MEDIUM|9"
                                class="d-flex flex-column align-items-baseline">
                                <h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
                                    Domestic Assignment
                                    <small class="fs-0 text-muted d-block">Edit existing domestic assignment</small>
                                    <input type="hidden" name="domesticAssignmentId" id="domesticAssignmentId" value="{{ $model->id }}">
                                </h5>
                            </x-bootstrap::column>
                        </x-bootstrap::row>
                    </x-bootstrap::card.header>
                    <x-bootstrap::card.body class="bg-light">
                        <div class="fancy-tab">
                            <div class="nav-bar">
                                <div class="nav-bar-item px-3 px-sm-4 active">Service Info</div>
                                <div class="nav-bar-item px-3 px-sm-4">Accomodations</div>
                            </div>
                            <div class="fancy-tab-contents mt-3 overflow-hidden">
                                <div class="fancy-tab-content active">
                                    <x-bootstrap::row>
                                        <x-bootstrap::column breakpoint="EXTRA_SMALL|6;MEDIUM|6" class="mx-4">
                                            <div class="form-group">
                                                <label for="assignment_type" class="control-label">Assignment Type</label>
                                                <select id="assignment_type" disabled>
                                                    <option value="{{ $model->assignment_type }}">{{ $model->assignment_type }}</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="overseas_to" class="control-label">Overseas To</label>
                                                <select id="overseas_to" disabled>
                                                    <option value="{{ $model->overseas_to }}">{{ $model->overseas_to }}</option>
                                                </select>
                                            </div>
                                            {!! form_row($form->assignment_no, [
                                                'attr' => [
                                                    'value' => $model->assignment_no,
                                                    'disabled' => 'disabled'
                                                ],
                                            ]) !!}
                                            {!! form_row($form->letter_date, [
                                                'attr' => [
                                                    'value' => $model->letter_date,
                                                    'disabled' => 'disabled'
                                                ],
                                            ]) !!}
                                            {!! form_row($form->sr_no, [
                                                'attr' => [
                                                    'value' => $model->sr_no,
                                                    'disabled' => 'disabled'
                                                ],
                                            ]) !!}
                                        </x-bootstrap::column>

                                        <x-bootstrap::column class="mx-4">
                                            <x-bootstrap::row>
                                                <x-bootstrap::column breakpoint="EXTRA_SMALL|6;MEDIUM|6">
                                                    {!! form_row($form->assignment_date_from, [
                                                        'value' => $model->assignment_date_from,
                                                        'attr' => [
                                                            'disabled' => 'disabled'
                                                        ],
                                                    ]) !!}
                                                </x-bootstrap::column>
                                                <x-bootstrap::column>
                                                    {!! form_row($form->assignment_date_to, [
                                                        'value' => $model->assignment_date_to,
                                                        'attr' => [
                                                            'disabled' => 'disabled'
                                                        ],
                                                    ]) !!}
                                                </x-bootstrap::column>
                                            </x-bootstrap::row>

                                            <x-bootstrap::row>
                                                <x-bootstrap::column breakpoint="EXTRA_SMALL|3;MEDIUM|3" class="d-flex align-items-center px-0">
                                                    {!! form_row($form->is_chargeable, [
                                                        'attr' => [
                                                            'value' => $model->is_chargeable,
                                                            'disabled' => 'disabled'
                                                        ]
                                                    ]) !!}
                                                </x-bootstrap::column>
                                                <x-bootstrap::column class="px-0">
                                                    {!! form_row($form->charge_price, [
                                                        'attr' => [
                                                            'value' => $model->charge_price,
                                                            'disabled' => 'disabled'
                                                        ]
                                                    ]) !!}
                                                </x-bootstrap::column>
                                            </x-bootstrap::row>

                                            <x-bootstrap::row>
                                                <x-bootstrap::column>
                                                    <div class="form-group">
                                                        <label for="customer" class="control-label">Customer</label>
                                                        <select id="customer" disabled>
                                                            <option value="{{ $model->customer_id }}">{{ $model->customer->name }}</option>
                                                        </select>
                                                    </div>
                                                </x-bootstrap::column>
                                            </x-bootstrap::row>

                                            <x-bootstrap::row>
                                                <x-bootstrap::column>
                                                    <div class="form-group">
                                                        <label for="machine" class="control-label">Machine</label>
                                                        <select id="machine" disabled>
                                                            <option value="{{ $model->machine_id }}">{{ $model->machine->name }}</option>
                                                        </select>
                                                    </div>

                                                </x-bootstrap::column>
                                            </x-bootstrap::row>

                                            <x-bootstrap::row>
                                                <x-bootstrap::column>
                                                    <div class="form-group">
                                                        <label for="technicians" class="control-label">Technicians</label>
                                                        <select id="technicians" name="technicians[]" multiple disabled>
                                                            @foreach ($model->domesticAssigmentPreServices as $technician)
                                                                <option value="{{ $technician->employee_id }}" selected>{{ $technician->employee->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </x-bootstrap::column>
                                            </x-bootstrap::row>

                                        </x-bootstrap::column>
                                    </x-bootstrap::row>
                                </div>

                                <div class="fancy-tab-content">
                                    <div id='accordion' style="cursor: pointer;">
                                        {{-- <x-bootstrap::media variant="primary" class="mt-4 mb-1 accordion" icon="fad fa-info" title="Pre Service" subtitle="Pre service accomodations" /> --}}
                                        <h5 style="margin-left: 44px; margin-right: 44px;">
                                            Pre Service<br/>
                                            <small>Pre service assigment accomodations</small>
                                        </h5>
                                        <div id="preServiceToggleShow" style="margin-left: 44px; margin-right: 44px;">
                                            <x-bootstrap::row>
                                                <x-bootstrap::column breakpoint="EXTRA_SMALL|6;MEDIUM|6">
                                                    {!! form_row($form->checkIn, ['class' => 'checkInDate']) !!}
                                                </x-bootstrap::column>
                                                <x-bootstrap::column>
                                                    {!! form_row($form->checkInAt, ['class' => 'checkInAt']) !!}
                                                </x-bootstrap::column>
                                            </x-bootstrap::row>
                                            <div class="table-responsive">
                                                <table class="table table-hover bg-white table-accpreservice">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th class="va-baseline text-center fs-0">No.</th>
                                                            <th class="va-baseline text-center fs-0" style="display: none;">Id</th>
                                                            <th class="va-baseline fs-0" width="400">Name</th>
                                                            <th class="text-center fs-0">Breakfast</th>
                                                            <th class="text-center fs-0">Lunch</th>
                                                            <th class="text-center fs-0">Dinner</th>
                                                            <th class="text-center fs-0">Supper</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        {{-- <x-bootstrap::media variant="primary" class="mt-4 mb-1" icon="fad fa-info" title="During Service" subtitle="During service accomodations" /> --}}
                                        <h5 style="margin-left: 44px; margin-right: 44px;">
                                            During Service<br/>
                                            <small>During service assignment accomodations</small>
                                        </h5>
                                        <div id="duringServiceToggleShow" style="margin-left: 44px; margin-right: 44px;">
                                            <x-bootstrap::row>
                                                <x-bootstrap::column breakpoint="EXTRA_SMALL|6;MEDIUM|6">
                                                    {!! form_row($form->checkOut) !!}
                                                </x-bootstrap::column>
                                            </x-bootstrap::row>
                                            <div class="table-responsive" id="table-accduringservice-container"></div>
                                        </div>

                                        {{-- <x-bootstrap::media variant="primary" class="mt-4 mb-1" icon="fad fa-info" title="Arrival" subtitle="Arrival accomodations" /> --}}
                                        <h5 id="arrivalLabelHeader" style="margin-left: 44px; margin-right: 44px;">
                                            Arrival<br/>
                                            <small>
                                                Arrival from overseas assignment accomodations
                                            </small>
                                        </h5>
                                        <div id="arrivalToggleShow" style="margin-left: 44px; margin-right: 44px;">
                                            <x-bootstrap::row>
                                                <x-bootstrap::column breakpoint="EXTRA_SMALL|6;MEDIUM|6">
                                                    {!! form_row($form->eta_flight_date) !!}
                                                </x-bootstrap::column>
                                                <x-bootstrap::column>
                                                    {!! form_row($form->eta_flight_time, ['attr' => ['class_append' => 'time24h']]) !!}
                                                </x-bootstrap::column>
                                            </x-bootstrap::row>
                                            <div class="table-responsive" id="table-arrival-container">
                                                <table class="table table-hover bg-white table-arrival">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th class="va-baseline text-center fs-0">No.</th>
                                                            <th class="va-baseline text-center fs-0" style="display: none;">Id</th>
                                                            <th class="va-baseline fs-0" width="400">Name</th>
                                                            <th class="text-center fs-0">Breakfast</th>
                                                            <th class="text-center fs-0">Lunch</th>
                                                            <th class="text-center fs-0">Dinner</th>
                                                            <th class="text-center fs-0">Supper</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </x-bootstrap::card.body>
                    <div class="card-footer">
                        {!! form_row($form->btnDomesticAssignment, [
                            'label' => '<i class="fad fa-save mr-1"></i> Update'
                        ]) !!}
                    </div>
                </x-bootstrap::card>
            </x-bootstrap::column>
        </x-bootstrap::row>

    {!! form_end($form, true) !!}
@endsection



