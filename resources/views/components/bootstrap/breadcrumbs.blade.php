<x-bootstrap::row class="mb-3">
	<x-bootstrap::column class="pb-1 px-3">
		<div class="btn-group btn-breadcrumb btn-breadcrumb-falcon">
			@foreach($nodes as $node)
				@if ($loop->first)
					<a href="{{ $node['url'] }}" class="btn btn-falcon-primary d-flex justify-content-center align-items-center"><i class="fa fa-home"></i></a>
				@else
					@if (!$loop->last)
						<a href="{{ $node['url'] }}" class="btn btn-falcon-primary fs--1">{{ $node['title'] }}</a>
					@else
						<a href="{{ $node['url'] }}" class="btn btn-falcon-default fs--1 disabled">{{ $node['title'] }}</a>
					@endif
				@endif
			@endforeach
		</div>
	</x-bootstrap::column>
</x-bootstrap::row>
