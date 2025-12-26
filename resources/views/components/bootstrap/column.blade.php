@php
	merge_attributes($attributes, 'class', [$breakpoint, !$gutters ? 'no-gutters' : null]);
@endphp
<div {{ $attributes }}>
	{!! $slot !!}
</div>
