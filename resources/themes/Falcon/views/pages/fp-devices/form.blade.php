@extends('falcon::layouts.base');

@section('content')
    @php
        $fields = collect($form->getFieldValues())
    @endphp
    {!! form_start($form, ['attr' => ['autocomplete' => "off"]]) !!}
    <x-bootstrap::row class="justify-content-center">
        <x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|6">
            <x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column breakpoint=" EXTRA_SMALL|6;SMALL|auto;MEDIUM|9" class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								@if(!$model->no)
									Finger Print Device
									<small class="fs-0 text-muted d-block">Add new finger print device</small>
								@else
									{{ $model->no }}
									<small class="fs-0 text-muted d-block">Finger Print Device</small>
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
                        <x-bootstrap::column>
                                {!! form_row($form->no) !!}
                                {!! form_row($form->ip_address) !!}
                                {!! form_row($form->port) !!}
                                {!! form_row($form->description) !!}
                        </x-bootstrap::column>
                    </x-bootstrap::row>
                </x-bootstrap::card.body>
            </x-boostrap::card>
        </x-bootstrap::column>
    </x-bootstrap::row>
    {!! form_end($form, $renderRest = true) !!}
@endsection
