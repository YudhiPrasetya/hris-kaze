@if ($breadcrumbs->count())
	<x-bootstrap::row class="mb-3">
		<x-bootstrap::column class="pb-1 px-3">
			<div class="btn-group btn-breadcrumb btn-breadcrumb-falcon">
				@foreach ($breadcrumbs as $breadcrumb)
					@if ($breadcrumb->url() && $loop->remaining)
						<a href="{{ $breadcrumb->url() }}" class="btn btn-falcon-primary text-nunito font-weight-bold d-flex justify-content-center align-items-center">{!! $breadcrumb->title() !!}</a>
					@else
						<a href="#" class="btn btn-falcon-primary text-nunito font-weight-bold d-flex justify-content-center align-items-center disabled">{!! $breadcrumb->title() !!}</a>
					@endif
				@endforeach
			</div>
		</x-bootstrap::column>
	</x-bootstrap::row>
@endif