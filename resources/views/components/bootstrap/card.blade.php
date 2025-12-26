@php
	merge_attributes($attributes, 'class', 'card');
@endphp
<div {{ $attributes }}>
	{!! $slot !!}
</div>
