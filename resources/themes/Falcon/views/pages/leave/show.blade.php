@extends('falcon::layouts.base')

@section('content')
	<x-bootstrap::row>
		<x-bootstrap::column>
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto;MEDIUM|8" class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								{{ $model->id }}<br />
								<small class="fs-0 text-muted d-block">Leave</small>
							</h5>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto" class="d-flex align-items-baseline">
							<a href="{{ route('leave.edit', ['leave' => $model->id]) }}" class="btn-falcon-success btn mr-1" role="button"><i class="fad fa-pencil-alt mr-2"></i>Edit</a>
							<button class="btn-falcon-danger btn" type="button" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $model->id }}').submit();">
								<i class="fad fa-trash-alt mr-2"></i>Delete
							</button>
							<form id="delete-form-{{ $model->id }}" action="{{ route('leave.destroy', ['leave' => $model->id]) }}" method="POST" style="display: none;">
								<input type="hidden" name="_method" value="DELETE">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</form>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light">
					<x-bootstrap::row>
						<x-bootstrap::column breakpoint="EXTRA_SMALL;MEDIUM|6" class="d-flex flex-column align-items-center justify-content-centr">
							<div class="avatar w-90 mb-3">
								<figure class="figure w-100">
									<img id="targetPreview" src="@if(!$model->attachment_path->isEmpty()){!! $model->attachment_path->toString() !!}@else{{ route('image-placeholder', ['size' => 512, 'bgColor' => 'EEF0F2', 'textColor' => 'ffffff']) }}@endif" class="figure-img img-fluid w-100 transition-all" alt="Profile Picture" />
								</figure>
							</div>
						</x-bootstrap::column>
						<x-bootstrap::column breakpoint="EXTRA_SMALL;MEDIUM|6" class="">
							<div class="fancy-tab">
								<div class="nav-bar">
									<div class="nav-bar-item px-3 px-sm-4 active">Info</div>
									{{-- <div class="nav-bar-item px-3 px-sm-4">Assignments</div>
									<div class="nav-bar-item px-3 px-sm-4">Tasks</div>
									<div class="nav-bar-item px-3 px-sm-4">Attendance</div> --}}
								</div>
								<div class="fancy-tab-contents mt-3">
									<div class="tab-content fancy-tab-content active">
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Date</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->leave_date->format('Y-m-d') }}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Name</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->employee->name }}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Type</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->reasonForLeave->name }}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">Reason</h6>
											<span id="jobtitle" class="form-control-plaintext text-1000 fs-0">{{ $model->note }}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">From</h6>
											<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->start->format('Y-m-d') }}</span>
										</div>
										<div class="form-group">
											<h6 class="text-600 control-label mb-1">To</h6>
											<span id="leaveAllowance" class="form-control-plaintext text-1000 fs-0">{{ $model->end->format('Y-m-d')}}</span>
										</div>
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
