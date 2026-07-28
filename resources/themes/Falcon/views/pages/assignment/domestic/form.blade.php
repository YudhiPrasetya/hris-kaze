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
    <script src="{{ asset('js/money.js') }}" defer></script>
    <script type="module" src="{{ asset('js/domesticAssignment.js') }}" defer></script>
@endsection

@section('content')
    @php
        $fields = collect($form->getFieldValues());
    @endphp

    {!! form_start($form, ['attr' => ['autocomplete' => 'off']]) !!}
    {{-- @csrf_field --}}
    {{ method_field('POST') }}
    <x-bootstrap::row class="justify-content-center">
        <x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|12">
            <x-bootstrap::card>
                <x-bootstrap::card.header>
                    <x-bootstrap::row class="align-items-baseline justify-content-between">
                        <x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto;MEDIUM|9"
                            class="d-flex flex-column align-items-baseline">
                            <h5
                                class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
                                @if (!$model->name)
                                    Domestic Assignment
                                    <small class="fs-0 text-muted d-block">Add new assignment</small>
                                @else
                                    {{ $model->name }}
                                    <small class="fs-0 text-muted d-block">Assignment</small>
                                @endif
                            </h5>
                        </x-bootstrap::column>
                        {{-- <x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto" class="d-flex align-items-baseline">
                        </x-bootstrap::column> --}}
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
                                        {{-- {!! form_row($form->assignment_type) !!} --}}
                                        <div class="form-group">
                                            <label for="assignment_type" class="control-label">Assignment Type</label>
                                            <select id="assignment_type">
                                                <option value="">Please select an assignment type</option>
                                                <option value="Domestic">Domestic</option>
                                                <option value="Overseas">Overseas</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="overseas_to" class="control-label">Overseas To</label>
                                            <select id="overseas_to">
                                                <option value="0">Please select destination of overseas</option>
                                            </select>
                                        </div>
                                        {{-- {!! form_row($form->overseas_to) !!} --}}
                                        {!! form_row($form->assignment_no) !!}
                                        {!! form_row($form->letter_date) !!}
                                        {!! form_row($form->sr_no) !!}

                                        {{-- <x-bootstrap::media variant="primary" class="mt-4 mb-1" icon="fad fa-info"
                                        title="Assignment Date" subtitle="Assignment Date." /> --}}



                                        {{-- {!! form_row($form->assignment_date) !!} --}}



                                        {{-- {!! form_row($form->vehicle_id, ['attr' => ['data-value' => $model->vehicle_id]]) !!} --}}
                                        {{-- <input type="hidden" name="is_chargeable" value="0" /> --}}
                                        {{-- {!! form_row($form->is_chargeable, ['attr' => ['value' =>
                                            $fields->get('is_chargeable')]]) !!} --}}
                                        {{-- {!! form_row($form->is_chargeable, ['attr' => ['checked' =>
                                            $model->is_chargeable]]) !!} --}}



                                        {{-- {!! form_row($form->destination) !!} --}}
                                    </x-bootstrap::column>
                                    <x-bootstrap::column class="mx-4">
                                        <x-bootstrap::row>
                                            <x-bootstrap::column breakpoint="EXTRA_SMALL|6;MEDIUM|6" class="px-0">
                                                {!! form_row($form->assignment_date_from) !!}
                                            </x-bootstrap::column>
                                            <x-bootstrap::column class="px-0">
                                                {!! form_row($form->assignment_date_to) !!}
                                            </x-bootstrap::column>
                                        </x-bootstrap::row>
                                        <x-bootstrap::row>
                                            <x-bootstrap::column breakpoint="EXTRA_SMALL|3;MEDIUM|3" class="d-flex align-items-center px-0" >
                                                {!! form_row($form->is_chargeable) !!}
                                            </x-bootstrap::column>

                                            <x-bootstrap::column class="px-0">
                                                {!! form_row($form->charge_price) !!}
                                            </x-bootstrap::column>
                                        </x-bootstrap::row>

                                        <x-bootstrap::row>
                                            <x-bootstrap::column class="px-0">
                                                <div class="form-group">
                                                    <label for="customer" class="control-label">Customer</label>
                                                    <select id="customer">
                                                        <option value="">Please select a customer</option>
                                                    </select>
                                                </div>
                                            </x-bootstrap::column>
                                        </x-bootstrap::row>

                                        <x-bootstrap::row>
                                            <x-bootstrap::column class="px-0">
                                                <div class="form-group">
                                                    <label for="machine" class="control-label">Machine</label>
                                                    <select id="machine">
                                                        <option value="">Please select a machine</option>
                                                    </select>
                                                </div>
                                            </x-bootstrap::column>
                                        </x-bootstrap::row>

                                        <x-bootstrap::row>
                                            <x-bootstrap::column class="px-0">
                                                <div class="form-group">
                                                    <label for="employees" class="control-label">Technicians</label>
                                                    <select id="employees" name="employees[]" multiple>
                                                        <option value="">Please select technicians</option>
                                                    </select>
                                                </div>
                                            </x-bootstrap::column>
                                        </x-bootstrap::row>
                                    </x-bootstrap::column>
                                </x-bootstrap::row>
                            </div>

                            <div class="fancy-tab-content">
                                <div id="toolbar-preservice" class="preservice collection-container pb-3 pt-1" data-prototype="{{ form_row($form->preService->prototype()) }}">
                                    <div class="form-group">
                                        <label for="accomodations" class="control-label">Accomodations</label>
                                        <select class="custom-control-input" style="cursor: pointer;" name="accomodations" id="accomodations">
                                            <option value="0">Please select accomodation</option>
                                            <option value="1">Pre Service</option>
                                            <option value="2">During Service</option>
                                            <option value="3">Arrival</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="preServiceToggleShow">
                                    <x-bootstrap::row>
                                        <x-bootstrap::column breakpoint="EXTRA_SMALL|6;MEDIUM|6">
                                            {!! form_row($form->checkIn) !!}
                                        </x-bootstrap::column>
                                        <x-bootstrap::column>
                                            {!! form_row($form->checkInAt) !!}
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
                                                @foreach ($form->preService->getChildren() as $child)
                                                @php(debug($child->employee_id->getOption('selected')))
                                                <tr>
                                                    <td class="va-baseline text-center fs-0">{{ $loop->index + 1 }}</td>
                                                    <td class="va-baseline fs-0">
                                                        {!! form_widget($child->employee_id, ['attr' => ['data-value' => $child->employee_id]]) !!}
                                                    </td>
                                                    <td class="va-baseline fs-0">
                                                        {!! form_widget($child->employee_name, ['attr' => ['data-value' => $child->employee_name]]) !!}
                                                    </td>
                                                    <td class="text-center fs-0">
                                                        {!! form_widget($child->breakfast, ['attr' => ['type' => 'checkbox', 'class' => 'form-control', 'size' => 8]]) !!}
                                                    </td>
                                                    <td class="text-center fs-0">
                                                        {!! form_widget($child->lunch, ['attr' => ['type' => 'checkbox', 'class' => 'form-control', 'size' => 8]]) !!}
                                                    </td>
                                                    <td class="text-center fs-0">
                                                        {!! form_widget($child->dinner, ['attr' => ['type' => 'checkbox', 'class' => 'form-control', 'size' => 8]]) !!}
                                                    </td>
                                                    <td class="text-center fs-0">
                                                        {!! form_widget($child->supper, ['attr' => ['type' => 'checkbox', 'class' => 'form-control', 'size' => 8]]) !!}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div id="duringServiceToggleShow">
                                    {!! form_row($form->checkOut) !!}
                                    <div class="table-responsive" id="table-accduringservice-container">
                                    </div>
                                </div>

                                <div id="arrivalTimeToggleShow">
                                    <x-bootstrap::row>
                                        <x-bootstrap::column breakpoint="EXTRA_SMALL|6;MEDIUM|6">
                                            {!! form_row($form->eta_flight_date) !!}
                                        </x-bootstrap::column>
                                        <x-bootstrap::column>
                                            {!! form_row($form->eta_flight_time) !!}
                                        </x-bootstrap::column>
                                    </x-bootstrap::row>
                                    <div class="table-responsive">
                                        <table class="table table-hover bg-white table-arrivaltime">
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
                </x-bootstrap::card.body>
                <div class="card-footer">
                    {!! form_row($form->btnDomesticAssignment) !!}
                </div>
            </x-bootstrap::card>
        </x-bootstrap::column>
    </x-bootstrap::row>
    {!! form_end($form, $renderRest = true) !!}
@endsection
