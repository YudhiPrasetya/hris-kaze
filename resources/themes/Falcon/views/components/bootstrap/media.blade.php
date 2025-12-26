@php
	merge_attributes($attributes, 'class', ['media', 'overflow-hidden']);
@endphp
<div {{ $attributes }} class="media overflow-hidden">
	@if($icon)
	<span class="fa-stack mr-2 ml-n1">
		<i class="fas fa-circle fa-stack-2x text-300"></i>
		<i class="fa-inverse fa-stack-1x text-{{ $variant }} {{ $icon }}"></i>
	</span>
	@endif
	<div class="media-body">
		<h{{ $size == 0 ? '6' : $size }} class="mb-0 {{ $size == 0 ? 'fs-0' : '' }} text-{{ $variant }} position-relative"><span class="pr-3">{!! $title !!}</span>
			<span class="border-top position-absolute absolute-vertical-center w-100"></span>
		</h{{ $size == 0 ? '6' : $size }}>
		@if($subtitle)<p class="mb-0 fs--1 text-muted">{!! $subtitle !!}</p>@endif
	</div>
</div>