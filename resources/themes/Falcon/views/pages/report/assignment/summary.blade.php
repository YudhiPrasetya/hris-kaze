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
	{!! form_start($form, ['attr' => ['autocomplete' => "off"]]) !!}
	<x-bootstrap::row class="justify-content-center">
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|6">
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column class="d-flex flex-column align-items-baseline">
							<h5
								class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								Assignment Report
								<small class="fs-0 text-muted d-block">List of all assignments</small>
							</h5>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light">
                    <x-bootstrap::media variant="primary" class="mb-0" title="" subtitle="" />

                    {{-- {!! form_row($form->assignment_type) !!} --}}
                    <x-bootstrap::media variant="primary" class="mb-0" icon="fad fa-info"
                        title="Assignment Date Range" subtitle="Date range of the assignments." />

                    <x-bootstrap::row class="mb-0">
                        <x-bootstrap::column>
                            {!! form_row($form->start_date, ['attr' => ['class_append' => 'col-3']]) !!}
                        </x-bootstrap::column>

                        <x-bootstrap::column>
                            {!! form_row($form->end_date) !!}
                        </x-bootstrap::column>
                    </x-bootstrap::row>
                    <x-bootstrap::media variant="primary" class="mb-0" title="" subtitle="" />

					<div class="form-group d-flex flex-column">
						{!! form_row($form->submit) !!}
					</div>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
	</x-bootstrap::row>
	{!! form_end($form, $renderRest = true) !!}
	@if($request?->isMethod('POST'))
		<x-bootstrap::row class="justify-content-center mt-4">
			<x-bootstrap::column>
				<x-bootstrap::card>
					<x-bootstrap::card.header>
						<x-bootstrap::row class="align-items-baseline justify-content-between">
							<x-bootstrap::column breakpoint=" EXTRA_SMALL|6;SMALL|auto;MEDIUM|8"
								class="d-flex flex-column align-items-baseline">
								<h5
									class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
									{{-- Result&nbsp;&mdash;&nbsp;{!! sprintf("%s &dash; %s", $data['start']->format('F'), $data['end']->format('F Y')) !!} --}}
									<small class="fs-0 text-muted d-block">List of all assignments</small>
								</h5>
							</x-bootstrap::column>
						</x-bootstrap::row>
					</x-bootstrap::card.header>
					<x-bootstrap::card.body class="bg-light overflow-hidden p-0">
                        @php
                            $strStartDate = new DateTimeImmutable($request->input('start_date'));
                            $strEndDate = new DateTimeImmutable($request->input('end_date'));

                            $startDate = $strStartDate->format('Y/m/d');
                            $endDate = $strEndDate->format('Y/m/d');
                        @endphp
						@include('themes::Falcon.views.layouts.table', [
							'method' => 'get',
							'hasActions' => false,
							'hasToolbar' => true,
							// 'customContent' => '<label class="col-form-label font-weight-normal">Working Days: ' . $data['working_days'] . ' days</label>',
                            // 'customContent' => '<a href="' . route('report.assignment.export', ['start_date' => $request->input('start_date'), 'end_date' => $request->input('end_date'), 'assignment_type' => $request->input('assignment_type')]) . '" class="btn btn-primary">Export</a>',
                            'customContent' => '<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">Result&nbsp;</h5>' . '<label class="col-form-label font-weight-normal">Periode ' . $startDate . ' - ' . $endDate . '</label>&nbsp;&nbsp;' . 
                                               ' <a href="' . route('report.assignment.export', ['start_date' => $request->input('start_date'), 'end_date' => $request->input('end_date')]) . '" class="btn btn-success">Export To Excel</a>',
                            // 'customContent' => '<a href="#" class="btn btn-success">Export To Excel</a>',
							'data' => [
								// 'url' => route('api.assignment.report', ['start' => sprintf("%s-%s-01", $request->input('year'), $request->input('month'))]),
								'url' => route('api.assignment.report', ['start_date' => $request->input('start_date'), 'end_date' => $request->input('end_date'), 'assignment_type' => $request->input('assignment_type')]),
								'page-size' => 25,
								'show-refresh' => 'true',
								'method' => 'get',
							],
							'columns' => [
								[
									'title' => 'Assignment Date',
									'attrs' => [
										'field' => 'assignment_date',
										'class' => 'va-baseline text-nowrap',
										'sortable' => 'true',
									],
								],
								[
									'title' => 'SR NO.',
									'attrs' => [
										'field' => 'domestic_assignment.sr_no',
										'class' => 'va-baseline text-nowrap',
										'sortable' => 'true',
									],
								],
								[
									'title' => 'Customer',
									'attrs' => [
										'field' => 'domestic_assignment.customer.name',
										'class' => 'va-baseline text-nowrap',
										'sortable' => 'true',
									],
								],
								[
									'title' => 'User',
									'attrs' => [
										'field' => 'domestic_assignment.customer.name',
										'class' => 'va-baseline text-nowrap',
										'sortable' => 'true',
									],
								],
								[
									'title' => 'Members',
									'attrs' => [
										'field' => 'employee.name',
										'class' => 'va-baseline text-nowrap',
										'sortable' => 'true',
									],
								],
                                [
                                    'title' => 'Start',
                                    'attrs' => [
                                        'field' => 'start_job',
                                        'class' => 'va-baseline text-nowrap text-center',
                                    ]
                                ],
                                [
                                    'title' => 'Finish',
                                    'attrs' => [
                                        'field' => 'finish_job',
                                        'class' => 'va-baseline text-nowrap text-center',
                                    ]
                                ],
                                [
                                    'title' => 'Hotel',
                                    'attrs' => [
                                        'field' => 'check_in_at',
                                        'class' => 'va-baseline text-nowrap',
                                    ]
                                ],
                                [
                                    'title' => 'BF',
                                    'attrs' => [
                                        'field' => 'during_service_breakfast',
                                        'class' => 'va-baseline text-nowrap',
                                    ]
                                ],
                                [
                                    'title' => 'LC',
                                    'attrs' => [
                                        'field' => 'during_service_lunch',
                                        'class' => 'va-baseline text-nowrap',
                                    ]
                                ],
                                [
                                    'title' => 'DN',
                                    'attrs' => [
                                        'field' => 'during_service_dinner',
                                        'class' => 'va-baseline text-nowrap',
                                    ]
                                ],
                                [
                                    'title' => 'NT',
                                    'attrs' => [
                                        'field' => 'during_service_supper',
                                        'class' => 'va-baseline text-nowrap',
                                    ]
                                ],
                                [
                                    'title' => 'Total/Member',
                                    'attrs' => [
                                        'field' => 'total_during_service',
                                        'class' => 'va-baseline text-nowrap',
                                    ]
                                ],
                                [
                                    'title' => 'Overseas Meal (IDR)',
                                    'attrs' => [
                                        'field' => 'overseas_meal',
                                        'class' => 'va-baseline text-nowrap',
                                    ]
                                ],
                                [
                                    'title' => 'Foreign Currency',
                                    'attrs' => [
                                        'field' => 'foreign_currency',
                                        'class' => 'va-baseline text-nowrap',
                                    ]
                                ],
                                [
                                    'title' => 'Overseas Meal in Foreign Currency',
                                    'attrs' => [
                                        'field' => 'overseas_meal_in_foreign_currency',
                                        'class' => 'va-baseline text-nowrap',
                                    ]
                                ],


							],
						])
						</x-bootstrap::card.body>
					</x-bootstrap::card>
				</x-bootstrap::column>
			</x-bootstrap::row>
	@endif
@endsection
