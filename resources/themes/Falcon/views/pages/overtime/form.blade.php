@extends('falcon::layouts.base')
@section('javascripts')
    @parent
    <script src="{{ themes('js/default-select.js') }}" defer></script>
    <script src="{{ asset('js/date.js') }}" defer></script>
@endsection
@section('content')
    @php
        use App\Models\Employee;
        $fields = collect($form->getFieldValues());
        $employee = Employee::find($model->employee_id);
    @endphp

    {!! form_start($form, ['attr' => ['autocomplete' => 'off']]) !!}

    <x-bootstrap::row class="justify-content-center">
        <x-bootstrap::column breakpoint="EXTRA_SMALL|10;MEDIUM|9">
            <x-bootstrap::card>
                <x-bootstrap::card.header>
                    <x-bootstrap::row class="align-items-baseline justify-content-between">
                        <x-bootstrap::column breakpoint=" EXTRA_SMALL|6;SMALL|auto;MEDIUM|9"
                            class="d-flex flex-column align-items-baseline">
                            <h5
                                class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
                                {{ $employee->name }}
                                <small class="fs-0 text-muted d-block">Overtime</small>
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
                            {!! form_row($form->id_attendance, ['value' => $model->id]) !!}
                            {!! form_row($form->overtime_date, ['value' => $model->at, 'attr' => ['readonly' => true]]) !!}
                            {!! form_row($form->start, ['value' => $model->start, 'attr' => ['readonly' => true]]) !!}
                            {!! form_row($form->overtime, ['value' => $model->overtime, 'attr' => ['readonly' => true]]) !!}
                            @php
                                $start = new DateTime($model->at->format('Y-m-d') . " " . $model->start);
                                $end = new DateTime($model->at->format('Y-m-d') . " " . $model->end);
                                $overtimeDuration = $start->diff($end);
                                $overtimeDurationHour = $overtimeDuration->h;
                                if ($overtimeDurationHour > 8) {
                                    $overtimeDurationHour = 8;
                                }
                            @endphp
                        </x-bootstrap::column>
                        <x-bootstrap::column>
                            {!! form_row($form->id_employee, ['value' => $model->employee_id, 'attr' => ['readonly' => true]]) !!}
                            {!! form_row($form->employee, ['value' => $employee->name, 'attr' => ['readonly' => true]]) !!}
                            {!! form_row($form->end, ['value' => $model->end, 'attr' => ['readonly' => true]]) !!}
                            {!! form_row($form->overtime_duration, ['value' => $overtimeDurationHour, 'attr' => ['readonly' => true]]) !!}
                            {!! form_row($form->necessity) !!}
                            {{-- {!! form_row($form->status) !!} --}}
                        </x-bootstrap::column>
                    </x-bootstrap::row>
                </x-bootstrap::card.body>
                </x-boostrap::card>
        </x-bootstrap::column>
    </x-bootstrap::row>
    {!! form_end($form, $renderRest = true) !!}
@endsection