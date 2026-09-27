@extends('falcon::layouts.base')

@section('content')
	<x-bootstrap::row>
		<x-bootstrap::column>
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto;MEDIUM|6"
							class="d-flex flex-column align-items-baseline">
							<h5
								class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								{{ $model->employee->name }}<br />
								<small class="fs-0 text-muted d-block">Permit</small>
							</h5>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto" class="d-flex align-items-baseline">
							<a href="{{ route('permit.edit', ['permit' => $model->id]) }}"
								class="btn-falcon-success btn mr-1" role="button"><i
									class="fad fa-pencil-alt mr-2"></i>Edit</a>
							<button class="btn-falcon-danger btn" type="button"
								onclick="event.preventDefault(); document.getElementById('delete-form-{{ $model->id }}').submit();">
								<i class="fad fa-trash-alt mr-2"></i>Delete
							</button>
							<form id="delete-form-{{ $model->id }}"
                                  action="{{ route('permit.destroy', ['permit' => $model->id]) }}"
                                  method="POST"
                                  style="display: none;"
                            >
								<input type="hidden" name="_method" value="DELETE">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</form>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light">
					<x-bootstrap::row>
						<x-bootstrap::column breakpoint="EXTRA_SMALL;MEDIUM|6" class="">
							<div class="fancy-tab">
								<div class="nav-bar">
									<div class="nav-bar-item px-3 px-sm-4 active">Info</div>
								</div>

								<div class="fancy-tab-contents mt-3">
									<div class="tab-content fancy-tab-content active">
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Permit Date</h6>
											<span id="permitDate"
												class="form-control-plaintext text-1000 fs-0">{{ $model->permit_date }}</span>
										</div>
                                        @php
                                            $permitTypes = [
                                                "2" => "Sick (Sakit)",
                                                "3" => "Business Trip (Perjalanan Bisnis)",
                                                "4" => "Permit (Izin)"
                                            ];
                                        @endphp
                                        <div class="form-group">
                                            <h6 class="text-600 control-label mb-1">Permit Type</h6>
                                            <span id="permitType"
                                                class="form-control-plaintext text-1000 fs-0">{{ $permitTypes[$model->permit_type] ?? '-' }}
                                            </span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Name</h6>
											<span id="name"
												class="form-control-plaintext text-1000 fs-0">{{ $model->employee->name }}</span>
										</div>
                                        <div class="form-group">
                                            <h6 class="text-600 control-label mb-1">Start</h6>
                                            <span id="start"
                                                class="form-control-plaintext text-1000 fs-0">{{ \Carbon\Carbon::parse($model->start, 'Asia/Jakarta')->format('Y-m-d') }}
                                            </span>
										</div>
                                        <div class="form-group">
                                            <h6 class="text-600 control-label mb-1">End</h6>
                                            <span id="end"
                                                class="form-control-plaintext text-1000 fs-0">{{ \Carbon\Carbon::parse($model->end, 'Asia/Jakarta')->format('Y-m-d') }}
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <h6 class="text-600 control-label mb-1">Note</h6>
                                            <span id="note"
                                                class="form-control-plaintext text-1000 fs-0">{{ $model->note }}
                                            </span>
                                        </div>
                                        {{-- <div class="form-group">
                                            <h6 class="text-600 control-label mb-1">Attachment</h6>
                                            @if($model->attachment_path)
                                                <a href="{{ route('#', ['permit' => $model->id]) }}" class="btn btn-link" target="_blank">
                                                    Download Attachment
                                                </a>
                                            @else
                                                <span id="attachment"
                                                    class="form-control-plaintext text-1000 fs-0">No attachment available</span>
                                            @endif
                                        </div> --}}
									</div>
                                </div>
                            </div>
                        </x-bootstrap::column>
                    </x-bootstrap::row>
                </x-bootstrap::card.body>
            </x-bootstrap::card>
        </x-bootstrap::column>
    </x-bootstrap::row>
@endsection
