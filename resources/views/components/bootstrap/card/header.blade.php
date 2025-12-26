@php
	merge_attributes($attributes, 'class', 'card-header');
@endphp
<div {{ $attributes }}>
	{!! $slot !!}
</div>
