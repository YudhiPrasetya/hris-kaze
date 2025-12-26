@extends('falcon::layouts.base')

@section('content')
	<x-bootstrap::row class="justify-content-center">
		<x-bootstrap::column breakpoint="EXTRA_SMALL|12;MEDIUM|9">
			<x-bootstrap::card>
				<x-bootstrap::card.header>
					<x-bootstrap::row class="align-items-baseline justify-content-between">
						<x-bootstrap::column class="d-flex flex-column align-items-baseline">
							<h5 class="fs-2 font-weight-semi-bold mb-0 text-nunito py-2 py-xl-0 text-truncate w-100 text-truncate">
								{{ $model->title }}<br />
								<small class="fs-0 text-muted d-block">Task</small>
							</h5>
						</x-bootstrap::column>
						@if($model->status()->name == "0")
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto" class="d-flex align-items-baseline">
							<a href="{{ route('task.edit', ['task' => $model->id]) }}" class="btn-falcon-success btn mr-1" role="button"><i class="fad fa-pencil-alt mr-2"></i>Edit</a>
							<button class="btn-falcon-danger btn" type="button" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $model->id }}').submit();">
								<i class="fad fa-trash-alt mr-2"></i>Delete
							</button>
							<form id="delete-form-{{ $model->id }}" action="{{ route('task.destroy', ['task' => $model->id]) }}" method="POST" style="display: none;">
								<input type="hidden" name="_method" value="DELETE">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</form>
						</x-bootstrap::column>
						@endif
					</x-bootstrap::row>
				</x-bootstrap::card.header>
				<x-bootstrap::card.body class="bg-light">
					<x-bootstrap::row>
						<x-bootstrap::column breakpoint="EXTRA_SMALL|6;SMALL|auto;MEDIUM|4">
							<x-bootstrap::media variant="primary" class="mb-4" icon="fad fa-user-clock" title="Assign To" subtitle="" />
							<div class="form-group">
								<h6 class="text-600 control-label mb-1">Employee</h6>
								<span id="name" class="form-control-plaintext text-1000 fs-0">{!! $model->getEmployee()->link() !!}</span>
							</div>
							<div class="form-group">
								<h6 class="text-600 control-label mb-1">Priority</h6>
								<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->getPriority()->name }}</span>
							</div>
							<div class="form-group">
								<h6 class="text-600 control-label mb-1">Dateline</h6>
								<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->dateline->format('l, d F Y') }} @if($model->status()->name != "2")&mdash; {{ \App\Libraries\PrettyDateTime::parse($model->dateline) }}@endif</span>
							</div>
							<div class="form-group">
								<h6 class="text-600 control-label mb-1">Status</h6>
								<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->status()->reason }}</span>
								@if($model->status()->name == "2")<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->status()->created_at->format('l, d F Y') }}</span>@endif
							</div>
						</x-bootstrap::column>
						<x-bootstrap::column>
							<x-bootstrap::media variant="primary" class="mb-4" icon="fad fa-tasks" title="Task" subtitle="Detail description of task" />
							<div class="form-group">
								<h6 class="text-600 control-label mb-1">Title</h6>
								<span id="name" class="form-control-plaintext text-1000 fs-0">{{ $model->title }}</span>
							</div>
							<div class="form-group">
								<h6 class="text-600 control-label mb-1">Description</h6>
								<span id="name" class="form-control-plaintext text-1000 fs-0">{!! $model->description !!}</span>
							</div>
						</x-bootstrap::column>
					</x-bootstrap::row>
				</x-bootstrap::card.body>
			</x-bootstrap::card>
		</x-bootstrap::column>
	</x-bootstrap::row>
@endsection
