@php
	merge_attributes($attributes, 'class', 'card-body');
@endphp
<div {{ $attributes }}>
	{!! $slot !!}
</div>
