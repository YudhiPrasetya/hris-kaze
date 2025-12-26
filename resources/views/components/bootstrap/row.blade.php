@php
	merge_attributes($attributes, 'class', ['row', !$gutters ? 'no-gutters' : null]);
@endphp
<div {{ $attributes }}>
	{!! $slot !!}
</div>
